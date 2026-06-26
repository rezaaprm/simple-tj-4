import pymysql
import math
import heapq
import time
from collections import defaultdict

# ============================================================
# KONFIGURASI DATABASE
# ============================================================
DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'db_transportasi_jakarta_4',
    'charset': 'utf8mb4'
}

# ============================================================
# PARAMETER BOBOT (CARI 1 - JARAK)
# ============================================================
TRANSFER_PENALTY = 2500
BUS_WEIGHT = 800
LONG_BUS_THRESHOLD = 4000
LONG_BUS_PENALTY = 100000
STAY_BONUS = 800  # ← HIDUPKAN KEMBALI (tapi aman karena bobot tetap positif)
WALK_MULTIPLIER_SHORT = 5
WALK_MULTIPLIER_LONG = 50
MAX_WALK = 300

# ============================================================
# RUMUS HAVERSINE
# ============================================================
def haversine(lat1, lon1, lat2, lon2):
    R = 6371000
    phi1 = math.radians(lat1)
    phi2 = math.radians(lat2)
    delta_phi = math.radians(lat2 - lat1)
    delta_lambda = math.radians(lon2 - lon1)
    a = math.sin(delta_phi/2)**2 + math.cos(phi1) * math.cos(phi2) * math.sin(delta_lambda/2)**2
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1-a))
    return R * c

# ============================================================
# FETCH DATA
# ============================================================
def fetch_stops():
    conn = pymysql.connect(**DB_CONFIG)
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    cursor.execute("SELECT stop_id, stop_name, stop_lat, stop_lon FROM tb_stops")
    stops = cursor.fetchall()
    conn.close()
    stop_map = {}
    for s in stops:
        stop_map[s['stop_id']] = {
            'name': s['stop_name'],
            'lat': float(s['stop_lat']),
            'lon': float(s['stop_lon'])
        }
    return stop_map

def fetch_routes_with_stops():
    """
    Ambil semua trip per route_id, lalu gabungkan urutan halte
    menjadi satu daftar per koridor (seperti GtfsCacheService).
    """
    conn = pymysql.connect(**DB_CONFIG)
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    
    # Ambil semua trip dan stop_times, urutkan berdasarkan route_id, trip_id, stop_sequence
    cursor.execute("""
        SELECT 
            t.route_id,
            t.trip_id,
            st.stop_id,
            st.stop_sequence
        FROM tb_trips t
        JOIN tb_stop_times st ON t.trip_id = st.trip_id
        ORDER BY t.route_id, t.trip_id, st.stop_sequence
    """)
    rows = cursor.fetchall()
    conn.close()
    
    # Kumpulkan per route_id, lalu per trip
    routes = defaultdict(list)
    for row in rows:
        routes[row['route_id']].append(row['stop_id'])
    
    # Gabungkan semua trip dalam satu koridor menjadi daftar unik
    # (Urutan berdasarkan kemunculan pertama di trip pertama)
    merged_routes = {}
    for route_id, stops in routes.items():
        seen = set()
        merged = []
        for stop_id in stops:
            if stop_id not in seen:
                seen.add(stop_id)
                merged.append(stop_id)
        merged_routes[route_id] = merged
    
    return merged_routes

def fetch_route_color():
    conn = pymysql.connect(**DB_CONFIG)
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    cursor.execute("SELECT route_id, route_short_name, route_color FROM tb_routes")
    rows = cursor.fetchall()
    conn.close()
    route_info = {}
    for r in rows:
        route_info[r['route_id']] = {
            'short_name': r['route_short_name'],
            'color': r['route_color'] or '#3498db'
        }
    return route_info

# ============================================================
# BANGUN GRAF (BERBASIS KORIDOR, SEPERTI APLIKASI)
# ============================================================
def build_graph(stop_map, routes, route_info):
    graph = defaultdict(list)
    stop_ids = list(stop_map.keys())
    
    # 1. KONEKSI BUS (berdasarkan urutan halte per koridor)
    for route_id, stops in routes.items():
        for i in range(len(stops) - 1):
            s1 = stops[i]
            s2 = stops[i+1]
            if s1 not in stop_map or s2 not in stop_map:
                continue
            lat1 = stop_map[s1]['lat']
            lon1 = stop_map[s1]['lon']
            lat2 = stop_map[s2]['lat']
            lon2 = stop_map[s2]['lon']
            dist = haversine(lat1, lon1, lat2, lon2)
            
            weight = dist + BUS_WEIGHT
            if dist > LONG_BUS_THRESHOLD:
                weight += LONG_BUS_PENALTY
            
            # Tambah edge dua arah
            graph[s1].append({
                'to': s2,
                'weight': weight,
                'route_id': route_id,
                'type': 'bus'
            })
            graph[s2].append({
                'to': s1,
                'weight': weight,
                'route_id': route_id,
                'type': 'bus'
            })
    
    # 2. KONEKSI JALAN KAKI (sama seperti sebelumnya)
    for i in range(len(stop_ids)):
        for j in range(i+1, len(stop_ids)):
            s1 = stop_ids[i]
            s2 = stop_ids[j]
            lat1 = stop_map[s1]['lat']
            lon1 = stop_map[s1]['lon']
            lat2 = stop_map[s2]['lat']
            lon2 = stop_map[s2]['lon']
            dist = haversine(lat1, lon1, lat2, lon2)
            if dist <= MAX_WALK:
                mult = WALK_MULTIPLIER_SHORT if dist < 100 else WALK_MULTIPLIER_LONG
                walk_weight = dist * mult
                graph[s1].append({
                    'to': s2,
                    'weight': walk_weight,
                    'route_id': 'WALK',
                    'type': 'walk'
                })
                graph[s2].append({
                    'to': s1,
                    'weight': walk_weight,
                    'route_id': 'WALK',
                    'type': 'walk'
                })
    return graph

# ============================================================
# ALGORITMA DIJKSTRA (sama seperti sebelumnya)
# ============================================================
def dijkstra(graph, start_id, target_id):
    distances = {node: float('inf') for node in graph}
    previous = {node: None for node in graph}
    previous_route = {node: None for node in graph}
    distances[start_id] = 0
    pq = [(0, start_id)]
    processed = 0
    
    while pq:
        current_dist, current_node = heapq.heappop(pq)
        processed += 1
        if processed % 500 == 0:
            print(f"      ⏳ Memproses node ke-{processed} (total node {len(graph)})...")
        
        if current_dist > distances[current_node]:
            continue
        if current_node == target_id:
            print(f"      ✅ Target ditemukan setelah {processed} iterasi.")
            break
        
        for edge in graph.get(current_node, []):
            neighbor = edge['to']
            weight = edge['weight']
            route_id = edge['route_id']
            
            # Penalti Transfer
            if previous_route[current_node] and previous_route[current_node] != 'WALK':
                if route_id != 'WALK' and route_id != previous_route[current_node]:
                    weight += TRANSFER_PENALTY
            
            # Stay Bonus
            if previous_route[current_node] and route_id == previous_route[current_node]:
                weight -= STAY_BONUS
            
            # Pastikan bobot tidak negatif (untuk jaga-jaga)
            if weight < 0:
                weight = 0
            
            new_dist = current_dist + weight
            if new_dist < distances[neighbor]:
                distances[neighbor] = new_dist
                previous[neighbor] = current_node
                previous_route[neighbor] = route_id
                heapq.heappush(pq, (new_dist, neighbor))
    
    # Rekonstruksi
    path = []
    path_routes = []
    current = target_id
    while current is not None:
        path.insert(0, current)
        route = previous_route[current]
        if route and (not path_routes or path_routes[-1] != route):
            path_routes.insert(0, route)
        current = previous[current]
    
    total_distance = distances[target_id] if distances[target_id] != float('inf') else 0
    unique_routes = [r for r in path_routes if r != 'WALK' and r is not None]
    total_transfers = max(0, len(set(unique_routes)) - 1)
    
    return {
        'path': path,
        'total_distance': total_distance,
        'total_transfers': total_transfers,
        'unique_routes': list(set(unique_routes)),
        'node_count': len(path)
    }

def get_stop_name(stop_map, stop_id):
    return stop_map.get(stop_id, {}).get('name', stop_id)

# ============================================================
# MAIN
# ============================================================
def main():
    print("=" * 60)
    print("VERIFIKASI AKURASI DIJKSTRA (PYTHON) - PER KORIDOR")
    print("=" * 60)
    
    print("▶ Mengambil data dari database...")
    stop_map = fetch_stops()
    routes = fetch_routes_with_stops()
    route_info = fetch_route_color()
    print(f"   ✓ {len(stop_map)} halte, {len(routes)} koridor")
    
    print("▶ Membangun graf (berbasis koridor)...")
    graph = build_graph(stop_map, routes, route_info)
    total_edges = sum(len(v) for v in graph.values())
    print(f"   ✓ {len(graph)} node, total edges ~{total_edges}")
    
    # Skenario (menggunakan ID yang benar)
    scenarios = [
        {'name': 'Skenario 1: Blok M → Tosari', 'start': 'P00017', 'target': 'H00251P'},
        {'name': 'Skenario 2: Tanjung Priok → St. MRT Lebak Bulus', 'start': 'H00240P', 'target': 'B05492P'},
        {'name': 'Skenario 3: Ancol → Pondok Indah', 'start': 'H00003P', 'target': 'H00178P'},
    ]
    
    print("\n" + "=" * 60)
    print("HASIL PERHITUNGAN MANUAL (PYTHON DIJKSTRA)")
    print("=" * 60)
    
    for idx, sc in enumerate(scenarios, 1):
        start_id = sc['start']
        target_id = sc['target']
        start_name = get_stop_name(stop_map, start_id)
        target_name = get_stop_name(stop_map, target_id)
        
        print(f"\n[{idx}] {sc['name']}")
        print(f"    Dari: {start_name} ({start_id})")
        print(f"    Ke:   {target_name} ({target_id})")
        
        start_time = time.time()
        result = dijkstra(graph, start_id, target_id)
        elapsed_ms = (time.time() - start_time) * 1000
        
        if result['path'] and result['total_distance'] > 0:
            path_names = [get_stop_name(stop_map, s) for s in result['path']]
            print(f"      ✅ Jalur ditemukan!")
            print(f"       Total node : {result['node_count']} halte")
            print(f"       Total jarak: {result['total_distance']/1000:.2f} km")
            print(f"       Total pindah: {result['total_transfers']} kali")
            print(f"       Koridor    : {', '.join(result['unique_routes'])}")
            print(f"       Waktu      : {elapsed_ms:.2f} ms")
            print(f"       Jalur      : {' → '.join(path_names[:10])}{'...' if len(path_names) > 10 else ''}")
            print(f"\n    📊 VERIFIKASI AKURASI:")
            print(f"       🔹 Python Dijkstra    : {result['total_distance']/1000:.2f} km, {result['total_transfers']} transfer")
            print(f"       🔸 Sistem (UI)        : [ISI DARI SISTEM ANDA]")
        else:
            print(f"      ❌ Rute tidak ditemukan!")
    
    print("\n" + "=" * 60)
    print("✅ Selesai. Bandingkan hasil Python dengan output sistem.")
    print("   Jika sama, akurasi sistem = 100%.")
    print("=" * 60)

if __name__ == "__main__":
    main()
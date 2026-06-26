import pymysql
import math
import heapq
import random
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
STAY_BONUS = 0  # dinonaktifkan agar stabil
WALK_MULTIPLIER_SHORT = 5
WALK_MULTIPLIER_LONG = 50
MAX_WALK = 300

def haversine(lat1, lon1, lat2, lon2):
    R = 6371000
    phi1 = math.radians(lat1)
    phi2 = math.radians(lat2)
    delta_phi = math.radians(lat2 - lat1)
    delta_lambda = math.radians(lon2 - lon1)
    a = math.sin(delta_phi/2)**2 + math.cos(phi1) * math.cos(phi2) * math.sin(delta_lambda/2)**2
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1-a))
    return R * c

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
    conn = pymysql.connect(**DB_CONFIG)
    cursor = conn.cursor(pymysql.cursors.DictCursor)
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
    
    routes = defaultdict(list)
    for row in rows:
        routes[row['route_id']].append(row['stop_id'])
    
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

def build_graph(stop_map, routes, route_info):
    graph = defaultdict(list)
    stop_ids = list(stop_map.keys())
    
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
            graph[s1].append({'to': s2, 'weight': weight, 'route_id': route_id})
            graph[s2].append({'to': s1, 'weight': weight, 'route_id': route_id})
    
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
                graph[s1].append({'to': s2, 'weight': walk_weight, 'route_id': 'WALK'})
                graph[s2].append({'to': s1, 'weight': walk_weight, 'route_id': 'WALK'})
    return graph

def dijkstra(graph, start_id, target_id):
    distances = {node: float('inf') for node in graph}
    previous = {node: None for node in graph}
    previous_route = {node: None for node in graph}
    distances[start_id] = 0
    pq = [(0, start_id)]
    
    while pq:
        current_dist, current_node = heapq.heappop(pq)
        if current_dist > distances[current_node]:
            continue
        if current_node == target_id:
            break
        for edge in graph.get(current_node, []):
            neighbor = edge['to']
            weight = edge['weight']
            route_id = edge['route_id']
            if previous_route[current_node] and previous_route[current_node] != 'WALK':
                if route_id != 'WALK' and route_id != previous_route[current_node]:
                    weight += TRANSFER_PENALTY
            if weight < 0:
                weight = 0
            new_dist = current_dist + weight
            if new_dist < distances[neighbor]:
                distances[neighbor] = new_dist
                previous[neighbor] = current_node
                previous_route[neighbor] = route_id
                heapq.heappush(pq, (new_dist, neighbor))
    
    path = []
    current = target_id
    while current is not None:
        path.insert(0, current)
        current = previous[current]
    
    total_distance = distances[target_id] if distances[target_id] != float('inf') else 0
    return {
        'path': path,
        'total_distance': total_distance,
        'node_count': len(path)
    }

def get_stop_name(stop_map, stop_id):
    return stop_map.get(stop_id, {}).get('name', stop_id)

def main():
    print("=" * 60)
    print("10 TES RUTE ACAK (DIJKSTRA) - DENGAN JARAK FISIK & WEIGHTED")
    print("=" * 60)
    
    print("▶ Mengambil data dari database...")
    stop_map = fetch_stops()
    routes = fetch_routes_with_stops()
    route_info = fetch_route_color()
    print(f"   ✓ {len(stop_map)} halte, {len(routes)} koridor")
    
    print("▶ Membangun graf...")
    graph = build_graph(stop_map, routes, route_info)
    total_edges = sum(len(v) for v in graph.values())
    print(f"   ✓ {len(graph)} node, total edges ~{total_edges}")
    
    stop_ids = list(stop_map.keys())
    random.seed(42)
    selected = random.sample(stop_ids, min(100, len(stop_ids)))
    print(f"▶ Pilih 100 halte acak dari {len(stop_ids)} halte")
    
    pairs = []
    attempts = 0
    while len(pairs) < 10 and attempts < 10000:
        a = random.choice(selected)
        b = random.choice(selected)
        if a != b and (a,b) not in pairs and (b,a) not in pairs:
            pairs.append((a,b))
        attempts += 1
    
    print(f"▶ {len(pairs)} pasangan rute acak siap diuji\n")
    
    results = []
    for i, (start, target) in enumerate(pairs, 1):
        start_name = get_stop_name(stop_map, start)
        target_name = get_stop_name(stop_map, target)
        print(f"[{i}] {start_name} → {target_name}")
        
        t0 = time.time()
        result = dijkstra(graph, start, target)   # result berisi path, total_distance (weighted), node_count
        elapsed_ms = (time.time() - t0) * 1000
        
        if result['path'] and result['total_distance'] > 0:
            path = result['path']
            halte = result['node_count']
            
            # HITUNG JARAK FISIK (Haversine murni)
            physical_meter = 0
            for j in range(len(path) - 1):
                s1 = path[j]
                s2 = path[j+1]
                if s1 in stop_map and s2 in stop_map:
                    lat1 = stop_map[s1]['lat']
                    lon1 = stop_map[s1]['lon']
                    lat2 = stop_map[s2]['lat']
                    lon2 = stop_map[s2]['lon']
                    physical_meter += haversine(lat1, lon1, lat2, lon2)
            jarak_fisik_km = physical_meter / 1000
            jarak_weighted_km = result['total_distance'] / 1000
            
            print(f"    ✅ Jarak Fisik: {jarak_fisik_km:.2f} km, Jarak Weighted: {jarak_weighted_km:.2f} km, {halte} halte, waktu: {elapsed_ms:.2f} ms")
            
            # Simpan jarak fisik ke results (bukan weighted)
            results.append((start_name, target_name, jarak_fisik_km, halte, elapsed_ms))
        else:
            print(f"    ❌ Rute tidak ditemukan")
            results.append((start_name, target_name, None, None, None))
    
    print("\n" + "=" * 60)
    print("RINGKASAN 10 TES ACAK (JARAK FISIK)")
    print("=" * 60)
    for i, (s, t, jarak, halte, waktu) in enumerate(results, 1):
        if jarak is not None:
            print(f"{i}. {s} → {t}: {jarak:.2f} km, {halte} halte, {waktu:.2f} ms")
        else:
            print(f"{i}. {s} → {t}: TIDAK DITEMUKAN")
    print("=" * 60)
    
if __name__ == "__main__":
    main()
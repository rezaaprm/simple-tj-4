import pymysql
import math
import heapq
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
# PARAMETER BOBOT (CARI 1)
# ============================================================
TRANSFER_PENALTY = 2500
BUS_WEIGHT = 800
LONG_BUS_THRESHOLD = 4000
LONG_BUS_PENALTY = 100000
STAY_BONUS = 0
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

def build_graph(stop_map, routes):
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

def dijkstra_with_path(graph, start_id, target_id, stop_map):
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
    
    # Rekonstruksi
    path = []
    current = target_id
    while current is not None:
        path.insert(0, current)
        current = previous[current]
    
    total_distance = distances[target_id] if distances[target_id] != float('inf') else 0
    
    # Print detail per halte
    print("\n" + "=" * 70)
    print("DETAIL RUTE (LONCATAN JARAK PER HALTE)")
    print("=" * 70)
    print(f"{'No':>4} | {'Nama Halte':<35} | {'Jarak ke Sebelumnya (km)':>22} | {'Kumulatif (km)':>14}")
    print("-" * 85)
    
    cumulative = 0
    for i, stop_id in enumerate(path):
        stop_name = stop_map.get(stop_id, {}).get('name', stop_id)
        if i == 0:
            print(f"{i+1:>4} | {stop_name[:35]:<35} | {'START':>22} | {cumulative:>14.2f}")
        else:
            prev_id = path[i-1]
            lat1 = stop_map[prev_id]['lat']
            lon1 = stop_map[prev_id]['lon']
            lat2 = stop_map[stop_id]['lat']
            lon2 = stop_map[stop_id]['lon']
            segment = haversine(lat1, lon1, lat2, lon2) / 1000  # km
            cumulative += segment
            print(f"{i+1:>4} | {stop_name[:35]:<35} | {segment:>22.2f} | {cumulative:>14.2f}")
    
    print("-" * 85)
    print(f"Total jarak: {cumulative:.2f} km | {len(path)} halte")
    
    return path, cumulative

def find_stop_id_by_name(stop_map, name_keyword):
    results = []
    for sid, info in stop_map.items():
        if name_keyword.lower() in info['name'].lower():
            results.append((sid, info['name']))
    return results

def main():
    print("=" * 70)
    print("DEBUG RUTE EKSTREM: CARI LONCATAN JARAK")
    print("=" * 70)
    
    print("▶ Mengambil data dari database...")
    stop_map = fetch_stops()
    routes = fetch_routes_with_stops()
    print(f"   ✓ {len(stop_map)} halte, {len(routes)} koridor")
    
    print("▶ Membangun graf...")
    graph = build_graph(stop_map, routes)
    print(f"   ✓ {len(graph)} node\n")
    
    # ============================================================
    # RUTE 9: Sbr. Jln. Berlian → Plaza Europa
    # ============================================================
    print("\n" + "=" * 70)
    print("RUTE 9: Sbr. Jln. Berlian → Plaza Europa")
    print("=" * 70)
    
    # Cari ID
    start_results = find_stop_id_by_name(stop_map, "Sbr. Jln. Berlian")
    target_results = find_stop_id_by_name(stop_map, "Plaza Europa")
    
    if start_results and target_results:
        start_id = start_results[0][0]
        target_id = target_results[0][0]
        print(f"Start: {start_results[0][1]} ({start_id})")
        print(f"Target: {target_results[0][1]} ({target_id})")
        path, total = dijkstra_with_path(graph, start_id, target_id, stop_map)
    else:
        print("❌ Stop ID tidak ditemukan!")
    
    # ============================================================
    # RUTE 10: Palem Semi 2 → SDN Tanah Tinggi
    # ============================================================
    print("\n" + "=" * 70)
    print("RUTE 10: Palem Semi 2 → SDN Tanah Tinggi")
    print("=" * 70)
    
    start_results = find_stop_id_by_name(stop_map, "Palem Semi 2")
    target_results = find_stop_id_by_name(stop_map, "SDN Tanah Tinggi")
    
    if start_results and target_results:
        start_id = start_results[0][0]
        target_id = target_results[0][0]
        print(f"Start: {start_results[0][1]} ({start_id})")
        print(f"Target: {target_results[0][1]} ({target_id})")
        path, total = dijkstra_with_path(graph, start_id, target_id, stop_map)
    else:
        print("❌ Stop ID tidak ditemukan!")

if __name__ == "__main__":
    main()
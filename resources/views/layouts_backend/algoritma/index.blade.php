@extends('layouts.backend')

@section('title', 'Algoritma Dijkstra')

@section('content')
    <div class="row">
        <!-- Baris 1: Tiga card dalam satu baris -->
        <div class="col-12">
            <div class="row mt-2">
                <!-- Card 1: Pseudocode -->
                <div class="col-md-4">
                    <div class="card card-warning card-outline h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-code mr-2"></i>
                                Pseudocode Dijkstra
                            </h3>
                        </div>
                        <div class="card-body">
                            <pre class="bg-dark text-light p-3 rounded" style="font-size: 0.85rem; overflow-x: auto;">
function Dijkstra(graph, source, target)
    distances = {}
    previous = {}
    unvisited = new Set()

    for each node in graph
        distances[node] = infinity
        previous[node] = null
        unvisited.add(node)

    distances[source] = 0

    while unvisited not empty
        current = node in unvisited with min distance

        if current == target
            break

        unvisited.remove(current)

        for each neighbor in graph[current]
            alt = distances[current] + cost(current, neighbor)

            if alt < distances[neighbor]
                distances[neighbor] = alt
                previous[neighbor] = current

    return reconstructPath(previous, target)
                        </pre>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Dijkstra mencari jalur terpendek dengan
                                    menjumlahkan bobot dari node awal ke semua node tetangga.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Rumus Dijkstra -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calculator mr-2"></i>
                                Rumus Dijkstra
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h4 class="bg-light p-3 rounded">
                                    d[v] = min(d[v], d[u] + w(u,v))
                                </h4>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">Komponen</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>d[v]</strong></td>
                                        <td>Jarak terpendek dari node awal ke node v</td>
                                    </tr>
                                    <tr>
                                        <td><strong>d[u]</strong></td>
                                        <td>Jarak terpendek dari node awal ke node u</td>
                                    </tr>
                                    <tr>
                                        <td><strong>w(u,v)</strong></td>
                                        <td>Bobot (jarak) antara node u dan node v</td>
                                    </tr>
                                    <tr>
                                        <td><strong>min()</strong></td>
                                        <td>Ambil nilai terkecil dari perbandingan</td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr>

                            <h5>Heuristik Haversine (Jarak Euclidean)</h5>
                            <pre class="bg-dark text-light p-3 rounded" style="font-size: 0.75rem;">
a = sin²(Δφ/2) + cos φ1 * cos φ2 * sin²(Δλ/2)
c = 2 * atan2(√a, √(1−a))
d = R * c
                        </pre>
                            <p><small>R = 6371000 meter (radius bumi)</small></p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Bobot Preferensi -->
                <div class="col-md-4">
                    <div class="card card-success card-outline h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-balance-scale mr-2"></i>
                                Bobot & Parameter Dijkstra
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Nilai</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Penalti Pindah Koridor</td>
                                        <td class="text-center"><span class="badge badge-warning">{{ number_format($params['transfer_penalty']) }} m</span></td>
                                        <td><small>Pinalti setiap ganti koridor</small></td>
                                    </tr>
                                    <tr>
                                        <td>Maksimal Jalan Kaki</td>
                                        <td class="text-center"><span class="badge badge-success">{{ $params['max_walking'] }} m</span></td>
                                        <td><small>Jarak maksimal jalan kaki antar halte</small></td>
                                    </tr>
                                    <tr>
                                        <td>Bobot Naik Bus</td>
                                        <td class="text-center"><span class="badge badge-info">{{ number_format($params['bus_weight']) }} m</span></td>
                                        <td><small>Tambahan bobot untuk preferensi bus</small></td>
                                    </tr>
                                    <tr>
                                        <td>Ambang Bus Jauh</td>
                                        <td class="text-center"><span class="badge badge-secondary">{{ number_format($params['long_bus_threshold']) }} m</span></td>
                                        <td><small>Jarak dianggap bus jarak jauh</small></td>
                                    </tr>
                                    <tr>
                                        <td>Pinalti Bus Jauh</td>
                                        <td class="text-center"><span class="badge badge-danger">{{ number_format($params['long_bus_penalty']) }} m</span></td>
                                        <td><small>Pinalti untuk bus jarak jauh</small></td>
                                    </tr>
                                    <tr>
                                        <td>Multiplier Jalan Kaki</td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary">&lt;100m: {{ $params['walk_multiplier_short'] }}x</span><br>
                                            <span class="badge badge-secondary">≥100m: {{ $params['walk_multiplier_long'] }}x</span>
                                        </td>
                                        <td><small>Bobot jalan kaki dikalikan multiplier</small></td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr>

                            <h6>Kecepatan Estimasi</h6>
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Kecepatan Bus</span>
                                    <span class="badge badge-info">{{ $params['bus_speed'] }} km/jam</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Kecepatan Jalan Kaki</span>
                                    <span class="badge badge-secondary">{{ $params['walk_speed'] }} km/jam</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris 2: Detail perhitungan rute terakhir -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-route mr-2"></i>
                        Detail Perhitungan Dijkstra - Rute Terakhir
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info" id="lastUpdateTime">
                            @if ($lastRoute)
                                {{ \Carbon\Carbon::parse($lastRoute['timestamp'] ?? now())->format('d/m/Y H:i:s') }}
                            @else
                                Belum ada pencarian
                            @endif
                        </span>
                        @if ($lastRoute)
                            <button type="button" class="btn btn-sm btn-outline-warning ml-2" onclick="resetLastRoute()" style="font-style: italic;">
                                <i class="fas fa-undo-alt"></i> Reset
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body" id="lastRouteContent">
                    @if ($lastRoute)
                        @php
                            $totalDistanceKm = ($lastRoute['total_distance'] ?? 0) / 1000;
                            $estimatedBusTime = ($totalDistanceKm / $params['bus_speed']) * 60;
                            $transferTime = ($lastRoute['total_transfers'] ?? 0) * 5;
                            $totalMinutes = round($estimatedBusTime + $transferTime);
                        @endphp

                        <div class="row">
                            <!-- Kolom Kiri: Info Rute -->
                            <div class="col-md-5">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center">
                                            <strong>Rute Perjalanan</strong>
                                            @if (isset($lastRoute['preference']))
                                                <small class="text-muted">({{ $lastRoute['preference'] == 'distance' ? 'Cari 1 - Prioritas Jarak' : 'Cari 2 - Prioritas Transfer' }})</small>
                                            @endif
                                        </span>
                                        <div class="text-center mt-2">
                                            <span class="badge badge-success p-2">{{ $lastRoute['start_stop'] ?? '-' }}</span>
                                            <i class="fas fa-arrow-right mx-2"></i>
                                            <span class="badge badge-danger p-2">{{ $lastRoute['end_stop'] ?? '-' }}</span>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <small>Halte Dilewati:</small>
                                                <h5 class="mb-0">{{ number_format($lastRoute['total_stops'] ?? 0) }}</h5>
                                            </div>
                                            <div class="col-6">
                                                <small>Pindah Koridor:</small>
                                                <h5 class="mb-0">{{ number_format($lastRoute['total_transfers'] ?? 0) }}</h5>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <small>Jarak Tempuh:</small>
                                                <h5 class="mb-0">{{ number_format($totalDistanceKm, 2) }} km</h5>
                                            </div>
                                            <div class="col-6">
                                                <small>Estimasi Waktu:</small>
                                                <h5 class="mb-0">{{ $totalMinutes }} menit</h5>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <small>Waktu Eksekusi Algoritma:</small>
                                                <h5 class="mb-0">{{ number_format($lastRoute['execution_time'] ?? 0, 2) }} ms</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Tengah: Koridor yang Dilewati -->
                            <div class="col-md-4">
                                <div class="card card-outline card-warning">
                                    <div class="card-header py-2">
                                        <h6 class="card-title"><i class="fas fa-bus"></i> Koridor Dilewati</h6>
                                    </div>
                                    <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                        @if (isset($lastRoute['koridors']) && count($lastRoute['koridors']) > 0)
                                            @foreach ($lastRoute['koridors'] as $index => $koridor)
                                                <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                                    <span class="badge badge-primary mr-2">{{ $index + 1 }}</span>
                                                    <span class="font-weight-bold">Koridor {{ $koridor['short_name'] ?? $koridor }}</span>
                                                    @if (isset($koridor['color']))
                                                        <div class="ml-auto" style="width: 20px; height: 20px; background: {{ $koridor['color'] }}; border-radius: 4px;"></div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted text-center">Tidak ada data koridor</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Jalur Halte -->
                            <div class="col-md-3">
                                <div class="card card-outline card-success">
                                    <div class="card-header py-2">
                                        <h6 class="card-title"><i class="fas fa-map-pin"></i> Jalur Halte</h6>
                                    </div>
                                    <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                        @if (isset($lastRoute['route_path']) && count($lastRoute['route_path']) > 0)
                                            @foreach ($lastRoute['route_path'] as $index => $stop)
                                                <div class="d-flex align-items-center mb-1 small">
                                                    <span class="badge badge-secondary mr-2">{{ $index + 1 }}</span>
                                                    <span class="text-truncate">{{ $stop['name'] ?? $stop }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted text-center">Tidak ada data halte</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Jalan Kaki (POI) -->
                        <!-- Tanpa nested row -->
                        @if (isset($lastRoute['walking_info']))
                            <div class="col-12 mb-3">
                                @if (isset($lastRoute['walking_info']['start']) && $lastRoute['walking_info']['start'])
                                    <div class="alert alert-warning p-2 mb-2" style="background: #fff3e0; border-left: 4px solid #e67e22;">
                                        <i class="fas fa-walking text-warning"></i>
                                        <strong>Jalan Kaki Awal:</strong>
                                        {{ $lastRoute['walking_info']['start']['from_poi'] ?? '-' }} →
                                        {{ $lastRoute['walking_info']['start']['to_stop'] ?? '-' }}
                                        ({{ $lastRoute['walking_info']['start']['distance_km'] ?? '0' }} km)
                                    </div>
                                @endif

                                @if (isset($lastRoute['walking_info']['end']) && $lastRoute['walking_info']['end'])
                                    <div class="alert alert-warning p-2" style="background: #fff3e0; border-left: 4px solid #e67e22;">
                                        <i class="fas fa-walking text-warning"></i>
                                        <strong>🚶 Jalan Kaki Tujuan:</strong>
                                        {{ $lastRoute['walking_info']['end']['from_stop'] ?? '-' }} →
                                        {{ $lastRoute['walking_info']['end']['to_poi'] ?? '-' }}
                                        ({{ $lastRoute['walking_info']['end']['distance_km'] ?? '0' }} km)
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Penjelasan Perhitungan -->
                        <div class="alert alert-secondary mt-3">
                            <h6><i class="fas fa-calculator"></i> Penjelasan Perhitungan Dijkstra:</h6>
                            <small>
                                <ul class="mb-0">
                                    <li><strong>Bobot perjalanan</strong> = Jarak antar halte (meter) + Bobot tambahan</li>
                                    <li><strong>Pindah koridor</strong> = +{{ number_format($params['transfer_penalty']) }} meter (pinalti)</li>
                                    <li><strong>Naik bus</strong> = +{{ number_format($params['bus_weight']) }} meter (preferensi)</li>
                                    <li><strong>Jalan kaki</strong> = Jarak × {{ $params['walk_multiplier_short'] }}x (jarak pendek) atau {{ $params['walk_multiplier_long'] }}x (jarak jauh)</li>
                                    <li><strong>Total bobot</strong> = Σ(Jarak × Bobot) + Σ(Pinalti)</li>
                                    <li><strong>Estimasi waktu</strong> = (Jarak total / kecepatan bus) + (pindah koridor × 5 menit)</li>
                                </ul>
                            </small>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-route fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada pencarian rute. Lakukan pencarian di halaman <strong>Peta Rute TransJakarta</strong> untuk melihat detail perhitungan Dijkstra.</p>
                            <a href="{{ route('admin.transjakarta.map') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-map-marked-alt"></i> Buka Peta Rute
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- BARIS 3: DETAIL RUTE DARI LOG PENCARIAN -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-info card-outline" id="cardLogRoute">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>
                        Detail Perhitungan Dijkstra - Rute dari Log Pencarian
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info" id="logSelectedId">Belum dipilih</span>
                        <button type="button" class="btn btn-sm btn-outline-warning ml-2" id="resetLogBtn" style="font-style: italic; display: none;" onclick="resetLogRoute()">
                            <i class="fas fa-undo-alt"></i> Reset
                        </button>
                    </div>
                </div>
                <div class="card-body" id="logRouteContent">
                    <div class="text-center py-5">
                        <i class="fas fa-click fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Klik nomor di halaman <strong>Log Pencarian</strong> untuk melihat detail perhitungan rute dari riwayat.</p>
                        <a href="{{ route('admin.pencarian.log') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-list"></i> Buka Log Pencarian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Helper function untuk escape HTML (TAMBAHKAN INI)
        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // Fungsi reset card 2 (Rute Terakhir)
        function resetLastRoute() {
            if (confirm('Reset tampilan rute terakhir?')) {
                // Kosongkan konten card 2
                document.getElementById('lastRouteContent').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-route fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada pencarian rute. Lakukan pencarian di halaman <strong>Peta Rute TransJakarta</strong> untuk melihat detail perhitungan Dijkstra.</p>
                    <a href="{{ route('admin.transjakarta.map') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-map-marked-alt"></i> Buka Peta Rute
                    </a>
                </div>
            `;

                // Update timestamp
                document.getElementById('lastUpdateTime').innerHTML = 'Belum ada pencarian';

                // Sembunyikan tombol reset
                const resetBtn = document.querySelector('#lastRouteContent').closest('.card').querySelector('.btn-outline-warning');
                if (resetBtn) resetBtn.style.display = 'none';

                // Hapus data di session via AJAX
                fetch('/admin/algoritma/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).catch(console.error);
            }
        }

        // Fungsi reset card 3 (Rute dari Log)
        function resetLogRoute() {
            if (confirm('Reset tampilan rute dari log?')) {
                // Kosongkan konten card 3
                document.getElementById('logRouteContent').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-click fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Klik nomor di halaman <strong>Log Pencarian</strong> untuk melihat detail perhitungan rute dari riwayat.</p>
                    <a href="{{ route('admin.pencarian.log') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-list"></i> Buka Log Pencarian
                    </a>
                </div>
            `;

                // Reset badge
                document.getElementById('logSelectedId').innerHTML = 'Belum dipilih';

                // Sembunyikan tombol reset
                const resetBtn = document.getElementById('resetLogBtn');
                if (resetBtn) resetBtn.style.display = 'none';

                // Hapus parameter URL tanpa reload
                const url = new URL(window.location.href);
                url.searchParams.delete('log_id');
                window.history.pushState({}, '', url);
            }
        }

        // Fungsi untuk menampilkan data dari log
        function displayLogRoute(data) {
            const totalDistanceKm = (data.total_distance || 0) / 1000;
            const estimatedBusTime = (totalDistanceKm / 25) * 60;
            const transferTime = (data.total_transfers || 0) * 5;
            const totalMinutes = Math.round(estimatedBusTime + transferTime);

            const koridors = data.koridors || [];
            const routePath = data.route_path || [];

            // ========== HTML ==========
            let html = '';

            // Info jalan kaki (POI) di awal
            if (data.walking_info) {
                if (data.walking_info.start) {
                    html += `
                <div class="alert alert-warning p-2 mb-2" style="background: #fff3e0; border-left: 4px solid #e67e22;">
                    <i class="fas fa-walking text-warning"></i>
                    <strong>🚶 Jalan Kaki Awal:</strong>
                    ${escapeHtml(data.walking_info.start.from_poi)} →
                    ${escapeHtml(data.walking_info.start.to_stop)}
                    (${data.walking_info.start.distance_km} km)
                </div>
            `;
                }

                if (data.walking_info.end) {
                    html += `
                <div class="alert alert-warning p-2 mb-3" style="background: #fff3e0; border-left: 4px solid #e67e22;">
                    <i class="fas fa-walking text-warning"></i>
                    <strong>🚶 Jalan Kaki Tujuan:</strong>
                    ${escapeHtml(data.walking_info.end.from_stop)} →
                    ${escapeHtml(data.walking_info.end.to_poi)}
                    (${data.walking_info.end.distance_km} km)
                </div>
            `;
                }
            }

            // Row yang ada yaitu kiri, tengah, dan kanan
            html += `
        <div class="row">
            <!-- Kolom Kiri: Info Rute -->
            <div class="col-md-5">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text text-center"><strong>Rute Perjalanan</strong></span>
                        <div class="text-center mt-2">
                            <span class="badge badge-success p-2">${escapeHtml(data.start_stop || '-')}</span>
                            <i class="fas fa-arrow-right mx-2"></i>
                            <span class="badge badge-danger p-2">${escapeHtml(data.end_stop || '-')}</span>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <small>Halte Dilewati:</small>
                                <h5 class="mb-0">${data.total_stops || 0}</h5>
                            </div>
                            <div class="col-6">
                                <small>Pindah Koridor:</small>
                                <h5 class="mb-0">${data.total_transfers || 0}</h5>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <small>Jarak Tempuh:</small>
                                <h5 class="mb-0">${totalDistanceKm.toFixed(2)} km</h5>
                            </div>
                            <div class="col-6">
                                <small>Estimasi Waktu:</small>
                                <h5 class="mb-0">${totalMinutes} menit</h5>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <small>Waktu Eksekusi Algoritma:</small>
                                <h5 class="mb-0">${(data.execution_time || 0).toFixed(2)} ms</h5>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <small>Tanggal Pencarian:</small>
                                <h5 class="mb-0">${data.timestamp || '-'}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Kolom Tengah: Koridor Dilewati -->
            <div class="col-md-4">
                <div class="card card-outline card-warning">
                    <div class="card-header py-2">
                        <h6 class="card-title"><i class="fas fa-bus"></i> Koridor Dilewati</h6>
                    </div>
                    <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                        ${koridors.length > 0 ? koridors.map((koridor, idx) => `
                                <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                    <span class="badge badge-primary mr-2">${idx + 1}</span>
                                    <span class="font-weight-bold">Koridor ${escapeHtml(koridor.short_name || koridor)}</span>
                                    <div class="ml-auto" style="width: 20px; height: 20px; background: ${koridor.color || '#ccc'}; border-radius: 4px;"></div>
                                </div>
                            `).join('') : '<p class="text-muted text-center">Tidak ada data koridor</p>'}
                    </div>
                </div>
            </div>
            
            <!-- Kolom Kanan: Jalur Halte -->
            <div class="col-md-3">
                <div class="card card-outline card-success">
                    <div class="card-header py-2">
                        <h6 class="card-title"><i class="fas fa-map-pin"></i> Jalur Halte</h6>
                    </div>
                    <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                        ${routePath.length > 0 ? routePath.map((stop, idx) => `
                                <div class="d-flex align-items-center mb-1 small">
                                    <span class="badge badge-secondary mr-2">${stop.order || idx + 1}</span>
                                    <span class="text-truncate">${escapeHtml(stop.name || stop)}</span>
                                </div>
                            `).join('') : '<p class="text-muted text-center">Tidak ada data halte</p>'}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Penjelasan Perhitungan -->
        <div class="alert alert-secondary mt-3">
            <h6><i class="fas fa-calculator"></i> Penjelasan Perhitungan Dijkstra:</h6>
            <small>
                <ul class="mb-0">
                    <li><strong>Bobot perjalanan</strong> = Jarak antar halte (meter) + Bobot tambahan</li>
                    <li><strong>Pindah koridor</strong> = +2500 meter (pinalti)</li>
                    <li><strong>Naik bus</strong> = +800 meter (preferensi)</li>
                    <li><strong>Jalan kaki</strong> = Jarak × 5x (jarak pendek) atau 50x (jarak jauh)</li>
                    <li><strong>Estimasi waktu</strong> = (Jarak total / kecepatan bus) + (pindah koridor × 5 menit)</li>
                </ul>
            </small>
        </div>
    `;

            document.getElementById('logRouteContent').innerHTML = html;
            document.getElementById('logSelectedId').innerHTML = `Log ID #${data.id}`;

            // Tampilkan tombol reset
            const resetBtn = document.getElementById('resetLogBtn');
            if (resetBtn) resetBtn.style.display = 'inline-block';
        }

        // Cek URL parameter saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const logId = urlParams.get('log_id');

            if (logId) {
                fetch('/api/pencarian-log/' + logId)
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            displayLogRoute(result.data);
                        } else {
                            document.getElementById('logRouteContent').innerHTML = `
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle"></i> ${result.message || 'Gagal mengambil data log'}
                            </div>
                        `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('logRouteContent').innerHTML = `
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-triangle"></i> Gagal mengambil data log
                        </div>
                    `;
                    });
            }
        });
    </script>
@endpush

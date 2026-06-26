@extends('layouts.backend')

@section('title', 'Peta Rute TransJakarta')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row mt-2">
                <!-- Card Kiri: Cari Rute & Detail Rute -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-map-marked-alt mr-2"></i>
                                Peta Rute TransJakarta
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" onclick="toggleAllStops()">
                                    <i class="fas fa-map-pin"></i> ON Halte
                                </button>
                                <button type="button" class="btn btn-tool" onclick="removeAllStops()">
                                    <i class="fas fa-eye-slash"></i> OFF Halte
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h5><i class="fas fa-search mr-2"></i>Cari Rute</h5>

                            <div class="form-group">
                                <label>📍 Halte Awal</label>
                                <input type="text" class="form-control form-control-sm" id="startSearch" placeholder="Cari halte awal..." autocomplete="off">
                                <div id="startResults" class="list-group mt-1" style="position: absolute; z-index: 1000; width: 90%; display: none;"></div>
                            </div>

                            <div class="form-group">
                                <label>🏁 Halte Tujuan</label>
                                <input type="text" class="form-control form-control-sm" id="endSearch" placeholder="Cari halte tujuan..." autocomplete="off">
                                <div id="endResults" class="list-group mt-1" style="position: absolute; z-index: 1000; width: 90%; display: none;"></div>
                            </div>

                            <div id="selectedStopsInfo" class="alert alert-info p-2 small" style="display: none;">
                                <div><strong>Dari:</strong> <span id="startStopInfo"></span></div>
                                <div><strong>Ke:</strong> <span id="endStopInfo"></span></div>
                            </div>

                            <!-- <div class="row">
                                                            <div class="col-6">
                                                                <button class="btn btn-secondary btn-block btn-sm" id="clearBtn" onclick="clearRoute()" disabled>
                                                                    <i class="fas fa-undo"></i> Reset
                                                                </button>
                                                            </div>
                                                            <div class="col-6">
                                                                <button class="btn btn-warning btn-block btn-sm" id="routeBtn" onclick="calculateRoute()" disabled>
                                                                    <i class="fas fa-route"></i> Cari
                                                                </button>
                                                            </div>
                                                        </div> -->

                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-secondary btn-block btn-sm" id="clearBtn" onclick="clearRoute()" disabled>
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <button class="btn btn-info btn-block btn-sm" id="routeBtn1" onclick="calculateRoute1()" disabled>
                                                <i class="fas fa-route"></i> Cari 1
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-warning btn-block btn-sm" id="routeBtn2" onclick="calculateRoute2()" disabled>
                                                <i class="fas fa-route"></i> Cari 2
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h5><i class="fas fa-info-circle mr-2"></i>Detail Rute</h5>
                            <div id="routeInfoPanel" class="small">
                                <p class="text-muted text-center">Belum ada rute dipilih</p>
                            </div>

                            <hr>

                            <h5><i class="fas fa-bus mr-2"></i>Koridor Dilewati</h5>
                            <div id="routeKoridorList" style="max-height: 150px; overflow-y: auto;">
                                <p class="text-muted text-center">Cari rute untuk melihat</p>
                            </div>

                            <hr>

                            <h5><i class="fas fa-map-pin mr-2"></i>Halte Dilewati</h5>
                            <div id="routeStopsList" style="max-height: 200px; overflow-y: auto;">
                                <p class="text-muted text-center">Cari rute untuk melihat</p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                <i class="fas fa-clock mr-1"></i> Load time: {{ $loadTime ?? 0 }}s |
                                <i class="fas fa-route mr-1"></i> {{ $totalRoutes ?? 0 }} koridor |
                                <i class="fas fa-map-pin mr-1"></i> {{ $totalStops ?? 0 }} halte
                            </small>
                        </div>
                    </div>
                </div>

                <!-- CARD KANAN: MAP -->
                <div class="col-md-8">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-map mr-2"></i>
                                Peta Interaktif
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" onclick="toggleAllRoutes()">
                                    <i class="fas fa-eye"></i> ON Koridor
                                </button>
                                <button type="button" class="btn btn-tool" onclick="hideAllRoutes()">
                                    <i class="fas fa-eye-slash"></i> OFF Koridor
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="map" style="height: 600px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BARIS 2: DAFTAR KORIDOR (DROPDOWN) -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bus mr-2"></i>
                                Daftar Koridor
                            </h3>
                            <div class="card-tools">
                                <input type="text" class="form-control form-control-sm" id="koridorSearch" placeholder="Filter koridor atau halte..." style="width: 200px;">
                            </div>
                        </div>
                        <div class="card-body p-2" style="max-height: 300px; overflow-y: auto;">
                            <div id="koridor-list">
                                @foreach ($routes as $route)
                                    @php
                                        $stopsCount = count($route['stops']);
                                        $isActive = $stopsCount > 0;
                                    @endphp
                                    <div class="koridor-dropdown" data-route-id="{{ $route['id'] }}"
                                        data-short-name="Koridor {{ $route['short_name'] }}"
                                        data-long-name="{{ $route['long_name'] }}"
                                        data-stops="{{ json_encode(array_column($route['stops'], 'name')) }}">

                                        <div class="koridor-header" style="border-left-color: {{ $isActive ? $route['color'] : '#ccc' }}; {{ !$isActive ? 'opacity: 0.8;' : '' }}" onclick="toggleDropdown(this)">
                                            <div class="koridor-info">
                                                <span class="koridor-name">Koridor {{ $route['short_name'] }}</span>
                                                <span class="koridor-desc">{{ $route['long_name'] }}</span>
                                                @if (!$isActive)
                                                    <span class="badge badge-warning ml-2" style="background: #f39c12; color: #000;">
                                                        <i class="fas fa-calendar-times"></i> Libur hari ini
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="arrow">▼</span>
                                        </div>

                                        <div class="koridor-body">
                                            @if ($isActive)
                                                <ul class="halte-list">
                                                    @foreach ($route['stops'] as $stopIdx => $stop)
                                                        <li class="halte-item" data-stop-name="{{ $stop['name'] }}" onclick="event.stopPropagation(); focusStop({{ $stop['lat'] }}, {{ $stop['lng'] }}, '{{ $stop['name'] }}')">
                                                            {{ $stop['name'] }}
                                                            <span class="badge">{{ $stopIdx + 1 }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="alert alert-warning text-center p-3 m-2" style="background: #fff3cd; border-radius: 6px; border-left: 4px solid #f39c12;">
                                                    <i class="fas fa-info-circle text-warning"></i>
                                                    <strong>Koridor ini TIDAK BEROPERASI pada hari ini.</strong><br>
                                                    <small class="text-muted">Silakan cek kembali di akhir pekan.</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map {
            min-height: 600px;
            width: 100%;
        }

        #startResults,
        #endResults {
            position: absolute;
            z-index: 1050;
            width: 92%;
            max-height: 200px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .autocomplete-item {
            cursor: pointer;
            padding: 8px 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .autocomplete-item:hover {
            background-color: #f8f9fa;
        }

        .leaflet-control-attribution {
            display: none !important;
        }

        .route-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 8px 0;
            padding: 8px 10px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .route-info-item:hover {
            background: #e9ecef;
            transform: translateX(3px);
        }

        .route-color-box {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .stops-list-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            margin: 4px 0;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #27ae60;
            cursor: pointer;
            transition: all 0.2s;
        }

        .stops-list-item:hover {
            background: #e9ecef;
            transform: translateX(3px);
        }

        .stops-list-item .stop-number {
            width: 26px;
            height: 26px;
            background: #27ae60;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .stops-list-item .stop-name {
            flex: 1;
        }

        .stops-list-item .stop-route {
            font-size: 0.7rem;
            color: #666;
        }

        /* Dropdown Koridor */
        .koridor-dropdown {
            margin-bottom: 8px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s ease;
        }

        .koridor-header {
            background: white;
            padding: 12px 15px;
            font-weight: bold;
            border-left: 5px solid;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.2s;
        }

        .koridor-header:hover {
            background: #e3f2fd;
        }

        .koridor-header .arrow {
            transition: transform 0.3s ease;
        }

        .koridor-header.open .arrow {
            transform: rotate(180deg);
        }

        .koridor-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: white;
            border-top: 1px solid #eee;
            transition: max-height 0.3s ease-out;
        }

        .koridor-body.open {
            max-height: 300px !important;
            overflow-y: auto;
        }

        .halte-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .halte-item {
            padding: 8px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .halte-item:hover {
            background: #f8f9fa;
        }

        .halte-item .badge {
            background-color: #3498db !important;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
        }

        .highlight {
            background-color: #fff3cd !important;
            color: #856404 !important;
            font-weight: bold;
            padding: 0 2px;
            border-radius: 3px;
        }

        .no-results {
            padding: 15px;
            text-align: center;
            color: #666;
            font-style: italic;
            background: white;
            border-radius: 6px;
            margin-top: 10px;
        }
    </style>
@endpush

@push('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // ==================== Data dari Controller ====================
        const routes = @json($routes);
        console.log('Routes loaded:', routes.length);

        // ==================== Inisialisasi Peta ====================
        const jakartaBounds = L.latLngBounds(L.latLng(-6.4, 106.6), L.latLng(-6.0, 107.0));
        const map = L.map('map', {
            center: [-6.2088, 106.8456],
            zoom: 12,
            minZoom: 11,
            maxZoom: 18,
            maxBounds: jakartaBounds,
            maxBoundsViscosity: 1.0,
            attributionControl: false
        });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ''
        }).addTo(map);

        // ==================== Variabel Global ====================
        const routeLayers = {};
        let activeRoutes = new Set();
        let stopMarkers = {};
        let stopsVisible = false;
        let selectedStartStop = null;
        let selectedEndStop = null;
        let routeLayers_temp = {};
        let currentRouteMarkers = [];
        let selectingPoi = false;


        const allStopsWithRoutes = [];
        routes.forEach(route => {
            route.stops.forEach((stop, index) => {
                if (stop.lat && stop.lng && !isNaN(stop.lat) && !isNaN(stop.lng)) {
                    allStopsWithRoutes.push({
                        id: stop.id,
                        name: stop.name,
                        lat: stop.lat,
                        lng: stop.lng,
                        routeId: route.id,
                        routeName: `Koridor ${route.short_name}`,
                        routeColor: route.color,
                        stopNumber: index + 1,
                        searchText: `${stop.name} ${route.short_name} ${route.long_name}`.toLowerCase()
                    });
                }
            });
        });

        function haversineDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const φ1 = lat1 * Math.PI / 180,
                φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180,
                Δλ = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(Δφ / 2) ** 2 + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) ** 2;
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // Fungsi Helper untuk escape HTML
        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // ==================== Fungsi Gambar Semua Rute ====================
        function drawAllRoutes() {
            routes.forEach(route => {
                if (route.shape && route.shape.length > 0 && !routeLayers[route.id]) {
                    const polyline = L.polyline(route.shape, {
                        color: route.color,
                        weight: 3,
                        opacity: 0.7
                    }).addTo(map);
                    polyline.bindTooltip(`<b>Koridor ${route.short_name}</b><br>${route.long_name}`, {
                        sticky: true
                    });
                    routeLayers[route.id] = polyline;
                    activeRoutes.add(route.id);
                    const dropdown = document.querySelector(`.koridor-dropdown[data-route-id="${route.id}"]`);
                    if (dropdown) {
                        dropdown.classList.add('active');
                    }
                }
            });
            updateActiveRoutesCount();
        }

        // ==================== Fungsi Halte ====================
        function drawAllStops() {
            if (stopsVisible) return;
            const allStops = new Map();
            routes.forEach(route => route.stops.forEach(stop => {
                if (!allStops.has(stop.id)) allStops.set(stop.id, stop);
            }));
            allStops.forEach(stop => {
                if (stop.lat && stop.lng) {
                    const marker = L.marker([stop.lat, stop.lng], {
                        icon: L.divIcon({
                            html: '<div style="background:#27ae60; width:8px; height:8px; border-radius:50%; border:2px solid white;"></div>',
                            iconSize: [12, 12],
                            iconAnchor: [6, 6]
                        })
                    }).bindPopup(`<b>${stop.name}</b>`);
                    marker.addTo(map);
                    stopMarkers[stop.id] = marker;
                }
            });
            stopsVisible = true;
        }

        function removeAllStops() {
            Object.values(stopMarkers).forEach(m => map.removeLayer(m));
            stopMarkers = {};
            stopsVisible = false;
        }
        window.toggleAllStops = function() {
            stopsVisible ? removeAllStops() : drawAllStops();
        };
        window.removeAllStops = removeAllStops;

        // ==================== Toggle Rute ====================
        function toggleRoute(routeId) {
            if (activeRoutes.has(routeId)) {
                if (routeLayers[routeId]) {
                    map.removeLayer(routeLayers[routeId]);
                    delete routeLayers[routeId];
                }
                activeRoutes.delete(routeId);
                const dropdown = document.querySelector(`.koridor-dropdown[data-route-id="${routeId}"]`);
                if (dropdown) dropdown.classList.remove('active');
            } else {
                const route = routes.find(r => r.id == routeId);
                if (route && route.shape && route.shape.length > 0) {
                    const polyline = L.polyline(route.shape, {
                        color: route.color,
                        weight: 4,
                        opacity: 0.8
                    }).addTo(map);
                    polyline.bindTooltip(`<b>Koridor ${route.short_name}</b>`);
                    routeLayers[routeId] = polyline;
                    activeRoutes.add(routeId);
                    const dropdown = document.querySelector(`.koridor-dropdown[data-route-id="${routeId}"]`);
                    if (dropdown) dropdown.classList.add('active');
                }
            }
            updateActiveRoutesCount();
        }
        window.toggleRoute = toggleRoute;

        // ==================== Toggle Semua Koridor ====================
        window.toggleAllRoutes = function() {
            const allDropdowns = document.querySelectorAll('.koridor-dropdown');

            if (activeRoutes.size > 0) {
                // OFF: Matikan semua koridor dan tutup semua dropdown
                Object.keys(routeLayers).forEach(id => {
                    map.removeLayer(routeLayers[id]);
                    delete routeLayers[id];
                });
                activeRoutes.clear();

                // Tutup semua dropdown
                allDropdowns.forEach(d => {
                    d.classList.remove('active');
                    const header = d.querySelector('.koridor-header');
                    const body = d.querySelector('.koridor-body');
                    if (header) header.classList.remove('open');
                    if (body) body.classList.remove('open');
                });
            } else {
                // ON: Hidupkan semua koridor dan buka semua dropdown
                routes.forEach(route => {
                    if (route.shape && route.shape.length > 0 && !routeLayers[route.id]) {
                        const polyline = L.polyline(route.shape, {
                            color: route.color,
                            weight: 3,
                            opacity: 0.7
                        }).addTo(map);
                        polyline.bindTooltip(`<b>Koridor ${route.short_name}</b>`);
                        routeLayers[route.id] = polyline;
                        activeRoutes.add(route.id);

                        // Buka dropdown
                        const dropdown = document.querySelector(`.koridor-dropdown[data-route-id="${route.id}"]`);
                        if (dropdown) {
                            dropdown.classList.add('active');
                            const header = dropdown.querySelector('.koridor-header');
                            const body = dropdown.querySelector('.koridor-body');
                            if (header) header.classList.add('open');
                            if (body) body.classList.add('open');
                        }
                    }
                });

                // Zoom ke semua koridor
                if (Object.keys(routeLayers).length > 0) {
                    const group = L.featureGroup(Object.values(routeLayers));
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }
            updateActiveRoutesCount();
        };

        window.hideAllRoutes = function() {
            // Matikan semua koridor
            Object.keys(routeLayers).forEach(id => {
                map.removeLayer(routeLayers[id]);
                delete routeLayers[id];
            });
            activeRoutes.clear();

            // Tutup semua dropdown
            document.querySelectorAll('.koridor-dropdown').forEach(d => {
                d.classList.remove('active');
                const header = d.querySelector('.koridor-header');
                const body = d.querySelector('.koridor-body');
                if (header) header.classList.remove('open');
                if (body) body.classList.remove('open');
            });

            updateActiveRoutesCount();
        };

        function updateActiveRoutesCount() {
            let activeSpan = document.getElementById('active-routes');
            if (!activeSpan) {
                const footerStats = document.querySelector('.card-footer small');
                if (footerStats) {
                    activeSpan = document.createElement('span');
                    activeSpan.id = 'active-routes';
                    activeSpan.textContent = activeRoutes.size;
                    footerStats.innerHTML += ' | <i class="fas fa-eye mr-1"></i> <span id="active-routes">0</span> aktif';
                }
            } else {
                activeSpan.textContent = activeRoutes.size;
            }
        }

        // ==================== Dropdown Koridor ====================
        window.toggleDropdown = function(header) {
            const dropdown = header.closest('.koridor-dropdown');
            if (!dropdown) return;
            const routeId = dropdown.dataset.routeId;
            const body = header.nextElementSibling;

            // Toggle status rute di peta
            toggleRoute(routeId);

            // Toggle dropdown
            header.classList.toggle('open');
            if (body) body.classList.toggle('open');
        };

        // ==================== Fungsi Search (dengan POI) ====================
        function setupSearch() {
            console.log('--- Setup Search dengan POI ---');

            const startInput = document.getElementById('startSearch');
            const endInput = document.getElementById('endSearch');
            const startResults = document.getElementById('startResults');
            const endResults = document.getElementById('endResults');

            // Fungsi Filter Halte dan POI
            async function filterAllPlaces(query) {
                if (!query || query.length < 2) return [];

                const q = query.toLowerCase();

                // 1. Cari dari halte
                const stopResults = allStopsWithRoutes
                    .filter(stop => stop.searchText.includes(q))
                    .slice(0, 8) // Batasi 8 hasil halte
                    .map(stop => ({
                        type: 'stop',
                        data: stop
                    }));

                // 2. Cari dari POI via API dengan timeout
                let poiResults = [];
                try {
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 3000);

                    const response = await fetch(`/api/geocode?q=${encodeURIComponent(query)}`, {
                        signal: controller.signal
                    });
                    clearTimeout(timeoutId);

                    const poiData = await response.json();
                    poiResults = poiData
                        .slice(0, 5) // Batasi 5 POI
                        .map(poi => ({
                            type: 'poi',
                            data: poi
                        }));
                } catch (e) {
                    console.log('POI search error atau timeout:', e);
                }

                return [...stopResults, ...poiResults];
            }

            // Fungsi Render Sutocomplete
            async function renderAutocomplete(input, resultsDiv, isStart) {
                // Cek Flag jika sedang Select POI
                if (selectingPoi) {
                    resultsDiv.style.display = 'none';
                    return;
                }

                let query = input.value;
                if (query.length < 2) {
                    resultsDiv.style.display = 'none';
                    return;
                }

                // Bersihkan Query dari teks "→ Naik di"
                const arrowIndex = query.indexOf('→');
                if (arrowIndex > -1) {
                    query = query.substring(0, arrowIndex).trim();
                }

                if (query.length < 2) {
                    resultsDiv.style.display = 'none';
                    return;
                }

                const items = await filterAllPlaces(query);

                if (items.length === 0) {
                    resultsDiv.innerHTML = '<div class="list-group-item">Tidak ditemukan</div>';
                    resultsDiv.style.display = 'block';
                    return;
                }

                // Map tanpa async/await di dalam
                resultsDiv.innerHTML = items.map(item => {
                    if (item.type === 'stop') {
                        const stop = item.data;
                        return `
                <div class="list-group-item list-group-item-action autocomplete-item" onclick='selectStopRoute(${JSON.stringify(stop).replace(/'/g, "\\'")}, ${isStart})'>
                    <div>
                        <strong>${escapeHtml(stop.name)}</strong>
                        <br><small>${escapeHtml(stop.routeName)}</small>
                    </div>
                </div>
            `;
                    } else {
                        const poi = item.data;

                        // Data sudah termasuk nearest_stop dari backend
                        let displayName = poi.name;
                        if (poi.nearest_stop && poi.nearest_stop.name) {
                            displayName = `${poi.name} - ${poi.nearest_stop.name} (${poi.nearest_stop.distance_km} km)`;
                        }

                        return `
                <div class="list-group-item list-group-item-action autocomplete-item" onclick='selectPoi(${JSON.stringify(poi).replace(/'/g, "\\'")}, ${isStart})'>
                    <div>
                        <strong>📍 ${escapeHtml(displayName)}</strong>
                        <br><small>${escapeHtml(poi.category)}</small>
                    </div>
                </div>
            `;
                    }
                }).join('');

                resultsDiv.style.display = 'block';
            }

            // Event Listeners dengan debounce
            let startTimer, endTimer;

            startInput.addEventListener('input', () => {
                clearTimeout(startTimer);
                startTimer = setTimeout(() => {
                    renderAutocomplete(startInput, startResults, true);
                }, 300);
            });

            endInput.addEventListener('input', () => {
                clearTimeout(endTimer);
                endTimer = setTimeout(() => {
                    renderAutocomplete(endInput, endResults, false);
                }, 300);
            });

            document.addEventListener('click', (e) => {
                if (!startInput.contains(e.target) && !startResults.contains(e.target)) {
                    startResults.style.display = 'none';
                }
                if (!endInput.contains(e.target) && !endResults.contains(e.target)) {
                    endResults.style.display = 'none';
                }
            });
        }

        // Fungsi untuk memilih halte: selectStopRoute
        window.selectStopRoute = function(stop, isStart) {
            if (isStart) {
                selectedStartStop = stop;
                document.getElementById('startSearch').value = stop.name;
                document.getElementById('startResults').style.display = 'none';
            } else {
                selectedEndStop = stop;
                document.getElementById('endSearch').value = stop.name;
                document.getElementById('endResults').style.display = 'none';
            }
            updateSelectedStopsInfo();
        }

        // Fungsi untuk memilih POI dengan Fallback
        window.selectPoi = async function(poi, isStart) {
            if (selectingPoi) return;
            selectingPoi = true;

            const startInput = document.getElementById('startSearch');
            const endInput = document.getElementById('endSearch');
            const startResults = document.getElementById('startResults');
            const endResults = document.getElementById('endResults');

            try {
                const response = await fetch(`/api/nearest-stop?lat=${poi.lat}&lng=${poi.lng}`);
                const nearest = await response.json();

                if (!nearest || !nearest.stop) {
                    alert(` Lokasi "${poi.name}" terlalu jauh dari halte TransJakarta.`);
                    return;
                }

                console.log('Nearest stop dari API:', nearest.stop);

                // ========== Strategi Pencarian ==========
                let originalStop = null;

                // Strategi 1: Cari berdasarkan stop_id
                originalStop = allStopsWithRoutes.find(s => s.id === nearest.stop.stop_id);
                if (originalStop) {
                    console.log('✅ Ditemukan berdasarkan ID:', originalStop.name);
                }

                // Strategi 2: Cari berdasarkan nama stop (exact match)
                if (!originalStop) {
                    const stopName = nearest.stop.stop_name.toLowerCase();
                    originalStop = allStopsWithRoutes.find(s =>
                        s.name.toLowerCase() === stopName
                    );
                    if (originalStop) {
                        console.log('✅ Ditemukan berdasarkan nama exact:', originalStop.name);
                    }
                }

                // Strategi 3: Cari berdasarkan nama stop (contains) - ambil yang terdekat
                if (!originalStop) {
                    const stopName = nearest.stop.stop_name.toLowerCase();
                    const candidates = allStopsWithRoutes.filter(s =>
                        s.name.toLowerCase().includes(stopName) ||
                        stopName.includes(s.name.toLowerCase())
                    );

                    if (candidates.length > 0) {
                        // Pilih yang terdekat dengan POI
                        let minDist = Infinity;
                        for (const candidate of candidates) {
                            const dist = haversineDistance(poi.lat, poi.lng, candidate.lat, candidate.lng);
                            if (dist < minDist) {
                                minDist = dist;
                                originalStop = candidate;
                            }
                        }
                        console.log(`✅ Ditemukan berdasarkan nama (${candidates.length} kandidat):`, originalStop.name);
                    }
                }

                // Strategi 4: Cari halte terdekat secara manual (Haversine)
                if (!originalStop) {
                    console.warn('Mencari manual dengan Haversine...');
                    let nearestManual = null;
                    let minDistance = Infinity;

                    allStopsWithRoutes.forEach(stop => {
                        const distance = haversineDistance(poi.lat, poi.lng, stop.lat, stop.lng);
                        if (distance < minDistance) {
                            minDistance = distance;
                            nearestManual = stop;
                        }
                    });

                    if (nearestManual && minDistance <= 5000) {
                        originalStop = nearestManual;
                        console.log('✅ Ditemukan manual (Haversine):', originalStop.name, 'Jarak:', (minDistance / 1000).toFixed(2), 'km');
                    }
                }

                if (!originalStop) {
                    alert(` Tidak dapat menemukan halte terdekat dari "${poi.name}".\n\nSilakan coba lokasi lain atau pilih halte secara manual.`);
                    return;
                }

                // Hitung jarak
                const distanceToStop = haversineDistance(poi.lat, poi.lng, originalStop.lat, originalStop.lng);
                const distanceKm = (distanceToStop / 1000).toFixed(2);

                // Notifikasi jika jarak > 1 km
                if (distanceToStop > 1000) {
                    // Tampilkan notifikasi (bisa pakai alert, toast, atau console.warn)
                    alert(` Perhatian!\n\nJarak dari "${poi.name}" ke halte terdekat "${originalStop.name}" adalah ${distanceKm} km.\n\nAnda perlu berjalan kaki sekitar ${Math.round(distanceToStop / 1000 * 12)} menit.`);

                    // Atau pakai console.warn
                    // console.warn(` Jarak jauh: ${distanceKm} km dari ${poi.name} ke ${originalStop.name}`);
                }

                const selectedItem = {
                    ...originalStop,
                    isPoi: true,
                    originalPoiName: poi.name,
                    poiCategory: poi.category,
                    walkingDistanceKm: distanceKm,
                    displayName: `${poi.name} (${originalStop.name})`
                };

                if (isStart) {
                    selectedStartStop = selectedItem;
                    startInput.value = `${poi.name} → Naik di ${originalStop.name} (${distanceKm} km)`;
                } else {
                    selectedEndStop = selectedItem;
                    endInput.value = `${poi.name} → Turun di ${originalStop.name} (${distanceKm} km)`;
                }

                startResults.style.display = 'none';
                endResults.style.display = 'none';
                updateSelectedStopsInfo();

            } catch (error) {
                console.error('Error selecting POI:', error);
                alert('Gagal memilih lokasi. Silakan coba lagi.');
            } finally {
                setTimeout(() => {
                    selectingPoi = false;
                }, 500);
            }
        }

        // Fungsi updateSelectedStopsInfo dengan routeBtn1 & routeBtn2
        function updateSelectedStopsInfo() {
            const infoDiv = document.getElementById('selectedStopsInfo');
            const routeBtn1 = document.getElementById('routeBtn1');
            const routeBtn2 = document.getElementById('routeBtn2');
            const clearBtn = document.getElementById('clearBtn');

            if (selectedStartStop && selectedEndStop) {
                let startText = selectedStartStop.isPoi ?
                    `${selectedStartStop.name}` :
                    `${selectedStartStop.name} <small style="color:#666;">(#${selectedStartStop.stopNumber} • ${selectedStartStop.routeName})</small>`;

                let endText = selectedEndStop.isPoi ?
                    `${selectedEndStop.name}` :
                    `${selectedEndStop.name} <small style="color:#666;">(#${selectedEndStop.stopNumber} • ${selectedEndStop.routeName})</small>`;

                document.getElementById('startStopInfo').innerHTML = startText;
                document.getElementById('endStopInfo').innerHTML = endText;
                infoDiv.style.display = 'flex';

                //  Aktifkan kedua tombol
                if (routeBtn1) routeBtn1.disabled = false;
                if (routeBtn2) routeBtn2.disabled = false;
                clearBtn.disabled = false;

            } else if (selectedStartStop || selectedEndStop) {
                if (selectedStartStop) {
                    let startText = selectedStartStop.isPoi ?
                        `${selectedStartStop.name}` :
                        `${selectedStartStop.name} <small style="color:#666;">(#${selectedStartStop.stopNumber} • ${selectedStartStop.routeName})</small>`;
                    document.getElementById('startStopInfo').innerHTML = startText;
                    document.getElementById('endStopInfo').innerHTML = 'Pilih tujuan';
                }
                if (selectedEndStop) {
                    let endText = selectedEndStop.isPoi ?
                        `${selectedEndStop.name}` :
                        `${selectedEndStop.name} <small style="color:#666;">(#${selectedEndStop.stopNumber} • ${selectedEndStop.routeName})</small>`;
                    document.getElementById('startStopInfo').innerHTML = 'Pilih awal';
                    document.getElementById('endStopInfo').innerHTML = endText;
                }
                infoDiv.style.display = 'flex';

                //  Nonaktifkan tombol cari
                if (routeBtn1) routeBtn1.disabled = true;
                if (routeBtn2) routeBtn2.disabled = true;
                clearBtn.disabled = false;

            } else {
                infoDiv.style.display = 'none';

                //  Nonaktifkan semua tombol
                if (routeBtn1) routeBtn1.disabled = true;
                if (routeBtn2) routeBtn2.disabled = true;
                if (clearBtn) clearBtn.disabled = true;
            }
        }

        // ==================== Filter Koridor ====================
        function setupKoridorFilter() {
            const searchInput = document.getElementById('koridorSearch');
            if (!searchInput) return;

            function filterKoridor() {
                const term = searchInput.value.toLowerCase().trim();
                const dropdowns = document.querySelectorAll('.koridor-dropdown');
                let visible = 0;
                dropdowns.forEach(d => {
                    const short = d.dataset.shortName?.toLowerCase() || '',
                        long = d.dataset.longName?.toLowerCase() || '';
                    let stops = [];
                    try {
                        stops = d.dataset.stops ? JSON.parse(d.dataset.stops) : [];
                    } catch (e) {}
                    const match = term === '' || short.includes(term) || long.includes(term) || stops.some(s => s.toLowerCase().includes(term));
                    if (match) {
                        d.style.display = '';
                        visible++;
                        if (term !== '') highlightMatch(d, term);
                        else removeHighlight(d);
                    } else {
                        d.style.display = 'none';
                    }
                });
                const container = document.getElementById('koridor-list');
                let noMsg = document.getElementById('noResultsMessage');
                if (term !== '' && visible === 0) {
                    if (!noMsg) {
                        noMsg = document.createElement('div');
                        noMsg.id = 'noResultsMessage';
                        noMsg.className = 'no-results';
                        noMsg.innerHTML = `<i class="fas fa-search mr-2"></i> Tidak ada koridor "${term}"`;
                        container.appendChild(noMsg);
                    }
                } else if (noMsg) noMsg.remove();
            }

            function highlightMatch(d, term) {
                const header = d.querySelector('.koridor-name'),
                    desc = d.querySelector('.koridor-desc'),
                    items = d.querySelectorAll('.halte-item');
                removeHighlight(d);
                if (header) {
                    const txt = header.textContent;
                    header.innerHTML = txt.replace(new RegExp(`(${term})`, 'gi'), '<span class="highlight">$1</span>');
                }
                if (desc) {
                    const txt = desc.textContent;
                    desc.innerHTML = txt.replace(new RegExp(`(${term})`, 'gi'), '<span class="highlight">$1</span>');
                }
                items.forEach(item => {
                    const badge = item.querySelector('.badge')?.textContent || '';
                    const txt = item.textContent.replace(badge, '').trim();
                    item.innerHTML = txt.replace(new RegExp(`(${term})`, 'gi'), '<span class="highlight">$1</span>') + `<span class="badge">${badge}</span>`;
                });
            }

            function removeHighlight(d) {
                const header = d.querySelector('.koridor-name'),
                    desc = d.querySelector('.koridor-desc'),
                    items = d.querySelectorAll('.halte-item');
                if (header) header.innerHTML = header.textContent;
                if (desc) desc.innerHTML = desc.textContent;
                items.forEach(item => {
                    const badge = item.querySelector('.badge')?.textContent || '';
                    const txt = item.textContent.replace(badge, '').trim();
                    item.innerHTML = `${txt} <span class="badge">${badge}</span>`;
                });
            }
            searchInput.addEventListener('input', filterKoridor);
            searchInput.addEventListener('keyup', filterKoridor);
            filterKoridor();
        }

        // ==================== Fungsi Rute ====================
        function findCompleteRoute(start, end, preference = 'distance') {
            console.log('🔍 Mencari rute dari:', start.name, 'ke', end.name);
            if (!allStopsWithRoutes.length) return {
                stops: [],
                koridors: []
            };
            const graph = {},
                TRANSFER_PENALTY = 2500,
                MAX_WALK = 300;
            allStopsWithRoutes.forEach(stop => {
                if (!graph[stop.id]) graph[stop.id] = {
                    id: stop.id,
                    name: stop.name,
                    lat: stop.lat,
                    lng: stop.lng,
                    connections: []
                };
            });
            routes.forEach(route => {
                if (route.stops && route.stops.length > 1) {
                    for (let i = 0; i < route.stops.length - 1; i++) {
                        const a = route.stops[i],
                            b = route.stops[i + 1];

                        // ========== VERSI B (SHAPE_DIST_TRAVELED) - AKTIF ==========
                        // let jarak = a.shape_dist_to_next;

                        // ========== VERSI A (HAVERSINE) - FALLBACK jika shape_dist tidak ada ==========
                        // if (!jarak || jarak <= 0) {
                        //     jarak = haversineDistance(a.lat, a.lng, b.lat, b.lng);
                        // }

                        // ==========  VERSI A (TANPA FALLBACK) ==========
                        const jarak = haversineDistance(a.lat, a.lng, b.lat, b.lng);

                        if (graph[a.id] && graph[b.id]) graph[a.id].connections.push({
                            stopId: b.id,
                            routeId: route.id,
                            routeName: `Koridor ${route.short_name}`,
                            distance: jarak,
                            type: 'bus'
                        });
                    }
                }
            });
            const ids = Object.keys(graph);
            for (let i = 0; i < ids.length; i++) {
                for (let j = i + 1; j < ids.length; j++) {
                    const s1 = graph[ids[i]],
                        s2 = graph[ids[j]],
                        dist = haversineDistance(s1.lat, s1.lng, s2.lat, s2.lng);
                    if (dist <= MAX_WALK) {
                        const mult = dist < 100 ? 5 : 50;
                        const walk = {
                            stopId: s2.id,
                            routeId: 'WALK',
                            routeName: 'Jalan Kaki',
                            distance: dist * mult,
                            type: 'walk'
                        };
                        graph[s1.id].connections.push(walk);
                        graph[s2.id].connections.push({
                            ...walk,
                            stopId: s1.id
                        });
                    }
                }
            }
            const distances = {},
                previous = {},
                prevInfo = {},
                unvisited = new Set();
            Object.keys(graph).forEach(id => {
                distances[id] = Infinity;
                previous[id] = null;
                unvisited.add(id);
            });
            distances[start.id] = 0;
            prevInfo[start.id] = {
                routeId: null,
                type: null
            };
            while (unvisited.size > 0) {
                let current = null,
                    minD = Infinity;
                unvisited.forEach(id => {
                    if (distances[id] < minD) {
                        minD = distances[id];
                        current = id;
                    }
                });
                if (!current || current === String(end.id)) break;
                unvisited.delete(current);
                graph[current].connections.forEach(conn => {
                    if (!unvisited.has(String(conn.stopId))) return;
                    let weight = conn.distance;
                    const last = prevInfo[current];

                    // ========== PREFERENSI BERDASARKAN PARAMETER ==========
                    if (preference === 'transfer') {
                        // ========== CARI 2: PRIORITAS MINIM TRANSFER ==========

                        // 1. Penalti transfer BESAR (6000m) agar malas pindah koridor
                        if (last && last.routeId !== null && last.routeId !== conn.routeId && conn.type === 'bus') {
                            weight += 50000; // Ubah di sini untuk pengecekan keefektifan
                        }

                        // 2. Bobot bus dasar
                        if (conn.type === 'bus') {
                            weight += 600;
                            if (conn.distance > 4000) weight += 60000;
                        }

                        // 3. Jalan kaki (tetap besar, prioritas naik bus)
                        if (conn.type === 'walk') {
                            const walkMult = conn.distance < 100 ? 10 : 100;
                            weight = conn.distance * walkMult;
                        }

                    } else {
                        // ========== CARI 1: PRIORITAS JARAK TERPENDEK ==========

                        if (last && last.routeId !== null && last.routeId !== conn.routeId && conn.type === 'bus') {
                            weight += TRANSFER_PENALTY; // Penalti transfer normal (2500m)
                        }

                        if (conn.type === 'bus') {
                            weight += 800; // Bobot bus normal
                            if (conn.distance > 4000) weight += 100000; // Penalti bus jauh normal
                        }

                        // Jalan kaki dengan multiplier normal
                        if (conn.type === 'walk') {
                            const walkMult = conn.distance < 100 ? 5 : 50;
                            weight = conn.distance * walkMult;
                        }
                    }

                    // ========== Tetap di Koridor yang sama ==========
                    // Jika naik bus yang sama dengan halte sebelumnya, beri diskon 800 meter
                    // Ini membuat algoritma "betah" di koridor yang sama (lebih manusiawi)
                    if (last && last.routeId !== null && last.routeId === conn.routeId && conn.type === 'bus') {
                        weight -= 800;
                    }
                    //

                    const newDist = distances[current] + weight;
                    if (newDist < distances[conn.stopId]) {
                        distances[conn.stopId] = newDist;
                        previous[conn.stopId] = current;
                        prevInfo[conn.stopId] = conn;
                    }
                });
            }
            let path = [],
                curr = String(end.id);
            if (previous[curr] === null && curr !== String(start.id)) return {
                stops: [],
                koridors: []
            };
            while (curr) {
                const data = graph[curr],
                    info = prevInfo[curr];
                path.unshift({
                    id: data.id,
                    name: data.name,
                    lat: data.lat,
                    lng: data.lng,
                    routeName: info?.routeName || 'Titik Awal',
                    isWalk: info?.type === 'walk'
                });
                curr = previous[curr];
            }
            const unique = [],
                seen = new Set();
            path.forEach(p => {
                if (p.routeName !== 'Titik Awal' && p.routeName !== 'Jalan Kaki' && !seen.has(p.routeName)) {
                    const det = routes.find(r => `Koridor ${r.short_name}` === p.routeName);
                    if (det) {
                        unique.push(det);
                        seen.add(p.routeName);
                    }
                }
            });
            return {
                stops: path,
                koridors: unique
            };
        }

        function displayRouteResult(route) {
            Object.values(routeLayers_temp).forEach(l => l.remove?.());
            currentRouteMarkers.forEach(m => m.remove?.());
            routeLayers_temp = {};
            currentRouteMarkers = [];
            const stops = route.stops,
                koridors = route.koridors;
            koridors.forEach((k, i) => {
                if (k?.shape?.length) routeLayers_temp[`route_${k.id}_${i}`] = L.polyline(k.shape, {
                    color: '#e74c3c',
                    weight: 6,
                    opacity: 0.9
                }).addTo(map);
            });
            const points = [];
            stops.forEach((s, i) => {
                if (!s.lat) return;
                points.push([s.lat, s.lng]);
                let size = '10px',
                    color = '#e74c3c',
                    border = '2px';
                if (i === 0) {
                    size = '20px';
                    color = '#27ae60';
                    border = '4px';
                } else if (i === stops.length - 1) {
                    size = '20px';
                    color = '#e74c3c';
                    border = '4px';
                }
                const m = L.marker([s.lat, s.lng], {
                    icon: L.divIcon({
                        html: `<div style="background:${color}; width:${size}; height:${size}; border-radius:50%; border:${border} solid white; box-shadow:0 0 10px ${color};"></div>`,
                        iconSize: [parseInt(size) + 8, parseInt(size) + 8],
                        iconAnchor: [(parseInt(size) + 8) / 2, (parseInt(size) + 8) / 2]
                    })
                }).bindPopup(`<b>${s.name}</b><br>${s.routeName}`);
                m.addTo(map);
                currentRouteMarkers.push(m);
            });
            if (points.length > 1) routeLayers_temp.routeLine = L.polyline(points, {
                color: '#f39c12',
                weight: 5,
                opacity: 0.8
            }).addTo(map);
            let total = 0;
            for (let i = 0; i < points.length - 1; i++) total += haversineDistance(points[i][0], points[i][1], points[i + 1][0], points[i + 1][1]);
            document.getElementById('routeInfoPanel').innerHTML = `<div><small>🚏 ${stops.length} halte</small><br><small>📏 ${(total/1000).toFixed(2)} km</small><br><small>⏱️ ~${Math.round((total/1000)*4)+(koridors.length*5)} menit</small><br><small>🔄 ${koridors.length} koridor</small></div>`;
            document.getElementById('routeKoridorList').innerHTML = koridors.map((k, i) => `<div class="route-info-item" onclick="focusKoridor('${k.id}')"><span class="badge badge-primary" style="background:${k.color}">${i+1}</span><div><strong>Koridor ${k.short_name}</strong><br><small>${k.long_name}</small></div><div class="route-color-box" style="background:${k.color}"></div></div>`).join('') || '<p class="text-muted">Tidak ada koridor</p>';
            document.getElementById('routeStopsList').innerHTML = stops.map((s, i) => `<div class="stops-list-item" onclick="focusStop(${s.lat}, ${s.lng}, '${s.name}')"><span class="stop-number">${i+1}</span><span class="stop-name">${i===0?'⚡ ':i===stops.length-1?'🏁 ':''}${s.name}</span><span class="stop-route">${s.routeName}</span></div>`).join('');
            if (points.length > 1) map.fitBounds(L.latLngBounds(points).pad(0.1));
        }

        function focusStop(lat, lng, name) {
            event.stopPropagation();
            map.setView([lat, lng], 17);
            //  Popup otomatis
            L.popup()
                .setLatLng([lat, lng])
                .setContent(`<b>${name}</b>`)
                .openOn(map);
        }
        window.focusStop = focusStop;

        function focusKoridor(id) {
            const layer = routeLayers[id];
            if (layer) {
                map.fitBounds(layer.getBounds().pad(0.1));

                //  Popup di tengah koridor
                const bounds = layer.getBounds();
                const center = bounds.getCenter();

                // Cari nama koridor dari routeLayers atau routes
                let koridorName = 'Koridor';
                const route = routes.find(r => r.id == id);
                if (route) {
                    koridorName = `Koridor ${route.short_name} - ${route.long_name}`;
                }

                L.popup()
                    .setLatLng(center)
                    .setContent(`<b>🚌 ${koridorName}</b><br>Klik untuk detail`)
                    .openOn(map);

                // Hilangkan popup setelah 3 detik
                setTimeout(() => {
                    map.closePopup();
                }, 3000);
            }
        }
        window.focusKoridor = focusKoridor;

        async function saveSearchLog(start, end, result, time, startWalkingInfo, endWalkingInfo, preference = 'distance') {
            let total = 0;
            for (let i = 0; i < result.stops.length - 1; i++) {
                total += haversineDistance(result.stops[i].lat, result.stops[i].lng, result.stops[i + 1].lat, result.stops[i + 1].lng);
            }
            const estimasi = Math.round((total / 1000) * 4) + (result.koridors.length * 5);

            //  Kirim data lengkap ke log, termasuk JSON
            await fetch('/api/pencarian-log', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id_halte_awal: start.id,
                    id_halte_tujuan: end.id,
                    waktu_eksekusi_ms: time,
                    node_dikunjungi: result.stops.length,
                    total_jarak: total,
                    total_waktu: estimasi * 60,
                    total_pindah: result.koridors.length - 1,
                    algoritma: 'Dijkstra',
                    preference: preference,
                    //  3 JSON
                    route_path_json: JSON.stringify(result.stops.map((s, i) => ({
                        order: i + 1,
                        name: s.name,
                        id: s.id
                    }))),
                    koridors_json: JSON.stringify(result.koridors.map(k => ({
                        short_name: k.short_name,
                        color: k.color,
                        long_name: k.long_name
                    }))),
                    walking_info_json: JSON.stringify({
                        start: startWalkingInfo ? {
                            from_poi: startWalkingInfo.fromPoi,
                            to_stop: startWalkingInfo.toStop,
                            distance_km: startWalkingInfo.distanceKm
                        } : null,
                        end: endWalkingInfo ? {
                            from_stop: endWalkingInfo.fromStop,
                            to_poi: endWalkingInfo.toPoi,
                            distance_km: endWalkingInfo.distanceKm
                        } : null
                    })
                })
            }).catch(e => console.error('Error save log:', e));
        }

        async function sendRouteToAlgoritmaPage(start, end, result, time, startWalkingInfo, endWalkingInfo) {

            console.log('=== SEND TO ALGORITMA ===');
            console.log('Stops length:', result.stops.length);
            console.log('Route_path (first 3 stops):', result.stops.slice(0, 3).map(s => s.name));
            console.log('Route_path (last stop):', result.stops[result.stops.length - 1].name);

            let total = 0;
            for (let i = 0; i < result.stops.length - 1; i++) {
                total += haversineDistance(result.stops[i].lat, result.stops[i].lng, result.stops[i + 1].lat, result.stops[i + 1].lng);
            }

            //  Informasi Jalan Kaki
            await fetch('/admin/algoritma/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    start_stop: start.name,
                    end_stop: end.name,
                    total_distance: total,
                    total_stops: result.stops.length,
                    total_transfers: result.koridors.length - 1,
                    execution_time: time,
                    route_path: result.stops.map((s, i) => ({
                        id: s.id,
                        name: s.name,
                        order: i + 1
                    })),
                    koridors: result.koridors.map(k => ({
                        id: k.id,
                        short_name: k.short_name,
                        color: k.color
                    })),
                    timestamp: new Date().toISOString(),
                    walking_info: {
                        start: startWalkingInfo ? {
                            from_poi: startWalkingInfo.fromPoi,
                            to_stop: startWalkingInfo.toStop,
                            distance_km: startWalkingInfo.distanceKm
                        } : null,
                        end: endWalkingInfo ? {
                            from_stop: endWalkingInfo.fromStop,
                            to_poi: endWalkingInfo.toPoi,
                            distance_km: endWalkingInfo.distanceKm
                        } : null
                    }
                })
            }).catch(e => console.error);
        }

        // Fungsi Cari 1 (prioritas jarak terpendek)
        window.calculateRoute1 = function() {
            if (!selectedStartStop || !selectedEndStop) {
                alert('Pilih halte awal dan tujuan');
                return;
            }

            document.getElementById('routeBtn1').disabled = true;
            document.getElementById('routeBtn1').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mencari...';

            // ========== Tangani POI (jalan kaki) ==========
            let actualStartStop = selectedStartStop;
            let actualEndStop = selectedEndStop;
            let startWalkingInfo = null;
            let endWalkingInfo = null;

            // Jika start adalah POI
            if (selectedStartStop.isPoi) {
                const originalStop = allStopsWithRoutes.find(s => s.id === selectedStartStop.id);
                if (originalStop) {
                    actualStartStop = originalStop;
                }
                startWalkingInfo = {
                    fromPoi: selectedStartStop.originalPoiName || selectedStartStop.name,
                    toStop: actualStartStop.name,
                    distanceKm: selectedStartStop.walkingDistanceKm || 0
                };
            }

            // Jika end adalah POI
            if (selectedEndStop.isPoi) {
                const originalStop = allStopsWithRoutes.find(s => s.id === selectedEndStop.id);
                if (originalStop) {
                    actualEndStop = originalStop;
                }
                endWalkingInfo = {
                    fromStop: actualEndStop.name,
                    toPoi: selectedEndStop.originalPoiName || selectedEndStop.name,
                    distanceKm: selectedEndStop.walkingDistanceKm || 0
                };
            }

            // Validasi dengan memastikan kedua stop valid
            if (!actualStartStop || !actualStartStop.id || !allStopsWithRoutes.find(s => s.id === actualStartStop.id)) {
                alert('Lokasi awal tidak valid atau tidak memiliki halte terdekat');
                document.getElementById('routeBtn1').disabled = false;
                document.getElementById('routeBtn1').innerHTML = '<i class="fas fa-route"></i> Cari 1';
                return;
            }
            if (!actualEndStop || !actualEndStop.id || !allStopsWithRoutes.find(s => s.id === actualEndStop.id)) {
                alert('Lokasi tujuan tidak valid atau tidak memiliki halte terdekat');
                document.getElementById('routeBtn1').disabled = false;
                document.getElementById('routeBtn1').innerHTML = '<i class="fas fa-route"></i> Cari 1';
                return;
            }

            const start = performance.now();
            setTimeout(() => {
                const route = findCompleteRoute(actualStartStop, actualEndStop, 'distance');

                console.log('=== HASIL ROUTE ===');
                console.log('Total stops:', route.stops.length);
                console.log('Sample stops:', route.stops.slice(0, 5));
                console.log('Last stop:', route.stops[route.stops.length - 1]);

                const end = performance.now();

                if (route.stops.length === 0) {
                    alert('Rute tidak ditemukan');
                    clearRoute();
                } else {
                    displayRouteResultWithWalking(route, startWalkingInfo, endWalkingInfo);
                    saveSearchLog(actualStartStop, actualEndStop, route, end - start, startWalkingInfo, endWalkingInfo, 'distance');

                    //  Parameter Preference
                    sendRouteToAlgoritmaPage(actualStartStop, actualEndStop, route, end - start, startWalkingInfo, endWalkingInfo, 'distance');
                }

                document.getElementById('routeBtn1').disabled = false;
                document.getElementById('routeBtn1').innerHTML = '<i class="fas fa-route"></i> Cari 1';
            }, 100);
        };

        // Fungsi Cari 2 (prioritas sedikit pindah koridor)
        window.calculateRoute2 = function() {
            if (!selectedStartStop || !selectedEndStop) {
                alert('Pilih halte awal dan tujuan');
                return;
            }

            document.getElementById('routeBtn2').disabled = true;
            document.getElementById('routeBtn2').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mencari...';

            // ========== Tangani POI (Jalan Kaki) ==========
            let actualStartStop = selectedStartStop;
            let actualEndStop = selectedEndStop;
            let startWalkingInfo = null;
            let endWalkingInfo = null;

            // Jika start adalah POI
            if (selectedStartStop.isPoi) {
                const originalStop = allStopsWithRoutes.find(s => s.id === selectedStartStop.id);
                if (originalStop) {
                    actualStartStop = originalStop;
                }
                startWalkingInfo = {
                    fromPoi: selectedStartStop.originalPoiName || selectedStartStop.name,
                    toStop: actualStartStop.name,
                    distanceKm: selectedStartStop.walkingDistanceKm || 0
                };
            }

            // Jika end adalah POI
            if (selectedEndStop.isPoi) {
                const originalStop = allStopsWithRoutes.find(s => s.id === selectedEndStop.id);
                if (originalStop) {
                    actualEndStop = originalStop;
                }
                endWalkingInfo = {
                    fromStop: actualEndStop.name,
                    toPoi: selectedEndStop.originalPoiName || selectedEndStop.name,
                    distanceKm: selectedEndStop.walkingDistanceKm || 0
                };
            }

            // Validasi dengan memastikan kedua stop valid
            if (!actualStartStop || !actualStartStop.id || !allStopsWithRoutes.find(s => s.id === actualStartStop.id)) {
                alert('Lokasi awal tidak valid atau tidak memiliki halte terdekat');
                document.getElementById('routeBtn2').disabled = false;
                document.getElementById('routeBtn2').innerHTML = '<i class="fas fa-route"></i> Cari 2';
                return;
            }
            if (!actualEndStop || !actualEndStop.id || !allStopsWithRoutes.find(s => s.id === actualEndStop.id)) {
                alert('Lokasi tujuan tidak valid atau tidak memiliki halte terdekat');
                document.getElementById('routeBtn2').disabled = false;
                document.getElementById('routeBtn2').innerHTML = '<i class="fas fa-route"></i> Cari 2';
                return;
            }

            const start = performance.now();
            setTimeout(() => {
                // Panggil fungsi dengan parameter preferensi transfer
                const route = findCompleteRoute(actualStartStop, actualEndStop, 'transfer');
                const end = performance.now();

                if (route.stops.length === 0) {
                    alert('Rute tidak ditemukan');
                    clearRoute();
                } else {
                    displayRouteResultWithWalking(route, startWalkingInfo, endWalkingInfo);
                    saveSearchLog(actualStartStop, actualEndStop, route, end - start, startWalkingInfo, endWalkingInfo, 'transfer');

                    //  PERBAIKAN: Tambah parameter preference
                    sendRouteToAlgoritmaPage(actualStartStop, actualEndStop, route, end - start, startWalkingInfo, endWalkingInfo, 'transfer');
                }

                document.getElementById('routeBtn2').disabled = false;
                document.getElementById('routeBtn2').innerHTML = '<i class="fas fa-route"></i> Cari 2';
            }, 100);
        };

        // Fungsi baru untuk menampilkan rute dengan informasi jalan kaki POI
        function displayRouteResultWithWalking(route, startWalkingInfo, endWalkingInfo) {
            if (window.poiMarkers) {
                window.poiMarkers.forEach(m => m.remove?.());
            }
            window.poiMarkers = [];

            // Tambah marker POI awal (jika start adalah POI)
            if (startWalkingInfo && startWalkingInfo.fromPoi && selectedStartStop && selectedStartStop.isPoi) {
                const startPoiMarker = L.marker([selectedStartStop.lat, selectedStartStop.lng], {
                    icon: L.divIcon({
                        html: `<div style="background: #e67e22; width: 40px; height: 40px; border-radius: 50%; border: 4px solid white; box-shadow: 0 0 15px #e67e22; display: flex; align-items: center; justify-content: center; font-size: 20px;">📍</div>`,
                        iconSize: [48, 48],
                        iconAnchor: [24, 24]
                    })
                }).bindPopup(`<b>📍 ${startWalkingInfo.fromPoi}</b><br>🚶 Jalan kaki ke ${startWalkingInfo.toStop}<br>📏 ${startWalkingInfo.distanceKm} km`).addTo(map);
                window.poiMarkers.push(startPoiMarker);
            }

            // Tambah marker POI tujuan (jika end adalah POI)
            if (endWalkingInfo && endWalkingInfo.toPoi && selectedEndStop && selectedEndStop.isPoi) {
                const endPoiMarker = L.marker([selectedEndStop.lat, selectedEndStop.lng], {
                    icon: L.divIcon({
                        html: `<div style="background: #e74c3c; width: 40px; height: 40px; border-radius: 50%; border: 4px solid white; box-shadow: 0 0 15px #e74c3c; display: flex; align-items: center; justify-content: center; font-size: 20px;">🏁</div>`,
                        iconSize: [48, 48],
                        iconAnchor: [24, 24]
                    })
                }).bindPopup(`<b>🏁 ${endWalkingInfo.toPoi}</b><br>🚶 Turun di ${endWalkingInfo.fromStop}<br>📏 ${endWalkingInfo.distanceKm} km`).addTo(map);
                window.poiMarkers.push(endPoiMarker);
            }

            // Hapus layer lama
            Object.values(routeLayers_temp).forEach(l => l.remove?.());
            currentRouteMarkers.forEach(m => m.remove?.());
            routeLayers_temp = {};
            currentRouteMarkers = [];

            const stops = route.stops;
            const koridors = route.koridors;

            // Gambar shape koridor
            koridors.forEach((k, i) => {
                if (k?.shape?.length) {
                    routeLayers_temp[`route_${k.id}_${i}`] = L.polyline(k.shape, {
                        color: '#e74c3c',
                        weight: 6,
                        opacity: 0.9
                    }).addTo(map);
                }
            });

            // Gambar titik antar halte
            const points = [];
            stops.forEach((s, i) => {
                if (!s.lat) return;
                points.push([s.lat, s.lng]);
                let size = '10px',
                    color = '#e74c3c',
                    border = '2px';
                if (i === 0) {
                    size = '20px';
                    color = '#27ae60';
                    border = '4px';
                } else if (i === stops.length - 1) {
                    size = '20px';
                    color = '#e74c3c';
                    border = '4px';
                }
                const m = L.marker([s.lat, s.lng], {
                    icon: L.divIcon({
                        html: `<div style="background:${color}; width:${size}; height:${size}; border-radius:50%; border:${border} solid white; box-shadow:0 0 10px ${color};"></div>`,
                        iconSize: [parseInt(size) + 8, parseInt(size) + 8],
                        iconAnchor: [(parseInt(size) + 8) / 2, (parseInt(size) + 8) / 2]
                    })
                }).bindPopup(`<b>${s.name}</b><br>${s.routeName}`);
                m.addTo(map);
                currentRouteMarkers.push(m);
            });

            if (points.length > 1) {
                routeLayers_temp.routeLine = L.polyline(points, {
                    color: '#f39c12',
                    weight: 5,
                    opacity: 0.8,
                    dashArray: '5, 10' // putus-putus
                }).addTo(map);
            }

            // Hitung total jarak
            let total = 0;
            for (let i = 0; i < points.length - 1; i++) {
                total += haversineDistance(points[i][0], points[i][1], points[i + 1][0], points[i + 1][1]);
            }

            // Build HTML dengan informasi jalan kaki POI
            let routeInfoHTML = '';

            // Jalan kaki awal (POI)
            if (startWalkingInfo) {
                routeInfoHTML += `
            <div class="route-info-item" style="background: #fff3e0;">
                <div class="route-color-box" style="background: #e67e22;"></div>
                <span><b>🚶 Jalan Kaki:</b> ${escapeHtml(startWalkingInfo.fromPoi)} → ${escapeHtml(startWalkingInfo.toStop)} (${startWalkingInfo.distanceKm} km)</span>
            </div>
        `;
            }

            // Koridor yang dilewati
            koridors.forEach((k, i) => {
                routeInfoHTML += `
            <div class="route-info-item" onclick="focusKoridor('${k.id}')">
                <div class="route-color-box" style="background: ${k.color};"></div>
                <span><b>Koridor ${k.short_name}</b> - ${escapeHtml(k.long_name)}</span>
            </div>
        `;
            });

            // Jalan kaki tujuan (POI)
            if (endWalkingInfo) {
                routeInfoHTML += `
            <div class="route-info-item" style="background: #fff3e0;">
                <div class="route-color-box" style="background: #e67e22;"></div>
                <span><b>🚶 Jalan Kaki:</b> ${escapeHtml(endWalkingInfo.fromStop)} → ${escapeHtml(endWalkingInfo.toPoi)} (${endWalkingInfo.distanceKm} km)</span>
            </div>
        `;
            }

            // Ringkasan perjalanan
            routeInfoHTML += `
        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #ccc;">
            <small>${stops.length} halte dilewati</small><br>
            <small>Jarak tempuh: ${(total/1000).toFixed(2)} km</small><br>
            <small>Estimasi: ~${Math.round((total/1000)*4)+(koridors.length*5)} Menit</small><br>
            <small>${koridors.length} koridor</small>
        </div>
    `;

            document.getElementById('routeInfoPanel').innerHTML = routeInfoHTML;
            document.getElementById('routeKoridorList').innerHTML = koridors.map((k, i) => `<div class="route-info-item" onclick="focusKoridor('${k.id}')"><span class="badge badge-primary" style="background:${k.color}">${i+1}</span><div><strong>Koridor ${k.short_name}</strong><br><small>${k.long_name}</small></div><div class="route-color-box" style="background:${k.color}"></div></div>`).join('') || '<p class="text-muted">Tidak ada koridor</p>';
            document.getElementById('routeStopsList').innerHTML = stops.map((s, i) => `<div class="stops-list-item" onclick="focusStop(${s.lat}, ${s.lng}, '${s.name}')"><span class="stop-number">${i+1}</span><span class="stop-name">${i===0?'⚡ ':i===stops.length-1?'🏁 ':''}${escapeHtml(s.name)}</span><span class="stop-route">${escapeHtml(s.routeName)}</span></div>`).join('');

            if (points.length > 1) {
                map.fitBounds(L.latLngBounds(points).pad(0.1));
            }
        }

        window.clearRoute = function() {
            selectedStartStop = null;
            selectedEndStop = null;
            document.getElementById('startSearch').value = '';
            document.getElementById('endSearch').value = '';
            document.getElementById('selectedStopsInfo').style.display = 'none';
            document.getElementById('routeInfoPanel').innerHTML = '<p class="text-muted text-center">Belum ada rute dipilih</p>';
            document.getElementById('routeKoridorList').innerHTML = '<p class="text-muted text-center">Cari rute untuk melihat</p>';
            document.getElementById('routeStopsList').innerHTML = '<p class="text-muted text-center">Cari rute untuk melihat</p>';
            Object.values(routeLayers_temp).forEach(l => l.remove?.());
            currentRouteMarkers.forEach(m => m.remove?.());
            routeLayers_temp = {};
            currentRouteMarkers = [];

            // Hhapus marker POI)
            if (window.poiMarkers) {
                window.poiMarkers.forEach(m => m.remove?.());
                window.poiMarkers = [];
            }

            document.getElementById('routeBtn1').disabled = true;
            document.getElementById('routeBtn2').disabled = true;
            document.getElementById('clearBtn').disabled = true;
        };

        // ==================== Init ====================
        document.addEventListener('DOMContentLoaded', function() {
            drawAllRoutes();
            setupSearch();
            setupKoridorFilter();
        });
    </script>
@endpush

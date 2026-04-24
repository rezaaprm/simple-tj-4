@extends('layouts.backend')

@section('title', 'Peta Rute TransJakarta')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="row mt-2">
            <!-- CARD KIRI: Cari Rute & Detail Rute -->
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
                            @foreach($routes as $route)
                            <div class="koridor-dropdown" data-route-id="{{ $route['id'] }}"
                                data-short-name="Koridor {{ $route['short_name'] }}"
                                data-long-name="{{ $route['long_name'] }}"
                                data-stops="{{ json_encode(array_column($route['stops'], 'name')) }}">

                                <div class="koridor-header" style="border-left-color: {{ $route['color'] }};" onclick="toggleDropdown(this)">
                                    <div class="koridor-info">
                                        <span class="koridor-name">Koridor {{ $route['short_name'] }}</span>
                                        <span class="koridor-desc">{{ $route['long_name'] }}</span>
                                    </div>
                                    <span class="arrow">▼</span>
                                </div>

                                <div class="koridor-body">
                                    <ul class="halte-list">
                                        @foreach($route['stops'] as $stopIdx => $stop)
                                        <li class="halte-item" data-stop-name="{{ $stop['name'] }}" onclick="event.stopPropagation(); focusStop({{ $stop['lat'] }}, {{ $stop['lng'] }}, '{{ $stop['name'] }}')">
                                            {{ $stop['name'] }}
                                            <span class="badge">{{ $stopIdx + 1 }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
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
    // ==================== DATA DARI CONTROLLER ====================
    const routes = @json($routes);
    console.log('Routes loaded:', routes.length);

    // ==================== INISIALISASI PETA ====================
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

    // ==================== VARIABEL GLOBAL ====================
    const routeLayers = {};
    let activeRoutes = new Set();
    let stopMarkers = {};
    let stopsVisible = false;
    let selectedStartStop = null;
    let selectedEndStop = null;
    let routeLayers_temp = {};
    let currentRouteMarkers = [];

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

    // ==================== FUNGSI GAMBAR SEMUA RUTE ====================
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

    // ==================== FUNGSI HALTE ====================
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

    // ==================== TOGGLE RUTE ====================
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

    // ==================== TOGGLE SEMUA KORIDOR ====================
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

    // ==================== DROPDOWN KORIDOR ====================
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

    // ==================== SEARCH ====================
    function setupSearch() {
        const startInput = document.getElementById('startSearch'),
            endInput = document.getElementById('endSearch');
        const startResults = document.getElementById('startResults'),
            endResults = document.getElementById('endResults');

        function filterStops(query) {
            if (!query || query.length < 2) return [];
            const q = query.toLowerCase();
            return allStopsWithRoutes.filter(stop => stop.searchText.includes(q)).slice(0, 10);
        }

        function renderResults(results, target, isStart) {
            if (results.length === 0) {
                target.innerHTML = '<div class="list-group-item">Tidak ditemukan</div>';
                target.style.display = 'block';
                return;
            }
            target.innerHTML = results.map(stop => `<div class="list-group-item list-group-item-action autocomplete-item" onclick='selectStop(${JSON.stringify(stop).replace(/'/g,"\\'")}, ${isStart})'><div><strong>${stop.name}</strong><br><small>${stop.routeName}</small></div></div>`).join('');
            target.style.display = 'block';
        }
        startInput.addEventListener('input', e => renderResults(filterStops(e.target.value), startResults, true));
        endInput.addEventListener('input', e => renderResults(filterStops(e.target.value), endResults, false));
        document.addEventListener('click', e => {
            if (!startInput.contains(e.target) && !startResults.contains(e.target)) startResults.style.display = 'none';
            if (!endInput.contains(e.target) && !endResults.contains(e.target)) endResults.style.display = 'none';
        });
    }
    window.selectStop = function(stop, isStart) {
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
    };

    function updateSelectedStopsInfo() {
        const infoDiv = document.getElementById('selectedStopsInfo'),
            routeBtn1 = document.getElementById('routeBtn1'),
            routeBtn2 = document.getElementById('routeBtn2'),
            clearBtn = document.getElementById('clearBtn');

        if (selectedStartStop && selectedEndStop) {
            document.getElementById('startStopInfo').innerHTML = `${selectedStartStop.name} <small>(${selectedStartStop.routeName})</small>`;
            document.getElementById('endStopInfo').innerHTML = `${selectedEndStop.name} <small>(${selectedEndStop.routeName})</small>`;
            infoDiv.style.display = 'block';
            routeBtn1.disabled = false;
            routeBtn2.disabled = false;
            clearBtn.disabled = false;
        } else if (selectedStartStop || selectedEndStop) {
            document.getElementById('startStopInfo').innerHTML = selectedStartStop?.name || 'Pilih awal';
            document.getElementById('endStopInfo').innerHTML = selectedEndStop?.name || 'Pilih tujuan';
            infoDiv.style.display = 'block';
            routeBtn1.disabled = true;
            routeBtn2.disabled = true;
            clearBtn.disabled = false;
        } else {
            infoDiv.style.display = 'none';
            routeBtn1.disabled = true;
            routeBtn2.disabled = true;
            clearBtn.disabled = true;
        }
    }

    // ==================== FILTER KORIDOR ====================
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

    // ==================== FUNGSI RUTE ====================
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
                    // PREFERENSI 2: PRIORITAS MINIM TRANSFER (Cari 2)
                    // Penalti transfer lebih kecil, bobot bus lebih kecil

                    if (last && last.routeId !== null && last.routeId !== conn.routeId && conn.type === 'bus') {
                        weight += TRANSFER_PENALTY / 2; // Penalti transfer dikurangi 50%
                    }

                    if (conn.type === 'bus') {
                        weight += 400; // Bobot bus dikurangi (lebih memilih bus)
                        if (conn.distance > 4000) weight += 50000; // Penalti bus jauh dikurangi
                    }

                    if (conn.type === 'walk') {
                        // Jalan kaki diberi penalti lebih besar (prioritas naik bus)
                        const walkMult = conn.distance < 100 ? 10 : 100;
                        weight = conn.distance * walkMult;
                    }

                } else {
                    // PREFERENSI 1: PRIORITAS JARAK TERPENDEK (Cari 1) - DEFAULT
                    // Penalti transfer normal, bobot bus normal

                    if (last && last.routeId !== null && last.routeId !== conn.routeId && conn.type === 'bus') {
                        weight += TRANSFER_PENALTY; // Penalti transfer normal
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
                // ========== END PREFERENSI ==========
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
        map.setView([lat, lng], 16);
        L.popup().setLatLng([lat, lng]).setContent(`<b>${name}</b>`).openOn(map);
    }
    window.focusStop = focusStop;

    function focusKoridor(id) {
        if (routeLayers[id]) map.fitBounds(routeLayers[id].getBounds().pad(0.1));
    }
    window.focusKoridor = focusKoridor;

    async function saveSearchLog(start, end, result, time) {
        let total = 0;
        for (let i = 0; i < result.stops.length - 1; i++) total += haversineDistance(result.stops[i].lat, result.stops[i].lng, result.stops[i + 1].lat, result.stops[i + 1].lng);
        const estimasi = Math.round((total / 1000) * 4) + (result.koridors.length * 5);
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
                algoritma: 'Dijkstra'
            })
        }).catch(e => console.error);
    }
    async function sendRouteToAlgoritmaPage(start, end, result, time) {
        let total = 0;
        for (let i = 0; i < result.stops.length - 1; i++) total += haversineDistance(result.stops[i].lat, result.stops[i].lng, result.stops[i + 1].lat, result.stops[i + 1].lng);
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
                timestamp: new Date().toISOString()
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

        const start = performance.now();
        setTimeout(() => {
            // Panggil fungsi dengan parameter preferensi jarak
            const route = findCompleteRoute(selectedStartStop, selectedEndStop, 'distance');
            const end = performance.now();

            if (route.stops.length === 0) {
                alert('❌ Rute tidak ditemukan');
                clearRoute();
            } else {
                displayRouteResult(route);
                saveSearchLog(selectedStartStop, selectedEndStop, route, end - start);
                sendRouteToAlgoritmaPage(selectedStartStop, selectedEndStop, route, end - start);
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

        const start = performance.now();
        setTimeout(() => {
            // Panggil fungsi dengan parameter preferensi transfer
            const route = findCompleteRoute(selectedStartStop, selectedEndStop, 'transfer');
            const end = performance.now();

            if (route.stops.length === 0) {
                alert('❌ Rute tidak ditemukan');
                clearRoute();
            } else {
                displayRouteResult(route);
                saveSearchLog(selectedStartStop, selectedEndStop, route, end - start);
                sendRouteToAlgoritmaPage(selectedStartStop, selectedEndStop, route, end - start);
            }

            document.getElementById('routeBtn2').disabled = false;
            document.getElementById('routeBtn2').innerHTML = '<i class="fas fa-route"></i> Cari 2';
        }, 100);
    };

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
        document.getElementById('routeBtn1').disabled = true;
        document.getElementById('routeBtn2').disabled = true;
        document.getElementById('clearBtn').disabled = true;
    };

    // ==================== INIT ====================
    document.addEventListener('DOMContentLoaded', function() {
        drawAllRoutes();
        setupSearch();
        setupKoridorFilter();
    });
</script>
@endpush
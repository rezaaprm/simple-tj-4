@extends('layouts.backend')

@section('content')
    <div class="row mt-4">
        <div class="col-sm-12 mt-4">
            <div class="callout callout-warning d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <span class="text-dark">
                        <i class="fa-solid fa-road mr-2"></i>
                        Data Koridor TransJakarta
                        ({{ $koridors->count() }} rute layanan
                        @php
                            $routesCollection = collect($routes);
                            $koridorUtama = $routesCollection->filter(function ($route) {
                                $shortName = $route['short_name'];
                                return is_numeric($shortName) && $shortName >= 1 && $shortName <= 14;
                            });
                        @endphp
                        <strong class="text-primary">| {{ $koridorUtama->count() }} koridor utama</strong>)
                    </span>
                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                        <i class="fas fa-info-circle"></i>
                        Koridor utama (1-14) + rute integrasi (JAK, S, Royaltrans, dll)
                    </small>
                </div>
                <div class="mt-2 mt-sm-0">
                    <button type="button" class="btn btn-sm btn-info mr-1" id="btnShowAll">
                        <i class="fas fa-eye"></i> Tampilkan Semua Koridor
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="btnHideAll">
                        <i class="fas fa-eye-slash"></i> Sembunyikan Semua
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <span>Daftar Koridor</span>
                    <input type="text" class="form-control form-control-sm mt-2" id="koridorSearch" placeholder="Filter koridor atau halte">
                </div>
                <div class="card-body p-2" style="max-height: 500px; overflow-y: auto;">
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
                                                <li class="halte-item" data-stop-name="{{ $stop['name'] }}" onclick="event.stopPropagation(); focusHalte({{ $stop['lat'] }}, {{ $stop['lng'] }}, '{{ $stop['name'] }}')">
                                                    {{ $stop['name'] }}
                                                    <span class="badge">{{ $stopIdx + 1 }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="alert alert-warning text-center p-3 m-2" style="background: #fff3cd; border-radius: 6px; border-left: 4px solid #f39c12;">
                                            <i class="fas fa-info-circle text-warning"></i>
                                            <strong>Koridor ini TIDAK BEROPERASI pada hari ini.</strong><br>
                                            <small class="text-muted">Koridor dengan jadwal khusus hanya beroperasi pada hari tertentu. Silakan cek kembali di akhir pekan.</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <span id="selectedTitle">📍 Koridor Terpilih</span>
                </div>
                <div class="card-body p-0">
                    <div id="koridor-map" style="height: 450px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

        <script>
            // ==================== Data dari Controller ====================
            const routes = @json($routes);
            console.log('Routes loaded:', routes.length);

            // ==================== Inisialisasi Peta dengan Batas Jakarta ====================
            // Batas wilayah Jakarta & sekitarnya (Jabodetabek)
            const jakartaBounds = L.latLngBounds(
                L.latLng(-6.5, 106.5), // Barat Daya
                L.latLng(-5.9, 107.2) // Timur Laut
            );

            const map = L.map('koridor-map', {
                center: [-6.2088, 106.8456],
                zoom: 11,
                minZoom: 10,
                maxZoom: 18,
                maxBounds: jakartaBounds,
                maxBoundsViscosity: 1.0,
                attributionControl: false
            });

            // Overlay batas wilayah
            L.rectangle(jakartaBounds, {
                color: "#ff4444",
                weight: 2,
                opacity: 0.3,
                fillOpacity: 0,
                dashArray: "5, 5"
            }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: ''
            }).addTo(map);

            // ==================== Variabel Global ====================
            const routeLayers = {};
            let activeRoutes = new Set();

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
                            sticky: true,
                            direction: "top"
                        });

                        routeLayers[route.id] = polyline;
                        activeRoutes.add(route.id);
                    }
                });
                updateActiveCount();
            }

            // ==================== Fungsi Toggle Rute (single) ====================
            function toggleRoute(routeId, shouldFitBounds = false) {
                if (activeRoutes.has(routeId)) {
                    if (routeLayers[routeId]) {
                        map.removeLayer(routeLayers[routeId]);
                    }
                    activeRoutes.delete(routeId);
                } else {
                    const route = routes.find(r => r.id == routeId);
                    if (route && route.shape && route.shape.length > 0) {
                        const polyline = L.polyline(route.shape, {
                            color: route.color,
                            weight: 3,
                            opacity: 0.7
                        }).addTo(map);

                        polyline.bindTooltip(`<b>Koridor ${route.short_name}</b><br>${route.long_name}`, {
                            sticky: true,
                            direction: "top"
                        });

                        routeLayers[routeId] = polyline;
                        activeRoutes.add(routeId);

                        // Fokus ke koridor yang dipilih (kecuali dari toggle all)
                        if (shouldFitBounds) {
                            const bounds = L.latLngBounds(route.shape);
                            map.fitBounds(bounds.pad(0.1));

                            // Efek Highlight
                            polyline.setStyle({
                                weight: 5,
                                opacity: 1
                            });
                            setTimeout(() => {
                                if (routeLayers[routeId]) {
                                    routeLayers[routeId].setStyle({
                                        weight: 3,
                                        opacity: 0.7
                                    });
                                }
                            }, 2000);
                        }
                    }
                }
                updateActiveCount();
            }

            // ==================== Fungsi Dropdown Koridor (DI SINI FOKUSNYA) ====================
            function toggleDropdown(header) {
                const dropdown = header.closest('.koridor-dropdown');
                if (!dropdown) return;
                const routeId = dropdown.dataset.routeId;
                const body = header.nextElementSibling;

                // Toggle peta (dengan fokus = true)
                const wasActive = activeRoutes.has(routeId);

                if (!wasActive) {
                    // Jika belum aktif, aktifkan dan fokus
                    toggleRoute(routeId, true);
                } else {
                    // Jika sudah aktif, tetap toggle (matikan saja, tidak perlu fokus)
                    toggleRoute(routeId, false);
                }

                // Toggle dropdown
                header.classList.toggle('open');
                if (body) body.classList.toggle('open');

                // Update title
                const route = routes.find(r => r.id == routeId);
                if (route) {
                    document.getElementById('selectedTitle').innerHTML = `📍 Koridor ${route.short_name} - ${route.long_name}`;
                }
            }

            // ==================== Tampilkan Semua Koridor (TANPA zoom) ====================
            function showAllRoutes() {
                console.log('Menampilkan semua koridor');

                routes.forEach(route => {
                    if (!route.shape || route.shape.length === 0) return;

                    if (!routeLayers[route.id]) {
                        const polyline = L.polyline(route.shape, {
                            color: route.color,
                            weight: 3,
                            opacity: 0.7
                        }).addTo(map);

                        polyline.bindTooltip(`<b>Koridor ${route.short_name}</b><br>${route.long_name}`, {
                            sticky: true,
                            direction: "top"
                        });

                        routeLayers[route.id] = polyline;
                    } else if (!map.hasLayer(routeLayers[route.id])) {
                        routeLayers[route.id].addTo(map);
                    }
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
                });

                updateActiveCount();
                document.getElementById('selectedTitle').innerHTML = `📍 Semua Koridor (${activeRoutes.size} aktif)`;
                console.log(`Semua koridor ditampilkan, total: ${activeRoutes.size}`);
            }

            // ==================== Sembunyikan Semua Koridor ====================
            function hideAllRoutes() {
                console.log('Menyembunyikan semua koridor');

                Object.keys(routeLayers).forEach(id => {
                    if (routeLayers[id] && map.hasLayer(routeLayers[id])) {
                        map.removeLayer(routeLayers[id]);
                    }
                });

                activeRoutes.clear();

                document.querySelectorAll('.koridor-dropdown').forEach(d => {
                    d.classList.remove('active');
                    const header = d.querySelector('.koridor-header');
                    const body = d.querySelector('.koridor-body');
                    if (header) {
                        header.classList.remove('open');
                        const arrow = header.querySelector('.arrow');
                        if (arrow) arrow.style.transform = '';
                    }
                    if (body) body.classList.remove('open');
                });

                document.getElementById('selectedTitle').innerHTML = '📍 Koridor Terpilih';
                updateActiveCount();
                console.log('Semua koridor disembunyikan');
            }

            // ==================== Fokus Halte ====================
            function focusHalte(lat, lng, nama) {
                map.setView([lat, lng], 17);
                L.popup().setLatLng([lat, lng]).setContent(`<b>${nama}</b>`).openOn(map);
            }

            function updateActiveCount() {
                let countSpan = document.getElementById('activeKoridorCount');
                if (!countSpan) {
                    const callout = document.querySelector('.callout');
                    if (callout) {
                        countSpan = document.createElement('span');
                        countSpan.id = 'activeKoridorCount';
                        countSpan.className = 'ml-3 badge badge-info';
                        countSpan.textContent = 'Aktif: 0';
                        callout.appendChild(countSpan);
                    }
                }
                if (countSpan) countSpan.textContent = `Aktif: ${activeRoutes.size}`;
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
                        const short = d.dataset.shortName?.toLowerCase() || '';
                        const long = d.dataset.longName?.toLowerCase() || '';
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

            // ==================== Event Listener ====================
            document.getElementById('btnShowAll').addEventListener('click', showAllRoutes);
            document.getElementById('btnHideAll').addEventListener('click', hideAllRoutes);

            // ==================== Init ====================
            drawAllRoutes();
            setupKoridorFilter();
            setTimeout(() => map.invalidateSize(), 200);

            console.log('Halaman Koridor Index siap!');
        </script>

        <style>
            #koridor-map {
                background-color: #e8ecf1;
                border-radius: 6px;
            }

            .koridor-dropdown {
                margin-bottom: 8px;
                border-radius: 6px;
                overflow: hidden;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

            .koridor-header.open .arrow {
                transform: rotate(180deg);
            }

            .koridor-body {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease-out;
                background: white;
                border-top: 1px solid #eee;
            }

            .koridor-body.open {
                max-height: 300px;
                overflow-y: auto;
            }

            /* Koridor tidak aktif tetap bisa diklik */
            .koridor-dropdown .koridor-header {
                transition: opacity 0.2s;
            }

            .koridor-dropdown .koridor-header:hover {
                opacity: 1 !important;
                background: #e3f2fd;
            }

            /* Badge libur */
            .badge-warning {
                font-size: 10px;
                padding: 3px 8px;
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

            .koridor-dropdown.active .koridor-header {
                background: #e3f2fd;
                border-left-width: 8px;
            }

            .arrow {
                transition: transform 0.3s;
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
@endsection

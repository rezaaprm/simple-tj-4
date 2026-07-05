@extends('layouts.backend')

@section('content')
    <div class="row mt-4">
        <div class="col-sm-12 mt-4">
            <div class="callout callout-success d-flex justify-content-between align-items-center flex-wrap">
                <span class="text-dark">
                    <i class="fa-solid fa-location-dot mr-2"></i>
                    Data Halte TransJakarta ({{ number_format(\App\Models\Stop::count()) }} halte)
                </span>
                <div class="d-flex gap-2 mt-2 mt-sm-0" style="gap: 8px;">
                    <button class="btn btn-sm btn-success" id="toggleHalteBtn">
                        <i class="fa-regular fa-eye-slash"></i> Toggle Halte
                    </button>
                    <button class="btn btn-sm btn-info" id="toggleMapBtn">
                        <i class="fa-solid fa-map"></i> Toggle Map
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                        <span>Daftar Halte TransJakarta</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-primary filter-btn" data-region="all" onclick="filterByRegion('all')">
                                <i class="fa-solid fa-globe"></i> Semua
                            </button>
                            <button class="btn btn-outline-primary filter-btn" data-region="utara" onclick="filterByRegion('utara')">
                                <i class="fa-solid fa-arrow-up"></i> Utara
                            </button>
                            <button class="btn btn-outline-primary filter-btn" data-region="timur" onclick="filterByRegion('timur')">
                                <i class="fa-solid fa-arrow-right"></i> Timur
                            </button>
                            <button class="btn btn-outline-primary filter-btn" data-region="selatan" onclick="filterByRegion('selatan')">
                                <i class="fa-solid fa-arrow-down"></i> Selatan
                            </button>
                            <button class="btn btn-outline-primary filter-btn" data-region="barat" onclick="filterByRegion('barat')">
                                <i class="fa-solid fa-arrow-left"></i> Barat
                            </button>
                            <button class="btn btn-outline-primary filter-btn" data-region="pusat" onclick="filterByRegion('pusat')">
                                <i class="fa-solid fa-location-dot"></i> Pusat
                            </button>
                            <button class="btn btn-outline-primary filter-btn" data-region="bodetabek" onclick="filterByRegion('bodetabek')">
                                <i class="fa-solid fa-city"></i> Bodetabek
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="halte-map" style="height: 400px; display: none; margin-bottom: 20px;"></div>

                    <table class="table table-bordered table-hover" id="datatable-halte">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID GTFS</th>
                                <th>Nama Halte</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Region</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="halte-table-body">
                            @foreach ($halte as $index => $item)
                                @php
                                    $lat = $item->stop_lat;
                                    $lng = $item->stop_lon;
                                    if ($lat >= -6.15 && $lat <= -6.05 && $lng >= 106.75 && $lng <= 106.95) {
                                        $region = 'utara';
                                    } elseif ($lat >= -6.3 && $lat <= -6.1 && $lng >= 106.9 && $lng <= 107.0) {
                                        $region = 'timur';
                                    } elseif ($lat >= -6.4 && $lat <= -6.2 && $lng >= 106.7 && $lng <= 106.9) {
                                        $region = 'selatan';
                                    } elseif ($lat >= -6.3 && $lat <= -6.1 && $lng >= 106.6 && $lng <= 106.8) {
                                        $region = 'barat';
                                    } elseif ($lat >= -6.25 && $lat <= -6.15 && $lng >= 106.8 && $lng <= 106.9) {
                                        $region = 'pusat';
                                    } else {
                                        $region = 'bodetabek';
                                    }
                                @endphp
                                <tr class="halte-row" data-lat="{{ $lat }}" data-lng="{{ $lng }}" data-name="{{ $item->stop_name }}" data-region="{{ $region }}">
                                    <td>{{ ($halte->currentPage() - 1) * $halte->perPage() + $index + 1 }}</td>
                                    <td>{{ $item->stop_id }}</td>
                                    <td>{{ $item->stop_name }}</td>
                                    <td>{{ $lat }}</td>
                                    <td>{{ $lng }}</td>
                                    <td><span class="badge region-badge region-{{ $region }}">{{ ucfirst($region) }}</span></td>
                                    <td>
                                        <button class="btn btn-xs btn-info focus-halte"
                                            data-lat="{{ $lat }}"
                                            data-lng="{{ $lng }}"
                                            data-name="{{ $item->stop_name }}">
                                            <i class="fa-solid fa-eye"></i> Lihat
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $halte->onEachSide(1)->links('layouts_backend.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

        <script>
            // ==================== DATA DARI CONTROLLER (seperti Projek 5) ====================
            const allStopsData = @json($allStops);
            console.log('Halte loaded:', allStopsData.length);

            let map;
            let stopMarkers = {};
            let stopsVisible = false;
            let mapVisible = false;
            let currentRegion = 'all';

            // Warna marker berdasarkan region (untuk filter)
            const REGION_COLORS = {
                utara: '#3498db',
                timur: '#2ecc71',
                selatan: '#e74c3c',
                barat: '#f39c12',
                pusat: '#9b59b6',
                bodetabek: '#95a5a6'
            };

            // Batas wilayah untuk zoom
            const REGION_BOUNDS = {
                utara: {
                    minLat: -6.15,
                    maxLat: -6.05,
                    minLng: 106.75,
                    maxLng: 106.95
                },
                timur: {
                    minLat: -6.3,
                    maxLat: -6.1,
                    minLng: 106.9,
                    maxLng: 107.0
                },
                selatan: {
                    minLat: -6.4,
                    maxLat: -6.2,
                    minLng: 106.7,
                    maxLng: 106.9
                },
                barat: {
                    minLat: -6.3,
                    maxLat: -6.1,
                    minLng: 106.6,
                    maxLng: 106.8
                },
                pusat: {
                    minLat: -6.25,
                    maxLat: -6.15,
                    minLng: 106.8,
                    maxLng: 106.9
                }
            };

            // Fungsi menentukan region (sama seperti di PHP)
            function getRegion(lat, lng) {
                // Utara (Jakarta Utara)
                if (lat >= -6.15 && lat <= -6.05 && lng >= 106.75 && lng <= 106.95) return 'utara';
                // Timur (Jakarta Timur)
                if (lat >= -6.3 && lat <= -6.15 && lng >= 106.9 && lng <= 107.0) return 'timur';
                // Selatan (Jakarta Selatan)
                if (lat >= -6.4 && lat <= -6.2 && lng >= 106.7 && lng <= 106.9) return 'selatan';
                // Barat (Jakarta Barat)
                if (lat >= -6.3 && lat <= -6.1 && lng >= 106.6 && lng <= 106.8) return 'barat';
                // Pusat (Jakarta Pusat)
                if (lat >= -6.25 && lat <= -6.15 && lng >= 106.8 && lng <= 106.9) return 'pusat';
                // Sisa lokasi sekitar Jakarta (Bodetabek)
                return 'bodetabek';
            }

            // ==================== Fungsi Toggle Halte dari Projek 5 ====================
            function toggleHalte() {
                const toggleBtn = document.getElementById('toggleHalteBtn');

                if (stopsVisible) {
                    // Sembunyikan semua halte
                    Object.values(stopMarkers).forEach(marker => {
                        if (marker && map.hasLayer(marker)) {
                            map.removeLayer(marker);
                        }
                    });
                    stopsVisible = false;
                    toggleBtn.innerHTML = '<i class="fa-regular fa-eye"></i> Toggle Halte';
                    toggleBtn.classList.remove('btn-success');
                    toggleBtn.classList.add('btn-secondary');
                    console.log('Halte disembunyikan');
                } else {
                    // Tampilkan semua halte sesuai filter region
                    drawHalteByRegion(currentRegion);
                    stopsVisible = true;
                    toggleBtn.innerHTML = '<i class="fa-regular fa-eye-slash"></i> Toggle Halte';
                    toggleBtn.classList.remove('btn-secondary');
                    toggleBtn.classList.add('btn-success');
                    console.log('Halte ditampilkan');
                }
            }

            // Menampilkan halte berdasarkan region, seperti drawAllStops tapi dengan filter
            function drawHalteByRegion(region) {
                // Hapus marker yang ada
                Object.values(stopMarkers).forEach(marker => {
                    if (marker && map.hasLayer(marker)) {
                        map.removeLayer(marker);
                    }
                });
                stopMarkers = {};

                // Filter data berdasarkan region
                const filteredStops = allStopsData.filter(stop => {
                    if (region === 'all') return true;
                    const stopRegion = getRegion(stop.lat, stop.lng);
                    return stopRegion === region;
                });

                // Buat marker untuk setiap halte
                filteredStops.forEach(stop => {
                    const stopRegion = getRegion(stop.lat, stop.lng);
                    const color = REGION_COLORS[stopRegion];

                    const marker = L.marker([stop.lat, stop.lng], {
                        icon: L.divIcon({
                            html: `<div style="background: ${color}; width: 10px; height: 10px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 3px rgba(0,0,0,0.3);"></div>`,
                            iconSize: [14, 14],
                            iconAnchor: [7, 7]
                        })
                    }).bindPopup(`<b>${stop.name}</b><br>ID: ${stop.id}<br>Region: ${ucfirst(stopRegion)}`);

                    marker.addTo(map);
                    stopMarkers[stop.id] = marker;
                });

                // Update info
                let infoSpan = document.getElementById('halteInfo');
                if (!infoSpan) {
                    infoSpan = document.createElement('span');
                    infoSpan.id = 'halteInfo';
                    infoSpan.className = 'ml-3 badge badge-info';
                    document.querySelector('.callout').appendChild(infoSpan);
                }
                const regionName = region === 'all' ? 'Semua Wilayah' : ucfirst(region);
                infoSpan.innerHTML = `📍 ${regionName}: ${filteredStops.length} halte`;
            }

            // ==================== Fungsi Filter Region ====================
            function filterByRegion(region) {
                currentRegion = region;

                // Update active state pada tombol filter
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                    if (btn.getAttribute('data-region') === region) {
                        btn.classList.remove('btn-outline-primary');
                        btn.classList.add('btn-primary');
                    }
                });

                // Filter baris tabel
                const rows = document.querySelectorAll('.halte-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const rowRegion = row.getAttribute('data-region');
                    const show = (region === 'all' || rowRegion === region);

                    if (show) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Badge di tabel
                if (region !== 'all') {
                    rows.forEach(row => {
                        const badgeSpan = row.querySelector('.region-badge');
                        if (badgeSpan && row.style.display !== 'none') {
                            badgeSpan.textContent = ucfirst(region);
                            badgeSpan.className = `badge region-badge region-${region}`;
                        }
                    });
                } else {
                    rows.forEach(row => {
                        const originalRegion = row.getAttribute('data-region');
                        const badgeSpan = row.querySelector('.region-badge');
                        if (badgeSpan) {
                            let displayRegion = originalRegion;
                            if (displayRegion === 'lainnya') displayRegion = 'bodetabek';
                            badgeSpan.textContent = ucfirst(displayRegion);
                            badgeSpan.className = `badge region-badge region-${displayRegion}`;
                        }
                    });
                }

                // Update info region
                let regionInfo = document.getElementById('regionInfo');
                if (!regionInfo) {
                    regionInfo = document.createElement('span');
                    regionInfo.id = 'regionInfo';
                    regionInfo.className = 'ml-3 badge badge-secondary';
                    document.querySelector('.card-header .d-flex').appendChild(regionInfo);
                }
                const regionName = region === 'all' ? 'Semua Wilayah' : ucfirst(region);
                regionInfo.innerHTML = `Filter: ${regionName} (${visibleCount} halte)`;

                // Jika halte sedang aktif, update map
                if (stopsVisible && mapVisible && map) {
                    drawHalteByRegion(region);
                }

                // Zoom ke region jika bukan 'all' dan map visible
                if (mapVisible && map && region !== 'all' && REGION_BOUNDS[region]) {
                    const bounds = REGION_BOUNDS[region];
                    map.fitBounds([
                        [bounds.minLat, bounds.minLng],
                        [bounds.maxLat, bounds.maxLng]
                    ]);
                }
            }

            function ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // ==================== Toggle Map ====================
            function toggleMap() {
                const mapDiv = document.getElementById('halte-map');
                const toggleBtn = document.getElementById('toggleMapBtn');

                if (mapVisible) {
                    mapDiv.style.display = 'none';
                    mapVisible = false;
                    toggleBtn.innerHTML = '<i class="fa-solid fa-map"></i> Toggle Map';
                } else {
                    mapDiv.style.display = 'block';
                    mapVisible = true;
                    toggleBtn.innerHTML = '<i class="fa-solid fa-map"></i> Sembunyikan Map';

                    if (!map) {
                        initMap();
                    } else {
                        setTimeout(() => {
                            map.invalidateSize();
                            if (stopsVisible) {
                                drawHalteByRegion(currentRegion);
                            }
                        }, 100);
                    }
                }
            }

            function initMap() {
                // Batas Wilayah sesuai dengan data halte terluar Jabodetabek
                const jabodetabekBounds = L.latLngBounds(
                    L.latLng(-6.6, 106.5), // Barat Daya (Bogor, Ciputat)
                    L.latLng(-5.9, 107.2) // Timur Laut (Bekasi, Tangerang)
                );

                map = L.map('halte-map', {
                    center: [-6.2088, 106.8456],
                    zoom: 11,
                    minZoom: 10,
                    maxZoom: 18,
                    maxBounds: jabodetabekBounds,
                    maxBoundsViscosity: 1.0,
                    bounceAtZoomLimits: false,
                    attributionControl: false
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: ''
                }).addTo(map);

                // Garis batas merah
                L.rectangle(jabodetabekBounds, {
                    color: "#ff4444",
                    weight: 2,
                    opacity: 0.3,
                    fillOpacity: 0,
                    dashArray: "5, 5"
                }).addTo(map);

                if (stopsVisible) {
                    drawHalteByRegion(currentRegion);
                }
            }

            function focusHalte(button) {
                const lat = parseFloat(button.getAttribute('data-lat'));
                const lng = parseFloat(button.getAttribute('data-lng'));
                const name = button.getAttribute('data-name');

                if (!mapVisible) toggleMap();
                if (!stopsVisible) toggleHalte();

                setTimeout(() => {
                    map.setView([lat, lng], 17);
                    L.popup().setLatLng([lat, lng]).setContent(`<b>${name}</b>`).openOn(map);
                }, 200);
            }

            // ==================== Event Listener ====================
            document.getElementById('toggleHalteBtn').addEventListener('click', toggleHalte);
            document.getElementById('toggleMapBtn').addEventListener('click', toggleMap);
            document.querySelectorAll('.focus-halte').forEach(btn => {
                btn.addEventListener('click', function() {
                    focusHalte(this);
                });
            });

            // ==================== Init Datatables ====================
            $(document).ready(function() {
                // Hanya jalankan jika tabel ada
                if ($('#datatable-halte').length) {
                    $('#datatable-halte').DataTable({
                        "pageLength": 25,
                        "language": {
                            "search": "Cari:",
                            "lengthMenu": "Tampilkan _MENU_ data per halaman",
                            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            "paginate": {
                                "first": "Pertama",
                                "last": "Terakhir",
                                "next": "→",
                                "previous": "←"
                            }
                        },
                        "bDestroy": true,
                        "bPaginate": false // Matikan paginasi DataTables (pakai dari Laravel)
                    });
                }
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .pagination {
                flex-wrap: wrap;
                gap: 5px;
                align-items: center;
            }

            .pagination .page-link {
                padding: 0.375rem 0.75rem !important;
                font-size: 0.875rem !important;
                line-height: 1.5 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-width: 32px;
            }

            .region-badge {
                font-size: 0.7rem;
                padding: 3px 8px;
            }

            .region-utara {
                background-color: #3498db;
                color: white;
            }

            .region-timur {
                background-color: #2ecc71;
                color: white;
            }

            .region-selatan {
                background-color: #e74c3c;
                color: white;
            }

            .region-barat {
                background-color: #f39c12;
                color: white;
            }

            .region-pusat {
                background-color: #9b59b6;
                color: white;
            }

            .region-bodetabek {
                background-color: #95a5a6;
                color: white;
            }

            .filter-btn.btn-primary {
                background-color: #007bff;
                border-color: #007bff;
                color: white;
            }

            .filter-btn.btn-outline-primary:hover {
                background-color: #007bff;
                color: white;
            }

            .gap-2 {
                gap: 8px;
            }
        </style>
    @endpush
@endsection

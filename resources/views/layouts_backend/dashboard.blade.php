@extends('layouts.backend')

@section('title', 'Dashboard')

@section('content')
<div class="row mt-4">
    <div class="col-lg-3 col-6 mt-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($jumlah_koridor) }}</h3>
                <p>Koridor / Routes</p>
            </div>
            <div class="icon">
                <i class="fas fa-route"></i>
            </div>
            <a href="{{ route('admin.koridor.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6 mt-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($jumlah_halte) }}</h3>
                <p>Halte / Stops</p>
            </div>
            <div class="icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <a href="{{ route('admin.halte.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6 mt-4">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($jumlah_perjalanan) }}</h3>
                <p>Perjalanan / Trips</p>
            </div>
            <div class="icon">
                <i class="fas fa-bus"></i>
            </div>
            <a href="{{ route('admin.transjakarta.map') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6 mt-4">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($jumlah_pencarian) }}</h3>
                <p>Pencarian / Logs</p>
            </div>
            <div class="icon">
                <i class="fas fa-search"></i>
            </div>
            <a href="{{ route('admin.pencarian.log') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Statistik Pencarian
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Rata-rata Waktu Eksekusi</th>
                        <td>{{ number_format($rata_waktu ?? 0, 2) }} ms</td>
                    </tr>
                    <tr>
                        <th>Pencarian Hari Ini</th>
                        <td>{{ $pencarian_hari_ini }} kali</td>
                    </tr>
                    <tr>
                        <th>Dataset GTFS</th>
                        <td>
                            <button class="btn btn-sm btn-primary" id="openDataModalBtn">
                                <i class="fas fa-database"></i> Lihat Data
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <th>Halaman Frontend</th>
                        <td>
                            <!-- <a href="{{ url('/frontend') }}" class="btn btn-sm btn-success" target="_blank">
                                <i class="fas fa-globe"></i> Lihat Profile
                            </a> -->
                            <a href="{{ url('/') }}" class="btn btn-sm btn-success">
                                <i class="fas fa-globe"></i> Lihat Profile
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marked-alt mr-2"></i>
                    Visualisasi
                </h3>
            </div>
            <div class="card-body">
                <img src="{{ asset('image/data_jumlah_halte_transjakarta.png') }}"
                    alt="Grafik Halte TransJakarta"
                    class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Modal Data GTFS (Projek 5) -->
<div id="gtfsDataModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 20px; border-radius: 8px; width: 80%; max-height: 80vh; overflow-y: auto; position: relative;">
        <span id="closeDataModal" style="position: absolute; right: 20px; top: 10px; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>

        <h3>Data Mentah Transjakarta (GTFS)</h3>
        <hr>

        <div style="margin: 15px 0; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <label>Tampilkan:</label>
                <select id="dataLimitFilter" style="padding: 5px; border-radius: 4px; border: 1px solid #ddd;">
                    <option value="10">10 Data</option>
                    <option value="50">50 Data</option>
                    <option value="100">100 Data</option>
                    <option value="500">500 Data</option>
                    <option value="all">Semua Data</option>
                </select>
            </div>

            <!-- Dropdown untuk pilih jenis JSON -->
            <div style="display: flex; align-items: center; gap: 10px; margin-left: auto;">
                <div id="poiCategoryGroup" style="display: none; align-items: center; gap: 10px;">
                    <label style="white-space: nowrap;">Kategori POI :</label>
                    <select id="poiCategoryFilter" style="padding: 5px; border-radius: 4px; border: 1px solid #ddd; background: white; min-width: 150px;">
                        <option value="semua">Semua Kategori</option>
                    </select>
                </div>

                <label style="white-space: nowrap;">Tampilkan JSON :</label>
                <select id="jsonTypeFilter" style="padding: 5px; border-radius: 4px; border: 1px solid #ddd; background: white; min-width: 120px;">
                    <option value="semua">Semua</option>
                    <option value="halte">Halte</option>
                    <option value="shape">Shape</option>
                    <option value="rute">Rute</option>
                    <option value="warna">Warna</option>
                    <option value="poi">POI (Tempat Menarik)</option>
                </select>
                <a href="#" id="jsonViewBtn" target="_blank"
                    style="padding: 6px 15px; background: white; border: 1px solid #333; border-radius: 4px; color: #333; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-code"></i> Lihat JSON
                </a>
            </div>
        </div>

        <h4>Daftar Rute</h4>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead style="background: #f2f2f2;">
                <tr>
                    <th style="padding: 8px; text-align: left;">ID</th>
                    <th style="padding: 8px; text-align: left;">Nama Singkat</th>
                    <th style="padding: 8px; text-align: left;">Nama Panjang</th>
                </tr>
            </thead>
            <tbody id="modalRouteTableBody"></tbody>
        </table>

        <h4>Daftar Halte (Seluruh Koridor)</h4>
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f2f2f2;">
                <tr>
                    <th style="padding: 8px; text-align: left;">ID</th>
                    <th style="padding: 8px; text-align: left;">Nama Halte</th>
                    <th style="padding: 8px; text-align: left;">Lat</th>
                    <th style="padding: 8px; text-align: left;">Lon</th>
                </tr>
            </thead>
            <tbody id="modalStopTableBody"></tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    // ==================== Modal Data GTFS ====================
    const modal = document.getElementById("gtfsDataModal");
    const btnLihat = document.getElementById("openDataModalBtn");
    const spanClose = document.getElementById("closeDataModal");
    const filterSelect = document.getElementById("dataLimitFilter");
    const jsonTypeFilter = document.getElementById("jsonTypeFilter");
    const jsonViewBtn = document.getElementById("jsonViewBtn");

    // Data routes untuk modal, ambil dari controller atau fetch
    let routesData = [];

    // Fungsi untuk mengambil data routes dari server
    async function fetchRoutesData() {
        try {
            const response = await fetch('/routes-json');
            const result = await response.json();
            if (result.success) {
                routesData = result.data;
                return routesData;
            }
            return [];
        } catch (error) {
            console.error('Error fetching routes:', error);
            return [];
        }
    }

    // Fungsi untuk mengambil semua halte unik dari routes
    function getAllUniqueStops() {
        const allStops = [];
        const uniqueIds = new Set();

        routesData.forEach(route => {
            if (route.stops) {
                route.stops.forEach(stop => {
                    if (!uniqueIds.has(stop.id)) {
                        uniqueIds.add(stop.id);
                        allStops.push(stop);
                    }
                });
            }
        });

        return allStops;
    }

    function renderModalData(limit) {
        const routeBody = document.getElementById("modalRouteTableBody");
        const stopBody = document.getElementById("modalStopTableBody");

        let displayRoutes = (limit === 'all') ? routesData : routesData.slice(0, parseInt(limit));
        let allStops = getAllUniqueStops();
        let displayStops = (limit === 'all') ? allStops : allStops.slice(0, parseInt(limit));

        routeBody.innerHTML = displayRoutes.map(route => `
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 8px;">${route.id}</td>
                <td style="padding: 8px;">${route.short_name}</td>
                <td style="padding: 8px;">${route.long_name}</td>
            </tr>
        `).join('');

        stopBody.innerHTML = displayStops.map(stop => `
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 8px;">${stop.id}</td>
                <td style="padding: 8px;">${stop.name}</td>
                <td style="padding: 8px;">${stop.lat}</td>
                <td style="padding: 8px;">${stop.lng}</td>
            </tr>
        `).join('');
    }

    // Event Listener untuk filter data di modal
    if (filterSelect) {
        filterSelect.onchange = function() {
            renderModalData(this.value);
        };
    }

    // Event Listener untuk membuka modal
    if (btnLihat) {
        btnLihat.onclick = async function() {
            // Ambil data routes jika belum ada
            if (routesData.length === 0) {
                await fetchRoutesData();
            }
            modal.style.display = "block";
            renderModalData(filterSelect ? filterSelect.value : '10');
        };
    }

    // Event Listener untuk menutup modal (tombol X)
    if (spanClose) {
        spanClose.onclick = function() {
            modal.style.display = "none";
        };
    }

    // Event Listener untuk menutup modal (klik di luar modal)
    window.onclick = function(e) {
        if (e.target == modal) {
            modal.style.display = "none";
        }
    };

    // Fungsi untuk mengambil daftar kategori POI
    async function loadPoiCategories() {
        try {
            const response = await fetch('/api/poi/categories');
            const categories = await response.json();

            const categorySelect = document.getElementById('poiCategoryFilter');
            if (categorySelect && categories.length > 0) {
                // Hapus option "Semua Kategori" sementara
                categorySelect.innerHTML = '<option value="semua">Semua Kategori</option>';

                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category.charAt(0).toUpperCase() + category.slice(1);
                    categorySelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Gagal load kategori POI:', error);
        }
    }

    // Tampilkan / sembunyikan dropdown kategori berdasarkan pilihan JSON
    if (jsonTypeFilter) {
        jsonTypeFilter.addEventListener('change', function() {
            const poiCategoryGroup = document.getElementById('poiCategoryGroup');
            if (this.value === 'poi') {
                if (poiCategoryGroup) poiCategoryGroup.style.display = 'flex';
                // Load kategori jika dropdown masih kosong (hanya option "Semua Kategori")
                const categorySelect = document.getElementById('poiCategoryFilter');
                if (categorySelect && categorySelect.options.length === 1) {
                    loadPoiCategories();
                }
            } else {
                if (poiCategoryGroup) poiCategoryGroup.style.display = 'none';
            }
        });
    }

    // Trigger sekali saat halaman dimuat untuk set initial state
    if (jsonTypeFilter && jsonTypeFilter.value === 'poi') {
        const event = new Event('change');
        jsonTypeFilter.dispatchEvent(event);
    }

    // Handler untuk dropdown JSON
    if (jsonViewBtn && jsonTypeFilter) {
        jsonViewBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            const selectedType = jsonTypeFilter.value;
            let url = '';

            switch (selectedType) {
                case 'halte':
                    url = "{{ url('/api/json/halte') }}";
                    break;
                case 'shape':
                    url = "{{ url('/api/json/shape') }}";
                    break;
                case 'rute':
                    url = "{{ url('/api/json/rute') }}";
                    break;
                case 'warna':
                    url = "{{ url('/api/json/warna') }}";
                    break;
                case 'poi':
                    const selectedCategory = document.getElementById('poiCategoryFilter')?.value;
                    if (selectedCategory && selectedCategory !== 'semua') {
                        url = "{{ url('/api/json/poi/kategori') }}/" + selectedCategory;
                    } else {
                        url = "{{ url('/api/json/poi') }}";
                    }
                    break;
                default:
                    url = "{{ url('/routes-json') }}";
            }

            // 🔥 TAMPILKAN DI MODAL, BUKAN TAB BARU
            try {
                // Tampilkan loading
                const jsonModal = document.getElementById('jsonPreviewModal');
                const jsonContent = document.getElementById('jsonPreviewContent');

                if (!jsonModal) {
                    // Buat modal jika belum ada
                    createJsonPreviewModal();
                }

                document.getElementById('jsonPreviewModal').style.display = 'block';
                document.getElementById('jsonPreviewTitle').innerHTML = `📄 Preview JSON - ${selectedType.toUpperCase()}`;
                document.getElementById('jsonPreviewContent').innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Memuat data...</div>';

                // Fetch data
                const response = await fetch(url);
                const data = await response.json();

                // Format JSON dengan highlight
                const formattedJson = JSON.stringify(data, null, 2);
                const highlightedJson = syntaxHighlightJson(formattedJson);

                document.getElementById('jsonPreviewContent').innerHTML = `<pre class="json-pre">${highlightedJson}</pre>`;

            } catch (error) {
                document.getElementById('jsonPreviewContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Gagal memuat data: ${error.message}
                </div>
            `;
            }
        });
    }

    // Fungsi untuk membuat modal preview JSON
    function createJsonPreviewModal() {
        const modalHtml = `
        <div id="jsonPreviewModal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7);">
            <div style="background-color: white; margin: 3% auto; padding: 20px; border-radius: 8px; width: 85%; max-height: 85vh; overflow-y: auto; position: relative;">
                <span id="closeJsonModal" style="position: absolute; right: 20px; top: 10px; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                <h4 id="jsonPreviewTitle">📄 Preview JSON</h4>
                <hr>
                <div id="jsonPreviewContent" style="background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 6px; overflow-x: auto; font-family: monospace; font-size: 12px; max-height: 70vh; overflow-y: auto;">
                    Loading...
                </div>
                <div class="mt-3 text-right">
                    <button id="copyJsonBtn" class="btn btn-sm btn-secondary">
                        <i class="fas fa-copy"></i> Copy JSON
                    </button>
                    <button id="closeJsonModalBtn" class="btn btn-sm btn-primary">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Event close modal
        document.getElementById('closeJsonModal').onclick = function() {
            document.getElementById('jsonPreviewModal').style.display = 'none';
        };
        document.getElementById('closeJsonModalBtn').onclick = function() {
            document.getElementById('jsonPreviewModal').style.display = 'none';
        };
        document.getElementById('copyJsonBtn').onclick = function() {
            const content = document.getElementById('jsonPreviewContent').innerText;
            navigator.clipboard.writeText(content);
            alert('JSON berhasil disalin ke clipboard!');
        };

        window.onclick = function(e) {
            const modal = document.getElementById('jsonPreviewModal');
            if (e.target == modal) {
                modal.style.display = 'none';
            }
        };
    }

    // Fungsi untuk syntax highlight JSON
    function syntaxHighlightJson(json) {
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
            let cls = 'json-number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'json-key';
                } else {
                    cls = 'json-string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'json-boolean';
            } else if (/null/.test(match)) {
                cls = 'json-null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    }

    // Efek hover pada tombol JSON
    if (jsonViewBtn) {
        jsonViewBtn.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f0f0f0';
        });
        jsonViewBtn.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'white';
        });
    }
</script>
@endpush

@push('styles')
<style>
    /* Syntax Highlight untuk JSON Preview */
    .json-pre {
        background: #1e1e1e;
        padding: 15px;
        border-radius: 6px;
        overflow-x: auto;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        margin: 0;
    }

    .json-key {
        color: #9cdcfe;
    }

    .json-string {
        color: #ce9178;
    }

    .json-number {
        color: #b5cea8;
    }

    .json-boolean {
        color: #569cd6;
    }

    .json-null {
        color: #569cd6;
    }
</style>
@endpush

@endsection
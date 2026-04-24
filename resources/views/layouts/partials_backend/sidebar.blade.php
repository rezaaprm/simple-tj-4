<div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="info">
        <a href="#" class="d-block">Admin</a>
    </div>
</div>

<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.transjakarta.map') }}" class="nav-link {{ request()->routeIs('admin.transjakarta.map') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map"></i>
                <p>Peta Rute TJ</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.algoritma') }}" class="nav-link {{ request()->routeIs('admin.algoritma') ? 'active' : '' }}">
                <i class="nav-icon fas fa-calculator"></i>
                <p>Algoritma Rute</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.halte.index') }}" class="nav-link {{ request()->routeIs('admin.halte.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map-marker-alt"></i>
                <p>Data Halte</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.koridor.index') }}" class="nav-link {{ request()->routeIs('admin.koridor.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-road"></i>
                <p>Data Koridor</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.pencarian.log') }}" class="nav-link {{ request()->routeIs('admin.pencarian.log') ? 'active' : '' }}">
                <i class="nav-icon fas fa-history"></i>
                <p>Log Pencarian</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.about.index') }}" class="nav-link {{-- request()->routeIs('admin.about.index') ? 'active' : '' --}}">
                <i class="nav-icon fas fa-info-circle"></i>
                <p>About</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.info_statistik.index') }}" class="nav-link {{-- request()->routeIs('admin.info_statistik.index') ? 'active' : '' --}}">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Info Statistik</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.destinasi.index') }}" class="nav-link {{-- request()->routeIs('admin.destinasi.index') ? 'active' : '' --}}">
                <i class="nav-icon fas fa-map-marked-alt"></i>
                <p>Galeri Destinasi</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.galeri.index') }}" class="nav-link {{-- request()->routeIs('admin.galeri.index') ? 'active' : '' --}}">
                <i class="nav-icon fas fa-images"></i>
                <p>Galeri Foto</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.kolaborasi.index') }}" class="nav-link {{-- request()->routeIs('admin.kolaborasi.index') ? 'active' : '' --}}">
                <i class="nav-icon fas fa-handshake"></i>
                <p>Kolaborasi</p>
            </a>
        </li>
    </ul>
</nav>
<div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="info">
        @if(Auth::guard('admin')->check())
        <a href="#" class="d-block">Admin: {{ Auth::guard('admin')->user()->nama }}</a>
        @elseif(Auth::guard('users')->check())
        <a href="#" class="d-block">User: {{ Auth::guard('users')->user()->nama }}</a>
        @else
        <a href="#" class="d-block">Guest</a>
        @endif
    </div>
</div>

<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <!-- Dashboard (hanya untuk admin dan user yang sudah login) -->
        @if(Auth::guard('admin')->check())
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard Admin</p>
            </a>
        </li>
        @elseif(Auth::guard('users')->check())
        <li class="nav-item">
            <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard User</p>
            </a>
        </li>
        @endif

        <!-- Peta Rute TJ (bisa diakses semua yang login, arahkan ke route publik atau user/admin sesuai) -->
        <li class="nav-item">
            <a href="{{ route('public.map') }}" class="nav-link {{ request()->routeIs('public.map') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map"></i>
                <p>Peta Rute TJ</p>
            </a>
        </li>

        <!-- Data Halte -->
        @if(Auth::guard('admin')->check())
        <li class="nav-item">
            <a href="{{ route('admin.halte.index') }}" class="nav-link {{ request()->routeIs('admin.halte.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map-marker-alt"></i>
                <p>Data Halte</p>
            </a>
        </li>
        @elseif(Auth::guard('users')->check())
        <li class="nav-item">
            <a href="{{ route('user.halte.index') }}" class="nav-link {{ request()->routeIs('user.halte.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map-marker-alt"></i>
                <p>Data Halte</p>
            </a>
        </li>
        @endif

        <!-- Data Koridor -->
        @if(Auth::guard('admin')->check())
        <li class="nav-item">
            <a href="{{ route('admin.koridor.index') }}" class="nav-link {{ request()->routeIs('admin.koridor.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-road"></i>
                <p>Data Koridor</p>
            </a>
        </li>
        @elseif(Auth::guard('users')->check())
        <li class="nav-item">
            <a href="{{ route('user.koridor.index') }}" class="nav-link {{ request()->routeIs('user.koridor.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-road"></i>
                <p>Data Koridor</p>
            </a>
        </li>
        @endif

        <!-- Log Pencarian -->
        @if(Auth::guard('admin')->check())
        <li class="nav-item">
            <a href="{{ route('admin.pencarian.log') }}" class="nav-link {{ request()->routeIs('admin.pencarian.log') ? 'active' : '' }}">
                <i class="nav-icon fas fa-history"></i>
                <p>Log Pencarian</p>
            </a>
        </li>
        @elseif(Auth::guard('users')->check())
        <li class="nav-item">
            <a href="{{ route('user.pencarian.log') }}" class="nav-link {{ request()->routeIs('user.pencarian.log') ? 'active' : '' }}">
                <i class="nav-icon fas fa-history"></i>
                <p>Log Pencarian</p>
            </a>
        </li>
        @endif

        <!-- Menu khusus admin (CRUD) -->
        @if(Auth::guard('admin')->check())
        <li class="nav-item">
            <a href="{{ route('admin.about.index') }}" class="nav-link {{ request()->routeIs('admin.about.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-info-circle"></i>
                <p>About</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.info_statistik.index') }}" class="nav-link {{ request()->routeIs('admin.info_statistik.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Info Statistik</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.destinasi.index') }}" class="nav-link {{ request()->routeIs('admin.destinasi.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map-marked-alt"></i>
                <p>Galeri Destinasi</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.galeri.index') }}" class="nav-link {{ request()->routeIs('admin.galeri.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-images"></i>
                <p>Galeri Foto</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.kolaborasi.index') }}" class="nav-link {{ request()->routeIs('admin.kolaborasi.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-handshake"></i>
                <p>Kolaborasi</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.poi.index') }}" class="nav-link {{ request()->routeIs('admin.poi.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map-pin"></i>
                <p>Data POI</p>
            </a>
        </li>
        @endif
    </ul>
</nav>
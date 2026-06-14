<ul class="navbar-nav mr-auto">
    <li class="nav-item d-none d-sm-inline-block">
        @if(Auth::guard('admin')->check())
        <a href="#" class="nav-link text-dark">
            <i class="fas fa-user-shield"></i> Admin: {{ Auth::guard('admin')->user()->nama }}
        </a>
        @elseif(Auth::guard('users')->check())
        <a href="#" class="nav-link text-dark">
            <i class="fas fa-user"></i> User: {{ Auth::guard('users')->user()->nama }}
        </a>
        @else
        <a href="{{ route('login') }}" class="nav-link text-dark">
            <i class="fas fa-sign-in-alt"></i> Login
        </a>
        @endif
    </li>
</ul>
<ul class="navbar-nav ml-auto">
    @if(Auth::guard('admin')->check() || Auth::guard('users')->check())
    <li class="nav-item d-none d-sm-inline-block">
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link nav-link text-danger" style="display: inline; cursor: pointer;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </li>
    @endif
    <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('frontend.explore') }}" class="nav-link text-success">
            <i class="fas fa-globe"></i> Frontend
        </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link text-dark">Sistem Transportasi Jakarta</a>
    </li>
</ul>
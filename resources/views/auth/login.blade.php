@extends('layouts.auth')
@section('content')
<div class="text-center">
    <div class="mb-3">
        <img src="{{ asset('image/laravel_logo_black.png') }}" style="height:100px">
    </div>
    <div class="mb-5">
        {{-- <h4 class="fw-bold">Welcome to Transjakarta</h4>
        <span class="text-muted">Sign in to your account</span> --}}

        <h4 class="fw-bold">Welcome to Transjakarta</h4>
        <span class="text-muted">
            Sign in to continue your journey in the campaign
        </span>
    </div>
</div>

<div class="card border-0 px-2 py-2" id="auth-card">
    <div class="card-body">
        <form action="{{ route('login.process') }}" method="post">
            @csrf
            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" id="email" autocomplete="email">
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text" id="togglePassword">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                    <input type="password" class="form-control" name="password" id="password">
                </div>
                <small class="text-muted">Password minimum 8 characters</small>
            </div>
            <div class="mb-0">
                <button type="submit" class="btn btn-login btn-warning w-100">
                    <i class="fas fa-paper-plane me-2"></i>Login
                </button>
            </div>
            <div class="text-center mt-3">
                <small>Don't have an account? <a href="{{ route('register') }}" class="text-warning fw-bold">Register here</a></small>
            </div>
        </form>
    </div>
    {{-- card-body --}}
</div>
{{-- card --}}

@push('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        let errors = [];

        const email = document.getElementById('email');
        const password = document.getElementById('password');

        if (!email.value.trim()) errors.push('Email tidak boleh kosong');
        if (!password.value) errors.push('Password tidak boleh kosong');

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
        }
    });
</script>
@endpush

@endsection
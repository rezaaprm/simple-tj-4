@extends('layouts.auth')

@section('banner', asset('image/banner_login.jpg'))

@section('title', 'Login - TransJakarta')

@section('content')
    <div class="text-center">
        <div class="mt-3 mb-3">
            <img src="{{ asset('image/logo_tj.png') }}" style="height:100px" alt="Logo TransJakarta">
        </div>

        <div class="mb-5">
            <h4 class="fw-bold">Welcome to Transjakarta</h4>
            <span class="text-muted">
                Sign in to continue your journey in the campaign
            </span>
        </div>
    </div>

    <div class="card border-0 px-2 py-2" id="auth-card">
        <div class="card-body">

            @if ($errors->has('email'))
                <div class="alert alert-danger">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="post">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" id="email" value="{{ old('email') }}" autocomplete="email">
                    </div>
                    <small class="text-danger" id="email-error">
                        @error('email')
                            {{ $message }}
                        @enderror
                    </small>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text" id="togglePassword">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" id="password">
                    </div>
                    <small class="text-muted">Password minimum 8 characters</small>
                    <br>
                    <small class="text-danger" id="password-error">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </small>
                </div>

                <div class="mb-0">
                    <button type="submit" class="btn btn-login btn-warning w-100">
                        <i class="fas fa-paper-plane me-2"></i>Login
                    </button>
                </div>

                <div class="text-center mt-3">
                    <small>
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-warning fw-bold">Register here</a>
                    </small>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('frontend.explore') }}" class="text-muted">
                        Proceed without registering →
                    </a>
                </div>
            </form>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const email = document.getElementById('email');
                const password = document.getElementById('password');

                email.addEventListener('input', function() {
                    const error = document.getElementById('email-error');
                    if (this.value.trim() === '') {
                        error.textContent = 'Email tidak boleh kosong';
                        this.classList.add('is-invalid');
                    } else {
                        error.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                });

                password.addEventListener('input', function() {
                    const error = document.getElementById('password-error');
                    if (this.value === '') {
                        error.textContent = 'Password tidak boleh kosong';
                        this.classList.add('is-invalid');
                    } else {
                        error.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                });
            });
        </script>
    @endpush
@endsection

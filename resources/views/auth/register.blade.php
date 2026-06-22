@extends('layouts.auth')

@section('banner', asset('image/banner_registrasi.webp'))

@section('title', 'Register - TransJakarta')

@section('content')
    <div class="text-center">
        <div class="mt-3 mb-3">
            <img src="{{ asset('image/logo_tj.png') }}" style="height:100px" alt="Logo TransJakarta">
        </div>

        <div class="mb-5">
            <h4 class="fw-bold">Create Account</h4>
            <span class="text-muted">
                Start your journey with TransJakarta
            </span>
        </div>
    </div>

    <div class="card border-0 px-2 py-2 mb-3" id="auth-card">
        <div class="card-body">

            <form action="{{ route('register.process') }}" method="post">
                @csrf

                <div class="mb-3">
                    <label for="nama" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                            name="nama" id="nama" value="{{ old('nama') }}" required>
                    </div>
                    <small class="text-danger" id="nama-error">
                        @error('nama')
                            {{ $message }}
                        @enderror
                    </small>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" id="email" value="{{ old('email') }}" required>
                    </div>
                    <small class="text-danger" id="email-error">
                        @error('email')
                            {{ $message }}
                        @enderror
                    </small>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text" id="togglePassword">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" id="password" required>
                    </div>
                    <small class="text-muted">Password minimum 8 characters</small>
                    <br>
                    <small class="text-danger" id="password-error">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </small>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control"
                            name="password_confirmation" id="password_confirmation" required>
                    </div>
                    <small class="text-danger" id="confirm-password-error"></small>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-login btn-warning w-100">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </button>
                </div>

                <div class="text-center">
                    <small>
                        Already have an account?
                        <a href="{{ route('login') }}">Login here</a>
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
                const nama = document.getElementById('nama');
                const email = document.getElementById('email');
                const password = document.getElementById('password');
                const confirmPassword = document.getElementById('password_confirmation');

                nama.addEventListener('input', function() {
                    const error = document.getElementById('nama-error');
                    const value = this.value.trim();
                    if (value === '') {
                        error.textContent = 'Nama tidak boleh kosong';
                        this.classList.add('is-invalid');
                    } else if (!/^[a-zA-Z\s\.]+$/.test(value)) {
                        error.textContent = 'Nama hanya boleh mengandung huruf, spasi, dan titik';
                        this.classList.add('is-invalid');
                    } else {
                        error.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                });

                email.addEventListener('input', function() {
                    const error = document.getElementById('email-error');
                    const value = this.value.trim();
                    if (value === '') {
                        error.textContent = 'Email tidak boleh kosong';
                        this.classList.add('is-invalid');
                    } else if (!/^[^\s@]+@([^\s@]+\.)+[^\s@]+$/.test(value)) {
                        error.textContent = 'Format email tidak valid';
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
                    } else if (this.value.length < 8) {
                        error.textContent = 'Password minimal 8 karakter';
                        this.classList.add('is-invalid');
                    } else {
                        error.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                    checkConfirmPassword();
                });

                confirmPassword.addEventListener('input', checkConfirmPassword);

                function checkConfirmPassword() {
                    const error = document.getElementById('confirm-password-error');
                    if (confirmPassword.value === '') {
                        error.textContent = 'Konfirmasi password wajib diisi';
                        confirmPassword.classList.add('is-invalid');
                    } else if (confirmPassword.value !== password.value) {
                        error.textContent = 'Konfirmasi password tidak cocok';
                        confirmPassword.classList.add('is-invalid');
                    } else {
                        error.textContent = '';
                        confirmPassword.classList.remove('is-invalid');
                    }
                }
            });
        </script>
    @endpush
@endsection

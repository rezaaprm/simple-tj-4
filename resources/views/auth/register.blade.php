@extends('layouts.auth')
@section('content')
<div class="text-center">
    <div class="mb-3">
        <img src="{{ asset('image/laravel_logo_black.png') }}" style="height:100px">
    </div>
    <div class="mb-5">
        <h4 class="fw-bold">Create Account</h4>
        <span class="text-muted">
            Mulai Perjalanan dengan TransJakarta
        </span>
    </div>
</div>

<div class="card border-0 px-2 py-2" id="auth-card">
    <div class="card-body">
        <form action="{{ route('register.process') }}" method="post">
            @csrf
            <div class="mb-3">
                <label for="nama" class="form-label">Full Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" id="nama" value="{{ old('nama') }}" required>
                </div>
                @error('nama')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" required>
                </div>
                @error('email')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text" id="togglePassword">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required>
                </div>
                <small class="text-muted">Password minimum 8 characters</small>
                @error('password')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                </div>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-login btn-warning w-100">
                    <i class="fas fa-user-plus me-2"></i>Register
                </button>
            </div>
            <div class="text-center">
                <small>Already have an account? <a href="{{ route('login') }}">Login here</a></small>
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('frontend.explore') }}" class="text-muted">Proceed without registering →</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        let isValid = true;
        let errors = [];

        // Nama: hanya huruf, spasi, dan titik
        const nama = document.getElementById('nama');
        const namaValue = nama.value.trim();
        if (namaValue === '') {
            errors.push('Nama tidak boleh kosong');
            isValid = false;
        } else if (!/^[a-zA-Z\s\.]+$/.test(namaValue)) {
            errors.push('Nama hanya boleh mengandung huruf, spasi, dan titik');
            isValid = false;
        }

        // Email: harus mengandung domain dengan TLD (minimal .com)
        const email = document.getElementById('email');
        const emailValue = email.value.trim();
        if (emailValue === '') {
            errors.push('Email tidak boleh kosong');
            isValid = false;
        } else if (!/^[^\s@]+@([^\s@]+\.)+[^\s@]+$/.test(emailValue)) {
            errors.push('Email tidak valid (harus mengandung domain seperti .com, .co.id, dll)');
            isValid = false;
        }

        // Password: min 8 karakter
        const password = document.getElementById('password');
        const passwordValue = password.value;
        if (passwordValue === '') {
            errors.push('Password tidak boleh kosong');
            isValid = false;
        } else if (passwordValue.length < 8) {
            errors.push('Password minimal 8 karakter');
            isValid = false;
        }

        // Konfirmasi password
        const passwordConfirmation = document.getElementById('password_confirmation');
        if (passwordConfirmation.value !== passwordValue) {
            errors.push('Konfirmasi password tidak cocok');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert(errors.join('\n'));
        }
    });
</script>
@endpush

@endsection
@extends('layouts.backend')

@section('title', 'Dashboard User')

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Dashboard User</h3>
                </div>
                <div class="card-body">
                    <p>Selamat datang, <strong>{{ Auth::guard('users')->user()->nama }}</strong>!</p>
                    <p>Anda login sebagai user biasa.</p>
                    <hr>
                    <a href="{{ route('user.riwayat') }}" class="btn btn-info">Lihat Riwayat Pencarian Saya</a>
                    <a href="{{ route('public.map') }}" class="btn btn-success">Cari Rute TransJakarta</a>
                    <a href="{{ route('frontend.explore') }}" class="btn btn-success">
                        <i class="fas fa-globe"></i> Menu Utama
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

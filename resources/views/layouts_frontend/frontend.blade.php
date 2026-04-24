<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>TransJakarta - Sistem Transportasi Jakarta</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link rel="stylesheet" href="{{ asset('travela-1.0.0/lib/owlcarousel/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('travela-1.0.0/lib/lightbox/css/lightbox.min.css') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link rel="stylesheet" href="{{ asset('travela-1.0.0/css/bootstrap.min.css') }}">

    <!-- Template Stylesheet -->
    <link rel="stylesheet" href="{{ asset('travela-1.0.0/css/style.css') }}">

    @stack('styles')

    <!-- Customized Bootstrap Stylesheet -->
    <style>
        :root {
            --primary: #13357B;
            --light: #F3F6F9;
            --dark: #1C2E41;
        }

        body {
            font-family: 'Roboto', sans-serif;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Jost', sans-serif;
        }

        .bg-primary {
            background-color: var(--primary) !important;
        }

        .text-primary {
            color: var(--primary) !important;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #0e2a62;
            border-color: #0e2a62;
        }

        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* Spinner */
        #spinner {
            opacity: 0;
            visibility: hidden;
            transition: opacity .5s ease-out, visibility 0s linear .5s;
            z-index: 99999;
        }

        #spinner.show {
            transition: opacity .5s ease-out, visibility 0s linear 0s;
            visibility: visible;
            opacity: 1;
        }

        /* Carousel */
        .carousel-header {
            margin-top: -1px;
        }

        .carousel-caption {
            bottom: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 100%);
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            width: 50px;
            height: 50px;
        }

        /* Booking */
        .booking {
            background: linear-gradient(rgba(19, 53, 123, 0.8), rgba(19, 53, 123, 0.8)),
            url('{{ asset("travela-1.0.0/img/tour-booking-bg.jpg") }}');
            background-size: cover;
            background-position: center;
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99;
        }

        .table_component {
            overflow: auto;
            width: 100%;
        }

        .table_component table {
            border: 1px solid #dededf;
            height: 100%;
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border-spacing: 1px;
            text-align: left;
        }

        .table_component th {
            border: 1px solid #dededf;
            background-color: #eceff1;
            color: #000000;
            padding: 5px;
        }

        .table_component td {
            border: 1px solid #dededf;
            background-color: #ffffff;
            color: #000000;
            padding: 5px;
        }

        .nav-pills .nav-link.active {
            background-color: var(--primary) !important;
        }

        .btn-hover {
            transition: 0.3s;
        }

        .btn-hover:hover {
            letter-spacing: 2px;
        }



        /* Styling Agar Mirip Web Promotion */
        /* Container carousel */
        #PromoCarousel {
            max-width: 1100px;
            margin: 0 auto;
        }

        /* Smooth animasi */
        .carousel-item {
            transition: transform 0.6s ease;
        }

        /* Image fit */
        .object-fit-cover {
            object-fit: cover;
        }

        /* PANAH */
        .custom-nav {
            width: 45px;
            height: 45px;
            background-color: #000;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.7;
        }

        /* 👉 INI YANG BIKIN KELUAR */
        .carousel-control-prev {
            left: -60px;
        }

        .carousel-control-next {
            right: -60px;
        }

        .custom-nav:hover {
            opacity: 1;
            background-color: #0d6efd;
        }

        /* ukuran icon */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            width: 18px;
            height: 18px;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            .carousel-control-prev {
                left: 10px;
            }

            .carousel-control-next {
                right: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid bg-primary px-5 d-none d-lg-block">
        <div class="row gx-0">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-twitter fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-facebook-f fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-linkedin-in fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-instagram fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="#"><i class="fab fa-youtube fw-normal"></i></a>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-light" data-bs-toggle="dropdown"><small><i class="fa fa-home me-2"></i> My Dashboard</small></a>
                        <div class="dropdown-menu rounded">
                            <a href="{{ url('/admin/dashboard') }}" class="dropdown-item"><i class="fas fa-tachometer-alt me-2"></i> Admin Dashboard</a>
                            <a href="#" class="dropdown-item" data-scroll-to="kontak">
                                <i class="fas fa-user-alt me-2"></i> End Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar & Hero Start -->
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
            <a href="{{ url('/frontend') }}" class="navbar-brand p-0">
                <h1 class="m-0"><i class="fa fa-map-marker-alt me-3"></i>TransJakarta</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="{{ url('/frontend') }}" class="nav-item nav-link active">Home</a>
                    <a href="#about" class="nav-item nav-link">About</a>
                    <a href="#statistik" class="nav-item nav-link">Informasi & Statistik</a>
                    <a href="#destinasi" class="nav-item nav-link">Objek Wisata</a>
                    <a href="#galeri" class="nav-item nav-link">Galeri Foto</a>
                    <a href="#kontak" class="nav-item nav-link">Kontak Kami</a>
                </div>
                <!-- projek 4 (sekarang) -->
                <!-- <a href="{{ route('admin.transjakarta.map') }}" target="_blank" class="btn btn-primary rounded-pill py-2 px-4 ms-lg-4">Rute Transjakarta</a> -->
                <a href="{{ route('admin.transjakarta.map') }}" class="btn btn-primary rounded-pill py-2 px-4 ms-lg-4">Rute Transjakarta</a>

                <!-- versi projek 5 -->
                <!-- <a href="{{-- url('/peta-v2') --}}" target="_blank" class="btn btn-primary rounded-pill py-2 px-4 ms-lg-4">Rute Transjakarta</a> -->
            </div>
        </nav>

        <!-- Carousel Start -->
        <div class="carousel-header">
            <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-bs-target="#carouselId" data-bs-slide-to="0" class="active"></li>
                    <li data-bs-target="#carouselId" data-bs-slide-to="1"></li>
                    <li data-bs-target="#carouselId" data-bs-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active">
                        <img src="{{ asset('travela-1.0.0/img/foto_tj-1.jpg') }}" class="img-fluid w-100" alt="Image" style="height: 600px; object-fit: cover;">
                        <div class="carousel-caption">
                            <div class="p-3" style="max-width: 900px;">
                                <h1 class="display-2 text-capitalize text-white mb-4">Keliling Jakarta? Naik TransJakarta Saja!</h1>
                                <div class="d-flex align-items-center justify-content-center">
                                    <a class="btn-hover-bg btn btn-primary rounded-pill text-white py-3 px-5" href="{{ route('admin.transjakarta.map') }}">Coba Rute Sekarang</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('travela-1.0.0/img/foto_tj-2.jpeg') }}" class="img-fluid w-100" alt="Image" style="height: 600px; object-fit: cover;">
                        <div class="carousel-caption">
                            <div class="p-3" style="max-width: 900px;">
                                <h1 class="display-2 text-capitalize text-white mb-4">Rute Terbaik, Liburan Makin Asyik.</h1>
                                <div class="d-flex align-items-center justify-content-center">
                                    <!-- <a class="btn-hover-bg btn btn-primary rounded-pill text-white py-3 px-5" href="{{ url('/peta-v2') }}" target="_blank">Coba Rute Sekarang</a> -->
                                    <a class="btn-hover-bg btn btn-primary rounded-pill text-white py-3 px-5" href="{{ route('admin.transjakarta.map') }}">Coba Rute Sekarang</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('travela-1.0.0/img/foto_tj-3.jpeg') }}" class="img-fluid w-100" alt="Image" style="height: 600px; object-fit: cover;">
                        <div class="carousel-caption">
                            <div class="p-3" style="max-width: 900px;">
                                <h1 class="display-2 text-capitalize text-white mb-4">Pintar Berkendara, Puas Berwisata.</h1>
                                <div class="d-flex align-items-center justify-content-center">
                                    <!-- <a class="btn-hover-bg btn btn-primary rounded-pill text-white py-3 px-5" href="{{ url('/peta-v2') }}" target="_blank">Coba Rute Sekarang</a> -->
                                    <a class="btn-hover-bg btn btn-primary rounded-pill text-white py-3 px-5" href="{{ route('admin.transjakarta.map') }}">Coba Rute Sekarang</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon btn bg-primary" aria-hidden="false"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                    <span class="carousel-control-next-icon btn bg-primary" aria-hidden="false"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <!-- Carousel End -->
    </div>

    <!-- Search Bar Start -->
    <div class="container-fluid search-bar position-relative" style="top: -50%; transform: translateY(-50%);">
        <div class="container">
            <div class="position-relative rounded-pill w-100 mx-auto p-4 text-center"
                style="background: rgba(19, 53, 123, 0.85); backdrop-filter: blur(2px); max-width: 700px;">

                <!-- Tombol Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="btn btn-light rounded-pill py-2 px-4"
                    style="font-weight: 600; color: #13357B;">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard Admin
                </a>

                <!-- Pemisah -->

                <!-- <div class="rounded-pill py-3 px-4"
                    style="background: rgba(255,255,255,0.9);">
                    <span style="color: #1a2b4e; font-weight: 500; letter-spacing: 0.3px;">
                        Cari tempat wisata
                    </span>
                </div> -->

            </div>
        </div>
    </div>
    <!-- Search Bar End -->

    <!-- About Start -->
    <div id="about" class="container-fluid about py-5">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <div class="h-100" style="border: 50px solid; border-color: transparent #13357B transparent #13357B;">
                        <img src="{{ asset('travela-1.0.0/img/about-tj.png') }}" class="img-fluid w-100 h-100" alt="">
                    </div>
                </div>
                <div class="col-lg-7">
                    <h5 class="section-about-title pe-3">Tentang Kami</h5>
                    <h1 class="mb-4">{{ $about->judul }}</h1>
                    <p class="mb-4">{{ $about->deskripsi }}</p>
                    <!-- <p class="mb-4">{{ $about->keterangan }}</p> -->
                    @php
                    $keteranganList = preg_split('/[\r\n;]+/', $about->keterangan);
                    @endphp

                    <div class="row gy-2 gx-4 mb-4">
                        @foreach ($keteranganList as $item)
                        @if(trim($item) != '')
                        <div class="col-sm-6">
                            <p class="mb-0">
                                <i class="fa fa-arrow-right text-primary me-2"></i>
                                {{ trim($item) }}
                            </p>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    <a class="btn btn-primary rounded-pill py-3 px-5 mt-2" href="#">Read More</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Statistik Start -->
    <div id="statistik" class="container-fluid destination py-5">
        <div class="container py-5">
            <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                <h5 class="section-title px-3">Informasi & Statistik</h5>
                <h1 class="mb-4">Data Transjakarta</h1>
                <p class="mb-0">Berikut adalah data statistik lengkap mengenai layanan Transjakarta yang terus berkembang untuk memenuhi kebutuhan transportasi masyarakat Jakarta.</p>
            </div>
            <table class="table table-bordered" style="border: 2px solid #13357c;">
                <thead style="border: 2px solid #13357c;">
                    <tr>
                        <th>Jenis Data</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody style="border: 2px solid #13357c;">
                    @forelse ($statistik as $item)
                    <tr>
                        <td>{{ $item->jenis_data }}</td>
                        <td><strong>{{ $item->jumlah }}</strong></td>
                        <td>{{ $item->keterangan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Data belum tersedia</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- <div class="table_component" role="region" tabindex="0"> </div> -->
        </div>
    </div>
    <!-- Statistik End -->

    <!-- Destination Start -->
    <div id="destinasi" class="container-fluid destination py-5">
        <div class="container py-5">
            <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                <h5 class="section-title px-3">Destinasi</h5>
                <h1 class="mb-0">Objek Wisata Terdekat</h1>
            </div>

            @php use Illuminate\Support\Str; @endphp

            <div class="tab-class text-center">

                {{-- NAV --}}
                <ul class="nav nav-pills d-inline-flex justify-content-center mb-5">

                    <li class="nav-item">
                        <a class="d-flex mx-3 py-2 border border-primary bg-light rounded-pill active"
                            data-bs-toggle="pill" href="#tab-all">
                            <span class="text-dark" style="width: 150px;">Semua</span>
                        </a>
                    </li>

                    @foreach ($destinasi as $kategori => $items)
                    <li class="nav-item">
                        <a class="d-flex mx-3 py-2 border border-primary bg-light rounded-pill"
                            data-bs-toggle="pill"
                            href="#tab-{{ Str::slug($kategori) }}">
                            <span class="text-dark" style="width: 150px;">
                                {{ $kategori }}
                            </span>
                        </a>
                    </li>
                    @endforeach

                </ul>

                {{-- CONTENT --}}
                <div class="tab-content">

                    {{-- TAB SEMUA --}}
                    <div id="tab-all" class="tab-pane fade show active p-0">
                        <div class="row g-4">

                            @forelse ($destinasi->flatten() as $item)
                            <div class="col-lg-4">
                                <div class="destination-img">

                                    <img class="img-fluid rounded w-100"
                                        src="{{ file_exists(public_path('upload/destinasi/' . $item->gambar)) 
                                                ? asset('upload/destinasi/' . $item->gambar) 
                                                : asset('travela-1.0.0/img/' . $item->gambar) }}">

                                    <div class="destination-overlay p-4">
                                        <a href="#" class="btn btn-primary">Cek Rute</a>
                                        <h4 class="text-white mt-3">{{ $item->nama }}</h4>
                                    </div>

                                    <div class="search-icon">
                                        <a href="{{ $item->gambar ? asset('upload/destinasi/' . $item->gambar) : '#' }}">
                                            <i class="fa fa-plus-square"></i>
                                        </a>
                                    </div>

                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-center">
                                <p>Data destinasi belum tersedia</p>
                            </div>
                            @endforelse

                        </div>
                    </div>

                    {{-- TAB PER KATEGORI --}}
                    @foreach ($destinasi as $kategori => $items)
                    <div id="tab-{{ Str::slug($kategori) }}" class="tab-pane fade p-0">
                        <div class="row g-4">

                            @forelse ($items as $item)
                            <div class="col-lg-4">
                                <div class="destination-img">

                                    <img class="img-fluid rounded w-100"
                                        src="{{ file_exists(public_path('upload/destinasi/' . $item->gambar)) 
                                                ? asset('upload/destinasi/' . $item->gambar) 
                                                : asset('travela-1.0.0/img/' . $item->gambar) }}">

                                    <div class="destination-overlay p-4">
                                        <a href="#" class="btn btn-primary">Cek Rute</a>
                                        <h4 class="text-white mt-3">{{ $item->nama }}</h4>
                                    </div>

                                    <div class="search-icon">
                                        <a href="{{ $item->gambar ? asset('upload/destinasi/' . $item->gambar) : '#' }}">
                                            <i class="fa fa-plus-square"></i>
                                        </a>
                                    </div>

                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-center">
                                <p>Tidak ada data kategori {{ $kategori }}</p>
                            </div>
                            @endforelse

                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
    <!-- Destination End -->

    <!-- Gallery Start -->
    <div id="galeri" class="container-fluid gallery py-5 my-5">
        <div class="mx-auto text-center mb-5" style="max-width: 900px;">
            <h5 class="section-title px-3">Galeri Kita</h5>
            <h1 class="mb-4">Galeri Foto Transjakarta</h1>
            <p class="mb-0">Dokumentasi kegiatan dan layanan Transjakarta dalam melayani masyarakat Jakarta.</p>
        </div>

        <div class="tab-class text-center">

            {{-- NAV --}}
            <ul class="nav nav-pills d-inline-flex justify-content-center mb-5">

                <li class="nav-item">
                    <a class="d-flex mx-3 py-2 border border-primary bg-light rounded-pill active"
                        data-bs-toggle="pill" href="#galeri-all">
                        <span class="text-dark" style="width:150px;">Semua</span>
                    </a>
                </li>

                @foreach ($galeri as $kategori => $items)
                <li class="nav-item">
                    <a class="d-flex mx-3 py-2 border border-primary bg-light rounded-pill"
                        data-bs-toggle="pill"
                        href="#galeri-{{ \Illuminate\Support\Str::slug($kategori) }}">
                        <span class="text-dark" style="width:150px;">
                            {{ $kategori }}
                        </span>
                    </a>
                </li>
                @endforeach

            </ul>

            {{-- CONTENT --}}
            <div class="tab-content">

                {{-- SEMUA --}}
                <div id="galeri-all" class="tab-pane fade show active p-0">
                    <div class="row g-2">

                        @forelse ($galeri->flatten() as $item)
                        <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                            <div class="gallery-item h-100">

                                <img
                                    src="{{ file_exists(public_path('upload/galeri/' . $item->gambar)) 
                                        ? asset('upload/galeri/' . $item->gambar) 
                                        : asset('travela-1.0.0/img/' . $item->gambar) }}"
                                    class="img-fluid w-100 h-100 rounded"
                                    alt="Image">

                                <div class="gallery-content">
                                    <div class="gallery-info">
                                        <h5 class="text-white text-uppercase mb-2">
                                            {{ $item->judul }}
                                        </h5>
                                    </div>
                                </div>

                                <div class="gallery-plus-icon">
                                    <a href="{{ asset('upload/galeri/' . $item->gambar) }}"
                                        data-lightbox="gallery">
                                        <i class="fas fa-plus fa-2x text-white"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center">
                            <p>Data galeri belum tersedia</p>
                        </div>
                        @endforelse

                    </div>
                </div>

                {{-- PER KATEGORI --}}
                @foreach ($galeri as $kategori => $items)
                <div id="galeri-{{ \Illuminate\Support\Str::slug($kategori) }}" class="tab-pane fade p-0">
                    <div class="row g-2">

                        @forelse ($items as $item)
                        <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                            <div class="gallery-item h-100">

                                <img
                                    src="{{ file_exists(public_path('upload/galeri/' . $item->gambar)) 
                                        ? asset('upload/galeri/' . $item->gambar) 
                                        : asset('travela-1.0.0/img/' . $item->gambar) }}"
                                    class="img-fluid w-100 h-100 rounded"
                                    alt="Image">

                                <div class="gallery-content">
                                    <div class="gallery-info">
                                        <h5 class="text-white text-uppercase mb-2">
                                            {{ $item->judul }}
                                        </h5>
                                    </div>
                                </div>

                                <div class="gallery-plus-icon">
                                    <a href="{{ asset('upload/galeri/' . $item->gambar) }}"
                                        data-lightbox="gallery">
                                        <i class="fas fa-plus fa-2x text-white"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center">
                            <p>Tidak ada data kategori {{ $kategori }}</p>
                        </div>
                        @endforelse

                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
    <!-- Gallery End -->

    <!-- Kolaborasi Start -->
    <section id="promotions-carousel" class="container-fluid py-5 bg-light">
        <div class="container py-5">

            <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                <h5 class="section-title px-3">Kolaborasi</h5>
                <h1 class="mb-4">Kolaborasi & Kampanye</h1>
            </div>

            <div class="mx-auto mt-3 mb-4" style="width: 50px; height: 3px; background-color: #0d6efd;"></div>

            <!-- WRAPPER -->
            <div class="carousel-wrapper">

                <div id="PromoCarousel" class="carousel slide shadow-sm" data-bs-ride="carousel">

                    <div class="carousel-inner rounded-4 overflow-hidden shadow">

                        @foreach ($kolaborasi as $key => $item)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                            <div class="row g-0 bg-white align-items-center">

                                {{-- GAMBAR --}}
                                <div class="col-md-7">
                                    <div style="height: 450px;">
                                        <img src="{{ file_exists(public_path('upload/kolaborasi/' . $item->gambar)) 
                            ? asset('upload/kolaborasi/' . $item->gambar) 
                            : asset('travela-1.0.0/img/' . $item->gambar) }}"
                                            class="w-100 h-100 object-fit-cover">
                                    </div>
                                </div>

                                {{-- TEXT --}}
                                <div class="col-md-5 p-5">
                                    <span class="badge bg-primary mb-3 px-3 py-2 text-uppercase">
                                        {{ $item->kategori }}
                                    </span>

                                    <h2 class="fw-bold mb-4">
                                        {{ $item->judul }}
                                    </h2>

                                    <p class="text-muted mb-4">
                                        {{ $item->deskripsi ?? '-' }}
                                    </p>

                                    {{-- optional --}}
                                    @if($item->link)
                                    <a href="{{ $item->link }}" target="_blank"
                                        class="btn btn-outline-primary px-4 py-2 text-uppercase fw-bold">
                                        Read More
                                    </a>
                                    @endif
                                </div>

                            </div>
                        </div>
                        @endforeach

                    </div>

                    <div class="carousel-inner">
                        {{-- loop item --}}
                    </div>

                    <div class="carousel-indicators mt-4">
                        @foreach ($kolaborasi as $key => $item)
                        <button type="button"
                            data-bs-target="#hotelPromoCarousel"
                            data-bs-slide-to="{{ $key }}"
                            class="{{ $key == 0 ? 'active' : '' }}">
                        </button>
                        @endforeach
                    </div>

                    {{-- PANAH --}}
                    <button class="carousel-control-prev custom-nav" type="button" data-bs-target="#PromoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>

                    <button class="carousel-control-next custom-nav" type="button" data-bs-target="#PromoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>

                </div>

            </div>

            <div class="mx-auto mt-4" style="width: 50px; height: 3px; background-color: #0d6efd;"></div>

        </div>
    </section>
    <!-- Kolaborasi End -->

    <!-- Footer Start -->
    <div id="kontak" class="container-fluid footer py-5">
        <div class="container py-5">
            <div class="row g-5">

                {{-- INFO --}}
                <div class="col-lg-4">
                    <h4 class="text-white mb-3">Transjakarta</h4>
                    <p class="text-white">
                        Sistem transportasi publik modern yang melayani masyarakat Jakarta
                        dengan aman, nyaman, dan terintegrasi.
                    </p>
                </div>

                {{-- KONTAK --}}
                <div class="col-lg-4">
                    <h4 class="text-white mb-3">Kontak</h4>
                    <p><i class="fas fa-map-marker-alt me-2"></i> Jakarta, Indonesia</p>
                    <p><i class="fas fa-envelope me-2"></i> info@transjakarta.co.id</p>
                    <p><i class="fas fa-phone me-2"></i> 1500-102</p>
                </div>

                {{-- PARTNER --}}
                <div class="col-lg-4">
                    <h4 class="text-white mb-3">Mitra</h4>
                    <span class="badge bg-primary p-2 m-1">Dishub DKI</span>
                    <span class="badge bg-primary p-2 m-1">Kemenhub RI</span>
                    <span class="badge bg-primary p-2 m-1">Pemprov DKI</span>
                    <span class="badge bg-primary p-2 m-1">Jaklingko</span>
                </div>

            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright text-body py-4">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-end mb-md-0">
                    <i class="fas fa-copyright me-2"></i><a class="text-white" href="#">TransJakarta</a>, All right reserved.
                </div>
                <div class="col-md-6 text-center text-md-start">
                    Designed By <a class="text-white" href="#">Transportasi Jakarta Team</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-primary-outline-0 btn-md-square back-to-top"><i class="fa fa-arrow-up"></i></a>

    <!-- Script universal (dibungkus DOMContentLoaded) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-scroll-to]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-scroll-to');
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('travela-1.0.0/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('travela-1.0.0/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('travela-1.0.0/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('travela-1.0.0/lib/lightbox/js/lightbox.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('travela-1.0.0/js/main.js') }}"></script>

    @stack('scripts')

    <!-- Spinner Script -->
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('#spinner').removeClass('show');
            }, 500);
        });
    </script>
</body>

</html>
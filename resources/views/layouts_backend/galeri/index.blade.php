@extends('layouts.backend')
@section('nav-galeri', 'active')
@section('content')

<div class="row">
    <div class="col-sm-12 mt-4">
        <div class="callout callout-info">
            <span class="text-dark">
                <i class="fa-solid fa-map mr-2"></i>Data Galeri
            </span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-outline card-info">
            <div class="card-header">
                <a href="{{ route('galeri.create') }}" class="btn btn-success">Tambah Data</a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Aksi</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Gambar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('galeri.edit', $item->id_galeri) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="{{ route('galeri.confirmDelete', $item->id_galeri) }}" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                                <td>{{ $item->judul }}</td>
                                <td>{{ $item->kategori }}</td>
                                <td>
                                    <img
                                        src="{{ file_exists(public_path('upload/galeri/' . $item->gambar)) 
                                            ? asset('upload/galeri/' . $item->gambar) 
                                            : asset('travela-1.0.0/img/' . $item->gambar) }}"
                                        width="80">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
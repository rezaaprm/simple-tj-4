@extends('layouts.backend')
@section('nav-destinasi', 'active')
@section('content')

    <div class="row">
        <div class="col-sm-12 mt-4">
            <div class="callout callout-info">
                <span class="text-dark">
                    <i class="fa-solid fa-map mr-2"></i>Data Destinasi
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <a href="{{ route('admin.destinasi.create') }}" class="btn btn-success">Tambah Data</a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">

                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Aksi</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Gambar</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>
                                            <a href="{{ route('admin.destinasi.edit', $item->id_destinasi) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>

                                            <a href="{{ route('admin.destinasi.confirmDelete', $item->id_destinasi) }}"
                                                class="btn btn-sm btn-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>

                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->kategori }}</td>

                                        <td>
                                            <img src="{{ asset('upload/destinasi/' . $item->gambar) }}" width="100">
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

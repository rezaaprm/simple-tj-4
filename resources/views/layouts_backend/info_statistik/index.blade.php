@extends('layouts.backend')
@section('nav-info_statistik', 'active')
@section('content')

    <div class="row">
        <div class="col-sm-12 mt-4">
            <div class="callout callout-info">
                <span class="text-dark">
                    <i class="fa-solid fa-book mr-2"></i>Info Statistik
                </span>
            </div>
            {{-- Callout --}}
        </div>
        {{-- Col --}}
    </div>
    {{-- Row --}}

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <a href="{{ route('admin.info_statistik.create') }}" class="btn btn-success">Tambah Data</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Aksi</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.info_statistik.edit', $item->id_info_statistik) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="{{ route('admin.info_statistik.confirmDelete', $item->id_info_statistik) }}" class="btn btn-danger btn-sm">Delete</a>
                                        </td>
                                        <td>{{ $item->jenis_data }}</td>
                                        <td>{{ $item->jumlah }}</td>
                                        <td>{{ $item->keterangan }}</td>
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

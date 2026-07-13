@extends('layouts.backend')

@section('title', 'Data POI')

@section('content')
    <div class="row mt-4">
        <div class="col-12 mt-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Point of Interest (POI)
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.poi.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Tambah POI
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($pois->count() > 0)
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pois as $index => $poi)
                                    <tr>
                                        <td>{{ ($pois->currentPage() - 1) * $pois->perPage() + $index + 1 }}</td>
                                        <td>{{ $poi->name }}</td>
                                        <td>{{ $poi->category }}</td>
                                        <td>{{ $poi->lat }}</td>
                                        <td>{{ $poi->lng }}</td>
                                        <td>
                                            <a href="{{ route('admin.poi.edit', $poi->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.poi.confirmDelete', $poi->id) }}" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $pois->links('layouts_backend.custom') }}
                        </div>
                    @else
                        <div class="alert alert-info">Belum ada data POI. Silakan tambah POI baru.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

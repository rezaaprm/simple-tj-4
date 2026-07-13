@extends('layouts.backend')

@section('title', 'Edit POI')

@section('content')
    <div class="row mt-4">
        <div class="col-sm-12 mt-4">
            <div class="callout callout-warning">
                <span class="text-dark">
                    <i class="fas fa-edit mr-2"></i> Edit Point of Interest (POI)
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <span class="text-muted">Ubah data POI. Koordinat akan divalidasi jarak dari halte terdekat.</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.poi.update', $data->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        @include('layouts_backend.poi.form_poi')
                        <div class="row">
                            <div class="col-sm-2">
                                <a href="{{ route('admin.poi.index') }}" class="btn btn-danger btn-block">Kembali</a>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success btn-block">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.backend')

@section('title', 'Hapus POI')

@section('content')
    <div class="row mt-4">
        <div class="col-sm-12 mt-4">
            <div class="callout callout-danger">
                <span class="text-dark">
                    <i class="fas fa-trash mr-2"></i> Hapus Point of Interest (POI)
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-outline card-danger">
                <div class="card-body">
                    <form action="{{ route('admin.poi.destroy', $data->id) }}" method="post">
                        @csrf
                        @method('DELETE')
                        @include('layouts_backend.poi.form_poi')
                        <div class="row">
                            <div class="col-sm-2">
                                <a href="{{ route('admin.poi.index') }}" class="btn btn-secondary btn-block">Kembali</a>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-danger btn-block">Hapus</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

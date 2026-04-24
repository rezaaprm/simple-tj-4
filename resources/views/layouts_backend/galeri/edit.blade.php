@extends('layouts.backend')
@section('nav-galeri', 'active')
@section('content')

<div class="row">
    <div class="col-sm-12 mt-4">
        <div class="callout callout-warning">
            <span class="text-dark">
                <i class="fa-solid fa-image mr-2"></i>Edit Galeri
            </span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <span class="text-muted">Edit data galeri</span>
            </div>

            <div class="card-body">
                <form action="{{ route('galeri.update', $data->id_galeri) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('layouts_frontend.templates.form_galeri')

                    <div class="row">
                        <div class="col-sm-2">
                            <a href="{{ route('galeri.index') }}" class="btn btn-danger btn-block">
                                Kembali
                            </a>
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-success btn-block">Update</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@extends('layouts.backend')
@section('nav-destinasi', 'active')
@section('content')

<div class="row">
    <div class="col-sm-12 mt-4">
        <div class="callout callout-warning">
            <span class="text-dark">
                <i class="fa-solid fa-map mr-2"></i>Edit Destinasi
            </span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-outline card-success">

            <div class="card-body">
                <form action="{{ route('destinasi.update', $data->id_destinasi) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('layouts_frontend.templates.form_destinasi')

                    <div class="row">
                        <div class="col-sm-2">
                            <a href="{{ route('destinasi.index') }}" class="btn btn-danger">Kembali</a>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

@endsection
@extends('layouts.backend')
@section('nav-destinasi', 'active')
@section('content')

<div class="row">
    <div class="col-sm-12 mt-4">
        <div class="callout callout-danger">
            <span class="text-dark">
                <i class="fa-solid fa-map mr-2"></i>Hapus Destinasi
            </span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-outline card-danger">

            <div class="card-body">
                <form action="{{ route('destinasi.destroy', $data->id_destinasi) }}" method="post">
                    @csrf
                    @method('DELETE')

                    @include('layouts_frontend.templates.form_destinasi')

                    <div class="row">
                        <div class="col-sm-2">
                            <a href="{{ route('destinasi.index') }}" class="btn btn-danger">Kembali</a>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

@endsection
@extends('layouts.backend')
@section('nav-galeri', 'active')
@section('content')

    <div class="row">
        <div class="col-sm-12 mt-4">
            <div class="callout callout-danger">
                <span class="text-dark">
                    <i class="fa-solid fa-map mr-2"></i>Hapus Galeri
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-outline card-danger">

                <div class="card-body">
                    <form action="{{ route('admin.galeri.destroy', $data->id_galeri) }}" method="post">
                        @csrf
                        @method('DELETE')

                        @include('layouts_frontend.templates.form_galeri')

                        <div class="row">
                            <div class="col-sm-2">
                                <a href="{{ route('admin.galeri.index') }}" class="btn btn-danger">Kembali</a>
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

@extends('layouts.backend')
@section('nav-info_statistik', 'active')
@section('content')

    <div class="row">
        <div class="col-sm-12 mt-4">
            <div class="callout callout-danger">
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
            <div class="card card-outline card-success">
                <div class="card-header">
                    <span class="text-muted">Harap isi data dengan benar</span>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.info_statistik.destroy', $data->id_info_statistik) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('DELETE')
                        @include('layouts_frontend.templates.form_info_statistik')

                        <div class="row">
                            <div class="col-sm-2">
                                <a href="{{ route('admin.info_statistik.index') }}" class="btn btn-block btn-danger">
                                    <i class="fas fa-undo mr-2"></i>Kembali
                                </a>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-block btn-success">
                                    <i class="fas fa-save mr-2"></i>Simpan
                                </button>
                            </div>
                            {{-- Col --}}
                        </div>
                        {{-- Row --}}
                    </form>
                </div>
            </div>
        </div>
        {{-- Col --}}
    </div>
    {{-- Row --}}

@endsection

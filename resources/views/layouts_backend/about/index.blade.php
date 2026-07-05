@extends('layouts.backend')
@section('nav-about', 'active')
@section('content')

    <div class="row">
        <div class="col-sm-12 mt-4">
            <div class="callout callout-info">
                <span class="text-dark">
                    <i class="fa-solid fa-book mr-2"></i>About Me
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
                    <a href="{{ route('admin.about.create') }}" class="btn btn-success">Tambah Data</a>
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
                                        <td class="text-nowrap">{{ number_format($loop->iteration) }}</td>
                                        <td class="text-nowrap">
                                            <a href="{{ route('admin.about.edit', $item->id_about) }}"
                                                class=" btn btn-sm btn-warning"><i class="fa-solid fa-edit"></i></a>
                                            <a href="{{ route('admin.about.confirmDelete', $item->id_about) }}"
                                                class=" btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                        <td>{{ $item->judul ?? '-' }}</td>
                                        <td>{{ $item->deskripsi ?? '-' }}</td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
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

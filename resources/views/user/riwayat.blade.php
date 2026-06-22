@extends('layouts.backend')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-header">
                    <h3>Riwayat Pencarian Saya</h3>
                </div>
                <div class="card-body">
                    @if ($logs->count())
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dari</th>
                                    <th>Ke</th>
                                    <th>Jarak (km)</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $log)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $log->halteAwal->stop_name ?? '-' }}</td>
                                        <td>{{ $log->halteTujuan->stop_name ?? '-' }}</td>
                                        <td>{{ number_format($log->total_jarak / 1000, 2) }}</td>
                                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $logs->links() }}
                    @else
                        <p class="text-muted">Belum ada riwayat pencarian.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

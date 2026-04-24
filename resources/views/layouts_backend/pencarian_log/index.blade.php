@extends('layouts.backend')

@section('title', 'Log Pencarian')

@section('content')
<div class="row mt-4">
    <div class="col-12 mt-4">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Log Pencarian Rute
                </h3>
            </div>
            <div class="card-body">
                @if(isset($logs) && $logs->count() > 0)
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Halte Awal</th>
                            <th>Halte Tujuan</th>
                            <th>Waktu (ms)</th>
                            <th>Node Dikunjungi</th>
                            <th>Jarak (km)</th>
                            <th>Waktu (menit)</th>
                            <th>Pindah</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $index => $log)
                        <tr>
                            <td>
                                <a href="javascript:void(0)"
                                    onclick="goToAlgoritma({{ $log->id }})"
                                    class="text-primary font-weight-bold"
                                    style="text-decoration: none; cursor: pointer;">
                                    {{ ($logs->currentPage() - 1) * $logs->perPage() + $index + 1 }}
                                </a>
                            </td>
                            <td>{{ $log->halteAwal->stop_name ?? 'Unknown' }}</td> {{-- stop_name, bukan nama_halte --}}
                            <td>{{ $log->halteTujuan->stop_name ?? 'Unknown' }}</td> {{-- stop_name, bukan nama_halte --}}
                            <td>{{ number_format($log->waktu_eksekusi_ms, 2) }}</td>
                            <td>{{ $log->node_dikunjungi }}</td>
                            <td>{{ number_format($log->total_jarak / 1000, 2) }}</td>
                            <td>{{ number_format($log->total_waktu / 60, 1) }}</td>
                            <td>{{ $log->total_pindah }}</td>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $logs->onEachSide(1)->links('layouts_backend.custom') }}
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Belum ada data log pencarian. Lakukan pencarian rute di halaman peta untuk melihat log.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function goToAlgoritma(logId) {
        window.location.href = '/admin/algoritma?log_id=' + logId;
    }
</script>
@endpush

@endsection
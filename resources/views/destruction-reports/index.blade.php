@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Destruction Reports</h4>
        <a href="{{ route('destruction-reports.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Buat Laporan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No. Laporan</th>
                            <th>Cabang</th>
                            <th>Tanggal</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th class="text-end">Total Biaya</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($destructionReports as $report)
                            <tr>
                                <td>{{ $report->report_number }}</td>
                                <td>{{ $report->branch->name ?? '-' }}</td>
                                <td>{{ optional($report->destruction_date)->format('Y-m-d') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($report->reason, 50) }}</td>
                                <td>
                                    <span class="badge {{ $report->status_badge }}">{{ ucfirst($report->status) }}</span>
                                </td>
                                <td class="text-end">{{ number_format($report->total_cost ?? 0, 2) }}</td>
                                <td class="d-flex gap-2">
                                    <a href="{{ route('destruction-reports.show', $report) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                    @if($report->status !== 'approved')
                                        <a href="{{ route('destruction-reports.edit', $report) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('destruction-reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Belum ada laporan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($destructionReports, 'links'))
            <div class="card-footer">{{ $destructionReports->links() }}</div>
        @endif
    </div>
</div>
@endsection

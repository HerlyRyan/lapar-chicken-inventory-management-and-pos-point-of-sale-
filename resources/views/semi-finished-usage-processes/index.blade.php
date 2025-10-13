@extends('layouts.app')

@section('title', 'Proses Penggunaan Bahan - Daftar')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Proses Penggunaan Bahan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Proses Penggunaan Bahan</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-list-ul mr-1"></i>
                Daftar Permintaan Siap Diproses
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('semi-finished-usage-processes.index') }}" class="form-inline mb-3">
                <div class="form-group mr-2">
                    <label for="status" class="mr-1">Status:</label>
                    <select name="status" id="status" class="form-control form-control-sm">
                        <option value="all" {{ ($statusFilter ?? request('status')) == 'all' ? 'selected' : '' }}>Disetujui & Sedang Diproses</option>
                        <option value="approved" {{ ($statusFilter ?? request('status')) == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="processing" {{ ($statusFilter ?? request('status')) == 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="completed" {{ ($statusFilter ?? request('status')) == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-secondary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Cabang</th>
                            <th>Tujuan</th>
                            <th>Tgl Permintaan</th>
                            <th>Tgl Dibutuhkan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td>{{ $req->request_number }}</td>
                                <td>{{ $req->requestingBranch->name }}</td>
                                <td>{{ Str::limit($req->purpose, 30) }}</td>
                                <td>{{ optional($req->requested_date)->format('d/m/Y') }}</td>
                                <td>{{ optional($req->required_date)->format('d/m/Y') ?? '-' }}</td>
                                <td>{!! $req->status_badge !!}</td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="{{ route('semi-finished-usage-processes.show', $req) }}">
                                        <i class="fas fa-tasks"></i> Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data untuk ditampilkan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

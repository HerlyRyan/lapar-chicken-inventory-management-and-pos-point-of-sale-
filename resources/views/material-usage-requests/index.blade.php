@extends('layouts.app')

@section('title', 'Daftar Permintaan Penggunaan Bahan')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Permintaan Penggunaan Bahan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Permintaan Penggunaan Bahan</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-list-alt mr-1"></i>
                Daftar Permintaan
            </div>
            <div>
                <a href="{{ (isset($currentBranchId) && $currentBranchId) ? route('semi-finished-usage-requests.create', ['branch_id' => $currentBranchId]) : route('semi-finished-usage-requests.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Buat Permintaan Baru
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('semi-finished-usage-requests.index') }}" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="status" class="mr-1">Status:</label>
                            <select name="status" id="status" class="form-control form-control-sm">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>

                        @if(auth()->user()->hasRole(['admin', 'super-admin']))
                            <div class="form-group mr-2">
                                <label for="branch_id" class="mr-1">Cabang:</label>
                                <select name="branch_id" id="branch_id" class="form-control form-control-sm">
                                    <option value="all">Semua Cabang</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-sm btn-secondary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nomor Permintaan</th>
                            <th>Cabang</th>
                            <th>Tujuan</th>
                            <th>Tanggal Permintaan</th>
                            <th>Tanggal Dibutuhkan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>{{ $request->request_number }}</td>
                                <td>{{ $request->requestingBranch->name }}</td>
                                <td>{{ Str::limit($request->purpose, 30) }}</td>
                                <td>{{ $request->requested_date->format('d/m/Y') }}</td>
                                <td>{{ $request->required_date ? $request->required_date->format('d/m/Y') : '-' }}</td>
                                <td>{!! $request->status_badge !!}</td>
                                <td>
                                    <a href="{{ route('semi-finished-usage-requests.show', $request) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data permintaan penggunaan bahan</td>
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

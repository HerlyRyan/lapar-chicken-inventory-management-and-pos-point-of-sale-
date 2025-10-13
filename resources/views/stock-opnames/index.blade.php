@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Stok Opname</h1>
        <a href="{{ route('stock-opnames.create') }}" class="btn btn-primary">Buat Opname Baru</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Nomor Opname</th>
                            <th>Status</th>
                            <th>Jenis Produk</th>
                            <th>Cabang</th>
                            <th>Ringkasan</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($opnames as $opname)
                        <tr>
                            <td>{{ $loop->iteration + ($opnames->currentPage() - 1) * $opnames->perPage() }}</td>
                            <td>{{ $opname->opname_number }}</td>
                            <td>
                                @if($opname->status === 'draft')
                                    <span class="badge bg-warning">Draft</span>
                                @else
                                    <span class="badge bg-success">Disubmit</span>
                                @endif
                            </td>
                            <td>{{ $opname->product_type === 'raw' ? 'Bahan Baku' : 'Setengah Jadi' }}</td>
                            <td>{{ $opname->branch->name ?? '-' }}</td>
                            <td>
                                Cocok: {{ $opname->matched_count }}/{{ $opname->total_items }}
                                ({{ number_format((float)$opname->match_percentage, 2) }}%)
                            </td>
                            <td>{{ $opname->created_at->format('d M Y H:i') }}</td>
                            <td>
                                @if($opname->status === 'draft')
                                    <a href="{{ route('stock-opnames.edit', $opname) }}" class="btn btn-sm btn-outline-primary">Lanjutkan</a>
                                @else
                                    <a href="{{ route('stock-opnames.show', $opname) }}" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Belum ada data stok opname.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $opnames->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

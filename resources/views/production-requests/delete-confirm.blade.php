@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Konfirmasi Hapus Pengajuan</h5>
        </div>
        <div class="card-body">
            <h5>Apakah Anda yakin ingin menghapus pengajuan ini?</h5>
            <p><strong>Kode:</strong> {{ $productionRequest->request_code }}</p>
            <p><strong>Tujuan:</strong> {{ $productionRequest->purpose }}</p>
            <p><strong>Total Biaya:</strong> Rp {{ number_format($productionRequest->total_raw_material_cost, 0, ',', '.') }}</p>
            
            <div class="mt-4">
                <form action="{{ route('production-requests.destroy', $productionRequest) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Pengajuan</button>
                    <a href="{{ route('production-requests.index') }}" class="btn btn-secondary ms-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

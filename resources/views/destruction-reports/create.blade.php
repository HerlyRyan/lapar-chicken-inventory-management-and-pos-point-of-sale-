@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Buat Laporan Pemusnahan</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('destruction-reports.store') }}" id="destruction-form">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Cabang</label>
                        <select name="branch_id" class="form-select" id="branch_id" onchange="onBranchChange(this)">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', optional($selectedBranch)->id ?? optional($currentBranch)->id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Pemusnahan</label>
                        <input type="date" class="form-control" name="destruction_date" value="{{ old('destruction_date', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Alasan</label>
                        <input type="text" class="form-control" name="reason" value="{{ old('reason') }}" placeholder="Contoh: Rusak/Kedaluwarsa">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Opsional">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <hr>

                @php($oldItems = old('items', []))
                @include('destruction-reports.partials.items-form', [
                    'oldItems' => $oldItems,
                    'finishedProducts' => $finishedProducts,
                    'semiFinishedProducts' => $semiFinishedProducts,
                    'currentBranch' => $currentBranch ?? null,
                    'selectedBranch' => $selectedBranch ?? null,
                ])

                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('destruction-reports.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

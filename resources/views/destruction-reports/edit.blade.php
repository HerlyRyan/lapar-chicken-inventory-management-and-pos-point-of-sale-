@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Laporan Pemusnahan</h4>
        <a href="{{ route('destruction-reports.index') }}" class="btn btn-light">Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
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
            <form method="POST" action="{{ route('destruction-reports.update', $destructionReport) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Cabang</label>
                        <select name="branch_id" class="form-select" id="branch_id" onchange="onBranchChange(this)">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', optional($selectedBranch)->id ?? $destructionReport->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Pemusnahan</label>
                        <input type="date" class="form-control" name="destruction_date" value="{{ old('destruction_date', optional($destructionReport->destruction_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Alasan</label>
                        <input type="text" class="form-control" name="reason" value="{{ old('reason', $destructionReport->reason) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="notes" rows="2">{{ old('notes', $destructionReport->notes) }}</textarea>
                    </div>
                </div>

                <hr>

                @php
                    $oldItems = old('items', $destructionReport->destructionReportItems->map(function($it){
                        if ($it->item_type === 'semi_finished_product') {
                            return [
                                'semi_finished_product_id' => $it->item_id,
                                'quantity' => $it->quantity,
                                'condition_description' => $it->condition_description,
                            ];
                        } else {
                            return [
                                'finished_product_id' => $it->item_id,
                                'quantity' => $it->quantity,
                                'condition_description' => $it->condition_description,
                            ];
                        }
                    })->toArray());
                @endphp
                @include('destruction-reports.partials.items-form', [
                    'oldItems' => $oldItems,
                    'finishedProducts' => $finishedProducts,
                    'semiFinishedProducts' => $semiFinishedProducts,
                    'currentBranch' => $currentBranch ?? null,
                    'selectedBranch' => $selectedBranch ?? null,
                ])

                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('destruction-reports.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

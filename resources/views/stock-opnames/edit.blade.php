@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Edit Draft Stok Opname</h1>
        <a href="{{ route('stock-opnames.index') }}" class="btn btn-light">Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">Periksa kembali input Anda.</div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3"><strong>Nomor:</strong> {{ $stockOpname->opname_number }}</div>
                <div class="col-md-3"><strong>Jenis:</strong> {{ $stockOpname->product_type === 'raw' ? 'Bahan Baku' : 'Setengah Jadi' }}</div>
                <div class="col-md-3"><strong>Cabang:</strong> {{ $stockOpname->branch->name ?? '-' }}</div>
                <div class="col-md-3"><strong>Status:</strong> <span class="badge bg-warning">Draft</span></div>
            </div>
        </div>
    </div>

    <form id="opnameForm" action="{{ route('stock-opnames.update', $stockOpname) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle table-hover mb-0" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th class="text-end">Stok Sistem</th>
                                <th class="text-end">Stok Nyata</th>
                                <th class="text-end">Selisih</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($stockOpname->items as $item)
                            <tr data-item-id="{{ $item->id }}">
                                <td>{{ $item->item_code }}</td>
                                <td>
                                    {{ $item->item_name }}
                                    @if($item->unit_abbr)
                                        <span class="text-muted">({{ $item->unit_abbr }})</span>
                                    @endif
                                    <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                </td>
                                <td class="text-end system-qty">{{ number_format((int)$item->system_quantity, 0) }}</td>
                                <td class="text-end" style="width: 160px;">
                                    <input type="number" step="1" min="0" class="form-control form-control-sm text-end real-input @error('items.'.$item->id.'.real_quantity') is-invalid @enderror" name="items[{{ $item->id }}][real_quantity]" value="{{ old('items.'.$item->id.'.real_quantity', (int)$item->real_quantity) }}">
                                    @error('items.'.$item->id.'.real_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-end diff-cell">{{ number_format((int)$item->difference, 0) }}</td>
                                <td class="status-cell">
                                    @php
                                        $status = $item->status;
                                        $badge = $status === 'matched' ? 'bg-success' : ($status === 'over' ? 'bg-info' : 'bg-danger');
                                        $label = $status === 'matched' ? 'Cocok' : ($status === 'over' ? 'Lebih' : 'Kurang');
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $label }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex flex-column flex-md-row gap-2 justify-content-between align-items-center">
                <div>
                    <label for="notes" class="form-label">Catatan</label>
                    <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $stockOpname->notes) }}</textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Simpan Draft</button>
                    <button type="submit" form="submitForm" class="btn btn-success">Submit Opname</button>
                    <div class="text-muted mt-1" style="font-size: 0.9rem;">Tip: Klik "Simpan Draft" sebelum submit untuk memastikan data terbaru tersimpan.</div>
                </div>
            </div>
        </div>
    </form>
    <form id="submitForm" action="{{ route('stock-opnames.submit', $stockOpname) }}" method="POST" class="d-none">
        @csrf
    </form>
</div>
@endsection

@push('scripts')
<script>
(function(){
  function recalcRow(tr){
    const sys = parseInt(tr.querySelector('.system-qty').textContent.replace(/,/g,''), 10) || 0;
    const input = tr.querySelector('.real-input');
    const real = parseInt(input.value, 10) || 0;
    const diff = real - sys;
    tr.querySelector('.diff-cell').textContent = String(diff);
    const statusCell = tr.querySelector('.status-cell');
    let badge = 'bg-success', label = 'Cocok';
    if (diff === 0) { badge = 'bg-success'; label = 'Cocok'; }
    else if (diff > 0) { badge = 'bg-info'; label = 'Lebih'; }
    else { badge = 'bg-danger'; label = 'Kurang'; }
    statusCell.innerHTML = `<span class="badge ${badge}">${label}</span>`;
  }
  document.querySelectorAll('#itemsTable tbody tr').forEach(function(tr){
    const input = tr.querySelector('.real-input');
    input.addEventListener('input', function(){ recalcRow(tr); });
    recalcRow(tr);
  });
})();
</script>
@endpush

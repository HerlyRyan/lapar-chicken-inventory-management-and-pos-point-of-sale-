@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Buat Stok Opname</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('stock-opnames.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="product_type" class="form-label">Jenis Produk</label>
                        <select name="product_type" id="product_type" class="form-select @error('product_type') is-invalid @enderror">
                            <option value="raw" {{ old('product_type') === 'raw' ? 'selected' : '' }}>Bahan Baku</option>
                            <option value="semi" {{ old('product_type') === 'semi' ? 'selected' : '' }}>Produk Setengah Jadi</option>
                        </select>
                        @error('product_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4" id="branch_wrapper" style="display: none;">
                        <label for="branch_id" class="form-label">Cabang</label>
                        <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">Catatan (opsional)</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('stock-opnames.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Buat Draft</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const productType = document.getElementById('product_type');
  const branchWrapper = document.getElementById('branch_wrapper');
  function toggleBranch(){
    branchWrapper.style.display = productType.value === 'semi' ? '' : 'none';
  }
  productType.addEventListener('change', toggleBranch);
  toggleBranch();
})();
</script>
@endpush

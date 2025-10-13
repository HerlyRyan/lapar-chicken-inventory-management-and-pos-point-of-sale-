@extends('layouts.app')

@section('title', 'Tambah Distribusi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-truck me-2"></i>Tambah Distribusi
        </h1>
        <p class="text-muted mb-0">Tambahkan distribusi produk ke cabang</p>
    </div>
    <a href="{{ route('distributions.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg">
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Form Tambah Distribusi
                </h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('distributions.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="distribution_number" class="form-label fw-semibold">
                                Nomor Distribusi <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="distribution_number" id="distribution_number" 
                                   class="form-control @error('distribution_number') is-invalid @enderror" 
                                   value="{{ old('distribution_number') }}" required placeholder="DST-001">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Nomor unik distribusi
                            </div>
                            @error('distribution_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="from_branch_id" class="form-label fw-semibold">
                                Cabang Asal <span class="text-danger">*</span>
                            </label>
                            <select name="from_branch_id" id="from_branch_id" class="form-select @error('from_branch_id') is-invalid @enderror" required>
                                <option value="">- Pilih Cabang Asal -</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('from_branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Pilih cabang asal distribusi
                            </div>
                            @error('from_branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="to_branch_id" class="form-label fw-semibold">
                                Cabang Tujuan <span class="text-danger">*</span>
                            </label>
                            <select name="to_branch_id" id="to_branch_id" class="form-select @error('to_branch_id') is-invalid @enderror" required>
                                <option value="">- Pilih Cabang Tujuan -</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('to_branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Pilih cabang tujuan distribusi
                            </div>
                            @error('to_branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="semi_finished_product_id" class="form-label fw-semibold">
                                Bahan Setengah Jadi <span class="text-danger">*</span>
                            </label>
                            <select name="semi_finished_product_id" id="semi_finished_product_id" class="form-select @error('semi_finished_product_id') is-invalid @enderror" required>
                                <option value="">- Pilih Bahan Setengah Jadi -</option>
                                @foreach($semiFinishedProducts as $product)
                                    <option value="{{ $product->id }}" {{ old('semi_finished_product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Pilih bahan setengah jadi yang akan didistribusikan
                            </div>
                            @error('semi_finished_product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="quantity" class="form-label fw-semibold">
                                Jumlah <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="quantity" id="quantity" 
                                   class="form-control @error('quantity') is-invalid @enderror" 
                                   value="{{ old('quantity') }}" required min="1" step="1">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Jumlah produk yang akan didistribusikan
                            </div>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="distribution_date" class="form-label fw-semibold">
                                Tanggal Distribusi <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="distribution_date" id="distribution_date" 
                                   class="form-control @error('distribution_date') is-invalid @enderror" 
                                   value="{{ old('distribution_date', date('Y-m-d')) }}" required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Tanggal distribusi dilakukan
                            </div>
                            @error('distribution_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_transit" {{ old('status') == 'in_transit' ? 'selected' : '' }}>Dalam Perjalanan</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Status distribusi saat ini
                            </div>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="notes" class="form-label fw-semibold">Catatan</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3" placeholder="Catatan distribusi (opsional)">{{ old('notes') }}</textarea>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Catatan tambahan distribusi (opsional)
                            </div>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: var(--primary-orange); opacity: 0.3;">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('distributions.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Branch Selector Component -->
@php
    // Optional flags (fallback false if not provided)
    $forDashboard = $forDashboard ?? false;
    $includeOverviewOption = $includeOverviewOption ?? false;
    $selectedBranch = $selectedBranch ?? null;
    $isSuperAdmin = (auth()->user()->is_super_admin ?? false) || (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('Super Admin'));

    // Determine selected option for dashboard mode
    $selectedId = null;
    if ($selectedBranch) {
        $selectedId = $selectedBranch->id;
    } elseif (session('selected_dashboard_branch')) {
        $selectedId = session('selected_dashboard_branch');
    }
@endphp
@if($isSuperAdmin && ($branches->count() > 1))
<div class="mb-3">
    <div class="card border-info">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-building text-info me-2"></i>
                <label for="branchSelector" class="form-label mb-0 me-3 fw-semibold">Pilih Cabang:</label>
                <select id="branchSelector" class="form-select form-select-sm" style="max-width: 380px;" onchange="switchBranch(this.value)">
                    @if($includeOverviewOption)
                        <option value="overview" {{ $selectedId ? '' : 'selected' }}>Semua Cabang (Overview)</option>
                    @endif
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (string)$selectedId === (string)$branch->id ? 'selected' : '' }}>
                            {{ $branch->name }} ({{ $branch->code }}) - {{ $branch->type === 'production' ? 'Produksi' : 'Cabang' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<script>
function switchBranch(branchId) {
    if (!branchId) return;
    // In dashboard mode: navigate using query param so the view updates without changing user context
    @if($forDashboard)
        if (branchId === 'overview') {
            window.location.href = '{{ route('dashboard') }}?clear_dashboard_branch=1';
        } else {
            window.location.href = '{{ route('dashboard') }}?branch_id=' + encodeURIComponent(branchId);
        }
        return;
    @endif

    // Default: switch user context via API then reload
    fetch('{{ route("switch-branch") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ branch_id: branchId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal mengganti cabang. Silakan coba lagi.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
}
</script>
@endif

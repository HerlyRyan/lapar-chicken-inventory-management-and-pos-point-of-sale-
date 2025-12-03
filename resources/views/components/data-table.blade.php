@props(['headers', 'data', 'actions' => null, 'searchable' => true, 'filterable' => false, 'filters' => []])

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        @if ($searchable || $filterable)
            <div class="card-header bg-light border-0 p-3">
                <div class="row align-items-center">
                    @if ($searchable)
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" name="q" class="form-control border-start-0"
                                        placeholder="Cari data..." value="{{ request('q') }}">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    @if ($filterable && count($filters) > 0)
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-end">
                                @foreach ($filters as $filter)
                                    <select name="{{ $filter['name'] }}" class="form-select form-select-sm"
                                        onchange="this.form.submit()">
                                        <option value="">{{ $filter['label'] }}</option>
                                        @foreach ($filter['options'] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ request($filter['name']) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="standard-table-container">
            <table class="standard-table table table-hover mb-0">
                <thead class="standard-table-header table-light">
                    <tr>
                        @foreach ($headers as $header)
                            <th class="fw-semibold text-dark border-0 py-3">{{ $header }}</th>
                        @endforeach
                        @if ($actions)
                            <th class="fw-semibold text-dark border-0 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: rgba(220, 38, 38, 0.05) !important;
    }

    .table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border-bottom: 2px solid var(--primary-red) !important;
    }

    .input-group-text {
        border-color: #dee2e6 !important;
    }

    .form-control:focus {
        border-color: var(--primary-red) !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25) !important;
    }
</style>

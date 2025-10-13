@props([
    'headers' => [], 
    'data' => null, 
    'actions' => true, 
    'searchable' => true, 
    'filterable' => false, 
    'filters' => [],
    'pagination' => null,
    'sortable' => false,
    'sortColumn' => null,
    'sortDirection' => 'asc'
])

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        @if($searchable || $filterable)
            <div class="card-header bg-light border-0 p-3 table-filter-form">
                <div class="row align-items-center">
                    @if($searchable)
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <div class="input-group input-group-filter">
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
                    
                    @if($filterable && count($filters) > 0)
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-end">
                                @foreach($filters as $filter)
                                    <select name="{{ $filter['name'] }}" class="form-select form-select-sm" 
                                            onchange="this.form.submit()">
                                        <option value="">{{ $filter['label'] }}</option>
                                        @foreach($filter['options'] as $value => $label)
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
                        @foreach($headers as $index => $header)
                            @php
                                $headerText = is_array($header) ? $header['text'] : $header;
                                $headerClass = is_array($header) && isset($header['class']) ? $header['class'] : '';
                                $headerWidth = is_array($header) && isset($header['width']) ? $header['width'] : '';
                                $headerSort = is_array($header) && isset($header['sort']) ? $header['sort'] : null;
                                $isSortable = $sortable && $headerSort;
                            @endphp
                            <th 
                                @if($headerWidth) width="{{ $headerWidth }}" @endif
                                class="{{ $headerClass }} {{ $isSortable ? 'sortable' : '' }}"
                                @if($isSortable)
                                    onclick="window.location='{{ request()->fullUrlWithQuery([
                                        'sort' => $headerSort,
                                        'direction' => ($sortColumn == $headerSort && $sortDirection == 'asc') ? 'desc' : 'asc'
                                    ]) }}'"
                                    style="cursor: pointer;"
                                @endif
                            >
                                {{ $headerText }}
                                
                                @if($isSortable)
                                    <span class="sort-icon {{ $sortColumn == $headerSort ? 'sort-active' : '' }}">
                                        @if($sortColumn == $headerSort)
                                            <i class="bi bi-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up"></i>
                                        @endif
                                    </span>
                                @endif
                            </th>
                        @endforeach
                        @if($actions)
                            <th class="text-center" width="15%">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
        
        @if($pagination)
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $pagination->firstItem() ?? 0 }} sampai {{ $pagination->lastItem() ?? 0 }} 
                        dari {{ $pagination->total() }} data
                        <span class="ms-2">
                            ({{ $pagination->perPage() }} data per halaman)
                        </span>
                    </div>
                    <div>
                        {{ $pagination->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

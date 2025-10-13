@props([
    'viewUrl' => '',
    'editUrl' => '',
    'deleteUrl' => '',
    'toggleUrl' => '',
    'deleteId' => '',
    'toggleId' => '',
    'isActive' => null,
    'showView' => true,
    'showEdit' => true,
    'showDelete' => true,
    'showToggle' => false,
    'itemName' => 'item'
])

<div class="d-flex">
    @if($showView && $viewUrl)
        <a href="{{ $viewUrl }}" class="btn btn-sm btn-outline-info me-1" data-bs-toggle="tooltip" title="Detail">
            <i class="bi bi-eye"></i>
        </a>
    @endif

    @if($showEdit && $editUrl)
        <a href="{{ $editUrl }}" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="tooltip" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
    @endif
    
    @if($showToggle && $toggleUrl && !is_null($isActive))
        <form action="{{ $toggleUrl }}" method="POST" class="d-inline me-1">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_active" value="{{ $isActive ? 0 : 1 }}">
            <button type="submit" class="btn btn-sm btn-outline-{{ $isActive ? 'success' : 'secondary' }}" data-bs-toggle="tooltip" 
                title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}">
                <i class="bi bi-{{ $isActive ? 'toggle-on' : 'toggle-off' }}"></i>
            </button>
        </form>
    @endif

    @if($showDelete && $deleteUrl)
        <button type="button" class="btn btn-sm btn-outline-danger" 
            onclick="confirmDelete('{{ $deleteUrl }}', '{{ $itemName }}')" 
            title="Hapus">
            <i class="bi bi-trash"></i>
        </button>
    @endif
</div>

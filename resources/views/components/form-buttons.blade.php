@props([
    'submitText' => 'Simpan',
    'cancelUrl' => null,
    'submitIcon' => null,
    'cancelIcon' => null,
])

<div class="form-group mt-4">
    <button type="submit" class="btn btn-primary">
        @if($submitIcon)
            <i class="ti ti-{{ $submitIcon }}"></i>
        @endif
        {{ $submitText }}
    </button>
    @if($cancelUrl)
        <a href="{{ $cancelUrl }}" class="btn btn-link text-secondary">
            @if($cancelIcon)
                <i class="ti ti-{{ $cancelIcon }}"></i>
            @endif
            Batal
        </a>
    @endif
</div>

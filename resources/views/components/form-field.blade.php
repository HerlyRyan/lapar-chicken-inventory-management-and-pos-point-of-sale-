@props([
    'name',
    'label',
    'value' => null,
    'type' => 'text',
    'placeholder' => '',
    'help' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autocomplete' => 'on',
    'pattern' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'options' => [],
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }} 
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    
    @if($type === 'textarea')
        <textarea 
            id="{{ $name }}" 
            name="{{ $name }}" 
            class="form-control @error($name) is-invalid @enderror" 
            placeholder="{{ $placeholder }}" 
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $attributes->except('options') }}
        >{{ $value }}</textarea>
    @elseif($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $attributes->except('options') }}
        >
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" {{ (string) $optionValue === (string) $value ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'checkbox')
        <div class="form-check">
            <input 
                type="checkbox" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                value="1" 
                class="form-check-input @error($name) is-invalid @enderror" 
                {{ $value ? 'checked' : '' }}
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $attributes->except('options') }}
            />
            <label class="form-check-label" for="{{ $name }}">
                {{ $help }}
            </label>
            @error($name)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @php $help = ''; @endphp
    @else
        <input 
            type="{{ $type }}" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ $value }}" 
            class="form-control @error($name) is-invalid @enderror" 
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $pattern ? 'pattern='.$pattern : '' }}
            {{ $min ? 'min='.$min : '' }}
            {{ $max ? 'max='.$max : '' }}
            {{ $step ? 'step='.$step : '' }}
            {{ $attributes->except('options') }}
        />
    @endif
    
    @if($help)
        <div class="form-text text-muted">{{ $help }}</div>
    @endif
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

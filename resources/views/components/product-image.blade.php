@props(['src', 'alt' => 'Product Image', 'class' => '', 'height' => '100'])

@php
    use App\Helpers\ImageHelper;
    $imageUrl = ImageHelper::getImageUrl($src);
    $placeholder = asset('img/placeholder-product.svg');
    $resolvedSrc = $imageUrl ?: $placeholder;
@endphp

<img
    src="{{ $resolvedSrc }}"
    alt="{{ $alt }}"
    class="img-thumbnail {{ $class }}"
    style="max-height: {{ $height }}px; object-fit: contain;"
    onerror="this.onerror=null; this.src='{{ $placeholder }}';"
>

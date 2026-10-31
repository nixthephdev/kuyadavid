@use('App\Support\ImageStore')
@props([
    'src',
    'alt' => '',
    'sizes' => '(min-width: 768px) 50vw, 100vw',
    'eager' => false,
])
@php
    [$width, $height] = ImageStore::dimensions($src);
    $smallWidth = $width ? (int) round($width * min(1, config('site.images.small') / max($width, $height))) : null;
@endphp
@if ($src)
    <img src="{{ ImageStore::url($src, 'sm') }}"
         srcset="{{ ImageStore::url($src, 'sm') }} {{ $smallWidth }}w, {{ ImageStore::url($src, 'lg') }} {{ $width }}w"
         sizes="{{ $sizes }}"
         alt="{{ $alt }}"
         @if ($width) width="{{ $width }}" height="{{ $height }}" @endif
         loading="{{ $eager ? 'eager' : 'lazy' }}"
         decoding="{{ $eager ? 'auto' : 'async' }}"
         @if ($eager) fetchpriority="high" @endif
         {{ $attributes }}>
@endif

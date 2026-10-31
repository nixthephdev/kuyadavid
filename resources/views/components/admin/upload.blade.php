@props(['model', 'label' => 'Choose a photo', 'multiple' => false])
@php($id = 'upload-'.str_replace(['.', '*'], '-', $model))
<div x-data="{ uploading: false, progress: 0 }"
     x-on:livewire-upload-start="uploading = true"
     x-on:livewire-upload-finish="uploading = false; progress = 0"
     x-on:livewire-upload-cancel="uploading = false"
     x-on:livewire-upload-error="uploading = false"
     x-on:livewire-upload-progress="progress = $event.detail.progress">
    <input id="{{ $id }}" type="file" wire:model="{{ $model }}" accept="image/jpeg,image/png,image/webp" @if ($multiple) multiple @endif class="peer sr-only">
    <label for="{{ $id }}" class="btn-secondary cursor-pointer peer-focus-visible:outline-3 peer-focus-visible:outline-offset-3 peer-focus-visible:outline-gold-500">
        <x-icon name="image" class="size-5" /> {{ $label }}
    </label>
    <p class="mt-2 text-base text-muted">JPG, PNG or WEBP, up to 5 MB each. Photos are made smaller automatically.</p>
    <div x-show="uploading" x-cloak class="mt-3">
        <div class="h-3 overflow-hidden rounded-full bg-sage-100"><div class="h-full bg-sage-700 transition-all" :style="`width: ${progress}%`"></div></div>
        <p class="mt-1 text-base text-muted">Uploading… <span x-text="progress"></span>%</p>
    </div>
</div>

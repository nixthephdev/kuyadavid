@props(['key', 'saved' => null, 'upload' => null, 'label' => 'QR code'])
<div>
    <p class="field-label">{{ $label }}</p>
    @if ($upload && method_exists($upload, 'isPreviewable') && $upload->isPreviewable())
        <img src="{{ $upload->temporaryUrl() }}" alt="New QR code preview" class="mb-3 w-48 rounded-xl border border-line">
        <p class="mb-3 text-base text-muted">Press Save to use this QR code.</p>
    @elseif ($saved)
        <img src="{{ \App\Support\ImageStore::url($saved, 'sm') }}" alt="Current QR code" class="mb-3 w-48 rounded-xl border border-line">
        <button type="button" wire:click="removeQr('{{ $key }}')" wire:confirm="Remove this QR code?" class="btn-quiet mb-3 min-h-11 px-4 py-2 text-base text-[#9a3412]"><x-icon name="trash" class="size-5" /> Remove QR code</button>
    @endif
    <x-admin.upload :model="'qrUploads.'.$key" :label="$saved ? 'Choose a new QR code' : 'Choose the QR code image'" />
    @error('qrUploads.'.$key) <p class="field-error">{{ $message }}</p> @enderror
</div>

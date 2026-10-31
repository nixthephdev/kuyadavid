<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ isset($title) ? $title.' · ' : '' }}Family admin</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-cream">
    <header class="border-b border-line bg-paper">
        <div class="mx-auto flex h-16 max-w-3xl items-center justify-between gap-3 px-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-serif text-xl font-semibold text-ink no-underline">
                <x-icon name="sprout" class="size-7 text-sage-700" /> Family admin
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" target="_blank" class="btn-quiet min-h-11 px-4 py-2 text-base">
                    <x-icon name="eye" class="size-5" /> <span class="hidden sm:inline">View site</span>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn-quiet min-h-11 px-4 py-2 text-base">
                        <x-icon name="logout" class="size-5" /> <span class="hidden sm:inline">Sign out</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-8">
        @unless (request()->routeIs('admin.dashboard'))
            <a href="{{ route('admin.dashboard') }}" class="mb-6 inline-flex items-center gap-2 text-lg font-semibold text-sage-800 no-underline hover:underline">
                <x-icon name="arrow-left" class="size-5" /> Back to the main menu
            </a>
        @endunless

        {{ $slot }}
    </main>

    {{-- "Saved" messages from Livewire: $this->dispatch('notify', message: '…') --}}
    <div x-data="{ show: false, message: '', timer: null }"
         @notify.window="message = $event.detail.message; show = true; clearTimeout(timer); timer = setTimeout(() => show = false, 3500)"
         x-show="show" x-cloak x-transition.opacity
         class="fixed inset-x-4 bottom-4 z-50 mx-auto flex max-w-md items-center gap-3 rounded-2xl bg-sage-900 px-5 py-4 text-lg font-semibold text-white shadow-xl"
         role="status" aria-live="polite">
        <x-icon name="check" class="size-6 text-gold-300" />
        <span x-text="message"></span>
    </div>

    @livewireScriptConfig
</body>
</html>

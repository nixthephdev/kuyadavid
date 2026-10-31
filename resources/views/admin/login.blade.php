<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in · Family admin</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-cream px-4 py-10">
    <main class="w-full max-w-md">
        <div class="mb-6 flex items-center justify-center gap-2">
            <x-icon name="sprout" class="size-9 text-sage-700" />
            <span class="font-serif text-2xl font-semibold">Family admin</span>
        </div>
        <form method="POST" action="{{ route('admin.login') }}" class="card space-y-5">
            @csrf
            <h1 class="text-3xl font-semibold">Sign in</h1>

            @if ($errors->any())
                <p class="rounded-xl bg-[#fff1e6] p-4 text-lg font-semibold text-[#9a3412]" role="alert">
                    {{ $errors->first() }}
                </p>
            @endif

            <div>
                <label for="email" class="field-label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="field">
            </div>
            <div>
                <label for="password" class="field-label">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="field">
            </div>
            <label class="flex items-center gap-3 text-lg">
                <input type="checkbox" name="remember" value="1" class="size-6 rounded accent-sage-700"> Keep me signed in
            </label>
            <button type="submit" class="btn-primary w-full">Sign in</button>
        </form>
        <p class="mt-6 text-center"><a href="{{ route('home') }}" class="link">Back to the website</a></p>
    </main>
</body>
</html>

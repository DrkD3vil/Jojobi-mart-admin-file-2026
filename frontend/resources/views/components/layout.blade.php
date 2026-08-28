@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ? $title . ' · ' . config('store.name') : config('store.name') . ' — Everyday essentials, delivered' }}</title>
<meta name="description" content="{{ $description ?? 'Shop everyday essentials online at ' . config('store.name') . '.' }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    (function () {
        try {
            var mode = localStorage.getItem('jojobi-theme');
            if (mode === 'dark' || mode === 'light') document.documentElement.setAttribute('data-theme', mode);
        } catch (e) {}
    })();
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data
    data-cart-count="{{ $cartCount ?? app(\App\Services\CartService::class)->count() }}"
    class="min-h-screen flex flex-col bg-bg text-ink"
>
    @include('partials.toasts')

    @if (session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => Alpine.store('toast').push(@js(session('success')), 'good'));</script>
    @endif
    @if ($errors->any())
        <script>document.addEventListener('DOMContentLoaded', () => Alpine.store('toast').push(@js($errors->first()), 'bad'));</script>
    @endif

    @include('partials.header')
    @include('partials.mobile-menu')
    @include('partials.cart-drawer')

    <main class="flex-1">
        {{ $slot }}
    </main>

    @include('partials.footer')
</body>
</html>

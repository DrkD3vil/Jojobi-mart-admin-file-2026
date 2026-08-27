<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Default Title')</title>

    <script>
        (function () {
            try {
                var saved = localStorage.getItem('shop_dashboard_theme');
                var theme = saved || (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {}
        })();
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopSphere - E-commerce Dashboard</title>
    <meta name="theme-color" content="#0A1420" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#F4F0E6" media="(prefers-color-scheme: light)">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @include('components.style')
</head>

<body class="flex h-screen overflow-hidden">

    @include('components.sidebar')
    <!-- Overlay for mobile sidebar -->
    <div id="sidebar-overlay"></div>

    <!-- Main Content Area -->
    <div id="app-scroll" class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar">

        <!-- Header -->

        @include('components.header')
        <!-- Main Content -->
        <main class="p-6 md:p-8 main-content page-enter">

           

            @yield('content')


        </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav id="bottom-nav">
        <a href="#" class="mobile-nav-link mobile-nav-link-active">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span>Dashboard</span>
        </a>
        <a href="#" class="mobile-nav-link">
            <i data-lucide="package" class="w-5 h-5"></i>
            <span>Products</span>
        </a>
        <a href="#" class="mobile-nav-link">
            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
            <span>Orders</span>
        </a>
        <a href="#" class="mobile-nav-link">
            <i data-lucide="trending-up" class="w-5 h-5"></i>
            <span>Analytics</span>
        </a>
        <a href="#" class="mobile-nav-link">
            <i data-lucide="more-horizontal" class="w-5 h-5"></i>
            <span>More</span>
        </a>
    </nav>


    @auth
        @include('components.ai-assistant-widget')
    @endauth

    @include('components.script')
</body>

</html>

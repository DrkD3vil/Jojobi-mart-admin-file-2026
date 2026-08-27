<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Default Title')</title>



    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopSphere - E-commerce Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @include('components.style')
</head>

<body class="flex h-screen overflow-hidden">

    @include('components.sidebar')
    <!-- Overlay for mobile sidebar -->
    <div id="sidebar-overlay"></div>

    <!-- Main Content Area -->
    <div class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar">

        <!-- Header -->

        @include('components.header')
        <!-- Main Content -->
        <main class="p-6 md:p-8 main-content">

           

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


    @include('components.script')
</body>

</html>

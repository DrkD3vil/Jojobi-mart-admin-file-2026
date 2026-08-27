<!-- Desktop Sidebar -->
<div id="sidebar-desktop" class="sidebar sidebar-expanded flex-shrink-0 z-50">

    <!-- Header -->
    <div class="p-6 pb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 flex items-center justify-center brand-glow"
                style="background: var(--primary); border-radius: calc(var(--radius) - 2px); clip-path: polygon(0 0,100% 0,100% 76%,88% 100%,0 100%);">
                <i data-lucide="shopping-bag" class="w-5 h-5" style="color: var(--primary-foreground);"></i>
            </div>

            <span class="logo-full text-xl tracking-tight" style="font-family: var(--font-display); font-weight: 600; color: var(--foreground);">
                JOJOBI MART
            </span>
            <span class="logo-icon text-xl hidden" style="font-family: var(--font-display); font-weight: 600; color: var(--primary);">
                JB
            </span>
        </div>
    </div>

    <!-- ✅ Scroll Area -->
    <div class="sidebar-body custom-scrollbar">
        @include('components.sidebar-nav')
    </div>

    <!-- ✅ Fixed Footer -->
    @include('components.sidebar-footer', ['footerId' => 'desktop'])

</div>



<!-- Mobile Sidebar -->
<div id="sidebar-mobile" class="sidebar flex-shrink-0 z-50">

    <!-- Header -->
    <div class="p-6 pb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 flex items-center justify-center brand-glow"
                style="background: var(--primary); border-radius: calc(var(--radius) - 2px); clip-path: polygon(0 0,100% 0,100% 76%,88% 100%,0 100%);">
                <i data-lucide="shopping-bag" class="w-5 h-5" style="color: var(--primary-foreground);"></i>
            </div>
            <span class="text-xl tracking-tight" style="font-family: var(--font-display); font-weight: 600; color: var(--foreground);">
                JOJOBI MART
            </span>
        </div>

        <button id="sidebar-close-mobile"
            class="text-[var(--muted-foreground)] hover:text-[var(--sidebar-primary)] transition-colors duration-200">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- ✅ Scroll Area -->
    <div class="sidebar-body custom-scrollbar">
        @include('components.sidebar-nav')
    </div>

    <!-- ✅ Fixed Footer -->
    @include('components.sidebar-footer', ['footerId' => 'mobile'])

</div>

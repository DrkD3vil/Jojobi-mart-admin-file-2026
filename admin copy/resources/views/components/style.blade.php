<style>
    /* Enhanced CSS Variables with the new color system */
    :root {
        /* Base radius for components */
        --radius: 0.625rem;

        /* Layout */
        --header-height: 64px;
        --sidebar-width: 280px;
        --sidebar-width-collapsed: 80px;

        /* Animation speeds */
        --transition-fast: 150ms;
        --transition-normal: 250ms;
        --transition-slow: 350ms;

        /* DARK MODE (Default) */
        --background: oklch(0.145 0 0);
        --foreground: oklch(0.985 0 0);
        --card: oklch(0.205 0 0);
        --card-foreground: oklch(0.985 0 0);
        --popover: oklch(0.205 0 0);
        --popover-foreground: oklch(0.985 0 0);
        --primary: oklch(0.922 0 0);
        --primary-foreground: oklch(0.205 0 0);
        --secondary: oklch(0.269 0 0);
        --secondary-foreground: oklch(0.985 0 0);
        --muted: oklch(0.269 0 0);
        --muted-foreground: oklch(0.708 0 0);
        --accent: oklch(0.269 0 0);
        --accent-foreground: oklch(0.985 0 0);
        --destructive: oklch(0.704 0.191 22.216);
        --border: oklch(1 0 0 / 15%);
        --input: oklch(1 0 0 / 15%);
        --ring: oklch(0.556 0 0);

        /* Sidebar colors (dark mode) */
        --sidebar: oklch(0.18 0 0);
        --sidebar-foreground: oklch(0.985 0 0);
        --sidebar-primary: oklch(0.488 0.243 264.376);
        --sidebar-primary-foreground: oklch(0.985 0 0);
        --sidebar-accent: oklch(0.24 0 0);
        --sidebar-accent-foreground: oklch(0.985 0 0);
        --sidebar-border: oklch(1 0 0 / 15%);
        --sidebar-ring: oklch(0.556 0 0);

        /* Extended semantic colors (dark mode) */
        --success: oklch(0.696 0.17 162.48);
        --warning: oklch(0.769 0.188 70.08);
        --info: oklch(0.488 0.243 264.376);
        --danger: oklch(0.704 0.191 22.216);

        /* Chart colors (dark mode) */
        --chart-1: oklch(0.488 0.243 264.376);
        --chart-2: oklch(0.696 0.17 162.48);
        --chart-3: oklch(0.769 0.188 70.08);
        --chart-4: oklch(0.627 0.265 303.9);
        --chart-5: oklch(0.645 0.246 16.439);

        /* Card shadows (dark mode) */
        --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.25);
        --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.35), 0 3px 6px -2px rgb(0 0 0 / 0.25);

        /* Dropdown shadow for dark mode */
        --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.3);

        /* Custom semantic variables for existing components */
        --accent-color: var(--sidebar-primary);
        --accent-hover: oklch(0.488 0.243 264.376 / 0.8);
        --accent-glow: oklch(0.488 0.243 264.376 / 0.2);
        --bg-primary: var(--background);
        --bg-secondary: var(--card);
        --bg-tertiary: var(--secondary);
        --text-primary: var(--foreground);
        --text-secondary: var(--muted-foreground);
        --text-muted: oklch(0.708 0 0 / 0.7);
        --border-color: var(--border);
        --glass-base: oklch(0.205 0 0 / 0.7);
    }

    /* LIGHT MODE */
    html[data-theme='light'] {
        --background: oklch(0.99 0 0);
        --foreground: oklch(0.12 0 0);
        --card: oklch(1 0 0);
        --card-foreground: oklch(0.12 0 0);
        --popover: oklch(1 0 0);
        --popover-foreground: oklch(0.12 0 0);
        --primary: oklch(0.15 0 0);
        --primary-foreground: oklch(0.99 0 0);
        --secondary: oklch(0.97 0 0);
        --secondary-foreground: oklch(0.15 0 0);
        --muted: oklch(0.96 0 0);
        --muted-foreground: oklch(0.5 0 0);
        --accent: oklch(0.96 0 0);
        --accent-foreground: oklch(0.15 0 0);
        --destructive: oklch(0.577 0.245 27.325);
        --border: oklch(0.9 0 0);
        --input: oklch(0.96 0 0);
        --ring: oklch(0.65 0 0);

        /* Sidebar colors - Pure white for light mode */
        --sidebar: oklch(1 0 0);
        --sidebar-foreground: oklch(0.12 0 0);
        --sidebar-primary: oklch(0.646 0.222 41.116);
        --sidebar-primary-foreground: oklch(1 0 0);
        --sidebar-accent: oklch(0.97 0 0);
        --sidebar-accent-foreground: oklch(0.15 0 0);
        --sidebar-border: oklch(0.88 0 0);
        --sidebar-ring: oklch(0.65 0 0);

        /* Extended semantic colors */
        --success: oklch(0.627 0.194 149.214);
        --warning: oklch(0.769 0.188 70.08);
        --info: oklch(0.623 0.214 259.815);
        --danger: oklch(0.577 0.245 27.325);

        /* Chart colors */
        --chart-1: oklch(0.646 0.222 41.116);
        --chart-2: oklch(0.6 0.118 184.704);
        --chart-3: oklch(0.398 0.07 227.392);
        --chart-4: oklch(0.828 0.189 84.429);
        --chart-5: oklch(0.769 0.188 70.08);

        /* Card shadows - More depth */
        --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.08);
        --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.12), 0 3px 6px -2px rgb(0 0 0 / 0.08);

        /* Dropdown shadow */
        --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15), 0 8px 10px -6px rgb(0 0 0 / 0.1);

        /* Custom semantic variables for existing components */
        --accent-color: var(--sidebar-primary);
        --accent-hover: oklch(0.646 0.222 41.116 / 0.8);
        --accent-glow: oklch(0.646 0.222 41.116 / 0.1);
        --bg-primary: var(--background);
        --bg-secondary: var(--card);
        --bg-tertiary: var(--secondary);
        --text-primary: var(--foreground);
        --text-secondary: var(--muted-foreground);
        --text-muted: oklch(0.5 0 0 / 0.7);
        --border-color: var(--border);
        --glass-base: rgba(255, 255, 255, 0.85);
    }

    /* Base Styles */
    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--background);
        color: var(--foreground);
        transition: background-color var(--transition-normal), color var(--transition-normal);
        overflow-x: hidden;
    }

    /* Enhanced Glassmorphism Effect */
    .glass-card {
        background-color: var(--glass-base);
        backdrop-filter: blur(12px);
        border: 1px solid var(--border);
        box-shadow: var(--card-shadow);
        border-radius: var(--radius);
        transition: all var(--transition-normal) cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--card-shadow-hover), 0 0 20px var(--accent-glow);
        border-color: var(--accent-color);
    }

    /* Sidebar Styles */
    .sidebar {
        transition: all var(--transition-normal) cubic-bezier(0.4, 0, 0.2, 1);
        background-color: var(--sidebar);
        border-right: 1px solid var(--sidebar-border);
    }

    .sidebar-collapsed { width: var(--sidebar-width-collapsed); }
    .sidebar-expanded { width: var(--sidebar-width); }

    /* Sticky Sidebar on Desktop */
    @media (min-width: 1024px) {
        #sidebar-desktop {
            position: sticky;
            top: 0;
            height: 100vh;
        }
    }

    /* Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideInLeft { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); max-height: 0; }
        to { opacity: 1; transform: translateY(0); max-height: 500px; }
    }
    @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
    @keyframes shimmer { 0% { background-position: -468px 0; } 100% { background-position: 468px 0; } }

    .fade-in-item { opacity: 0; animation: fadeInUp 0.5s ease-out forwards; }
    .slide-in-left { opacity: 0; animation: slideInLeft 0.5s ease-out forwards; }
    .pulse-animation { animation: pulse 2s infinite; }
    .shimmer {
        background: linear-gradient(to right, var(--card) 4%, var(--secondary) 25%, var(--card) 36%);
        background-size: 1000px 100%;
        animation: shimmer 2s infinite linear;
    }

    /* Enhanced Navigation Styles */
    .nav-link {
        position: relative;
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-radius: calc(var(--radius) - 2px);
        color: var(--sidebar-foreground);
        transition: all var(--transition-fast) ease;
        overflow: hidden;
        background-color: transparent;
        cursor: pointer;
        text-decoration: none;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background-color: var(--sidebar-primary);
        transform: scaleY(0);
        transition: transform var(--transition-fast) ease;
    }

    .nav-link:hover {
        color: var(--sidebar-primary);
        background-color: var(--sidebar-accent);
    }

    .nav-link:hover::before { transform: scaleY(1); }

    .nav-link-active {
        color: var(--sidebar-primary-foreground);
        background-color: var(--sidebar-primary);
    }
    .nav-link-active::before { transform: scaleY(1); }

    /* Enhanced Dropdown Styles - Integrated into sidebar */
    .dropdown { position: relative; }
    .dropdown-toggle { display: flex; align-items: center; width: 100%; cursor: pointer; }

    .dropdown-content {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: all var(--transition-normal) ease;
        margin-left: 2rem;
        border-left: 2px solid var(--sidebar-border);
        padding-left: 0.5rem;
    }

    .dropdown.active .dropdown-content {
        max-height: 500px;
        opacity: 1;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        animation: slideDown var(--transition-normal) ease;
    }

    .dropdown-content .nav-link {
        padding: 0.625rem 1rem;
        border-radius: calc(var(--radius) - 4px);
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
        border-left: none;
        position: relative;
    }

    .dropdown-content .nav-link:last-child { margin-bottom: 0; }
    .dropdown-content .nav-link::before { display: none; }
    .dropdown-content .nav-link:hover { background-color: var(--sidebar-accent); transform: translateX(5px); }

    .dropdown-chevron { margin-left: auto; transition: transform var(--transition-fast) ease; }
    .dropdown.active .dropdown-chevron { transform: rotate(180deg); }

    /* Centered icons for collapsed sidebar */
    .sidebar-collapsed .nav-link { justify-content: center; padding: 0.75rem; }
    .sidebar-collapsed .nav-text,
    .sidebar-collapsed .nav-badge,
    .sidebar-collapsed .dropdown-chevron { display: none; }

    .sidebar-collapsed .logo-full { display: none; }
    .sidebar-collapsed .logo-icon { display: block; }
    .sidebar-expanded .logo-icon { display: none; }

    /* ===========================
       ✅ UPDATED SIDEBAR SCROLL + FIXED FOOTER (ONLY SIDEBAR)
       =========================== */

    /* Make sidebar a flex column so footer is fixed and menu scrolls */
    #sidebar-desktop,
    #sidebar-mobile {
        display: flex;
        flex-direction: column;
        height: 100vh;
        overflow: hidden; /* stops whole sidebar scrolling */
    }

    /* Scrollable area wrapper for nav: <div class="sidebar-body"> ... </div> */
    .sidebar-body {
        flex: 1;
         min-height: 0;   
        overflow-y: auto;
        overflow-x: hidden;

        /* smooth scroll */
        scroll-behavior: smooth;

        /* iOS momentum scrolling */
        -webkit-overflow-scrolling: touch;

        /* keeps last items visible above footer */
        padding-bottom: 1rem;
    }

    /* Footer fixed at bottom inside sidebar */
    .sidebar-footer {
        flex-shrink: 0;
        background: var(--sidebar);
    }

    /* Smooth + nicer scrollbar only for sidebar-body */
/* Optional: better scrollbar only for nav scroll area */
.sidebar-body::-webkit-scrollbar {
    width: 6px;
}
.sidebar-body::-webkit-scrollbar-thumb {
    background: color-mix(in srgb, var(--sidebar-primary) 70%, transparent);
    border-radius: 10px;
}
.sidebar-body::-webkit-scrollbar-track {
    background: transparent;
}

    /* Mobile Sidebar */
    #sidebar-mobile {
        position: fixed;
        top: 0;
        left: -100%;
        width: var(--sidebar-width);
        z-index: 100;
        transition: left var(--transition-normal) ease;
    }

    #sidebar-mobile.active { left: 0; }

    #sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 99;
        opacity: 0;
        visibility: hidden;
        transition: opacity var(--transition-normal) ease, visibility var(--transition-normal) ease;
    }

    #sidebar-overlay.active { opacity: 1; visibility: visible; }

    /* Mobile Navigation */
    @media (max-width: 1023px) {
        #sidebar-desktop { display: none; }

        .main-content { padding-bottom: 80px !important; }

        #bottom-nav {
            display: flex;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 70px;
            background-color: var(--card);
            border-top: 1px solid var(--border);
            z-index: 50;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .mobile-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            color: var(--muted-foreground);
            padding: 0.5rem;
            border-radius: var(--radius);
            transition: all var(--transition-fast);
            position: relative;
            text-decoration: none;
        }

        .mobile-nav-link::after {
            content: '';
            position: absolute;
            bottom: -10px;
            width: 0;
            height: 3px;
            background-color: var(--sidebar-primary);
            border-radius: 3px;
            transition: width var(--transition-normal) ease;
        }

        .mobile-nav-link-active { color: var(--sidebar-primary); }
        .mobile-nav-link-active::after { width: 20px; }

        /* Mobile dropdown adjustments */
        .dropdown-content { margin-left: 1.5rem; }
    }

    @media (min-width: 1024px) {
        #bottom-nav { display: none; }
    }

    /* Custom Scrollbar (keep your original, but sidebar-body already has better scrollbars) */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--sidebar-primary); border-radius: 3px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }

    /* ✅ Blade uses .active, your CSS was styling .nav-link-active only */
    .nav-link.active {
        color: var(--sidebar-primary-foreground);
        background-color: var(--sidebar-primary);
    }
    .nav-link.active::before { transform: scaleY(1); }

    /* ✅ Tooltip only in collapsed desktop sidebar */
    #sidebar-desktop.sidebar-collapsed .nav-link[data-tooltip] { position: relative; }
    #sidebar-desktop.sidebar-collapsed .nav-link[data-tooltip]::after {
        content: attr(data-tooltip);
        position: absolute;
        left: calc(100% + 10px);
        top: 50%;
        transform: translateY(-50%);
        background: var(--card);
        color: var(--foreground);
        border: 1px solid var(--border);
        padding: 6px 10px;
        border-radius: 10px;
        white-space: nowrap;
        box-shadow: var(--dropdown-shadow);
        opacity: 0;
        pointer-events: none;
        transition: opacity var(--transition-fast) ease;
        z-index: 9999;
    }
    #sidebar-desktop.sidebar-collapsed .nav-link[data-tooltip]:hover::after { opacity: 1; }
</style>

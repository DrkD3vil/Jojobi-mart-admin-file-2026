<style>
    /* Enhanced CSS Variables with the new color system */
    :root {
        /* Typography — Dockline system: Fraunces (display), Inter (UI/body), IBM Plex Mono (data) */
        --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        --font-display: 'Fraunces', ui-serif, Georgia, 'Times New Roman', serif;
        --font-mono: 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace;

        /* Base radius for components */
        --radius: 0.375rem;

        /* Shared motion curve — everything eases on this one curve */
        --ease: cubic-bezier(.22, .9, .32, 1);

        /* Layout */
        --header-height: 64px;
        --sidebar-width: 280px;
        --sidebar-width-collapsed: 80px;

        /* Animation speeds */
        --transition-fast: 150ms;
        --transition-normal: 250ms;
        --transition-slow: 350ms;

        /* DARK MODE (Default) — Dockline: navy dock / amber stamp / teal signal */
        --background: #0A1420;
        --foreground: #ECE6D8;
        --card: #101B27;
        --card-foreground: #ECE6D8;
        --popover: #101B27;
        --popover-foreground: #ECE6D8;
        --primary: #FFB020;
        --primary-foreground: #0A1420;
        --secondary: #0E1926;
        --secondary-foreground: #ECE6D8;
        --muted: #0E1926;
        --muted-foreground: #93A4B0;
        --accent: color-mix(in srgb, #ECE6D8 8%, transparent);
        --accent-foreground: #ECE6D8;
        --destructive: #FF5D5D;
        --border: rgba(236, 230, 216, .14);
        --input: rgba(236, 230, 216, .14);
        --ring: #FFB020;

        /* Sidebar colors (dark mode) */
        --sidebar: #101B27;
        --sidebar-foreground: #ECE6D8;
        --sidebar-primary: #FFB020;
        --sidebar-primary-foreground: #0A1420;
        --sidebar-accent: rgba(236, 230, 216, .06);
        --sidebar-accent-foreground: #ECE6D8;
        --sidebar-border: rgba(236, 230, 216, .14);
        --sidebar-ring: #FFB020;

        /* Extended semantic colors (dark mode) */
        --success: #2FD9C0;
        --success-foreground: #06251F;
        --warning: #FFB020;
        --info: #6EA8FE;
        --danger: #FF5D5D;

        /* Chart colors (dark mode) */
        --chart-1: #FFB020;
        --chart-2: #2FD9C0;
        --chart-3: #6EA8FE;
        --chart-4: #FF5D5D;
        --chart-5: #C792EA;

        /* Card shadows (dark mode) */
        --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.25);
        --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.35), 0 3px 6px -2px rgb(0 0 0 / 0.25);

        /* Dropdown shadow for dark mode */
        --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.3);

        /* Custom semantic variables for existing components */
        --accent-color: var(--sidebar-primary);
        --accent-hover: #FFC658;
        --accent-glow: rgba(255, 176, 32, .22);
        --bg-primary: var(--background);
        --bg-secondary: var(--card);
        --bg-tertiary: var(--secondary);
        --text-primary: var(--foreground);
        --text-secondary: var(--muted-foreground);
        --text-muted: rgba(147, 164, 176, .7);
        --border-color: var(--border);
        --glass-base: rgba(16, 27, 39, .7);
    }

    /* LIGHT MODE — Dockline: warm paper / burnt-amber stamp / deep teal signal */
    html[data-theme='light'] {
        --background: #F4F0E6;
        --foreground: #12181C;
        --card: #FFFFFF;
        --card-foreground: #12181C;
        --popover: #FFFFFF;
        --popover-foreground: #12181C;
        --primary: #B96E10;
        --primary-foreground: #12181C;
        --secondary: #EAE3D2;
        --secondary-foreground: #12181C;
        --muted: #EAE3D2;
        --muted-foreground: #5A6570;
        --accent: color-mix(in srgb, #12181C 6%, transparent);
        --accent-foreground: #12181C;
        --destructive: #C22E2E;
        --border: rgba(18, 24, 28, .14);
        --input: rgba(18, 24, 28, .14);
        --ring: #B96E10;

        /* Sidebar colors - clean paper for light mode */
        --sidebar: #FFFFFF;
        --sidebar-foreground: #12181C;
        --sidebar-primary: #B96E10;
        --sidebar-primary-foreground: #12181C;
        --sidebar-accent: rgba(18, 24, 28, .05);
        --sidebar-accent-foreground: #12181C;
        --sidebar-border: rgba(18, 24, 28, .14);
        --sidebar-ring: #B96E10;

        /* Extended semantic colors */
        --success: #177264;
        --success-foreground: #FFFFFF;
        --warning: #B96E10;
        --info: #3B6FC4;
        --danger: #C22E2E;

        /* Chart colors */
        --chart-1: #B96E10;
        --chart-2: #177264;
        --chart-3: #3B6FC4;
        --chart-4: #C22E2E;
        --chart-5: #8452A6;

        /* Card shadows - More depth */
        --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.08);
        --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.12), 0 3px 6px -2px rgb(0 0 0 / 0.08);

        /* Dropdown shadow */
        --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15), 0 8px 10px -6px rgb(0 0 0 / 0.1);

        /* Custom semantic variables for existing components */
        --accent-color: var(--sidebar-primary);
        --accent-hover: #9C5B0D;
        --accent-glow: rgba(185, 110, 16, .16);
        --bg-primary: var(--background);
        --bg-secondary: var(--card);
        --bg-tertiary: var(--secondary);
        --text-primary: var(--foreground);
        --text-secondary: var(--muted-foreground);
        --text-muted: rgba(90, 101, 112, .7);
        --border-color: var(--border);
        --glass-base: rgba(255, 255, 255, 0.85);
    }

    /* Base Styles */
    @media (prefers-reduced-motion: no-preference) {
        html { scroll-behavior: smooth; }
    }
    body {
        font-family: var(--font-sans);
        background-color: var(--background);
        color: var(--foreground);
        transition: background-color var(--transition-normal), color var(--transition-normal);
        overflow-x: hidden;
    }
    ::selection { background-color: var(--sidebar-primary); color: var(--sidebar-primary-foreground); }
    :focus-visible { outline: 2px solid var(--sidebar-primary); outline-offset: 2px; border-radius: 4px; }

    /* Display type: Fraunces for big headings, Inter (bolder) for card/section titles */
    h1, h2 { font-family: var(--font-display); font-weight: 500; letter-spacing: -0.01em; }
    h3 { font-weight: 600; }
    code, .font-mono, .mono { font-family: var(--font-mono); }

    /* Enhanced Glassmorphism Effect */
    .glass-card {
        background-color: var(--glass-base);
        backdrop-filter: blur(12px);
        border: 1px solid var(--border);
        box-shadow: var(--card-shadow);
        border-radius: var(--radius);
        transition: all var(--transition-normal) var(--ease);
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--card-shadow-hover), 0 0 20px var(--accent-glow);
        border-color: var(--accent-color);
    }

    /* Sidebar Styles */
    .sidebar {
        transition: all var(--transition-normal) var(--ease);
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

    .fade-in-item { opacity: 0; animation: fadeInUp 0.5s var(--ease) forwards; }
    .slide-in-left { opacity: 0; animation: slideInLeft 0.5s var(--ease) forwards; }
    .pulse-animation { animation: pulse 2s infinite; }
    .shimmer {
        background: linear-gradient(to right, var(--card) 4%, var(--secondary) 25%, var(--card) 36%);
        background-size: 1000px 100%;
        animation: shimmer 2s infinite linear;
    }
    @media (prefers-reduced-motion: no-preference) {
        .page-enter { animation: fadeInUp 0.45s var(--ease) both; }
    }

    /* Scroll-triggered reveal: add data-reveal to any wrapper; JS in script.blade.php
       toggles .is-visible the first time it enters the viewport. */
    [data-reveal] { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: no-preference) {
        [data-reveal] {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity 0.6s var(--ease), transform 0.6s var(--ease);
        }
        [data-reveal].is-visible { opacity: 1; transform: none; }
        [data-reveal-group] > [data-reveal]:nth-child(1) { transition-delay: 0.03s; }
        [data-reveal-group] > [data-reveal]:nth-child(2) { transition-delay: 0.07s; }
        [data-reveal-group] > [data-reveal]:nth-child(3) { transition-delay: 0.11s; }
        [data-reveal-group] > [data-reveal]:nth-child(4) { transition-delay: 0.15s; }
        [data-reveal-group] > [data-reveal]:nth-child(5) { transition-delay: 0.19s; }
        [data-reveal-group] > [data-reveal]:nth-child(n+6) { transition-delay: 0.23s; }
    }

    /* Shared, theme-aware primitives every page can opt into instead of
       re-declaring its own local button/badge/alert/input CSS.
       Dockline: two button languages only — Stamp (primary commit) and
       Cut-line (secondary/exploratory) — plus flat semantic fills where a
       dense admin UI genuinely needs unambiguous color (destructive/success). */
    .btn {
        display: inline-flex; align-items: center; gap: 0.5rem; justify-content: center;
        padding: 0.65rem 1.2rem; border-radius: calc(var(--radius) - 2px);
        font-size: 0.8125rem; font-weight: 600; border: 1px solid transparent;
        cursor: pointer; transition: transform 0.35s var(--ease), box-shadow 0.35s var(--ease), filter 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        text-decoration: none; user-select: none;
    }
    .btn:disabled { opacity: 0.45; cursor: not-allowed; transform: none !important; }

    /* Primary — the "Stamp": amber fill, clipped corner, lifts + tilts on hover like a stamp coming off the page */
    .btn-primary {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.7rem 1.3rem; border: none; cursor: pointer; text-decoration: none;
        font-family: var(--font-mono); font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em;
        background: var(--primary); color: var(--primary-foreground);
        clip-path: polygon(0 0, 100% 0, 100% 78%, 94% 100%, 0 100%);
        box-shadow: 0 1px 0 rgba(0, 0, 0, .15);
        transition: transform 0.35s var(--ease), box-shadow 0.35s var(--ease), filter 0.2s ease;
    }
    .btn-primary:hover { transform: translate(-2px, -3px) rotate(-.6deg); box-shadow: 4px 6px 0 rgba(0, 0, 0, .3); }
    .btn-primary:active { transform: translate(0, 0) rotate(0); box-shadow: 0 1px 0 rgba(0, 0, 0, .15); }

    /* Secondary / outline / ghost — the "Cut-line": restrained, dashed or transparent, simple lift on hover */
    .btn-secondary {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.65rem 1.2rem; border-radius: calc(var(--radius) - 2px);
        font-size: 0.8125rem; font-weight: 600; cursor: pointer; text-decoration: none;
        background: var(--secondary); color: var(--secondary-foreground); border: 1px solid var(--border);
        transition: transform 0.35s var(--ease), background-color 0.2s ease, border-color 0.2s ease;
    }
    .btn-secondary:hover { background: var(--accent); transform: translateY(-2px); }
    .btn-outline {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.65rem 1.2rem; border-radius: calc(var(--radius) - 2px);
        font-size: 0.8125rem; font-weight: 600; cursor: pointer; text-decoration: none;
        background: transparent; color: var(--foreground);
        border: 1px dashed color-mix(in srgb, var(--foreground) 35%, transparent);
        transition: transform 0.35s var(--ease), border-color 0.2s ease, color 0.2s ease, background-color 0.2s ease;
    }
    .btn-outline:hover { border-color: var(--primary); border-style: solid; color: var(--primary); background: var(--secondary); transform: translateY(-2px); }
    .btn-ghost {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.65rem 1.2rem; border-radius: calc(var(--radius) - 2px);
        font-size: 0.8125rem; font-weight: 600; cursor: pointer; text-decoration: none; border: 1px solid transparent;
        background: transparent; color: var(--muted-foreground);
        transition: background-color 0.2s ease, color 0.2s ease;
    }
    .btn-ghost:hover { background: var(--accent); color: var(--foreground); }

    /* Functional fills — unambiguous semantic actions, kept flat (never stamped) */
    .btn-destructive, .btn-danger {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.65rem 1.2rem; border-radius: calc(var(--radius) - 2px);
        font-size: 0.8125rem; font-weight: 600; cursor: pointer; text-decoration: none; border: none;
        background: var(--danger); color: #fff;
        transition: transform 0.35s var(--ease), filter 0.2s ease;
    }
    .btn-destructive:hover, .btn-danger:hover { filter: brightness(1.08); transform: translateY(-2px); }
    .btn-success {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.65rem 1.2rem; border-radius: calc(var(--radius) - 2px);
        font-size: 0.8125rem; font-weight: 600; cursor: pointer; text-decoration: none; border: none;
        background: var(--success); color: var(--success-foreground);
        transition: transform 0.35s var(--ease), filter 0.2s ease;
    }
    .btn-success:hover { filter: brightness(1.08); transform: translateY(-2px); }

    .btn-sm.btn-primary, .btn-primary.btn-sm { padding: 0.4rem 0.8rem; font-size: 0.7rem; }
    .btn-sm:not(.btn-primary) { padding: 0.4rem 0.8rem; font-size: 0.76rem; }

    .badge {
        display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.72rem; font-weight: 600;
        padding: 0.22rem 0.6rem; border-radius: 999px; border: 1px solid transparent; white-space: nowrap;
        font-family: var(--font-mono); letter-spacing: 0.02em;
    }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .badge-success { background: color-mix(in oklch, var(--success) 18%, var(--card)); color: var(--success); }
    .badge-warning { background: color-mix(in oklch, var(--warning) 18%, var(--card)); color: var(--warning); }
    .badge-danger { background: color-mix(in oklch, var(--danger) 18%, var(--card)); color: var(--danger); }
    .badge-info { background: color-mix(in oklch, var(--info) 18%, var(--card)); color: var(--info); }
    .badge-neutral { background: var(--secondary); color: var(--secondary-foreground); }
    .badge-outline { background: transparent; border-color: var(--border); color: var(--muted-foreground); }

    .ui-card {
        border: 1px solid var(--border); border-radius: var(--radius); background: var(--card);
        box-shadow: var(--card-shadow); padding: 1.25rem; transition: box-shadow var(--transition-normal) ease, transform var(--transition-normal) ease;
    }
    .ui-card.hoverable:hover { transform: translateY(-3px); box-shadow: var(--card-shadow-hover); }

    .ui-alert { display: flex; gap: 0.7rem; align-items: flex-start; padding: 1rem; border-radius: var(--radius); border: 1px solid; font-size: 0.875rem; }
    .ui-alert-info { background: color-mix(in oklch, var(--info) 10%, var(--card)); border-color: color-mix(in oklch, var(--info) 35%, var(--border)); color: var(--info); }
    .ui-alert-success { background: color-mix(in oklch, var(--success) 10%, var(--card)); border-color: color-mix(in oklch, var(--success) 35%, var(--border)); color: var(--success); }
    .ui-alert-warning { background: color-mix(in oklch, var(--warning) 10%, var(--card)); border-color: color-mix(in oklch, var(--warning) 35%, var(--border)); color: var(--warning); }
    .ui-alert-danger { background: color-mix(in oklch, var(--danger) 10%, var(--card)); border-color: color-mix(in oklch, var(--danger) 35%, var(--border)); color: var(--danger); }
    .ui-alert p, .ui-alert div { color: var(--foreground); }

    .ui-input, .ui-select, .ui-textarea {
        width: 100%; padding: 0.6rem 0.75rem; border-radius: calc(var(--radius) - 2px);
        border: 1px solid var(--border); background: var(--input); color: var(--foreground);
        font-size: 0.875rem; font-family: inherit; transition: border-color var(--transition-fast) ease, box-shadow var(--transition-fast) ease;
    }
    .ui-input:focus, .ui-select:focus, .ui-textarea:focus { outline: none; border-color: var(--sidebar-primary); box-shadow: 0 0 0 3px var(--accent-glow); }
    .ui-textarea { resize: vertical; min-height: 90px; }

    .ui-table-wrap { overflow-x: auto; border: 1px solid var(--border); border-radius: var(--radius); }
    .ui-table-wrap table { width: 100%; border-collapse: collapse; font-size: 0.875rem; min-width: 560px; }
    .ui-table-wrap thead th {
        text-align: left; font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground);
        padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); background: var(--secondary); white-space: nowrap;
        font-family: var(--font-mono); font-weight: 600;
    }
    .ui-table-wrap tbody td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); }
    .ui-table-wrap tbody tr:last-child td { border-bottom: none; }
    .ui-table-wrap tbody tr { transition: background var(--transition-fast) ease; }
    .ui-table-wrap tbody tr:hover { background: var(--accent); }

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

    /* Ambient glow on the brand mark */
    @keyframes brandGlow { 0%, 100% { box-shadow: 0 0 0 0 var(--accent-glow); } 50% { box-shadow: 0 0 16px 2px var(--accent-glow); } }
    @media (prefers-reduced-motion: no-preference) {
        .brand-glow { animation: brandGlow 3.5s ease-in-out infinite; }
    }

    /* Staggered nav entrance on load */
    @media (prefers-reduced-motion: no-preference) {
        .sidebar-body nav.space-y-1 > * {
            opacity: 0;
            animation: slideInLeft 0.4s ease-out forwards;
        }
        .sidebar-body nav.space-y-1 > *:nth-child(1) { animation-delay: 0.03s; }
        .sidebar-body nav.space-y-1 > *:nth-child(2) { animation-delay: 0.06s; }
        .sidebar-body nav.space-y-1 > *:nth-child(3) { animation-delay: 0.09s; }
        .sidebar-body nav.space-y-1 > *:nth-child(4) { animation-delay: 0.12s; }
        .sidebar-body nav.space-y-1 > *:nth-child(5) { animation-delay: 0.15s; }
        .sidebar-body nav.space-y-1 > *:nth-child(6) { animation-delay: 0.18s; }
        .sidebar-body nav.space-y-1 > *:nth-child(7) { animation-delay: 0.21s; }
        .sidebar-body nav.space-y-1 > *:nth-child(8) { animation-delay: 0.24s; }
        .sidebar-body nav.space-y-1 > *:nth-child(9) { animation-delay: 0.27s; }
        .sidebar-body nav.space-y-1 > *:nth-child(n+10) { animation-delay: 0.3s; }
    }

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

    /* Header: scroll state + icon interactions + mobile search */
    header { transition: box-shadow var(--transition-normal) ease; }
    header.header-scrolled { box-shadow: var(--card-shadow); }

    .icon-btn-anim { transition: transform var(--transition-fast) ease, color var(--transition-fast) ease, background-color var(--transition-fast) ease; }
    .icon-btn-anim:hover { transform: translateY(-1px) scale(1.06); }
    .icon-btn-anim:active { transform: scale(0.94); }

    #mobile-search-overlay {
        position: fixed;
        inset: 0;
        z-index: 90;
        background: color-mix(in oklch, var(--background) 92%, transparent);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 1.25rem;
        opacity: 0;
        visibility: hidden;
        transition: opacity var(--transition-normal) ease, visibility var(--transition-normal) ease;
    }
    #mobile-search-overlay.active { opacity: 1; visibility: visible; }
    #mobile-search-overlay .mobile-search-box {
        width: 100%;
        max-width: 34rem;
        margin-top: 4.5rem;
        transform: translateY(-12px);
        transition: transform var(--transition-normal) ease;
    }
    #mobile-search-overlay.active .mobile-search-box { transform: translateY(0); }

    /* ============================================================
       Flatpickr calendar — reskinned to match the app's own color
       tokens so it follows light/dark mode automatically instead of
       looking like a bare, unstyled browser date picker.
    ============================================================ */
    .flatpickr-calendar {
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        border-radius: calc(var(--radius) * 2);
        box-shadow: var(--dropdown-shadow);
        font-family: var(--font-sans);
    }
    .flatpickr-calendar.arrowTop::before,
    .flatpickr-calendar.arrowTop::after {
        border-bottom-color: var(--bg-secondary);
    }
    .flatpickr-months .flatpickr-month {
        color: var(--text-primary);
        fill: var(--text-primary);
    }
    .flatpickr-current-month .flatpickr-monthDropdown-months {
        background: transparent;
        color: var(--text-primary);
    }
    .flatpickr-current-month input.cur-year {
        color: var(--text-primary);
    }
    .flatpickr-prev-month, .flatpickr-next-month {
        fill: var(--text-secondary);
    }
    .flatpickr-prev-month:hover svg, .flatpickr-next-month:hover svg {
        fill: var(--accent-color);
    }
    .flatpickr-weekday {
        background: transparent;
        color: var(--text-secondary);
    }
    .flatpickr-day {
        color: var(--text-primary);
        border-radius: var(--radius);
    }
    .flatpickr-day.today {
        border-color: var(--accent-color);
    }
    .flatpickr-day:hover, .flatpickr-day:focus {
        background: var(--sidebar-accent);
        border-color: var(--sidebar-accent);
    }
    .flatpickr-day.selected,
    .flatpickr-day.selected:hover,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background: var(--accent-color);
        border-color: var(--accent-color);
        color: var(--primary-foreground);
    }
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: var(--text-muted);
    }
    .flatpickr-time {
        border-top: 1px solid var(--border-color);
    }
    .flatpickr-time input, .flatpickr-time .flatpickr-time-separator {
        color: var(--text-primary);
    }
    .numInputWrapper span.arrowUp:after { border-bottom-color: var(--text-secondary); }
    .numInputWrapper span.arrowDown:after { border-top-color: var(--text-secondary); }
</style>

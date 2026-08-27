<script>
    // Initialize Lucide icons
    lucide.createIcons();

    const html = document.documentElement;
    const sidebar = document.getElementById('sidebar-desktop');
    const desktopToggle = document.getElementById('sidebar-toggle-desktop');
    const mobileToggle = document.getElementById('sidebar-toggle-mobile');
    const mobileSidebar = document.getElementById('sidebar-mobile');
    const mobileClose = document.getElementById('sidebar-close-mobile');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');

    // Slider elements
    const slider = document.querySelector('.slider');
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');

    let isSidebarOpen = true;
    let currentSlide = 0;

    // --- Theme Management ---
    const THEME_KEY = 'shop_dashboard_theme';

    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);
        localStorage.setItem(THEME_KEY, theme);
        if (theme === 'dark') {
            themeIcon.setAttribute('data-lucide', 'sun');
        } else {
            themeIcon.setAttribute('data-lucide', 'moon');
        }
        lucide.createIcons();
    }

    function toggleTheme() {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
    }

    function initializeTheme() {
        const savedTheme = localStorage.getItem(THEME_KEY);
        let preferredTheme = 'dark';

        if (savedTheme) {
            preferredTheme = savedTheme;
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
            preferredTheme = 'light';
        }
        applyTheme(preferredTheme);
    }

    // --- Sidebar Management (Desktop Only) ---
    const SIDEBAR_STATE_KEY = 'shop_sidebar_state'; // 'expanded' | 'collapsed'

    function setDesktopSidebarState(state) {
        if (!sidebar) return;

        if (state === 'collapsed') {
            sidebar.classList.remove('sidebar-expanded');
            sidebar.classList.add('sidebar-collapsed');
            isSidebarOpen = false;
        } else {
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.add('sidebar-expanded');
            isSidebarOpen = true;
        }

        localStorage.setItem(SIDEBAR_STATE_KEY, state);

        // When collapsing, close all dropdowns
        if (state === 'collapsed') {
            document.querySelectorAll('#sidebar-desktop .dropdown').forEach(d => d.classList.remove('active'));
        }
    }

    function toggleDesktopSidebar() {
        const nextState = isSidebarOpen ? 'collapsed' : 'expanded';
        setDesktopSidebarState(nextState);
    }

    function initializeDesktopSidebarState() {
        // On mobile width, keep desktop sidebar hidden like your existing logic
        if (window.innerWidth < 1024) return;

        const saved = localStorage.getItem(SIDEBAR_STATE_KEY);
        if (saved === 'collapsed' || saved === 'expanded') {
            setDesktopSidebarState(saved);
        } else {
            // default expanded
            setDesktopSidebarState('expanded');
        }
    }

    // --- Mobile Sidebar Management ---
    function openMobileSidebar() {
        mobileSidebar.classList.add('active');
        sidebarOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        mobileSidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // --- Dropdown Management (Accordion + Auto-open if active child) ---
    function closeOtherDropdowns(container, exceptDropdown) {
        container.querySelectorAll('.dropdown').forEach(d => {
            if (d !== exceptDropdown) d.classList.remove('active');
        });
    }

    function syncDropdownAria(dropdown) {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        if (!toggle) return;
        toggle.setAttribute('aria-expanded', dropdown.classList.contains('active') ? 'true' : 'false');
    }

    function openDropdownIfHasActiveLink(sidebarEl) {
        if (!sidebarEl) return;

        sidebarEl.querySelectorAll('.dropdown').forEach(dropdown => {
            const hasActiveChild = dropdown.querySelector('.dropdown-content .nav-link.active');
            if (hasActiveChild) {
                dropdown.classList.add('active');
                syncDropdownAria(dropdown);
            } else {
                syncDropdownAria(dropdown);
            }
        });
    }

    function initializeDropdowns() {
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

        dropdownToggles.forEach(toggle => {
            // click
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const dropdown = toggle.closest('.dropdown');
                const sidebarEl = dropdown.closest('.sidebar');

                // If desktop sidebar collapsed, don't open dropdown (icons-only mode)
                if (sidebarEl && sidebarEl.id === 'sidebar-desktop' && sidebarEl.classList.contains('sidebar-collapsed')) {
                    return;
                }

                // Accordion behavior inside same sidebar
                if (sidebarEl) closeOtherDropdowns(sidebarEl, dropdown);

                dropdown.classList.toggle('active');
                syncDropdownAria(dropdown);
            });

            // keyboard support (Enter / Space)
            toggle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggle.click();
                }
            });
        });

        // Close dropdowns when clicking outside (desktop)
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown').forEach(dropdown => {
                    dropdown.classList.remove('active');
                    syncDropdownAria(dropdown);
                });
            }
        });

        // Auto-open if active child exists (desktop + mobile)
        openDropdownIfHasActiveLink(document.getElementById('sidebar-desktop'));
        openDropdownIfHasActiveLink(document.getElementById('sidebar-mobile'));
    }

    // --- Slider Functionality ---
    function goToSlide(n) {
        currentSlide = (n + slides.length) % slides.length;
        updateSlider();
    }

    function nextSlide() { goToSlide(currentSlide + 1); }
    function prevSlide() { goToSlide(currentSlide - 1); }

    function updateSlider() {
        if (!slider) return;
        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
        dots.forEach((dot, index) => dot.classList.toggle('active', index === currentSlide));
    }

    // --- Initial Load and Event Listeners ---
    document.addEventListener('DOMContentLoaded', () => {
        initializeTheme();
        initializeDropdowns();
        initializeDesktopSidebarState();

        // Event listeners
        if (desktopToggle) desktopToggle.addEventListener('click', toggleDesktopSidebar);
        if (mobileToggle) mobileToggle.addEventListener('click', openMobileSidebar);
        if (mobileClose) mobileClose.addEventListener('click', closeMobileSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeMobileSidebar);
        if (themeToggle) themeToggle.addEventListener('click', toggleTheme);

        // Slider event listeners
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);
        if (nextBtn) nextBtn.addEventListener('click', nextSlide);

        dots.forEach((dot, index) => dot.addEventListener('click', () => goToSlide(index)));

        // Auto slide every 5 seconds
        if (slides.length) setInterval(nextSlide, 5000);

        // Animation cleanup
        const animatedItems = document.querySelectorAll('.fade-in-item');
        animatedItems.forEach(item => {
            item.addEventListener('animationend', () => {
                item.style.opacity = '1';
                item.style.transform = 'none';
                item.style.animation = 'none';
            });
        });
    });

    // Initial setup for desktop view (your existing logic kept)
    if (window.innerWidth < 1024) {
        isSidebarOpen = false;
        if (sidebar) {
            sidebar.style.display = 'none';
        }
    }
</script>

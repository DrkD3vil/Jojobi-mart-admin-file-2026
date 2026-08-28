import './bootstrap';
import Alpine from 'alpinejs';

/**
 * Theme: reads/writes documentElement[data-theme]. Absence of the attribute
 * means "follow the OS" -- app.css already defines that branch via
 * prefers-color-scheme, so this only needs to persist an explicit choice.
 */
Alpine.store('theme', {
    mode: localStorage.getItem('jojobi-theme') || 'system',

    init() {
        this.apply();
    },

    set(mode) {
        this.mode = mode;
        localStorage.setItem('jojobi-theme', mode);
        this.apply();
    },

    apply() {
        if (this.mode === 'system') {
            document.documentElement.removeAttribute('data-theme');
        } else {
            document.documentElement.setAttribute('data-theme', this.mode);
        }
    },
});

/**
 * Cart badge count, hydrated from the server-rendered layout so it's correct
 * on first paint, then updated in place by add/update/remove calls.
 */
Alpine.store('cart', {
    count: Number(document.documentElement.dataset.cartCount || 0),
    set(n) {
        this.count = n;
    },
});

/**
 * Lightweight toast queue any component can push into via
 * `$store.toast.push('Added to cart')`.
 */
Alpine.store('toast', {
    items: [],
    push(message, tone = 'good') {
        const id = Date.now() + Math.random();
        this.items.push({ id, message, tone });
        setTimeout(() => this.dismiss(id), 3200);
    },
    dismiss(id) {
        this.items = this.items.filter((t) => t.id !== id);
    },
});

// Scroll-reveal directive: x-reveal on any element toggles the shared
// .reveal/.in pair (see app.css) once it crosses into the viewport.
Alpine.directive('reveal', (el) => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        el.classList.add('in');
        return;
    }
    el.classList.add('reveal');
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    io.observe(el);
});

window.Alpine = Alpine;
Alpine.start();

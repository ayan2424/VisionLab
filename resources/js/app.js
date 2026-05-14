import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// ── Theme Manager ─────────────────────────────────────────────────
window.themeManager = {
    init() {
        const saved = localStorage.getItem('vc-theme');
        if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
    toggle() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('vc-theme', isDark ? 'dark' : 'light');
        // Dispatch event for any listening components
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: isDark } }));
    },
    get isDark() {
        return document.documentElement.classList.contains('dark');
    }
};

// Apply theme immediately (before Alpine starts)
window.themeManager.init();

// Listen for OS-level theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('vc-theme')) {
        if (e.matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
});

Alpine.start();

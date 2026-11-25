import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Global Dual-Light Theme Store (Obsidian Velvet Dark <-> Pearl Mist Light)
 * Syncs with localStorage('vl-theme'), <html class="dark">, meta[name="theme-color"],
 * and emits 'vl:theme-changed' so Chart.js & Three.js scenes update live.
 */
Alpine.store('theme', {
    preference: 'system',
    isDark: true,

    init() {
        const stored = localStorage.getItem('vl-theme');
        this.preference = stored === 'light' || stored === 'dark' ? stored : 'system';
        this.apply();

        const mq = window.matchMedia('(prefers-color-scheme: light)');
        mq.addEventListener('change', () => {
            if (this.preference === 'system') {
                this.apply();
            }
        });
    },

    resolveDark() {
        if (this.preference === 'dark') return true;
        if (this.preference === 'light') return false;
        return !window.matchMedia('(prefers-color-scheme: light)').matches;
    },

    apply() {
        this.isDark = this.resolveDark();
        document.documentElement.classList.toggle('dark', this.isDark);
        document.documentElement.style.colorScheme = this.isDark ? 'dark' : 'light';

        const meta = document.querySelector('meta[name="theme-color"]');
        if (meta) {
            meta.setAttribute('content', this.isDark ? '#090D14' : '#F6F9FC');
        }

        window.dispatchEvent(new CustomEvent('vl:theme-changed', {
            detail: { isDark: this.isDark, preference: this.preference },
        }));
    },

    set(mode) {
        this.preference = mode;
        if (mode === 'system') {
            localStorage.removeItem('vl-theme');
        } else {
            localStorage.setItem('vl-theme', mode);
        }
        this.apply();
    },

    toggle() {
        this.set(this.isDark ? 'light' : 'dark');
    },
});

Alpine.start();

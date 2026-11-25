import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,jsx,ts,tsx}',
    ],

    theme: {
        extend: {
            fontFamily: {
                display: ['"Plus Jakarta Sans"', '"Inter"', ...defaultTheme.fontFamily.sans],
                sans: ['"Inter"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                /* Luminous Atelier — RGB channel semantic tokens with full alpha modifier support */
                vl: {
                    bg: 'rgb(var(--vl-bg) / <alpha-value>)',
                    surface: 'rgb(var(--vl-surface) / <alpha-value>)',
                    'surface-raised': 'rgb(var(--vl-surface-raised) / <alpha-value>)',
                    rail: 'rgb(var(--vl-rail) / <alpha-value>)',
                    border: 'rgb(var(--vl-border) / <alpha-value>)',
                    'border-strong': 'rgb(var(--vl-border-strong) / <alpha-value>)',
                    ink: 'rgb(var(--vl-ink) / <alpha-value>)',
                    'ink-secondary': 'rgb(var(--vl-ink-secondary) / <alpha-value>)',
                    'ink-muted': 'rgb(var(--vl-ink-muted) / <alpha-value>)',
                    accent: 'rgb(var(--vl-accent) / <alpha-value>)',
                    'accent-cta': 'rgb(var(--vl-accent-cta) / <alpha-value>)',
                    'accent-fg': 'rgb(var(--vl-accent-fg) / <alpha-value>)',
                    teal: 'rgb(var(--vl-teal) / <alpha-value>)',
                    gate: 'rgb(var(--vl-gate) / <alpha-value>)',
                    danger: 'rgb(var(--vl-danger) / <alpha-value>)',
                    info: 'rgb(var(--vl-info) / <alpha-value>)',
                },
                /* Backward-compatibility aliases mapped cleanly to Luminous Atelier tokens */
                vc: {
                    bg: 'var(--vc-bg)',
                    surface: 'var(--vc-surface)',
                    elevated: 'var(--vc-elevated)',
                    border: 'var(--vc-border)',
                    ring: 'var(--vc-ring)',
                    text: 'var(--vc-text)',
                    'text-secondary': 'var(--vc-text-secondary)',
                    muted: 'var(--vc-muted)',
                    accent: 'var(--vc-accent)',
                    'accent-hover': 'var(--vc-accent-hover)',
                    card: 'var(--vc-card)',
                    'card-hover': 'var(--vc-card-hover)',
                    nav: 'var(--vc-nav)',
                    input: 'var(--vc-input)',
                    'input-border': 'var(--vc-input-border)',
                    'input-focus': 'var(--vc-input-focus)',
                },
            },
            boxShadow: {
                'vl-panel': 'var(--vl-panel-shadow)',
                'vl-panel-hover': 'var(--vl-panel-shadow-hover)',
                'vl-cta': 'var(--vl-cta-shadow)',
                'vl-overlay': 'var(--vl-overlay-shadow)',
            },
            transitionTimingFunction: {
                'vl-out': 'cubic-bezier(0.22, 1, 0.36, 1)',
                'vl-in-out': 'cubic-bezier(0.77, 0, 0.175, 1)',
                'vl-drawer': 'cubic-bezier(0.32, 0.72, 0, 1)',
            },
            keyframes: {
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                'vl-fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                shimmer: 'shimmer 2.2s linear infinite',
                'vl-fade-up': 'vl-fade-up 360ms cubic-bezier(0.22, 1, 0.36, 1) forwards',
            },
        },
    },

    plugins: [forms],
};

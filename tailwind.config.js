import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Space Grotesk', 'Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'Fira Code', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                /* Semantic tokens — resolved by CSS vars set in app.css */
                vc: {
                    bg:       'var(--vc-bg)',
                    surface:  'var(--vc-surface)',
                    elevated: 'var(--vc-elevated)',
                    border:   'var(--vc-border)',
                    ring:     'var(--vc-ring)',
                    text:     'var(--vc-text)',
                    'text-secondary': 'var(--vc-text-secondary)',
                    muted:    'var(--vc-muted)',
                    accent:   'var(--vc-accent)',
                    'accent-hover': 'var(--vc-accent-hover)',
                    card:     'var(--vc-card)',
                    'card-hover': 'var(--vc-card-hover)',
                    nav:      'var(--vc-nav)',
                    input:    'var(--vc-input)',
                    'input-border': 'var(--vc-input-border)',
                    'input-focus': 'var(--vc-input-focus)',
                },
                /* Static brand colors */
                brand: {
                    50:  '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                    950: '#1e1b4b',
                },
                /* Keep tailwind defaults for violet, cyan, etc. */
                void:    '#0a0a0a',
                surface: '#111111',
                elevated:'#161b22',
                border:  '#21262d',
                muted:   '#64748b',
            },
            boxShadow: {
                'vc-sm':     'var(--vc-shadow-sm)',
                'vc-md':     'var(--vc-shadow-md)',
                'vc-lg':     'var(--vc-shadow-lg)',
                'glow-brand': '0 0 24px rgba(240,80,0,0.35), 0 0 48px rgba(255,129,71,0.12)',
                'glow-sm':    '0 0 12px rgba(240,80,0,0.3)',
                'inner-glow': 'inset 0 1px 0 rgba(255,255,255,0.07)',
                'card':       '0 4px 24px rgba(0,0,0,0.12)',
                'card-hover': '0 8px 40px rgba(0,0,0,0.18)',
            },
            keyframes: {
                'fade-up': {
                    '0%':   { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%':   { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'slide-in-right': {
                    '0%':   { opacity: '0', transform: 'translateX(24px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                'blob': {
                    '0%,100%': { transform: 'translate(0,0) scale(1)' },
                    '33%':     { transform: 'translate(30px,-50px) scale(1.1)' },
                    '66%':     { transform: 'translate(-20px,20px) scale(0.9)' },
                },
                'shimmer': {
                    '0%':   { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                'blink': {
                    '0%,100%': { opacity: '1' },
                    '50%':     { opacity: '0' },
                },
            },
            animation: {
                'fade-up':         'fade-up 0.5s ease forwards',
                'fade-up-delay-1': 'fade-up 0.5s 0.1s ease forwards',
                'fade-up-delay-2': 'fade-up 0.5s 0.2s ease forwards',
                'fade-up-delay-3': 'fade-up 0.5s 0.3s ease forwards',
                'fade-up-delay-4': 'fade-up 0.5s 0.4s ease forwards',
                'fade-in':         'fade-in 0.6s ease forwards',
                'slide-in-right':  'slide-in-right 0.5s ease forwards',
                'blob':            'blob 8s infinite ease-in-out',
                'shimmer':         'shimmer 2.5s linear infinite',
                'blink':           'blink 1s step-end infinite',
            },
            transitionTimingFunction: {
                'premium': 'cubic-bezier(0.16, 1, 0.3, 1)',
            },
        },
    },

    plugins: [forms],
};

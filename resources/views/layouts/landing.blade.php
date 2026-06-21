<!DOCTYPE html>
<html lang="en" class="scroll-smooth dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'VisionLab is an enterprise-grade collaborative coding and learning platform for research universities.')">
    <title>@yield('title', 'VisionLab — Collaborative Coding for Universities')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Three.js via Import Map -->
    <script type="importmap">
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.164.1/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.164.1/examples/jsm/"
        }
    }
    </script>

    {{-- Force Dark Mode Temporarily --}}
    

    <!-- Design System Styles -->
    <style>
        /* ── Forced Dark Mode ───────────────────────────────────────────────────────────── */
        :root {
            --background: #050507;
            --foreground: #f4f4f5;
            --surface: rgba(255, 255, 255, 0.03);
            --surface-2: rgba(255, 255, 255, 0.06);
            --muted: rgba(255, 255, 255, 0.04);
            --muted-foreground: rgba(244, 244, 245, 0.6);
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(255, 255, 255, 0.16);
            --indigo: #4f46e5;
            --indigo-light: #818cf8;
            --rose: #f0426d;
            --rose-light: #fb7c97;
            --cyan: #17c3d6;
            --cyan-light: #6ee7e0;
            --cyan-glow: rgba(23, 195, 214, 0.3);
            --emerald: #00bfa6;
            --emerald-light: #5eeac9;
            --violet: #9b5de5;
            --violet-light: #c7a6f5;
            --ease-out-expo: cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background: var(--background);
            color: var(--foreground);
            font-family: 'Geist', -apple-system, sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
            background-image: linear-gradient(-45deg, #050507, #0b0716, #050a12, #050507);
            background-size: 400% 400%;
            animation: gradientBg 18s ease infinite;
        }

        @keyframes gradientBg {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .font-display {
            font-family: "Clash Display", Inter, sans-serif;
        }

        .font-mono {
            font-family: "JetBrains Mono", ui-monospace, monospace;
        }

        .font-serif-italic {
            font-family: "Instrument Serif", serif;
            font-style: italic;
        }

        /* Text Effects */
        .metallic-text {
            -webkit-text-fill-color: transparent;
            color: rgba(0, 0, 0, 0);
            background: linear-gradient(rgb(255, 255, 255) 0%, rgb(229, 231, 235) 40%, rgb(148, 163, 184) 100%) text;
            -webkit-background-clip: text;
            background-clip: text;
        }

        .aurora-text {
            -webkit-text-fill-color: transparent;
            color: rgba(0, 0, 0, 0);
            background-image: linear-gradient(110deg, var(--cyan) 0%, var(--violet) 25%, var(--rose) 50%, var(--indigo) 75%, var(--emerald) 100%);
            background-size: 250% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            animation: aurora 8s ease-in-out infinite;
        }

        @keyframes aurora {
            0%, 100% { background-position: 0% center; }
            50% { background-position: 100% center; }
        }

        /* Glass Panel */
        .glass-panel {
            backdrop-filter: blur(14px) saturate(140%);
            background: linear-gradient(rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
            border: 1px solid rgba(255, 255, 255, 0.07);
            box-shadow: rgba(255, 255, 255, 0.02) 0px 1px inset, rgba(0, 0, 0, 0.6) 0px 20px 50px -20px;
            border-radius: 1rem;
            transition: border-color 0.3s var(--ease-out-expo), background-color 0.3s var(--ease-out-expo);
        }
        .glass-panel:hover {
            border-color: rgba(255, 255, 255, 0.14);
            background-color: rgba(255, 255, 255, 0.035);
        }

        /* Cyber Grid Floor */
        .grid-floor {
            background-image: linear-gradient(90deg, rgba(23, 195, 214, 0.08) 1px, rgba(0, 0, 0, 0) 1px),
                linear-gradient(rgba(23, 195, 214, 0.08) 1px, rgba(0, 0, 0, 0) 1px);
            background-size: 55px 55px;
        }

        /* Reveal Scroll */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.8s var(--ease-out-expo), transform 0.8s var(--ease-out-expo);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0px);
        }

        /* Navigation Header */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(5, 5, 7, 0.7); backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border); transition: all 0.3s;
        }
        .nav-inner {
            max-width: 1280px; margin: 0 auto; padding: 0 2rem; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .nav-links { display: none; align-items: center; gap: 2.2rem; }
        @media (min-width: 768px) { .nav-links { display: flex; } }
        
        .nav-links a {
            color: var(--muted-foreground); text-decoration: none; font-size: 11px;
            font-family: 'JetBrains Mono', monospace; font-weight: 500;
            text-transform: uppercase; letter-spacing: 0.2em; transition: color 0.2s, text-shadow 0.2s;
        }
        .nav-links a:hover, .nav-links a.active {
            color: #fff;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        /* Buttons matching welcome.blade.php */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.65rem 1.6rem; font-size: 11px; font-family: 'JetBrains Mono', monospace;
            font-weight: 500; text-transform: uppercase; letter-spacing: 0.2em;
            border-radius: 9999px; text-decoration: none;
            transition: all 0.3s var(--ease-out-expo); cursor: pointer; border: none; white-space: nowrap;
        }
        .btn-primary {
            background: var(--cyan); color: #000;
            box-shadow: 0 0 15px var(--cyan-glow), 0 0 45px rgba(23,195,214,0.1);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px var(--cyan-glow), 0 0 60px rgba(23,195,214,0.25);
            background: var(--cyan-light);
        }
        .btn-secondary {
            background: transparent; color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .btn-secondary:hover {
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.03);
            transform: translateY(-1px);
        }

        /* Footer layout */
        .footer { border-top: 1px solid var(--border); padding: 4rem 2rem; background: rgba(0, 0, 0, 0.3); position: relative; z-index: 10; }
        .footer-inner { max-width: 1280px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem; align-items: center; justify-content: space-between; }
        @media (min-width: 768px) { .footer-inner { flex-direction: row; } }
        .footer-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; }
        .footer-links a {
            color: var(--muted-foreground); text-decoration: none; font-size: 10px;
            font-family: 'JetBrains Mono', monospace; font-weight: 500;
            text-transform: uppercase; letter-spacing: 0.22em; transition: color 0.2s;
        }
        .footer-links a:hover { color: #fff; }

        /* Cursor dot and ring */
        .cursor-dot { position: fixed; width: 6px; height: 6px; background: var(--cyan); border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%,-50%); box-shadow: 0 0 12px var(--cyan); display: none; }
        .cursor-ring { position: fixed; width: 36px; height: 36px; border: 1.5px solid rgba(23,195,214,0.4); border-radius: 50%; pointer-events: none; z-index: 9998; transform: translate(-50%,-50%); transition: all 0.15s cubic-bezier(0.16,1,0.3,1); display: none; }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.4); }
        }

        @keyframes float-y {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
    </style>
    @yield('styles')
</head>
<body class="selection:bg-cyan/30 selection:text-white">

    <!-- Cursor -->
    <div class="cursor-dot" id="cursorDot"></div>
    <div class="cursor-ring" id="cursorRing"></div>

    <!-- Navigation -->
    <x-frontend-header />

    <!-- Main Content Yield -->
    <div class="relative min-h-screen pt-20">
        @yield('content')
    </div>

    <!-- Footer -->
    <x-frontend-footer />

    <!-- Global Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ── Mouse Cursor ──
            const dot = document.getElementById('cursorDot');
            const ring = document.getElementById('cursorRing');
            if (window.matchMedia('(pointer: fine)').matches) {
                dot.style.display = 'block';
                ring.style.display = 'block';
                document.addEventListener('mousemove', e => {
                    dot.style.left = e.clientX + 'px';
                    dot.style.top = e.clientY + 'px';
                    setTimeout(() => {
                        ring.style.left = e.clientX + 'px';
                        ring.style.top = e.clientY + 'px';
                    }, 50);
                });
            }

            // ── Scroll Reveal ──
            const reveals = document.querySelectorAll('.reveal');
            const revealOnScroll = () => {
                reveals.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    const windowHeight = window.innerHeight;
                    if (rect.top < windowHeight * 0.88) {
                        el.classList.add('visible');
                    }
                });
            };
            window.addEventListener('scroll', revealOnScroll);
            revealOnScroll(); // Trigger once on load
        });
    </script>
    @yield('scripts')
</body>
</html>

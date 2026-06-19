<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="VisionLab — The AI-Powered Collaborative Coding Ecosystem for universities. Sandboxed workspaces, real-time collaboration, and a 3-mode AI Agent in the browser.">
    <meta name="keywords" content="VisionLab, collaborative coding, AI IDE, university coding platform, code-server, real-time collaboration">
    <title>VisionLab — Code. Collaborate. Conquer.</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════════════════════════════════
           LANDING PAGE — PREMIUM DARK DESIGN SYSTEM
           ═══════════════════════════════════════════════════════════════════════ */

        :root {
            --landing-bg: #050507;
            --landing-surface: #0c0c10;
            --landing-card: rgba(255,255,255,0.03);
            --landing-card-border: rgba(255,255,255,0.06);
            --landing-card-hover: rgba(255,255,255,0.07);
            --landing-text: #f0f0f5;
            --landing-text-secondary: #8b8b9e;
            --landing-muted: #4a4a5e;
            --landing-accent-violet: #8b5cf6;
            --landing-accent-cyan: #06b6d4;
            --landing-accent-pink: #ec4899;
            --landing-accent-emerald: #10b981;
            --landing-glow-violet: rgba(139,92,246,0.15);
            --landing-glow-cyan: rgba(6,182,212,0.12);
        }

        /* ── Global Overrides for Landing ───────────────────────────────── */
        body.landing-page {
            background: var(--landing-bg);
            color: var(--landing-text);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }

        /* ── Animated Gradient Mesh Backgrounds ────────────────────────── */
        .hero-mesh {
            background:
                radial-gradient(ellipse 120% 80% at 10% -10%, rgba(139,92,246,0.18) 0%, transparent 55%),
                radial-gradient(ellipse 80% 100% at 90% 110%, rgba(6,182,212,0.12) 0%, transparent 50%),
                radial-gradient(ellipse 60% 60% at 50% 20%, rgba(236,72,153,0.08) 0%, transparent 60%),
                radial-gradient(circle at 30% 80%, rgba(16,185,129,0.06) 0%, transparent 40%),
                var(--landing-bg);
        }

        .section-mesh-violet {
            background:
                radial-gradient(ellipse 80% 60% at 80% 0%, rgba(139,92,246,0.1) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 0% 100%, rgba(6,182,212,0.06) 0%, transparent 50%),
                var(--landing-bg);
        }

        .section-mesh-cyan {
            background:
                radial-gradient(ellipse 90% 60% at 20% 0%, rgba(6,182,212,0.12) 0%, transparent 55%),
                radial-gradient(ellipse 50% 80% at 90% 100%, rgba(139,92,246,0.06) 0%, transparent 50%),
                var(--landing-bg);
        }

        .section-mesh-pink {
            background:
                radial-gradient(ellipse 80% 60% at 60% 0%, rgba(236,72,153,0.1) 0%, transparent 55%),
                radial-gradient(ellipse 60% 80% at 10% 80%, rgba(139,92,246,0.08) 0%, transparent 50%),
                var(--landing-bg);
        }

        /* ── Neural Network Canvas ────────────────────────────────────── */
        #neural-canvas {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 1;
        }

        /* ── Ambient Glow Orbs ────────────────────────────────────────── */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            will-change: transform;
        }

        .glow-orb.animate {
            animation: orb-float 12s ease-in-out infinite;
        }

        /* ── Grid Pattern Background ──────────────────────────────────── */
        .grid-pattern {
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* ── Gradient Borders ─────────────────────────────────────────── */
        .gradient-border {
            position: relative;
        }
        .gradient-border::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(139,92,246,0.4), rgba(6,182,212,0.2), rgba(236,72,153,0.3));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        /* ── Animated Border Spin ─────────────────────────────────────── */
        @property --border-angle {
            syntax: '<angle>';
            initial-value: 0deg;
            inherits: false;
        }

        .spinning-border {
            --border-angle: 0deg;
            background: conic-gradient(from var(--border-angle),
                transparent 60%,
                rgba(139,92,246,0.6),
                rgba(6,182,212,0.5),
                rgba(236,72,153,0.4),
                transparent);
            animation: spin-border 6s linear infinite;
        }

        @keyframes spin-border {
            to { --border-angle: 360deg; }
        }

        /* ── Text Gradient Variants ───────────────────────────────────── */
        .text-gradient-hero {
            background: linear-gradient(135deg, #c084fc 0%, #818cf8 30%, #22d3ee 60%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-gradient-violet-pink {
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-gradient-cyan-green {
            background: linear-gradient(135deg, #06b6d4 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Glassmorphism Cards ───────────────────────────────────────── */
        .glass-card {
            background: var(--landing-card);
            border: 1px solid var(--landing-card-border);
            border-radius: 1.25rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .glass-card:hover {
            background: var(--landing-card-hover);
            border-color: rgba(139,92,246,0.2);
            box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 40px rgba(139,92,246,0.06);
            transform: translateY(-4px);
        }

        /* ── Feature Bento Card Variants ──────────────────────────────── */
        .bento-card {
            background: var(--landing-card);
            border: 1px solid var(--landing-card-border);
            border-radius: 1.5rem;
            backdrop-filter: blur(16px);
            overflow: hidden;
            position: relative;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }

        .bento-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(600px circle at var(--mouse-x, 50%) var(--mouse-y, 50%),
                rgba(139,92,246,0.06), transparent 40%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.4s;
        }

        .bento-card:hover::after {
            opacity: 1;
        }

        .bento-card:hover {
            border-color: rgba(139,92,246,0.25);
            box-shadow: 0 30px 80px rgba(0,0,0,0.5), 0 0 50px rgba(139,92,246,0.05);
        }

        /* ── Magnetic Button ──────────────────────────────────────────── */
        .btn-magnetic {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2.25rem;
            border-radius: 0.875rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #fff;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
        }

        .btn-magnetic.primary {
            background: linear-gradient(135deg, #7c3aed, #6366f1, #8b5cf6);
            border: 1px solid rgba(139,92,246,0.5);
            box-shadow: 0 0 30px rgba(139,92,246,0.2), inset 0 1px 0 rgba(255,255,255,0.1);
        }

        .btn-magnetic.primary:hover {
            box-shadow: 0 0 50px rgba(139,92,246,0.35), 0 10px 40px rgba(139,92,246,0.2);
            transform: translateY(-2px);
        }

        .btn-magnetic.primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .btn-magnetic.secondary {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.12);
            color: var(--landing-text-secondary);
        }

        .btn-magnetic.secondary:hover {
            border-color: rgba(139,92,246,0.4);
            color: #fff;
            background: rgba(139,92,246,0.08);
        }

        /* ── IDE Mockup ───────────────────────────────────────────────── */
        .ide-container {
            border-radius: 1.25rem;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.08);
            background: #0d0d12;
            box-shadow: 0 60px 120px rgba(0,0,0,0.8), 0 0 80px rgba(139,92,246,0.05);
        }

        .ide-titlebar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            background: rgba(255,255,255,0.03);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .ide-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .code-line {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            line-height: 1.75;
            white-space: pre;
        }

        /* ── Mode Switcher ────────────────────────────────────────────── */
        .mode-tab {
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--landing-muted);
            background: transparent;
            border: none;
        }

        .mode-tab.active {
            color: #fff;
            background: linear-gradient(135deg, #7c3aed, #6366f1);
            box-shadow: 0 4px 16px rgba(139,92,246,0.3);
        }

        .mode-tab:hover:not(.active) {
            color: var(--landing-text-secondary);
            background: rgba(255,255,255,0.04);
        }

        /* ── Scrolling Logo Strip ─────────────────────────────────────── */
        .logo-scroll-track {
            display: flex;
            gap: 4rem;
            animation: scroll-logos 30s linear infinite;
            width: max-content;
        }

        .logo-scroll-track:hover {
            animation-play-state: paused;
        }

        @keyframes scroll-logos {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* ── Typing Cursor ────────────────────────────────────────────── */
        .typing-cursor {
            display: inline-block;
            width: 2px;
            height: 18px;
            background: #8b5cf6;
            animation: cursor-blink 1s step-end infinite;
            vertical-align: text-bottom;
            margin-left: 1px;
        }

        @keyframes cursor-blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        /* ── Floating Animation Variants ──────────────────────────────── */
        @keyframes orb-float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25%      { transform: translate(30px, -40px) scale(1.05); }
            50%      { transform: translate(-20px, -20px) scale(0.95); }
            75%      { transform: translate(15px, 30px) scale(1.02); }
        }

        @keyframes float-gentle {
            0%, 100% { transform: translateY(0px); }
            50%      { transform: translateY(-15px); }
        }

        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50%      { transform: translateY(-20px) rotate(2deg); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(139,92,246,0.2); }
            50%      { box-shadow: 0 0 40px rgba(139,92,246,0.4), 0 0 80px rgba(139,92,246,0.1); }
        }

        @keyframes scan-down {
            0%   { top: -2px; opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }

        @keyframes gradient-x {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes reveal-up {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes reveal-scale {
            from { opacity: 0; transform: scale(0.92) translateY(20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        @keyframes hero-image-float {
            0%, 100% { transform: translateY(0) rotate3d(0,1,0,0deg); }
            25%      { transform: translateY(-12px) rotate3d(0,1,0,3deg); }
            75%      { transform: translateY(-6px) rotate3d(0,1,0,-2deg); }
        }

        @keyframes shimmer-fast {
            0%   { left: -100%; }
            100% { left: 200%; }
        }

        @keyframes check-in {
            0%   { transform: scale(0) rotate(-45deg); opacity: 0; }
            60%  { transform: scale(1.2) rotate(0deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }

        /* ── Scroll Reveal ────────────────────────────────────────────── */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-scale {
            opacity: 0;
            transform: scale(0.92) translateY(20px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal-scale.visible {
            opacity: 1;
            transform: scale(1) translateY(0);
        }

        /* ── Achievement Badge ────────────────────────────────────────── */
        .achievement-badge {
            width: 72px;
            height: 72px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            position: relative;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .achievement-badge:hover {
            transform: translateY(-6px) scale(1.1);
        }

        /* ── Heatmap Cell ─────────────────────────────────────────────── */
        .heatmap-cell {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            transition: all 0.2s ease;
        }

        .heatmap-cell:hover {
            transform: scale(1.8);
            box-shadow: 0 0 8px currentColor;
        }

        /* ── Security Checklist ───────────────────────────────────────── */
        .security-check {
            opacity: 0;
            transform: translateX(-20px);
        }

        .security-check.checked {
            opacity: 1;
            transform: translateX(0);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .security-check .check-icon {
            transform: scale(0);
        }

        .security-check.checked .check-icon {
            animation: check-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* ── Navbar Glassmorphism ─────────────────────────────────────── */
        .navbar-glass {
            background: rgba(5,5,7,0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }

        .navbar-solid {
            background: rgba(5,5,7,0.92);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 4px 30px rgba(0,0,0,0.3);
        }

        /* ── Responsive ───────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .hero-headline { font-size: 2.5rem !important; }
            .bento-grid { grid-template-columns: 1fr !important; }
            .hide-mobile { display: none !important; }
        }

        @media (max-width: 1024px) {
            .hero-headline { font-size: 3.5rem !important; }
        }

        /* ── Reduced Motion ───────────────────────────────────────────── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ── Custom Scrollbar ─────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--landing-bg); }
        ::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.3); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(139,92,246,0.5); }

        /* ── Stat Number ──────────────────────────────────────────────── */
        .stat-number {
            font-variant-numeric: tabular-nums;
            font-feature-settings: 'tnum';
        }

        /* ── Image Hover Parallax ─────────────────────────────────────── */
        .parallax-image {
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .parallax-image:hover {
            transform: scale(1.03);
        }

        /* ── Footer Gradient Line ─────────────────────────────────────── */
        .footer-gradient-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(139,92,246,0.5), rgba(6,182,212,0.5), rgba(236,72,153,0.3), transparent);
        }

        /* ── Toast Notification Mockup ────────────────────────────────── */
        .mock-toast {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 0.5rem;
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="landing-page antialiased">

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 1: NAVBAR
     ═══════════════════════════════════════════════════════════════════════════ -->
<header id="landing-navbar" class="fixed top-0 left-0 right-0 z-50 navbar-glass transition-all duration-500">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <!-- Logo -->
        <a href="#" class="flex items-center gap-2.5 group">
            <x-logo size="h-8 w-8" />
        </a>

        <!-- Nav Links -->
        <nav class="hidden md:flex items-center gap-8">
            <a href="#features" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors duration-200">Features</a>
            <a href="#ide-showcase" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors duration-200">IDE</a>
            <a href="#ai-agent" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors duration-200">AI Agent</a>
            <a href="#collaboration" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors duration-200">Collaborate</a>
            <a href="#security" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors duration-200">Security</a>
        </nav>

        <!-- CTAs -->
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-magnetic primary text-sm py-2 px-5">
                    Dashboard
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors duration-200 hidden sm:block">Sign In</a>
                <a href="{{ route('register') }}" class="btn-magnetic primary text-sm py-2 px-5">
                    Get Started
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            @endauth
        </div>
    </div>
</header>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 2: HERO
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="hero-mesh relative min-h-screen flex items-center justify-center overflow-hidden pt-16">
    <!-- Neural Network Canvas -->
    <canvas id="neural-canvas"></canvas>

    <!-- Ambient Glow Orbs -->
    <div class="glow-orb animate w-[700px] h-[700px] top-[-200px] left-[-150px]" style="background:rgba(139,92,246,0.12)"></div>
    <div class="glow-orb animate w-[500px] h-[500px] bottom-[-100px] right-[-100px]" style="background:rgba(6,182,212,0.08); animation-delay:-4s"></div>
    <div class="glow-orb animate w-[400px] h-[400px] top-[30%] right-[10%]" style="background:rgba(236,72,153,0.06); animation-delay:-8s"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 py-20 flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
        <!-- Left: Content -->
        <div class="flex-1 text-center lg:text-left">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-violet-500/20 bg-violet-500/[0.07] text-violet-300 text-xs font-medium mb-8 reveal" style="transition-delay:0.1s">
                <span class="w-2 h-2 rounded-full bg-violet-400 animate-pulse"></span>
                Aptech Vision 2026 — Competition Entry
                <svg class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>

            <!-- Headline -->
            <h1 class="hero-headline text-5xl md:text-6xl lg:text-7xl font-black leading-[1.05] tracking-tight mb-6 reveal" style="transition-delay:0.2s">
                <span class="text-white">Code the</span><br>
                <span class="text-gradient-hero">Future of</span><br>
                <span class="text-white">Higher Learning.</span>
            </h1>

            <!-- Sub -->
            <p class="text-lg md:text-xl text-[var(--landing-text-secondary)] max-w-xl mb-10 leading-relaxed reveal" style="transition-delay:0.35s">
                Sandboxed workspaces, real-time multi-cursor collaboration, and a <span class="text-white font-medium">3-mode AI Agent</span> — engineered for research universities. The full VS Code IDE in your browser.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center lg:items-start gap-4 mb-12 reveal" style="transition-delay:0.5s">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-magnetic primary">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Open Workspace
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn-magnetic primary">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Start Coding Free
                    </a>
                    <a href="{{ route('login') }}" class="btn-magnetic secondary">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Live Demo
                    </a>
                @endauth
            </div>

            <!-- Stats -->
            <div class="flex flex-wrap justify-center lg:justify-start gap-8 reveal" style="transition-delay:0.65s">
                <div class="text-center lg:text-left">
                    <div class="text-3xl font-black text-white stat-number" data-target="3" data-suffix="+">0</div>
                    <div class="text-xs text-[var(--landing-muted)] mt-1">AI Modes</div>
                </div>
                <div class="w-px h-12 bg-[var(--landing-card-border)] hidden sm:block"></div>
                <div class="text-center lg:text-left">
                    <div class="text-3xl font-black text-gradient-violet-pink stat-number" data-target="40" data-suffix="+">0</div>
                    <div class="text-xs text-[var(--landing-muted)] mt-1">Languages</div>
                </div>
                <div class="w-px h-12 bg-[var(--landing-card-border)] hidden sm:block"></div>
                <div class="text-center lg:text-left">
                    <div class="text-3xl font-black text-gradient-cyan-green stat-number" data-target="100" data-suffix="%">0</div>
                    <div class="text-xs text-[var(--landing-muted)] mt-1">Real-Time</div>
                </div>
            </div>
        </div>

        <!-- Right: Hero Visual -->
        <div class="flex-1 relative reveal-scale" style="transition-delay:0.4s">
            <div class="relative" style="animation: hero-image-float 8s ease-in-out infinite">
                <!-- Glow behind image -->
                <div class="absolute inset-0 rounded-3xl" style="background:radial-gradient(circle at center, rgba(139,92,246,0.15) 0%, transparent 70%); filter:blur(40px); transform:scale(1.2)"></div>
                <!-- Hero Image -->
                <img src="{{ asset('images/landing/hero-bg.png') }}" alt="VisionLab Futuristic AI Lab" class="relative rounded-3xl shadow-2xl w-full max-w-lg mx-auto parallax-image" style="border:1px solid rgba(255,255,255,0.08)">
                <!-- Floating Badge -->
                <div class="absolute -bottom-4 -left-4 glass-card px-4 py-3 flex items-center gap-3" style="animation: float-gentle 4s ease-in-out infinite">
                    <div class="w-10 h-10 rounded-xl bg-violet-500/20 border border-violet-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-white">AI Agent</div>
                        <div class="text-[10px] text-emerald-400">● Active</div>
                    </div>
                </div>
                <!-- Floating Badge Right -->
                <div class="absolute -top-4 -right-4 glass-card px-4 py-3 hidden md:flex items-center gap-3" style="animation: float-gentle 5s ease-in-out infinite; animation-delay: -2s">
                    <div class="flex -space-x-2">
                        <div class="w-7 h-7 rounded-full bg-violet-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-[10px] font-bold text-white">A</div>
                        <div class="w-7 h-7 rounded-full bg-cyan-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-[10px] font-bold text-white">J</div>
                        <div class="w-7 h-7 rounded-full bg-emerald-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-[10px] font-bold text-white">S</div>
                    </div>
                    <div class="text-xs text-white font-medium">3 Online</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 reveal" style="transition-delay:1.2s">
        <span class="text-[10px] text-[var(--landing-muted)] uppercase tracking-[0.2em]">Scroll to explore</span>
        <div class="w-5 h-8 rounded-full border border-[var(--landing-card-border)] flex items-start justify-center p-1">
            <div class="w-1 h-2 rounded-full bg-violet-400" style="animation: float-gentle 2s ease-in-out infinite"></div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 3: TRUSTED BY — Logo Strip
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="relative py-16 border-y border-[var(--landing-card-border)] overflow-hidden" style="background:rgba(255,255,255,0.01)">
    <div class="max-w-7xl mx-auto px-6 mb-8">
        <p class="text-center text-xs text-[var(--landing-muted)] uppercase tracking-[0.2em]">Built for the world's leading institutions</p>
    </div>
    <div class="relative overflow-hidden">
        <div class="logo-scroll-track">
            <!-- Logos repeated for infinite scroll effect -->
            @for($i = 0; $i < 2; $i++)
            <div class="flex items-center gap-12 text-[var(--landing-muted)] opacity-40">
                <span class="text-lg font-bold whitespace-nowrap">🏛️ MIT</span>
                <span class="text-lg font-bold whitespace-nowrap">🎓 Stanford</span>
                <span class="text-lg font-bold whitespace-nowrap">🏫 Harvard</span>
                <span class="text-lg font-bold whitespace-nowrap">🔬 Caltech</span>
                <span class="text-lg font-bold whitespace-nowrap">🌍 Oxford</span>
                <span class="text-lg font-bold whitespace-nowrap">📚 Cambridge</span>
                <span class="text-lg font-bold whitespace-nowrap">⚡ ETH Zürich</span>
                <span class="text-lg font-bold whitespace-nowrap">🏗️ Georgia Tech</span>
                <span class="text-lg font-bold whitespace-nowrap">🔭 Carnegie Mellon</span>
                <span class="text-lg font-bold whitespace-nowrap">💡 Berkeley</span>
            </div>
            @endfor
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 4: FEATURES — Bento Grid
     ═══════════════════════════════════════════════════════════════════════════ -->
<section id="features" class="section-mesh-violet relative py-32 px-6 overflow-hidden">
    <div class="grid-pattern absolute inset-0 pointer-events-none opacity-40"></div>

    <div class="relative max-w-7xl mx-auto">
        <!-- Section Header -->
        <div class="text-center mb-20 reveal">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-violet-500/20 bg-violet-500/[0.06] text-violet-300 text-xs font-medium mb-6">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Core Capabilities
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-5 leading-tight">
                Five primitives.<br>
                <span class="text-gradient-hero">One platform.</span>
            </h2>
            <p class="text-lg text-[var(--landing-text-secondary)] max-w-2xl mx-auto">
                Every component engineered for peak productivity — from the VS Code IDE to the sandboxed AI that writes and reviews code autonomously.
            </p>
        </div>

        <!-- Bento Grid -->
        <div class="bento-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            <!-- Card 1: VS Code IDE (Large — spans 2 cols) -->
            <div class="bento-card p-8 md:col-span-2 lg:col-span-2 reveal" data-delay="0">
                <div class="flex flex-col lg:flex-row items-start gap-8">
                    <div class="flex-1">
                        <div class="w-14 h-14 rounded-2xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Full VS Code IDE in Browser</h3>
                        <p class="text-[var(--landing-text-secondary)] text-sm leading-relaxed mb-5">
                            Powered by code-server, every student gets the complete VS Code experience — IntelliSense, integrated terminal, extensions marketplace, debugger, and Git — running entirely in their browser. Zero local setup required.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[11px] px-3 py-1.5 rounded-full bg-white/[0.04] border border-[var(--landing-card-border)] text-[var(--landing-text-secondary)]">IntelliSense</span>
                            <span class="text-[11px] px-3 py-1.5 rounded-full bg-white/[0.04] border border-[var(--landing-card-border)] text-[var(--landing-text-secondary)]">Terminal</span>
                            <span class="text-[11px] px-3 py-1.5 rounded-full bg-white/[0.04] border border-[var(--landing-card-border)] text-[var(--landing-text-secondary)]">Extensions</span>
                            <span class="text-[11px] px-3 py-1.5 rounded-full bg-white/[0.04] border border-[var(--landing-card-border)] text-[var(--landing-text-secondary)]">Debugger</span>
                            <span class="text-[11px] px-3 py-1.5 rounded-full bg-white/[0.04] border border-[var(--landing-card-border)] text-[var(--landing-text-secondary)]">Git</span>
                        </div>
                    </div>
                    <div class="flex-1 w-full">
                        <!-- Mini IDE Preview -->
                        <div class="ide-container text-xs">
                            <div class="ide-titlebar">
                                <div class="flex gap-1.5">
                                    <div class="ide-dot" style="background:rgba(255,95,86,0.7)"></div>
                                    <div class="ide-dot" style="background:rgba(255,189,46,0.7)"></div>
                                    <div class="ide-dot" style="background:rgba(39,201,63,0.7)"></div>
                                </div>
                                <div class="flex-1 text-center text-[10px] text-[var(--landing-muted)] font-mono">main.py — VisionLab</div>
                            </div>
                            <div class="p-4 font-mono text-xs leading-relaxed" style="min-height:140px">
                                <div><span class="text-violet-400">from</span> <span class="text-cyan-400">visionlab</span> <span class="text-violet-400">import</span> <span class="text-slate-300">Agent</span></div>
                                <div class="mt-1"><span class="text-slate-500"># AI-powered workspace</span></div>
                                <div><span class="text-cyan-400">agent</span> <span class="text-slate-500">=</span> <span class="text-cyan-400">Agent</span><span class="text-slate-400">(</span><span class="text-emerald-400">"gemini"</span><span class="text-slate-400">)</span></div>
                                <div><span class="text-violet-400">await</span> <span class="text-cyan-400">agent</span><span class="text-slate-400">.</span><span class="text-cyan-400">analyze</span><span class="text-slate-400">(</span><span class="text-emerald-400">"./src"</span><span class="text-slate-400">)</span></div>
                                <div id="typing-target" class="mt-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Real-Time Collaboration -->
            <div class="bento-card p-7 reveal" data-delay="100">
                <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Multi-Cursor Sync</h3>
                <p class="text-[var(--landing-text-secondary)] text-sm leading-relaxed mb-4">
                    Sub-frame latency cursors with faculty governance baked into the protocol layer. See every collaborator in real-time.
                </p>
                <div class="flex -space-x-3">
                    <div class="w-9 h-9 rounded-full bg-violet-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-xs font-bold text-white">A</div>
                    <div class="w-9 h-9 rounded-full bg-cyan-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-xs font-bold text-white">J</div>
                    <div class="w-9 h-9 rounded-full bg-emerald-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-xs font-bold text-white">S</div>
                    <div class="w-9 h-9 rounded-full bg-pink-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-xs font-bold text-white">M</div>
                    <div class="w-9 h-9 rounded-full bg-[var(--landing-card-border)] border-2 border-[var(--landing-bg)] flex items-center justify-center text-[10px] text-[var(--landing-muted)]">+20</div>
                </div>
            </div>

            <!-- Card 3: Responsible AI -->
            <div class="bento-card p-7 reveal" data-delay="200">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Responsible AI</h3>
                <p class="text-[var(--landing-text-secondary)] text-sm leading-relaxed">
                    AI that explains reasoning before answers. Full audit trail on every completion. Human-in-the-loop approval for all code changes.
                </p>
            </div>

            <!-- Card 4: Sandboxed Nodes -->
            <div class="bento-card p-7 reveal" data-delay="300">
                <div class="w-14 h-14 rounded-2xl bg-pink-500/10 border border-pink-500/20 flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Sandboxed Nodes</h3>
                <p class="text-[var(--landing-text-secondary)] text-sm leading-relaxed">
                    Isolated, ephemeral Docker containers spin up per student in under a second. Zero local config. Full resource quotas enforced.
                </p>
            </div>

            <!-- Card 5: Live Sessions -->
            <div class="bento-card p-7 reveal" data-delay="400">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Live Sessions</h3>
                <p class="text-[var(--landing-text-secondary)] text-sm leading-relaxed">
                    WebRTC voice & video stitched into the editor — office hours, anywhere. Jitsi-powered with attendance tracking.
                </p>
            </div>

            <!-- Card 6: LMS Sync (Large — spans 2 cols) -->
            <div class="bento-card p-8 md:col-span-2 lg:col-span-1 reveal" data-delay="500">
                <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Built-in LMS</h3>
                <p class="text-[var(--landing-text-secondary)] text-sm leading-relaxed mb-4">
                    Courses, assignments, grading, announcements — a complete Learning Management System. Replace Google Classroom entirely.
                </p>
                <div class="flex flex-wrap gap-2">
                    <span class="text-[11px] px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">Courses</span>
                    <span class="text-[11px] px-3 py-1.5 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-400">Assignments</span>
                    <span class="text-[11px] px-3 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400">Grades</span>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 5: IDE SHOWCASE
     ═══════════════════════════════════════════════════════════════════════════ -->
<section id="ide-showcase" class="relative py-32 px-6 overflow-hidden" style="background:rgba(12,12,16,0.8)">
    <div class="grid-pattern absolute inset-0 pointer-events-none opacity-20"></div>
    <div class="glow-orb w-[600px] h-[600px] top-[20%] left-[-200px]" style="background:rgba(139,92,246,0.08)"></div>

    <div class="relative max-w-7xl mx-auto">
        <div class="text-center mb-16 reveal">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-cyan-500/20 bg-cyan-500/[0.06] text-cyan-300 text-xs font-medium mb-6">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                IDE Experience
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-5">
                Real-time, in-browser IDE<br>
                <span class="text-gradient-cyan-green">built for the cohort.</span>
            </h2>
            <p class="text-lg text-[var(--landing-text-secondary)] max-w-2xl mx-auto">
                Every student, instructor, and TA shares one cursor-aware environment. No setup, no drift, just code.
            </p>
        </div>

        <!-- IDE Showcase Image -->
        <div class="relative reveal-scale" style="transition-delay:0.2s">
            <!-- Glow -->
            <div class="absolute -inset-8 rounded-3xl" style="background:radial-gradient(circle at 50% 50%, rgba(139,92,246,0.1) 0%, transparent 60%); filter:blur(40px)"></div>

            <!-- Spinning Border -->
            <div class="absolute -inset-[1px] rounded-2xl overflow-hidden z-0 opacity-60">
                <div class="spinning-border absolute inset-0 rounded-2xl"></div>
            </div>

            <!-- IDE Image -->
            <div class="relative z-10 rounded-2xl overflow-hidden">
                <img src="{{ asset('images/landing/ide-workspace.png') }}" alt="VisionLab IDE Workspace" class="w-full rounded-2xl" style="border:1px solid rgba(255,255,255,0.08)">
                <!-- Overlay gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-[var(--landing-bg)] via-transparent to-transparent opacity-40 pointer-events-none rounded-2xl"></div>
            </div>

            <!-- Floating Annotation Bubbles -->
            <div class="absolute top-8 -right-4 glass-card px-3 py-2 text-xs text-violet-300 font-medium hidden lg:flex items-center gap-2" style="animation: float-gentle 5s ease-in-out infinite">
                <span class="w-2 h-2 rounded-full bg-violet-400"></span> IntelliSense Active
            </div>
            <div class="absolute bottom-16 -left-4 glass-card px-3 py-2 text-xs text-cyan-300 font-medium hidden lg:flex items-center gap-2" style="animation: float-gentle 4s ease-in-out infinite; animation-delay:-1.5s">
                <span class="w-2 h-2 rounded-full bg-cyan-400"></span> Terminal Ready
            </div>
            <div class="absolute top-1/2 -right-8 glass-card px-3 py-2 text-xs text-emerald-300 font-medium hidden lg:flex items-center gap-2" style="animation: float-gentle 6s ease-in-out infinite; animation-delay:-3s">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> 3 Extensions
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 6: AI AGENT
     ═══════════════════════════════════════════════════════════════════════════ -->
<section id="ai-agent" class="section-mesh-pink relative py-32 px-6 overflow-hidden">
    <div class="grid-pattern absolute inset-0 pointer-events-none opacity-30"></div>
    <div class="glow-orb animate w-[500px] h-[500px] bottom-[-100px] right-[-100px]" style="background:rgba(236,72,153,0.06)"></div>

    <div class="relative max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row items-center gap-16">

            <!-- Left: Content -->
            <div class="flex-1 reveal">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-pink-500/20 bg-pink-500/[0.06] text-pink-300 text-xs font-medium mb-6">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    AI Agent Engine
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-white mb-6 leading-tight">
                    AI as a teaching<br>
                    assistant — <span class="text-gradient-violet-pink">not an<br>answer machine.</span>
                </h2>
                <p class="text-lg text-[var(--landing-text-secondary)] mb-8 leading-relaxed">
                    Every completion is logged, attributed, and graded against academic-integrity policy. Students learn the <em class="text-white not-italic font-medium">why</em> before the <em class="text-white not-italic font-medium">what</em>.
                </p>

                <!-- Mode Switcher -->
                <div class="flex gap-1 bg-white/[0.03] rounded-xl p-1 mb-6 w-fit border border-[var(--landing-card-border)]" id="mode-switcher">
                    <button class="mode-tab active" data-mode="chat">CHAT</button>
                    <button class="mode-tab" data-mode="plan">PLAN</button>
                    <button class="mode-tab" data-mode="agent">AGENT</button>
                </div>

                <!-- Mode Descriptions -->
                <div class="space-y-3" id="mode-descriptions">
                    <div class="mode-desc active flex items-start gap-4 p-4 rounded-xl bg-white/[0.03] border border-white/[0.06]" data-mode="chat">
                        <div class="w-9 h-9 rounded-lg bg-slate-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-slate-300">C</span>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white mb-1">CHAT — Read Only</div>
                            <div class="text-xs text-[var(--landing-text-secondary)]">Explain code, search references, debug guidance. Zero file access.</div>
                        </div>
                    </div>
                    <div class="mode-desc flex items-start gap-4 p-4 rounded-xl bg-white/[0.03] border border-white/[0.06] hidden" data-mode="plan">
                        <div class="w-9 h-9 rounded-lg bg-cyan-600/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-cyan-400">P</span>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white mb-1">PLAN — Read + Plan</div>
                            <div class="text-xs text-[var(--landing-text-secondary)]">Analyze architecture, list affected files, produce step-by-step execution plan.</div>
                        </div>
                    </div>
                    <div class="mode-desc flex items-start gap-4 p-4 rounded-xl bg-violet-500/[0.08] border border-violet-500/25 hidden" data-mode="agent">
                        <div class="w-9 h-9 rounded-lg bg-violet-600 flex items-center justify-center flex-shrink-0 mt-0.5" style="box-shadow:0 0 12px rgba(139,92,246,0.3)">
                            <span class="text-xs font-bold text-white">A</span>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-violet-300 mb-1">AGENT — Sandboxed R/W</div>
                            <div class="text-xs text-[var(--landing-text-secondary)]">Propose diffs, await approval, apply patches. Every action logged and reversible.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: AI Robot Visual -->
            <div class="flex-1 w-full reveal-scale" style="transition-delay:0.2s">
                <div class="relative">
                    <!-- Glow -->
                    <div class="absolute inset-0 rounded-3xl" style="background:radial-gradient(circle at center, rgba(236,72,153,0.1) 0%, transparent 60%); filter:blur(50px); transform:scale(1.3)"></div>
                    <!-- Robot Image -->
                    <img src="{{ asset('images/landing/ai-robot.png') }}" alt="VisionLab AI Agent" class="relative rounded-3xl w-full max-w-md mx-auto parallax-image" style="animation: hero-image-float 10s ease-in-out infinite; border:1px solid rgba(255,255,255,0.06)">

                    <!-- Floating Diff Preview -->
                    <div class="absolute -bottom-6 -left-6 glass-card p-4 max-w-[260px] hidden md:block" style="animation: float-gentle 6s ease-in-out infinite">
                        <div class="flex items-center gap-2 text-xs text-[var(--landing-text-secondary)] mb-2">
                            <svg class="w-3.5 h-3.5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Patch Preview
                        </div>
                        <div class="font-mono text-[10px] space-y-0.5">
                            <div class="text-red-400 bg-red-500/10 px-2 py-0.5 rounded">- result.append(x)</div>
                            <div class="text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded">+ return [x*2 for x in items]</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 7: INSTRUCTOR COMMAND DECK
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="section-mesh-cyan relative py-32 px-6 overflow-hidden">
    <div class="grid-pattern absolute inset-0 pointer-events-none opacity-20"></div>

    <!-- Scan line effect -->
    <div class="absolute left-0 right-0 h-px bg-gradient-to-r from-transparent via-cyan-500/30 to-transparent pointer-events-none" style="animation: scan-down 6s linear infinite; top:-2px"></div>

    <div class="relative max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row items-center gap-16">

            <!-- Left: Dashboard Mockup Image -->
            <div class="flex-1 w-full reveal-scale">
                <div class="relative">
                    <div class="absolute inset-0 rounded-3xl" style="background:radial-gradient(circle, rgba(6,182,212,0.1) 0%, transparent 60%); filter:blur(40px); transform:scale(1.2)"></div>
                    <img src="{{ asset('images/landing/collaboration.png') }}" alt="Instructor Command Deck" class="relative rounded-2xl w-full parallax-image" style="border:1px solid rgba(255,255,255,0.08); box-shadow: 0 40px 80px rgba(0,0,0,0.6)">
                </div>
            </div>

            <!-- Right: Content -->
            <div class="flex-1 reveal" style="transition-delay:0.15s">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-cyan-500/20 bg-cyan-500/[0.06] text-cyan-300 text-xs font-medium mb-6">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Instructor Dashboard
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-white mb-6 leading-tight">
                    The instructor<br>
                    <span class="text-gradient-cyan-green">command deck.</span>
                </h2>
                <p class="text-lg text-[var(--landing-text-secondary)] mb-10 leading-relaxed">
                    Observe an entire lecture hall in real time. Peek into any workspace, broadcast intent, throttle resources, and replay sessions for review.
                </p>

                <!-- Feature Pills -->
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-white/[0.03] border border-white/[0.06] hover:border-cyan-500/30 transition-all duration-300 group cursor-default">
                        <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center flex-shrink-0 text-sm font-bold text-cyan-400 group-hover:bg-cyan-500/20 transition-colors">A</div>
                        <div>
                            <div class="text-sm font-semibold text-white mb-0.5">Live Telemetry</div>
                            <div class="text-xs text-[var(--landing-text-secondary)]">Per-student keystrokes, compute spend, AI usage — streaming in real-time.</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-white/[0.03] border border-white/[0.06] hover:border-cyan-500/30 transition-all duration-300 group cursor-default">
                        <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center flex-shrink-0 text-sm font-bold text-cyan-400 group-hover:bg-cyan-500/20 transition-colors">B</div>
                        <div>
                            <div class="text-sm font-semibold text-white mb-0.5">Resource Throttling</div>
                            <div class="text-xs text-[var(--landing-text-secondary)]">Cap CPU/GPU per cohort. Burst budgets, audited and enforced.</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-white/[0.03] border border-white/[0.06] hover:border-cyan-500/30 transition-all duration-300 group cursor-default">
                        <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center flex-shrink-0 text-sm font-bold text-cyan-400 group-hover:bg-cyan-500/20 transition-colors">C</div>
                        <div>
                            <div class="text-sm font-semibold text-white mb-0.5">Session Replay</div>
                            <div class="text-xs text-[var(--landing-text-secondary)]">Scrub through any session like a video. Diff every commit.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 8: COLLABORATION
     ═══════════════════════════════════════════════════════════════════════════ -->
<section id="collaboration" class="relative py-32 px-6 overflow-hidden" style="background:var(--landing-bg)">
    <div class="glow-orb w-[500px] h-[500px] top-[-100px] right-[-100px]" style="background:rgba(6,182,212,0.06)"></div>

    <div class="relative max-w-6xl mx-auto text-center">
        <div class="reveal">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-amber-500/20 bg-amber-500/[0.06] text-amber-300 text-xs font-medium mb-6">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Real-Time Collaboration
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-5">
                Code together,<br>
                <span class="text-gradient-hero">anywhere.</span>
            </h2>
            <p class="text-lg text-[var(--landing-text-secondary)] max-w-2xl mx-auto mb-16">
                Laravel Reverb powers instant WebSocket presence channels. Watch teammates' cursors move in real-time. Get notified when anyone joins or leaves.
            </p>
        </div>

        <!-- 3-Column Collab Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Live Presence -->
            <div class="bento-card p-7 text-left reveal" data-delay="0">
                <div class="flex -space-x-3 mb-5">
                    <div class="w-11 h-11 rounded-full bg-violet-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-sm font-bold text-white" style="box-shadow:0 0 12px rgba(139,92,246,0.3)">A</div>
                    <div class="w-11 h-11 rounded-full bg-cyan-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-sm font-bold text-white" style="box-shadow:0 0 12px rgba(6,182,212,0.3)">J</div>
                    <div class="w-11 h-11 rounded-full bg-emerald-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-sm font-bold text-white" style="box-shadow:0 0 12px rgba(16,185,129,0.3)">S</div>
                    <div class="w-11 h-11 rounded-full bg-[var(--landing-card-border)] border-2 border-[var(--landing-bg)] flex items-center justify-center text-xs text-[var(--landing-muted)]">+8</div>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Live Presence</h3>
                <p class="text-sm text-[var(--landing-text-secondary)]">Glowing avatars appear in the IDE topbar, showing who's active in your room right now. Real-time WebSocket powered.</p>
            </div>

            <!-- Multi-Cursor -->
            <div class="bento-card p-7 text-left reveal" data-delay="150">
                <div class="mb-5 font-mono text-xs bg-[var(--landing-bg)] rounded-lg p-3 border border-[var(--landing-card-border)]">
                    <span class="text-violet-400">|</span> <span class="text-violet-300 text-[10px]">Alex</span>
                    <span class="text-slate-500 block mt-1">    result = sorted(items)</span>
                    <span class="text-cyan-400">|</span> <span class="text-cyan-300 text-[10px]">Jane</span>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Multi-Cursor Sync</h3>
                <p class="text-sm text-[var(--landing-text-secondary)]">Each collaborator's cursor renders with a colored flag showing their name — zero lag, sub-frame latency.</p>
            </div>

            <!-- Smart Toasts -->
            <div class="bento-card p-7 text-left reveal" data-delay="300">
                <div class="mb-5 space-y-2">
                    <div class="mock-toast">
                        <div class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></div>
                        <span class="text-emerald-400 font-medium">Alex</span>
                        <span class="text-[var(--landing-muted)]">joined workspace</span>
                    </div>
                    <div class="mock-toast opacity-60">
                        <div class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></div>
                        <span class="text-red-400 font-medium">Jane</span>
                        <span class="text-[var(--landing-muted)]">left workspace</span>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Smart Toasts</h3>
                <p class="text-sm text-[var(--landing-text-secondary)]">Sleek dark notifications slide in whenever someone joins or leaves — never intrusive, always informative.</p>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 9: ACHIEVEMENTS & GAMIFICATION
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="relative py-24 px-6 border-y border-[var(--landing-card-border)]" style="background:rgba(255,255,255,0.01)">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16 reveal">
            <h2 class="text-3xl md:text-4xl font-black text-white mb-4">
                Level Up Your <span class="text-gradient-hero">Coding Journey</span>
            </h2>
            <p class="text-[var(--landing-text-secondary)] max-w-xl mx-auto">Track your contributions, earn achievements, and build your coding streak.</p>
        </div>

        <div class="flex flex-col lg:flex-row items-center gap-12">
            <!-- Achievement Badges -->
            <div class="flex-1 reveal">
                <div class="text-xs text-[var(--landing-muted)] uppercase tracking-wider mb-4">Achievements</div>
                <div class="flex flex-wrap gap-3">
                    <div class="achievement-badge bg-violet-500/10 border border-violet-500/20" title="First Commit">🚀</div>
                    <div class="achievement-badge bg-cyan-500/10 border border-cyan-500/20" title="Code Review Master">🔍</div>
                    <div class="achievement-badge bg-emerald-500/10 border border-emerald-500/20" title="7-Day Streak">🔥</div>
                    <div class="achievement-badge bg-pink-500/10 border border-pink-500/20" title="AI Whisperer">🤖</div>
                    <div class="achievement-badge bg-amber-500/10 border border-amber-500/20" title="Bug Slayer">🐛</div>
                    <div class="achievement-badge bg-indigo-500/10 border border-indigo-500/20" title="Team Player">🤝</div>
                    <div class="achievement-badge bg-rose-500/10 border border-rose-500/20" title="Speed Demon">⚡</div>
                    <div class="achievement-badge bg-teal-500/10 border border-teal-500/20" title="100 Commits">💎</div>
                    <div class="achievement-badge bg-orange-500/10 border border-orange-500/20" title="Night Owl">🦉</div>
                    <div class="achievement-badge bg-purple-500/10 border border-purple-500/20" title="Mentor">👨‍🏫</div>
                </div>
            </div>

            <!-- Contribution Heatmap -->
            <div class="flex-1 w-full reveal" style="transition-delay:0.15s">
                <div class="text-xs text-[var(--landing-muted)] uppercase tracking-wider mb-4">365-Day Contribution Map</div>
                <div id="heatmap-container" class="flex gap-[3px] overflow-hidden rounded-xl p-4 bg-white/[0.02] border border-[var(--landing-card-border)]">
                    <!-- Filled by JS -->
                </div>
                <div class="flex items-center justify-between mt-3 text-[10px] text-[var(--landing-muted)]">
                    <span>Less</span>
                    <div class="flex gap-1">
                        <div class="w-3 h-3 rounded-sm" style="background:rgba(139,92,246,0.1)"></div>
                        <div class="w-3 h-3 rounded-sm" style="background:rgba(139,92,246,0.25)"></div>
                        <div class="w-3 h-3 rounded-sm" style="background:rgba(139,92,246,0.5)"></div>
                        <div class="w-3 h-3 rounded-sm" style="background:rgba(139,92,246,0.75)"></div>
                        <div class="w-3 h-3 rounded-sm" style="background:rgba(139,92,246,1)"></div>
                    </div>
                    <span>More</span>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 10: SECURITY & GOVERNANCE
     ═══════════════════════════════════════════════════════════════════════════ -->
<section id="security" class="section-mesh-violet relative py-32 px-6 overflow-hidden">
    <div class="grid-pattern absolute inset-0 pointer-events-none opacity-20"></div>

    <div class="relative max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row items-center gap-16">

            <!-- Left: Security Image -->
            <div class="flex-1 w-full reveal-scale">
                <div class="relative">
                    <div class="absolute inset-0 rounded-3xl" style="background:radial-gradient(circle, rgba(139,92,246,0.12) 0%, transparent 60%); filter:blur(50px); transform:scale(1.3)"></div>
                    <img src="{{ asset('images/landing/security-shield.png') }}" alt="VisionLab Security" class="relative rounded-3xl w-full max-w-md mx-auto parallax-image" style="animation: hero-image-float 12s ease-in-out infinite; border:1px solid rgba(255,255,255,0.06)">
                </div>
            </div>

            <!-- Right: Security Content -->
            <div class="flex-1 reveal" style="transition-delay:0.15s">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/[0.06] text-emerald-300 text-xs font-medium mb-6">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Enterprise Security
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-white mb-6 leading-tight">
                    Zero-trust<br>
                    <span class="text-gradient-cyan-green">by design.</span>
                </h2>
                <p class="text-lg text-[var(--landing-text-secondary)] mb-10 leading-relaxed">
                    OWASP ASVS Level 2 compliance, container hardening, path traversal prevention, and full audit trails on every action.
                </p>

                <!-- Security Checklist -->
                <div class="space-y-3" id="security-checklist">
                    <div class="security-check flex items-center gap-3 p-3 rounded-lg bg-white/[0.02] border border-[var(--landing-card-border)]">
                        <div class="check-icon w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm text-white">OWASP ASVS Level 2 Verified</span>
                    </div>
                    <div class="security-check flex items-center gap-3 p-3 rounded-lg bg-white/[0.02] border border-[var(--landing-card-border)]">
                        <div class="check-icon w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm text-white">Docker Container Hardening</span>
                    </div>
                    <div class="security-check flex items-center gap-3 p-3 rounded-lg bg-white/[0.02] border border-[var(--landing-card-border)]">
                        <div class="check-icon w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm text-white">Path Traversal Prevention</span>
                    </div>
                    <div class="security-check flex items-center gap-3 p-3 rounded-lg bg-white/[0.02] border border-[var(--landing-card-border)]">
                        <div class="check-icon w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm text-white">AI Safety Filters & Sandboxing</span>
                    </div>
                    <div class="security-check flex items-center gap-3 p-3 rounded-lg bg-white/[0.02] border border-[var(--landing-card-border)]">
                        <div class="check-icon w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm text-white">Full Audit Trail on Every Action</span>
                    </div>
                    <div class="security-check flex items-center gap-3 p-3 rounded-lg bg-white/[0.02] border border-[var(--landing-card-border)]">
                        <div class="check-icon w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm text-white">Extension SHA256 Integrity Checks</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 11: CTA — Final Call
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="relative py-36 px-6 overflow-hidden">
    <!-- Aurora Background -->
    <div class="absolute inset-0 hero-mesh opacity-80 pointer-events-none"></div>
    <div class="glow-orb animate w-[800px] h-[800px] top-[-300px] left-1/2 -translate-x-1/2" style="background:rgba(139,92,246,0.12)"></div>
    <div class="glow-orb animate w-[500px] h-[500px] bottom-[-200px] right-[-100px]" style="background:rgba(6,182,212,0.08); animation-delay:-5s"></div>

    <div class="relative z-10 max-w-4xl mx-auto text-center reveal">
        <h2 class="text-4xl md:text-6xl lg:text-7xl font-black text-white mb-8 leading-tight">
            Ready to revolutionize<br>
            <span class="text-gradient-hero">your classroom?</span>
        </h2>
        <p class="text-xl text-[var(--landing-text-secondary)] max-w-2xl mx-auto mb-12">
            Deploy VisionLab across your institution. Onboarding in days, not quarters. Join the future of collaborative coding education.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-magnetic primary text-base px-10 py-4">
                    Open My Workspace
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            @else
                <a href="{{ route('register') }}" class="btn-magnetic primary text-base px-10 py-4">
                    Create Free Account
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="{{ route('login') }}" class="btn-magnetic secondary text-base px-10 py-4">Sign In</a>
            @endauth
        </div>

        <!-- Social Proof -->
        <div class="flex items-center justify-center gap-4">
            <div class="flex -space-x-2">
                <div class="w-8 h-8 rounded-full bg-violet-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-[10px] font-bold text-white">A</div>
                <div class="w-8 h-8 rounded-full bg-cyan-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-[10px] font-bold text-white">K</div>
                <div class="w-8 h-8 rounded-full bg-emerald-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-[10px] font-bold text-white">M</div>
                <div class="w-8 h-8 rounded-full bg-pink-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-[10px] font-bold text-white">R</div>
                <div class="w-8 h-8 rounded-full bg-amber-500 border-2 border-[var(--landing-bg)] flex items-center justify-center text-[10px] font-bold text-white">S</div>
            </div>
            <span class="text-sm text-[var(--landing-text-secondary)]">Built for <span class="text-white font-semibold">Aptech Vision 2026</span></span>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 12: FOOTER
     ═══════════════════════════════════════════════════════════════════════════ -->
<footer class="relative pt-16 pb-8 px-6">
    <div class="footer-gradient-line mb-16"></div>

    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-10 mb-16">
            <!-- Column 1: Brand -->
            <div class="col-span-2 md:col-span-1">
                <a href="#" class="flex items-center gap-2.5 mb-4">
                    <x-logo size="h-8 w-8" />
                </a>
                <p class="text-sm text-[var(--landing-text-secondary)] mb-6 leading-relaxed">
                    The AI-powered collaborative coding ecosystem engineered for research universities.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 rounded-lg bg-white/[0.04] border border-[var(--landing-card-border)] flex items-center justify-center text-[var(--landing-muted)] hover:text-white hover:border-violet-500/30 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-white/[0.04] border border-[var(--landing-card-border)] flex items-center justify-center text-[var(--landing-muted)] hover:text-white hover:border-violet-500/30 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Column 2: Product -->
            <div>
                <h4 class="text-sm font-semibold text-white mb-4">Product</h4>
                <ul class="space-y-2.5">
                    <li><a href="#features" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">Features</a></li>
                    <li><a href="#ide-showcase" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">IDE</a></li>
                    <li><a href="#ai-agent" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">AI Agent</a></li>
                    <li><a href="#collaboration" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">Collaboration</a></li>
                    <li><a href="#security" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">Security</a></li>
                </ul>
            </div>

            <!-- Column 3: Resources -->
            <div>
                <h4 class="text-sm font-semibold text-white mb-4">Resources</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('demo') }}" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">Demo Guide</a></li>
                    <li><a href="#" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">Documentation</a></li>
                    <li><a href="#" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">API Reference</a></li>
                    <li><a href="#" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">Status</a></li>
                </ul>
            </div>

            <!-- Column 4: Legal -->
            <div>
                <h4 class="text-sm font-semibold text-white mb-4">Legal</h4>
                <ul class="space-y-2.5">
                    <li><a href="#" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">Privacy</a></li>
                    <li><a href="#" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">Terms</a></li>
                    <li><a href="#" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">Security</a></li>
                    <li><a href="{{ route('login') }}" class="text-sm text-[var(--landing-text-secondary)] hover:text-white transition-colors">Sign In</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-[var(--landing-card-border)] pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-xs text-[var(--landing-muted)]">
                © {{ date('Y') }} VisionLab — Built for Aptech Vision 2026
            </p>
            <!-- Tech Stack Badges -->
            <div class="flex items-center gap-3">
                <span class="text-[10px] px-2.5 py-1 rounded-full bg-white/[0.03] border border-[var(--landing-card-border)] text-[var(--landing-muted)]">Laravel {{ app()->version() }}</span>
                <span class="text-[10px] px-2.5 py-1 rounded-full bg-white/[0.03] border border-[var(--landing-card-border)] text-[var(--landing-muted)]">PHP {{ PHP_VERSION }}</span>
                <span class="text-[10px] px-2.5 py-1 rounded-full bg-white/[0.03] border border-[var(--landing-card-border)] text-[var(--landing-muted)]">Tailwind CSS</span>
                <span class="text-[10px] px-2.5 py-1 rounded-full bg-white/[0.03] border border-[var(--landing-card-border)] text-[var(--landing-muted)]">Docker</span>
            </div>
        </div>
    </div>
</footer>


<!-- ═══════════════════════════════════════════════════════════════════════════
     JAVASCRIPT — All Interactions
     ═══════════════════════════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ══════════════════════════════════════════════════════════════════════
    // 1. NEURAL NETWORK CANVAS
    // ══════════════════════════════════════════════════════════════════════
    const canvas = document.getElementById('neural-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let nodes = [];
        let mouseX = 0, mouseY = 0;
        const NODE_COUNT = 80;
        const CONNECTION_DIST = 180;

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas, { passive: true });

        // Track mouse for interactive node attraction
        window.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        }, { passive: true });

        // Create nodes
        for (let i = 0; i < NODE_COUNT; i++) {
            nodes.push({
                x: Math.random() * window.innerWidth,
                y: Math.random() * window.innerHeight,
                vx: (Math.random() - 0.5) * 0.4,
                vy: (Math.random() - 0.5) * 0.4,
                r: Math.random() * 2 + 0.5,
                pulse: Math.random() * Math.PI * 2,
                color: Math.random() > 0.6 ? [139, 92, 246] : Math.random() > 0.3 ? [6, 182, 212] : [236, 72, 153]
            });
        }

        function drawNetwork() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Update & draw nodes
            nodes.forEach((node, i) => {
                // Gentle mouse attraction
                const dx = mouseX - node.x;
                const dy = mouseY - node.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 300 && dist > 0) {
                    node.vx += (dx / dist) * 0.01;
                    node.vy += (dy / dist) * 0.01;
                }

                // Damping
                node.vx *= 0.99;
                node.vy *= 0.99;

                node.x += node.vx;
                node.y += node.vy;
                node.pulse += 0.02;

                // Wrap around edges
                if (node.x < -10) node.x = canvas.width + 10;
                if (node.x > canvas.width + 10) node.x = -10;
                if (node.y < -10) node.y = canvas.height + 10;
                if (node.y > canvas.height + 10) node.y = -10;

                // Draw node with pulse
                const pulseAlpha = 0.3 + Math.sin(node.pulse) * 0.15;
                ctx.beginPath();
                ctx.arc(node.x, node.y, node.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${node.color[0]},${node.color[1]},${node.color[2]},${pulseAlpha})`;
                ctx.fill();

                // Draw connections
                for (let j = i + 1; j < nodes.length; j++) {
                    const other = nodes[j];
                    const cdx = node.x - other.x;
                    const cdy = node.y - other.y;
                    const cdist = Math.sqrt(cdx * cdx + cdy * cdy);

                    if (cdist < CONNECTION_DIST) {
                        const alpha = (1 - cdist / CONNECTION_DIST) * 0.12;
                        ctx.beginPath();
                        ctx.moveTo(node.x, node.y);
                        ctx.lineTo(other.x, other.y);
                        ctx.strokeStyle = `rgba(${node.color[0]},${node.color[1]},${node.color[2]},${alpha})`;
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }
            });

            requestAnimationFrame(drawNetwork);
        }
        drawNetwork();
    }


    // ══════════════════════════════════════════════════════════════════════
    // 2. SCROLL REVEAL OBSERVER
    // ══════════════════════════════════════════════════════════════════════
    const revealElements = document.querySelectorAll('.reveal, .reveal-scale');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = entry.target.dataset.delay || 0;
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, parseInt(delay));
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    revealElements.forEach(el => revealObserver.observe(el));


    // ══════════════════════════════════════════════════════════════════════
    // 3. NAVBAR SCROLL EFFECT
    // ══════════════════════════════════════════════════════════════════════
    const navbar = document.getElementById('landing-navbar');
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        if (scrollY > 60) {
            navbar.classList.remove('navbar-glass');
            navbar.classList.add('navbar-solid');
        } else {
            navbar.classList.add('navbar-glass');
            navbar.classList.remove('navbar-solid');
        }
        lastScroll = scrollY;
    }, { passive: true });


    // ══════════════════════════════════════════════════════════════════════
    // 4. STAT COUNTER ANIMATION
    // ══════════════════════════════════════════════════════════════════════
    const statElements = document.querySelectorAll('.stat-number[data-target]');
    const statObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.dataset.target);
                const suffix = el.dataset.suffix || '';
                if (isNaN(target)) return;

                let current = 0;
                const duration = 1500;
                const start = performance.now();

                function animate(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    // Ease out cubic
                    const eased = 1 - Math.pow(1 - progress, 3);
                    current = Math.round(eased * target);
                    el.textContent = current + suffix;
                    if (progress < 1) requestAnimationFrame(animate);
                }
                requestAnimationFrame(animate);
                statObserver.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    statElements.forEach(el => statObserver.observe(el));


    // ══════════════════════════════════════════════════════════════════════
    // 5. BENTO CARD MOUSE TRACKING (Radial Glow Effect)
    // ══════════════════════════════════════════════════════════════════════
    document.querySelectorAll('.bento-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            card.style.setProperty('--mouse-x', x + '%');
            card.style.setProperty('--mouse-y', y + '%');
        });

        // 3D Tilt
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            card.style.transform = `perspective(1000px) rotateY(${x * 5}deg) rotateX(${-y * 3}deg) translateY(-4px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });


    // ══════════════════════════════════════════════════════════════════════
    // 6. IDE TYPING ANIMATION
    // ══════════════════════════════════════════════════════════════════════
    const typingTarget = document.getElementById('typing-target');
    if (typingTarget) {
        const lines = [
            { text: 'print(result.summary)', color: '#a78bfa' },
            { text: '# ✨ 2 patches ready', color: '#64748b' },
        ];
        let lineIdx = 0, charIdx = 0;
        let currentLine = null;

        function typeChar() {
            if (lineIdx >= lines.length) {
                // Reset after pause
                setTimeout(() => {
                    typingTarget.innerHTML = '';
                    lineIdx = 0;
                    charIdx = 0;
                    typeChar();
                }, 3000);
                return;
            }

            if (charIdx === 0) {
                currentLine = document.createElement('div');
                currentLine.className = 'code-line';
                currentLine.style.color = lines[lineIdx].color;
                typingTarget.appendChild(currentLine);
            }

            if (charIdx < lines[lineIdx].text.length) {
                currentLine.textContent = lines[lineIdx].text.substring(0, charIdx + 1);
                charIdx++;
                setTimeout(typeChar, 40 + Math.random() * 60);
            } else {
                // Add cursor blink at end of last line
                if (lineIdx === lines.length - 1) {
                    const cursor = document.createElement('span');
                    cursor.className = 'typing-cursor';
                    currentLine.appendChild(cursor);
                }
                lineIdx++;
                charIdx = 0;
                setTimeout(typeChar, 400);
            }
        }

        // Start typing after a delay
        setTimeout(typeChar, 2000);
    }


    // ══════════════════════════════════════════════════════════════════════
    // 7. AI MODE SWITCHER
    // ══════════════════════════════════════════════════════════════════════
    const modeSwitcher = document.getElementById('mode-switcher');
    if (modeSwitcher) {
        const tabs = modeSwitcher.querySelectorAll('.mode-tab');
        const descs = document.querySelectorAll('.mode-desc');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const mode = tab.dataset.mode;

                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                // Update descriptions with animation
                descs.forEach(d => {
                    if (d.dataset.mode === mode) {
                        d.classList.remove('hidden');
                        d.style.animation = 'reveal-up 0.4s cubic-bezier(0.16,1,0.3,1) forwards';
                    } else {
                        d.classList.add('hidden');
                        d.style.animation = '';
                    }
                });
            });
        });
    }


    // ══════════════════════════════════════════════════════════════════════
    // 8. CONTRIBUTION HEATMAP
    // ══════════════════════════════════════════════════════════════════════
    const heatmapContainer = document.getElementById('heatmap-container');
    if (heatmapContainer) {
        const weeks = 52;
        const days = 7;
        const levels = [
            'rgba(139,92,246,0.05)',
            'rgba(139,92,246,0.15)',
            'rgba(139,92,246,0.3)',
            'rgba(139,92,246,0.5)',
            'rgba(139,92,246,0.75)',
            'rgba(139,92,246,1)',
        ];

        for (let w = 0; w < weeks; w++) {
            const col = document.createElement('div');
            col.style.display = 'flex';
            col.style.flexDirection = 'column';
            col.style.gap = '3px';

            for (let d = 0; d < days; d++) {
                const cell = document.createElement('div');
                cell.className = 'heatmap-cell';
                // Random-ish but with a pattern — more activity in recent weeks
                const base = Math.random();
                const recencyBoost = w / weeks;
                const level = Math.floor((base * 0.6 + recencyBoost * 0.4) * levels.length);
                cell.style.background = levels[Math.min(level, levels.length - 1)];
                col.appendChild(cell);
            }
            heatmapContainer.appendChild(col);
        }
    }


    // ══════════════════════════════════════════════════════════════════════
    // 9. SECURITY CHECKLIST ANIMATION
    // ══════════════════════════════════════════════════════════════════════
    const securityChecks = document.querySelectorAll('.security-check');
    const securityObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Stagger the check animations
                const checks = entry.target.parentElement.querySelectorAll('.security-check');
                checks.forEach((check, i) => {
                    setTimeout(() => {
                        check.classList.add('checked');
                    }, i * 200);
                });
                securityObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    if (securityChecks.length > 0) {
        securityObserver.observe(securityChecks[0]);
    }


    // ══════════════════════════════════════════════════════════════════════
    // 10. PARALLAX IMAGE TILT ON HOVER
    // ══════════════════════════════════════════════════════════════════════
    document.querySelectorAll('.parallax-image').forEach(img => {
        img.addEventListener('mousemove', (e) => {
            const rect = img.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            img.style.transform = `perspective(800px) rotateY(${x * 6}deg) rotateX(${-y * 4}deg) scale(1.02)`;
        });
        img.addEventListener('mouseleave', () => {
            img.style.transform = '';
        });
    });


    // ══════════════════════════════════════════════════════════════════════
    // 11. SMOOTH SCROLL FOR NAV LINKS
    // ══════════════════════════════════════════════════════════════════════
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

});
</script>

</body>
</html>

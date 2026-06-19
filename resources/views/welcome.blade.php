<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="The collaborative IDE built for research universities. Sandboxed, audited, and AI-assisted.">
    <title>VisionLab — Collaborative coding for universities</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts: Inter (sans) + Playfair Display (serif italic accents) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,600;1,700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════════════════════════════
           DESIGN SYSTEM — Exact Lovable Reference Match + Premium Additions
           ═══════════════════════════════════════════════════════════════════ */
        :root {
            --bg: #030303;
            --surface: #0a0a0a;
            --surface-2: #111111;
            --border: rgba(255,255,255,0.06);
            --border-hover: rgba(255,255,255,0.12);
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --text-muted: #52525b;
            --cyan: #00e5ff;
            --cyan-glow: rgba(0,229,255,0.4);
            --purple: #a855f7;
            --pink: #ec4899;
            --emerald: #10b981;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background: var(--bg);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Typography */
        .font-serif-italic {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
        }
        .font-mono {
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
        }

        /* Gradients */
        .text-gradient-hero {
            background: linear-gradient(135deg, var(--purple), var(--pink), var(--cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .text-gradient-purple-pink {
            background: linear-gradient(to right, var(--purple), var(--pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .text-gradient-cyan {
            background: linear-gradient(to right, var(--cyan), #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ═══════════ NAVIGATION ═══════════ */
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            background: rgba(3,3,3,0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        .nav.scrolled {
            background: rgba(3,3,3,0.95);
            box-shadow: 0 4px 30px rgba(0,0,0,0.5);
        }
        .nav-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: white;
        }
        .nav-brand span {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: -0.02em;
        }
        .nav-brand .brand-slash {
            color: var(--text-muted);
            font-weight: 300;
            margin: 0 0.25rem;
        }
        .nav-brand .brand-edu {
            color: var(--text-secondary);
            font-weight: 400;
            font-size: 0.85rem;
        }
        .nav-links {
            display: none;
            align-items: center;
            gap: 2rem;
        }
        @media (min-width: 768px) { .nav-links { display: flex; } }
        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-links a:hover { color: var(--text-primary); }
        .nav-cta {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* ═══════════ BUTTONS ═══════════ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.65rem 1.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 9999px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            border: none;
            white-space: nowrap;
        }
        .btn-primary {
            background: var(--cyan);
            color: #000;
            box-shadow: 0 0 20px var(--cyan-glow), 0 0 60px rgba(0,229,255,0.15);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 30px var(--cyan-glow), 0 0 80px rgba(0,229,255,0.25);
            background: #33eeff;
        }
        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .btn-secondary:hover {
            border-color: rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.03);
        }

        /* ═══════════ CARDS ═══════════ */
        .card {
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border);
            border-radius: 1rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .card:hover {
            border-color: var(--border-hover);
            background: rgba(255,255,255,0.035);
        }
        .card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(600px circle at var(--mouse-x, 50%) var(--mouse-y, 50%),
                rgba(255,255,255,0.03), transparent 40%);
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        .card:hover::before { opacity: 1; }

        /* ═══════════ SECTIONS ═══════════ */
        .section {
            max-width: 1280px;
            margin: 0 auto;
            padding: 6rem 2rem;
        }
        .section-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .section-heading {
            font-size: clamp(2rem, 5vw, 3.25rem);
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
        }
        .section-sub {
            font-size: 0.95rem;
            color: var(--text-secondary);
            max-width: 600px;
            line-height: 1.7;
        }

        /* ═══════════ GRID BACKGROUND ═══════════ */
        .mesh-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 80% 60% at 50% 40%, black 20%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 40%, black 20%, transparent 100%);
            pointer-events: none;
        }

        /* ═══════════ HERO ═══════════ */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 2rem 4rem;
            overflow: hidden;
        }
        .hero-headline {
            font-size: clamp(3rem, 7vw, 5.5rem);
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: -0.04em;
        }
        .hero-sub {
            font-size: 1.15rem;
            color: var(--text-secondary);
            max-width: 640px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* Ambient orbs */
        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            animation: orb-drift 20s ease-in-out infinite alternate;
        }
        @keyframes orb-drift {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -20px) scale(1.1); }
            100% { transform: translate(-20px, 15px) scale(0.95); }
        }

        /* ═══════════ IDE SHOWCASE ═══════════ */
        .ide-window {
            position: relative;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow:
                0 25px 50px rgba(0,0,0,0.5),
                0 0 0 1px rgba(255,255,255,0.03),
                0 0 100px rgba(0,229,255,0.05);
            transition: all 0.5s ease;
        }
        .ide-window:hover {
            box-shadow:
                0 30px 60px rgba(0,0,0,0.6),
                0 0 0 1px rgba(255,255,255,0.05),
                0 0 120px rgba(0,229,255,0.08);
        }
        .ide-titlebar {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid var(--border);
        }
        .ide-dot { width: 10px; height: 10px; border-radius: 50%; }
        .ide-dot-red { background: #ff5f57; }
        .ide-dot-yellow { background: #febc2e; }
        .ide-dot-green { background: #28c840; }

        /* IDE Sidebar + Editor mock */
        .ide-body { display: flex; min-height: 350px; }
        .ide-sidebar {
            width: 220px;
            border-right: 1px solid var(--border);
            padding: 1rem;
            display: none;
        }
        @media (min-width: 768px) { .ide-sidebar { display: block; } }
        .ide-editor { flex: 1; padding: 1.25rem; position: relative; }

        .file-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.5rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
            cursor: default;
            transition: background 0.2s;
        }
        .file-item:hover { background: rgba(255,255,255,0.04); }
        .file-item.active { background: rgba(0,229,255,0.08); color: var(--cyan); }
        .file-icon { font-size: 0.7rem; opacity: 0.5; }

        .code-line {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            line-height: 1.8;
            display: flex;
            gap: 1.25rem;
        }
        .line-num {
            color: var(--text-muted);
            user-select: none;
            min-width: 2ch;
            text-align: right;
        }
        .code-keyword { color: #c084fc; }
        .code-func { color: #67e8f9; }
        .code-string { color: #86efac; }
        .code-comment { color: #52525b; font-style: italic; }
        .code-type { color: #fbbf24; }
        .code-plain { color: #d4d4d8; }

        /* Presence avatars */
        .presence-bar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-top: 1px solid var(--border);
            background: rgba(255,255,255,0.01);
        }
        .avatar-stack { display: flex; }
        .avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            border: 2px solid var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: 700;
            color: white;
            margin-left: -8px;
        }
        .avatar:first-child { margin-left: 0; }

        /* ═══════════ FEATURE GRID ═══════════ */
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        @media (min-width: 640px) { .feature-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .feature-grid { grid-template-columns: repeat(3, 1fr); } }

        .feature-card {
            padding: 2rem;
            min-height: 200px;
            display: flex;
            flex-direction: column;
        }
        .feature-num {
            font-size: 0.6rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .feature-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
        }
        .feature-desc {
            font-size: 0.85rem;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-top: auto;
        }
        .feature-dot {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        /* ═══════════ INSTRUCTOR SECTION ═══════════ */
        .instructor-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 4rem;
            align-items: center;
        }
        @media (min-width: 1024px) { .instructor-grid { grid-template-columns: 1fr 1fr; } }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.25rem 0;
        }
        .step-letter {
            width: 32px; height: 32px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
        }
        .step-title {
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .step-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* Monitor Grid */
        .monitor-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }
        .monitor-cell {
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border);
            background: var(--surface);
        }
        .monitor-bar {
            height: 3px;
            border-radius: 2px;
            background: var(--surface-2);
            margin-top: 0.5rem;
            overflow: hidden;
        }
        .monitor-bar-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 1.5s ease;
        }

        /* ═══════════ AI SECTION ═══════════ */
        .ai-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        @media (min-width: 768px) { .ai-cards { grid-template-columns: repeat(3, 1fr); } }

        .ai-msg {
            padding: 1.25rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* ═══════════ CTA SECTION ═══════════ */
        .cta-section {
            position: relative;
            text-align: center;
            padding: 8rem 2rem;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,229,255,0.06), transparent 70%);
            pointer-events: none;
        }

        /* ═══════════ FOOTER ═══════════ */
        .footer {
            border-top: 1px solid var(--border);
            padding: 3rem 2rem;
            background: var(--bg);
        }
        .footer-inner {
            max-width: 1280px;
            margin: 0 auto;
        }
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .footer-links a:hover { color: white; }

        /* ═══════════ SCROLL REVEAL ═══════════ */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.9s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.9s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* ═══════════ FLOATING ═══════════ */
        @keyframes float-gentle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(3deg); }
        }

        /* ═══════════ CUSTOM CURSOR ═══════════ */
        .cursor-dot {
            position: fixed;
            width: 6px; height: 6px;
            background: var(--cyan);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 12px var(--cyan);
            transition: transform 0.15s ease;
        }
        .cursor-ring {
            position: fixed;
            width: 36px; height: 36px;
            border: 1.5px solid rgba(0,229,255,0.4);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9998;
            transform: translate(-50%, -50%);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .cursor-ring.hovering {
            width: 50px; height: 50px;
            border-color: rgba(168,85,247,0.6);
            background: rgba(168,85,247,0.05);
        }

        /* ═══════════ 3D TILT ═══════════ */
        .tilt-3d {
            transform-style: preserve-3d;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* ═══════════ SEPARATOR ═══════════ */
        .section-sep {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
            max-width: 1280px;
            margin: 0 auto;
        }

        /* Smooth scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════════════════════════════════
     CUSTOM CURSOR
     ═══════════════════════════════════════════════════════════════════ -->
<div class="cursor-dot" id="cursorDot" style="display:none"></div>
<div class="cursor-ring" id="cursorRing" style="display:none"></div>

<!-- ═══════════════════════════════════════════════════════════════════
     NAVIGATION
     ═══════════════════════════════════════════════════════════════════ -->
<nav class="nav" id="mainNav">
    <div class="nav-inner">
        <a href="{{ url('/') }}" class="nav-brand">
            <svg width="22" height="22" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" fill="url(#logo-grad)"/>
                <path d="M10 22V10l6 6 6-6v12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <defs><linearGradient id="logo-grad" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#a855f7"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs>
            </svg>
            <span>VisionLab</span>
            <span class="brand-slash">/</span>
            <span class="brand-edu">edu</span>
        </a>

        <div class="nav-links">
            <a href="#workspace">Workspace</a>
            <a href="#features">Features</a>
            <a href="#governance">Governance</a>
            <a href="#ai">AI</a>
        </div>

        <div class="nav-cta">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding:0.5rem 1.5rem; font-size:0.8rem">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="nav-links" style="display:inline-flex;color:var(--text-muted);text-decoration:none;font-size:0.85rem;font-weight:500">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding:0.5rem 1.5rem; font-size:0.8rem">Deploy Instance</a>
            @endauth
        </div>
    </div>
</nav>


<!-- ═══════════════════════════════════════════════════════════════════
     HERO
     ═══════════════════════════════════════════════════════════════════ -->
<section class="hero" id="top">
    <div class="mesh-bg"></div>

    <!-- Ambient light orbs -->
    <div class="hero-orb" style="width:800px;height:800px;top:-300px;left:-200px;background:rgba(168,85,247,0.06)"></div>
    <div class="hero-orb" style="width:500px;height:500px;bottom:-100px;right:-100px;background:rgba(0,229,255,0.04);animation-delay:-7s"></div>
    <div class="hero-orb" style="width:400px;height:400px;top:40%;right:5%;background:rgba(236,72,153,0.03);animation-delay:-14s"></div>

    <!-- 3D Floating Robot — subtle, ambient -->
    <img src="{{ asset('images/landing/ai-robot.png') }}" alt=""
         style="position:absolute;right:-5%;top:10%;width:380px;opacity:0.25;filter:blur(1px);animation:float-slow 8s ease-in-out infinite;pointer-events:none;z-index:0"
         class="hidden lg:block">

    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto">
        <!-- Badge -->
        <div class="reveal" style="margin-bottom:2.5rem">
            <div style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.4rem 1rem;border-radius:9999px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.03);font-size:0.75rem;color:var(--text-secondary)">
                <span style="width:6px;height:6px;border-radius:50%;background:#10b981;box-shadow:0 0 8px #10b981"></span>
                Aptech Vision 2026 — Competition Entry
            </div>
        </div>

        <!-- Headline -->
        <h1 class="hero-headline reveal reveal-delay-1" style="margin-bottom:1.75rem">
            Code the <span class="font-serif-italic text-gradient-hero" style="font-weight:400">future</span> of<br>
            higher learning.
        </h1>

        <!-- Sub -->
        <p class="hero-sub reveal reveal-delay-2" style="margin-bottom:3rem">
            Sandboxed workspaces, real-time multi-cursor collaboration, and responsible AI assistance — engineered for research universities.
        </p>

        <!-- CTA -->
        <div class="reveal reveal-delay-3" style="display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap;margin-bottom:4rem">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Open Workspace</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary">Try VisionLab</a>
                <a href="{{ route('login') }}" class="btn btn-secondary">Read Docs</a>
            @endauth
        </div>

        <!-- Social proof -->
        <div class="reveal reveal-delay-4" style="display:flex;align-items:center;justify-content:center;gap:1rem">
            <div class="avatar-stack" style="margin-left:0">
                <div class="avatar" style="background:#7c3aed">A</div>
                <div class="avatar" style="background:#0891b2">J</div>
                <div class="avatar" style="background:#059669">S</div>
                <div class="avatar" style="background:#db2777">M</div>
            </div>
            <span style="font-size:0.8rem;color:var(--text-muted)"><span style="color:white;font-weight:600">500+</span> universities joined</span>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="reveal" style="position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:0.5rem;transition-delay:1.2s">
        <span style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--text-muted)">Scroll</span>
        <div style="width:20px;height:32px;border-radius:10px;border:1.5px solid rgba(255,255,255,0.15);display:flex;justify-content:center;padding-top:6px">
            <div style="width:3px;height:8px;border-radius:2px;background:var(--cyan);animation:float-gentle 2s ease-in-out infinite"></div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════
     IDE SHOWCASE
     ═══════════════════════════════════════════════════════════════════ -->
<section id="workspace" class="section">
    <div class="reveal" style="display:flex;flex-direction:column;gap:3rem">
        <!-- Header -->
        <div style="display:grid;grid-template-columns:1fr;gap:2rem;align-items:end" class="lg-two-col-header">
            <h2 class="section-heading" style="max-width:600px">
                Real-time, <span class="font-serif-italic text-gradient-cyan" style="font-weight:400">in-browser</span> IDE — built for the cohort.
            </h2>
            <p class="section-sub" style="max-width:400px;margin-left:auto">
                Every student, instructor, and TA shares one cursor-aware environment. No setup, no drift, just code.
            </p>
        </div>

        <!-- IDE Window -->
        <div class="ide-window tilt-3d" id="ideWindow">
            <div class="ide-titlebar">
                <div class="ide-dot ide-dot-red"></div>
                <div class="ide-dot ide-dot-yellow"></div>
                <div class="ide-dot ide-dot-green"></div>
                <span style="margin-left:1rem;font-size:0.7rem;color:var(--text-muted);font-weight:500">VisionLab — workspace/src/architecture.ts</span>
            </div>

            <div class="ide-body">
                <!-- Sidebar -->
                <div class="ide-sidebar">
                    <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);font-weight:700;margin-bottom:1rem;padding:0 0.5rem">Explorer</div>
                    <div class="file-item"><span class="file-icon">📁</span> src/</div>
                    <div class="file-item active" style="margin-left:1rem"><span class="file-icon">↳</span> architecture.ts</div>
                    <div class="file-item" style="margin-left:1rem"><span class="file-icon">↳</span> QuantumCore.tsx</div>
                    <div class="file-item" style="margin-left:1rem"><span class="file-icon">↳</span> ComputeEngine.rs</div>
                    <div class="file-item"><span class="file-icon">📁</span> tests/</div>
                    <div class="file-item"><span class="file-icon">📄</span> README.md</div>
                </div>

                <!-- Editor -->
                <div class="ide-editor">
                    <div class="code-line"><span class="line-num">1</span><span class="code-comment">// VisionLab Architecture Module</span></div>
                    <div class="code-line"><span class="line-num">2</span><span class="code-keyword">import</span> <span class="code-plain">{ QuantumCore }</span> <span class="code-keyword">from</span> <span class="code-string">'./QuantumCore'</span></div>
                    <div class="code-line"><span class="line-num">3</span></div>
                    <div class="code-line"><span class="line-num">4</span><span class="code-keyword">export interface</span> <span class="code-type">WorkspaceConfig</span> <span class="code-plain">{</span></div>
                    <div class="code-line"><span class="line-num">5</span><span class="code-plain" style="margin-left:1.5rem">sandboxed:</span> <span class="code-type">boolean</span></div>
                    <div class="code-line"><span class="line-num">6</span><span class="code-plain" style="margin-left:1.5rem">maxCursors:</span> <span class="code-type">number</span></div>
                    <div class="code-line"><span class="line-num">7</span><span class="code-plain" style="margin-left:1.5rem">aiMode:</span> <span class="code-string">'socratic'</span> <span class="code-plain">|</span> <span class="code-string">'guided'</span> <span class="code-plain">|</span> <span class="code-string">'autonomous'</span></div>
                    <div class="code-line"><span class="line-num">8</span><span class="code-plain">}</span></div>
                    <div class="code-line"><span class="line-num">9</span></div>
                    <div class="code-line"><span class="line-num">10</span><span class="code-keyword">export class</span> <span class="code-func">VisionEngine</span> <span class="code-plain">{</span></div>
                    <div class="code-line"><span class="line-num">11</span><span class="code-plain" style="margin-left:1.5rem">private core = </span><span class="code-keyword">new</span> <span class="code-func">QuantumCore</span><span class="code-plain">()</span></div>
                    <div class="code-line"><span class="line-num">12</span></div>
                    <div class="code-line"><span class="line-num">13</span><span class="code-plain" style="margin-left:1.5rem"></span><span class="code-keyword">async</span> <span class="code-func">initialize</span><span class="code-plain">(config: </span><span class="code-type">WorkspaceConfig</span><span class="code-plain">) {</span></div>
                    <div class="code-line"><span class="line-num">14</span><span class="code-plain" style="margin-left:3rem"></span><span class="code-keyword">await</span> <span class="code-plain">this.core.</span><span class="code-func">boot</span><span class="code-plain">(config)</span></div>
                    <div class="code-line"><span class="line-num">15</span><span class="code-plain" style="margin-left:1.5rem">}</span></div>
                    <div class="code-line"><span class="line-num">16</span><span class="code-plain">}</span></div>
                </div>
            </div>

            <!-- Presence bar -->
            <div class="presence-bar">
                <div class="avatar-stack">
                    <div class="avatar" style="background:#7c3aed">P</div>
                    <div class="avatar" style="background:#0891b2">M</div>
                    <div class="avatar" style="background:#059669">A</div>
                    <div class="avatar" style="background:#db2777">S</div>
                </div>
                <span style="font-size:0.75rem;color:var(--text-muted)">Prof. Aris, Marcus V., Alina K., Sana D. <span style="color:var(--text-secondary)">+ 20 more</span></span>
            </div>
        </div>
    </div>

    <style>
        @media (min-width: 1024px) {
            .lg-two-col-header { grid-template-columns: 1.2fr 0.8fr !important; }
        }
    </style>
</section>

<div class="section-sep"></div>

<!-- ═══════════════════════════════════════════════════════════════════
     FIVE PRIMITIVES
     ═══════════════════════════════════════════════════════════════════ -->
<section id="features" class="section">
    <h2 class="section-heading reveal" style="margin-bottom:3rem">
        Five primitives. <span class="font-serif-italic" style="color:var(--text-muted);font-weight:400">One platform.</span>
    </h2>

    <div class="feature-grid">
        @php
        $primitives = [
            ['num' => '01', 'title' => 'Sandboxed Nodes', 'desc' => 'Isolated, ephemeral containers spin up per student in under a second. Zero local config.', 'color' => '#00e5ff'],
            ['num' => '02', 'title' => 'Multi-Cursor Sync', 'desc' => 'Sub-frame latency cursors with faculty governance baked into the protocol layer.', 'color' => '#a855f7'],
            ['num' => '03', 'title' => 'Responsible AI', 'desc' => 'AI that explains reasoning before answers. Audit trail on every completion.', 'color' => '#10b981'],
            ['num' => '04', 'title' => 'Live Sessions', 'desc' => 'WebRTC voice & video stitched into the editor — office hours, anywhere.', 'color' => '#ec4899'],
            ['num' => '05', 'title' => 'LMS Sync', 'desc' => 'Native bridges to Canvas, Moodle, Blackboard. Grades flow back automatically.', 'color' => '#3b82f6'],
        ];
        @endphp

        @foreach($primitives as $i => $p)
        <div class="card feature-card tilt-3d reveal" style="transition-delay:{{ $i * 0.08 }}s">
            <div class="feature-dot" style="background:{{ $p['color'] }};box-shadow:0 0 10px {{ $p['color'] }}"></div>
            <div class="feature-num">{{ $p['num'] }}</div>
            <h3 class="feature-title">{{ $p['title'] }}</h3>
            <p class="feature-desc">{{ $p['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

<div class="section-sep"></div>

<!-- ═══════════════════════════════════════════════════════════════════
     INSTRUCTOR COMMAND DECK
     ═══════════════════════════════════════════════════════════════════ -->
<section id="governance" class="section">
    <div class="instructor-grid">
        <!-- Left: Content -->
        <div class="reveal">
            <h2 class="section-heading" style="margin-bottom:1.5rem">
                The instructor <span class="font-serif-italic text-gradient-purple-pink" style="font-weight:400">command<br>deck.</span>
            </h2>
            <p class="section-sub" style="margin-bottom:3rem">
                Observe an entire lecture hall in real time. Peek into any workspace, broadcast intent, throttle resources, and replay sessions for review — all from one console.
            </p>

            <div style="display:flex;flex-direction:column;gap:0.5rem">
                <div class="step-item">
                    <div class="step-letter" style="background:rgba(0,229,255,0.15);color:var(--cyan)">A</div>
                    <div>
                        <div class="step-title">Live Telemetry</div>
                        <div class="step-desc">Per-student keystrokes, compute spend, AI usage — streaming.</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-letter" style="background:rgba(168,85,247,0.15);color:var(--purple)">B</div>
                    <div>
                        <div class="step-title">Resource Throttling</div>
                        <div class="step-desc">Cap CPU/GPU per cohort. Burst budgets, audited.</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-letter" style="background:rgba(16,185,129,0.15);color:var(--emerald)">C</div>
                    <div>
                        <div class="step-title">Session Replay</div>
                        <div class="step-desc">Scrub through any session like a video. Diff every commit.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Monitor Grid -->
        <div class="reveal reveal-delay-2">
            <div class="card" style="padding:1.5rem;background:var(--surface)">
                <div class="monitor-grid">
                    @php
                    $students = ['Aris N.','Marcus V.','Alina K.','Sana D.','Jake P.','Chen W.','Omar F.','Lisa T.','Ryan M.','Dev S.','Amy L.','Noah R.','Zara K.','Leo H.','Mia C.'];
                    $colors = ['#00e5ff','#a855f7','#10b981','#ec4899','#3b82f6','#f59e0b'];
                    @endphp
                    @foreach($students as $idx => $name)
                    <div class="monitor-cell">
                        <div style="display:flex;align-items:center;justify-content:space-between">
                            <div style="display:flex;align-items:center;gap:0.4rem">
                                <div style="width:18px;height:18px;border-radius:50%;background:{{ $colors[$idx % count($colors)] }}33;display:flex;align-items:center;justify-content:center">
                                    <span style="font-size:0.5rem;font-weight:700;color:{{ $colors[$idx % count($colors)] }}">{{ strtoupper(substr($name,0,1)) }}</span>
                                </div>
                                <span style="font-size:0.6rem;color:var(--text-secondary);font-weight:500">{{ $name }}</span>
                            </div>
                            <span style="width:5px;height:5px;border-radius:50%;background:#10b981;box-shadow:0 0 6px #10b981"></span>
                        </div>
                        <div class="monitor-bar">
                            <div class="monitor-bar-fill" style="width:{{ rand(25, 95) }}%;background:{{ $colors[$idx % count($colors)] }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- ═══════════════════════════════════════════════════════════════════
     AI TEACHING ASSISTANT
     ═══════════════════════════════════════════════════════════════════ -->
<section id="ai" class="section">
    <div class="reveal">
        <h2 class="section-heading" style="margin-bottom:0.75rem">
            AI as a <span class="font-serif-italic text-gradient-cyan" style="font-weight:400">teaching assistant</span><br>
            — not an answer machine.
        </h2>
        <p class="section-sub" style="margin-bottom:3rem">
            Every completion is logged, attributed, and graded against academic-integrity policy. Students learn the <em>why</em> before the <em>what</em>.
        </p>
    </div>

    <div class="ai-cards">
        <!-- Student asks -->
        <div class="card reveal" style="transition-delay:0.05s">
            <div style="padding:1.75rem">
                <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.15em;color:var(--text-muted);font-weight:700;margin-bottom:1.25rem">Student asks.</div>
                <div class="ai-msg" style="background:rgba(255,255,255,0.03);border:1px solid var(--border);color:var(--text-secondary)">
                    Why is my binary search returning -1 on a sorted array of 1M ints?
                </div>
            </div>
        </div>

        <!-- AI reasons -->
        <div class="card reveal" style="transition-delay:0.15s;border-color:rgba(168,85,247,0.2);background:rgba(168,85,247,0.03)">
            <div style="padding:1.75rem">
                <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.15em;color:var(--purple);font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.4rem">
                    <span style="width:6px;height:6px;border-radius:50%;background:var(--purple);box-shadow:0 0 8px var(--purple)"></span>
                    AI reasons.
                </div>
                <div class="ai-msg font-mono" style="background:var(--surface);border:1px solid var(--border);color:var(--text-secondary);font-size:0.8rem">
                    <span style="color:var(--text-muted)">// Analyzing student's code...</span><br>
                    <span style="color:var(--purple)">mid</span> = <span style="color:var(--cyan)">(lo + hi)</span> / 2<br>
                    <span style="color:#f59e0b">⚠ Integer overflow</span> when lo + hi > 2³¹
                </div>
            </div>
        </div>

        <!-- AI guides -->
        <div class="card reveal" style="transition-delay:0.25s">
            <div style="padding:1.75rem">
                <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.15em;color:var(--emerald);font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.4rem">
                    <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);box-shadow:0 0 8px var(--emerald)"></span>
                    AI guides, doesn't dictate.
                </div>
                <div class="ai-msg" style="background:rgba(255,255,255,0.03);border:1px solid var(--border);color:var(--text-secondary)">
                    Try tracing the values of <code class="font-mono" style="color:var(--cyan);font-size:0.8rem">lo</code>, <code class="font-mono" style="color:var(--cyan);font-size:0.8rem">hi</code>, <code class="font-mono" style="color:var(--cyan);font-size:0.8rem">mid</code> for the last 3 iterations. Want a Socratic walkthrough or the patch?
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- ═══════════════════════════════════════════════════════════════════
     CTA — Ready for production?
     ═══════════════════════════════════════════════════════════════════ -->
<section class="cta-section" id="cta">
    <div style="position:relative;z-index:10" class="reveal">
        <h2 style="font-size:clamp(2rem,5vw,3.5rem);font-weight:700;letter-spacing:-0.03em;line-height:1.15;margin-bottom:1.5rem">
            Ready for <span class="font-serif-italic text-gradient-purple-pink" style="font-weight:400">production</span>?
        </h2>
        <p style="font-size:1rem;color:var(--text-secondary);max-width:500px;margin:0 auto 2.5rem;line-height:1.7">
            Deploy VisionLab across your institution. Onboarding in days, not quarters.
        </p>
        <div style="display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary">Deploy Instance</a>
                <a href="#" class="btn btn-secondary">Request Demo</a>
            @endauth
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════════════════ -->
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-links">
            <a href="#">Security</a>
            <a href="#">Privacy</a>
            <a href="#">Status</a>
            <a href="#">Docs</a>
            <a href="#">Contact</a>
        </div>
        <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;padding-top:2rem;border-top:1px solid var(--border)">
            <div style="display:flex;align-items:center;gap:0.5rem">
                <svg width="18" height="18" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="url(#logo-grad-f)"/>
                    <path d="M10 22V10l6 6 6-6v12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <defs><linearGradient id="logo-grad-f" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#a855f7"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs>
                </svg>
                <span style="font-size:0.85rem;font-weight:700;color:white">VisionLab</span>
            </div>
            <p style="font-size:0.7rem;color:var(--text-muted)">
                Built for Aptech Vision 2026. All rights reserved.
            </p>
        </div>
    </div>
</footer>


<!-- ═══════════════════════════════════════════════════════════════════
     JAVASCRIPT — Interactions, Tilt, Cursor, Reveals
     ═══════════════════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ─── Custom Cursor ───
    const dot = document.getElementById('cursorDot');
    const ring = document.getElementById('cursorRing');
    const isMobile = window.matchMedia('(max-width: 767px)').matches;

    if (!isMobile && dot && ring) {
        dot.style.display = 'block';
        ring.style.display = 'block';

        let mouseX = 0, mouseY = 0, ringX = 0, ringY = 0;

        document.addEventListener('mousemove', e => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            dot.style.left = mouseX + 'px';
            dot.style.top = mouseY + 'px';
        });

        // Smooth follow for the ring
        (function animateRing() {
            ringX += (mouseX - ringX) * 0.15;
            ringY += (mouseY - ringY) * 0.15;
            ring.style.left = ringX + 'px';
            ring.style.top = ringY + 'px';
            requestAnimationFrame(animateRing);
        })();

        // Hover expansion on interactive elements
        document.querySelectorAll('a, button, .card, .tilt-3d, .btn').forEach(el => {
            el.addEventListener('mouseenter', () => ring.classList.add('hovering'));
            el.addEventListener('mouseleave', () => ring.classList.remove('hovering'));
        });
    }

    // ─── Scroll Reveal ───
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // ─── Nav scroll state ───
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    }, { passive: true });

    // ─── 3D Tilt on cards ───
    document.querySelectorAll('.tilt-3d').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const midX = rect.width / 2;
            const midY = rect.height / 2;
            const rotX = ((y - midY) / midY) * -8;
            const rotY = ((x - midX) / midX) * 8;
            card.style.transform = `perspective(800px) rotateX(${rotX}deg) rotateY(${rotY}deg) scale3d(1.02,1.02,1.02)`;
            card.style.transition = 'none';

            // Mouse-tracking glow for cards with ::before
            card.style.setProperty('--mouse-x', x + 'px');
            card.style.setProperty('--mouse-y', y + 'px');
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(800px) rotateX(0) rotateY(0) scale3d(1,1,1)';
            card.style.transition = 'transform 0.6s cubic-bezier(0.16,1,0.3,1)';
        });
    });

    // ─── Parallax on scroll for hero orbs ───
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                const scrollY = window.scrollY;
                document.querySelectorAll('.hero-orb').forEach((orb, i) => {
                    const speed = 0.03 + (i * 0.015);
                    orb.style.transform = `translateY(${scrollY * speed}px)`;
                });
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    // ─── Animated monitor bars ───
    const monitorObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.querySelectorAll('.monitor-bar-fill').forEach(bar => {
                    const w = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => { bar.style.width = w; }, 100);
                });
                monitorObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    const monitorGrid = document.querySelector('.monitor-grid');
    if (monitorGrid) monitorObserver.observe(monitorGrid.closest('.card'));

});
</script>

</body>
</html>

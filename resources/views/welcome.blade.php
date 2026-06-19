<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="The collaborative IDE built for research universities. Sandboxed, audited, and AI-assisted.">
    <title>VisionLab — Collaborative coding for universities</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,600;1,700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Three.js via Import Map (CDN — zero npm dependency) -->
    <script type="importmap">
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.164.1/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.164.1/examples/jsm/"
        }
    }
    </script>

    <style>
        /* ═══════════════════════════════════════════════════════════════════
           DESIGN SYSTEM — Lovable Reference + 3D Enhancements
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
        html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
        body {
            background: var(--bg);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }

        .font-serif-italic { font-family: 'Playfair Display', Georgia, serif; font-style: italic; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        .text-gradient-hero {
            background: linear-gradient(135deg, var(--purple), var(--pink), var(--cyan));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .text-gradient-purple-pink {
            background: linear-gradient(to right, var(--purple), var(--pink));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .text-gradient-cyan {
            background: linear-gradient(to right, var(--cyan), #3b82f6);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }

        /* ═══════════ NAV ═══════════ */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(3,3,3,0.8); backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border); transition: all 0.3s;
        }
        .nav.scrolled { background: rgba(3,3,3,0.95); box-shadow: 0 4px 30px rgba(0,0,0,0.5); }
        .nav-inner { max-width: 1280px; margin: 0 auto; padding: 0 2rem; height: 64px; display: flex; align-items: center; justify-content: space-between; }
        .nav-brand { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: white; }
        .nav-brand span { font-weight: 700; font-size: 1.1rem; letter-spacing: -0.02em; }
        .nav-brand .brand-slash { color: var(--text-muted); font-weight: 300; margin: 0 0.25rem; }
        .nav-brand .brand-edu { color: var(--text-secondary); font-weight: 400; font-size: 0.85rem; }
        .nav-links { display: none; align-items: center; gap: 2rem; }
        @media (min-width: 768px) { .nav-links { display: flex; } }
        .nav-links a { color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; }
        .nav-links a:hover { color: white; }

        /* ═══════════ BUTTONS ═══════════ */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-size: 0.875rem; font-weight: 600; border-radius: 9999px; text-decoration: none; transition: all 0.3s cubic-bezier(0.16,1,0.3,1); cursor: pointer; border: none; white-space: nowrap; }
        .btn-primary { background: var(--cyan); color: #000; box-shadow: 0 0 20px var(--cyan-glow), 0 0 60px rgba(0,229,255,0.15); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 0 30px var(--cyan-glow), 0 0 80px rgba(0,229,255,0.25); background: #33eeff; }
        .btn-secondary { background: transparent; color: white; border: 1px solid rgba(255,255,255,0.15); }
        .btn-secondary:hover { border-color: rgba(255,255,255,0.4); background: rgba(255,255,255,0.03); }

        /* ═══════════ CARDS ═══════════ */
        .card {
            background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 1rem;
            transition: all 0.4s cubic-bezier(0.16,1,0.3,1); position: relative; overflow: hidden;
        }
        .card:hover { border-color: var(--border-hover); background: rgba(255,255,255,0.035); }
        .card::before { content: ''; position: absolute; inset: 0; border-radius: inherit; background: radial-gradient(600px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(255,255,255,0.03), transparent 40%); opacity: 0; transition: opacity 0.3s; pointer-events: none; }
        .card:hover::before { opacity: 1; }

        /* ═══════════ 3D CANVAS CONTAINERS ═══════════ */
        .canvas-container { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
        .canvas-container canvas { display: block; width: 100%; height: 100%; }
        .canvas-interactive { pointer-events: auto; }

        /* ═══════════ SECTIONS ═══════════ */
        .section { max-width: 1280px; margin: 0 auto; padding: 6rem 2rem; }
        .section-heading { font-size: clamp(2rem, 5vw, 3.25rem); font-weight: 700; line-height: 1.15; letter-spacing: -0.03em; margin-bottom: 1rem; }
        .section-sub { font-size: 0.95rem; color: var(--text-secondary); max-width: 600px; line-height: 1.7; }
        .section-sep { width: 100%; height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); max-width: 1280px; margin: 0 auto; }

        /* ═══════════ HERO ═══════════ */
        .hero { position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 8rem 2rem 4rem; overflow: hidden; }
        .hero-headline { font-size: clamp(3rem, 7vw, 5.5rem); font-weight: 900; line-height: 1.05; letter-spacing: -0.04em; }
        .hero-sub { font-size: 1.15rem; color: var(--text-secondary); max-width: 640px; margin: 0 auto; line-height: 1.7; }

        /* ═══════════ IDE MOCK ═══════════ */
        .ide-window { position: relative; border-radius: 1rem; overflow: hidden; border: 1px solid var(--border); background: var(--surface); box-shadow: 0 25px 50px rgba(0,0,0,0.5), 0 0 100px rgba(0,229,255,0.05); transition: all 0.5s; }
        .ide-window:hover { box-shadow: 0 30px 60px rgba(0,0,0,0.6), 0 0 120px rgba(0,229,255,0.08); }
        .ide-titlebar { display: flex; align-items: center; gap: 6px; padding: 0.75rem 1rem; background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border); }
        .ide-dot { width: 10px; height: 10px; border-radius: 50%; }
        .ide-body { display: flex; min-height: 350px; }
        .ide-sidebar { width: 220px; border-right: 1px solid var(--border); padding: 1rem; display: none; }
        @media (min-width: 768px) { .ide-sidebar { display: block; } }
        .ide-editor { flex: 1; padding: 1.25rem; }
        .file-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.5rem; border-radius: 0.4rem; font-size: 0.8rem; color: var(--text-secondary); transition: background 0.2s; }
        .file-item:hover { background: rgba(255,255,255,0.04); }
        .file-item.active { background: rgba(0,229,255,0.08); color: var(--cyan); }
        .code-line { font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; line-height: 1.8; display: flex; gap: 1.25rem; }
        .line-num { color: var(--text-muted); user-select: none; min-width: 2ch; text-align: right; }
        .code-keyword { color: #c084fc; } .code-func { color: #67e8f9; } .code-string { color: #86efac; }
        .code-comment { color: #52525b; font-style: italic; } .code-type { color: #fbbf24; } .code-plain { color: #d4d4d8; }
        .presence-bar { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-top: 1px solid var(--border); background: rgba(255,255,255,0.01); }
        .avatar-stack { display: flex; }
        .avatar { width: 28px; height: 28px; border-radius: 50%; border: 2px solid var(--bg); display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 700; color: white; margin-left: -8px; }
        .avatar:first-child { margin-left: 0; }

        /* ═══════════ FEATURES ═══════════ */
        .feature-grid { display: grid; grid-template-columns: 1fr; gap: 1.25rem; }
        @media (min-width: 640px) { .feature-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .feature-grid { grid-template-columns: repeat(3, 1fr); } }
        .feature-card { padding: 2rem; min-height: 200px; display: flex; flex-direction: column; }
        .feature-num { font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.15em; font-weight: 700; margin-bottom: 1.5rem; }
        .feature-title { font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem; }
        .feature-desc { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6; margin-top: auto; }
        .feature-dot { position: absolute; top: 1.5rem; right: 1.5rem; width: 6px; height: 6px; border-radius: 50%; }

        /* ═══════════ INSTRUCTOR ═══════════ */
        .instructor-grid { display: grid; grid-template-columns: 1fr; gap: 4rem; align-items: center; }
        @media (min-width: 1024px) { .instructor-grid { grid-template-columns: 1fr 1fr; } }
        .step-item { display: flex; align-items: flex-start; gap: 1rem; padding: 1.25rem 0; }
        .step-letter { width: 32px; height: 32px; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 800; flex-shrink: 0; }
        .step-title { font-size: 0.9rem; font-weight: 700; margin-bottom: 0.25rem; }
        .step-desc { font-size: 0.8rem; color: var(--text-muted); line-height: 1.5; }
        .monitor-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
        .monitor-cell { padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border); background: var(--surface); }
        .monitor-bar { height: 3px; border-radius: 2px; background: var(--surface-2); margin-top: 0.5rem; overflow: hidden; }
        .monitor-bar-fill { height: 100%; border-radius: 2px; transition: width 1.5s ease; }

        /* ═══════════ AI CARDS ═══════════ */
        .ai-cards { display: grid; grid-template-columns: 1fr; gap: 1.25rem; }
        @media (min-width: 768px) { .ai-cards { grid-template-columns: repeat(3, 1fr); } }
        .ai-msg { padding: 1.25rem; border-radius: 0.75rem; font-size: 0.875rem; line-height: 1.6; }

        /* ═══════════ CTA ═══════════ */
        .cta-section { position: relative; text-align: center; padding: 8rem 2rem; overflow: hidden; }

        /* ═══════════ FOOTER ═══════════ */
        .footer { border-top: 1px solid var(--border); padding: 3rem 2rem; background: var(--bg); }
        .footer-inner { max-width: 1280px; margin: 0 auto; }
        .footer-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; margin-bottom: 2rem; }
        .footer-links a { color: var(--text-muted); text-decoration: none; font-size: 0.8rem; font-weight: 500; transition: color 0.2s; }
        .footer-links a:hover { color: white; }

        /* ═══════════ REVEAL ═══════════ */
        .reveal { opacity: 0; transform: translateY(40px); transition: opacity 0.9s cubic-bezier(0.16,1,0.3,1), transform 0.9s cubic-bezier(0.16,1,0.3,1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; } .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; } .reveal-delay-4 { transition-delay: 0.4s; }

        /* ═══════════ CURSOR ═══════════ */
        .cursor-dot { position: fixed; width: 6px; height: 6px; background: var(--cyan); border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%,-50%); box-shadow: 0 0 12px var(--cyan); transition: transform 0.15s; }
        .cursor-ring { position: fixed; width: 36px; height: 36px; border: 1.5px solid rgba(0,229,255,0.4); border-radius: 50%; pointer-events: none; z-index: 9998; transform: translate(-50%,-50%); transition: all 0.2s cubic-bezier(0.16,1,0.3,1); }
        .cursor-ring.hovering { width: 50px; height: 50px; border-color: rgba(168,85,247,0.6); background: rgba(168,85,247,0.05); }

        /* ═══════════ 3D TILT ═══════════ */
        .tilt-3d { transform-style: preserve-3d; transition: transform 0.4s cubic-bezier(0.16,1,0.3,1); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; } ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
    </style>
</head>
<body>

<!-- Cursor -->
<div class="cursor-dot" id="cursorDot" style="display:none"></div>
<div class="cursor-ring" id="cursorRing" style="display:none"></div>

<!-- ═══════════════════════════════════════════════════════════════════
     NAVIGATION
     ═══════════════════════════════════════════════════════════════════ -->
<nav class="nav" id="mainNav">
    <div class="nav-inner">
        <a href="{{ url('/') }}" class="nav-brand">
            <svg width="22" height="22" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="url(#lg)"/><path d="M10 22V10l6 6 6-6v12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="lg" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#a855f7"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs></svg>
            <span>VisionLab</span><span class="brand-slash">/</span><span class="brand-edu">edu</span>
        </a>
        <div class="nav-links">
            <a href="#workspace">Workspace</a><a href="#features">Features</a><a href="#governance">Governance</a><a href="#ai">AI</a>
        </div>
        <div style="display:flex;align-items:center;gap:1rem">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding:0.5rem 1.5rem;font-size:0.8rem">Dashboard</a>
            @else
                <a href="{{ route('login') }}" style="color:var(--text-muted);text-decoration:none;font-size:0.85rem;font-weight:500;display:none" class="nav-links">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding:0.5rem 1.5rem;font-size:0.8rem">Deploy Instance</a>
            @endauth
        </div>
    </div>
</nav>

<!-- ═══════════════════════════════════════════════════════════════════
     HERO — Full 3D Scene Background
     ═══════════════════════════════════════════════════════════════════ -->
<section class="hero" id="top">
    <!-- Three.js Canvas for hero particles + 3D robot -->
    <div class="canvas-container canvas-interactive" id="heroCanvasContainer"></div>

    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto">
        <div class="reveal" style="margin-bottom:2.5rem">
            <div style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.4rem 1rem;border-radius:9999px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.03);font-size:0.75rem;color:var(--text-secondary)">
                <span style="width:6px;height:6px;border-radius:50%;background:#10b981;box-shadow:0 0 8px #10b981"></span>
                Aptech Vision 2026 — Competition Entry
            </div>
        </div>
        <h1 class="hero-headline reveal reveal-delay-1" style="margin-bottom:1.75rem">
            Code the <span class="font-serif-italic text-gradient-hero" style="font-weight:400">future</span> of<br>higher learning.
        </h1>
        <p class="hero-sub reveal reveal-delay-2" style="margin-bottom:3rem">
            Sandboxed workspaces, real-time multi-cursor collaboration, and responsible AI assistance — engineered for research universities.
        </p>
        <div class="reveal reveal-delay-3" style="display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap;margin-bottom:4rem">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Open Workspace</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary">Try VisionLab</a>
                <a href="{{ route('login') }}" class="btn btn-secondary">Read Docs</a>
            @endauth
        </div>
        <div class="reveal reveal-delay-4" style="display:flex;align-items:center;justify-content:center;gap:1rem">
            <div class="avatar-stack" style="margin-left:0">
                <div class="avatar" style="background:#7c3aed">A</div><div class="avatar" style="background:#0891b2">J</div><div class="avatar" style="background:#059669">S</div><div class="avatar" style="background:#db2777">M</div>
            </div>
            <span style="font-size:0.8rem;color:var(--text-muted)"><span style="color:white;font-weight:600">500+</span> universities joined</span>
        </div>
    </div>
    <div class="reveal" style="position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:0.5rem;transition-delay:1.2s">
        <span style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--text-muted)">Scroll</span>
        <div style="width:20px;height:32px;border-radius:10px;border:1.5px solid rgba(255,255,255,0.15);display:flex;justify-content:center;padding-top:6px">
            <div style="width:3px;height:8px;border-radius:2px;background:var(--cyan);animation:scrollBounce 2s ease-in-out infinite"></div>
        </div>
    </div>
    <style>@keyframes scrollBounce { 0%,100%{transform:translateY(0);opacity:1} 50%{transform:translateY(6px);opacity:0.3} }</style>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     IDE SHOWCASE — with floating 3D elements
     ═══════════════════════════════════════════════════════════════════ -->
<section id="workspace" class="section" style="position:relative">
    <div class="reveal" style="display:flex;flex-direction:column;gap:3rem">
        <div style="display:grid;grid-template-columns:1fr;gap:2rem;align-items:end" class="lg-header">
            <h2 class="section-heading" style="max-width:600px">
                Real-time, <span class="font-serif-italic text-gradient-cyan" style="font-weight:400">in-browser</span> IDE — built for the cohort.
            </h2>
            <p class="section-sub" style="max-width:400px;margin-left:auto">
                Every student, instructor, and TA shares one cursor-aware environment. No setup, no drift, just code.
            </p>
        </div>
        <div class="ide-window tilt-3d" id="ideWindow" style="position:relative">
            <!-- Floating 3D canvas behind IDE -->
            <div id="ideFloatingCanvas" style="position:absolute;inset:-60px;z-index:-1;pointer-events:none"></div>
            <div class="ide-titlebar">
                <div class="ide-dot" style="background:#ff5f57"></div><div class="ide-dot" style="background:#febc2e"></div><div class="ide-dot" style="background:#28c840"></div>
                <span style="margin-left:1rem;font-size:0.7rem;color:var(--text-muted);font-weight:500">VisionLab — workspace/src/architecture.ts</span>
            </div>
            <div class="ide-body">
                <div class="ide-sidebar">
                    <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);font-weight:700;margin-bottom:1rem;padding:0 0.5rem">Explorer</div>
                    <div class="file-item"><span style="font-size:0.7rem;opacity:0.5">📁</span> src/</div>
                    <div class="file-item active" style="margin-left:1rem"><span style="font-size:0.7rem;opacity:0.5">↳</span> architecture.ts</div>
                    <div class="file-item" style="margin-left:1rem"><span style="font-size:0.7rem;opacity:0.5">↳</span> QuantumCore.tsx</div>
                    <div class="file-item" style="margin-left:1rem"><span style="font-size:0.7rem;opacity:0.5">↳</span> ComputeEngine.rs</div>
                    <div class="file-item"><span style="font-size:0.7rem;opacity:0.5">📁</span> tests/</div>
                    <div class="file-item"><span style="font-size:0.7rem;opacity:0.5">📄</span> README.md</div>
                </div>
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
            <div class="presence-bar">
                <div class="avatar-stack"><div class="avatar" style="background:#7c3aed">P</div><div class="avatar" style="background:#0891b2">M</div><div class="avatar" style="background:#059669">A</div><div class="avatar" style="background:#db2777">S</div></div>
                <span style="font-size:0.75rem;color:var(--text-muted)">Prof. Aris, Marcus V., Alina K., Sana D. <span style="color:var(--text-secondary)">+ 20 more</span></span>
            </div>
        </div>
    </div>
    <style>@media (min-width: 1024px) { .lg-header { grid-template-columns: 1.2fr 0.8fr !important; } }</style>
</section>

<div class="section-sep"></div>

<!-- ═══════════════════════════════════════════════════════════════════
     FIVE PRIMITIVES — with 3D floating icon scene
     ═══════════════════════════════════════════════════════════════════ -->
<section id="features" class="section" style="position:relative">
    <!-- 3D scene behind feature cards -->
    <div id="featuresCanvasContainer" style="position:absolute;inset:0;z-index:0;pointer-events:none;opacity:0.5"></div>
    
    <div style="position:relative;z-index:1">
        <h2 class="section-heading reveal" style="margin-bottom:3rem">
            Five primitives. <span class="font-serif-italic" style="color:var(--text-muted);font-weight:400">One platform.</span>
        </h2>
        <div class="feature-grid">
            @php
            $primitives = [
                ['num'=>'01','title'=>'Sandboxed Nodes','desc'=>'Isolated, ephemeral containers spin up per student in under a second. Zero local config.','color'=>'#00e5ff'],
                ['num'=>'02','title'=>'Multi-Cursor Sync','desc'=>'Sub-frame latency cursors with faculty governance baked into the protocol layer.','color'=>'#a855f7'],
                ['num'=>'03','title'=>'Responsible AI','desc'=>'AI that explains reasoning before answers. Audit trail on every completion.','color'=>'#10b981'],
                ['num'=>'04','title'=>'Live Sessions','desc'=>'WebRTC voice & video stitched into the editor — office hours, anywhere.','color'=>'#ec4899'],
                ['num'=>'05','title'=>'LMS Sync','desc'=>'Native bridges to Canvas, Moodle, Blackboard. Grades flow back automatically.','color'=>'#3b82f6'],
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
    </div>
</section>

<div class="section-sep"></div>

<!-- ═══════════════════════════════════════════════════════════════════
     INSTRUCTOR COMMAND DECK
     ═══════════════════════════════════════════════════════════════════ -->
<section id="governance" class="section">
    <div class="instructor-grid">
        <div class="reveal">
            <h2 class="section-heading" style="margin-bottom:1.5rem">
                The instructor <span class="font-serif-italic text-gradient-purple-pink" style="font-weight:400">command<br>deck.</span>
            </h2>
            <p class="section-sub" style="margin-bottom:3rem">
                Observe an entire lecture hall in real time. Peek into any workspace, broadcast intent, throttle resources, and replay sessions — all from one console.
            </p>
            <div style="display:flex;flex-direction:column;gap:0.5rem">
                <div class="step-item"><div class="step-letter" style="background:rgba(0,229,255,0.15);color:var(--cyan)">A</div><div><div class="step-title">Live Telemetry</div><div class="step-desc">Per-student keystrokes, compute spend, AI usage — streaming.</div></div></div>
                <div class="step-item"><div class="step-letter" style="background:rgba(168,85,247,0.15);color:var(--purple)">B</div><div><div class="step-title">Resource Throttling</div><div class="step-desc">Cap CPU/GPU per cohort. Burst budgets, audited.</div></div></div>
                <div class="step-item"><div class="step-letter" style="background:rgba(16,185,129,0.15);color:var(--emerald)">C</div><div><div class="step-title">Session Replay</div><div class="step-desc">Scrub through any session like a video. Diff every commit.</div></div></div>
            </div>
        </div>
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
                        <div class="monitor-bar"><div class="monitor-bar-fill" style="width:{{ rand(25,95) }}%;background:{{ $colors[$idx % count($colors)] }}"></div></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- ═══════════════════════════════════════════════════════════════════
     AI TEACHING ASSISTANT — with 3D brain/sphere
     ═══════════════════════════════════════════════════════════════════ -->
<section id="ai" class="section" style="position:relative">
    <div id="aiCanvasContainer" style="position:absolute;right:0;top:0;width:50%;height:100%;z-index:0;pointer-events:none;opacity:0.6"></div>
    <div style="position:relative;z-index:1">
        <div class="reveal">
            <h2 class="section-heading" style="margin-bottom:0.75rem">
                AI as a <span class="font-serif-italic text-gradient-cyan" style="font-weight:400">teaching assistant</span><br>— not an answer machine.
            </h2>
            <p class="section-sub" style="margin-bottom:3rem">
                Every completion is logged, attributed, and graded against academic-integrity policy. Students learn the <em>why</em> before the <em>what</em>.
            </p>
        </div>
        <div class="ai-cards">
            <div class="card reveal" style="transition-delay:0.05s">
                <div style="padding:1.75rem">
                    <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.15em;color:var(--text-muted);font-weight:700;margin-bottom:1.25rem">Student asks.</div>
                    <div class="ai-msg" style="background:rgba(255,255,255,0.03);border:1px solid var(--border);color:var(--text-secondary)">Why is my binary search returning -1 on a sorted array of 1M ints?</div>
                </div>
            </div>
            <div class="card reveal" style="transition-delay:0.15s;border-color:rgba(168,85,247,0.2);background:rgba(168,85,247,0.03)">
                <div style="padding:1.75rem">
                    <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.15em;color:var(--purple);font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.4rem">
                        <span style="width:6px;height:6px;border-radius:50%;background:var(--purple);box-shadow:0 0 8px var(--purple)"></span> AI reasons.
                    </div>
                    <div class="ai-msg font-mono" style="background:var(--surface);border:1px solid var(--border);color:var(--text-secondary);font-size:0.8rem">
                        <span style="color:var(--text-muted)">// Analyzing...</span><br>
                        <span style="color:var(--purple)">mid</span> = <span style="color:var(--cyan)">(lo + hi)</span> / 2<br>
                        <span style="color:#f59e0b">⚠ Integer overflow</span> when lo + hi > 2³¹
                    </div>
                </div>
            </div>
            <div class="card reveal" style="transition-delay:0.25s">
                <div style="padding:1.75rem">
                    <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.15em;color:var(--emerald);font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.4rem">
                        <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);box-shadow:0 0 8px var(--emerald)"></span> AI guides, doesn't dictate.
                    </div>
                    <div class="ai-msg" style="background:rgba(255,255,255,0.03);border:1px solid var(--border);color:var(--text-secondary)">
                        Try tracing <code class="font-mono" style="color:var(--cyan);font-size:0.8rem">lo</code>, <code class="font-mono" style="color:var(--cyan);font-size:0.8rem">hi</code>, <code class="font-mono" style="color:var(--cyan);font-size:0.8rem">mid</code> for the last 3 iterations. Want a Socratic walkthrough or the patch?
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- CTA -->
<section class="cta-section" id="cta" style="position:relative">
    <div id="ctaCanvasContainer" style="position:absolute;inset:0;z-index:0;pointer-events:none"></div>
    <div style="position:relative;z-index:10" class="reveal">
        <h2 style="font-size:clamp(2rem,5vw,3.5rem);font-weight:700;letter-spacing:-0.03em;line-height:1.15;margin-bottom:1.5rem">
            Ready for <span class="font-serif-italic text-gradient-purple-pink" style="font-weight:400">production</span>?
        </h2>
        <p style="font-size:1rem;color:var(--text-secondary);max-width:500px;margin:0 auto 2.5rem;line-height:1.7">Deploy VisionLab across your institution. Onboarding in days, not quarters.</p>
        <div style="display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap">
            @auth <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
            @else <a href="{{ route('register') }}" class="btn btn-primary">Deploy Instance</a><a href="#" class="btn btn-secondary">Request Demo</a>
            @endauth
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-links"><a href="#">Security</a><a href="#">Privacy</a><a href="#">Status</a><a href="#">Docs</a><a href="#">Contact</a></div>
        <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;padding-top:2rem;border-top:1px solid var(--border)">
            <div style="display:flex;align-items:center;gap:0.5rem">
                <svg width="18" height="18" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="url(#lgf)"/><path d="M10 22V10l6 6 6-6v12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="lgf" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#a855f7"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs></svg>
                <span style="font-size:0.85rem;font-weight:700">VisionLab</span>
            </div>
            <p style="font-size:0.7rem;color:var(--text-muted)">Built for Aptech Vision 2026. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- ═══════════════════════════════════════════════════════════════════
     UI INTERACTIONS (Cursor, Tilt, Reveals, Nav)
     ═══════════════════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Custom Cursor
    const dot = document.getElementById('cursorDot'), ring = document.getElementById('cursorRing');
    if (!window.matchMedia('(max-width:767px)').matches && dot && ring) {
        dot.style.display='block'; ring.style.display='block';
        let mx=0,my=0,rx=0,ry=0;
        document.addEventListener('mousemove', e => { mx=e.clientX; my=e.clientY; dot.style.left=mx+'px'; dot.style.top=my+'px'; });
        (function anim(){rx+=(mx-rx)*0.15;ry+=(my-ry)*0.15;ring.style.left=rx+'px';ring.style.top=ry+'px';requestAnimationFrame(anim)})();
        document.querySelectorAll('a,button,.card,.tilt-3d,.btn').forEach(el=>{
            el.addEventListener('mouseenter',()=>ring.classList.add('hovering'));
            el.addEventListener('mouseleave',()=>ring.classList.remove('hovering'));
        });
    }
    // Scroll Reveal
    const ro = new IntersectionObserver(e=>e.forEach(en=>{if(en.isIntersecting)en.target.classList.add('visible')}),{threshold:0.08,rootMargin:'0px 0px -40px 0px'});
    document.querySelectorAll('.reveal').forEach(el=>ro.observe(el));
    // Nav scroll
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll',()=>nav.classList.toggle('scrolled',window.scrollY>50),{passive:true});
    // 3D Tilt
    document.querySelectorAll('.tilt-3d').forEach(c=>{
        c.addEventListener('mousemove',e=>{const r=c.getBoundingClientRect(),x=e.clientX-r.left,y=e.clientY-r.top;c.style.transform=`perspective(800px) rotateX(${((y-r.height/2)/(r.height/2))*-8}deg) rotateY(${((x-r.width/2)/(r.width/2))*8}deg) scale3d(1.02,1.02,1.02)`;c.style.transition='none';c.style.setProperty('--mouse-x',x+'px');c.style.setProperty('--mouse-y',y+'px')});
        c.addEventListener('mouseleave',()=>{c.style.transform='perspective(800px) rotateX(0) rotateY(0) scale3d(1,1,1)';c.style.transition='transform 0.6s cubic-bezier(0.16,1,0.3,1)'});
    });
    // Monitor bars animation
    const mo=new IntersectionObserver(e=>e.forEach(en=>{if(en.isIntersecting){en.target.querySelectorAll('.monitor-bar-fill').forEach(b=>{const w=b.style.width;b.style.width='0%';setTimeout(()=>b.style.width=w,100)});mo.unobserve(en.target)}}),{threshold:0.3});
    const mg=document.querySelector('.monitor-grid');if(mg)mo.observe(mg.closest('.card'));
});
</script>

<!-- ═══════════════════════════════════════════════════════════════════
     THREE.JS 3D SCENES
     ═══════════════════════════════════════════════════════════════════ -->
<script type="module">
import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

// ─── Utility ───
function createRenderer(container, alpha = true) {
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.2;
    container.appendChild(renderer.domElement);
    return renderer;
}

// ═══════════════════════════════════════════════════════════════════
// SCENE 1: HERO — Particle Field + Procedural AI Robot
// ═══════════════════════════════════════════════════════════════════
(function initHeroScene() {
    const container = document.getElementById('heroCanvasContainer');
    if (!container) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
    camera.position.set(0, 0, 18);
    const renderer = createRenderer(container);

    // ── Lighting ──
    const ambientLight = new THREE.AmbientLight(0x404060, 0.5);
    scene.add(ambientLight);
    const purpleLight = new THREE.PointLight(0xa855f7, 2, 50);
    purpleLight.position.set(-8, 5, 10);
    scene.add(purpleLight);
    const cyanLight = new THREE.PointLight(0x00e5ff, 2, 50);
    cyanLight.position.set(8, -3, 8);
    scene.add(cyanLight);
    const pinkLight = new THREE.PointLight(0xec4899, 1, 40);
    pinkLight.position.set(0, 8, 5);
    scene.add(pinkLight);

    // ── Floating Particles ──
    const particleCount = 800;
    const pGeo = new THREE.BufferGeometry();
    const pPositions = new Float32Array(particleCount * 3);
    const pColors = new Float32Array(particleCount * 3);
    const pSizes = new Float32Array(particleCount);
    const colors = [
        new THREE.Color(0xa855f7), new THREE.Color(0x00e5ff),
        new THREE.Color(0xec4899), new THREE.Color(0x10b981)
    ];
    for (let i = 0; i < particleCount; i++) {
        pPositions[i*3]   = (Math.random() - 0.5) * 40;
        pPositions[i*3+1] = (Math.random() - 0.5) * 30;
        pPositions[i*3+2] = (Math.random() - 0.5) * 20;
        const c = colors[Math.floor(Math.random() * colors.length)];
        pColors[i*3] = c.r; pColors[i*3+1] = c.g; pColors[i*3+2] = c.b;
        pSizes[i] = Math.random() * 3 + 1;
    }
    pGeo.setAttribute('position', new THREE.BufferAttribute(pPositions, 3));
    pGeo.setAttribute('color', new THREE.BufferAttribute(pColors, 3));
    pGeo.setAttribute('size', new THREE.BufferAttribute(pSizes, 1));

    const pMat = new THREE.PointsMaterial({
        size: 0.08, vertexColors: true, transparent: true, opacity: 0.7,
        blending: THREE.AdditiveBlending, depthWrite: false
    });
    const particles = new THREE.Points(pGeo, pMat);
    scene.add(particles);

    // ── Procedural Robot (Head + Body + Arms + Antenna) ──
    const robotGroup = new THREE.Group();
    const glow = (color) => new THREE.MeshStandardMaterial({
        color, emissive: color, emissiveIntensity: 0.3,
        metalness: 0.8, roughness: 0.2, transparent: true, opacity: 0.9
    });

    // Head
    const head = new THREE.Mesh(new THREE.BoxGeometry(1.6, 1.4, 1.4, 2, 2, 2), glow(0x8b5cf6));
    head.position.y = 2.5;
    robotGroup.add(head);

    // Eyes
    const eyeGeo = new THREE.SphereGeometry(0.18, 16, 16);
    const eyeMat = new THREE.MeshStandardMaterial({ color: 0x00e5ff, emissive: 0x00e5ff, emissiveIntensity: 1 });
    const leftEye = new THREE.Mesh(eyeGeo, eyeMat);
    leftEye.position.set(-0.35, 2.6, 0.72);
    robotGroup.add(leftEye);
    const rightEye = new THREE.Mesh(eyeGeo, eyeMat);
    rightEye.position.set(0.35, 2.6, 0.72);
    robotGroup.add(rightEye);

    // Antenna
    const antenna = new THREE.Mesh(new THREE.CylinderGeometry(0.04, 0.04, 0.8), glow(0x71717a));
    antenna.position.y = 3.5;
    robotGroup.add(antenna);
    const antennaBall = new THREE.Mesh(new THREE.SphereGeometry(0.12), new THREE.MeshStandardMaterial({ color: 0xec4899, emissive: 0xec4899, emissiveIntensity: 1 }));
    antennaBall.position.y = 3.95;
    robotGroup.add(antennaBall);

    // Neck
    const neck = new THREE.Mesh(new THREE.CylinderGeometry(0.3, 0.3, 0.4), glow(0x52525b));
    neck.position.y = 1.6;
    robotGroup.add(neck);

    // Body
    const body = new THREE.Mesh(new THREE.BoxGeometry(2, 2.4, 1.2, 2, 2, 2), glow(0x7c3aed));
    body.position.y = 0;
    robotGroup.add(body);

    // Chest panel (glowing core)
    const chest = new THREE.Mesh(new THREE.BoxGeometry(0.8, 0.5, 0.1), new THREE.MeshStandardMaterial({ color: 0x00e5ff, emissive: 0x00e5ff, emissiveIntensity: 0.8 }));
    chest.position.set(0, 0.3, 0.65);
    robotGroup.add(chest);

    // Arms
    const armGeo = new THREE.BoxGeometry(0.5, 1.8, 0.5);
    const leftArm = new THREE.Mesh(armGeo, glow(0x6d28d9));
    leftArm.position.set(-1.5, 0, 0);
    robotGroup.add(leftArm);
    const rightArm = new THREE.Mesh(armGeo, glow(0x6d28d9));
    rightArm.position.set(1.5, 0, 0);
    robotGroup.add(rightArm);

    // Legs
    const legGeo = new THREE.BoxGeometry(0.6, 1.6, 0.6);
    const leftLeg = new THREE.Mesh(legGeo, glow(0x4c1d95));
    leftLeg.position.set(-0.5, -2, 0);
    robotGroup.add(leftLeg);
    const rightLeg = new THREE.Mesh(legGeo, glow(0x4c1d95));
    rightLeg.position.set(0.5, -2, 0);
    robotGroup.add(rightLeg);

    // Position robot to right side
    robotGroup.position.set(6, -1, 0);
    robotGroup.rotation.y = -0.3;
    scene.add(robotGroup);

    // ── Floating Geometric Objects ──
    const floaters = [];
    const shapes = [
        new THREE.TorusKnotGeometry(0.4, 0.15, 64, 16),
        new THREE.OctahedronGeometry(0.5),
        new THREE.IcosahedronGeometry(0.4),
        new THREE.TorusGeometry(0.4, 0.12, 16, 32),
        new THREE.TetrahedronGeometry(0.5),
    ];
    for (let i = 0; i < 12; i++) {
        const geo = shapes[Math.floor(Math.random() * shapes.length)];
        const c = colors[Math.floor(Math.random() * colors.length)];
        const mat = new THREE.MeshStandardMaterial({ color: c, emissive: c, emissiveIntensity: 0.15, metalness: 0.6, roughness: 0.3, wireframe: Math.random() > 0.5 });
        const mesh = new THREE.Mesh(geo, mat);
        mesh.position.set((Math.random()-0.5)*25, (Math.random()-0.5)*15, (Math.random()-0.5)*10 - 5);
        mesh.rotation.set(Math.random()*Math.PI, Math.random()*Math.PI, 0);
        mesh.userData = { speed: Math.random()*0.005+0.002, floatSpeed: Math.random()*0.002+0.001, axis: Math.random() > 0.5 ? 'x' : 'y' };
        scene.add(mesh);
        floaters.push(mesh);
    }

    // ── Connection Lines ──
    const lineMat = new THREE.LineBasicMaterial({ color: 0x333344, transparent: true, opacity: 0.15 });
    const lineGeo = new THREE.BufferGeometry();
    const linePositions = [];
    for (let i = 0; i < 30; i++) {
        linePositions.push((Math.random()-0.5)*30, (Math.random()-0.5)*20, (Math.random()-0.5)*10);
        linePositions.push((Math.random()-0.5)*30, (Math.random()-0.5)*20, (Math.random()-0.5)*10);
    }
    lineGeo.setAttribute('position', new THREE.Float32BufferAttribute(linePositions, 3));
    scene.add(new THREE.LineSegments(lineGeo, lineMat));

    // ── Mouse interaction ──
    let mouseX = 0, mouseY = 0;
    document.addEventListener('mousemove', e => {
        mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
        mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
    });

    // ── Animate ──
    const clock = new THREE.Clock();
    function animate() {
        requestAnimationFrame(animate);
        const t = clock.getElapsedTime();

        // Particles drift
        const pos = particles.geometry.attributes.position.array;
        for (let i = 0; i < particleCount; i++) {
            pos[i*3+1] += Math.sin(t * 0.5 + i) * 0.001;
        }
        particles.geometry.attributes.position.needsUpdate = true;
        particles.rotation.y = t * 0.02;

        // Robot floating + head tracking mouse
        robotGroup.position.y = -1 + Math.sin(t * 0.8) * 0.5;
        head.rotation.y = mouseX * 0.4;
        head.rotation.x = mouseY * -0.2;
        leftArm.rotation.x = Math.sin(t * 1.2) * 0.15;
        rightArm.rotation.x = Math.sin(t * 1.2 + Math.PI) * 0.15;
        antennaBall.material.emissiveIntensity = 0.5 + Math.sin(t * 3) * 0.5;

        // Floaters
        floaters.forEach(f => {
            f.rotation.x += f.userData.speed;
            f.rotation.y += f.userData.speed * 0.7;
            f.position.y += Math.sin(t * f.userData.floatSpeed * 100) * 0.003;
        });

        // Camera subtle movement
        camera.position.x += (mouseX * 1.5 - camera.position.x) * 0.02;
        camera.position.y += (-mouseY * 0.8 - camera.position.y) * 0.02;
        camera.lookAt(0, 0, 0);

        renderer.render(scene, camera);
    }
    animate();

    window.addEventListener('resize', () => {
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    });
})();

// ═══════════════════════════════════════════════════════════════════
// SCENE 2: FEATURES — Floating Abstract Geometry
// ═══════════════════════════════════════════════════════════════════
(function initFeaturesScene() {
    const container = document.getElementById('featuresCanvasContainer');
    if (!container) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(50, container.clientWidth / container.clientHeight, 0.1, 100);
    camera.position.set(0, 0, 15);
    const renderer = createRenderer(container);

    scene.add(new THREE.AmbientLight(0x404060, 0.3));
    const pl = new THREE.PointLight(0xa855f7, 1.5, 30); pl.position.set(5, 5, 8); scene.add(pl);
    const pl2 = new THREE.PointLight(0x00e5ff, 1, 25); pl2.position.set(-5, -3, 6); scene.add(pl2);

    // Floating cubes and spheres
    const objs = [];
    for (let i = 0; i < 20; i++) {
        const isBox = Math.random() > 0.5;
        const geo = isBox ? new THREE.BoxGeometry(0.3,0.3,0.3) : new THREE.SphereGeometry(0.15, 12, 12);
        const c = [0xa855f7, 0x00e5ff, 0xec4899, 0x10b981][Math.floor(Math.random()*4)];
        const mat = new THREE.MeshStandardMaterial({ color: c, emissive: c, emissiveIntensity: 0.2, wireframe: true, transparent: true, opacity: 0.5 });
        const m = new THREE.Mesh(geo, mat);
        m.position.set((Math.random()-0.5)*20, (Math.random()-0.5)*12, (Math.random()-0.5)*8);
        m.userData = { rx: Math.random()*0.01, ry: Math.random()*0.01, fy: Math.random()*0.003+0.001 };
        scene.add(m); objs.push(m);
    }

    const clock = new THREE.Clock();
    function animate() {
        requestAnimationFrame(animate);
        const t = clock.getElapsedTime();
        objs.forEach(o => { o.rotation.x += o.userData.rx; o.rotation.y += o.userData.ry; o.position.y += Math.sin(t + o.position.x) * o.userData.fy; });
        renderer.render(scene, camera);
    }
    animate();
    window.addEventListener('resize', () => { camera.aspect = container.clientWidth/container.clientHeight; camera.updateProjectionMatrix(); renderer.setSize(container.clientWidth, container.clientHeight); });
})();

// ═══════════════════════════════════════════════════════════════════
// SCENE 3: AI — Rotating Neural Sphere
// ═══════════════════════════════════════════════════════════════════
(function initAIScene() {
    const container = document.getElementById('aiCanvasContainer');
    if (!container) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(50, container.clientWidth / container.clientHeight, 0.1, 100);
    camera.position.set(0, 0, 8);
    const renderer = createRenderer(container);

    scene.add(new THREE.AmbientLight(0x404060, 0.3));
    const l1 = new THREE.PointLight(0x00e5ff, 2, 20); l1.position.set(3, 3, 5); scene.add(l1);
    const l2 = new THREE.PointLight(0xa855f7, 1.5, 20); l2.position.set(-3, -2, 4); scene.add(l2);

    // Central brain sphere with wireframe
    const brainGeo = new THREE.IcosahedronGeometry(2.5, 2);
    const brainMat = new THREE.MeshStandardMaterial({ color: 0x6d28d9, emissive: 0x6d28d9, emissiveIntensity: 0.15, wireframe: true, transparent: true, opacity: 0.3 });
    const brain = new THREE.Mesh(brainGeo, brainMat);
    scene.add(brain);

    // Inner solid core
    const core = new THREE.Mesh(new THREE.SphereGeometry(0.6, 32, 32), new THREE.MeshStandardMaterial({ color: 0x00e5ff, emissive: 0x00e5ff, emissiveIntensity: 0.6 }));
    scene.add(core);

    // Orbiting particles
    const orbitParticles = [];
    for (let i = 0; i < 60; i++) {
        const dot = new THREE.Mesh(new THREE.SphereGeometry(0.04), new THREE.MeshBasicMaterial({ color: Math.random()>0.5 ? 0x00e5ff : 0xa855f7 }));
        const angle = Math.random() * Math.PI * 2;
        const radius = 2 + Math.random() * 1.5;
        const yOff = (Math.random() - 0.5) * 3;
        dot.userData = { angle, radius, yOff, speed: Math.random() * 0.01 + 0.005 };
        scene.add(dot);
        orbitParticles.push(dot);
    }

    const clock = new THREE.Clock();
    function animate() {
        requestAnimationFrame(animate);
        const t = clock.getElapsedTime();
        brain.rotation.x = t * 0.1;
        brain.rotation.y = t * 0.15;
        core.material.emissiveIntensity = 0.4 + Math.sin(t * 2) * 0.3;
        orbitParticles.forEach(p => {
            p.userData.angle += p.userData.speed;
            p.position.x = Math.cos(p.userData.angle) * p.userData.radius;
            p.position.z = Math.sin(p.userData.angle) * p.userData.radius;
            p.position.y = p.userData.yOff + Math.sin(t + p.userData.angle) * 0.3;
        });
        renderer.render(scene, camera);
    }
    animate();
    window.addEventListener('resize', () => { camera.aspect = container.clientWidth/container.clientHeight; camera.updateProjectionMatrix(); renderer.setSize(container.clientWidth, container.clientHeight); });
})();

// ═══════════════════════════════════════════════════════════════════
// SCENE 4: CTA — Soft Particle Cloud
// ═══════════════════════════════════════════════════════════════════
(function initCTAScene() {
    const container = document.getElementById('ctaCanvasContainer');
    if (!container) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(50, container.clientWidth / container.clientHeight, 0.1, 100);
    camera.position.set(0, 0, 12);
    const renderer = createRenderer(container);

    const particles = new THREE.Points(
        (() => {
            const g = new THREE.BufferGeometry();
            const pos = new Float32Array(300 * 3);
            for (let i = 0; i < 300; i++) { pos[i*3] = (Math.random()-0.5)*20; pos[i*3+1] = (Math.random()-0.5)*10; pos[i*3+2] = (Math.random()-0.5)*10; }
            g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
            return g;
        })(),
        new THREE.PointsMaterial({ color: 0xa855f7, size: 0.06, transparent: true, opacity: 0.5, blending: THREE.AdditiveBlending })
    );
    scene.add(particles);

    // Central ring
    const ring = new THREE.Mesh(new THREE.TorusGeometry(2.5, 0.03, 16, 100), new THREE.MeshBasicMaterial({ color: 0x00e5ff, transparent: true, opacity: 0.4 }));
    scene.add(ring);
    const ring2 = new THREE.Mesh(new THREE.TorusGeometry(3, 0.02, 16, 100), new THREE.MeshBasicMaterial({ color: 0xa855f7, transparent: true, opacity: 0.3 }));
    ring2.rotation.x = Math.PI / 3;
    scene.add(ring2);

    const clock = new THREE.Clock();
    function animate() {
        requestAnimationFrame(animate);
        const t = clock.getElapsedTime();
        particles.rotation.y = t * 0.03;
        ring.rotation.z = t * 0.2;
        ring2.rotation.z = -t * 0.15;
        ring2.rotation.y = t * 0.1;
        renderer.render(scene, camera);
    }
    animate();
    window.addEventListener('resize', () => { camera.aspect = container.clientWidth/container.clientHeight; camera.updateProjectionMatrix(); renderer.setSize(container.clientWidth, container.clientHeight); });
})();

</script>

</body>
</html>

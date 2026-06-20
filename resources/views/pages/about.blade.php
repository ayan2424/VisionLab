<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="VisionLab is a collaborative coding and learning platform built for research universities. Learn about our mission, team, and technology.">
    <title>About VisionLab — Our Mission to Transform CS Education</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,600;1,700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Three.js via Import Map -->
    <script type="importmap">
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.164.1/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.164.1/examples/jsm/"
        }
    }
    </script>

    <style>
        :root {
            --bg: #030303;
            --surface: #0a0a0a;
            --surface-2: #111111;
            --border: rgba(255,255,255,0.06);
            --border-hover: rgba(255,255,255,0.12);
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --text-muted: #52525b;
            --purple: #a855f7;
            --pink: #ec4899;
            --cyan: #00e5ff;
            --cyan-glow: rgba(0,229,255,0.4);
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

        /* Nav */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(3,3,3,0.8); backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border); transition: all 0.3s;
        }
        .nav-inner { max-width: 1280px; margin: 0 auto; padding: 0 2rem; height: 64px; display: flex; align-items: center; justify-content: space-between; }
        .nav-brand { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: white; }
        .nav-brand span { font-weight: 700; font-size: 1.1rem; letter-spacing: -0.02em; }
        .nav-brand .brand-slash { color: var(--text-muted); font-weight: 300; margin: 0 0.25rem; }
        .nav-brand .brand-edu { color: var(--text-secondary); font-weight: 400; font-size: 0.85rem; }
        .nav-links { display: none; align-items: center; gap: 2rem; }
        @media (min-width: 768px) { .nav-links { display: flex; } }
        .nav-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: white; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-size: 0.875rem; font-weight: 600; border-radius: 9999px; text-decoration: none; transition: all 0.3s cubic-bezier(0.16,1,0.3,1); cursor: pointer; border: none; white-space: nowrap; }
        .btn-primary { background: var(--cyan); color: #000; box-shadow: 0 0 20px var(--cyan-glow), 0 0 60px rgba(0,229,255,0.15); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 0 30px var(--cyan-glow), 0 0 80px rgba(0,229,255,0.25); background: #33eeff; }
        .btn-secondary { background: transparent; color: white; border: 1px solid rgba(255,255,255,0.15); }
        .btn-secondary:hover { border-color: rgba(255,255,255,0.4); background: rgba(255,255,255,0.03); }

        /* Cards */
        .card {
            background: rgba(255,255,255,0.015); border: 1px solid var(--border); border-radius: 1rem;
            transition: all 0.4s cubic-bezier(0.16,1,0.3,1); position: relative; overflow: hidden;
        }
        .card:hover { border-color: var(--border-hover); background: rgba(255,255,255,0.03); }

        /* Sections */
        .section { max-width: 1280px; margin: 0 auto; padding: 6rem 2rem; position: relative; z-index: 10; }
        .section-heading { font-size: clamp(2.25rem, 5vw, 3.5rem); font-weight: 700; line-height: 1.1; letter-spacing: -0.03em; margin-bottom: 1.5rem; }
        .section-sub { font-size: 1rem; color: var(--text-secondary); max-width: 700px; line-height: 1.7; }
        .section-sep { width: 100%; height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); max-width: 1280px; margin: 0 auto; }

        /* Hero */
        .hero { position: relative; min-height: 80vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 8rem 2rem 4rem; overflow: hidden; }
        .canvas-container { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
        .canvas-container canvas { display: block; width: 100%; height: 100%; }
        .hero-headline { font-size: clamp(3rem, 7vw, 5rem); font-weight: 900; line-height: 1.1; letter-spacing: -0.04em; }

        /* Grid layouts */
        .grid-4 { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
        @media (min-width: 640px) { .grid-4 { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .grid-4 { grid-template-columns: repeat(4, 1fr); } }

        /* Value Cards */
        .value-card { padding: 2.5rem 2rem; display: flex; flex-direction: column; gap: 1.5rem; }
        .value-icon { width: 44px; height: 44px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .value-title { font-size: 1.15rem; font-weight: 700; }
        .value-desc { font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6; }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; max-width: 800px; margin: 0 auto; }
        @media (min-width: 768px) { .stats-grid { grid-template-columns: repeat(4, 1fr); } }
        .stat-item { text-align: center; display: flex; flex-direction: column; gap: 0.5rem; }
        .stat-val { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; letter-spacing: -0.03em; }
        .stat-lbl { font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; }

        /* Story/Timeline */
        .timeline { position: relative; max-width: 800px; margin: 4rem auto 0; padding-left: 2rem; border-left: 2px solid rgba(255,255,255,0.06); }
        .timeline-item { position: relative; padding-bottom: 3.5rem; }
        .timeline-item:last-child { padding-bottom: 0; }
        .timeline-dot { position: absolute; left: calc(-2rem - 6px); top: 8px; width: 10px; height: 10px; border-radius: 50%; background: var(--bg); border: 2px solid var(--purple); box-shadow: 0 0 10px var(--purple); }
        .timeline-dot.cyan { border-color: var(--cyan); box-shadow: 0 0 10px var(--cyan); }
        .timeline-dot.pink { border-color: var(--pink); box-shadow: 0 0 10px var(--pink); }
        .timeline-dot.emerald { border-color: var(--emerald); box-shadow: 0 0 10px var(--emerald); }
        .timeline-date { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.5rem; }
        .timeline-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; }
        .timeline-desc { font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6; }

        /* Tech Stack */
        .tech-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 1.5rem; max-width: 900px; margin: 3rem auto 0; }
        .tech-badge { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; border-radius: 9999px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); font-size: 0.9rem; font-weight: 500; }
        .tech-badge:hover { border-color: var(--border-hover); background: rgba(255,255,255,0.04); }

        /* Team */
        .team-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
        @media (min-width: 640px) { .team-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .team-grid { grid-template-columns: repeat(4, 1fr); } }
        .team-card { padding: 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 1.25rem; }
        .team-avatar { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 700; color: white; border: 2px solid var(--border); }
        .team-name { font-size: 1.1rem; font-weight: 700; }
        .team-role { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }

        /* Reveal animations */
        .reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }

        /* Footer */
        .footer { border-top: 1px solid var(--border); padding: 4rem 2rem; background: var(--bg); position: relative; z-index: 10; }
        .footer-inner { max-width: 1280px; margin: 0 auto; display: flex; flex-direction: column; align-items: center; gap: 2rem; }
        .footer-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; }
        .footer-links a { color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; }
        .footer-links a:hover { color: white; }

        /* Cursor */
        .cursor-dot { position: fixed; width: 6px; height: 6px; background: var(--cyan); border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%,-50%); box-shadow: 0 0 12px var(--cyan); display: none; }
        .cursor-ring { position: fixed; width: 36px; height: 36px; border: 1.5px solid rgba(0,229,255,0.4); border-radius: 50%; pointer-events: none; z-index: 9998; transform: translate(-50%,-50%); transition: all 0.2s cubic-bezier(0.16,1,0.3,1); display: none; }
    </style>
</head>
<body>

<!-- Cursor -->
<div class="cursor-dot" id="cursorDot"></div>
<div class="cursor-ring" id="cursorRing"></div>

<!-- NAVIGATION -->
<nav class="nav" id="mainNav">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-brand">
            <svg width="22" height="22" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="url(#lg)"/><path d="M10 22V10l6 6 6-6v12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="lg" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#a855f7"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs></svg>
            <span>VisionLab</span><span class="brand-slash">/</span><span class="brand-edu">edu</span>
        </a>
        <div class="nav-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('about') }}" class="active">About</a>
            <a href="{{ route('features') }}">Features</a>
            <a href="{{ route('pricing') }}">Pricing</a>
            <a href="{{ route('docs') }}">Docs</a>
            <a href="{{ route('contact') }}">Contact</a>
        </div>
        <div style="display:flex;align-items:center;gap:1rem">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding:0.5rem 1.5rem;font-size:0.8rem">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary" style="padding:0.5rem 1.5rem;font-size:0.8rem">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding:0.5rem 1.5rem;font-size:0.8rem">Deploy</a>
            @endauth
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="canvas-container" id="aboutHeroCanvas"></div>
    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto">
        <h1 class="hero-headline reveal text-gradient-hero">
            Built for the <br><span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">next generation</span> of computer science.
        </h1>
        <p class="section-sub reveal reveal-delay-1" style="margin: 2rem auto 0; text-align:center;">
            VisionLab transforms how higher education environments operate. We merge sandbox engineering, socratic AI tools, and seamless collaboration into a centralized workspace.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- MISSION -->
<section class="section">
    <div style="max-width:800px;margin:0 auto;text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--purple);font-weight:700;display:block;margin-bottom:1rem">Our Mission</span>
        <h2 class="section-heading">Democratizing Development Environments</h2>
        <p class="section-sub" style="margin:0 auto">
            Our mission is to democratize access to enterprise-grade development workspaces across every university campus. By replacing fragmented solutions, students are equipped with secure, sandboxed systems, TAs gain absolute visibility, and AI tutors help guide the way.
        </p>
    </div>
</section>

<!-- STATS -->
<section class="section" style="background: rgba(255,255,255,0.01);">
    <div class="stats-grid reveal">
        <div class="stat-item">
            <span class="stat-val text-gradient-purple-pink" data-target="500">0</span>
            <span class="stat-lbl">Universities</span>
        </div>
        <div class="stat-item">
            <span class="stat-val text-gradient-cyan" data-target="50000">0</span>
            <span class="stat-lbl">Students</span>
        </div>
        <div class="stat-item">
            <span class="stat-val text-gradient-purple-pink" data-target="1000000">0</span>
            <span class="stat-lbl">Lines of Code</span>
        </div>
        <div class="stat-item">
            <span class="stat-val text-gradient-cyan" data-target="99">0</span>
            <span class="stat-lbl">Uptime %</span>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- TIMELINE / STORY -->
<section class="section">
    <div style="text-align:center;margin-bottom:4rem" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--cyan);font-weight:700;display:block;margin-bottom:1rem">The Journey</span>
        <h2 class="section-heading">How VisionLab Evolved</h2>
    </div>
    
    <div class="timeline reveal">
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-date">Q1 2024</div>
            <h3 class="timeline-title">Project Founded</h3>
            <p class="timeline-desc">Conceived as an academic research project to address server costs and IDE configuration drift for large computer science cohorts.</p>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot cyan"></div>
            <div class="timeline-date">Q3 2025</div>
            <h3 class="timeline-title">Beta Deployment</h3>
            <p class="timeline-desc">Deployed across 3 local departments. Scaled multi-cursor synchronization mechanisms and sandboxed student environments.</p>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot pink"></div>
            <div class="timeline-date">Q1 2026</div>
            <h3 class="timeline-title">Aptech Vision 2026 Entry</h3>
            <p class="timeline-desc">Refactored workspace containers to Nix build targets, hardend Docker runtime, and introduced human-approved Socratic AI mutations.</p>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot emerald"></div>
            <div class="timeline-date">Future</div>
            <h3 class="timeline-title">Global Expansion</h3>
            <p class="timeline-desc">Expanding automated grading networks, real-time telemetry analytics, and integrations with learning standards (LTI).</p>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- VALUES -->
<section class="section">
    <div style="text-align:center;margin-bottom:4rem" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--purple);font-weight:700;display:block;margin-bottom:1rem">Foundations</span>
        <h2 class="section-heading">Core Values</h2>
    </div>

    <div class="grid-4 reveal">
        <div class="card value-card">
            <div class="value-icon" style="background:rgba(168,85,247,0.1);color:var(--purple)">🔒</div>
            <h3 class="value-title">Security First</h3>
            <p class="value-desc">Strict OS-level isolation on every container. Path-traversal checks, memory quotas, and dropping of root privileges.</p>
        </div>
        <div class="card value-card">
            <div class="value-icon" style="background:rgba(0,229,255,0.1);color:var(--cyan)">🌐</div>
            <h3 class="value-title">Open Standards</h3>
            <p class="value-desc">No proprietary vendor locks. Nix packages, VS Code open-source forks, and standard Docker networks.</p>
        </div>
        <div class="card value-card">
            <div class="value-icon" style="background:rgba(236,72,153,0.1);color:var(--pink)">🛡️</div>
            <h3 class="value-title">Student Privacy</h3>
            <p class="value-desc">Zero telemetry tracking leaks. Safe logs stored locally inside university borders with strict role governance.</p>
        </div>
        <div class="card value-card">
            <div class="value-icon" style="background:rgba(16,185,129,0.1);color:var(--emerald)">🎓</div>
            <h3 class="value-title">Academic Integrity</h3>
            <p class="value-desc">Governance policies for AI helpers. TAs approve mutations via dynamic diff panels, maintaining strict learning models.</p>
        </div>
    </div>
</section>

<!-- TECH STACK -->
<section class="section" style="background: rgba(255,255,255,0.01);">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--cyan);font-weight:700;display:block;margin-bottom:1rem">Engine Room</span>
        <h2 class="section-heading">Enterprise Technology Stack</h2>
        <p class="section-sub" style="margin:0 auto">Precision engineering powered by industry-grade frameworks.</p>
        
        <div class="tech-grid">
            <div class="tech-badge"><span>🐘</span> Laravel 11</div>
            <div class="tech-badge"><span>🐳</span> Docker</div>
            <div class="tech-badge"><span>📐</span> Three.js</div>
            <div class="tech-badge"><span>📦</span> Redis Cache</div>
            <div class="tech-badge"><span>💾</span> MySQL 8</div>
            <div class="tech-badge"><span>⚡</span> WebSockets</div>
            <div class="tech-badge"><span>❄️</span> Nix OS Nix</div>
        </div>
    </div>
</section>

<!-- TEAM -->
<section class="section">
    <div style="text-align:center;margin-bottom:4rem" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--pink);font-weight:700;display:block;margin-bottom:1rem">The Syndicate</span>
        <h2 class="section-heading">Meet the Engineers</h2>
    </div>

    <div class="team-grid reveal">
        <div class="card team-card">
            <div class="team-avatar" style="background:linear-gradient(135deg, var(--purple), var(--pink))">AS</div>
            <h3 class="team-name">Ayan S.</h3>
            <span class="team-role">Lead Architect</span>
        </div>
        <div class="card team-card">
            <div class="team-avatar" style="background:linear-gradient(135deg, var(--cyan), #3b82f6)">PK</div>
            <h3 class="team-name">Prof. Khan</h3>
            <span class="team-role">Academic Advisor</span>
        </div>
        <div class="card team-card">
            <div class="team-avatar" style="background:linear-gradient(135deg, var(--emerald), #059669)">SM</div>
            <h3 class="team-name">Sarah M.</h3>
            <span class="team-role">UI/UX Engineer</span>
        </div>
        <div class="card team-card">
            <div class="team-avatar" style="background:linear-gradient(135deg, var(--pink), var(--purple))">DT</div>
            <h3 class="team-name">Dev Syndicate</h3>
            <span class="team-role">Full-Stack Core</span>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section" style="text-align:center;padding:8rem 2rem">
    <div style="position:relative;z-index:10;max-width:700px;margin:0 auto" class="reveal">
        <h2 class="section-heading">Join the Mission</h2>
        <p class="section-sub" style="margin:0 auto 3rem">Deploy a production-grade sandboxed classroom IDE environment for your university department in minutes.</p>
        <a href="{{ route('register') }}" class="btn btn-primary">Deploy Institution</a>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('features') }}">Features</a>
            <a href="{{ route('pricing') }}">Pricing</a>
            <a href="{{ route('docs') }}">Docs</a>
            <a href="{{ route('contact') }}">Contact</a>
        </div>
        <p style="font-size:0.75rem;color:var(--text-muted)">&copy; 2026 VisionLab. Aptech Vision 2026 Competition Entry. All rights reserved.</p>
    </div>
</footer>

<!-- SCRIPTS -->
<script type="module">
    import * as THREE from 'three';

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
        document.querySelectorAll('a, button, .card').forEach(el => {
            el.addEventListener('mouseenter', () => ring.style.transform = 'translate(-50%,-50%) scale(1.5)');
            el.addEventListener('mouseleave', () => ring.style.transform = 'translate(-50%,-50%) scale(1)');
        });
    }

    // ── Reveal on Scroll ──
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                // Trigger stats counting if applicable
                const stats = entry.target.querySelectorAll('.stat-val');
                if (stats.length) {
                    stats.forEach(animateCount);
                }
            }
        });
    }, { threshold: 0.1 });
    reveals.forEach(r => observer.observe(r));

    // Stats counter animation
    function animateCount(el) {
        if (el.dataset.counted === 'true') return;
        el.dataset.counted = 'true';
        const target = parseInt(el.dataset.target);
        const duration = 2000;
        const start = 0;
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 4); // easeOutQuart
            const val = Math.floor(start + ease * (target - start));
            
            if (target >= 1000000) {
                el.innerText = (val / 1000000).toFixed(1) + 'M+';
            } else if (target >= 1000) {
                el.innerText = val.toLocaleString() + '+';
            } else {
                el.innerText = val + (target === 99 ? '.9%' : '+');
            }

            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        requestAnimationFrame(update);
    }

    // ── Three.js Hero Scene ──
    (function initHeroScene() {
        const container = document.getElementById('aboutHeroCanvas');
        if (!container) return;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 0, 15);

        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        container.appendChild(renderer.domElement);

        // Lighting
        scene.add(new THREE.AmbientLight(0x404060, 0.5));
        const pl1 = new THREE.PointLight(0xa855f7, 2, 40); pl1.position.set(5, 5, 8); scene.add(pl1);
        const pl2 = new THREE.PointLight(0x00e5ff, 2, 40); pl2.position.set(-5, -5, 8); scene.add(pl2);

        // Floating Icosahedrons
        const count = 8;
        const floaters = [];
        for (let i = 0; i < count; i++) {
            const geo = new THREE.IcosahedronGeometry(Math.random() * 0.8 + 0.4, 1);
            const color = Math.random() > 0.5 ? 0xa855f7 : 0x00e5ff;
            const mat = new THREE.MeshStandardMaterial({
                color: color,
                emissive: color,
                emissiveIntensity: 0.15,
                metalness: 0.8,
                roughness: 0.2,
                wireframe: Math.random() > 0.4
            });
            const m = new THREE.Mesh(geo, mat);
            m.position.set(
                (Math.random() - 0.5) * 16,
                (Math.random() - 0.5) * 10,
                (Math.random() - 0.5) * 6 - 2
            );
            m.userData = {
                speed: Math.random() * 0.005 + 0.002,
                floatSpeed: Math.random() * 0.002 + 0.001,
                offset: Math.random() * Math.PI
            };
            scene.add(m);
            floaters.push(m);
        }

        // Particle field
        const pCount = 300;
        const pGeo = new THREE.BufferGeometry();
        const pPos = new Float32Array(pCount * 3);
        for (let i = 0; i < pCount; i++) {
            pPos[i*3] = (Math.random() - 0.5) * 30;
            pPos[i*3+1] = (Math.random() - 0.5) * 20;
            pPos[i*3+2] = (Math.random() - 0.5) * 15;
        }
        pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
        const pMat = new THREE.PointsMaterial({
            color: 0xa855f7,
            size: 0.04,
            transparent: true,
            opacity: 0.4,
            blending: THREE.AdditiveBlending
        });
        const particles = new THREE.Points(pGeo, pMat);
        scene.add(particles);

        let mouseX = 0, mouseY = 0;
        document.addEventListener('mousemove', e => {
            mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
            mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
        });

        const clock = new THREE.Clock();
        function animate() {
            requestAnimationFrame(animate);
            const t = clock.getElapsedTime();

            floaters.forEach(f => {
                f.rotation.x += f.userData.speed;
                f.rotation.y += f.userData.speed * 0.7;
                f.position.y += Math.sin(t * 1.5 + f.userData.offset) * 0.002;
            });

            particles.rotation.y = t * 0.01;

            camera.position.x += (mouseX * 2 - camera.position.x) * 0.02;
            camera.position.y += (-mouseY * 1.5 - camera.position.y) * 0.02;
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
</script>

</body>
</html>

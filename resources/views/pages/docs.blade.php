<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Complete documentation for VisionLab: quick start guide, API reference, workspace management, AI configuration, and deployment.">
    <title>Documentation — VisionLab Developer Guide</title>
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

        /* Sections */
        .section { max-width: 1280px; margin: 0 auto; padding: 6rem 2rem; position: relative; z-index: 10; }
        .section-heading { font-size: clamp(2.25rem, 5vw, 3.5rem); font-weight: 700; line-height: 1.1; letter-spacing: -0.03em; margin-bottom: 1.5rem; }
        .section-sub { font-size: 1rem; color: var(--text-secondary); max-width: 700px; line-height: 1.7; }
        .section-sep { width: 100%; height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); max-width: 1280px; margin: 0 auto; }

        /* Hero */
        .hero { position: relative; min-height: 70vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 8rem 2rem 4rem; overflow: hidden; }
        .canvas-container { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
        .canvas-container canvas { display: block; width: 100%; height: 100%; }
        .hero-headline { font-size: clamp(3rem, 7vw, 5.25rem); font-weight: 900; line-height: 1.1; letter-spacing: -0.04em; }

        /* Quick start steps */
        .steps-container { display: grid; grid-template-columns: 1fr; gap: 2rem; margin-top: 3rem; }
        @media (min-width: 768px) { .steps-container { grid-template-columns: repeat(3, 1fr); } }
        .step-card {
            background: rgba(255,255,255,0.01); border: 1px solid var(--border); border-radius: 1rem;
            padding: 2.5rem 2rem; display: flex; flex-direction: column; gap: 1rem; position: relative;
        }
        .step-badge {
            position: absolute; top: -15px; left: 2rem; width: 30px; height: 30px; border-radius: 50%;
            background: var(--cyan); color: #000; font-size: 0.85rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center; box-shadow: 0 0 10px var(--cyan-glow);
        }
        .step-title { font-size: 1.1rem; font-weight: 700; margin-top: 0.5rem; }
        .step-desc { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6; }

        /* Category Grid */
        .categories-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-top: 3rem; }
        @media (min-width: 640px) { .categories-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .categories-grid { grid-template-columns: repeat(3, 1fr); } }
        
        .cat-card {
            background: rgba(255,255,255,0.015); border: 1px solid var(--border); border-radius: 1rem;
            padding: 2rem; display: flex; flex-direction: column; gap: 1rem; transition: all 0.3s;
            border-top: 3px solid var(--border);
        }
        .cat-card:hover { border-color: var(--border-hover); background: rgba(255,255,255,0.03); transform: translateY(-2px); }
        .cat-header { display: flex; align-items: center; justify-content: space-between; }
        .cat-icon { font-size: 1.5rem; }
        .cat-title { font-size: 1.1rem; font-weight: 700; }
        .cat-desc { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6; }
        .cat-link { font-size: 0.8rem; font-weight: 600; color: var(--cyan); text-decoration: none; margin-top: auto; display: inline-flex; align-items: center; gap: 0.25rem; }
        .cat-link:hover { text-decoration: underline; }

        /* Code Block Panel */
        .code-panel {
            background: var(--surface); border: 1px solid var(--border); border-radius: 1rem;
            padding: 1.5rem; margin-top: 3rem; overflow-x: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }
        .code-title { font-size: 0.75rem; font-family: 'JetBrains Mono', monospace; color: var(--text-muted); border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 1rem; display: flex; justify-content: space-between; }
        .code-content { font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; line-height: 1.7; color: #d4d4d8; }

        /* Community section */
        .community-box {
            background: rgba(168,85,247,0.02); border: 1px solid rgba(168,85,247,0.15); border-radius: 1rem;
            padding: 3rem 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 1.25rem;
        }
        .community-btns { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; }

        /* Reveal animations */
        .reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }

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
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('features') }}">Features</a>
            <a href="{{ route('pricing') }}">Pricing</a>
            <a href="{{ route('docs') }}" class="active">Docs</a>
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
    <div class="canvas-container" id="docsHeroCanvas"></div>
    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto">
        <h1 class="hero-headline reveal text-gradient-hero">
            Documentation & <br><span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">developer guide.</span>
        </h1>
        <p class="section-sub reveal reveal-delay-1" style="margin: 2rem auto 0; text-align:center;">
            Explore setup guides, API endpoints, collaborative sync primitives, and container deployment scripts.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- QUICK START -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--cyan);font-weight:700;display:block;margin-bottom:1rem">Onboarding</span>
        <h2 class="section-heading">Quick Start Guide</h2>
    </div>

    <div class="steps-container reveal">
        <div class="step-card">
            <span class="step-badge">1</span>
            <h3 class="step-title">Create an Account</h3>
            <p class="step-desc">Sign up with your university email. VisionLab auto-resolves your role if pre-registered by your course TAs.</p>
        </div>
        <div class="step-card">
            <span class="step-badge">2</span>
            <h3 class="step-title">Launch a Workspace</h3>
            <p class="step-desc">Access your course board and click "New Workspace". Our backend spawns a sandboxed Nix container in under 5 seconds.</p>
        </div>
        <div class="step-card">
            <span class="step-badge">3</span>
            <h3 class="step-title">Start Coding</h3>
            <p class="step-desc">Open the browser IDE. Your environment loads with predefined compilers, code tools, and live presence channels.</p>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- DOCUMENTATION CATEGORIES -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--purple);font-weight:700;display:block;margin-bottom:1rem">Sectors</span>
        <h2 class="section-heading">Explore Documentation</h2>
    </div>

    <div class="categories-grid reveal">
        <!-- Get started -->
        <div class="cat-card" style="border-top-color: var(--purple);">
            <div class="cat-header">
                <h3 class="cat-title">Getting Started</h3>
                <span class="cat-icon">🚀</span>
            </div>
            <p class="cat-desc">Account registration models, environment setup guidelines, and IDE shell walkthroughs.</p>
            <a href="#" class="cat-link">Read Guide &rarr;</a>
        </div>

        <!-- Workspace guide -->
        <div class="cat-card" style="border-top-color: var(--cyan);">
            <div class="cat-header">
                <h3 class="cat-title">Workspace Guide</h3>
                <span class="cat-icon">💻</span>
            </div>
            <p class="cat-desc">Configuring sandboxed Docker instances, handling file allocations, and mounting project volumes.</p>
            <a href="#" class="cat-link">Read Guide &rarr;</a>
        </div>

        <!-- AI helper -->
        <div class="cat-card" style="border-top-color: var(--pink);">
            <div class="cat-header">
                <h3 class="cat-title">AI Teaching Assistant</h3>
                <span class="cat-icon">🤖</span>
            </div>
            <p class="cat-desc">Switching agent helper modes, using Socratic guidance loops, and approving code mutations via diff panels.</p>
            <a href="#" class="cat-link">Read Guide &rarr;</a>
        </div>

        <!-- Collaboration -->
        <div class="cat-card" style="border-top-color: var(--emerald);">
            <div class="cat-header">
                <h3 class="cat-title">Live Collaboration</h3>
                <span class="cat-icon">👥</span>
            </div>
            <p class="cat-desc">Synchronizing code edits, peer presence tracking, embedded chat operations, and WebRTC video panels.</p>
            <a href="#" class="cat-link">Read Guide &rarr;</a>
        </div>

        <!-- API Reference -->
        <div class="cat-card" style="border-top-color: var(--purple);">
            <div class="cat-header">
                <h3 class="cat-title">API Reference</h3>
                <span class="cat-icon">📡</span>
            </div>
            <p class="cat-desc">Sanctum authorization models, course telemetry Webhook events, and JSON endpoint structures.</p>
            <a href="#" class="cat-link">Read Guide &rarr;</a>
        </div>

        <!-- Deployment -->
        <div class="cat-card" style="border-top-color: var(--cyan);">
            <div class="cat-header">
                <h3 class="cat-title">Local Deployment</h3>
                <span class="cat-icon">🚢</span>
            </div>
            <p class="cat-desc">Deploying Docker clusters, running Nix blueprints, and setting up reverse proxy configurations.</p>
            <a href="#" class="cat-link">Read Guide &rarr;</a>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- CODE EXAMPLE -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <h2 class="section-heading">Quick API Reference</h2>
        <p class="section-sub" style="margin:0 auto">Fetch sandboxed workspace list directly via authenticated shell commands.</p>
    </div>

    <div class="code-panel reveal">
        <div class="code-title">
            <span>Terminal</span>
            <span>bash</span>
        </div>
        <pre class="code-content"><code>curl -X GET https://visionlab.edu/api/workspaces \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Accept: application/json'</code></pre>
    </div>
</section>

<!-- COMMUNITY -->
<section class="section">
    <div class="community-box reveal">
        <h2 class="section-heading" style="margin-bottom:0">Join the Developer Syndicate</h2>
        <p class="section-sub" style="margin:0 auto">Contribute, ask deployment questions, and share custom Nix blueprints.</p>
        <div class="community-btns">
            <a href="https://github.com" target="_blank" class="btn btn-secondary">🐙 GitHub Discussions</a>
            <a href="https://discord.com" target="_blank" class="btn btn-secondary">💬 Discord Server</a>
        </div>
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
        document.querySelectorAll('a, button, .card, .cat-card, .quick-card').forEach(el => {
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
            }
        });
    }, { threshold: 0.1 });
    reveals.forEach(r => observer.observe(r));

    // ── Three.js Hero Scene (Matrix-like flowing particles) ──
    (function initHeroScene() {
        const container = document.getElementById('docsHeroCanvas');
        if (!container) return;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 0, 10);

        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        container.appendChild(renderer.domElement);

        scene.add(new THREE.AmbientLight(0x404060, 0.5));
        const pl = new THREE.PointLight(0x00e5ff, 2, 25); pl.position.set(4, 2, 5); scene.add(pl);
        const pl2 = new THREE.PointLight(0xa855f7, 2, 25); pl2.position.set(-4, -2, 5); scene.add(pl2);

        // Grid particles falling down
        const pCount = 200;
        const pGeo = new THREE.BufferGeometry();
        const pPos = new Float32Array(pCount * 3);
        const speeds = [];
        for (let i = 0; i < pCount; i++) {
            pPos[i*3] = (Math.random() - 0.5) * 20;
            pPos[i*3+1] = (Math.random() - 0.5) * 12;
            pPos[i*3+2] = (Math.random() - 0.5) * 8 - 2;
            speeds.push(Math.random() * 0.02 + 0.005);
        }
        pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
        const pMat = new THREE.PointsMaterial({
            color: 0xa855f7,
            size: 0.05,
            transparent: true,
            opacity: 0.5,
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

            // Falling animation
            const pos = particles.geometry.attributes.position.array;
            for (let i = 0; i < pCount; i++) {
                pos[i*3+1] -= speeds[i];
                if (pos[i*3+1] < -6) {
                    pos[i*3+1] = 6;
                }
            }
            particles.geometry.attributes.position.needsUpdate = true;

            camera.position.x += (mouseX * 1.5 - camera.position.x) * 0.02;
            camera.position.y += (-mouseY * 1.0 - camera.position.y) * 0.02;
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

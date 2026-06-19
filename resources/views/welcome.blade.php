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
           LANDING PAGE — ULTRA PREMIUM INTERACTIVE 3D DESIGN
           ═══════════════════════════════════════════════════════════════════════ */

        :root {
            --landing-bg: #030305;
            --landing-surface: #0a0a0f;
            --landing-card: rgba(255,255,255,0.02);
            --landing-card-border: rgba(255,255,255,0.05);
            --landing-text: #f8f8fc;
            --landing-text-secondary: #9494a8;
            --landing-muted: #4e4e66;
            --landing-accent-violet: #8b5cf6;
            --landing-accent-cyan: #06b6d4;
            --landing-accent-pink: #ec4899;
        }

        body.landing-page {
            background: var(--landing-bg);
            color: var(--landing-text);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            /* Hide default cursor to show custom glowing orb */
            cursor: none;
        }

        /* ── CUSTOM CURSOR GLOW ── */
        #cursor-glow {
            position: fixed;
            top: 0;
            left: 0;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(139,92,246,0.15) 0%, transparent 60%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            transform: translate(-50%, -50%);
            mix-blend-mode: screen;
            transition: width 0.3s, height 0.3s, background 0.3s;
        }
        #cursor-dot {
            position: fixed;
            top: 0;
            left: 0;
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
            pointer-events: none;
            z-index: 10000;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 10px #fff, 0 0 20px #8b5cf6;
            transition: transform 0.1s;
        }
        
        /* Interactive element cursor states */
        body.landing-page a, body.landing-page button, .interactive {
            cursor: none;
        }

        /* ── SCROLL PROGRESS BAR ── */
        #scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #8b5cf6, #06b6d4, #ec4899);
            z-index: 10001;
            box-shadow: 0 0 10px rgba(139,92,246,0.5);
        }

        /* ── 3D INTERACTIVE ROBOT HEAD ── */
        .robot-head-container {
            position: absolute;
            right: 5%;
            top: 20%;
            width: 300px;
            height: 300px;
            perspective: 1000px;
            z-index: 20;
            pointer-events: none;
        }
        .robot-head {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.1s ease-out;
        }
        /* A sleek glass visor */
        .robot-visor {
            position: absolute;
            top: 30%;
            left: 10%;
            width: 80%;
            height: 40%;
            background: rgba(10, 10, 20, 0.8);
            border: 2px solid rgba(139,92,246,0.5);
            border-radius: 50px;
            box-shadow: inset 0 0 30px rgba(139,92,246,0.5), 0 0 50px rgba(139,92,246,0.3);
            overflow: hidden;
            transform: translateZ(50px);
        }
        /* Robot Eyes tracking cursor */
        .robot-eye {
            position: absolute;
            top: 50%;
            width: 25px;
            height: 15px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 0 20px #06b6d4, 0 0 40px #06b6d4;
            transform: translateY(-50%);
            transition: left 0.1s ease-out, top 0.1s ease-out, height 0.2s;
        }
        .robot-eye.left { left: 25%; }
        .robot-eye.right { right: 25%; }
        /* Blinking animation */
        @keyframes blink {
            0%, 96%, 98%, 100% { transform: translateY(-50%) scaleY(1); }
            97% { transform: translateY(-50%) scaleY(0.1); }
        }
        .robot-eye { animation: blink 4s infinite; }

        /* ── FLOATING 3D SHAPES ── */
        .shape-3d {
            position: absolute;
            pointer-events: none;
            transform-style: preserve-3d;
        }
        .cube {
            width: 60px; height: 60px;
            animation: rotate-3d 15s linear infinite;
        }
        .cube div {
            position: absolute; width: 60px; height: 60px;
            border: 1px solid rgba(139,92,246,0.3);
            background: rgba(139,92,246,0.05);
            box-shadow: inset 0 0 15px rgba(139,92,246,0.1);
        }
        .cube .front  { transform: translateZ(30px); }
        .cube .back   { transform: rotateY(180deg) translateZ(30px); }
        .cube .right  { transform: rotateY(90deg) translateZ(30px); }
        .cube .left   { transform: rotateY(-90deg) translateZ(30px); }
        .cube .top    { transform: rotateX(90deg) translateZ(30px); }
        .cube .bottom { transform: rotateX(-90deg) translateZ(30px); }

        @keyframes rotate-3d {
            0% { transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
            100% { transform: rotateX(360deg) rotateY(360deg) rotateZ(360deg); }
        }

        /* ── TILT CARDS WITH GLARE ── */
        .tilt-card {
            transform-style: preserve-3d;
            position: relative;
            background: var(--landing-card);
            border: 1px solid var(--landing-card-border);
            border-radius: 1.5rem;
            backdrop-filter: blur(16px);
            overflow: hidden;
            transition: transform 0.1s, box-shadow 0.1s;
        }
        .tilt-glare {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1), transparent 50%);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
            mix-blend-mode: overlay;
        }
        .tilt-card:hover .tilt-glare { opacity: 1; }
        
        /* ── MAGNETIC BUTTONS ── */
        .magnetic-wrap {
            display: inline-block;
            padding: 20px; /* Magnetic area */
            margin: -20px;
        }
        .magnetic-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 2.25rem;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #fff;
            background: linear-gradient(135deg, #7c3aed, #06b6d4);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 10px 30px rgba(139,92,246,0.3);
            transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.2s;
            transform-style: preserve-3d;
        }
        .magnetic-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
            pointer-events: none;
        }
        
        .magnetic-btn.ghost {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: none;
        }
        .magnetic-btn.ghost:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(139,92,246,0.5);
        }

        /* ── BACKGROUND MESH & PARALLAX ── */
        .hero-mesh {
            background: radial-gradient(circle at 50% 0%, rgba(139,92,246,0.15), transparent 60%),
                        radial-gradient(circle at 80% 80%, rgba(6,182,212,0.1), transparent 50%);
        }
        .parallax-layer {
            will-change: transform;
        }

        /* ── TEXT SCRAMBLE / GLITCH ── */
        .scramble-text {
            display: inline-block;
            font-family: 'JetBrains Mono', monospace;
            position: relative;
        }
        
        /* General Utilities */
        .text-gradient-hero {
            background: linear-gradient(135deg, #c084fc 0%, #06b6d4 50%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .ide-container {
            border-radius: 1.25rem;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.08);
            background: #0d0d12;
            box-shadow: 0 40px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(139,92,246,0.1);
        }

        .navbar-glass {
            background: rgba(3,3,5,0.5);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

    </style>
</head>
<body class="landing-page antialiased">

<!-- Custom Cursor Elements -->
<div id="cursor-glow"></div>
<div id="cursor-dot"></div>

<!-- Scroll Progress -->
<div id="scroll-progress"></div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 1: NAVBAR
     ═══════════════════════════════════════════════════════════════════════════ -->
<header id="landing-navbar" class="fixed top-0 left-0 right-0 z-50 navbar-glass transition-all duration-300">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <a href="#" class="flex items-center gap-2.5 interactive">
            <x-logo size="h-10 w-10" />
        </a>
        <nav class="hidden md:flex items-center gap-8">
            <a href="#features" class="text-sm font-medium text-[var(--landing-text-secondary)] hover:text-white transition-colors interactive">Features</a>
            <a href="#ide-showcase" class="text-sm font-medium text-[var(--landing-text-secondary)] hover:text-white transition-colors interactive">IDE</a>
            <a href="#ai-agent" class="text-sm font-medium text-[var(--landing-text-secondary)] hover:text-white transition-colors interactive">AI Agent</a>
            <a href="#security" class="text-sm font-medium text-[var(--landing-text-secondary)] hover:text-white transition-colors interactive">Security</a>
        </nav>
        <div class="flex items-center gap-4">
            @auth
                <div class="magnetic-wrap">
                    <a href="{{ route('dashboard') }}" class="magnetic-btn interactive">Dashboard</a>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-[var(--landing-text-secondary)] hover:text-white transition-colors interactive hidden sm:block">Sign In</a>
                <div class="magnetic-wrap">
                    <a href="{{ route('register') }}" class="magnetic-btn interactive">Get Started</a>
                </div>
            @endauth
        </div>
    </div>
</header>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 2: HERO (3D Interactive)
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="hero-mesh relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
    
    <!-- Floating 3D Shapes Background -->
    <div class="shape-3d cube parallax-layer" data-speed="0.05" style="top: 20%; left: 10%;">
        <div class="front"></div><div class="back"></div><div class="right"></div><div class="left"></div><div class="top"></div><div class="bottom"></div>
    </div>
    <div class="shape-3d cube parallax-layer" data-speed="-0.08" style="bottom: 30%; right: 15%; transform: scale(0.6)">
        <div class="front"></div><div class="back"></div><div class="right"></div><div class="left"></div><div class="top"></div><div class="bottom"></div>
    </div>

    <!-- Interactive CSS Robot Head -->
    <div class="robot-head-container hidden lg:block parallax-layer" data-speed="0.1">
        <div class="robot-head" id="robot-head">
            <div class="robot-visor">
                <div class="robot-eye left"></div>
                <div class="robot-eye right"></div>
            </div>
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 py-20 flex flex-col items-center lg:items-start text-center lg:text-left w-full">
        
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-violet-500/30 bg-violet-500/[0.1] text-violet-300 text-xs font-semibold mb-8 backdrop-blur-md">
            <span class="w-2 h-2 rounded-full bg-violet-400 animate-pulse"></span>
            Vision 2026 Ultimate Prototype
        </div>

        <!-- Scramble Text Headline -->
        <h1 class="text-6xl md:text-7xl lg:text-8xl font-black leading-[1.1] tracking-tight mb-8">
            <span class="text-white">Code the</span><br>
            <span class="text-gradient-hero scramble-text" data-text="Future of Higher Ed.">Future of Higher Ed.</span>
        </h1>

        <p class="text-xl text-[var(--landing-text-secondary)] max-w-2xl mb-12 leading-relaxed">
            Sandboxed workspaces, multi-cursor real-time collaboration, and an autonomous AI Agent built purely for universities. 
            Experience the <span class="text-white font-medium">next dimension</span> of coding.
        </p>

        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-6">
            <div class="magnetic-wrap">
                <a href="{{ route('register') }}" class="magnetic-btn interactive text-lg px-10 py-4">
                    Deploy Your Instance
                </a>
            </div>
            <div class="magnetic-wrap">
                <a href="{{ route('demo') }}" class="magnetic-btn ghost interactive text-lg px-10 py-4">
                    Watch Showcase
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 3: TILT FEATURES GRID
     ═══════════════════════════════════════════════════════════════════════════ -->
<section id="features" class="relative py-32 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-20">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-6">
                Engineered for <span class="text-gradient-hero">Performance</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Tilt Card 1 -->
            <div class="tilt-card p-8 lg:col-span-2 parallax-layer interactive" data-speed="0.03">
                <div class="tilt-glare"></div>
                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-violet-500/20 flex items-center justify-center text-violet-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">code-server IDE</h3>
                </div>
                <p class="text-[var(--landing-text-secondary)] relative z-10 text-lg">
                    Full VS Code running in your browser. Complete terminal access, extensions, and IntelliSense.
                </p>
                <div class="mt-8 relative z-10 ide-container p-4">
                    <div class="font-mono text-sm text-cyan-300">
                        $ docker run -d --name workspace -p 8080:8080 codercom/code-server
                    </div>
                </div>
            </div>

            <!-- Tilt Card 2 -->
            <div class="tilt-card p-8 parallax-layer interactive" data-speed="0.05">
                <div class="tilt-glare"></div>
                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/20 flex items-center justify-center text-cyan-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Real-Time Sync</h3>
                </div>
                <p class="text-[var(--landing-text-secondary)] relative z-10">
                    Laravel Reverb powered WebSockets for sub-millisecond cursor synchronization.
                </p>
            </div>

             <!-- Tilt Card 3 -->
             <div class="tilt-card p-8 parallax-layer interactive" data-speed="0.04">
                <div class="tilt-glare"></div>
                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/20 flex items-center justify-center text-pink-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">AI Sandboxing</h3>
                </div>
                <p class="text-[var(--landing-text-secondary)] relative z-10">
                    Agent writes code autonomously but requires human approval before execution. Fully audited.
                </p>
            </div>

            <!-- Tilt Card 4 (Spans 2) -->
            <div class="tilt-card p-8 lg:col-span-2 parallax-layer interactive" data-speed="0.02">
                <div class="tilt-glare"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                    <img src="{{ asset('images/landing/collaboration.png') }}" class="w-full md:w-1/2 rounded-xl object-cover shadow-2xl" alt="Collab">
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-4">Instructor Command Deck</h3>
                        <p class="text-[var(--landing-text-secondary)]">
                            Observe an entire lecture hall, throttle resources per cohort, and replay sessions stroke by stroke. Total visibility.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 4: CALL TO ACTION WITH MAGNETIC BUTTONS
     ═══════════════════════════════════════════════════════════════════════════ -->
<section class="relative py-40 px-6 text-center overflow-hidden">
    <div class="absolute inset-0" style="background: radial-gradient(circle at center, rgba(139,92,246,0.2), transparent 70%);"></div>
    
    <div class="relative z-10 max-w-3xl mx-auto parallax-layer" data-speed="-0.05">
        <h2 class="text-5xl md:text-7xl font-black text-white mb-8">
            Experience <br><span class="text-gradient-hero">VisionLab</span>
        </h2>
        
        <div class="flex justify-center gap-6 mt-12">
            <div class="magnetic-wrap">
                <a href="{{ route('register') }}" class="magnetic-btn interactive text-xl px-12 py-5 shadow-[0_0_50px_rgba(139,92,246,0.4)]">
                    Create Workspace
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════════════════════════ -->
<footer class="relative py-12 px-6 border-t border-[var(--landing-card-border)] bg-[var(--landing-surface)]">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-3">
            <x-logo size="h-6 w-6" />
            <span class="text-[var(--landing-text-secondary)] text-sm">© {{ date('Y') }} VisionLab Pro.</span>
        </div>
        <div class="flex gap-6">
            <a href="#" class="text-[var(--landing-text-secondary)] hover:text-white transition-colors interactive">Twitter</a>
            <a href="#" class="text-[var(--landing-text-secondary)] hover:text-white transition-colors interactive">GitHub</a>
            <a href="#" class="text-[var(--landing-text-secondary)] hover:text-white transition-colors interactive">Discord</a>
        </div>
    </div>
</footer>

<!-- ═══════════════════════════════════════════════════════════════════════════
     JAVASCRIPT: 3D INTERACTIONS & EFFECTS
     ═══════════════════════════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    // 1. CUSTOM CURSOR & GLOW
    const cursorDot = document.getElementById('cursor-dot');
    const cursorGlow = document.getElementById('cursor-glow');
    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let dotX = mouseX;
    let dotY = mouseY;
    let glowX = mouseX;
    let glowY = mouseY;

    window.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
    });

    function animateCursor() {
        // Dot follows quickly
        dotX += (mouseX - dotX) * 0.5;
        dotY += (mouseY - dotY) * 0.5;
        cursorDot.style.transform = `translate(${dotX}px, ${dotY}px)`;

        // Glow follows smoothly with delay (spring physics)
        glowX += (mouseX - glowX) * 0.1;
        glowY += (mouseY - glowY) * 0.1;
        cursorGlow.style.transform = `translate(${glowX}px, ${glowY}px)`;

        requestAnimationFrame(animateCursor);
    }
    animateCursor();

    // Hover effects on interactive elements
    const interactives = document.querySelectorAll('.interactive');
    interactives.forEach(el => {
        el.addEventListener('mouseenter', () => {
            cursorDot.style.transform = `translate(${dotX}px, ${dotY}px) scale(2)`;
            cursorDot.style.background = 'transparent';
            cursorDot.style.border = '2px solid #06b6d4';
            cursorGlow.style.width = '200px';
            cursorGlow.style.height = '200px';
            cursorGlow.style.background = 'radial-gradient(circle, rgba(6,182,212,0.3) 0%, transparent 60%)';
        });
        el.addEventListener('mouseleave', () => {
            cursorDot.style.background = '#fff';
            cursorDot.style.border = 'none';
            cursorGlow.style.width = '400px';
            cursorGlow.style.height = '400px';
            cursorGlow.style.background = 'radial-gradient(circle, rgba(139,92,246,0.15) 0%, transparent 60%)';
        });
    });

    // 2. SCROLL PROGRESS BAR
    const progressBar = document.getElementById('scroll-progress');
    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        progressBar.style.width = scrolled + "%";
    });

    // 3. MAGNETIC BUTTONS
    const magnets = document.querySelectorAll('.magnetic-wrap');
    magnets.forEach(wrap => {
        const btn = wrap.querySelector('.magnetic-btn');
        wrap.addEventListener('mousemove', (e) => {
            const rect = wrap.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            // Magnetic pull
            btn.style.transform = `translate(${x * 0.4}px, ${y * 0.4}px) rotateX(${-y*0.1}deg) rotateY(${x*0.1}deg)`;
        });
        wrap.addEventListener('mouseleave', () => {
            btn.style.transform = 'translate(0px, 0px) rotateX(0deg) rotateY(0deg)';
        });
    });

    // 4. 3D TILT CARDS WITH GLARE
    const tiltCards = document.querySelectorAll('.tilt-card');
    tiltCards.forEach(card => {
        const glare = card.querySelector('.tilt-glare');
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = ((y - centerY) / centerY) * -10; // Max 10deg
            const rotateY = ((x - centerX) / centerX) * 10;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
            
            if(glare) {
                // Move glare opposite to cursor
                glare.style.background = `radial-gradient(circle at ${x}px ${y}px, rgba(255,255,255,0.2), transparent 60%)`;
            }
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            if(glare) {
                glare.style.background = `radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1), transparent 50%)`;
            }
        });
    });

    // 5. PARALLAX LAYERS
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        document.querySelectorAll('.parallax-layer').forEach(layer => {
            const speed = parseFloat(layer.getAttribute('data-speed'));
            layer.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });

    // 6. ROBOT EYES CURSOR TRACKING
    const robotHead = document.getElementById('robot-head');
    const eyes = document.querySelectorAll('.robot-eye');
    if(robotHead && eyes.length) {
        window.addEventListener('mousemove', (e) => {
            const rect = robotHead.getBoundingClientRect();
            const headX = rect.left + rect.width / 2;
            const headY = rect.top + rect.height / 2;
            
            // Calculate angle and distance
            const angleX = (e.clientX - headX) / window.innerWidth;
            const angleY = (e.clientY - headY) / window.innerHeight;
            
            // Rotate entire head slightly
            robotHead.style.transform = `rotateY(${angleX * 40}deg) rotateX(${-angleY * 40}deg)`;
            
            // Move eyes inside visor
            eyes.forEach(eye => {
                const moveX = angleX * 20; // max px move
                const moveY = angleY * 15;
                // apply base positioning plus offset
                const baseLeft = eye.classList.contains('left') ? 25 : 75;
                // Using transform for eyes
                eye.style.transform = `translate(calc(-50% + ${moveX}px), calc(-50% + ${moveY}px))`;
            });
        });
    }

    // 7. TEXT SCRAMBLE / DECODE EFFECT
    class TextScramble {
        constructor(el) {
            this.el = el;
            this.chars = '!<>-_\\/[]{}—=+*^?#________';
            this.update = this.update.bind(this);
        }
        setText(newText) {
            const oldText = this.el.innerText;
            const length = Math.max(oldText.length, newText.length);
            const promise = new Promise((resolve) => this.resolve = resolve);
            this.queue = [];
            for (let i = 0; i < length; i++) {
                const from = oldText[i] || '';
                const to = newText[i] || '';
                const start = Math.floor(Math.random() * 40);
                const end = start + Math.floor(Math.random() * 40);
                this.queue.push({ from, to, start, end });
            }
            cancelAnimationFrame(this.frameRequest);
            this.frame = 0;
            this.update();
            return promise;
        }
        update() {
            let output = '';
            let complete = 0;
            for (let i = 0, n = this.queue.length; i < n; i++) {
                let { from, to, start, end, char } = this.queue[i];
                if (this.frame >= end) {
                    complete++;
                    output += to;
                } else if (this.frame >= start) {
                    if (!char || Math.random() < 0.28) {
                        char = this.randomChar();
                        this.queue[i].char = char;
                    }
                    output += `<span class="opacity-50 text-cyan-400">${char}</span>`;
                } else {
                    output += from;
                }
            }
            this.el.innerHTML = output;
            if (complete === this.queue.length) {
                this.resolve();
            } else {
                this.frameRequest = requestAnimationFrame(this.update);
                this.frame++;
            }
        }
        randomChar() {
            return this.chars[Math.floor(Math.random() * this.chars.length)];
        }
    }

    const scrambleEl = document.querySelector('.scramble-text');
    if(scrambleEl) {
        const fx = new TextScramble(scrambleEl);
        // Initial animation
        setTimeout(() => {
            fx.setText(scrambleEl.getAttribute('data-text'));
        }, 500);
        
        // Loop effect
        setInterval(() => {
            const text = scrambleEl.getAttribute('data-text');
            fx.setText(text);
        }, 8000);
    }

});
</script>

</body>
</html>

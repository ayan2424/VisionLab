<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Explore VisionLab features: browser-based VS Code IDE, AI teaching assistant, real-time collaboration, video sessions, and smart LMS integration.">
    <title>Features — VisionLab IDE, AI Agent & Live Collaboration</title>
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
        .hero { position: relative; min-height: 75vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 8rem 2rem 4rem; overflow: hidden; }
        .canvas-container { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
        .canvas-container canvas { display: block; width: 100%; height: 100%; }
        .hero-headline { font-size: clamp(3rem, 7vw, 5.25rem); font-weight: 900; line-height: 1.1; letter-spacing: -0.04em; }

        /* Alternating showcase feature blocks */
        .feature-block { display: grid; grid-template-columns: 1fr; gap: 4rem; align-items: center; margin-bottom: 8rem; }
        .feature-block:last-child { margin-bottom: 0; }
        @media (min-width: 1024px) {
            .feature-block { grid-template-columns: 1fr 1.1fr; }
            .feature-block.reversed { direction: rtl; }
            .feature-block.reversed .feature-content { direction: ltr; }
            .feature-block.reversed .feature-visual { direction: ltr; }
        }
        .feature-content { display: flex; flex-direction: column; gap: 1.25rem; }
        .feature-tag { font-size: 0.7rem; color: var(--cyan); text-transform: uppercase; letter-spacing: 0.15em; font-weight: 700; }
        .feature-title { font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight: 800; letter-spacing: -0.02em; }
        .feature-desc { font-size: 0.95rem; color: var(--text-secondary); line-height: 1.7; }

        /* Custom Mocks inside Visuals */
        .feature-visual {
            background: rgba(255,255,255,0.015); border: 1px solid var(--border); border-radius: 1rem;
            padding: 1.5rem; position: relative; overflow: hidden; width: 100%; aspect-ratio: 16/10;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5); display: flex; flex-direction: column;
        }
        .mock-header { display: flex; align-items: center; gap: 6px; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); margin-bottom: 1rem; }
        .mock-dot { width: 8px; height: 8px; border-radius: 50%; }
        .mock-body { flex: 1; font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; overflow: hidden; }

        /* Code highlight inside Visuals */
        .code-row { display: flex; gap: 1rem; line-height: 1.6; }
        .code-ln { color: var(--text-muted); user-select: none; width: 2.5ch; text-align: right; }
        .code-keyword { color: #c084fc; }
        .code-type { color: #facc15; }
        .code-func { color: #22d3ee; }
        .code-string { color: #4ade80; }
        .code-comment { color: var(--text-muted); font-style: italic; }

        /* Comparison Table */
        .table-container { overflow-x: auto; width: 100%; border: 1px solid var(--border); border-radius: 1rem; background: rgba(255,255,255,0.01); margin-top: 3rem; }
        .comp-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
        .comp-table th, .comp-table td { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
        .comp-table th { font-weight: 700; color: white; background: rgba(255,255,255,0.01); }
        .comp-table td:first-child { font-weight: 600; }
        .check-icon { color: var(--emerald); font-weight: bold; }
        .cross-icon { color: #ef4444; font-weight: bold; }

        /* Integrations Grid */
        .integration-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 3rem; }
        @media (min-width: 640px) { .integration-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1024px) { .integration-grid { grid-template-columns: repeat(6, 1fr); } }
        .integration-card { background: rgba(255,255,255,0.015); border: 1px solid var(--border); border-radius: 0.75rem; padding: 1.5rem 1rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.75rem; transition: all 0.3s; }
        .integration-card:hover { border-color: var(--border-hover); background: rgba(255,255,255,0.03); transform: translateY(-2px); }
        .integration-icon { font-size: 1.75rem; }
        .integration-name { font-size: 0.85rem; font-weight: 600; }

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
            <a href="{{ route('features') }}" class="active">Features</a>
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
    <div class="canvas-container" id="featuresHeroCanvas"></div>
    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto">
        <h1 class="hero-headline reveal text-gradient-hero">
            Everything you need. <br><span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">Nothing you don't.</span>
        </h1>
        <p class="section-sub reveal reveal-delay-1" style="margin: 2rem auto 0; text-align:center;">
            Deep-dive into the technical primitives driving VisionLab, from container isolated workspaces to event-driven collaborative queues.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- FEATURE SHOWCASE -->
<section class="section">
    <!-- Block 1: IDE -->
    <div class="feature-block reveal">
        <div class="feature-content">
            <span class="feature-tag">Sandbox Primitive</span>
            <h2 class="feature-title">Browser-Based IDE</h2>
            <p class="feature-desc">
                Provide a complete sandbox compilation layer to every student. Powered by Docker container profiles that compile code on sandboxed loopbacks, without configuration drift. Integrates terminal execution, file system hierarchies, and diagnostics directly in the browser.
            </p>
        </div>
        <div class="feature-visual">
            <div class="mock-header">
                <div class="mock-dot" style="background:#ef4444"></div>
                <div class="mock-dot" style="background:#eab308"></div>
                <div class="mock-dot" style="background:#22c55e"></div>
                <span style="color:var(--text-muted);font-size:0.65rem;margin-left:0.5rem">workspace/src/sandbox.rs</span>
            </div>
            <div class="mock-body">
                <div class="code-row"><span class="code-ln">1</span><span><span class="code-keyword">use</span> std::process::Command;</span></div>
                <div class="code-row"><span class="code-ln">2</span><span></span></div>
                <div class="code-row"><span class="code-ln">3</span><span><span class="code-keyword">fn</span> <span class="code-func">execute_sandbox</span>(id: &<span class="code-type">str</span>) {</span></div>
                <div class="code-row"><span class="code-ln">4</span><span>    <span class="code-keyword">let</span> output = Command::<span class="code-func">new</span>(<span class="code-string">"nix-shell"</span>)</span></div>
                <div class="code-row"><span class="code-ln">5</span><span>        .<span class="code-func">arg</span>(<span class="code-string">"--run"</span>)</span></div>
                <div class="code-row"><span class="code-ln">6</span><span>        .<span class="code-func">arg</span>(<span class="code-string">"cargo run"</span>)</span></div>
                <div class="code-row"><span class="code-ln">7</span><span>        .<span class="code-func">output</span>();</span></div>
                <div class="code-row"><span class="code-ln">8</span><span>    <span class="code-func">println!</span>(<span class="code-string">"Container stdout: {}"</span>, String::<span class="code-func">from_utf8_lossy</span>(&output.stdout));</span></div>
                <div class="code-row"><span class="code-ln">9</span><span>}</span></div>
            </div>
        </div>
    </div>

    <!-- Block 2: AI Assistant -->
    <div class="feature-block reversed reveal">
        <div class="feature-content">
            <span class="feature-tag">Responsible Helper</span>
            <h2 class="feature-title">AI Teaching Assistant</h2>
            <p class="feature-desc">
                Deploy socratic models designed to teach rather than feed. Socratic, Guided, and Autonomous modes guarantee students think through compiler issues. Every direct code mutation must be explicitly approved via a dynamic dual-pane patch reviewer, locking down security loops.
            </p>
        </div>
        <div class="feature-visual" style="border-color: rgba(168,85,247,0.3)">
            <div class="mock-header" style="border-bottom-color: rgba(168,85,247,0.1)">
                <div class="mock-dot" style="background:var(--purple)"></div>
                <span style="color:var(--text-secondary);font-size:0.65rem;margin-left:0.5rem;font-weight:600">Socratic Assistant</span>
            </div>
            <div class="mock-body" style="display:flex;flex-direction:column;gap:1rem;color:var(--text-secondary)">
                <div style="background:rgba(255,255,255,0.02);padding:0.75rem;border-radius:0.5rem;border:1px solid var(--border)">
                    <span style="color:var(--cyan);font-weight:600">Student:</span> Why does my Rust compiler raise a lifetime mismatch?
                </div>
                <div style="background:rgba(168,85,247,0.05);padding:0.75rem;border-radius:0.5rem;border:1px solid rgba(168,85,247,0.2)">
                    <span style="color:var(--purple);font-weight:600">AI:</span> Look at the borrow of your vector on line 4. Is the vector outliving the reference you're passing to the thread? Try matching the scope bounds.
                </div>
            </div>
        </div>
    </div>

    <!-- Block 3: Collaboration -->
    <div class="feature-block reveal">
        <div class="feature-content">
            <span class="feature-tag">Presence Protocols</span>
            <h2 class="feature-title">Real-Time Collaboration</h2>
            <p class="feature-desc">
                Broadcast edits, viewport selections, and cursor movements across the cohort. Backed by Laravel Reverb channels, our OT-based synchronization keeps every student and instructor in lockstep without document conflicts.
            </p>
        </div>
        <div class="feature-visual">
            <div class="mock-header">
                <span style="color:var(--emerald);font-size:0.65rem;font-weight:700">● 4 Active Peer cursors</span>
            </div>
            <div class="mock-body" style="display:flex;flex-direction:column;gap:0.75rem">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem;background:rgba(255,255,255,0.02);border-radius:0.5rem;border:1px solid var(--border)">
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--purple)"></span>
                        <span>Ayan S. (Lead)</span>
                    </div>
                    <span style="font-size:0.65rem;color:var(--text-muted)">Editing line 12</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem;background:rgba(255,255,255,0.02);border-radius:0.5rem;border:1px solid var(--border)">
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--cyan)"></span>
                        <span>Sarah M.</span>
                    </div>
                    <span style="font-size:0.65rem;color:var(--text-muted)">Viewing console</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Block 4: Video Sessions -->
    <div class="feature-block reversed reveal">
        <div class="feature-content">
            <span class="feature-tag">Unified Media</span>
            <h2 class="feature-title">Jitsi Video Sessions</h2>
            <p class="feature-desc">
                Engage in direct 1-to-1 video debugging inside workspaces. We embed secure Jitsi nodes authorized through cryptographic JWT tokens, eliminating external link sharing or complex calendar invitations.
            </p>
        </div>
        <div class="feature-visual" style="border-color: rgba(236,72,153,0.3)">
            <div class="mock-header" style="border-bottom-color: rgba(236,72,153,0.1)">
                <span style="color:var(--pink);font-size:0.65rem;font-weight:700">🎥 Embedded Office Hours</span>
            </div>
            <div class="mock-body" style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;height:100%">
                <div style="background:#1a1a1a;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;position:relative">
                    <span style="font-size:0.7rem;color:var(--text-secondary)">Instructor (Ayan)</span>
                    <span style="position:absolute;bottom:5px;left:5px;font-size:0.6rem;background:rgba(0,0,0,0.6);padding:2px 4px;border-radius:3px">Mic: On</span>
                </div>
                <div style="background:#1a1a1a;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;position:relative">
                    <span style="font-size:0.7rem;color:var(--text-secondary)">Student (Sarah)</span>
                    <span style="position:absolute;bottom:5px;left:5px;font-size:0.6rem;background:rgba(0,0,0,0.6);padding:2px 4px;border-radius:3px">Mic: On</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Block 5: Smart Grading -->
    <div class="feature-block reveal">
        <div class="feature-content">
            <span class="feature-tag">Assesment Loop</span>
            <h2 class="feature-title">Smart Grading & Diffs</h2>
            <p class="feature-desc">
                Review student assignments directly from snapshot histories. Compare code differences through dynamic diff panels and check structural patterns without cloning repositories or configuring local runtimes.
            </p>
        </div>
        <div class="feature-visual">
            <div class="mock-header">
                <span style="color:var(--text-secondary);font-size:0.65rem;font-weight:700">Assignment Review Diff</span>
            </div>
            <div class="mock-body" style="font-size:0.65rem">
                <div style="background:rgba(239,68,68,0.1);padding:2px 4px;border-radius:3px;color:#f87171">- return val * 2.0;</div>
                <div style="background:rgba(34,197,94,0.1);padding:2px 4px;border-radius:3px;color:#4ade80">+ return Math.pow(val, 2);</div>
                <div style="margin-top:1rem;color:var(--text-muted)">Instructor Comment: Optimal complexity achieved. (Grade: 100/100)</div>
            </div>
        </div>
    </div>

    <!-- Block 6: Hardened Infrastructure -->
    <div class="feature-block reversed reveal">
        <div class="feature-content">
            <span class="feature-tag">DevSecOps Standard</span>
            <h2 class="feature-title">Hardened Governance</h2>
            <p class="feature-desc">
                Enforce OWASP ASVS Level 2 parameters across the board. Every API request transits via path-traversal sanitization bounds, and containers block root permissions using strict security policies, ensuring complete protection.
            </p>
        </div>
        <div class="feature-visual" style="border-color: rgba(16,185,129,0.3)">
            <div class="mock-header" style="border-bottom-color: rgba(16,185,129,0.1)">
                <span style="color:var(--emerald);font-size:0.65rem;font-weight:700">⚙️ Security Telemetry Log</span>
            </div>
            <div class="mock-body" style="color:var(--emerald);font-size:0.7rem">
                <div>[INFO] Sanitizing path request: /var/workspace/src/app.py</div>
                <div>[INFO] REALPATH check: OK</div>
                <div>[INFO] Capabilities check: Dropped ALL</div>
                <div>[INFO] User context: UID 1000 (Non-privileged)</div>
                <div style="color:white;font-weight:bold;margin-top:0.5rem">[SECURE] Audit loop completed. Request authorized.</div>
            </div>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- COMPARISON TABLE -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--purple);font-weight:700;display:block;margin-bottom:1rem">Feature Matrix</span>
        <h2 class="section-heading">How VisionLab Compares</h2>
        <p class="section-sub" style="margin:0 auto">A holistic review of tooling features built directly into our ecosystem.</p>
    </div>

    <div class="table-container reveal">
        <table class="comp-table">
            <thead>
                <tr>
                    <th>Capabilities</th>
                    <th>VisionLab</th>
                    <th>Google Classroom</th>
                    <th>GitHub Codespaces</th>
                    <th>Zoom / Teams</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>In-browser VS Code Workspace</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                </tr>
                <tr>
                    <td>Real-time Multi-cursor Sync</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                </tr>
                <tr>
                    <td>Socratic AI Guidance</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                </tr>
                <tr>
                    <td>Embedded JWT Video Rooms</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="check-icon">✓</span></td>
                </tr>
                <tr>
                    <td>Direct Diff Review Grading</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                </tr>
                <tr>
                    <td>OWASP ASVS Level 2 Hardening</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<div class="section-sep"></div>

<!-- INTEGRATIONS -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--cyan);font-weight:700;display:block;margin-bottom:1rem">Ecosystem</span>
        <h2 class="section-heading">Seamless Integrations</h2>
        <p class="section-sub" style="margin:0 auto">Connect VisionLab with your institution's core services.</p>
    </div>

    <div class="integration-grid reveal">
        <div class="integration-card">
            <span class="integration-icon">🎨</span>
            <span class="integration-name">Canvas LMS</span>
        </div>
        <div class="integration-card">
            <span class="integration-icon">🎓</span>
            <span class="integration-name">Moodle LMS</span>
        </div>
        <div class="integration-card">
            <span class="integration-icon">🗂️</span>
            <span class="integration-name">Blackboard</span>
        </div>
        <div class="integration-card">
            <span class="integration-icon">🐙</span>
            <span class="integration-name">GitHub API</span>
        </div>
        <div class="integration-card">
            <span class="integration-icon">⚡</span>
            <span class="integration-name">Vercel Deploy</span>
        </div>
        <div class="integration-card">
            <span class="integration-icon">🚂</span>
            <span class="integration-name">Railway Deploy</span>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section" style="text-align:center;padding:8rem 2rem">
    <div style="position:relative;z-index:10;max-width:700px;margin:0 auto" class="reveal">
        <h2 class="section-heading">Get Started Today</h2>
        <p class="section-sub" style="margin:0 auto 3rem">Empower your computer science department with sandboxed workspace nodes.</p>
        <a href="{{ route('register') }}" class="btn btn-primary">Try VisionLab Features</a>
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
            }
        });
    }, { threshold: 0.1 });
    reveals.forEach(r => observer.observe(r));

    // ── Three.js Hero Scene (Particle Sphere) ──
    (function initHeroScene() {
        const container = document.getElementById('featuresHeroCanvas');
        if (!container) return;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 0, 10);

        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        container.appendChild(renderer.domElement);

        // Lighting
        scene.add(new THREE.AmbientLight(0x404060, 0.5));
        const pl = new THREE.PointLight(0x00e5ff, 2, 30); pl.position.set(4, 4, 6); scene.add(pl);
        const pl2 = new THREE.PointLight(0xec4899, 1.5, 30); pl2.position.set(-4, -4, 6); scene.add(pl2);

        // Particle Sphere
        const pCount = 500;
        const pGeo = new THREE.BufferGeometry();
        const pPos = new Float32Array(pCount * 3);
        const radius = 3.5;
        for (let i = 0; i < pCount; i++) {
            const u = Math.random();
            const v = Math.random();
            const theta = u * 2.0 * Math.PI;
            const phi = Math.acos(2.0 * v - 1.0);
            
            pPos[i*3] = radius * Math.sin(phi) * Math.cos(theta);
            pPos[i*3+1] = radius * Math.sin(phi) * Math.sin(theta);
            pPos[i*3+2] = radius * Math.cos(phi);
        }
        pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
        const pMat = new THREE.PointsMaterial({
            color: 0x00e5ff,
            size: 0.05,
            transparent: true,
            opacity: 0.6,
            blending: THREE.AdditiveBlending
        });
        const sphere = new THREE.Points(pGeo, pMat);
        scene.add(sphere);

        // Orbiting rings
        const ringGeo = new THREE.TorusGeometry(radius + 0.5, 0.015, 8, 64);
        const ringMat = new THREE.MeshBasicMaterial({ color: 0xa855f7, transparent: true, opacity: 0.3 });
        const ring = new THREE.Mesh(ringGeo, ringMat);
        ring.rotation.x = Math.PI / 3;
        scene.add(ring);

        let mouseX = 0, mouseY = 0;
        document.addEventListener('mousemove', e => {
            mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
            mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
        });

        const clock = new THREE.Clock();
        function animate() {
            requestAnimationFrame(animate);
            const t = clock.getElapsedTime();

            sphere.rotation.y = t * 0.05;
            sphere.rotation.x = t * 0.02;
            ring.rotation.z = -t * 0.1;

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

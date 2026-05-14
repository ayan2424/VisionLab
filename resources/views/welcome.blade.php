<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="VisionLab — The AI-Powered Collaborative Coding Ecosystem. Full VS Code IDE (code-server) with real-time collaboration, video classes, and a 3-mode AI Agent.">
    <title>VisionLab — Code. Collaborate. Conquer.</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Hero gradient mesh animation */
        .mesh-bg {
            background:
                radial-gradient(ellipse 80% 80% at 20% -20%, rgba(240,80,0,0.25) 0%, transparent 60%),
                radial-gradient(ellipse 60% 60% at 80% 120%, rgba(245,158,11,0.2) 0%, transparent 60%),
                radial-gradient(ellipse 50% 50% at 50% 50%, rgba(30,0,60,0.4) 0%, transparent 100%),
                #0a0a0a;
        }
        /* Animated gradient border for hero card */
        @keyframes border-spin {
            from { --angle: 0deg; }
            to   { --angle: 360deg; }
        }
        @property --angle {
            syntax: '<angle>';
            initial-value: 0deg;
            inherits: false;
        }
        .hero-card-border {
            background: conic-gradient(from var(--angle), transparent 70%, rgba(240,80,0,0.7), rgba(245,158,11,0.7), transparent);
            animation: border-spin 4s linear infinite;
        }
        /* Word-reveal animation */
        .word { display: inline-block; opacity: 0; transform: translateY(20px); }
        /* Particle canvas */
        #particle-canvas { position: absolute; inset: 0; pointer-events: none; opacity: 0.4; }
        /* Stats counter */
        .stat-num { font-variant-numeric: tabular-nums; }
        /* Feature card hover */
        .feature-card { transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s ease; }
        .feature-card:hover { transform: translateY(-6px); }
        /* Glow orb */
        .orb {
            position: absolute;
            border-radius: 9999px;
            filter: blur(80px);
            pointer-events: none;
        }
        /* Code preview scrollbar */
        .code-preview::-webkit-scrollbar { width: 4px; }
        .code-preview::-webkit-scrollbar-thumb { background: rgba(240,80,0,0.5); border-radius: 2px; }
        /* Timeline */
        .timeline-line { background: linear-gradient(to bottom, rgba(240,80,0,0.8), rgba(245,158,11,0.3)); }
    </style>
</head>
<body class="bg-void text-slate-100 font-sans antialiased overflow-x-hidden">

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  NAVBAR                                                           -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <!-- Logo -->
        <a href="#" class="flex items-center gap-2.5 group">
            <div class="w-9 h-9 rounded-lg bg-orange-600 flex items-center justify-center shadow-glow-sm group-hover:shadow-glow-brand transition-all duration-300">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
            </div>
            <span class="text-lg font-bold tracking-tight">
                Vision<span class="text-gradient-brand">Code</span>
                <span class="text-orange-400 text-sm font-medium ml-0.5">AI</span>
            </span>
        </a>

        <!-- Nav links -->
        <nav class="hidden md:flex items-center gap-8">
            <a href="#features" class="nav-link">Features</a>
            <a href="#ide-preview" class="nav-link">IDE</a>
            <a href="#ai-agent" class="nav-link">AI Agent</a>
            <a href="#collaboration" class="nav-link">Collaborate</a>
        </nav>

        <!-- CTAs -->
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-ghost py-2 px-4 text-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="nav-link">Sign In</a>
                <a href="{{ route('register') }}" class="btn-glow py-2 px-4 text-sm">
                    Get Started Free
                </a>
            @endauth
        </div>
    </div>
</header>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  HERO SECTION                                                     -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<section class="mesh-bg relative min-h-screen flex flex-col items-center justify-center px-6 pt-24 pb-16 overflow-hidden">

    <!-- Canvas particles -->
    <canvas id="particle-canvas"></canvas>

    <!-- Ambient orbs -->
    <div class="orb w-[600px] h-[600px] bg-orange-600/20 top-[-200px] left-[-100px]"></div>
    <div class="orb w-[400px] h-[400px] bg-amber-500/15 bottom-[-100px] right-[-50px]"></div>

    <!-- Badge -->
    <div class="relative z-10 mb-6 opacity-0" id="hero-badge">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-orange-500/30 bg-orange-500/10 text-orange-300 text-xs font-medium">
            <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
            Aptech Vision 2026 — Competition Entry
            <span class="ml-1 text-orange-500">→</span>
        </div>
    </div>

    <!-- Hero headline -->
    <div class="relative z-10 text-center max-w-5xl mx-auto">
        <h1 class="text-5xl md:text-7xl lg:text-8xl font-black leading-[1.05] tracking-tight mb-6" id="hero-headline">
            <span class="word">Code.</span>
            <span class="word">&nbsp;</span>
            <span class="word">Collaborate.</span>
            <span class="word">&nbsp;</span>
            <span class="word text-gradient-brand">Conquer.</span>
        </h1>

        <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10 opacity-0" id="hero-sub">
            The AI-powered coding ecosystem that rivals
            <span class="text-slate-300 font-medium">Cursor AI</span> in the browser.
            Full VS Code IDE, real-time collaboration, video classes, and a 3-mode AI Agent — one platform.
        </p>

        <!-- CTA buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16 opacity-0" id="hero-ctas">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-glow px-8 py-4 text-base">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Open Workspace
                </a>
            @else
                <a href="{{ route('register') }}" class="btn-glow px-8 py-4 text-base">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Start Coding Free
                </a>
                <a href="{{ route('login') }}" class="btn-ghost px-8 py-4 text-base">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Live Demo
                </a>
            @endauth
        </div>

        <!-- IDE Preview Card -->
        <div class="relative z-10 opacity-0 perspective-1000" id="hero-ide-card">
            <!-- Glowing border ring -->
            <div class="absolute -inset-[1px] rounded-2xl overflow-hidden z-0 opacity-70">
                <div class="hero-card-border absolute inset-0 rounded-2xl"></div>
            </div>

            <!-- IDE Mock -->
            <div class="relative z-10 rounded-2xl bg-elevated border border-border overflow-hidden shadow-[0_40px_80px_rgba(0,0,0,0.8)]">
                <!-- IDE Titlebar -->
                <div class="flex items-center gap-3 px-4 py-3 bg-surface/80 border-b border-border">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-500/70"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500/70"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/70"></div>
                    </div>
                    <div class="flex-1 flex items-center justify-center">
                        <div class="flex items-center gap-2 px-3 py-1 rounded-md bg-void/60 border border-border text-xs text-muted font-mono">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            workspace/main.py
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex -space-x-2">
                            <div class="w-6 h-6 rounded-full bg-orange-500 border-2 border-elevated flex items-center justify-center text-[9px] font-bold">A</div>
                            <div class="w-6 h-6 rounded-full bg-amber-500 border-2 border-elevated flex items-center justify-center text-[9px] font-bold">J</div>
                            <div class="w-6 h-6 rounded-full bg-emerald-500 border-2 border-elevated flex items-center justify-center text-[9px] font-bold">S</div>
                        </div>
                        <span class="text-xs text-muted">3 online</span>
                    </div>
                </div>

                <!-- IDE Body -->
                <div class="flex h-64 md:h-80">
                    <!-- File Explorer -->
                    <div class="w-40 bg-surface/40 border-r border-border px-3 py-3 hidden md:block">
                        <p class="text-[10px] font-semibold text-muted uppercase tracking-wider mb-2">Explorer</p>
                        <div class="space-y-0.5 font-mono text-xs">
                            <div class="flex items-center gap-1.5 text-slate-400 py-0.5 px-1.5 rounded">
                                <span class="text-yellow-500">📁</span> workspace/
                            </div>
                            <div class="flex items-center gap-1.5 text-orange-400 py-0.5 px-1.5 rounded bg-orange-500/10 ml-3">
                                <span>🐍</span> main.py
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500 py-0.5 px-1.5 ml-3">
                                <span>📄</span> utils.py
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500 py-0.5 px-1.5 ml-3">
                                <span>📄</span> models.py
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500 py-0.5 px-1.5">
                                <span>📋</span> requirements.txt
                            </div>
                        </div>
                    </div>

                    <!-- Code Editor -->
                    <div class="flex-1 code-preview overflow-y-auto bg-void/60 px-6 py-4 font-mono text-xs leading-relaxed">
                        <div class="flex gap-4 text-muted">
                            <div class="select-none text-right w-4">
                                <div>1</div><div>2</div><div>3</div><div>4</div><div>5</div>
                                <div>6</div><div>7</div><div>8</div><div>9</div><div>10</div>
                                <div>11</div><div>12</div><div>13</div><div>14</div><div>15</div>
                            </div>
                            <div class="flex-1">
                                <div><span class="text-orange-400">from</span> <span class="text-amber-400">visioncode</span> <span class="text-orange-400">import</span> <span class="text-slate-300">AIAgent, Workspace</span></div>
                                <div><span class="text-orange-400">import</span> <span class="text-amber-400">asyncio</span></div>
                                <div>&nbsp;</div>
                                <div><span class="text-muted"># Initialize the AI-powered workspace</span></div>
                                <div><span class="text-amber-400">agent</span> <span class="text-slate-400">=</span> <span class="text-yellow-400">AIAgent</span><span class="text-slate-300">(</span></div>
                                <div class="ml-8"><span class="text-orange-400">mode</span><span class="text-slate-400">=</span><span class="text-emerald-400">"AGENT"</span><span class="text-slate-300">,</span></div>
                                <div class="ml-8"><span class="text-orange-400">model</span><span class="text-slate-400">=</span><span class="text-emerald-400">"gemini-2.0-flash"</span><span class="text-slate-300">,</span></div>
                                <div class="ml-8"><span class="text-orange-400">sandbox</span><span class="text-slate-400">=</span><span class="text-orange-400">True</span></div>
                                <div><span class="text-slate-300">)</span></div>
                                <div>&nbsp;</div>
                                <div><span class="text-orange-400">async def</span> <span class="text-yellow-400">main</span><span class="text-slate-300">():</span></div>
                                <div class="ml-8"><span class="text-muted"># Let the AI scan and improve our code</span></div>
                                <div class="ml-8"><span class="text-amber-400">result</span> <span class="text-slate-400">=</span> <span class="text-orange-400">await</span> <span class="text-amber-400">agent</span><span class="text-slate-300">.</span><span class="text-yellow-400">analyze</span><span class="text-slate-300">(</span><span class="text-emerald-400">"./workspace"</span><span class="text-slate-300">)</span></div>
                                <div class="ml-8"><span class="text-orange-400">print</span><span class="text-slate-300">(</span><span class="text-amber-400">result</span><span class="text-slate-300">.</span><span class="text-yellow-400">patch_preview</span><span class="text-slate-300">)</span></div>
                                <!-- Blinking cursor -->
                                <div class="ml-8"><span class="inline-block w-2 h-4 bg-orange-400 animate-blink align-middle"></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Sidebar -->
                    <div class="w-52 bg-surface/60 border-l border-border px-4 py-3 hidden lg:flex flex-col gap-3">
                        <!-- Mode toggle -->
                        <div class="flex gap-1 bg-void/60 rounded-lg p-1">
                            <button class="flex-1 text-[10px] font-semibold py-1 rounded-md text-muted">CHAT</button>
                            <button class="flex-1 text-[10px] font-semibold py-1 rounded-md text-muted">PLAN</button>
                            <button class="flex-1 text-[10px] font-semibold py-1 rounded-md bg-orange-600 text-white shadow-glow-sm">AGENT</button>
                        </div>
                        <!-- Chat bubbles -->
                        <div class="space-y-2 flex-1">
                            <div class="text-[10px] text-muted font-mono bg-void/40 rounded-lg p-2 border border-border">
                                Found 2 potential optimizations in <span class="text-orange-400">main.py</span>...
                            </div>
                            <div class="text-[10px] bg-orange-600/20 border border-orange-500/30 rounded-lg p-2 text-orange-300">
                                Ready to apply patch. Approve?
                            </div>
                        </div>
                        <!-- Approve button -->
                        <button class="w-full text-[10px] font-semibold py-2 rounded-lg bg-orange-600 text-white animate-pulse-glow">
                            ✓ Approve & Apply
                        </button>
                    </div>
                </div>

                <!-- IDE Status bar -->
                <div class="flex items-center justify-between px-4 py-1.5 bg-orange-600/20 border-t border-orange-500/20 text-[10px] font-mono text-orange-400">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        AI Agent Active — AGENT Mode
                    </span>
                    <span>Python 3.11 · UTF-8 · LF</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-0" id="scroll-indicator">
        <span class="text-xs text-muted uppercase tracking-widest">Scroll to explore</span>
        <div class="w-px h-12 bg-gradient-to-b from-orange-500 to-transparent animate-pulse"></div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  STATS STRIP                                                      -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<section class="relative py-12 border-y border-border bg-surface/30">
    <div class="max-w-6xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8">
        <div class="text-center reveal-on-scroll">
            <div class="text-4xl font-black text-white stat-num" data-target="3">0</div>
            <div class="text-sm text-muted mt-1">AI Agent Modes</div>
        </div>
        <div class="text-center reveal-on-scroll">
            <div class="text-4xl font-black text-gradient-brand stat-num" data-target="40">0</div>
            <div class="text-sm text-muted mt-1">Languages Supported</div>
        </div>
        <div class="text-center reveal-on-scroll">
            <div class="text-4xl font-black text-white stat-num" data-target="0">∞</div>
            <div class="text-sm text-muted mt-1">Collaboration Rooms</div>
        </div>
        <div class="text-center reveal-on-scroll">
            <div class="text-4xl font-black text-gradient-cyan stat-num" data-target="100">0</div>
            <div class="text-sm text-muted mt-1">% Real-time Sync</div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  FEATURES SECTION                                                 -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<section id="features" class="py-28 px-6 relative overflow-hidden">
    <div class="orb w-96 h-96 bg-orange-600/10 top-0 right-0"></div>

    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-20 reveal-on-scroll">
            <div class="badge mb-4 mx-auto w-fit">Core Capabilities</div>
            <h2 class="section-title mb-4">
                Built for the
                <span class="text-gradient-brand">next generation</span>
                of developers
            </h2>
            <p class="section-subtitle mx-auto">
                Every component engineered for peak productivity — from the VS Code IDE (code-server) to the sandboxed AI that writes and reviews code autonomously.
            </p>
        </div>

        <!-- Bento grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Feature 1 — Monaco IDE -->
            <div class="feature-card glass-hover p-7 col-span-1 md:col-span-2 lg:col-span-1 reveal-on-scroll">
                <div class="w-12 h-12 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">VS Code IDE (code-server)</h3>
                <p class="text-muted text-sm leading-relaxed">
                    The full VS Code experience running in your browser via code-server. IntelliSense, terminal, extensions, debugging — the real thing.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <span class="text-[11px] px-2.5 py-1 rounded-full bg-white/[0.04] border border-border text-slate-400">IntelliSense</span>
                    <span class="text-[11px] px-2.5 py-1 rounded-full bg-white/[0.04] border border-border text-slate-400">Terminal</span>
                    <span class="text-[11px] px-2.5 py-1 rounded-full bg-white/[0.04] border border-border text-slate-400">Extensions</span>
                </div>
            </div>

            <!-- Feature 2 — Real-time Collab -->
            <div class="feature-card glass-hover p-7 reveal-on-scroll" style="animation-delay:0.1s">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Real-Time Collaboration</h3>
                <p class="text-muted text-sm leading-relaxed">
                    Powered by Laravel Reverb WebSockets. See teammates' cursors live, with presence indicators and instant code sync — zero latency.
                </p>
            </div>

            <!-- Feature 3 — AI Agent -->
            <div class="feature-card glass-hover p-7 reveal-on-scroll" style="animation-delay:0.2s">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">3-Mode AI Agent</h3>
                <p class="text-muted text-sm leading-relaxed">
                    CHAT, PLAN, and AGENT modes. The Agent can autonomously read your codebase, propose a diff, and apply it — all sandboxed and reversible.
                </p>
            </div>

            <!-- Feature 4 — Code Runner -->
            <div class="feature-card glass-hover p-7 reveal-on-scroll" style="animation-delay:0.3s">
                <div class="w-12 h-12 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Sandboxed Code Runner</h3>
                <p class="text-muted text-sm leading-relaxed">
                    Execute code in 40+ languages via the Piston API in a fully isolated sandbox. stdout, stderr, and exit codes returned in real-time.
                </p>
            </div>

            <!-- Feature 5 — RBAC -->
            <div class="feature-card glass-hover p-7 reveal-on-scroll" style="animation-delay:0.4s">
                <div class="w-12 h-12 rounded-xl bg-pink-500/10 border border-pink-500/20 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Role-Based Access</h3>
                <p class="text-muted text-sm leading-relaxed">
                    Admin, Instructor, and Student roles with strict Policies and Gates. Every AI write action is logged with a full diff audit trail.
                </p>
            </div>

            <!-- Feature 6 — Analytics -->
            <div class="feature-card glass-hover p-7 reveal-on-scroll" style="animation-delay:0.5s">
                <div class="w-12 h-12 rounded-xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Admin Analytics</h3>
                <p class="text-muted text-sm leading-relaxed">
                    Dark-mode ApexCharts dashboards tracking active users, code submissions, and AI patch approval rates with staggered entrance animations.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  AI AGENT SECTION                                                 -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<section id="ai-agent" class="py-28 px-6 relative overflow-hidden">
    <div class="orb w-[500px] h-[500px] bg-orange-600/10 bottom-0 left-[-100px]"></div>

    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16">

        <!-- Left: Text -->
        <div class="flex-1 reveal-on-scroll">
            <div class="badge mb-5">AI Agent Engine</div>
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
                Your codebase's<br>
                <span class="text-gradient-brand">autonomous co-pilot</span>
            </h2>
            <p class="text-slate-400 text-lg mb-8 leading-relaxed">
                Not a chatbot. A full agentic system that explores your files, runs searches, proposes patches with a diff viewer, and awaits your approval before writing a single byte.
            </p>

            <!-- Mode cards -->
            <div class="space-y-3">
                <div class="flex items-start gap-4 p-4 rounded-xl bg-white/[0.03] border border-white/[0.06] hover:border-orange-500/30 transition-colors duration-200">
                    <div class="w-8 h-8 rounded-lg bg-slate-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-xs font-bold text-slate-300">C</span>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-white mb-1">CHAT Mode — Read Only</div>
                        <div class="text-xs text-muted">Explain code, search references, debug guidance. Zero file access.</div>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 rounded-xl bg-white/[0.03] border border-white/[0.06] hover:border-amber-500/30 transition-colors duration-200">
                    <div class="w-8 h-8 rounded-lg bg-amber-600/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-xs font-bold text-amber-400">P</span>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-white mb-1">PLAN Mode — Read + Plan</div>
                        <div class="text-xs text-muted">Analyze architecture, list affected files, produce step-by-step execution plan.</div>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 rounded-xl bg-orange-500/[0.08] border border-orange-500/25 hover:border-orange-500/50 transition-colors duration-200">
                    <div class="w-8 h-8 rounded-lg bg-orange-600 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-glow-sm">
                        <span class="text-xs font-bold text-white">A</span>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-orange-300 mb-1">AGENT Mode — Sandboxed R/W</div>
                        <div class="text-xs text-muted">Propose diffs, await approval, apply patches. Every action logged and reversible.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Diff Viewer mockup -->
        <div class="flex-1 w-full reveal-on-scroll">
            <div class="glass border border-border rounded-2xl overflow-hidden shadow-[0_40px_80px_rgba(0,0,0,0.7)]">
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-3.5 bg-surface/80 border-b border-border">
                    <div class="flex items-center gap-2 text-sm font-medium text-slate-300">
                        <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Patch Preview — <span class="text-orange-400 font-mono text-xs ml-1">utils.py</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-muted">
                        <span class="text-red-400">-3</span>
                        <span class="text-emerald-400">+7</span>
                    </div>
                </div>

                <!-- Diff -->
                <div class="font-mono text-xs leading-relaxed overflow-x-auto bg-void/60">
                    <div class="px-5 py-4 space-y-0.5">
                        <div class="text-muted py-0.5">  @@ -12,8 +12,14 @@ def process_data(items):</div>
                        <div class="text-slate-400 py-0.5">      result = []</div>
                        <div class="py-0.5 bg-red-500/10 border-l-2 border-red-500 pl-3 -mx-5 px-5 text-red-400">
                            -     for item in items:
                        </div>
                        <div class="py-0.5 bg-red-500/10 border-l-2 border-red-500 pl-3 -mx-5 px-5 text-red-400">
                            -         result.append(item * 2)
                        </div>
                        <div class="py-0.5 bg-red-500/10 border-l-2 border-red-500 pl-3 -mx-5 px-5 text-red-400">
                            -     return result
                        </div>
                        <div class="py-0.5 bg-emerald-500/10 border-l-2 border-emerald-500 pl-3 -mx-5 px-5 text-emerald-400">
                            +     if not items:
                        </div>
                        <div class="py-0.5 bg-emerald-500/10 border-l-2 border-emerald-500 pl-3 -mx-5 px-5 text-emerald-400">
                            +         return []
                        </div>
                        <div class="py-0.5 bg-emerald-500/10 border-l-2 border-emerald-500 pl-3 -mx-5 px-5 text-emerald-400">
                            +     return [item * 2 for item in items if item is not None]
                        </div>
                        <div class="text-slate-400 py-0.5">  </div>
                        <div class="py-0.5 bg-emerald-500/10 border-l-2 border-emerald-500 pl-3 -mx-5 px-5 text-emerald-400">
                            + def validate_item(item):
                        </div>
                        <div class="py-0.5 bg-emerald-500/10 border-l-2 border-emerald-500 pl-3 -mx-5 px-5 text-emerald-400">
                            +     <span class="text-orange-400">"""Validate item is numeric."""</span>
                        </div>
                        <div class="py-0.5 bg-emerald-500/10 border-l-2 border-emerald-500 pl-3 -mx-5 px-5 text-emerald-400">
                            +     return isinstance(item, (int, float))
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex gap-3 px-5 py-4 bg-surface/60 border-t border-border">
                    <button class="btn-glow flex-1 justify-center py-2.5 text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve & Apply
                    </button>
                    <button class="btn-ghost py-2.5 px-5 text-sm">
                        Reject
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  COLLABORATION SECTION                                            -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<section id="collaboration" class="py-28 px-6 bg-surface/20 relative overflow-hidden">
    <div class="orb w-96 h-96 bg-amber-500/10 top-0 right-0"></div>

    <div class="max-w-6xl mx-auto text-center">
        <div class="badge mb-5 mx-auto w-fit" style="background:rgba(245,158,11,0.1);color:#22d3ee;border-color:rgba(245,158,11,0.2)">
            Real-Time Collaboration
        </div>
        <h2 class="section-title mb-5 reveal-on-scroll">
            Code together,
            <span class="text-gradient-cyan">anywhere</span>
        </h2>
        <p class="section-subtitle mx-auto mb-16 reveal-on-scroll">
            Laravel Reverb powers instant WebSocket presence channels. Watch teammates' cursors move in real-time. Get notified when anyone joins or leaves.
        </p>

        <!-- Collab feature cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-hover p-6 text-left reveal-on-scroll">
                <div class="flex -space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-orange-500 border-2 border-elevated flex items-center justify-center text-sm font-bold">A</div>
                    <div class="w-10 h-10 rounded-full bg-amber-500 border-2 border-elevated flex items-center justify-center text-sm font-bold">J</div>
                    <div class="w-10 h-10 rounded-full bg-emerald-500 border-2 border-elevated flex items-center justify-center text-sm font-bold">S</div>
                    <div class="w-10 h-10 rounded-full bg-border border-2 border-elevated flex items-center justify-center text-xs text-muted">+8</div>
                </div>
                <h3 class="text-base font-semibold text-white mb-2">Live Presence</h3>
                <p class="text-sm text-muted">Glowing glassmorphic avatars appear in the IDE topbar, showing who's active in your room right now.</p>
            </div>
            <div class="glass-hover p-6 text-left reveal-on-scroll" style="animation-delay:0.15s">
                <div class="mb-4 font-mono text-xs bg-void/60 rounded-lg p-3 border border-border">
                    <span class="text-orange-400">|</span> <span class="text-orange-300 text-[10px]">Alex</span>
                    <span class="text-slate-400 block mt-1">    result = sorted(items)</span>
                    <span class="text-amber-400">|</span> <span class="text-amber-300 text-[10px]">Jane</span>
                </div>
                <h3 class="text-base font-semibold text-white mb-2">Multi-Cursor Sync</h3>
                <p class="text-sm text-muted">Each collaborator's cursor renders in the IDE with a colored flag showing their name — zero lag.</p>
            </div>
            <div class="glass-hover p-6 text-left reveal-on-scroll" style="animation-delay:0.3s">
                <div class="mb-4 space-y-2">
                    <div class="toast pointer-events-none">
                        <div class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></div>
                        <span class="text-emerald-400 font-medium text-xs">Alex</span>
                        <span class="text-muted text-xs">joined the workspace</span>
                    </div>
                    <div class="toast pointer-events-none opacity-60">
                        <div class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></div>
                        <span class="text-red-400 font-medium text-xs">Jane</span>
                        <span class="text-muted text-xs">left the workspace</span>
                    </div>
                </div>
                <h3 class="text-base font-semibold text-white mb-2">Smart Toasts</h3>
                <p class="text-sm text-muted">Sleek dark notifications slide in whenever someone joins or leaves — never intrusive, always informative.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  CTA SECTION                                                      -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<section class="py-28 px-6 relative overflow-hidden">
    <div class="absolute inset-0 mesh-bg opacity-70 pointer-events-none"></div>
    <div class="orb w-[600px] h-[600px] bg-orange-600/15 top-[-200px] left-1/2 -translate-x-1/2"></div>

    <div class="relative z-10 max-w-3xl mx-auto text-center reveal-on-scroll">
        <h2 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight">
            Ready to experience the<br>
            <span class="text-gradient-brand">future of coding?</span>
        </h2>
        <p class="text-slate-400 text-lg mb-10">
            Join VisionLab and unlock your workspace with a full VS Code IDE, real-time collaboration, and your personal AI development agent.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-glow px-10 py-4 text-base">
                    Open My Workspace
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            @else
                <a href="{{ route('register') }}" class="btn-glow px-10 py-4 text-base">
                    Create Free Account
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="{{ route('login') }}" class="btn-ghost px-10 py-4 text-base">Sign In</a>
            @endauth
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  FOOTER                                                           -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<footer class="border-t border-border py-10 px-6">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-orange-600 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-white">VisionLab</span>
        </div>
        <p class="text-xs text-muted text-center">
            Built for Aptech Vision 2026 · Laravel {{ app()->version() }} · PHP {{ PHP_VERSION }}
        </p>
        <div class="flex items-center gap-6 text-xs text-muted">
            <a href="{{ route('demo') }}" class="hover:text-slate-300 transition-colors">Demo Guide</a>
            <a href="{{ route('login') }}" class="hover:text-slate-300 transition-colors">Sign In</a>
        </div>
    </div>
</footer>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  JAVASCRIPT                                                       -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── 1. Navbar scroll effect ──────────────────────────────────────
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 40) {
            navbar.style.background = 'rgba(10,10,10,0.85)';
            navbar.style.backdropFilter = 'blur(16px)';
            navbar.style.borderBottom = '1px solid rgba(255,255,255,0.06)';
        } else {
            navbar.style.background = 'transparent';
            navbar.style.backdropFilter = 'none';
            navbar.style.borderBottom = 'none';
        }
    }, { passive: true });

    // ── 2. Hero word-reveal animation ────────────────────────────────
    const words = document.querySelectorAll('.word');
    words.forEach((w, i) => {
        setTimeout(() => {
            w.style.transition = 'opacity 0.5s ease, transform 0.5s cubic-bezier(0.16,1,0.3,1)';
            w.style.opacity = '1';
            w.style.transform = 'translateY(0)';
        }, 200 + i * 80);
    });

    // Fade in other hero elements
    const heroElements = [
        { el: document.getElementById('hero-badge'),      delay: 100 },
        { el: document.getElementById('hero-sub'),        delay: 600 },
        { el: document.getElementById('hero-ctas'),       delay: 750 },
        { el: document.getElementById('hero-ide-card'),   delay: 900 },
        { el: document.getElementById('scroll-indicator'),delay: 1400 },
    ];
    heroElements.forEach(({ el, delay }) => {
        if (!el) return;
        setTimeout(() => {
            el.style.transition = 'opacity 0.7s ease, transform 0.7s cubic-bezier(0.16,1,0.3,1)';
            el.style.opacity = '1';
            if (el.id === 'hero-ide-card') {
                el.style.transform = 'translateY(0)';
                el.style.transform = 'perspective(1000px) rotateX(0deg)';
            }
        }, delay);
    });

    // ── 3. Particle canvas ───────────────────────────────────────────
    const canvas = document.getElementById('particle-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let particles = [];
        let animId;

        function resizeCanvas() {
            canvas.width  = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas, { passive: true });

        const PARTICLE_COUNT = 60;
        for (let i = 0; i < PARTICLE_COUNT; i++) {
            particles.push({
                x: Math.random() * window.innerWidth,
                y: Math.random() * window.innerHeight,
                r: Math.random() * 1.5 + 0.3,
                dx: (Math.random() - 0.5) * 0.3,
                dy: (Math.random() - 0.5) * 0.3,
                alpha: Math.random() * 0.6 + 0.1,
                color: Math.random() > 0.5 ? '124,58,237' : '6,182,212',
            });
        }

        function drawParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${p.color},${p.alpha})`;
                ctx.fill();
                p.x += p.dx;
                p.y += p.dy;
                if (p.x < 0 || p.x > canvas.width)  p.dx *= -1;
                if (p.y < 0 || p.y > canvas.height)  p.dy *= -1;
            });
            animId = requestAnimationFrame(drawParticles);
        }
        drawParticles();
    }

    // ── 4. Intersection Observer — reveal on scroll ───────────────────
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                el.style.transition = 'opacity 0.6s ease, transform 0.6s cubic-bezier(0.16,1,0.3,1)';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
                revealObserver.unobserve(el);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal-on-scroll').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(28px)';
        revealObserver.observe(el);
    });

    // ── 5. Stats counter animation ───────────────────────────────────
    const statObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.dataset.target);
                if (isNaN(target)) return;
                let current = 0;
                const step = Math.ceil(target / 40);
                const timer = setInterval(() => {
                    current = Math.min(current + step, target);
                    el.textContent = current + (el.dataset.suffix || '');
                    if (current >= target) clearInterval(timer);
                }, 30);
                statObserver.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-num[data-target]').forEach(el => {
        statObserver.observe(el);
    });

    // ── 6. 3D Tilt effect on IDE hero card ──────────────────────────
    const ideCard = document.getElementById('hero-ide-card');
    if (ideCard) {
        ideCard.addEventListener('mousemove', (e) => {
            const rect = ideCard.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width  - 0.5;
            const y = (e.clientY - rect.top)  / rect.height - 0.5;
            ideCard.style.transform = `perspective(1200px) rotateY(${x * 6}deg) rotateX(${-y * 4}deg)`;
        });
        ideCard.addEventListener('mouseleave', () => {
            ideCard.style.transform = 'perspective(1200px) rotateY(0deg) rotateX(0deg)';
            ideCard.style.transition = 'transform 0.5s cubic-bezier(0.16,1,0.3,1)';
        });
    }

    // ── 7. Feature card tilt ─────────────────────────────────────────
    document.querySelectorAll('.feature-card').forEach(card => {
        card.style.willChange = 'transform';
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width  - 0.5;
            const y = (e.clientY - rect.top)  / rect.height - 0.5;
            card.style.transform = `perspective(800px) rotateY(${x * 8}deg) rotateX(${-y * 5}deg) translateY(-4px)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
});
</script>

</body>
</html>

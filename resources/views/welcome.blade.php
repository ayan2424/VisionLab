<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="The collaborative IDE built for research universities. Sandboxed, audited, and AI-assisted.">
    <title>VisionLab — Collaborative coding for universities</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Three.js via Import Map for ES Modules -->
    <script type="importmap">
        {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.164.1/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.164.1/examples/jsm/"
        }
    }
    </script>
    <script type="module" src="https://unpkg.com/@splinetool/viewer@1.12.97/build/spline-viewer.js"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Three.js Global -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <!-- Theme Check Script -->
    <script>
        (function() {
            var t = localStorage.getItem('vc-theme');
            if (t === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <style>
        /* ═══════════════════════════════════════════════════════════════════
           DESIGN SYSTEM — 100% Lovable Reference Colors + Fonts
           ═══════════════════════════════════════════════════════════════════ */
        /* ── Light Mode ───────────────────────────────────────────────────────────── */
        :root {
            --background: #fafaf8;
            --foreground: #1a1714;
            --surface: rgba(0, 0, 0, 0.03);
            --surface-2: rgba(0, 0, 0, 0.06);
            --muted: rgba(0, 0, 0, 0.04);
            --muted-foreground: rgba(26, 23, 20, 0.6);
            --border: rgba(0, 0, 0, 0.08);
            --line: rgba(0, 0, 0, 0.05);
            /* Brand Colors (Static across themes) */
            --indigo: #4f46e5;
            --indigo-light: #818cf8;
            --rose: #f0426d;
            --rose-light: #fb7c97;
            --cyan: #17c3d6;
            --cyan-light: #6ee7e0;
            --emerald: #00bfa6;
            --emerald-light: #5eeac9;
            --violet: #9b5de5;
            --violet-light: #c7a6f5;
            --ease-out-expo: cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* ── Dark Mode ────────────────────────────────────────────────────────────── */
        @media (prefers-color-scheme: dark) {
            :root {
                --background: #050507;
                --foreground: #f4f4f5;
                --surface: #ffffff08;
                --surface-2: #ffffff0f;
                --muted: #ffffff0a;
                --muted-foreground: #f4f4f58c;
                --border: #ffffff14;
                --line: #ffffff0d;
            }
        }
        html.dark :root {
            --background: #050507;
            --foreground: #f4f4f5;
            --surface: #ffffff08;
            --surface-2: #ffffff0f;
            --muted: #ffffff0a;
            --muted-foreground: #f4f4f58c;
            --border: #ffffff14;
            --line: #ffffff0d;
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
            /* Animated gradient background */
            background-image: linear-gradient(-45deg, #fafaf8, #f3f1ed, #ffffff, #fafaf8);
            background-size: 400% 400%;
            animation: gradientBg 18s ease infinite;
        }

        html.dark body {
            background-image: linear-gradient(-45deg, #050507, #0b0716, #050a12, #050507);
        }

        @keyframes gradientBg {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
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

        /* ═══════════ TEXT EFFECTS ═══════════ */
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

        /* ═══════════ GLASS PANEL ═══════════ */
        .glass-panel {
            backdrop-filter: blur(14px) saturate(140%);
            background: linear-gradient(rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.016) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: rgba(255, 255, 255, 0.04) 0px 1px inset, rgba(0, 0, 0, 0.6) 0px 30px 80px -30px;
        }

        /* ═══════════ GRID FLOOR ═══════════ */
        .grid-floor {
            background-image: linear-gradient(90deg, rgba(23, 195, 214, 0.15) 1px, rgba(0, 0, 0, 0) 1px),
                linear-gradient(rgba(23, 195, 214, 0.15) 1px, rgba(0, 0, 0, 0) 1px);
            background-size: 60px 60px;
        }

        /* ═══════════ ANIMATIONS ═══════════ */
        @keyframes aurora {

            0%,
            100% {
                background-position: 0% center;
            }

            50% {
                background-position: 100% center;
            }
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }

        @keyframes float-y {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes scanline {
            0% {
                transform: translate(-100%);
            }

            100% {
                transform: translate(100%);
            }
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.4;
                transform: scale(1.4);
            }
        }

        @keyframes reveal {
            0% {
                opacity: 0;
                filter: blur(6px);
                transform: translateY(24px);
            }

            100% {
                opacity: 1;
                filter: blur(0px);
                transform: translateY(0px);
            }
        }

        @keyframes spin-y {
            0% {
                transform: rotateY(0deg) rotateX(-25deg);
            }

            100% {
                transform: rotateY(360deg) rotateX(-25deg);
            }
        }

        @keyframes spin-soft {
            0% {
                transform: rotateY(-15deg);
            }

            100% {
                transform: rotateY(25deg);
            }
        }

        @keyframes wave-bar {

            0%,
            100% {
                transform: scaleY(0.3);
            }

            50% {
                transform: scaleY(1);
            }
        }

        /* ═══════════ REVEAL SCROLL ═══════════ */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.9s var(--ease-out-expo), transform 0.9s var(--ease-out-expo);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0px);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--background);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
    </style>
</head>

<body class="selection:bg-cyan/30 selection:text-white">

    <!-- ═══════════════════════════════════════════════════════════════════
     NAVIGATION
     ═══════════════════════════════════════════════════════════════════ -->
    <x-frontend-header />

    <!-- ═══════════════════════════════════════════════════════════════════
     HERO SECTION
     ═══════════════════════════════════════════════════════════════════ -->
    <section id="top" class="relative overflow-hidden pt-40 pb-24 min-h-screen flex flex-col justify-center">
        <!-- Perspective Cyber Grid Floor -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute left-1/2 h-[140%] w-[200%] -translate-x-1/2 grid-floor opacity-[0.45]" style="top: 30%; transform: translateX(-50%) perspective(900px) rotateX(65deg); transform-origin: center top; mask-image: linear-gradient(transparent 5%, black 50%, transparent 100%);"></div>
            <div class="absolute inset-x-0 h-40" style="bottom: 0px; background: linear-gradient(to top, var(--background), transparent);"></div>
        </div>

        <!-- Background Radial Glows -->
        <div aria-hidden="true" class="pointer-events-none absolute left-1/2 top-1/3 h-[500px] w-[800px] -translate-x-1/2 -translate-y-1/2 rounded-full opacity-40 blur-[120px]" style="background: radial-gradient(circle, color-mix(in srgb, var(--violet) 18%, transparent), transparent 60%);"></div>
        <div aria-hidden="true" class="pointer-events-none absolute right-10 top-24 h-[400px] w-[500px] rounded-full opacity-35 blur-[100px]" style="background: radial-gradient(circle, color-mix(in srgb, var(--cyan) 22%, transparent), transparent 60%);"></div>

        <!-- WebGL Canvas Container for 3D Rotating Globe -->
        <div id="heroGlobeCanvas" class="absolute inset-0 z-0 pointer-events-auto opacity-70" style="mix-blend-mode: screen;"></div>

        <div class="relative mx-auto max-w-6xl px-6 text-center z-10">
            <div class="reveal" style="transition-delay: 0ms;">
                <div class="mb-10 inline-flex items-center gap-2.5 rounded-full border border-border bg-surface px-3 py-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald opacity-60"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald"></span>
                    </span>
                    <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground">Kernel v4.0.1 · live across 38 universities</span>
                </div>
            </div>

            <div class="reveal" style="transition-delay: 100ms;">
                <h1 class="mx-auto max-w-5xl font-display text-[clamp(2.75rem,8vw,7.5rem)] font-semibold leading-[0.92] tracking-tight">
                    <span class="metallic-text">Code the </span>
                    <span class="aurora-text font-serif-italic">future</span>
                    <span class="metallic-text"> of</span><br>
                    <span class="metallic-text">higher learning.</span>
                </h1>
            </div>

            <div class="reveal" style="transition-delay: 200ms;">
                <p class="mx-auto mt-8 max-w-xl text-pretty text-base leading-relaxed text-muted-foreground sm:text-lg">
                    Sandboxed workspaces, real-time multi-cursor collaboration, and responsible AI assistance — engineered for research universities.
                </p>
            </div>

            <div class="reveal" style="transition-delay: 300ms;">
                <div class="mt-12 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    @auth
                    <a href="{{ route('dashboard') }}" class="relative inline-flex items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-medium tracking-wide transition-[background,color,box-shadow] duration-300 bg-cyan text-background shadow-[0_0_0_1px_rgba(23,195,214,0.5),0_20px_60px_-20px_rgba(23,195,214,0.55)] hover:bg-cyan-light hover:shadow-[0_0_0_1px_rgba(110,231,224,0.7),0_30px_80px_-20px_rgba(23,195,214,0.7)] text-decoration-none">
                        Dashboard
                        <span class="font-mono text-[10px] opacity-70">↗</span>
                    </a>
                    @else
                    <a href="{{ route('register') }}" class="relative inline-flex items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-medium tracking-wide transition-[background,color,box-shadow] duration-300 bg-cyan text-background shadow-[0_0_0_1px_rgba(23,195,214,0.5),0_20px_60px_-20px_rgba(23,195,214,0.55)] hover:bg-cyan-light hover:shadow-[0_0_0_1px_rgba(110,231,224,0.7),0_30px_80px_-20px_rgba(23,195,214,0.7)] text-decoration-none">
                        Start Building
                        <span class="font-mono text-[10px] opacity-70">↗</span>
                    </a>
                    @endauth
                    <a href="{{ route('demo') }}" class="relative inline-flex items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-medium tracking-wide transition-[background,color,box-shadow] duration-300 border border-white/15 bg-white/[0.02] text-foreground hover:bg-white/[0.06] hover:border-white/30 text-decoration-none">
                        Faculty Demo
                    </a>
                </div>
            </div>

            <div class="reveal" style="transition-delay: 400ms;">
                <div class="mt-10 flex items-center justify-center gap-6 font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground">
                    <span class="flex items-center gap-2"><span class="h-1 w-1 rounded-full bg-emerald"></span> FERPA</span>
                    <span class="h-3 w-px bg-border"></span>
                    <span class="flex items-center gap-2"><span class="h-1 w-1 rounded-full bg-cyan"></span> SOC2 Type II</span>
                    <span class="h-3 w-px bg-border"></span>
                    <span class="flex items-center gap-2"><span class="h-1 w-1 rounded-full bg-violet"></span> GDPR</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════════
     WORKSPACE SHOWCASE
     ═══════════════════════════════════════════════════════════════════ -->
    <section id="workspace" class="relative py-32 overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse at center, color-mix(in srgb, var(--indigo) 8%, transparent), transparent 70%);"></div>
        <div class="relative mx-auto max-w-6xl px-6">
            <div class="reveal" style="transition-delay: 0ms;">
                <div class="mb-16 flex flex-col items-start gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <span class="font-mono text-[10px] uppercase tracking-[0.3em] text-cyan">/ 01 · workspace</span>
                        <h2 class="mt-3 max-w-xl font-display text-4xl font-semibold tracking-tight md:text-5xl">
                            Real-time, <span class="font-serif-italic text-cyan-light">in-browser</span> IDE — built for the cohort.
                        </h2>
                    </div>
                    <p class="max-w-sm text-sm text-muted-foreground">
                        Every student, instructor, and TA shares one cursor-aware environment. No setup, no drift, just code.
                    </p>
                </div>
            </div>

            <div class="relative" style="perspective: 1800px;">
                <div class="relative tilt-3d" style="transform: rotateX(8deg) rotateY(-4deg); transform-style: preserve-3d; transition: transform 600ms var(--ease-out-expo);">

                    <!-- Floating Neural Assistant Mock -->
                    <div class="absolute -right-6 -top-10 z-0 hidden w-64 lg:block" style="transform: translateZ(60px); animation: float-y 7s ease-in-out infinite;">
                        <div class="glass-panel rounded-xl p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <div class="h-1.5 w-1.5 rounded-full bg-violet"></div>
                                <span class="font-mono text-[9px] uppercase tracking-[0.22em] text-violet-light">Neural Assistant</span>
                            </div>
                            <div class="space-y-2">
                                <div class="h-1.5 w-full rounded-full bg-white/10"></div>
                                <div class="h-1.5 w-4/5 rounded-full bg-white/10"></div>
                                <div class="h-1.5 w-1/2 rounded-full bg-violet/40"></div>
                            </div>
                            <div class="mt-4 flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 animate-[pulse-dot_1.2s_ease-in-out_infinite] rounded-full bg-violet"></span>
                                <span class="h-1.5 w-1.5 animate-[pulse-dot_1.2s_ease-in-out_infinite] rounded-full bg-violet" style="animation-delay: 0.2s;"></span>
                                <span class="h-1.5 w-1.5 animate-[pulse-dot_1.2s_ease-in-out_infinite] rounded-full bg-violet" style="animation-delay: 0.4s;"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Main IDE Mock Window -->
                    <div class="glass-panel relative z-10 overflow-hidden rounded-2xl">
                        <div class="relative flex items-center justify-between border-b border-border bg-white/[0.02] px-4 py-3">
                            <div class="flex gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-rose/60"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-white/20"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald/60"></span>
                            </div>
                            <div class="font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground">visionlab · workspace-core / architecture.ts</div>
                            <div class="flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald"></span>
                                <span class="font-mono text-[9px] uppercase tracking-[0.22em] text-emerald-light">live</span>
                            </div>
                            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px overflow-hidden">
                                <div class="h-full w-1/3 bg-gradient-to-r from-transparent via-cyan to-transparent" style="animation: scanline 4s linear infinite;"></div>
                            </div>
                        </div>

                        <div class="grid min-h-[460px] grid-cols-[180px_1fr] md:min-h-[520px]">
                            <!-- Sidebar Explorer -->
                            <aside class="border-r border-border bg-black/30 p-4 font-mono text-[11px]">
                                <div class="mb-3 text-[9px] uppercase tracking-[0.22em] text-muted-foreground">Workspace</div>
                                <ul class="space-y-1.5 list-none">
                                    <li class="text-muted-foreground">📁 src/</li>
                                    <li class="pl-4 text-cyan-light">↳ architecture.ts</li>
                                    <li class="pl-4 text-muted-foreground/70">↳ QuantumCore.tsx</li>
                                    <li class="pl-4 text-muted-foreground/70">↳ ComputeEngine.rs</li>
                                    <li class="text-muted-foreground">📁 tests/</li>
                                    <li class="text-muted-foreground">📄 README.md</li>
                                </ul>
                                <div class="mt-8 text-[9px] uppercase tracking-[0.22em] text-muted-foreground">Cohort · 24 live</div>
                                <ul class="mt-3 space-y-2 list-none">
                                    <li class="flex items-center gap-2">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose"></span>
                                        <span class="text-muted-foreground">Prof. Aris</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="h-1.5 w-1.5 rounded-full bg-indigo"></span>
                                        <span class="text-muted-foreground">Marcus V.</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald"></span>
                                        <span class="text-muted-foreground">Alina K.</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="h-1.5 w-1.5 rounded-full bg-violet"></span>
                                        <span class="text-muted-foreground">Sana D.</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="h-1.5 w-1.5 rounded-full bg-cyan"></span>
                                        <span class="text-muted-foreground">+ 20 more</span>
                                    </li>
                                </ul>
                            </aside>

                            <!-- Code Editor -->
                            <div class="relative p-6 font-mono text-[13px] leading-7">
                                <div class="flex gap-5"><span class="select-none text-muted-foreground/40">01</span><span><span class="text-violet-light">import</span> <span class="text-foreground">{ Sandbox }</span> <span class="text-violet-light">from</span> <span class="text-emerald-light">"@vision/lab"</span>;</span></div>
                                <div class="flex gap-5"><span class="select-none text-muted-foreground/40">02</span><span><span class="text-muted-foreground">// initialize a student session</span></span></div>
                                <div class="flex gap-5"><span class="select-none text-muted-foreground/40">03</span><span><span class="text-violet-light">export const</span> <span class="text-cyan-light">session</span> = <span class="text-violet-light">async</span> () =&gt; {</span></div>
                                <div class="flex gap-5">
                                    <span class="select-none text-muted-foreground/40">04</span>
                                    <span>
                                        <span class="relative">
                                            <span class="text-foreground"> await runtime.initialize({ sandboxed: true, region: "us-east-1" });</span>
                                            <span class="ml-0.5 inline-block h-4 w-[2px] translate-y-0.5 bg-cyan align-middle" style="animation: blink 1s steps(1) infinite;"></span>
                                            <!-- User Cursor Tag Mock -->
                                            <span class="pointer-events-none absolute -top-5 left-[120px] flex flex-col items-start">
                                                <span class="rounded-sm px-1.5 py-0.5 text-[9px] font-semibold uppercase tracking-wider text-white bg-rose">Prof. Aris</span>
                                                <span class="h-5 w-[2px] bg-rose" style="animation: blink 1s steps(1) infinite;"></span>
                                            </span>
                                        </span>
                                    </span>
                                </div>
                                <div class="flex gap-5"><span class="select-none text-muted-foreground/40">05</span><span><span class="text-foreground"> return </span><span class="text-cyan-light">Sandbox</span><span class="text-foreground">.deploy({ runtime: </span><span class="text-emerald-light">"node-20"</span><span class="text-foreground"> });</span></span></div>
                                <div class="flex gap-5">
                                    <span class="select-none text-muted-foreground/40">06</span>
                                    <span>
                                        <span class="relative text-foreground">}
                                            <!-- Another User Cursor Tag Mock -->
                                            <span class="pointer-events-none absolute -bottom-5 left-6 flex flex-col items-start">
                                                <span class="h-5 w-[2px] bg-emerald" style="animation: blink 1s steps(1) infinite;"></span>
                                                <span class="rounded-sm px-1.5 py-0.5 text-[9px] font-semibold uppercase tracking-wider text-background bg-emerald">Alina K.</span>
                                            </span>
                                        </span>
                                    </span>
                                </div>

                                <!-- Terminal Mock -->
                                <div class="mt-12 rounded-lg border border-border bg-black/40 p-4">
                                    <div class="mb-2 flex items-center gap-2 font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground">
                                        <span>terminal</span>
                                        <span class="opacity-50">/ bash</span>
                                        <span class="ml-auto flex items-center gap-1">
                                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-cyan"></span>
                                            12ms
                                        </span>
                                    </div>
                                    <div class="space-y-1 text-[12px]">
                                        <div><span class="text-emerald-light">vlab</span> <span class="text-muted-foreground">deploy --cohort cs101</span></div>
                                        <div class="text-muted-foreground">✓ provisioned <span class="text-cyan-light">24 sandboxes</span> in 1.2s</div>
                                        <div class="text-muted-foreground">↺ syncing cursors · <span class="text-violet-light">5 peers</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Bottom Status Bar -->
                    <div class="absolute -bottom-8 left-1/2 z-20 w-[min(560px,92%)] -translate-x-1/2" style="transform: translate(-50%, 0px) translateZ(110px);">
                        <div class="glass-panel flex items-center justify-between rounded-full px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex -space-x-2">
                                    <span class="h-7 w-7 rounded-full border-2 border-background bg-rose"></span>
                                    <span class="h-7 w-7 rounded-full border-2 border-background bg-indigo"></span>
                                    <span class="h-7 w-7 rounded-full border-2 border-background bg-emerald"></span>
                                    <span class="h-7 w-7 rounded-full border-2 border-background bg-violet"></span>
                                    <span class="grid h-7 w-7 place-items-center rounded-full border-2 border-background bg-white/10 text-[10px] font-semibold text-foreground">+20</span>
                                </div>
                                <span class="hidden font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground sm:inline">cohort active</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="grid h-8 w-8 place-items-center rounded-full border border-border bg-white/[0.04] text-emerald-light transition-colors hover:bg-emerald/15">
                                    <span class="block h-2 w-2 rotate-45 bg-current"></span>
                                </button>
                                @auth
                                <a href="{{ route('dashboard') }}" class="rounded-full bg-cyan px-4 py-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.22em] text-background transition-colors hover:bg-cyan-light text-decoration-none">Sync</a>
                                @else
                                <a href="{{ route('register') }}" class="rounded-full bg-cyan px-4 py-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.22em] text-background transition-colors hover:bg-cyan-light text-decoration-none">Sync</a>
                                @endauth
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════════
     FEATURES SECTION
     ═══════════════════════════════════════════════════════════════════ -->
    <section id="features" class="relative py-32 overflow-hidden">
        <div class="mx-auto max-w-7xl px-6">
            <div class="reveal">
                <div class="mb-16 max-w-2xl">
                    <span class="font-mono text-[10px] uppercase tracking-[0.3em] text-violet-light">/ 02 · the kernel</span>
                    <h2 class="mt-3 font-display text-4xl font-semibold tracking-tight md:text-5xl">
                        Five primitives. <span class="font-serif-italic text-muted-foreground">One platform.</span>
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                <!-- Feature 1 -->
                <div class="reveal md:col-span-3 lg:col-span-2">
                    <div class="group relative overflow-hidden rounded-2xl border border-border bg-surface p-7 transition-colors duration-300 hover:border-white/20 h-full cursor-pointer" style="--spot-color: var(--cyan);">
                        <div class="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-500 group-hover:opacity-100" style="background: radial-gradient(420px circle at var(--mx, 50%) var(--my, 50%), color-mix(in srgb, var(--spot-color) 22%, transparent), transparent 60%);"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px opacity-60 bg-gradient-to-r from-transparent via-cyan to-transparent"></div>
                        <div class="relative">
                            <div class="flex h-full min-h-[280px] flex-col">
                                <div class="flex items-start justify-between">
                                    <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-cyan">01 //</span>
                                    <div class="relative h-16 w-16 shrink-0" style="perspective: 600px;">
                                        <div class="absolute inset-0 rounded-full opacity-60 blur-xl bg-cyan"></div>
                                        <div class="relative grid h-full w-full place-items-center" style="transform-style: preserve-3d; animation: float-y 5s ease-in-out infinite;">
                                            <div class="relative h-9 w-9" style="transform-style: preserve-3d; transform: rotateX(-25deg) rotateY(35deg); animation: spin-y 8s linear infinite;">
                                                <div class="absolute inset-0 border bg-gradient-to-tr from-cyan/40 to-transparent border-cyan" style="transform: translateZ(18px); box-shadow: 0 0 12px var(--cyan);"></div>
                                                <div class="absolute inset-0 border bg-gradient-to-tr from-cyan/40 to-transparent border-cyan" style="transform: rotateY(180deg) translateZ(18px); box-shadow: 0 0 12px var(--cyan);"></div>
                                                <div class="absolute inset-0 border bg-gradient-to-tr from-cyan/40 to-transparent border-cyan" style="transform: rotateY(90deg) translateZ(18px); box-shadow: 0 0 12px var(--cyan);"></div>
                                                <div class="absolute inset-0 border bg-gradient-to-tr from-cyan/40 to-transparent border-cyan" style="transform: rotateY(-90deg) translateZ(18px); box-shadow: 0 0 12px var(--cyan);"></div>
                                                <div class="absolute inset-0 border bg-gradient-to-tr from-cyan/40 to-transparent border-cyan" style="transform: rotateX(90deg) translateZ(18px); box-shadow: 0 0 12px var(--cyan);"></div>
                                                <div class="absolute inset-0 border bg-gradient-to-tr from-cyan/40 to-transparent border-cyan" style="transform: rotateX(-90deg) translateZ(18px); box-shadow: 0 0 12px var(--cyan);"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="mt-6 font-display text-2xl font-semibold tracking-tight">Sandboxed Nodes</h3>
                                <p class="mt-3 text-sm leading-relaxed text-muted-foreground">Isolated, ephemeral containers spin up per student in under a second. Zero local config.</p>
                                <div class="mt-auto pt-8">
                                    <div class="h-px w-full bg-gradient-to-r from-transparent via-cyan to-transparent opacity-40"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="reveal md:col-span-3 lg:col-span-2 lg:translate-y-8">
                    <div class="group relative overflow-hidden rounded-2xl border border-border bg-surface p-7 transition-colors duration-300 hover:border-white/20 h-full cursor-pointer" style="--spot-color: var(--violet);">
                        <div class="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-500 group-hover:opacity-100" style="background: radial-gradient(420px circle at var(--mx, 50%) var(--my, 50%), color-mix(in srgb, var(--spot-color) 22%, transparent), transparent 60%);"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px opacity-60 bg-gradient-to-r from-transparent via-violet to-transparent"></div>
                        <div class="relative">
                            <div class="flex h-full min-h-[280px] flex-col">
                                <div class="flex items-start justify-between">
                                    <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-violet">02 //</span>
                                    <div class="relative h-16 w-16 shrink-0" style="perspective: 600px;">
                                        <div class="absolute inset-0 rounded-full opacity-60 blur-xl bg-violet"></div>
                                        <div class="relative grid h-full w-full place-items-center" style="transform-style: preserve-3d; animation: float-y 5s ease-in-out infinite;">
                                            <div class="relative h-12 w-12" style="transform-style: preserve-3d; transform: rotateX(60deg); animation: spin-y 6s linear infinite;">
                                                <div class="absolute inset-0 rounded-full border-2 border-violet" style="transform: rotateY(0deg); box-shadow: 0 0 12px var(--violet);"></div>
                                                <div class="absolute inset-0 rounded-full border-2 border-violet" style="transform: rotateY(60deg); box-shadow: 0 0 12px var(--violet);"></div>
                                                <div class="absolute inset-0 rounded-full border-2 border-violet" style="transform: rotateY(120deg); box-shadow: 0 0 12px var(--violet);"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="mt-6 font-display text-2xl font-semibold tracking-tight">Multi-Cursor Sync</h3>
                                <p class="mt-3 text-sm leading-relaxed text-muted-foreground">Sub-frame latency cursors with faculty governance baked into the protocol layer.</p>
                                <div class="mt-auto pt-8">
                                    <div class="h-px w-full bg-gradient-to-r from-transparent via-violet to-transparent opacity-40"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="reveal md:col-span-2">
                    <div class="group relative overflow-hidden rounded-2xl border border-border bg-surface p-7 transition-colors duration-300 hover:border-white/20 h-full cursor-pointer" style="--spot-color: var(--emerald);">
                        <div class="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-500 group-hover:opacity-100" style="background: radial-gradient(420px circle at var(--mx, 50%) var(--my, 50%), color-mix(in srgb, var(--spot-color) 22%, transparent), transparent 60%);"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px opacity-60 bg-gradient-to-r from-transparent via-emerald to-transparent"></div>
                        <div class="relative">
                            <div class="flex h-full min-h-[280px] flex-col">
                                <div class="flex items-start justify-between">
                                    <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-emerald">03 //</span>
                                    <div class="relative h-16 w-16 shrink-0" style="perspective: 600px;">
                                        <div class="absolute inset-0 rounded-full opacity-60 blur-xl bg-emerald"></div>
                                        <div class="relative grid h-full w-full place-items-center" style="transform-style: preserve-3d; animation: float-y 5s ease-in-out infinite;">
                                            <div class="relative h-11 w-9" style="transform: rotateY(25deg); animation: spin-soft 5s ease-in-out infinite alternate;">
                                                <div class="absolute inset-0 bg-gradient-to-b from-emerald to-transparent" style="clip-path: polygon(50% 0px, 100% 25%, 100% 70%, 50% 100%, 0px 70%, 0px 25%); box-shadow: 0 0 20px var(--emerald);"></div>
                                                <div class="absolute inset-1 border border-white/50" style="clip-path: polygon(50% 0px, 100% 25%, 100% 70%, 50% 100%, 0px 70%, 0px 25%);"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="mt-6 font-display text-2xl font-semibold tracking-tight">Responsible AI</h3>
                                <p class="mt-3 text-sm leading-relaxed text-muted-foreground">AI that explains reasoning before answers. Audit trail on every completion.</p>
                                <div class="mt-auto pt-8">
                                    <div class="h-px w-full bg-gradient-to-r from-transparent via-emerald to-transparent opacity-40"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="reveal md:col-span-2 lg:translate-y-8">
                    <div class="group relative overflow-hidden rounded-2xl border border-border bg-surface p-7 transition-colors duration-300 hover:border-white/20 h-full cursor-pointer" style="--spot-color: var(--rose);">
                        <div class="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-500 group-hover:opacity-100" style="background: radial-gradient(420px circle at var(--mx, 50%) var(--my, 50%), color-mix(in srgb, var(--spot-color) 22%, transparent), transparent 60%);"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px opacity-60 bg-gradient-to-r from-transparent via-rose to-transparent"></div>
                        <div class="relative">
                            <div class="flex h-full min-h-[280px] flex-col">
                                <div class="flex items-start justify-between">
                                    <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-rose">04 //</span>
                                    <div class="relative h-16 w-16 shrink-0" style="perspective: 600px;">
                                        <div class="absolute inset-0 rounded-full opacity-60 blur-xl bg-rose"></div>
                                        <div class="relative grid h-full w-full place-items-center" style="transform-style: preserve-3d; animation: float-y 5s ease-in-out infinite;">
                                            <div class="flex h-10 items-end gap-1">
                                                <div class="w-1.5 rounded-full bg-rose" style="height: 100%; box-shadow: 0 0 8px var(--rose); animation: wave-bar 1.4s ease-in-out infinite;"></div>
                                                <div class="w-1.5 rounded-full bg-rose" style="height: 100%; box-shadow: 0 0 8px var(--rose); animation: wave-bar 1.4s ease-in-out 0.15s infinite;"></div>
                                                <div class="w-1.5 rounded-full bg-rose" style="height: 100%; box-shadow: 0 0 8px var(--rose); animation: wave-bar 1.4s ease-in-out 0.30s infinite;"></div>
                                                <div class="w-1.5 rounded-full bg-rose" style="height: 100%; box-shadow: 0 0 8px var(--rose); animation: wave-bar 1.4s ease-in-out 0.45s infinite;"></div>
                                                <div class="w-1.5 rounded-full bg-rose" style="height: 100%; box-shadow: 0 0 8px var(--rose); animation: wave-bar 1.4s ease-in-out 0.60s infinite;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="mt-6 font-display text-2xl font-semibold tracking-tight">Live Sessions</h3>
                                <p class="mt-3 text-sm leading-relaxed text-muted-foreground">WebRTC voice &amp; video stitched into the editor — office hours, anywhere.</p>
                                <div class="mt-auto pt-8">
                                    <div class="h-px w-full bg-gradient-to-r from-transparent via-rose to-transparent opacity-40"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="reveal md:col-span-2">
                    <div class="group relative overflow-hidden rounded-2xl border border-border bg-surface p-7 transition-colors duration-300 hover:border-white/20 h-full cursor-pointer" style="--spot-color: var(--indigo);">
                        <div class="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-500 group-hover:opacity-100" style="background: radial-gradient(420px circle at var(--mx, 50%) var(--my, 50%), color-mix(in srgb, var(--spot-color) 22%, transparent), transparent 60%);"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px opacity-60 bg-gradient-to-r from-transparent via-indigo to-transparent"></div>
                        <div class="relative">
                            <div class="flex h-full min-h-[280px] flex-col">
                                <div class="flex items-start justify-between">
                                    <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-indigo">05 //</span>
                                    <div class="relative h-16 w-16 shrink-0" style="perspective: 600px;">
                                        <div class="absolute inset-0 rounded-full opacity-60 blur-xl bg-indigo"></div>
                                        <div class="relative grid h-full w-full place-items-center" style="transform-style: preserve-3d; animation: float-y 5s ease-in-out infinite;">
                                            <div class="relative h-12 w-12" style="animation: spin-y 12s linear infinite;">
                                                <div class="absolute left-1/2 top-1/2 h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full bg-indigo" style="box-shadow: 0 0 16px var(--indigo);"></div>
                                                <div class="absolute left-1/2 top-1/2 h-1.5 w-1.5 rounded-full bg-indigo" style="transform: rotate(0deg) translateY(-22px); box-shadow: 0 0 8px var(--indigo);"></div>
                                                <div class="absolute left-1/2 top-1/2 h-1.5 w-1.5 rounded-full bg-indigo" style="transform: rotate(72deg) translateY(-22px); box-shadow: 0 0 8px var(--indigo);"></div>
                                                <div class="absolute left-1/2 top-1/2 h-1.5 w-1.5 rounded-full bg-indigo" style="transform: rotate(144deg) translateY(-22px); box-shadow: 0 0 8px var(--indigo);"></div>
                                                <div class="absolute left-1/2 top-1/2 h-1.5 w-1.5 rounded-full bg-indigo" style="transform: rotate(216deg) translateY(-22px); box-shadow: 0 0 8px var(--indigo);"></div>
                                                <div class="absolute left-1/2 top-1/2 h-1.5 w-1.5 rounded-full bg-indigo" style="transform: rotate(288deg) translateY(-22px); box-shadow: 0 0 8px var(--indigo);"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="mt-6 font-display text-2xl font-semibold tracking-tight">LMS Sync</h3>
                                <p class="mt-3 text-sm leading-relaxed text-muted-foreground">Native bridges to Canvas, Moodle, Blackboard. Grades flow back automatically.</p>
                                <div class="mt-auto pt-8">
                                    <div class="h-px w-full bg-gradient-to-r from-transparent via-indigo to-transparent opacity-40"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════════
     MEET KERNEL SECTION (AI COMPANION WITH 3D ROBOT)
     ═══════════════════════════════════════════════════════════════════ -->
    <section id="companion" class="relative overflow-hidden border-t border-border py-32">
        <!-- Background Radial Gradients & Grid -->
        <div aria-hidden="true" class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse at 70% 30%, color-mix(in srgb, var(--violet) 18%, transparent), transparent 60%), radial-gradient(ellipse at 20% 80%, color-mix(in srgb, var(--cyan) 14%, transparent), transparent 55%);"></div>
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 opacity-[0.08]" style="background-image: linear-gradient(rgba(255, 255, 255, 0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.5) 1px, transparent 1px); background-size: 80px 80px; mask-image: radial-gradient(black, transparent 70%);"></div>

        <div class="relative mx-auto max-w-7xl px-6">
            <div class="reveal">
                <div class="mb-12 max-w-2xl">
                    <span class="font-mono text-[10px] uppercase tracking-[0.3em] text-violet-light">/ 05 · meet kernel</span>
                    <h2 class="mt-3 font-display text-4xl font-semibold tracking-tight md:text-5xl">
                        An AI <span class="font-serif-italic aurora-text">companion</span><br>that lives in your IDE.
                    </h2>
                    <p class="mt-6 max-w-xl text-muted-foreground">
                        Move your mouse, ask a question, watch Kernel think. Trained on your course material, scoped to your cohort, auditable by faculty.
                    </p>
                </div>
            </div>

            <div class="grid items-stretch gap-6 lg:grid-cols-[1.05fr_1fr]">
                <!-- Left: Interactive Spline 3D Robot -->
                <div class="reveal">
                    <div class="glass-panel relative h-[520px] overflow-hidden rounded-3xl md:h-[600px]">
                        <div class="absolute left-5 top-5 z-10 flex items-center gap-2">
                            <span class="flex h-2 w-2">
                                <span class="absolute inline-flex h-2 w-2 animate-ping rounded-full bg-emerald opacity-60"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald"></span>
                            </span>
                            <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground">kernel · online</span>
                        </div>
                        <div class="absolute right-5 top-5 z-10 rounded-full border border-border bg-black/40 px-3 py-1 font-mono text-[9px] uppercase tracking-[0.25em] text-cyan-light backdrop-blur">
                            drag · interact
                        </div>

                        <!-- Spline Viewer embedding the active robot scene -->
                        <div class="absolute inset-0">
                            <spline-viewer url="https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode" style="width: 100%; height: 100%; display: block;"></spline-viewer>
                            <!-- Custom Modern Loader for 3D Robot -->
                            <div id="splineLoader" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-[#050507] transition-opacity duration-500">
                                <div class="relative flex items-center justify-center">
                                    <div class="absolute h-12 w-12 rounded-full border border-cyan/20"></div>
                                    <div class="h-12 w-12 animate-spin rounded-full border border-transparent border-t-cyan"></div>
                                </div>
                                <span class="mt-4 font-mono text-[10px] uppercase tracking-[0.2em] text-cyan/70 animate-pulse">Connecting with Kernel...</span>
                            </div>
                        </div>


                        <span class="pointer-events-none absolute left-3 top-3 h-4 w-4 border-cyan/60" style="border-width: 1px 0px 0px 1px;"></span>
                        <span class="pointer-events-none absolute right-3 top-3 h-4 w-4 border-cyan/60" style="border-width: 1px 1px 0px 0px;"></span>
                        <span class="pointer-events-none absolute left-3 bottom-3 h-4 w-4 border-cyan/60" style="border-width: 0px 0px 1px 1px;"></span>
                        <span class="pointer-events-none absolute right-3 bottom-3 h-4 w-4 border-cyan/60" style="border-width: 0px 1px 1px 0px;"></span>
                    </div>
                </div>

                <!-- Right: Interactive Companion UI Mock -->
                <div class="reveal">
                    <div class="glass-panel flex h-full flex-col rounded-3xl p-6 md:p-8">
                        <div class="mb-6 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="grid h-8 w-8 place-items-center rounded-lg bg-violet/20">
                                    <div class="h-2 w-2 rounded-full bg-violet-light shadow-[0_0_12px_var(--violet)]"></div>
                                </div>
                                <div>
                                    <div class="font-display text-sm font-semibold">Kernel v4</div>
                                    <div class="font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground">teaching mode · cs101</div>
                                </div>
                            </div>
                            <span class="font-mono text-[9px] uppercase tracking-[0.22em] text-emerald-light">audited</span>
                        </div>

                        <div class="mb-4 self-end rounded-2xl rounded-br-sm border border-border bg-white/[0.04] px-4 py-3 text-sm">
                            Explain recursion to me
                        </div>

                        <div class="relative rounded-2xl rounded-bl-sm border p-4" style="border-color: color-mix(in srgb, var(--violet) 35%, transparent); background: linear-gradient(135deg, color-mix(in srgb, var(--violet) 10%, transparent), color-mix(in srgb, var(--indigo) 6%, transparent));">
                            <div class="mb-2 flex items-center gap-2 font-mono text-[9px] uppercase tracking-[0.22em] text-violet-light">
                                <span>kernel</span>
                                <span class="opacity-50">/ reasoning</span>
                            </div>
                            <p id="typedReasoning" class="min-h-[80px] text-sm leading-relaxed text-foreground/90">
                                Let's trace your recursion step by step. First, what's the base case?
                            </p>
                        </div>

                        <div class="mt-auto pt-6">
                            <div class="mb-3 font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground">try a prompt</div>
                            <div class="flex flex-wrap gap-2">
                                <button onclick="changeMockPrompt(this)" class="group rounded-full border px-3.5 py-1.5 text-xs transition-all duration-300 border-violet/60 bg-violet/20 text-violet-light">Explain recursion to me</button>
                                <button onclick="changeMockPrompt(this)" class="group rounded-full border px-3.5 py-1.5 text-xs transition-all duration-300 border-border bg-white/[0.02] text-muted-foreground hover:border-cyan/50 hover:text-foreground">Why does my loop crash at iteration 12?</button>
                                <button onclick="changeMockPrompt(this)" class="group rounded-full border px-3.5 py-1.5 text-xs transition-all duration-300 border-border bg-white/[0.02] text-muted-foreground hover:border-cyan/50 hover:text-foreground">Audit my latest commit for style</button>
                                <button onclick="changeMockPrompt(this)" class="group rounded-full border px-3.5 py-1.5 text-xs transition-all duration-300 border-border bg-white/[0.02] text-muted-foreground hover:border-cyan/50 hover:text-foreground">Pair-program a binary tree</button>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-3 gap-2 border-t border-border pt-5 font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground">
                            <div>
                                <div class="text-cyan-light">42ms</div>
                                <div>latency</div>
                            </div>
                            <div>
                                <div class="text-emerald-light">policy ✓</div>
                                <div>integrity</div>
                            </div>
                            <div>
                                <div class="text-violet-light">tier-2</div>
                                <div>reasoning</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════════
     CONTROL PLANE (GOVERNANCE)
     ═══════════════════════════════════════════════════════════════════ -->
    <section id="governance" class="relative border-t border-border bg-white/[0.015] py-32">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid items-center gap-16 lg:grid-cols-2">
                <div class="reveal">
                    <div>
                        <span class="font-mono text-[10px] uppercase tracking-[0.3em] text-rose-light">/ 03 · control plane</span>
                        <h2 class="mt-3 font-display text-4xl font-semibold tracking-tight md:text-5xl">
                            The instructor <span class="font-serif-italic text-rose-light">command deck.</span>
                        </h2>
                        <p class="mt-6 max-w-md text-muted-foreground">
                            Observe an entire lecture hall in real time. Peek into any workspace, broadcast intent, throttle resources, and replay sessions for review — all from one console.
                        </p>
                        <ul class="mt-10 space-y-1 list-none">
                            <li class="group flex items-start gap-4 border-b border-border py-5 last:border-b-0">
                                <span class="grid h-9 w-9 shrink-0 place-items-center border border-border font-mono text-xs font-semibold transition-colors" style="color: var(--cyan);">A</span>
                                <div>
                                    <div class="font-display text-base font-semibold">Live Telemetry</div>
                                    <div class="mt-1 text-sm text-muted-foreground">Per-student keystrokes, compute spend, AI usage — streaming.</div>
                                </div>
                            </li>
                            <li class="group flex items-start gap-4 border-b border-border py-5 last:border-b-0">
                                <span class="grid h-9 w-9 shrink-0 place-items-center border border-border font-mono text-xs font-semibold transition-colors" style="color: var(--violet);">B</span>
                                <div>
                                    <div class="font-display text-base font-semibold">Resource Throttling</div>
                                    <div class="mt-1 text-sm text-muted-foreground">Cap CPU/GPU per cohort. Burst budgets, audited.</div>
                                </div>
                            </li>
                            <li class="group flex items-start gap-4 border-b border-border py-5 last:border-b-0">
                                <span class="grid h-9 w-9 shrink-0 place-items-center border border-border font-mono text-xs font-semibold transition-colors" style="color: var(--emerald);">C</span>
                                <div>
                                    <div class="font-display text-base font-semibold">Session Replay</div>
                                    <div class="mt-1 text-sm text-muted-foreground">Scrub through any session like a video. Diff every commit.</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Student Grid Nodes Telemetry -->
                <div class="reveal">
                    <div class="glass-panel relative aspect-square overflow-hidden rounded-2xl p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald"></span>
                                <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground">CS101 · 24 sandboxes</span>
                            </div>
                            <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-cyan-light">live</span>
                        </div>

                        <div class="grid h-[calc(100%-3rem)] grid-cols-4 grid-rows-6 gap-2">
                            @php
                            $colorsList = ['cyan', 'violet', 'emerald', 'rose', 'indigo'];
                            $nodeIndex = 1;
                            @endphp
                            @for ($r = 0; $r < 24; $r++)
                                @php
                                $col=$colorsList[$r % count($colorsList)];
                                $delay=($r * 0.07) . 's' ;
                                $pulseSpeed=rand(3, 7) . 's' ;
                                $val1=rand(25, 95);
                                $val2=rand(15, $val1 - 5);
                                @endphp
                                <div class="relative overflow-hidden rounded-md border border-border bg-black/30 p-1.5" style="animation: pulse-dot {{ $pulseSpeed }} ease-in-out {{ $delay }} infinite;">
                                <div class="flex items-center gap-1">
                                    <span class="h-1 w-1 rounded-full" style="background: var(--{{ $col }}); box-shadow: 0 0 6px var(--{{ $col }});"></span>
                                    <span class="font-mono text-[7px] uppercase tracking-wider text-muted-foreground">n-{{ sprintf('%02d', $nodeIndex++) }}</span>
                                </div>
                                <div class="mt-1 space-y-0.5">
                                    <div class="h-0.5 rounded-full bg-white/10" style="width: {{ $val1 }}%;"></div>
                                    <div class="h-0.5 rounded-full" style="width: {{ $val2 }}%; background: var(--{{ $col }}); opacity: 0.5;"></div>
                                </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════════
     RESPONSIBLE AI SECTION
     ═══════════════════════════════════════════════════════════════════ -->
    <section id="ai" class="relative py-32 overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse at top, color-mix(in srgb, var(--violet) 8%, transparent), transparent 60%);"></div>
        <div class="relative mx-auto max-w-7xl px-6">
            <div class="reveal">
                <div class="mb-16 max-w-2xl">
                    <span class="font-mono text-[10px] uppercase tracking-[0.3em] text-emerald-light">/ 04 · responsible ai</span>
                    <h2 class="mt-3 font-display text-4xl font-semibold tracking-tight md:text-5xl">
                        AI as a <span class="font-serif-italic text-emerald-light">teaching assistant</span><br>— not an answer machine.
                    </h2>
                    <p class="mt-6 max-w-xl text-muted-foreground">
                        Every completion is logged, attributed, and graded against academic-integrity policy. Students learn the why before the what.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <!-- AI Card 1 -->
                <div class="reveal">
                    <div class="glass-panel relative h-full overflow-hidden rounded-2xl p-6">
                        <div class="mb-5 flex items-center justify-between">
                            <span class="font-mono text-[10px] uppercase tracking-[0.25em]" style="color: var(--cyan);">01 / Prompt</span>
                            <span class="h-1.5 w-1.5 rounded-full bg-cyan" style="box-shadow: 0 0 12px var(--cyan);"></span>
                        </div>
                        <h3 class="font-display text-xl font-semibold">Student asks.</h3>
                        <div class="mt-5 rounded-lg border border-border bg-black/30 p-4" style="box-shadow: inset 0 0 0 1px rgba(23, 195, 214, 0.12);">
                            <p class="text-sm leading-relaxed text-foreground/85">Why is my binary search returning -1 on a sorted array of 1M ints?</p>
                        </div>
                        <div class="mt-6 flex items-center justify-between font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground">
                            <span>logged · 93ms</span>
                            <span>policy ✓</span>
                        </div>
                    </div>
                </div>

                <!-- AI Card 2 -->
                <div class="reveal" style="transition-delay: 120ms;">
                    <div class="glass-panel relative h-full overflow-hidden rounded-2xl p-6">
                        <div class="mb-5 flex items-center justify-between">
                            <span class="font-mono text-[10px] uppercase tracking-[0.25em]" style="color: var(--violet);">02 / Reason</span>
                            <span class="h-1.5 w-1.5 rounded-full bg-violet" style="box-shadow: 0 0 12px var(--violet);"></span>
                        </div>
                        <h3 class="font-display text-xl font-semibold">AI reasons.</h3>
                        <div class="mt-5 rounded-lg border border-border bg-black/30 p-4" style="box-shadow: inset 0 0 0 1px rgba(155, 93, 229, 0.12);">
                            <div class="font-mono text-[11px] leading-relaxed text-muted-foreground">
                                <span class="text-violet">&gt; thinking...</span><br>
                                Inspecting the loop bounds — `hi` initialized to `arr.length` instead of `arr.length - 1` creates an off-by-one on the right edge.
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-between font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground">
                            <span>logged · 67ms</span>
                            <span>policy ✓</span>
                        </div>
                    </div>
                </div>

                <!-- AI Card 3 -->
                <div class="reveal" style="transition-delay: 240ms;">
                    <div class="glass-panel relative h-full overflow-hidden rounded-2xl p-6">
                        <div class="mb-5 flex items-center justify-between">
                            <span class="font-mono text-[10px] uppercase tracking-[0.25em]" style="color: var(--emerald);">03 / Guide</span>
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald" style="box-shadow: 0 0 12px var(--emerald);"></span>
                        </div>
                        <h3 class="font-display text-xl font-semibold">AI guides, doesn't dictate.</h3>
                        <div class="mt-5 rounded-lg border border-border bg-black/30 p-4" style="box-shadow: inset 0 0 0 1px rgba(0, 191, 166, 0.12);">
                            <p class="text-sm leading-relaxed text-foreground/85">Try tracing the values of `lo`, `hi`, `mid` for the last 3 iterations. Want a Socratic walkthrough or the patch?</p>
                            <div class="mt-3 flex items-center gap-1">
                                <span class="h-1.5 w-1.5 animate-[pulse-dot_1.2s_ease-in-out_infinite] rounded-full bg-emerald"></span>
                                <span class="h-1.5 w-1.5 animate-[pulse-dot_1.2s_ease-in-out_infinite] rounded-full bg-emerald" style="animation-delay: 0.2s;"></span>
                                <span class="h-1.5 w-1.5 animate-[pulse-dot_1.2s_ease-in-out_infinite] rounded-full bg-emerald" style="animation-delay: 0.4s;"></span>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-between font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground">
                            <span>logged · 23ms</span>
                            <span>policy ✓</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════════
     CTA SECTION
     ═══════════════════════════════════════════════════════════════════ -->
    <section id="cta" class="relative overflow-hidden py-40">
        <!-- Grid Floor -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute left-1/2 h-[140%] w-[200%] -translate-x-1/2 grid-floor opacity-[0.55]" style="top: -50%; transform: translateX(-50%) perspective(900px) rotateX(-65deg); transform-origin: center bottom; mask-image: linear-gradient(to top, transparent 5%, black 50%, transparent 100%);"></div>
            <div class="absolute inset-x-0 h-40" style="top: 0px; background: linear-gradient(to bottom, var(--background), transparent);"></div>
        </div>

        <div aria-hidden="true" class="pointer-events-none absolute left-1/2 bottom-0 h-[400px] w-[800px] -translate-x-1/2 rounded-full opacity-40 blur-[120px]" style="background: radial-gradient(circle, color-mix(in srgb, var(--cyan) 30%, transparent), transparent 60%);"></div>

        <div class="relative mx-auto max-w-3xl px-6 text-center">
            <div class="reveal">
                <span class="font-mono text-[10px] uppercase tracking-[0.3em] text-cyan-light">/ establish connection</span>
                <h2 class="mt-6 font-display text-5xl font-semibold leading-[0.95] tracking-tight md:text-7xl">
                    <span class="metallic-text">Ready for </span>
                    <span class="aurora-text font-serif-italic">production</span>
                    <span class="metallic-text">?</span>
                </h2>
                <p class="mx-auto mt-6 max-w-md text-muted-foreground">Deploy VisionLab across your institution. Onboarding in days, not quarters.</p>
            </div>

            <div class="reveal" style="transition-delay: 120ms;">
                <div class="mt-12 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    @auth
                    <a href="{{ route('dashboard') }}" class="relative inline-flex items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-medium tracking-wide transition-[background,color,box-shadow] duration-300 bg-cyan text-background shadow-[0_0_0_1px_rgba(23,195,214,0.5),0_20px_60px_-20px_rgba(23,195,214,0.55)] hover:bg-cyan-light hover:shadow-[0_0_0_1px_rgba(110,231,224,0.7),0_30px_80px_-20px_rgba(23,195,214,0.7)] text-decoration-none">
                        Dashboard
                        <span class="font-mono text-[10px] opacity-70">↗</span>
                    </a>
                    @else
                    <a href="{{ route('register') }}" class="relative inline-flex items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-medium tracking-wide transition-[background,color,box-shadow] duration-300 bg-cyan text-background shadow-[0_0_0_1px_rgba(23,195,214,0.5),0_20px_60px_-20px_rgba(23,195,214,0.55)] hover:bg-cyan-light hover:shadow-[0_0_0_1px_rgba(110,231,224,0.7),0_30px_80px_-20px_rgba(23,195,214,0.7)] text-decoration-none">
                        Start Building
                        <span class="font-mono text-[10px] opacity-70">↗</span>
                    </a>
                    <a href="{{ route('demo') }}" class="relative inline-flex items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-medium tracking-wide transition-[background,color,box-shadow] duration-300 border border-white/15 bg-white/[0.02] text-foreground hover:bg-white/[0.06] hover:border-white/30 text-decoration-none">
                        Faculty Demo
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════════════════ -->
    <x-frontend-footer />

    <!-- ═══════════════════════════════════════════════════════════════════
     INTERACTIONS (Scroll reveal, 3D Tilt, Card spotlight tracking)
     ═══════════════════════════════════════════════════════════════════ -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Dismiss Spline Loader once model is ready
            const viewer = document.querySelector('spline-viewer');
            const loader = document.getElementById('splineLoader');
            if (viewer && loader) {
                viewer.addEventListener('load', () => {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        if (loader) loader.remove();
                    }, 500);
                });
                // Safety timeout (maximum 15 seconds)
                setTimeout(() => {
                    const currentLoader = document.getElementById('splineLoader');
                    if (currentLoader) {
                        currentLoader.style.opacity = '0';
                        setTimeout(() => currentLoader.remove(), 500);
                    }
                }, 15000);
            }

            // Hide Spline logo watermark inside shadow DOM
            const hideSplineLogo = setInterval(() => {
                const viewerInstance = document.querySelector('spline-viewer');
                if (viewerInstance && viewerInstance.shadowRoot) {
                    const logo = viewerInstance.shadowRoot.getElementById('logo');
                    if (logo) {
                        logo.style.display = 'none';
                        clearInterval(hideSplineLogo);
                    }
                }
            }, 100);
            setTimeout(() => clearInterval(hideSplineLogo), 12000);

            // Scroll reveal logic
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.05,
                rootMargin: '0px 0px -40px 0px'
            });

            document.querySelectorAll('.reveal').forEach(el => io.observe(el));

            // Navbar scroll background logic
            const nav = document.getElementById('mainNav');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 40) {
                    nav.classList.add('bg-background/95');
                    nav.classList.remove('bg-background/70');
                } else {
                    nav.classList.add('bg-background/70');
                    nav.classList.remove('bg-background/95');
                }
            }, {
                passive: true
            });

            // 3D Tilt interaction logic
            document.querySelectorAll('.tilt-3d').forEach(card => {
                card.addEventListener('mousemove', e => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const midX = rect.width / 2;
                    const midY = rect.height / 2;

                    // Limit rotations to max 8 degrees
                    const rx = -((y - midY) / midY) * 8;
                    const ry = ((x - midX) / midX) * 8;

                    card.style.setProperty('--rx', `${rx}deg`);
                    card.style.setProperty('--ry', `${ry}deg`);
                });

                card.addEventListener('mouseleave', () => {
                    card.style.setProperty('--rx', '8deg');
                    card.style.setProperty('--ry', '-4deg');
                });
            });

            // Card spotlight mouse coordinate tracking
            document.querySelectorAll('.group').forEach(group => {
                group.addEventListener('mousemove', e => {
                    const rect = group.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    group.style.setProperty('--mx', `${x}px`);
                    group.style.setProperty('--my', `${y}px`);
                });
            });
        });

        // Mock interactive chat responses for Kernel AI
        const mockReasoningList = {
            'Explain recursion to me': "Let's trace your recursion step by step. First, what's the base case?",
            'Why does my loop crash at iteration 12?': "Analyzing indices... The loop references `arr[i+1]` which overflows the allocated buffer at index 12.",
            'Audit my latest commit for style': "Commit status: 2 stylistic suggestions. Remove console logs in production; convert var to const in architecture.ts.",
            'Pair-program a binary tree': "Tree interface loaded. Let's start with the Node class defining left, right, and generic value properties."
        };

        function changeMockPrompt(button) {
            const promptText = button.innerText.trim();
            const typedReasoning = document.getElementById('typedReasoning');

            // Clear styles from siblings and highlight clicked chip
            button.parentElement.querySelectorAll('button').forEach(btn => {
                btn.className = "group rounded-full border px-3.5 py-1.5 text-xs transition-all duration-300 border-border bg-white/[0.02] text-muted-foreground hover:border-cyan/50 hover:text-foreground";
            });
            button.className = "group rounded-full border px-3.5 py-1.5 text-xs transition-all duration-300 border-violet/60 bg-violet/20 text-violet-light";

            // Typewriter effect mock for response
            typedReasoning.style.opacity = 0.5;
            setTimeout(() => {
                typedReasoning.innerText = mockReasoningList[promptText] || "Kernel assistant analyzing request...";
                typedReasoning.style.opacity = 1;
            }, 150);
        }
    </script>

    <!-- ═══════════════════════════════════════════════════════════════════
     THREE.JS CANVAS GRAPHICS
     ═══════════════════════════════════════════════════════════════════ -->
    <script>
        // SCENE: HERO — 3D Rotating Globe
        (function initGlobeScene() {
            const container = document.getElementById('heroGlobeCanvas');
            if (!container) return;

            const scene = new THREE.Scene();
            const width = container.clientWidth || window.innerWidth;
            const height = container.clientHeight || window.innerHeight;
            const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
            camera.position.set(0, 0, 11);

            const renderer = new THREE.WebGLRenderer({
                antialias: true,
                alpha: true,
                powerPreference: "high-performance"
            });
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.setSize(width, height);
            container.appendChild(renderer.domElement);

            const globeGroup = new THREE.Group();
            scene.add(globeGroup);

            // Realistic World Globe continent algorithm
            function isLand(lat, lon) {
                const centers = [
                    // North America
                    {
                        lat: 45,
                        lon: -100,
                        r: 28
                    },
                    {
                        lat: 60,
                        lon: -110,
                        r: 22
                    },
                    {
                        lat: 35,
                        lon: -90,
                        r: 18
                    },
                    {
                        lat: 65,
                        lon: -85,
                        r: 15
                    },
                    // South America
                    {
                        lat: -10,
                        lon: -60,
                        r: 20
                    },
                    {
                        lat: -30,
                        lon: -60,
                        r: 18
                    },
                    {
                        lat: -45,
                        lon: -70,
                        r: 12
                    },
                    // Europe
                    {
                        lat: 55,
                        lon: 15,
                        r: 18
                    },
                    {
                        lat: 45,
                        lon: 10,
                        r: 15
                    },
                    // Africa
                    {
                        lat: 5,
                        lon: 20,
                        r: 25
                    },
                    {
                        lat: 20,
                        lon: 15,
                        r: 20
                    },
                    {
                        lat: -20,
                        lon: 25,
                        r: 18
                    },
                    // Asia / Russia
                    {
                        lat: 60,
                        lon: 100,
                        r: 35
                    },
                    {
                        lat: 50,
                        lon: 80,
                        r: 30
                    },
                    {
                        lat: 50,
                        lon: 120,
                        r: 25
                    },
                    {
                        lat: 35,
                        lon: 105,
                        r: 25
                    },
                    {
                        lat: 30,
                        lon: 75,
                        r: 15
                    }, // India
                    {
                        lat: 35,
                        lon: 135,
                        r: 10
                    }, // Japan
                    {
                        lat: 15,
                        lon: 105,
                        r: 12
                    }, // SE Asia
                    // Australia
                    {
                        lat: -25,
                        lon: 135,
                        r: 20
                    },
                    {
                        lat: -30,
                        lon: 140,
                        r: 15
                    },
                    // Greenland
                    {
                        lat: 72,
                        lon: -40,
                        r: 16
                    }
                ];

                // Multi-frequency noise to simulate realistic organic coastlines
                let noise = Math.sin(lat * 0.15) * Math.cos(lon * 0.15) * 6 +
                    Math.sin(lat * 0.45) * Math.cos(lon * 0.45) * 3 +
                    Math.sin(lat * 1.2 + lon) * 1.5;

                for (const c of centers) {
                    let dLon = Math.abs(lon - c.lon);
                    if (dLon > 180) dLon = 360 - dLon;
                    const dLat = lat - c.lat;
                    const dist = Math.sqrt(dLat * dLat + dLon * dLon);
                    if (dist < c.r + noise) {
                        return true;
                    }
                }
                return false;
            }

            const radius = 3.3;
            const segments = 120;
            const rings = 240;
            const geometry = new THREE.BufferGeometry();
            const positions = [];
            const colors = [];
            const colorCyan = new THREE.Color('#17c3d6');
            const colorViolet = new THREE.Color('#9b5de5');
            const colorRose = new THREE.Color('#f0426d');

            for (let i = 0; i < segments; i++) {
                const phi = (i / segments) * Math.PI;
                const lat = 90 - (phi / Math.PI) * 180;

                for (let j = 0; j < rings; j++) {
                    const theta = (j / rings) * 2 * Math.PI;
                    const lon = (theta / (2 * Math.PI)) * 360 - 180;

                    if (isLand(lat, lon)) {
                        const x = radius * Math.sin(phi) * Math.cos(theta);
                        const y = radius * Math.cos(phi);
                        const z = radius * Math.sin(phi) * Math.sin(theta);

                        positions.push(x, y, z);

                        // Create the beautiful gradient color mapping
                        const ratio = i / segments;
                        const mixedColor = colorCyan.clone()
                            .lerp(colorViolet, ratio)
                            .lerp(colorRose, Math.sin(theta) * 0.5 + 0.5);
                        colors.push(mixedColor.r, mixedColor.g, mixedColor.b);
                    }
                }
            }

            geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
            geometry.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3));

            const material = new THREE.PointsMaterial({
                size: 0.045,
                vertexColors: true,
                transparent: true,
                opacity: 0.8,
                blending: THREE.AdditiveBlending,
                depthWrite: false
            });

            const globePoints = new THREE.Points(geometry, material);
            globeGroup.add(globePoints);

            // Coordinate grid lines (latitude/longitude rings)
            const lineMat = new THREE.LineBasicMaterial({
                color: 0x17c3d6,
                transparent: true,
                opacity: 0.15,
                blending: THREE.AdditiveBlending
            });

            // Latitude rings
            for (let i = 1; i < 8; i++) {
                const ringRadius = radius * Math.sin((i / 8) * Math.PI);
                const ringHeight = radius * Math.cos((i / 8) * Math.PI);
                const ringGeo = new THREE.BufferGeometry();
                const ringPos = [];
                for (let j = 0; j <= 64; j++) {
                    const angle = (j / 64) * 2 * Math.PI;
                    ringPos.push(ringRadius * Math.cos(angle), ringHeight, ringRadius * Math.sin(angle));
                }
                ringGeo.setAttribute('position', new THREE.Float32BufferAttribute(ringPos, 3));
                const ringLine = new THREE.Line(ringGeo, lineMat);
                globeGroup.add(ringLine);
            }

            // Longitude rings
            for (let i = 0; i < 6; i++) {
                const angle = (i / 6) * Math.PI;
                const ringGeo = new THREE.BufferGeometry();
                const ringPos = [];
                for (let j = 0; j <= 64; j++) {
                    const ringTheta = (j / 64) * 2 * Math.PI;
                    ringPos.push(
                        radius * Math.sin(ringTheta) * Math.cos(angle),
                        radius * Math.cos(ringTheta),
                        radius * Math.sin(ringTheta) * Math.sin(angle)
                    );
                }
                ringGeo.setAttribute('position', new THREE.Float32BufferAttribute(ringPos, 3));
                const ringLine = new THREE.Line(ringGeo, lineMat);
                globeGroup.add(ringLine);
            }

            // Interactive tech hub code pins
            const hubs = [{
                    name: "Silicon Valley",
                    lat: 37.7749,
                    lon: -122.4194,
                    icon: "</>",
                    color: "#17c3d6"
                },
                {
                    name: "New York",
                    lat: 40.7128,
                    lon: -74.0060,
                    icon: "js",
                    color: "#f0426d"
                },
                {
                    name: "London",
                    lat: 51.5074,
                    lon: -0.1278,
                    icon: "ts",
                    color: "#9b5de5"
                },
                {
                    name: "Paris",
                    lat: 48.8566,
                    lon: 2.3522,
                    icon: "py",
                    color: "#17c3d6"
                },
                {
                    name: "Berlin",
                    lat: 52.5200,
                    lon: 13.4050,
                    icon: "rs",
                    color: "#f0426d"
                },
                {
                    name: "Bangalore",
                    lat: 12.9716,
                    lon: 77.5946,
                    icon: "{}",
                    color: "#9b5de5"
                },
                {
                    name: "Tokyo",
                    lat: 35.6762,
                    lon: 139.6503,
                    icon: "go",
                    color: "#17c3d6"
                },
                {
                    name: "Sydney",
                    lat: -33.8688,
                    lon: 151.2093,
                    icon: "php",
                    color: "#f0426d"
                },
                {
                    name: "São Paulo",
                    lat: -23.5505,
                    lon: -46.6333,
                    icon: "db",
                    color: "#9b5de5"
                },
                {
                    name: "Cape Town",
                    lat: -33.9249,
                    lon: 18.4241,
                    icon: "git",
                    color: "#17c3d6"
                },
                {
                    name: "Singapore",
                    lat: 1.3521,
                    lon: 103.8198,
                    icon: "ai",
                    color: "#f0426d"
                },
                {
                    name: "Toronto",
                    lat: 43.6532,
                    lon: -79.3832,
                    icon: "c++",
                    color: "#9b5de5"
                }
            ];

            function createHubTexture(iconText, colorHex) {
                const canvas = document.createElement('canvas');
                canvas.width = 64;
                canvas.height = 64;
                const ctx = canvas.getContext('2d');

                ctx.clearRect(0, 0, 64, 64);

                // Outer glowing circle
                const gradient = ctx.createRadialGradient(32, 32, 2, 32, 32, 24);
                gradient.addColorStop(0, colorHex + '66');
                gradient.addColorStop(0.6, colorHex + '18');
                gradient.addColorStop(1, '#00000000');
                ctx.fillStyle = gradient;
                ctx.beginPath();
                ctx.arc(32, 32, 24, 0, Math.PI * 2);
                ctx.fill();

                // Inner solid circle border
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.arc(32, 32, 12, 0, Math.PI * 2);
                ctx.stroke();

                // Solid background for the text to hide background lines
                ctx.fillStyle = '#050507';
                ctx.beginPath();
                ctx.arc(32, 32, 11, 0, Math.PI * 2);
                ctx.fill();

                // Text icon
                ctx.fillStyle = colorHex;
                ctx.font = 'bold 9px "JetBrains Mono", monospace';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(iconText, 32, 32);

                const texture = new THREE.CanvasTexture(canvas);
                texture.minFilter = THREE.LinearFilter;
                return texture;
            }

            const spriteGroup = new THREE.Group();
            globeGroup.add(spriteGroup);

            hubs.forEach(hub => {
                // Convert lat/lon to 3D Cartesian coordinates
                const phi = (90 - hub.lat) * (Math.PI / 180);
                const theta = (hub.lon + 180) * (Math.PI / 180);

                // Surface point
                const x0 = radius * Math.sin(phi) * Math.cos(theta);
                const y0 = radius * Math.cos(phi);
                const z0 = radius * Math.sin(phi) * Math.sin(theta);

                // Elevated pin height
                const height = 0.45;
                const x1 = (radius + height) * Math.sin(phi) * Math.cos(theta);
                const y1 = (radius + height) * Math.cos(phi);
                const z1 = (radius + height) * Math.sin(phi) * Math.sin(theta);

                // Pin line
                const lineGeo = new THREE.BufferGeometry().setFromPoints([
                    new THREE.Vector3(x0, y0, z0),
                    new THREE.Vector3(x1, y1, z1)
                ]);
                const lineMat = new THREE.LineBasicMaterial({
                    color: new THREE.Color(hub.color),
                    transparent: true,
                    opacity: 0.6,
                    linewidth: 1
                });
                const line = new THREE.Line(lineGeo, lineMat);
                spriteGroup.add(line);

                // Base glowing dot
                const dotGeo = new THREE.SphereGeometry(0.035, 8, 8);
                const dotMat = new THREE.MeshBasicMaterial({
                    color: new THREE.Color(hub.color),
                    transparent: true,
                    opacity: 0.9
                });
                const dot = new THREE.Mesh(dotGeo, dotMat);
                dot.position.set(x0, y0, z0);
                spriteGroup.add(dot);

                // Glowing text sprite
                const texture = createHubTexture(hub.icon, hub.color);
                const spriteMat = new THREE.SpriteMaterial({
                    map: texture,
                    transparent: true,
                    depthWrite: false,
                    blending: THREE.AdditiveBlending
                });
                const sprite = new THREE.Sprite(spriteMat);
                sprite.position.set(x1, y1, z1);
                sprite.scale.set(0.4, 0.4, 1);

                // Save pulse metadata
                sprite.userData = {
                    baseScale: 0.4,
                    pulseSpeed: 1.8 + Math.random() * 1.5,
                    phase: Math.random() * Math.PI
                };

                spriteGroup.add(sprite);
            });

            // Animation Loop
            const clock = new THREE.Clock();
            let mouseX = 0,
                mouseY = 0;
            let targetX = 0,
                targetY = 0;

            document.addEventListener('mousemove', (e) => {
                mouseX = (e.clientX / window.innerWidth) - 0.5;
                mouseY = (e.clientY / window.innerHeight) - 0.5;
            });

            function animate() {
                requestAnimationFrame(animate);
                const elapsed = clock.getElapsedTime();

                // Auto rotation
                globeGroup.rotation.y = elapsed * 0.035;

                // Smooth mouse tilt / drag interaction
                targetX = mouseX * 0.35;
                targetY = mouseY * 0.35;

                globeGroup.rotation.x += (targetY - globeGroup.rotation.x) * 0.04;
                globeGroup.rotation.z += (targetX - globeGroup.rotation.z) * 0.04;

                // Pulse the sprite sizes
                spriteGroup.children.forEach(child => {
                    if (child instanceof THREE.Sprite && child.userData && child.userData.baseScale) {
                        const ud = child.userData;
                        const scale = ud.baseScale + Math.sin(elapsed * ud.pulseSpeed + ud.phase) * 0.06;
                        child.scale.set(scale, scale, 1);
                    }
                });

                renderer.render(scene, camera);
            }
            animate();

            window.addEventListener('resize', () => {
                const w = container.clientWidth || window.innerWidth;
                const h = container.clientHeight || window.innerHeight;
                camera.aspect = w / h;
                camera.updateProjectionMatrix();
                renderer.setSize(w, h);
            });
        })();
    </script>

</body>

</html>
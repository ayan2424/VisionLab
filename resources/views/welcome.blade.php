<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="The collaborative IDE built for research universities. Sandboxed, audited, and AI-assisted.">
    <title>VisionLab — Collaborative coding for universities</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: var(--vc-bg);
            color: var(--vc-text);
            font-family: 'Geist', -apple-system, sans-serif;
            overflow-x: hidden;
        }
        .font-mono {
            font-family: "JetBrains Mono", ui-monospace, monospace;
        }
    </style>
</head>
<body class="relative min-h-screen flex flex-col">

    <!-- Ambient Background Glows -->
    <div class="fixed top-0 left-0 w-[800px] h-[800px] bg-[#B026FF]/15 rounded-full blur-[150px] -translate-x-1/3 -translate-y-1/3 pointer-events-none z-0"></div>
    <div class="fixed bottom-0 right-0 w-[1000px] h-[1000px] bg-[#00F3FF]/10 rounded-full blur-[200px] translate-x-1/4 translate-y-1/4 pointer-events-none z-0"></div>

    <!-- Navigation -->
    <x-frontend-header />

    <!-- Hero Section -->
    <main class="flex-1 flex flex-col items-center justify-center relative z-10 px-6 pt-32 pb-24 text-center">
        <div class="mb-8 inline-flex items-center gap-2.5 rounded-full border border-white/10 bg-white/5 px-4 py-2 backdrop-blur-md">
            <span class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#00F3FF] opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-[#00F3FF]"></span>
            </span>
            <span class="font-mono text-xs uppercase tracking-widest" style="color:var(--vc-text-secondary);">VisionLab v1.0.0 is live</span>
        </div>

        <h1 class="max-w-4xl text-5xl md:text-7xl font-bold tracking-tight mb-8 text-white leading-tight">
            Build the future of<br>
            <span style="background:linear-gradient(135deg, #B026FF, #00F3FF);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">collaborative learning.</span>
        </h1>

        <p class="max-w-2xl text-lg md:text-xl mb-12 leading-relaxed" style="color:var(--vc-text-secondary);">
            The only in-browser IDE engineered specifically for research universities. Fully sandboxed, real-time sync, and responsibly AI-assisted.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
            @auth
            <a href="{{ route('dashboard') }}" class="btn-glow !px-8 !py-4 !text-base">
                Go to Dashboard
            </a>
            @else
            <a href="{{ route('register') }}" class="btn-glow !px-8 !py-4 !text-base">
                Start Building Free
            </a>
            @endauth
            <a href="{{ route('demo') }}" class="px-8 py-4 rounded-xl text-base font-semibold border transition-all duration-300 hover:bg-white/5" style="border-color:var(--vc-border);color:var(--vc-text);">
                Request Faculty Demo
            </a>
        </div>

        <!-- Hero Graphic (Glass Panel) -->
        <div class="mt-24 w-full max-w-5xl vc-card p-2 md:p-4 rounded-2xl relative">
            <div class="w-full bg-[#0A0A0F] rounded-xl overflow-hidden border border-white/5 relative z-10">
                <div class="flex items-center px-4 py-3 border-b border-white/5 bg-[#0F0F15]">
                    <div class="flex gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                    </div>
                    <div class="mx-auto text-xs font-mono" style="color:var(--vc-muted);">agent.py — VisionLab Workspace</div>
                </div>
                <div class="p-6 md:p-8 text-left font-mono text-sm md:text-base leading-relaxed overflow-x-auto">
                    <div class="flex gap-4"><span style="color:var(--vc-muted);">1</span><span class="text-[#B026FF]">from</span> <span class="text-white">visionlab</span> <span class="text-[#B026FF]">import</span> <span class="text-white">Sandbox</span></div>
                    <div class="flex gap-4"><span style="color:var(--vc-muted);">2</span></div>
                    <div class="flex gap-4"><span style="color:var(--vc-muted);">3</span><span style="color:var(--vc-muted);"># Initialize a perfectly isolated grading environment</span></div>
                    <div class="flex gap-4"><span style="color:var(--vc-muted);">4</span><span class="text-[#B026FF]">async def</span> <span class="text-[#00F3FF]">evaluate_submission</span><span class="text-white">(code: str):</span></div>
                    <div class="flex gap-4"><span style="color:var(--vc-muted);">5</span><span class="text-white pl-8">env = </span><span class="text-[#B026FF]">await</span> <span class="text-white">Sandbox.create(</span></div>
                    <div class="flex gap-4"><span style="color:var(--vc-muted);">6</span><span class="text-white pl-16">runtime=</span><span class="text-[#00F3FF]">'python:3.11'</span><span class="text-white">,</span></div>
                    <div class="flex gap-4"><span style="color:var(--vc-muted);">7</span><span class="text-white pl-16">network=</span><span class="text-[#B026FF]">False</span></div>
                    <div class="flex gap-4"><span style="color:var(--vc-muted);">8</span><span class="text-white pl-8">)</span></div>
                    <div class="flex gap-4"><span style="color:var(--vc-muted);">9</span><span class="text-white pl-8">return </span><span class="text-[#B026FF]">await</span> <span class="text-white">env.execute(code)</span></div>
                </div>
            </div>
            
            <!-- Neon glow behind the editor -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#B026FF]/20 to-[#00F3FF]/20 blur-3xl -z-10 rounded-[2rem]"></div>
        </div>
    </main>

    <!-- Features Section -->
    <section class="relative z-10 w-full max-w-7xl mx-auto px-6 py-32 border-t" style="border-color:var(--vc-border);">
        <div class="text-center mb-20">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-white">Everything you need. Nothing you don't.</h2>
            <p class="text-lg" style="color:var(--vc-text-secondary);">Focus on teaching and learning, not infrastructure.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="vc-card p-8 rounded-2xl flex flex-col items-start text-left">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background:rgba(0,243,255,0.1); border:1px solid rgba(0,243,255,0.2);">
                    <svg class="w-6 h-6 text-[#00F3FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Isolated Sandboxes</h3>
                <p style="color:var(--vc-text-secondary);" class="leading-relaxed">Instant, ephemeral containers for every student. Securely execute code without risking the host system.</p>
            </div>

            <div class="vc-card p-8 rounded-2xl flex flex-col items-start text-left">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background:rgba(176,38,255,0.1); border:1px solid rgba(176,38,255,0.2);">
                    <svg class="w-6 h-6 text-[#B026FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Real-time Sync</h3>
                <p style="color:var(--vc-text-secondary);" class="leading-relaxed">Collaborate with sub-millisecond latency. Multiple cursors, shared terminals, and instant state synchronization.</p>
            </div>

            <div class="vc-card p-8 rounded-2xl flex flex-col items-start text-left">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1);">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Responsible AI</h3>
                <p style="color:var(--vc-text-secondary);" class="leading-relaxed">AI pair programming that guides instead of giving away answers. Fully auditable by faculty.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 py-10 text-center border-t mt-auto" style="border-color:var(--vc-border);">
        <p style="color:var(--vc-muted);" class="text-sm">© {{ date('Y') }} VisionLab Inc. All rights reserved.</p>
    </footer>

</body>
</html>
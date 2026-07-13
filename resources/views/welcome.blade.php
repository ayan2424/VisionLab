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
            background-color: #0A0A0F;
            color: white;
            font-family: 'Geist', -apple-system, sans-serif;
            overflow-x: hidden;
        }
        .font-mono {
            font-family: "JetBrains Mono", ui-monospace, monospace;
        }
        
        /* Neon grid background */
        .bg-grid-pattern {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
        }

        /* Copilot style glowing borders */
        .glow-border {
            position: relative;
        }
        .glow-border::before {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(180deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }
        .glow-border:hover::before {
            background: linear-gradient(180deg, #00F3FF, #B026FF);
            opacity: 0.5;
        }
    </style>
</head>
<body class="relative min-h-screen flex flex-col selection:bg-[#00F3FF]/30 selection:text-white">

    <!-- Ambient Background Glows -->
    <div class="fixed top-[-20%] left-[-10%] w-[60vw] h-[60vw] bg-[#B026FF]/10 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-20%] right-[-10%] w-[60vw] h-[60vw] bg-[#00F3FF]/10 rounded-full blur-[150px] pointer-events-none z-0"></div>
    
    <!-- Grid Pattern overlay -->
    <div class="fixed inset-0 bg-grid-pattern pointer-events-none z-0"></div>

    <!-- Navigation -->
    <x-frontend-header />

    <!-- Hero Section -->
    <main class="flex-1 flex flex-col items-center justify-center relative z-10 px-6 pt-40 pb-20 text-center">
        <!-- Badge -->
        <div class="mb-8 inline-flex items-center gap-2 rounded-full border border-[#00F3FF]/30 bg-[#00F3FF]/5 px-4 py-1.5 backdrop-blur-md transition-all hover:bg-[#00F3FF]/10 cursor-pointer shadow-[0_0_20px_rgba(0,243,255,0.1)]">
            <span class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#00F3FF] opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-[#00F3FF]"></span>
            </span>
            <span class="font-mono text-xs uppercase tracking-widest text-[#00F3FF]">VisionLab v1.0 is live</span>
        </div>

        <h1 class="max-w-4xl text-6xl md:text-8xl font-bold tracking-tighter mb-8 text-white leading-[1.1]">
            Command your <br />
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#00F3FF] via-white to-[#B026FF] animate-gradient-x">
                craft.
            </span>
        </h1>

        <p class="max-w-2xl text-lg md:text-xl mb-12 leading-relaxed text-white/60">
            The collaborative IDE built for research universities. Fully sandboxed workspaces, real-time sync, and responsibly AI-assisted learning.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
            @auth
            <a href="{{ route('dashboard') }}" class="group relative inline-flex items-center justify-center px-8 py-4 text-base font-bold text-[#0A0A0F] transition-all rounded-full overflow-hidden bg-[#00F3FF] hover:bg-white shadow-[0_0_40px_rgba(0,243,255,0.4)]">
                <span class="relative flex items-center gap-2">
                    Open Dashboard
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
            </a>
            @else
            <a href="{{ route('register') }}" class="group relative inline-flex items-center justify-center px-8 py-4 text-base font-bold text-[#0A0A0F] transition-all rounded-full overflow-hidden bg-[#00F3FF] hover:bg-white shadow-[0_0_40px_rgba(0,243,255,0.4)]">
                <span class="relative flex items-center gap-2">
                    Start Building Free
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
            </a>
            @endauth
        </div>

        <!-- Hero Graphic (Glass Panel / Editor Mockup) -->
        <div class="mt-24 w-full max-w-5xl rounded-2xl relative group perspective-1000">
            <!-- Neon glow behind the editor -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#B026FF]/30 via-[#00F3FF]/20 to-[#B026FF]/30 blur-[80px] -z-10 rounded-[2rem] opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
            
            <div class="w-full bg-[#0A0A0F]/80 backdrop-blur-xl rounded-xl overflow-hidden border border-white/10 relative z-10 shadow-2xl transform transition-transform duration-700 hover:scale-[1.01]">
                <div class="flex items-center justify-between px-4 py-3 border-b border-white/5 bg-white/5">
                    <div class="flex gap-2">
                        <div class="w-3 h-3 rounded-full bg-[#FF5F56]"></div>
                        <div class="w-3 h-3 rounded-full bg-[#FFBD2E]"></div>
                        <div class="w-3 h-3 rounded-full bg-[#27C93F]"></div>
                    </div>
                    <div class="text-xs font-mono text-white/40">workspace.py — VisionLab</div>
                    <div class="w-16"></div> <!-- Spacer for centering -->
                </div>
                <div class="p-6 md:p-8 text-left font-mono text-sm md:text-base leading-relaxed overflow-x-auto">
                    <div class="flex gap-4 hover:bg-white/5 px-2 py-0.5 rounded transition-colors"><span class="text-white/20 select-none">1</span><span class="text-[#B026FF]">from</span> <span class="text-white">visionlab</span> <span class="text-[#B026FF]">import</span> <span class="text-white">Sandbox</span></div>
                    <div class="flex gap-4 px-2 py-0.5"><span class="text-white/20 select-none">2</span></div>
                    <div class="flex gap-4 hover:bg-white/5 px-2 py-0.5 rounded transition-colors"><span class="text-white/20 select-none">3</span><span class="text-[#00F3FF]/70 italic"># Initialize a perfectly isolated grading environment</span></div>
                    <div class="flex gap-4 hover:bg-white/5 px-2 py-0.5 rounded transition-colors"><span class="text-white/20 select-none">4</span><span class="text-[#B026FF]">async def</span> <span class="text-[#00F3FF]">evaluate_submission</span><span class="text-white">(code: str):</span></div>
                    <div class="flex gap-4 hover:bg-white/5 px-2 py-0.5 rounded transition-colors"><span class="text-white/20 select-none">5</span><span class="text-white pl-8">env = </span><span class="text-[#B026FF]">await</span> <span class="text-white">Sandbox.create(</span></div>
                    <div class="flex gap-4 hover:bg-white/5 px-2 py-0.5 rounded transition-colors"><span class="text-white/20 select-none">6</span><span class="text-white pl-16">runtime=</span><span class="text-[#00F3FF]">'python:3.11'</span><span class="text-white">,</span></div>
                    <div class="flex gap-4 hover:bg-white/5 px-2 py-0.5 rounded transition-colors"><span class="text-white/20 select-none">7</span><span class="text-white pl-16">network=</span><span class="text-[#B026FF]">False</span></div>
                    <div class="flex gap-4 hover:bg-white/5 px-2 py-0.5 rounded transition-colors"><span class="text-white/20 select-none">8</span><span class="text-white pl-8">)</span></div>
                    <div class="flex gap-4 hover:bg-white/5 px-2 py-0.5 rounded transition-colors"><span class="text-white/20 select-none">9</span><span class="text-white pl-8 border-l border-[#00F3FF] animate-pulse h-5"></span></div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bento Grid Features Section -->
    <section class="relative z-10 w-full max-w-7xl mx-auto px-6 py-32">
        <div class="text-center mb-20">
            <h2 class="text-4xl md:text-5xl font-bold text-white tracking-tight mb-6">Code, command, and collaborate</h2>
            <p class="text-lg text-white/50 max-w-2xl mx-auto">Everything you need to run programming courses at scale. Built for the modern research university.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- 1. Cloud Workspace -->
            <div class="md:col-span-2 glow-border bg-[#0F0F15] rounded-3xl p-8 flex flex-col justify-between overflow-hidden relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-[#00F3FF]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#00F3FF] bg-[#00F3FF]/10 border border-[#00F3FF]/20 mb-6">
                        Cloud Workspace
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Full-fledged IDE in the Browser</h3>
                    <p class="text-white/60 mb-8 max-w-md">No local setup required. Every student gets a dedicated, isolated sandbox powered by VS Code technology. Real-time syncing allows multiple users to collaborate seamlessly.</p>
                </div>
                <!-- Mini mockup -->
                <div class="relative w-full h-48 bg-[#0A0A0F] rounded-t-xl border border-white/10 border-b-0 translate-y-8 group-hover:translate-y-4 transition-transform duration-500 overflow-hidden shadow-2xl">
                    <img src="/assets/workspace-preview.png" alt="Workspace IDE" class="w-full h-full object-cover opacity-80" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'400\'><rect width=\'800\' height=\'400\' fill=\'%23111\'/><text x=\'50%\' y=\'50%\' fill=\'%23555\' text-anchor=\'middle\' font-family=\'sans-serif\'>Workspace Preview</text></svg>'">
                </div>
            </div>

            <!-- 2. Smart LMS -->
            <div class="md:col-span-1 glow-border bg-[#0F0F15] rounded-3xl p-8 flex flex-col justify-between relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-[#B026FF]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#B026FF] bg-[#B026FF]/10 border border-[#B026FF]/20 mb-6">
                        Smart LMS
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Course Management</h3>
                    <p class="text-white/60">Automated grading, queuing systems, and real-time progress tracking.</p>
                </div>
                <!-- Visual Element -->
                <div class="mt-8 space-y-3">
                    <div class="h-10 rounded-lg bg-white/5 border border-white/5 flex items-center px-4">
                        <div class="w-2 h-2 rounded-full bg-green-500 mr-3"></div><div class="h-2 w-1/2 bg-white/20 rounded"></div>
                    </div>
                    <div class="h-10 rounded-lg bg-white/5 border border-white/5 flex items-center px-4">
                        <div class="w-2 h-2 rounded-full bg-yellow-500 mr-3"></div><div class="h-2 w-3/4 bg-white/20 rounded"></div>
                    </div>
                </div>
            </div>

            <!-- 3. Gamification -->
            <div class="md:col-span-1 glow-border bg-[#0F0F15] rounded-3xl p-8 flex flex-col justify-between relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-orange-400 bg-orange-500/10 border border-orange-500/20 mb-6">
                        Gamification
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Level Up Learning</h3>
                    <p class="text-white/60">Earn XP, unlock badges, and climb the leaderboard as you complete assignments.</p>
                </div>
                <!-- Visual Element -->
                <div class="mt-8 flex items-center justify-center">
                    <div class="relative w-24 h-24 flex items-center justify-center">
                        <div class="absolute inset-0 border-4 border-orange-500/30 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-orange-500 rounded-full border-t-transparent animate-spin" style="animation-duration: 3s;"></div>
                        <span class="font-bold text-2xl text-white">42<span class="text-xs text-orange-400">xp</span></span>
                    </div>
                </div>
            </div>

            <!-- 4. ERP -->
            <div class="md:col-span-2 glow-border bg-[#0F0F15] rounded-3xl p-8 flex flex-col justify-between overflow-hidden relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10 flex flex-col md:flex-row gap-8 items-center">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-white/80 bg-white/5 border border-white/10 mb-6">
                            Institute ERP
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Centralized Administration</h3>
                        <p class="text-white/60">Manage campuses, departments, and quotas from a single pane of glass.</p>
                    </div>
                    <div class="flex-1 w-full bg-white/5 rounded-xl border border-white/10 p-4 transform group-hover:scale-105 transition-transform duration-500">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center"><span class="text-xs text-white/50">Active Nodes</span><span class="text-xs font-mono text-[#00F3FF]">342</span></div>
                            <div class="w-full bg-white/10 rounded-full h-1"><div class="bg-[#00F3FF] h-1 rounded-full w-[70%]"></div></div>
                            <div class="flex justify-between items-center pt-2"><span class="text-xs text-white/50">CPU Usage</span><span class="text-xs font-mono text-[#B026FF]">45%</span></div>
                            <div class="w-full bg-white/10 rounded-full h-1"><div class="bg-[#B026FF] h-1 rounded-full w-[45%]"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="relative z-10 w-full max-w-7xl mx-auto px-6 py-32 border-t border-white/5">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-white tracking-tight">Trusted by Top Educators</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glow-border bg-[#0F0F15] p-8 rounded-2xl">
                <p class="text-white/60 mb-6 italic">"VisionLab completely eliminated 'it works on my machine' problems in our python cohorts. The grading system is exceptionally robust."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#B026FF]/20 border border-[#B026FF]/30 flex items-center justify-center text-white font-bold text-sm">AJ</div>
                    <div>
                        <div class="text-sm font-bold text-white">Prof. Andrew Johnson</div>
                        <div class="text-xs text-white/40">Computer Science Dept.</div>
                    </div>
                </div>
            </div>
            <div class="glow-border bg-[#0F0F15] p-8 rounded-2xl relative">
                <div class="absolute -top-3 right-6 bg-gradient-to-r from-[#00F3FF] to-[#B026FF] text-[#0A0A0F] text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded-full shadow-[0_0_15px_rgba(0,243,255,0.3)]">Top Rated</div>
                <p class="text-white/60 mb-6 italic">"The AI agent doesn't just write code for me, it explains where I went wrong. It's like having a tutor available 24/7."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#00F3FF]/20 border border-[#00F3FF]/30 flex items-center justify-center text-white font-bold text-sm">SK</div>
                    <div>
                        <div class="text-sm font-bold text-white">Sarah Khan</div>
                        <div class="text-xs text-white/40">Student, Batch 2026</div>
                    </div>
                </div>
            </div>
            <div class="glow-border bg-[#0F0F15] p-8 rounded-2xl">
                <p class="text-white/60 mb-6 italic">"The centralized ERP handles our entire institute. Managing enrollments and sandboxing in one system saves us hundreds of hours."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white font-bold text-sm">DM</div>
                    <div>
                        <div class="text-sm font-bold text-white">Dr. David Miller</div>
                        <div class="text-xs text-white/40">Dean of Engineering</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Integration -->
    <x-frontend-footer />

</body>
</html>
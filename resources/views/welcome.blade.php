<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="The collaborative IDE built for research universities. Sandboxed, audited, and AI-assisted.">
    <title>VisionLab — Collaborative coding for universities</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #040d21; /* Deep GitHub dark background */
            color: white;
            font-family: 'Inter', -apple-system, sans-serif;
            overflow-x: hidden;
        }
        .font-mono {
            font-family: "JetBrains Mono", ui-monospace, monospace;
        }

        /* Hero Text Gradient */
        .text-gradient-hero {
            background: linear-gradient(to right, #ffffff, #a3a3a3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Buttons */
        .btn-green {
            background-color: #238636;
            color: white;
            border: 1px solid rgba(240, 246, 252, 0.1);
        }
        .btn-green:hover {
            background-color: #2ea043;
        }
        .btn-outline {
            background-color: transparent;
            color: white;
            border: 1px solid rgba(240, 246, 252, 0.1);
        }
        .btn-outline:hover {
            border-color: rgba(240, 246, 252, 0.3);
        }

        /* Heavy Gradient Backgrounds for Mockups */
        .gradient-box-purple {
            background: linear-gradient(135deg, #7928CA, #FF0080);
            position: relative;
            overflow: hidden;
        }
        .gradient-box-green {
            background: linear-gradient(135deg, #10B981, #047857);
            position: relative;
            overflow: hidden;
        }
        .gradient-box-blue {
            background: linear-gradient(135deg, #00F3FF, #0070F3);
            position: relative;
            overflow: hidden;
        }

        /* Dotted halftone pattern over gradients */
        .halftone-overlay {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.15) 2px, transparent 2px);
            background-size: 16px 16px;
            pointer-events: none;
        }

        /* The glowing 3D pipeline divider */
        .pipeline-divider {
            height: 100px;
            width: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='100%25' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 50 Q 250 0, 500 50 T 1000 50' stroke='url(%23grad1)' stroke-width='4' fill='none' /%3E%3Cdefs%3E%3ClinearGradient id='grad1' x1='0%25' y1='0%25' x2='100%25' y2='0%25'%3E%3Cstop offset='0%25' style='stop-color:%2310B981;stop-opacity:1' /%3E%3Cstop offset='100%25' style='stop-color:%2300F3FF;stop-opacity:1' /%3E%3C/linearGradient%3E%3C/defs%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            filter: drop-shadow(0 0 20px rgba(16, 185, 129, 0.5));
            margin: 4rem 0;
        }

        /* 3 Column Top Border Glow */
        .card-top-glow {
            border-top: 1px solid rgba(255,255,255,0.1);
            position: relative;
        }
        .card-top-glow::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            width: 30%;
            height: 1px;
            background: linear-gradient(90deg, #10B981, transparent);
        }
        
        /* Typography fixes */
        .text-light-gray {
            color: #7d8590;
        }
    </style>
</head>
<body class="relative min-h-screen flex flex-col selection:bg-[#238636]/30 selection:text-white">

    <!-- Global Header -->
    <x-frontend-header />

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 px-6 lg:px-8 max-w-7xl mx-auto w-full flex flex-col lg:flex-row items-center justify-between gap-12 overflow-hidden">
        <!-- Text Column -->
        <div class="flex-1 z-10">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-white/10 bg-white/5 mb-6">
                <span class="w-2 h-2 rounded-full bg-[#10B981] animate-pulse"></span>
                <span class="text-sm font-medium text-white/80">VisionLab 1.0 is live</span>
            </div>
            
            <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight mb-6 text-gradient-hero leading-[1.1]">
                Command your craft
            </h1>
            
            <p class="text-xl text-light-gray mb-10 max-w-xl leading-relaxed">
                The collaborative IDE built for research universities. Fully sandboxed workspaces, real-time sync, and responsibly AI-assisted learning.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4">
                @auth
                <a href="{{ route('dashboard') }}" class="btn-green px-6 py-3 rounded-md font-semibold text-center transition-all">
                    Open Dashboard
                </a>
                @else
                <a href="{{ route('register') }}" class="btn-green px-6 py-3 rounded-md font-semibold text-center transition-all">
                    Start Building Free
                </a>
                <a href="{{ route('contact') }}" class="btn-outline px-6 py-3 rounded-md font-semibold text-center transition-all">
                    Contact Sales
                </a>
                @endauth
            </div>
        </div>

        <!-- 3D Graphic Column (Right side of Hero) -->
        <div class="flex-1 relative z-10 flex justify-center lg:justify-end">
            <!-- Simulating the Copilot 3D glowing asset -->
            <div class="relative w-72 h-72 lg:w-96 lg:h-96">
                <div class="absolute inset-0 bg-gradient-to-br from-[#00F3FF] to-[#7928CA] rounded-[3rem] blur-3xl opacity-40 animate-pulse"></div>
                <div class="absolute inset-4 bg-[#0d1117] rounded-[2.5rem] border border-white/10 flex items-center justify-center shadow-2xl overflow-hidden">
                    <div class="absolute inset-0 halftone-overlay"></div>
                    <img src="{{ asset('icons/logo.svg') }}" alt="VisionLab Glow" class="w-32 h-32 relative z-10 drop-shadow-[0_0_30px_rgba(0,243,255,0.8)]">
                </div>
            </div>
        </div>
    </section>

    <!-- Massive Gradient Mockup Section -->
    <section class="w-full relative mt-12 mb-32">
        <div class="max-w-[1400px] mx-auto px-4 lg:px-8">
            <div class="gradient-box-purple rounded-[2rem] p-4 md:p-8 lg:p-12 shadow-[0_0_80px_rgba(121,40,202,0.3)]">
                <div class="halftone-overlay"></div>
                <!-- IDE Mockup Window -->
                <div class="relative z-10 w-full bg-[#0d1117] rounded-xl border border-white/20 shadow-2xl overflow-hidden">
                    <!-- Fake Mac Window Header -->
                    <div class="bg-[#161b22] px-4 py-3 flex items-center border-b border-white/10">
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#FF5F56]"></div>
                            <div class="w-3 h-3 rounded-full bg-[#FFBD2E]"></div>
                            <div class="w-3 h-3 rounded-full bg-[#27C93F]"></div>
                        </div>
                        <div class="mx-auto text-xs font-mono text-white/50">workspace.py — VisionLab</div>
                    </div>
                    <!-- Mockup Content -->
                    <img src="/assets/workspace-preview.png" alt="VisionLab Interface" class="w-full h-auto object-cover opacity-90" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'1200\' height=\'600\'><rect width=\'1200\' height=\'600\' fill=\'%230d1117\'/><text x=\'50%\' y=\'50%\' fill=\'%237d8590\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'24\'>IDE Workspace Preview</text></svg>'">
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By / Stats Strip -->
    <section class="max-w-7xl mx-auto px-6 lg:px-8 py-12 mb-20 border-y border-white/5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-white mb-2">50+</div>
                <div class="text-sm text-light-gray font-medium uppercase tracking-widest">Universities</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-2">10k+</div>
                <div class="text-sm text-light-gray font-medium uppercase tracking-widest">Students</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-2">1M+</div>
                <div class="text-sm text-light-gray font-medium uppercase tracking-widest">Sandboxes Run</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-2">0</div>
                <div class="text-sm text-light-gray font-medium uppercase tracking-widest">Security Breaches</div>
            </div>
        </div>
    </section>

    <!-- Features Heading -->
    <div class="text-center mb-24 max-w-3xl mx-auto px-6">
        <h2 class="text-4xl md:text-5xl font-bold text-white tracking-tight mb-6">Code, command, and collaborate</h2>
        <p class="text-xl text-light-gray">A complete ecosystem for teaching programming, with tools that work the way developers actually work.</p>
    </div>

    <!-- Alternating Feature Rows (Zigzag Layout) -->
    <section class="max-w-7xl mx-auto px-6 lg:px-8 space-y-40 mb-32">
        
        <!-- Row 1: Cloud Workspace (Text Left, Image Right) -->
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="flex-1 lg:pr-12">
                <h3 class="text-3xl font-bold text-white mb-4">Cloud Workspace</h3>
                <p class="text-lg text-light-gray mb-6 leading-relaxed">
                    No local setup required. Every student gets a dedicated, isolated sandbox powered by VS Code technology. Real-time syncing allows multiple users to collaborate seamlessly on complex architectures.
                </p>
                <a href="{{ route('features') }}" class="text-[#00F3FF] font-semibold hover:underline flex items-center gap-1">
                    Explore Workspaces
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="flex-1 w-full">
                <div class="gradient-box-purple rounded-2xl p-6 shadow-2xl transform rotate-1 hover:rotate-0 transition-transform duration-500">
                    <div class="halftone-overlay"></div>
                    <div class="relative z-10 bg-[#0d1117] rounded-lg border border-white/10 p-4 aspect-video flex flex-col shadow-inner">
                        <div class="flex gap-2 mb-4 border-b border-white/5 pb-2">
                            <div class="h-2 w-16 bg-white/20 rounded"></div><div class="h-2 w-8 bg-white/10 rounded"></div>
                        </div>
                        <div class="flex-1 flex flex-col gap-2">
                            <div class="flex gap-4"><span class="text-white/20 text-xs">1</span><div class="h-3 w-1/2 bg-white/20 rounded"></div></div>
                            <div class="flex gap-4"><span class="text-white/20 text-xs">2</span><div class="h-3 w-3/4 bg-[#00F3FF]/50 rounded"></div></div>
                            <div class="flex gap-4"><span class="text-white/20 text-xs">3</span><div class="h-3 w-1/3 bg-white/20 rounded"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Smart LMS (Image Left, Text Right) -->
        <div class="flex flex-col-reverse lg:flex-row items-center gap-16">
            <div class="flex-1 w-full">
                <div class="gradient-box-green rounded-2xl p-6 shadow-2xl transform -rotate-1 hover:rotate-0 transition-transform duration-500">
                    <div class="halftone-overlay"></div>
                    <div class="relative z-10 bg-[#0d1117] rounded-lg border border-white/10 p-4 aspect-video flex flex-col gap-4 shadow-inner">
                        <div class="flex items-center justify-between bg-white/5 p-3 rounded border border-white/5">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded bg-[#10B981]/20 flex items-center justify-center"><div class="w-2 h-2 rounded-full bg-[#10B981]"></div></div>
                                <div class="h-3 w-24 bg-white/60 rounded"></div>
                            </div>
                            <div class="h-4 w-12 bg-[#10B981]/80 rounded-full"></div>
                        </div>
                        <div class="flex items-center justify-between bg-white/5 p-3 rounded border border-white/5">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded bg-[#FFBD2E]/20 flex items-center justify-center"><div class="w-2 h-2 rounded-full bg-[#FFBD2E]"></div></div>
                                <div class="h-3 w-32 bg-white/60 rounded"></div>
                            </div>
                            <div class="h-4 w-12 bg-white/20 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-1 lg:pl-12">
                <h3 class="text-3xl font-bold text-white mb-4">Smart LMS & Gamification</h3>
                <p class="text-lg text-light-gray mb-6 leading-relaxed">
                    Automated grading, secure queuing systems, and real-time progress tracking. Level up your learning with developer-centric gamification: earn XP, unlock kernel titles, and claim badges for writing clean code.
                </p>
                <a href="#" class="text-[#10B981] font-semibold hover:underline flex items-center gap-1">
                    See LMS Features
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Row 3: Institute ERP (Text Left, Image Right) -->
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="flex-1 lg:pr-12">
                <h3 class="text-3xl font-bold text-white mb-4">Centralized ERP</h3>
                <p class="text-lg text-light-gray mb-6 leading-relaxed">
                    Manage campuses, departments, and computational quotas from a single pane of glass. Keep track of server health, active nodes, and assignment workloads effortlessly.
                </p>
                <a href="#" class="text-white font-semibold hover:underline flex items-center gap-1">
                    Discover Administration
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="flex-1 w-full">
                <div class="gradient-box-blue rounded-2xl p-6 shadow-2xl transform rotate-1 hover:rotate-0 transition-transform duration-500">
                    <div class="halftone-overlay"></div>
                    <div class="relative z-10 bg-[#0d1117] rounded-lg border border-white/10 p-6 aspect-video shadow-inner">
                        <div class="flex justify-between items-end mb-4">
                            <div class="space-y-1">
                                <div class="text-xs text-white/50 uppercase">Active Nodes</div>
                                <div class="text-2xl font-mono text-white">342</div>
                            </div>
                            <div class="w-16 h-12 flex items-end gap-1">
                                <div class="flex-1 bg-[#00F3FF]/40 h-1/3 rounded-t"></div>
                                <div class="flex-1 bg-[#00F3FF]/60 h-2/3 rounded-t"></div>
                                <div class="flex-1 bg-[#00F3FF] h-full rounded-t"></div>
                            </div>
                        </div>
                        <div class="w-full h-px bg-white/10 mb-4"></div>
                        <div class="space-y-3">
                            <div class="w-full bg-white/5 rounded-full h-2"><div class="bg-white/80 h-2 rounded-full w-[70%]"></div></div>
                            <div class="w-full bg-white/5 rounded-full h-2"><div class="bg-white/40 h-2 rounded-full w-[45%]"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Glowing Pipeline Divider -->
    <div class="pipeline-divider"></div>

    <!-- Tailored for your organization (3 Columns) -->
    <section class="max-w-7xl mx-auto px-6 lg:px-8 py-20 mb-20">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-white tracking-tight mb-4">Tailored for your institution</h2>
            <p class="text-light-gray max-w-2xl mx-auto">VisionLab adapts to your university's security and scale requirements, ensuring a safe learning environment.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-[#0d1117] p-8 rounded-xl border border-white/5 card-top-glow">
                <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center mb-6">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Enterprise Security</h3>
                <p class="text-light-gray text-sm leading-relaxed">
                    Every student workspace runs in an isolated Docker container with strict network policies. Code execution is completely sandboxed.
                </p>
            </div>
            
            <div class="bg-[#0d1117] p-8 rounded-xl border border-white/5 card-top-glow">
                <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center mb-6">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Infinite Scale</h3>
                <p class="text-light-gray text-sm leading-relaxed">
                    Designed to handle hundreds of concurrent users. Node pools auto-scale based on assignment deadlines and active connections.
                </p>
            </div>
            
            <div class="bg-[#0d1117] p-8 rounded-xl border border-white/5 card-top-glow">
                <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center mb-6">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Responsibly AI</h3>
                <p class="text-light-gray text-sm leading-relaxed">
                    Our AI assistant acts as a tutor, not an answer key. It guides students to find solutions themselves, preventing plagiarism.
                </p>
            </div>
        </div>
    </section>

    <!-- Global Footer -->
    <x-frontend-footer />

</body>
</html>
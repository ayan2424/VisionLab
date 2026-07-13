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
            position: relative;
        }
        /* Subtle background noise/grain for premium feel */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 50;
        }

        .font-mono {
            font-family: "JetBrains Mono", ui-monospace, monospace;
        }

        /* Hero Text Gradient */
        .text-gradient-hero {
            background: linear-gradient(180deg, #ffffff 0%, #a3a3a3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 40px rgba(255,255,255,0.1);
        }

        /* Premium Buttons */
        .btn-green {
            background-color: #238636;
            color: white;
            border: 1px solid rgba(240, 246, 252, 0.1);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.1), 0 0 20px rgba(35,134,54,0.3);
        }
        .btn-green:hover {
            background-color: #2ea043;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.1), 0 0 30px rgba(35,134,54,0.5);
        }
        .btn-outline {
            background-color: rgba(255,255,255,0.05);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .btn-outline:hover {
            background-color: rgba(255,255,255,0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Heavy Gradient Backgrounds for Mockups */
        .gradient-box-purple {
            background: linear-gradient(135deg, #7928CA 0%, #FF0080 100%);
            position: relative;
            overflow: hidden;
        }
        .gradient-box-green {
            background: linear-gradient(135deg, #10B981 0%, #047857 100%);
            position: relative;
            overflow: hidden;
        }
        .gradient-box-blue {
            background: linear-gradient(135deg, #00F3FF 0%, #0070F3 100%);
            position: relative;
            overflow: hidden;
        }

        /* Glassmorphic Inner Panels */
        .glass-panel {
            background: rgba(13, 17, 23, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.1), 0 20px 40px rgba(0,0,0,0.5);
        }

        /* Floating Animations */
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(1deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        @keyframes float-reverse {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(15px) rotate(-1deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .animate-float { animation: float 8s ease-in-out infinite; }
        .animate-float-delayed { animation: float-reverse 9s ease-in-out infinite; }

        /* Dotted halftone pattern over gradients */
        .halftone-overlay {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.2) 1.5px, transparent 1.5px);
            background-size: 14px 14px;
            pointer-events: none;
            opacity: 0.8;
            mask-image: radial-gradient(ellipse at center, black 20%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 20%, transparent 80%);
        }

        /* The glowing 3D pipeline divider */
        .pipeline-divider {
            height: 120px;
            width: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='100%25' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 50 Q 250 0, 500 50 T 1000 50' stroke='url(%23grad1)' stroke-width='3' fill='none' /%3E%3Cdefs%3E%3ClinearGradient id='grad1' x1='0%25' y1='0%25' x2='100%25' y2='0%25'%3E%3Cstop offset='0%25' style='stop-color:%2310B981;stop-opacity:1' /%3E%3Cstop offset='100%25' style='stop-color:%2300F3FF;stop-opacity:1' /%3E%3C/linearGradient%3E%3C/defs%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            filter: drop-shadow(0 0 30px rgba(0, 243, 255, 0.4));
            margin: 5rem 0;
        }

        /* 3 Column Top Border Glow */
        .card-top-glow {
            border-top: 1px solid rgba(255,255,255,0.05);
            position: relative;
            background: linear-gradient(180deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0) 100%);
            transition: all 0.3s ease;
        }
        .card-top-glow:hover {
            transform: translateY(-5px);
            background: linear-gradient(180deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0) 100%);
        }
        .card-top-glow::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            width: 50%;
            height: 1px;
            background: linear-gradient(90deg, #10B981, transparent);
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }
        .card-top-glow:hover::before {
            opacity: 1;
        }
        
        .text-light-gray {
            color: #8b949e;
        }
    </style>
</head>
<body class="relative min-h-screen flex flex-col selection:bg-[#238636]/30 selection:text-white">

    <!-- Ambient Glows -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[80vw] h-[50vh] bg-[#238636]/10 blur-[150px] pointer-events-none rounded-full"></div>

    <!-- Global Header -->
    <x-frontend-header />

    <!-- Hero Section -->
    <section class="relative pt-40 pb-24 px-6 lg:px-8 max-w-[1400px] mx-auto w-full flex flex-col lg:flex-row items-center justify-between gap-16 overflow-hidden">
        <!-- Text Column -->
        <div class="flex-1 z-10">
            <!-- Premium Badge -->
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full border border-white/10 bg-white/5 mb-8 backdrop-blur-md shadow-[0_0_20px_rgba(255,255,255,0.05)] hover:bg-white/10 transition-colors cursor-default">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#10B981] opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-[#10B981]"></span>
                </span>
                <span class="text-sm font-semibold text-white/90">VisionLab 1.0 is live</span>
            </div>
            
            <h1 class="text-6xl lg:text-[5.5rem] font-extrabold tracking-tight mb-8 text-gradient-hero leading-[1.05]">
                Command your craft
            </h1>
            
            <p class="text-xl text-light-gray mb-12 max-w-xl leading-relaxed font-medium">
                The collaborative IDE built for research universities. Fully sandboxed workspaces, real-time sync, and responsibly AI-assisted learning.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-5">
                @auth
                <a href="{{ route('dashboard') }}" class="btn-green px-8 py-4 rounded-lg font-bold text-center transition-all text-base flex items-center justify-center gap-2">
                    Open Dashboard
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                @else
                <a href="{{ route('register') }}" class="btn-green px-8 py-4 rounded-lg font-bold text-center transition-all text-base flex items-center justify-center gap-2">
                    Start Building Free
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="{{ route('contact') }}" class="btn-outline px-8 py-4 rounded-lg font-bold text-center transition-all text-base">
                    Contact Sales
                </a>
                @endauth
            </div>
        </div>

        <!-- 3D Graphic Column (Right side of Hero) -->
        <div class="flex-1 relative z-10 flex justify-center lg:justify-end min-h-[400px]">
            <!-- Sleek Glowing Orb & Floating Elements -->
            <div class="relative w-full h-full flex items-center justify-center">
                <!-- Massive Background Glow -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-[#00F3FF] via-[#7928CA] to-[#FF0080] rounded-full blur-[100px] opacity-40 animate-pulse"></div>
                
                <!-- Center Glass Orb -->
                <div class="relative z-10 w-48 h-48 rounded-full glass-panel flex items-center justify-center animate-float border-white/20">
                    <img src="{{ asset('icons/logo.svg') }}" alt="VisionLab Glow" class="w-24 h-24 relative z-10 drop-shadow-[0_0_40px_rgba(255,255,255,0.8)]">
                </div>

                <!-- Floating Code Card 1 -->
                <div class="absolute -left-10 top-10 glass-panel rounded-lg p-3 animate-float-delayed border-white/10 shadow-2xl">
                    <div class="flex gap-2 mb-2">
                        <div class="w-2 h-2 rounded-full bg-[#FF5F56]"></div><div class="w-2 h-2 rounded-full bg-[#FFBD2E]"></div><div class="w-2 h-2 rounded-full bg-[#27C93F]"></div>
                    </div>
                    <div class="font-mono text-xs text-white/70">
                        <span class="text-[#FF0080]">import</span> visionlab<br>
                        env = visionlab.Sandbox()
                    </div>
                </div>

                <!-- Floating Success Card 2 -->
                <div class="absolute -right-5 bottom-10 glass-panel rounded-lg p-3 animate-float border-[#10B981]/30 shadow-2xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#10B981]/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#10B981]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="font-mono text-xs text-white">
                        <span class="font-bold">Build passing</span><br>
                        <span class="text-white/50">14.2s execution</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Massive Gradient Mockup Section -->
    <section class="w-full relative mt-8 mb-32">
        <div class="max-w-[1400px] mx-auto px-4 lg:px-8">
            <div class="gradient-box-purple rounded-[2rem] p-4 md:p-8 lg:p-12 shadow-[0_0_100px_rgba(121,40,202,0.4)] border border-white/10">
                <div class="halftone-overlay"></div>
                <!-- IDE Mockup Window -->
                <div class="relative z-10 w-full glass-panel rounded-xl overflow-hidden transform hover:scale-[1.01] transition-transform duration-700">
                    <!-- Fake Mac Window Header -->
                    <div class="bg-[#040d21]/80 px-4 py-4 flex items-center border-b border-white/10 backdrop-blur-md">
                        <div class="flex gap-2">
                            <div class="w-3.5 h-3.5 rounded-full bg-[#FF5F56] shadow-inner"></div>
                            <div class="w-3.5 h-3.5 rounded-full bg-[#FFBD2E] shadow-inner"></div>
                            <div class="w-3.5 h-3.5 rounded-full bg-[#27C93F] shadow-inner"></div>
                        </div>
                        <div class="mx-auto text-xs font-mono text-white/60 tracking-wider">workspace.py — VisionLab</div>
                    </div>
                    <!-- Mockup Content -->
                    <img src="/assets/workspace-preview.png" alt="VisionLab Interface" class="w-full h-auto object-cover opacity-95" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'1200\' height=\'600\'><rect width=\'1200\' height=\'600\' fill=\'%230d1117\'/><text x=\'50%\' y=\'50%\' fill=\'%237d8590\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'24\' font-weight=\'bold\' opacity=\'0.3\'>IDE Workspace Preview</text></svg>'">
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By / Stats Strip -->
    <section class="max-w-[1400px] mx-auto px-6 lg:px-8 py-16 mb-32 border-y border-white/10 bg-white/[0.02]">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="flex flex-col items-center">
                <div class="text-5xl font-extrabold text-white mb-3 tracking-tight">50<span class="text-[#10B981]">+</span></div>
                <div class="text-sm text-light-gray font-bold uppercase tracking-widest">Universities</div>
            </div>
            <div class="flex flex-col items-center">
                <div class="text-5xl font-extrabold text-white mb-3 tracking-tight">10k<span class="text-[#00F3FF]">+</span></div>
                <div class="text-sm text-light-gray font-bold uppercase tracking-widest">Students</div>
            </div>
            <div class="flex flex-col items-center">
                <div class="text-5xl font-extrabold text-white mb-3 tracking-tight">1M<span class="text-[#FF0080]">+</span></div>
                <div class="text-sm text-light-gray font-bold uppercase tracking-widest">Sandboxes Run</div>
            </div>
            <div class="flex flex-col items-center">
                <div class="text-5xl font-extrabold text-white mb-3 tracking-tight">0</div>
                <div class="text-sm text-light-gray font-bold uppercase tracking-widest">Security Breaches</div>
            </div>
        </div>
    </section>

    <!-- Features Heading -->
    <div class="text-center mb-24 max-w-4xl mx-auto px-6">
        <h2 class="text-5xl md:text-6xl font-extrabold text-white tracking-tight mb-8">Code, command, and collaborate</h2>
        <p class="text-xl text-light-gray font-medium">A complete ecosystem for teaching programming, with tools that work the way developers actually work.</p>
    </div>

    <!-- Alternating Feature Rows (Zigzag Layout) -->
    <section class="max-w-[1400px] mx-auto px-6 lg:px-8 space-y-40 mb-32">
        
        <!-- Row 1: Cloud Workspace (Text Left, Image Right) -->
        <div class="flex flex-col lg:flex-row items-center gap-20">
            <div class="flex-1 lg:pr-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-[#7928CA]/30 bg-[#7928CA]/10 mb-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#d884ff]">Feature 01</span>
                </div>
                <h3 class="text-4xl font-bold text-white mb-6 tracking-tight">Cloud Workspace</h3>
                <p class="text-xl text-light-gray mb-8 leading-relaxed">
                    No local setup required. Every student gets a dedicated, isolated sandbox powered by VS Code technology. Real-time syncing allows multiple users to collaborate seamlessly on complex architectures.
                </p>
                <a href="{{ route('features') }}" class="text-white font-semibold hover:text-[#d884ff] transition-colors flex items-center gap-2 text-lg">
                    Explore Workspaces
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            <div class="flex-1 w-full relative">
                <div class="gradient-box-purple rounded-[2rem] p-6 lg:p-8 shadow-2xl transform rotate-1 hover:rotate-0 transition-transform duration-700">
                    <div class="halftone-overlay"></div>
                    <div class="relative z-10 glass-panel rounded-xl p-4 aspect-video flex flex-col shadow-inner">
                        <div class="flex gap-2 mb-4 border-b border-white/10 pb-3">
                            <div class="h-2.5 w-16 bg-white/20 rounded-full"></div><div class="h-2.5 w-8 bg-white/10 rounded-full"></div>
                        </div>
                        <div class="flex-1 flex flex-col gap-3 mt-2">
                            <div class="flex gap-4 items-center"><span class="text-white/20 text-xs font-mono">1</span><div class="h-4 w-1/2 bg-white/20 rounded"></div></div>
                            <div class="flex gap-4 items-center"><span class="text-white/20 text-xs font-mono">2</span><div class="h-4 w-3/4 bg-[#FF0080]/60 rounded"></div></div>
                            <div class="flex gap-4 items-center"><span class="text-white/20 text-xs font-mono">3</span><div class="h-4 w-1/3 bg-white/20 rounded"></div></div>
                        </div>
                    </div>
                </div>
                <!-- Floating decorative element -->
                <div class="absolute -bottom-8 -left-8 glass-panel rounded-xl p-4 shadow-2xl border-white/20 animate-float">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=Ali&background=7928CA&color=fff" class="w-10 h-10 rounded-full border border-white/20" alt="Avatar">
                        <div class="text-sm">
                            <div class="text-white font-bold">Ali joined</div>
                            <div class="text-white/50">Editing main.py</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Smart LMS (Image Left, Text Right) -->
        <div class="flex flex-col-reverse lg:flex-row items-center gap-20">
            <div class="flex-1 w-full relative">
                <div class="gradient-box-green rounded-[2rem] p-6 lg:p-8 shadow-2xl transform -rotate-1 hover:rotate-0 transition-transform duration-700">
                    <div class="halftone-overlay"></div>
                    <div class="relative z-10 glass-panel rounded-xl p-6 aspect-video flex flex-col gap-4 shadow-inner">
                        <div class="flex items-center justify-between bg-white/5 p-4 rounded-lg border border-white/5 hover:bg-white/10 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-full bg-[#10B981]/20 flex items-center justify-center border border-[#10B981]/30"><div class="w-2.5 h-2.5 rounded-full bg-[#10B981] shadow-[0_0_10px_#10B981]"></div></div>
                                <div class="h-4 w-32 bg-white/60 rounded"></div>
                            </div>
                            <div class="h-5 w-16 bg-[#10B981]/80 rounded-full"></div>
                        </div>
                        <div class="flex items-center justify-between bg-white/5 p-4 rounded-lg border border-white/5 hover:bg-white/10 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-full bg-[#FFBD2E]/20 flex items-center justify-center border border-[#FFBD2E]/30"><div class="w-2.5 h-2.5 rounded-full bg-[#FFBD2E] shadow-[0_0_10px_#FFBD2E]"></div></div>
                                <div class="h-4 w-40 bg-white/60 rounded"></div>
                            </div>
                            <div class="h-5 w-16 bg-white/20 rounded-full"></div>
                        </div>
                    </div>
                </div>
                <!-- Floating decorative element -->
                <div class="absolute -top-8 -right-8 glass-panel rounded-xl p-4 shadow-2xl border-[#10B981]/30 animate-float-delayed flex items-center gap-3">
                    <div class="text-3xl">🏆</div>
                    <div>
                        <div class="text-white font-bold text-sm">Clean Coder Badge</div>
                        <div class="text-[#10B981] text-xs font-bold">+50 XP Earned</div>
                    </div>
                </div>
            </div>
            <div class="flex-1 lg:pl-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-[#10B981]/30 bg-[#10B981]/10 mb-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#34d399]">Feature 02</span>
                </div>
                <h3 class="text-4xl font-bold text-white mb-6 tracking-tight">Smart LMS & Gamification</h3>
                <p class="text-xl text-light-gray mb-8 leading-relaxed">
                    Automated grading, secure queuing systems, and real-time progress tracking. Level up your learning with developer-centric gamification: earn XP, unlock kernel titles, and claim badges for writing clean code.
                </p>
                <a href="#" class="text-white font-semibold hover:text-[#34d399] transition-colors flex items-center gap-2 text-lg">
                    See LMS Features
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Row 3: Institute ERP (Text Left, Image Right) -->
        <div class="flex flex-col lg:flex-row items-center gap-20">
            <div class="flex-1 lg:pr-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-[#00F3FF]/30 bg-[#00F3FF]/10 mb-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#7dd3fc]">Feature 03</span>
                </div>
                <h3 class="text-4xl font-bold text-white mb-6 tracking-tight">Centralized ERP</h3>
                <p class="text-xl text-light-gray mb-8 leading-relaxed">
                    Manage campuses, departments, and computational quotas from a single pane of glass. Keep track of server health, active nodes, and assignment workloads effortlessly.
                </p>
                <a href="#" class="text-white font-semibold hover:text-[#7dd3fc] transition-colors flex items-center gap-2 text-lg">
                    Discover Administration
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            <div class="flex-1 w-full relative">
                <div class="gradient-box-blue rounded-[2rem] p-6 lg:p-8 shadow-2xl transform rotate-1 hover:rotate-0 transition-transform duration-700">
                    <div class="halftone-overlay"></div>
                    <div class="relative z-10 glass-panel rounded-xl p-8 aspect-video shadow-inner flex flex-col justify-between">
                        <div class="flex justify-between items-end mb-4">
                            <div class="space-y-2">
                                <div class="text-sm text-white/50 uppercase tracking-widest font-bold">Active Nodes</div>
                                <div class="text-4xl font-mono text-white font-extrabold">342</div>
                            </div>
                            <div class="w-24 h-16 flex items-end gap-2">
                                <div class="flex-1 bg-[#00F3FF]/30 h-[40%] rounded-t-sm"></div>
                                <div class="flex-1 bg-[#00F3FF]/60 h-[70%] rounded-t-sm"></div>
                                <div class="flex-1 bg-[#00F3FF] h-[100%] rounded-t-sm shadow-[0_0_15px_#00F3FF]"></div>
                            </div>
                        </div>
                        <div class="w-full h-px bg-white/10 my-6"></div>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-xs text-white/50 mb-2 font-mono"><span>CPU Usage</span><span>70%</span></div>
                                <div class="w-full bg-white/5 rounded-full h-2.5 overflow-hidden border border-white/5"><div class="bg-gradient-to-r from-[#00F3FF] to-white h-full w-[70%]"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs text-white/50 mb-2 font-mono"><span>Memory</span><span>45%</span></div>
                                <div class="w-full bg-white/5 rounded-full h-2.5 overflow-hidden border border-white/5"><div class="bg-white/40 h-full w-[45%]"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Glowing Pipeline Divider -->
    <div class="pipeline-divider"></div>

    <!-- Tailored for your organization (3 Columns) -->
    <section class="max-w-[1400px] mx-auto px-6 lg:px-8 py-20 mb-24">
        <div class="text-center mb-20">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-6">Tailored for your institution</h2>
            <p class="text-xl text-light-gray max-w-2xl mx-auto font-medium">VisionLab adapts to your university's security and scale requirements, ensuring a safe learning environment.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-[#0d1117] p-10 rounded-2xl border border-white/5 card-top-glow group">
                <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center mb-8 group-hover:bg-[#10B981]/10 group-hover:border-[#10B981]/30 transition-colors">
                    <svg class="w-6 h-6 text-white group-hover:text-[#10B981] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Enterprise Security</h3>
                <p class="text-light-gray text-base leading-relaxed font-medium">
                    Every student workspace runs in an isolated Docker container with strict network policies. Code execution is completely sandboxed.
                </p>
            </div>
            
            <div class="bg-[#0d1117] p-10 rounded-2xl border border-white/5 card-top-glow group">
                <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center mb-8 group-hover:bg-[#00F3FF]/10 group-hover:border-[#00F3FF]/30 transition-colors">
                    <svg class="w-6 h-6 text-white group-hover:text-[#00F3FF] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Infinite Scale</h3>
                <p class="text-light-gray text-base leading-relaxed font-medium">
                    Designed to handle hundreds of concurrent users. Node pools auto-scale based on assignment deadlines and active connections.
                </p>
            </div>
            
            <div class="bg-[#0d1117] p-10 rounded-2xl border border-white/5 card-top-glow group">
                <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center mb-8 group-hover:bg-[#FF0080]/10 group-hover:border-[#FF0080]/30 transition-colors">
                    <svg class="w-6 h-6 text-white group-hover:text-[#FF0080] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Responsibly AI</h3>
                <p class="text-light-gray text-base leading-relaxed font-medium">
                    Our AI assistant acts as a tutor, not an answer key. It guides students to find solutions themselves, preventing plagiarism.
                </p>
            </div>
        </div>
    </section>

    <!-- Global Footer -->
    <x-frontend-footer />

</body>
</html>
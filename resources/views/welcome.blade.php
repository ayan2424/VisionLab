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

    <!-- Detailed Features Sections -->
    <!-- 1. LMS Section -->
    <section class="relative z-10 w-full max-w-7xl mx-auto px-6 py-24 border-t" style="border-color:var(--vc-border);">
        <div class="flex flex-col md:flex-row gap-12 items-center">
            <div class="flex-1 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#B026FF]" style="background:rgba(176,38,255,0.1); border:1px solid rgba(176,38,255,0.2);">
                    Smart LMS
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-white">Course Management, Reimagined.</h2>
                <p class="text-lg leading-relaxed" style="color:var(--vc-text-secondary);">
                    Manage courses, enrollments, and announcements effortlessly. Our LMS module integrates deeply with the code execution environment. Instructors can instantly distribute assignments and gather analytics on student progress, all in one place.
                </p>
                <ul class="space-y-3" style="color:var(--vc-text-secondary);">
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#B026FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Automated Grading & Queue System</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#B026FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Global & Course-level Announcements</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#B026FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Real-time Progress Tracking</li>
                </ul>
            </div>
            <div class="flex-1 vc-card p-6 rounded-2xl w-full">
                <img src="/assets/dashboard-preview.png" alt="LMS Dashboard" class="rounded-xl border border-white/10 w-full opacity-80" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'><rect width=\'400\' height=\'300\' fill=\'%23111\'/><text x=\'50%\' y=\'50%\' fill=\'%23555\' text-anchor=\'middle\' font-family=\'sans-serif\'>LMS Interface Preview</text></svg>'">
            </div>
        </div>
    </section>

    <!-- 2. Workspace Section -->
    <section class="relative z-10 w-full max-w-7xl mx-auto px-6 py-24 border-t" style="border-color:var(--vc-border);">
        <div class="flex flex-col md:flex-row-reverse gap-12 items-center">
            <div class="flex-1 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-[#00F3FF]" style="background:rgba(0,243,255,0.1); border:1px solid rgba(0,243,255,0.2);">
                    Cloud Workspace
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-white">Full-fledged IDE in the Browser.</h2>
                <p class="text-lg leading-relaxed" style="color:var(--vc-text-secondary);">
                    No local setup required. Every student gets a dedicated, isolated sandbox powered by VS Code technology. Real-time syncing allows multiple users to collaborate seamlessly on the same codebase with multi-cursor support.
                </p>
                <ul class="space-y-3" style="color:var(--vc-text-secondary);">
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#00F3FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Instantly Booting Docker Sandboxes</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#00F3FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Exam Lockdown Mode functionality</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#00F3FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Built-in AI Agent (Chat & Plan modes)</li>
                </ul>
            </div>
            <div class="flex-1 vc-card p-6 rounded-2xl w-full">
                <img src="/assets/workspace-preview.png" alt="Workspace IDE" class="rounded-xl border border-white/10 w-full opacity-80" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'><rect width=\'400\' height=\'300\' fill=\'%23111\'/><text x=\'50%\' y=\'50%\' fill=\'%23555\' text-anchor=\'middle\' font-family=\'sans-serif\'>Workspace Preview</text></svg>'">
            </div>
        </div>
    </section>

    <!-- 3. Institute Management Section -->
    <section class="relative z-10 w-full max-w-7xl mx-auto px-6 py-24 border-t" style="border-color:var(--vc-border);">
        <div class="flex flex-col md:flex-row gap-12 items-center">
            <div class="flex-1 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold text-white" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2);">
                    Institute Management
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-white">Centralized ERP & Administration.</h2>
                <p class="text-lg leading-relaxed" style="color:var(--vc-text-secondary);">
                    Designed specifically for Aptech Vision 2026. Manage campuses, departments, batches, fee challans, library resources, and employees from a single pane of glass. High-level analytics keep stakeholders informed.
                </p>
                <ul class="space-y-3" style="color:var(--vc-text-secondary);">
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Multi-Campus Hierarchy & Roles</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Deep System Auditing & Logs</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Quota & Infrastructure Management</li>
                </ul>
            </div>
            <div class="flex-1 vc-card p-6 rounded-2xl w-full">
                <img src="/assets/admin-preview.png" alt="Admin ERP Dashboard" class="rounded-xl border border-white/10 w-full opacity-80" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'><rect width=\'400\' height=\'300\' fill=\'%23111\'/><text x=\'50%\' y=\'50%\' fill=\'%23555\' text-anchor=\'middle\' font-family=\'sans-serif\'>ERP Admin Preview</text></svg>'">
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="relative z-10 w-full max-w-7xl mx-auto px-6 py-24 border-t" style="border-color:var(--vc-border);">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-white">Trusted by Top Educators</h2>
            <p class="text-lg" style="color:var(--vc-text-secondary);">See what faculty and students are saying.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="vc-card p-8 rounded-2xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-full bg-[#B026FF] flex items-center justify-center text-white font-bold text-xl">AJ</div>
                    <div>
                        <h4 class="font-bold text-white">Prof. Andrew Johnson</h4>
                        <p class="text-xs" style="color:var(--vc-text-secondary);">Computer Science Dept.</p>
                    </div>
                </div>
                <p class="italic text-sm leading-relaxed" style="color:var(--vc-text-secondary);">
                    "VisionLab completely eliminated 'it works on my machine' problems in our python cohorts. The grading system is exceptionally robust."
                </p>
            </div>
            <div class="vc-card p-8 rounded-2xl relative" style="border-color:var(--vc-accent);">
                <!-- Highlight badge -->
                <div class="absolute -top-3 right-6 bg-[#00F3FF] text-[#0A0A0F] text-xs font-bold px-3 py-1 rounded-full">Top Rated</div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-full bg-[#00F3FF] flex items-center justify-center text-[#0A0A0F] font-bold text-xl">SK</div>
                    <div>
                        <h4 class="font-bold text-white">Sarah Khan</h4>
                        <p class="text-xs" style="color:var(--vc-text-secondary);">Student, Batch 2026</p>
                    </div>
                </div>
                <p class="italic text-sm leading-relaxed" style="color:var(--vc-text-secondary);">
                    "The AI agent doesn't just write code for me, it explains where I went wrong. It's like having a tutor available 24/7."
                </p>
            </div>
            <div class="vc-card p-8 rounded-2xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-[#0A0A0F] font-bold text-xl">DM</div>
                    <div>
                        <h4 class="font-bold text-white">Dr. David Miller</h4>
                        <p class="text-xs" style="color:var(--vc-text-secondary);">Dean of Engineering</p>
                    </div>
                </div>
                <p class="italic text-sm leading-relaxed" style="color:var(--vc-text-secondary);">
                    "The centralized ERP handles our entire institute. Managing enrollments and sandboxing in one system saves us hundreds of hours."
                </p>
            </div>
        </div>
    </section>

    <!-- FAQs Section -->
    <section class="relative z-10 w-full max-w-4xl mx-auto px-6 py-24 border-t" style="border-color:var(--vc-border);">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-white">Frequently Asked Questions</h2>
        </div>
        <div class="space-y-4">
            <div class="vc-card p-6 rounded-2xl">
                <h3 class="text-lg font-bold text-white mb-2">How isolated are the workspaces?</h3>
                <p class="text-sm leading-relaxed" style="color:var(--vc-text-secondary);">
                    Every workspace runs in an isolated Docker container with strict resource limits and no root access. Network policies prevent lateral movement, ensuring complete security.
                </p>
            </div>
            <div class="vc-card p-6 rounded-2xl">
                <h3 class="text-lg font-bold text-white mb-2">Can the AI write the assignments for students?</h3>
                <p class="text-sm leading-relaxed" style="color:var(--vc-text-secondary);">
                    No. The integrated AI is strictly prompted to act as a "Socratic Tutor". It provides hints, explains concepts, and helps debug, but deliberately refuses to provide direct solutions.
                </p>
            </div>
            <div class="vc-card p-6 rounded-2xl">
                <h3 class="text-lg font-bold text-white mb-2">Does it support exam lockdown?</h3>
                <p class="text-sm leading-relaxed" style="color:var(--vc-text-secondary);">
                    Yes. The lockdown mode enforces fullscreen, blocks tab switching, disables copy-paste from external sources, and actively monitors browser visibility API to report violations to instructors.
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 py-12 text-center border-t mt-auto" style="border-color:var(--vc-border);">
        <p style="color:var(--vc-muted);" class="text-sm mb-4">© {{ date('Y') }} VisionLab Inc. All rights reserved.</p>
        <div class="flex justify-center gap-6 text-sm" style="color:var(--vc-text-secondary);">
            <a href="{{ route('about') }}" class="hover:text-white transition-colors">About Us</a>
            <a href="{{ route('features') }}" class="hover:text-white transition-colors">Features</a>
            <a href="{{ route('docs') }}" class="hover:text-white transition-colors">Documentation</a>
        </div>
    </footer>

</body>
</html>
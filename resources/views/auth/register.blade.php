<x-guest-layout>
    <div class="min-h-screen flex transition-colors duration-300" style="background:var(--vc-bg);">

        {{-- ═══ LEFT: Platform Preview Panel ═══ --}}
        <div class="hidden lg:flex lg:w-1/2 relative flex-col items-center justify-center px-12 overflow-hidden"
             style="background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #1E1B4B 100%);">

            {{-- Ambient orbs --}}
            <div class="absolute w-[500px] h-[500px] rounded-full bg-cyan-400/15 blur-[120px] -top-40 -right-40 pointer-events-none"></div>
            <div class="absolute w-[400px] h-[400px] rounded-full bg-orange-600/20 blur-[100px] -bottom-32 -left-20 pointer-events-none"></div>

            {{-- Grid --}}
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 60px 60px;"></div>

            <div class="relative z-10 max-w-md w-full" style="opacity:0;animation:slideRight 0.8s 0.3s ease forwards">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-cyan-400/20 bg-cyan-400/5 text-cyan-300 text-xs font-medium mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    Everything you need · One platform
                </div>

                <h2 class="text-3xl font-bold text-white mb-4 leading-snug">
                    <span class="text-gradient-cyan">All-in-one</span> platform<br>
                    for modern education.
                </h2>
                <p class="text-slate-300 text-sm mb-10 leading-relaxed">
                    VisionLab combines a VS Code IDE, Google Classroom-style LMS,
                    Zoom-like video classes, and an autonomous AI agent — all in one unified interface.
                </p>

                {{-- Feature list --}}
                <div class="space-y-4">
                    <div class="flex items-start gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-orange-600/10 border border-orange-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-orange-600/20 transition-colors">
                            <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white">Full VS Code IDE</div>
                            <div class="text-xs text-slate-400">IntelliSense, terminal, extensions — the real thing</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-cyan-500/20 transition-colors">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white">Video Classrooms</div>
                            <div class="text-xs text-slate-400">Jitsi-powered live sessions inside the IDE</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-500/20 transition-colors">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white">AI Agent with 3 Modes</div>
                            <div class="text-xs text-slate-400">Chat, Plan, and Agent — sandboxed with diff approval</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500/20 transition-colors">
                            <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white">Smart LMS</div>
                            <div class="text-xs text-slate-400">Courses, assignments, grading, and announcements</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ RIGHT: Register Form ═══ --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 relative transition-colors duration-300" style="background:var(--vc-bg);">

            {{-- Theme toggle --}}
            <button onclick="window.themeManager.toggle()" class="theme-toggle absolute top-6 right-6" title="Toggle theme">
                <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>

            <div class="w-full max-w-[420px] relative z-10">
                {{-- Logo --}}
                <div class="mb-10" style="opacity:0;animation:fadeUp 0.5s 0.1s ease forwards">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center group-hover:shadow-glow-brand transition-all duration-300"
                             style="background:linear-gradient(135deg,#F05000,#FF8147);">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </div>
                        <span class="text-xl font-bold" style="color:var(--vc-text);">Vision<span style="color:var(--vc-accent);">Code</span> AI</span>
                    </a>
                </div>

                <div class="mb-8" style="opacity:0;animation:fadeUp 0.5s 0.2s ease forwards">
                    <h1 class="text-2xl font-bold mb-1.5" style="color:var(--vc-text);">Create your account</h1>
                    <p class="text-sm" style="color:var(--vc-text-secondary);">Start coding, collaborating, and learning</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5" style="opacity:0;animation:fadeUp 0.5s 0.3s ease forwards">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--vc-text-secondary);">Full Name</label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                   class="vc-input pl-10"
                                   placeholder="Alex Johnson" required autofocus autocomplete="name">
                        </div>
                        @error('name')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--vc-text-secondary);">Email Address</label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   class="vc-input pl-10"
                                   placeholder="alex@university.edu" required autocomplete="username">
                        </div>
                        @error('email')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="reg-password" class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--vc-text-secondary);">Password</label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input id="reg-password" type="password" name="password"
                                   class="vc-input pl-10"
                                   placeholder="Min. 8 characters" required autocomplete="new-password"
                                   oninput="updateStrength(this.value)">
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex-1 flex gap-1">
                                <div class="h-1 flex-1 rounded-full overflow-hidden" style="background:var(--vc-border);"><div id="str-1" class="h-full w-0 rounded-full transition-all duration-300"></div></div>
                                <div class="h-1 flex-1 rounded-full overflow-hidden" style="background:var(--vc-border);"><div id="str-2" class="h-full w-0 rounded-full transition-all duration-300"></div></div>
                                <div class="h-1 flex-1 rounded-full overflow-hidden" style="background:var(--vc-border);"><div id="str-3" class="h-full w-0 rounded-full transition-all duration-300"></div></div>
                                <div class="h-1 flex-1 rounded-full overflow-hidden" style="background:var(--vc-border);"><div id="str-4" class="h-full w-0 rounded-full transition-all duration-300"></div></div>
                            </div>
                            <span id="str-label" class="text-[10px] font-medium min-w-[52px] text-right" style="color:var(--vc-muted);"></span>
                        </div>
                        @error('password')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--vc-text-secondary);">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                   class="vc-input pl-10" placeholder="Repeat password" required autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center py-3.5 text-sm mt-2 group">
                        <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Create My Workspace
                    </button>

                    <p class="text-center text-sm" style="color:var(--vc-text-secondary);">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold transition-colors ml-1" style="color:var(--vc-accent);">
                            Sign in →
                        </a>
                    </p>
                </form>

                <p class="text-center text-[10px] mt-8" style="color:var(--vc-muted);opacity:0;animation:fadeUp 0.5s 0.6s ease forwards">
                    Built for Aptech Vision 2026 · VisionLab
                </p>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        @keyframes slideRight { from { opacity:0; transform:translateX(-24px); } to { opacity:1; transform:translateX(0); } }
    </style>
    <script>
        function updateStrength(pw) {
            let score = 0;
            if (pw.length >= 8) score++;
            if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
            if (/\d/.test(pw)) score++;
            if (/[^a-zA-Z0-9]/.test(pw)) score++;

            const colors = ['','#ef4444','#f59e0b','#06b6d4','#10b981'];
            const labels = ['','Weak','Fair','Good','Strong'];

            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('str-' + i);
                if (i <= score) {
                    bar.style.width = '100%';
                    bar.style.background = colors[score];
                } else {
                    bar.style.width = '0';
                }
            }
            document.getElementById('str-label').textContent = pw.length > 0 ? labels[score] : '';
            document.getElementById('str-label').style.color = colors[score] || 'var(--vc-muted)';
        }
    </script>
</x-guest-layout>

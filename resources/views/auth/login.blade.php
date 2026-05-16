<x-guest-layout>
    <div class="min-h-screen flex">

        {{-- ═══ LEFT: IDE Preview Panel ═══ --}}
        <div class="hidden lg:flex lg:w-1/2 relative flex-col items-center justify-center px-12 overflow-hidden"
             style="background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #1E1B4B 100%);">

            {{-- Ambient orbs --}}
            <div class="absolute w-[500px] h-[500px] rounded-full bg-orange-600/20 blur-[120px] -top-40 -left-40 pointer-events-none"></div>
            <div class="absolute w-[400px] h-[400px] rounded-full bg-cyan-400/15 blur-[100px] -bottom-32 -right-20 pointer-events-none"></div>

            {{-- Grid lines --}}
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 60px 60px;"></div>

            {{-- Content --}}
            <div class="relative z-10 max-w-md w-full" style="opacity:0;animation:slideRight 0.8s 0.3s ease forwards">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-orange-400/20 bg-orange-400/5 text-orange-300 text-xs font-medium mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                    Powered by AI · Built for Developers
                </div>

                <h2 class="text-4xl font-bold text-white mb-3 leading-tight">
                    Your Intelligent<br>
                    <span style="background:linear-gradient(135deg,#818cf8,#06b6d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Coding Workspace</span>
                </h2>
                <p class="text-slate-300 text-sm leading-relaxed mb-10">Full VS Code IDE in the browser with AI pair programming, real-time collaboration, and smart classroom management.</p>

                {{-- IDE preview --}}
                <div class="rounded-xl border border-white/10 overflow-hidden" style="background:rgba(15,23,42,0.8);box-shadow:0 16px 64px rgba(0,0,0,0.4);">
                    {{-- Title bar --}}
                    <div class="flex items-center gap-2 px-4 py-2.5" style="border-bottom:1px solid rgba(255,255,255,0.06);">
                        <div class="flex gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-400/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-green-400/80"></span>
                        </div>
                        <span class="text-[10px] text-slate-500 ml-2 font-mono">agent.py — VisionLab</span>
                    </div>
                    {{-- Code lines --}}
                    <div class="p-4 font-mono text-xs leading-6" id="codePreview">
                        <div><span class="text-orange-400">class</span> <span class="text-cyan-300">VisionAgent</span><span class="text-slate-400">:</span></div>
                        <div class="pl-4"><span class="text-orange-400">def</span> <span class="text-emerald-400">analyze</span><span class="text-slate-400">(self, code):</span></div>
                        <div class="pl-8"><span class="text-slate-500"># AI-powered code analysis</span></div>
                        <div class="pl-8"><span class="text-amber-300">result</span> <span class="text-slate-400">=</span> <span class="text-emerald-400">self.engine.run</span><span class="text-slate-400">(code)</span></div>
                        <div class="pl-8"><span class="text-orange-400">return</span> <span class="text-amber-300">result</span><span class="text-slate-400">.optimize()</span></div>
                    </div>
                </div>

                {{-- Feature pills --}}
                <div class="flex flex-wrap gap-2 mt-6">
                    @foreach(['VS Code IDE', 'AI Agent', 'Live Collab', 'Video Classes'] as $f)
                    <span class="px-3 py-1 rounded-full text-[10px] font-medium text-slate-300 border border-white/10 bg-white/[0.03]">{{ $f }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ═══ RIGHT: Login Form ═══ --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 relative transition-colors duration-300"
             style="background:var(--vc-bg);">

            {{-- Theme toggle at top right --}}
            <button onclick="window.themeManager.toggle()"
                    class="theme-toggle absolute top-6 right-6" title="Toggle theme">
                <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>

            <div class="w-full max-w-md" style="opacity:0;animation:fadeSlideUp .6s .2s ease forwards">

                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2.5 mb-8">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center"
                         style="background:linear-gradient(135deg,#F05000,#FF8147);box-shadow:0 2px 10px rgba(240,80,0,0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <span class="text-lg font-bold" style="color:var(--vc-text);">Vision<span style="color:var(--vc-accent);">Code</span> AI</span>
                </a>

                <h1 class="text-2xl font-bold mb-1" style="color:var(--vc-text);">Sign in to your account</h1>
                <p class="text-sm mb-8" style="color:var(--vc-text-secondary);">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold transition-colors" style="color:var(--vc-accent);">Create one free</a>
                </p>

                @if($errors->any())
                <div class="mb-6 px-4 py-3 rounded-xl text-sm"
                     style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:var(--vc-danger);">
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Email address</label>
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="vc-input pl-11" placeholder="you@university.edu">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Password</label>
                        <div class="relative" x-data="{ showPw: false }">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <input :type="showPw ? 'text' : 'password'" id="password" name="password" required
                                   class="vc-input pl-11 pr-11" placeholder="••••••••">
                            <button type="button" @click="showPw = !showPw"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 cursor-pointer" style="color:var(--vc-muted);">
                                <svg x-show="!showPw" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPw" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember me + Forgot --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 dark:border-gray-600 text-orange-500 focus:ring-orange-500 w-4 h-4">
                            <span class="text-sm" style="color:var(--vc-text-secondary);">Remember me</span>
                        </label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium" style="color:var(--vc-accent);">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-primary w-full py-3 justify-center">
                        Sign In
                    </button>
                </form>

                {{-- Demo Credentials --}}
                <div class="mt-8 pt-6" style="border-top:1px solid var(--vc-border);">
                    <p class="text-xs font-medium mb-3" style="color:var(--vc-muted);">Quick login with demo accounts:</p>
                    <div class="space-y-2">
                        @foreach([
                            ['label' => 'Admin Account', 'email' => 'admin@VisionLab.ai', 'pass' => 'Admin@12345', 'color' => '#DC2626'],
                            ['label' => 'Instructor Account', 'email' => 'instructor@VisionLab.ai', 'pass' => 'Instructor@12345', 'color' => '#F05000'],
                            ['label' => 'Student Account', 'email' => 'student@VisionLab.ai', 'pass' => 'Student@12345', 'color' => '#059669'],
                        ] as $demo)
                        <button type="button"
                                onclick="document.getElementById('email').value='{{ $demo['email'] }}';document.getElementById('password').value='{{ $demo['pass'] }}';"
                                class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-sm transition-all duration-200 cursor-pointer"
                                style="border:1px solid var(--vc-border);color:var(--vc-text-secondary);">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $demo['color'] }};"></span>
                            <span class="font-medium">{{ $demo['label'] }}</span>
                            <span class="ml-auto text-xs font-mono" style="color:var(--vc-muted);">{{ $demo['email'] }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideRight {
            from { opacity: 0; transform: translateX(-30px); }
            to   { opacity: 1; transform: translateX(0); }
        }
    </style>
</x-guest-layout>

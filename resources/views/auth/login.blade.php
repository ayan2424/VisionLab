<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col items-center justify-center relative overflow-hidden" style="background-color:var(--vc-bg);">
        
        {{-- Ambient Neon Glows --}}
        <div class="absolute top-1/2 left-1/2 w-[600px] h-[600px] bg-[#B026FF]/20 rounded-full blur-[150px] -translate-x-full -translate-y-1/2 pointer-events-none z-0"></div>
        <div class="absolute top-1/2 right-1/2 w-[600px] h-[600px] bg-[#00F3FF]/15 rounded-full blur-[150px] translate-x-1/2 -translate-y-1/2 pointer-events-none z-0"></div>

        <div class="w-full max-w-[400px] px-6 z-10" style="animation:fadeSlideUp .6s ease forwards">
            
            {{-- Logo --}}
            <div class="flex justify-center mb-8">
                <a href="/" class="group">
                    <x-logo size="h-12 w-12" textSize="text-2xl" />
                </a>
            </div>

            <div class="vc-card p-8 !rounded-2xl w-full">
                <h1 class="text-2xl font-bold mb-2 text-center text-white">Sign in</h1>
                <p class="text-sm mb-8 text-center" style="color:var(--vc-text-secondary);">
                    Welcome back to VisionLab
                </p>

                @if(session('status'))
                <div class="mb-6 px-4 py-3 rounded-xl text-sm text-center"
                     style="background:rgba(59,130,246,0.06);border:1px solid rgba(59,130,246,0.15);color:var(--vc-accent);">
                    {{ session('status') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 px-4 py-3 rounded-xl text-sm text-center"
                     style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);color:var(--vc-danger);">
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               class="vc-input w-full" placeholder="you@university.edu">
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-medium" style="color:var(--vc-text);">Password</label>
                            @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-medium hover:underline" style="color:var(--vc-accent);">Forgot password?</a>
                            @endif
                        </div>
                        <div class="relative" x-data="{ showPw: false }">
                            <input :type="showPw ? 'text' : 'password'" id="password" name="password" required
                                   class="vc-input w-full pr-11" placeholder="••••••••">
                            <button type="button" @click="showPw = !showPw"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 cursor-pointer" style="color:var(--vc-muted);">
                                <svg x-show="!showPw" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPw" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember me --}}
                    <label class="flex items-center gap-2 cursor-pointer pt-1">
                        <input type="checkbox" name="remember" class="rounded border-gray-600 focus:ring-[#B026FF] w-4 h-4" style="background:var(--vc-input); accent-color:#B026FF;">
                        <span class="text-sm" style="color:var(--vc-text-secondary);">Remember me</span>
                    </label>

                    <button type="submit" class="btn-glow w-full py-3 justify-center mt-4">
                        Sign In
                    </button>
                </form>
            </div>

            <div class="mt-6 text-center text-sm" style="color:var(--vc-text-secondary);">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-medium hover:underline" style="color:var(--vc-text);">Sign up</a>
            </div>



        </div>
    </div>

    <style>
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-guest-layout>

<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col items-center justify-center relative overflow-hidden" style="background-color:var(--vc-bg);">
        
        {{-- Ambient Neon Glows --}}
        <div class="absolute top-1/2 left-1/2 w-[600px] h-[600px] bg-[#B026FF]/20 rounded-full blur-[150px] -translate-x-full -translate-y-1/2 pointer-events-none z-0"></div>
        <div class="absolute top-1/2 right-1/2 w-[600px] h-[600px] bg-[#00F3FF]/15 rounded-full blur-[150px] translate-x-1/2 -translate-y-1/2 pointer-events-none z-0"></div>

        <div class="w-full max-w-[420px] px-6 z-10" style="animation:fadeSlideUp .6s ease forwards">
            
            {{-- Logo --}}
            <div class="flex justify-center mb-8">
                <a href="/" class="group">
                    <x-logo size="h-12 w-12" textSize="text-2xl" />
                </a>
            </div>

            <div class="vc-card p-8 !rounded-2xl w-full">
                <h1 class="text-2xl font-bold mb-2 text-center text-white">Create an account</h1>
                <p class="text-sm mb-8 text-center" style="color:var(--vc-text-secondary);">
                    Join VisionLab to start building
                </p>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                               class="vc-input w-full"
                               placeholder="Alex Johnson" required autofocus autocomplete="name">
                        @error('name')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="vc-input w-full"
                               placeholder="alex@university.edu" required autocomplete="username">
                        @error('email')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="reg-password" class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Password</label>
                        <input id="reg-password" type="password" name="password"
                               class="vc-input w-full"
                               placeholder="Min. 8 characters" required autocomplete="new-password"
                               oninput="updateStrength(this.value)">
                        
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
                        <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="vc-input w-full" placeholder="Repeat password" required autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn-glow w-full justify-center py-3 mt-6">
                        Create Account
                    </button>
                </form>
            </div>

            <div class="mt-6 text-center text-sm" style="color:var(--vc-text-secondary);">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-medium hover:underline" style="color:var(--vc-text);">Sign in</a>
            </div>

        </div>
    </div>

    <style>
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
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

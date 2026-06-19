<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="text-center mb-8" style="opacity:0;animation:fadeUp 0.6s 0.1s ease forwards">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
                    <x-logo size="h-10 w-10" textSize="text-xl" />
                </a>
                <p class="mt-2 text-sm text-muted">Reset your password</p>
            </div>

            {{-- Card --}}
            <div class="glass p-8" style="opacity:0;animation:fadeUp 0.6s 0.2s ease forwards">
                <h2 class="text-base font-bold text-white mb-1">Forgot your password?</h2>
                <p class="text-xs text-muted mb-6">No problem. Enter your email and we'll send you a reset link.</p>

                @if (session('status'))
                    <div class="mb-5 flex items-center gap-2 text-sm text-emerald-300 bg-emerald-400/10 border border-emerald-400/20 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="you@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <x-primary-button class="w-full justify-center">
                        Send Reset Link
                    </x-primary-button>
                </form>

                <p class="mt-6 text-center text-xs text-muted">
                    Remember your password?
                    <a href="{{ route('login') }}" class="text-violet-400 hover:text-violet-300 transition-colors">Sign in</a>
                </p>
            </div>

        </div>
    </div>

    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-guest-layout>

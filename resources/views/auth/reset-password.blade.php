<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="text-center mb-8" style="opacity:0;animation:fadeUp 0.6s 0.1s ease forwards">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-violet-600 flex items-center justify-center shadow-glow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold">Vision<span class="text-gradient-violet">Code</span> AI</span>
                </a>
                <p class="mt-2 text-sm text-muted">Set a new password</p>
            </div>

            {{-- Card --}}
            <div class="glass p-8" style="opacity:0;animation:fadeUp 0.6s 0.2s ease forwards">
                <h2 class="text-base font-bold text-white mb-1">Reset Password</h2>
                <p class="text-xs text-muted mb-6">Choose a strong password for your account.</p>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="you@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('New Password')" />
                        <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Min. 8 characters" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat new password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                    </div>

                    <x-primary-button class="w-full justify-center">
                        Reset Password
                    </x-primary-button>
                </form>

                <p class="mt-6 text-center text-xs text-muted">
                    <a href="{{ route('login') }}" class="text-violet-400 hover:text-violet-300 transition-colors">Back to sign in</a>
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

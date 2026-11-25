<x-guest-layout title="Sign in">
    <div class="space-y-6">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-bold tracking-tight text-vl-ink">Sign in to VisionLab</h1>
            <p class="text-sm text-vl-ink-secondary">
                Use your institutional credentials to access your courses and cloud IDE workspaces.
            </p>
        </div>

        @if(session('status'))
            <div role="status" class="rounded-xl bg-vl-teal/12 px-4 py-3 text-sm text-vl-teal">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div role="alert" class="rounded-xl bg-vl-danger/12 px-4 py-3 text-sm text-vl-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-vl-ink mb-2">Institutional Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@university.edu"
                    class="vl-input @error('email') !border-vl-danger @enderror"
                >
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-medium text-vl-ink">Password</label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-vl-accent hover:underline">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <div class="relative" x-data="{ showPw: false }">
                    <input
                        :type="showPw ? 'text' : 'password'"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="vl-input pr-11 @error('password') !border-vl-danger @enderror"
                    >
                    <button
                        type="button"
                        @click="showPw = !showPw"
                        :aria-label="showPw ? 'Hide password' : 'Show password'"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-lg text-vl-ink-muted hover:text-vl-ink transition-colors duration-150 cursor-pointer"
                    >
                        <svg x-show="!showPw" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="showPw" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>

            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface"
                >
                <span class="text-sm text-vl-ink-secondary">Keep me signed in on this device</span>
            </label>

            <button type="submit" class="vl-btn-primary w-full !py-3">
                Sign in to Portal
            </button>
        </form>

        <div class="pt-5 border-t border-vl-border/10 text-xs leading-relaxed text-vl-ink-muted">
            Campus accounts are provisioned by your university administrator.
            <a href="{{ route('contact') }}" class="font-semibold text-vl-accent hover:underline ml-1">Request institutional access</a>
        </div>
    </div>
</x-guest-layout>

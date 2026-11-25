<x-guest-layout title="Reset Password">
    <div class="space-y-6">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-bold tracking-tight text-vl-ink">Forgot your password?</h1>
            <p class="text-sm text-vl-ink-secondary leading-relaxed">
                Enter your institutional email address and we will send a secure password reset link.
            </p>
        </div>

        @if(session('status'))
            <div role="status" class="rounded-xl bg-vl-teal/12 px-4 py-3 text-sm text-vl-teal">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-vl-ink mb-2">Institutional Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="you@university.edu"
                    class="vl-input @error('email') !border-vl-danger @enderror"
                >
                @error('email')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="vl-btn-primary w-full !py-3">
                Send Reset Link
            </button>
        </form>

        <div class="pt-4 border-t border-vl-border/10 text-center text-xs text-vl-ink-muted">
            Remembered your password?
            <a href="{{ route('login') }}" class="font-semibold text-vl-accent hover:underline ml-1">Back to Sign in</a>
        </div>
    </div>
</x-guest-layout>

<x-guest-layout title="Choose New Password">
    <div class="space-y-6">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-bold tracking-tight text-vl-ink">Set a new password</h1>
            <p class="text-sm text-vl-ink-secondary">
                Choose a strong password to secure your VisionLab account.
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="block text-sm font-medium text-vl-ink mb-2">Institutional Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@university.edu"
                    class="vl-input @error('email') !border-vl-danger @enderror"
                >
                @error('email')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-vl-ink mb-2">New Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimum 8 characters"
                    class="vl-input @error('password') !border-vl-danger @enderror"
                >
                @error('password')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-vl-ink mb-2">Confirm Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repeat new password"
                    class="vl-input @error('password_confirmation') !border-vl-danger @enderror"
                >
                @error('password_confirmation')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="vl-btn-primary w-full !py-3">
                Update Password
            </button>
        </form>

        <div class="pt-4 border-t border-vl-border/10 text-center text-xs text-vl-ink-muted">
            <a href="{{ route('login') }}" class="font-semibold text-vl-accent hover:underline">Back to Sign in</a>
        </div>
    </div>
</x-guest-layout>

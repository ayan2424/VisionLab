<x-guest-layout title="Confirm Password">
    <div class="space-y-6">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-bold tracking-tight text-vl-ink">Confirm your password</h1>
            <p class="text-sm text-vl-ink-secondary leading-relaxed">
                This action accesses a protected area of VisionLab. Please re-enter your password to continue.
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <label for="password" class="block text-sm font-medium text-vl-ink mb-2">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autofocus
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    class="vl-input @error('password') !border-vl-danger @enderror"
                >
                @error('password')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="vl-btn-primary w-full !py-3">
                Confirm &amp; Continue
            </button>
        </form>
    </div>
</x-guest-layout>

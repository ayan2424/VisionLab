<x-guest-layout title="Verify Email">
    <div class="space-y-6">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-bold tracking-tight text-vl-ink">Verify your email address</h1>
            <p class="text-sm text-vl-ink-secondary leading-relaxed">
                Before launching workspaces, please verify your institutional email address using the link we sent to your inbox.
            </p>
        </div>

        @if(session('status') === 'verification-link-sent')
            <div role="status" class="rounded-xl bg-vl-teal/12 px-4 py-3 text-sm text-vl-teal">
                A fresh verification link has been sent to your institutional email address.
            </div>
        @endif

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="vl-btn-primary w-full sm:w-auto">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="vl-btn-secondary w-full sm:w-auto">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

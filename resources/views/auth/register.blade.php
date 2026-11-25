<x-guest-layout title="Account Provisioning">
    <div class="space-y-6">
        <div class="space-y-2">
            <h1 class="text-2xl font-semibold tracking-tight text-vl-ink">Institutional account provisioning</h1>
            <p class="text-sm text-vl-ink-muted leading-relaxed">
                VisionLab accounts are provisioned directly by your campus administrator or academic department.
            </p>
        </div>

        <div class="rounded-xl border border-vl-line bg-vl-surface-2 p-4 text-sm text-vl-ink-muted leading-relaxed">
            Self-registration is disabled for academic integrity and container quota governance. If you are a student or instructor without credentials, contact your institution's lab coordinator or request a campus pilot.
        </div>

        <div class="flex flex-col gap-3">
            <a href="{{ route('login') }}" class="vl-btn-primary w-full justify-center py-2.5">
                Sign in to Portal
            </a>
            <a href="{{ route('contact') }}" class="vl-btn-secondary w-full justify-center py-2.5">
                Request Campus Pilot
            </a>
        </div>
    </div>
</x-guest-layout>

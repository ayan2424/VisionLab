<footer class="relative vl-rail-surface text-vl-ink-secondary">
    {{-- Top specular hairline --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-vl-border/12 to-transparent"></div>

    <div class="mx-auto max-w-7xl px-5 sm:px-8 py-16 lg:py-20">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
            {{-- Brand & Mission Column --}}
            <div class="lg:col-span-4 space-y-5">
                <a href="{{ route('home') }}" class="inline-flex items-center">
                    <x-logo size="h-7 w-7" textSize="text-lg" />
                </a>
                <p class="text-sm leading-relaxed text-vl-ink-secondary max-w-sm">
                    Sovereign university coding laboratories powered by deterministic Nix workspaces, human-reviewed AI patch governance, and campus ERP operations.
                </p>
                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <a href="{{ route('contact') }}" class="vl-btn-primary !py-2 !px-4 !text-xs">
                        Request Campus Pilot
                    </a>
                    <a href="{{ route('login') }}" class="vl-btn-secondary !py-2 !px-4 !text-xs">
                        Sign in to Portal
                    </a>
                </div>
            </div>

            {{-- Platform Links --}}
            <div class="lg:col-span-2 lg:col-start-6">
                <h2 class="text-xs font-semibold text-vl-ink mb-4">Platform</h2>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-vl-ink transition-colors duration-150">Overview</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-vl-ink transition-colors duration-150">Product Philosophy</a></li>
                    <li><a href="{{ route('features') }}" class="hover:text-vl-ink transition-colors duration-150">Capabilities</a></li>
                    <li><a href="{{ route('docs') }}" class="hover:text-vl-ink transition-colors duration-150">Documentation</a></li>
                </ul>
            </div>

            {{-- Governance & Operations --}}
            <div class="lg:col-span-3">
                <h2 class="text-xs font-semibold text-vl-ink mb-4">Campus Operations</h2>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('docs') }}#workspaces" class="hover:text-vl-ink transition-colors duration-150">Nix &amp; Docker Sandboxes</a></li>
                    <li><a href="{{ route('docs') }}#ai-governance" class="hover:text-vl-ink transition-colors duration-150">AI Patch Governance</a></li>
                    <li><a href="{{ route('docs') }}#extensions" class="hover:text-vl-ink transition-colors duration-150">SHA256 Extension Supply Chain</a></li>
                    <li><a href="{{ route('health') }}" class="hover:text-vl-ink transition-colors duration-150">System Health Endpoint</a></li>
                </ul>
            </div>

            {{-- Access & Support --}}
            <div class="lg:col-span-2">
                <h2 class="text-xs font-semibold text-vl-ink mb-4">Access</h2>
                <ul class="space-y-2.5 text-sm">
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="hover:text-vl-ink transition-colors duration-150">Open Dashboard</a></li>
                        <li><a href="{{ route('workspace.index') }}" class="hover:text-vl-ink transition-colors duration-150">My Workspaces</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-vl-ink transition-colors duration-150">Institutional Sign In</a></li>
                    @endauth
                    <li><a href="{{ route('contact') }}" class="hover:text-vl-ink transition-colors duration-150">Department Inquiries</a></li>
                </ul>
            </div>
        </div>

        {{-- Bottom meta bar --}}
        <div class="mt-14 pt-6 border-t border-vl-border/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs text-vl-ink-muted">
            <p>&copy; {{ date('Y') }} VisionLab. Institutional coding infrastructure.</p>
            <p class="leading-relaxed">
                Sovereign Docker &amp; Nix workspaces &middot; Human-gated AI patch governance &middot; Institutional LMS &amp; ERP.
            </p>
        </div>
    </div>
</footer>

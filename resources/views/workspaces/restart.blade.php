<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#090D14">
    <link rel="icon" type="image/svg+xml" href="/icons/logo.svg">
    <title>Restarting {{ $workspace->name }} — VisionLab</title>

    @include('partials.theme-init')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes vlIndeterminate {
            0% { transform: translateX(-100%); width: 32%; }
            50% { width: 52%; }
            100% { transform: translateX(300%); width: 32%; }
        }
        .vl-progress-bar {
            animation: vlIndeterminate 1.45s cubic-bezier(0.22, 1, 0.36, 1) infinite;
        }
    </style>
</head>
<body class="h-full vl-atmosphere text-vl-ink font-sans antialiased flex items-center justify-center p-5">
    <div id="premium-loader" class="w-full max-w-[420px]">
        <div class="vl-panel p-8 space-y-6">
            <div class="flex items-center gap-4 pb-5 border-b border-vl-border/10">
                <div class="h-12 w-12 rounded-2xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center">
                    <svg class="h-6 w-6 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h1 class="text-base font-bold text-vl-ink truncate">Restarting Workspace</h1>
                    <p class="text-xs text-vl-ink-muted mt-0.5 leading-relaxed">
                        Stopping and restarting <span class="font-mono">{{ $workspace->slug }}</span>. Your files remain preserved.
                    </p>
                </div>
            </div>

            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-vl-ink-secondary">Status</span>
                    <span id="loader-status-text" class="font-mono font-semibold text-vl-accent">Restarting container...</span>
                </div>
                <div class="relative h-1.5 w-full rounded-full bg-vl-surface-raised overflow-hidden">
                    <div class="vl-progress-bar absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-vl-accent to-vl-teal"></div>
                </div>
            </div>

            <div class="pt-1 flex justify-end">
                <a href="{{ route('workspace.index') }}" class="text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
                    Return to Workspaces
                </a>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const workspaceSlug = @json($workspace->slug);
        const statusEl = document.getElementById('loader-status-text');

        fetch(`/workspace/${workspaceSlug}/restart/process`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        }).then((res) => res.json()).then(() => {
            if (statusEl) statusEl.textContent = 'Waiting for IDE readiness...';
            pollPing();
        }).catch(() => {
            if (statusEl) {
                statusEl.textContent = 'Restart request failed';
                statusEl.className = 'font-mono font-semibold text-vl-danger';
            }
        });

        function pollPing() {
            const interval = setInterval(async () => {
                try {
                    const res = await fetch(`/workspace/${workspaceSlug}/ping`);
                    if (!res.ok) return;
                    const data = await res.json();
                    if (data.ready) {
                        clearInterval(interval);
                        if (statusEl) {
                            statusEl.textContent = 'Workspace Ready';
                            statusEl.className = 'font-mono font-semibold text-vl-teal';
                        }
                        setTimeout(() => {
                            window.location.href = `/workspace/${workspaceSlug}`;
                        }, 450);
                    }
                } catch {}
            }, 2000);
        }
    </script>
</body>
</html>

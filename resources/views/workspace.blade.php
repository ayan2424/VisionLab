<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#090D14">
    <link rel="icon" type="image/svg+xml" href="/icons/logo.svg">
    <title>{{ $workspaceName ?? $workspace->name ?? 'Workspace' }} — VisionLab IDE</title>

    @include('partials.theme-init')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .workspace-container {
            display: flex;
            height: calc(100vh - 40px);
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        #vscode-frame {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
            opacity: 0;
            transition: opacity 280ms cubic-bezier(0.22, 1, 0.36, 1);
        }
        #vscode-frame.ready {
            opacity: 1;
        }
        #premium-loader {
            position: absolute;
            inset: 0;
            z-index: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 260ms cubic-bezier(0.22, 1, 0.36, 1), visibility 260ms cubic-bezier(0.22, 1, 0.36, 1);
        }
        #premium-loader.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .vl-boot-spinner {
            width: 15px;
            height: 15px;
            border-radius: 9999px;
            border: 2px solid rgb(var(--vl-accent) / 0.22);
            border-top-color: rgb(var(--vl-accent));
            animation: vlSpin 0.85s linear infinite;
            flex-shrink: 0;
        }
        @keyframes vlSpin {
            to { transform: rotate(360deg); }
        }
        .step-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: rgb(var(--vl-ink-muted));
        }
        .step-item.active {
            color: rgb(var(--vl-ink));
            font-weight: 600;
        }
        .step-item.done {
            color: rgb(var(--vl-teal));
            font-weight: 500;
        }
    </style>
</head>
<body class="h-full bg-vl-bg text-vl-ink font-sans antialiased">

    <x-exam-lockdown :is-exam-mode="isset($assignment) && $assignment->mode === 'exam'" />

    {{-- Toast Notification Stack --}}
    <div id="toast-container" class="fixed bottom-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none" aria-live="polite"></div>

    {{-- Confirmation Dialog --}}
    <div id="vc-confirm-modal" class="fixed inset-0 z-[9998] hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="vc-confirm-title">
        <div onclick="vcConfirmClose()" class="absolute inset-0 bg-black/60 backdrop-blur-[2px]"></div>
        <div class="relative z-10 w-full max-w-sm rounded-2xl bg-vl-surface p-6 shadow-vl-overlay">
            <h3 id="vc-confirm-title" class="text-base font-bold text-vl-ink">Confirm Action</h3>
            <p id="vc-confirm-message" class="mt-2 text-sm leading-relaxed text-vl-ink-secondary"></p>
            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button type="button" onclick="vcConfirmClose()" class="vl-btn-secondary !py-2 !px-4 !text-xs">Cancel</button>
                <button type="button" id="vc-confirm-btn" class="vl-btn-primary !py-2 !px-4 !text-xs">Confirm</button>
            </div>
        </div>
    </div>

    {{-- Slim 40px Luminous Atelier IDE Chrome Bar --}}
    <header id="vc-topbar" class="h-[40px] shrink-0 px-3 bg-vl-surface border-b border-vl-border/10 flex items-center justify-between gap-3 text-xs z-40 relative">
        {{-- Left Cluster: Back, Logo, Slug, Live Status Dot, Template --}}
        <div class="flex items-center gap-2.5 min-w-0">
            <a
                href="{{ route('workspace.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface-raised transition-colors duration-150 shrink-0"
                title="Back to Workspaces"
            >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <x-logo size="h-4 w-4" :showText="false" />
            </a>

            <span class="h-3.5 w-px bg-vl-border/15 shrink-0" aria-hidden="true"></span>

            <span class="font-mono text-xs font-semibold text-vl-ink truncate max-w-[180px] sm:max-w-[260px]" title="{{ $workspace->slug }}">
                {{ $workspace->slug }}
            </span>

            <span class="inline-flex items-center gap-1.5 rounded-md bg-vl-surface-raised px-2 py-0.5 font-mono text-[11px] font-medium text-vl-ink-secondary shrink-0">
                <span id="vsc-dot" class="h-1.5 w-1.5 rounded-full bg-vl-accent"></span>
                <span id="vsc-label">Booting</span>
            </span>

            <span class="hidden md:inline-flex items-center rounded-md bg-vl-accent/10 px-2 py-0.5 font-mono text-[11px] font-medium text-vl-accent shrink-0">
                {{ $workspace->template->name ?? ucfirst($workspace->language ?? 'Standard') }}
            </span>
        </div>

        {{-- Right Cluster: Real /status Telemetry, Pending Patches, Theme Toggle, Real Lifecycle Controls --}}
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            {{-- Live CPU / RAM Readout from /status --}}
            <div id="vl-telemetry-stats" class="hidden lg:inline-flex items-center gap-2 rounded-md bg-vl-surface-raised/85 px-2.5 py-1 font-mono text-[11px] text-vl-ink-muted tabular-nums" title="Live Container Telemetry">
                <span id="vl-cpu-stat">CPU —</span>
                <span class="text-vl-border/30">&middot;</span>
                <span id="vl-mem-stat">RAM —</span>
            </div>

            {{-- Real Pending AI Patches Indicator (Shown only when pending_patches_count > 0) --}}
            <span
                id="vl-pending-patches"
                class="hidden items-center gap-1.5 rounded-md bg-vl-gate/14 px-2 py-1 font-mono text-[11px] font-semibold text-vl-gate tabular-nums"
                title="AI patches awaiting human approval"
            >
                <span class="h-1.5 w-1.5 rounded-full bg-vl-gate"></span>
                <span id="vl-pending-patches-label">0 Pending Patches</span>
            </span>

            @if(isset($assignment))
                <form method="POST" action="{{ route('submissions.submit', $assignment->id) }}" class="m-0" onsubmit="event.preventDefault(); vcConfirm('Submit this assignment snapshot for grading?', () => this.submit())">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-md bg-vl-teal/15 hover:bg-vl-teal/25 text-vl-teal px-2.5 py-1 text-[11px] font-semibold active:scale-[0.97] transition-[background-color,transform] duration-150 cursor-pointer">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Submit</span>
                    </button>
                </form>
            @endif

            <button
                type="button"
                onclick="triggerRestart(false)"
                class="inline-flex items-center rounded-md bg-vl-surface-raised hover:bg-vl-accent/12 text-vl-ink-secondary hover:text-vl-ink px-2.5 py-1 text-[11px] font-semibold active:scale-[0.97] transition-[background-color,color,transform] duration-150 cursor-pointer"
                title="Restart Workspace Container"
            >
                Restart
            </button>

            <button
                type="button"
                onclick="triggerRestart(true)"
                class="hidden sm:inline-flex items-center rounded-md bg-vl-surface-raised hover:bg-vl-accent/12 text-vl-ink-secondary hover:text-vl-ink px-2.5 py-1 text-[11px] font-semibold active:scale-[0.97] transition-[background-color,color,transform] duration-150 cursor-pointer"
                title="Rebuild Environment Image"
            >
                Rebuild
            </button>

            <form method="POST" action="{{ route('workspace.destroy', $workspace->slug) }}" class="m-0" onsubmit="event.preventDefault(); vcConfirm('Permanently delete this workspace and wipe all container files? This cannot be undone.', () => this.submit())">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-vl-danger/12 hover:bg-vl-danger/22 text-vl-danger px-2.5 py-1 text-[11px] font-semibold active:scale-[0.97] transition-[background-color,transform] duration-150 cursor-pointer"
                    title="Delete Workspace"
                >
                    Delete
                </button>
            </form>

            <x-theme-toggle />

            <a
                href="{{ route('dashboard') }}"
                class="h-6 w-6 rounded-md bg-vl-accent-cta text-white text-[10px] font-bold inline-flex items-center justify-center hover:brightness-110 transition-[filter]"
                title="Return to Dashboard"
            >
                {{ $user->avatar_initials ?? 'U' }}
            </a>
        </div>
    </header>

    {{-- Full-Height Workspace Viewport (100vh - 40px) --}}
    <div class="workspace-container bg-vl-bg">
        <div class="flex-1 min-w-0 relative flex flex-col">
            {{-- Sculptural Container Boot Checklist Overlay --}}
            <div id="premium-loader" class="vl-atmosphere px-4">
                <div class="vl-panel w-full max-w-[390px] p-7 space-y-6">
                    <div class="flex items-center gap-3.5 pb-5 border-b border-vl-border/10">
                        <div class="h-11 w-11 rounded-xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h1 class="loader-title text-base font-bold text-vl-ink truncate">{{ $workspaceName ?? $workspace->name ?? 'VisionLab Workspace' }}</h1>
                            <p class="loader-subtitle font-mono text-[11px] text-vl-ink-muted truncate mt-0.5">
                                {{ $workspace->template->name ?? 'Standard' }} &middot; {{ $workspace->slug }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3.5 px-1">
                        <div class="step-item active" id="step-1">
                            <div class="vl-boot-spinner" id="spin-1"></div>
                            <span>Initializing container sandbox</span>
                        </div>
                        <div class="step-item" id="step-2">
                            <div class="w-[15px] h-[15px] shrink-0"></div>
                            <span>Mounting persistent workspace volume</span>
                        </div>
                        <div class="step-item" id="step-3">
                            <div class="w-[15px] h-[15px] shrink-0"></div>
                            <span>Configuring {{ $workspace->template->name ?? 'Starter' }} runtime</span>
                        </div>
                        <div class="step-item" id="step-4">
                            <div class="w-[15px] h-[15px] shrink-0"></div>
                            <span>Starting browser code-server</span>
                        </div>
                        <div class="step-item" id="step-5">
                            <div class="w-[15px] h-[15px] shrink-0"></div>
                            <span>Connecting IDE viewport</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Browser code-server iframe --}}
            <iframe
                id="vscode-frame"
                data-src="{{ $vscodeUrl ?? '' }}"
                allow="clipboard-read; clipboard-write"
                sandbox="allow-scripts allow-same-origin allow-forms allow-modals allow-popups allow-popups-to-escape-sandbox"
            ></iframe>
        </div>
    </div>

    <script>
        let vscLoaded = false;
        let pollInterval = null;
        let telemetryInterval = null;
        let fallbackTimeout = null;
        let currentStep = 1;

        function showToast(title, message, type = 'info') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const tone = type === 'error' ? 'rgb(var(--vl-danger))' : (type === 'success' ? 'rgb(var(--vl-teal))' : 'rgb(var(--vl-accent))');
            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto rounded-xl bg-vl-surface px-4 py-3 shadow-vl-overlay text-xs min-w-[260px] max-w-[360px]';
            const safeTitle = String(title).replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const safeMsg = String(message).replace(/</g, '&lt;').replace(/>/g, '&gt;');
            toast.innerHTML = `<div class="font-bold" style="color:${tone}">${safeTitle}</div><div class="mt-0.5 text-vl-ink-secondary">${safeMsg}</div>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 4500);
        }

        let vcConfirmCallback = null;
        function vcConfirm(msg, callback) {
            const modal = document.getElementById('vc-confirm-modal');
            document.getElementById('vc-confirm-message').innerText = msg;
            vcConfirmCallback = callback;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function vcConfirmClose() {
            const modal = document.getElementById('vc-confirm-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            vcConfirmCallback = null;
        }
        document.getElementById('vc-confirm-btn')?.addEventListener('click', () => {
            if (vcConfirmCallback) vcConfirmCallback();
            vcConfirmClose();
        });

        setTimeout(advanceStep, 2000);
        setTimeout(advanceStep, 5000);
        setTimeout(advanceStep, 15000);

        const CHECK_SVG = `<svg class="h-[15px] w-[15px] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.75"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>`;

        function advanceStep() {
            if (vscLoaded || currentStep >= 4) return;

            const cur = document.getElementById('step-' + currentStep);
            if (cur) {
                cur.classList.remove('active');
                cur.classList.add('done');
                cur.innerHTML = `${CHECK_SVG}<span>${cur.innerText}</span>`;
            }

            currentStep++;

            if (currentStep <= 5) {
                const next = document.getElementById('step-' + currentStep);
                if (next) {
                    next.classList.add('active');
                    next.innerHTML = `<div class="vl-boot-spinner"></div><span>${next.innerText}</span>`;
                }
            }
        }

        async function fetchWorkspaceTelemetry() {
            try {
                const res = await fetch("{{ route('workspace.status', $workspace->slug) }}", {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                if (!res.ok) return;
                const data = await res.json();

                const cpuEl = document.getElementById('vl-cpu-stat');
                const memEl = document.getElementById('vl-mem-stat');
                if (data.stats && cpuEl && memEl) {
                    const cpu = data.stats.cpu ?? data.stats.cpu_percent ?? null;
                    const mem = data.stats.memory ?? data.stats.mem_usage ?? null;
                    if (cpu !== null) cpuEl.textContent = `CPU ${cpu}`;
                    if (mem !== null) memEl.textContent = `RAM ${mem}`;
                }

                const pendingBadge = document.getElementById('vl-pending-patches');
                const pendingLabel = document.getElementById('vl-pending-patches-label');
                const count = Number(data.pending_patches_count || 0);
                if (pendingBadge && pendingLabel) {
                    if (count > 0) {
                        pendingLabel.textContent = `${count} Pending ${count === 1 ? 'Patch' : 'Patches'}`;
                        pendingBadge.classList.remove('hidden');
                        pendingBadge.classList.add('inline-flex');
                    } else {
                        pendingBadge.classList.add('hidden');
                        pendingBadge.classList.remove('inline-flex');
                    }
                }
            } catch {
                // Silent telemetry fallback
            }
        }

        async function startWorkspace() {
            const needsBoot = {{ $needsBoot ? 'true' : 'false' }};
            if (!needsBoot) {
                startPolling();
                return;
            }

            try {
                const startUrl = "{{ route('workspace.start', $workspace->slug) }}";
                const response = await fetch(startUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                });

                if (!response.ok) {
                    onVsCodeError();
                    return;
                }

                const data = await response.json();
                if (data.status === 'running') {
                    document.getElementById('vscode-frame').setAttribute('data-src', data.url);
                    startPolling();
                } else {
                    onVsCodeError();
                }
            } catch {
                onVsCodeError();
            }
        }

        function startPolling() {
            pollInterval = setInterval(async () => {
                try {
                    const pingUrl = "{{ route('workspace.ping', $workspace->slug) }}";
                    const response = await fetch(pingUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) return;

                    const data = await response.json();
                    if (data.ready) {
                        clearInterval(pollInterval);
                        currentStep = 4;
                        advanceStep();

                        const frame = document.getElementById('vscode-frame');
                        frame.src = frame.getAttribute('data-src');

                        fallbackTimeout = setTimeout(() => {
                            if (!vscLoaded) onVsCodeLoad();
                        }, 10000);
                    }
                } catch {
                    // Retry on next tick
                }
            }, 3000);
        }

        function onVsCodeLoad() {
            const frame = document.getElementById('vscode-frame');
            if (!frame.src || frame.src === window.location.href || frame.src === 'about:blank') return;

            vscLoaded = true;
            if (fallbackTimeout) clearTimeout(fallbackTimeout);
            if (pollInterval) clearInterval(pollInterval);

            for (let i = 1; i <= 5; i++) {
                const el = document.getElementById('step-' + i);
                if (!el || el.classList.contains('done')) continue;
                el.classList.remove('active');
                el.classList.add('done');
                el.innerHTML = `${CHECK_SVG}<span>${el.innerText}</span>`;
            }

            setTimeout(() => {
                document.getElementById('premium-loader').classList.add('hidden');
                document.getElementById('vscode-frame').classList.add('ready');
                const dot = document.getElementById('vsc-dot');
                const label = document.getElementById('vsc-label');
                if (dot) dot.style.background = 'rgb(var(--vl-teal))';
                if (label) label.textContent = 'Running';

                fetchWorkspaceTelemetry();
                if (!telemetryInterval) {
                    telemetryInterval = setInterval(fetchWorkspaceTelemetry, 15000);
                }
            }, 450);
        }

        function onVsCodeError() {
            const dot = document.getElementById('vsc-dot');
            const label = document.getElementById('vsc-label');
            if (dot) dot.style.background = 'rgb(var(--vl-danger))';
            if (label) label.textContent = 'Boot Failed';
        }

        startWorkspace();

        function triggerRestart(isRebuild = false) {
            if (!isRebuild) {
                vcConfirm('Restart the workspace container? Unsaved editor buffers should be saved first.', () => _executeRestart(false));
            } else {
                vcConfirm('Rebuild this workspace environment to apply updated Nix packages?', () => _executeRestart(true));
            }
        }

        function _executeRestart(isRebuild) {
            document.getElementById('premium-loader').classList.remove('hidden');
            document.querySelector('.loader-title').textContent = isRebuild ? 'Rebuilding Environment' : 'Restarting Workspace';
            document.querySelector('.loader-subtitle').textContent = isRebuild ? 'Applying Nix packages...' : 'Restarting isolated container...';

            for (let i = 1; i <= 5; i++) {
                const el = document.getElementById('step-' + i);
                if (!el) continue;
                el.classList.remove('done', 'active');
                const span = el.querySelector('span');
                if (span) {
                    el.innerHTML = `<div class="w-[15px] h-[15px] shrink-0"></div><span>${span.innerText}</span>`;
                }
            }
            const firstStep = document.getElementById('step-1');
            if (firstStep) {
                firstStep.classList.add('active');
                const span = firstStep.querySelector('span');
                if (span) firstStep.innerHTML = `<div class="vl-boot-spinner"></div><span>${span.innerText}</span>`;
            }

            const frame = document.getElementById('vscode-frame');
            frame.classList.remove('ready');
            frame.src = 'about:blank';

            const processUrl = isRebuild
                ? "{{ route('workspace.process_rebuild', $workspace->slug) }}"
                : "{{ route('workspace.process_restart', $workspace->slug) }}";

            fetch(processUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            }).then(() => {
                window.location.reload();
            }).catch(() => {
                onVsCodeError();
            });
        }

        document.getElementById('vscode-frame').addEventListener('load', onVsCodeLoad);
        document.getElementById('vscode-frame').addEventListener('error', onVsCodeError);

        @if(isset($isAssignmentLocked) && $isAssignmentLocked)
        (function () {
            let isLocked = false;
            let violations = 0;

            function enforceLockdown() {
                const elem = document.documentElement;
                if (elem.requestFullscreen) {
                    elem.requestFullscreen().catch(() => {});
                }
                isLocked = true;
                overlay.style.display = 'none';
            }

            function checkFullscreen() {
                if (!document.fullscreenElement) {
                    isLocked = false;
                    overlay.style.display = 'flex';
                    violations++;
                    if (violations > 1) {
                        showToast('Violation Recorded', 'You exited fullscreen during a locked exam.', 'error');
                    }
                } else {
                    overlay.style.display = 'none';
                }
            }

            document.addEventListener('visibilitychange', () => {
                if (document.hidden && isLocked) {
                    showToast('Exam Violation', 'Tab switch detected and logged.', 'error');
                    violations++;
                }
            });

            document.addEventListener('fullscreenchange', checkFullscreen);
            document.addEventListener('click', () => {
                if (!isLocked) enforceLockdown();
            }, { once: true });

            const overlay = document.createElement('div');
            overlay.id = 'lockdown-overlay';
            overlay.className = 'fixed inset-0 z-[999999] flex flex-col items-center justify-center bg-vl-bg/95 backdrop-blur-md p-6 text-center';
            overlay.innerHTML = `
                <div class="vl-panel max-w-md p-8 space-y-5">
                    <h2 class="text-xl font-bold text-vl-ink">Exam Lockdown Active</h2>
                    <p class="text-sm leading-relaxed text-vl-ink-secondary">
                        This assignment requires fullscreen focus. Switching tabs or exiting fullscreen is recorded automatically.
                    </p>
                    <button id="btn-enter-lockdown" type="button" class="vl-btn-primary w-full !py-3">
                        Enter Fullscreen &amp; Begin
                    </button>
                </div>
            `;
            document.body.appendChild(overlay);
            document.getElementById('btn-enter-lockdown')?.addEventListener('click', enforceLockdown);
        })();
        @endif
    </script>
</body>
</html>

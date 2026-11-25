@extends('layouts.landing')

@section('title', 'VisionLab — Sovereign University Coding Labs')
@section('meta_description', 'Deterministic Nix-powered VS Code workspaces, human-reviewed AI patch governance, SHA256 extension verification, and university LMS & ERP infrastructure.')

@section('content')
    {{-- ════════════════════════════════════════════════════════════════════════
         SECTION 1: INTERACTIVE 3D HERO STAGE (.vl-atmosphere)
         ════════════════════════════════════════════════════════════════════════ --}}
    <section
        x-data="{
            mode: 'nix',
            modes: {
                nix: {
                    label: 'Nix Container Core',
                    code: 'cgroups v2 · isolated code-server · 2 GB RAM / 1 vCPU',
                    detail: 'Every seat boots an identical Nix-pinned toolchain inside an isolated Docker sandbox—zero local setup drift across Python, C++, Java, Node.js, and PHP.'
                },
                ai: {
                    label: 'AI Patch Gate',
                    code: 'ai_pending_patches · human approve/reject before write',
                    detail: 'The Socratic AI assistant never mutates student files silently. Every diff enters a 5-stage patch queue requiring explicit review and full audit logging.'
                },
                sha: {
                    label: 'SHA256 Supply Chain',
                    code: 'vsix digest check · per-course allowlists · air-gapped registry',
                    detail: 'Administrators pin signed VSIX extension packages verified by cryptographic SHA256 hashes and enforce course-level allowlists for lab and exam integrity.'
                }
            },
            setMode(next) {
                this.mode = next;
                window.dispatchEvent(new CustomEvent('vl:hero-mode', { detail: { mode: next } }));
            }
        }"
        class="relative vl-atmosphere overflow-hidden"
    >
        {{-- Subtle ambient ocean-sapphire & teal horizon bloom --}}
        <div aria-hidden="true" class="pointer-events-none absolute -top-24 right-[-10%] h-[540px] w-[540px] rounded-full bg-vl-accent/10 blur-[120px]"></div>
        <div aria-hidden="true" class="pointer-events-none absolute bottom-0 left-[-8%] h-[420px] w-[420px] rounded-full bg-vl-teal/10 blur-[110px]"></div>

        <div class="relative mx-auto max-w-7xl px-5 sm:px-8 pt-10 pb-20 lg:pt-14 lg:pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                {{-- Editorial Hero Copy & Actions --}}
                <div class="lg:col-span-6 space-y-7">
                    <h1 class="text-4xl sm:text-5xl lg:text-[3.6rem] font-bold tracking-[-0.032em] leading-[1.06] text-vl-ink text-balance">
                        Cloud coding labs built for real university rigor.
                    </h1>

                    <p class="text-base sm:text-lg leading-relaxed text-vl-ink-secondary max-w-xl">
                        Every student launches a full browser-based VS Code laboratory in seconds—backed by deterministic Nix containers, human-reviewed AI patch governance, and unified campus LMS &amp; ERP operations.
                    </p>

                    <div class="flex flex-wrap items-center gap-3.5 pt-1">
                        @auth
                            <a href="{{ route('dashboard') }}" class="vl-btn-primary !px-6 !py-3">
                                Open Dashboard
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                            <a href="{{ route('workspace.index') }}" class="vl-btn-secondary !px-6 !py-3">
                                Launch Workspace
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="vl-btn-primary !px-6 !py-3">
                                Sign in to Portal
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                            <a href="{{ route('contact') }}" class="vl-btn-secondary !px-6 !py-3">
                                Request Campus Pilot
                            </a>
                        @endauth
                    </div>

                    {{-- Interactive Subsystem Coupling Bar --}}
                    <div class="pt-4 space-y-3.5">
                        <div class="flex flex-wrap items-center gap-2" role="tablist" aria-label="Inspect VisionLab core subsystems">
                            <button
                                type="button"
                                role="tab"
                                :aria-selected="(mode === 'nix').toString()"
                                @click="setMode('nix')"
                                :class="mode === 'nix' ? 'bg-vl-accent/15 text-vl-accent shadow-[inset_0_1px_0_rgb(var(--vl-accent)/0.25)]' : 'bg-vl-surface/65 text-vl-ink-secondary hover:text-vl-ink'"
                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors duration-150 ease-vl-out cursor-pointer"
                            >
                                <span class="h-2 w-2 rounded-full bg-vl-accent"></span>
                                Nix Container Core
                            </button>
                            <button
                                type="button"
                                role="tab"
                                :aria-selected="(mode === 'ai').toString()"
                                @click="setMode('ai')"
                                :class="mode === 'ai' ? 'bg-vl-gate/15 text-vl-gate shadow-[inset_0_1px_0_rgb(var(--vl-gate)/0.25)]' : 'bg-vl-surface/65 text-vl-ink-secondary hover:text-vl-ink'"
                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors duration-150 ease-vl-out cursor-pointer"
                            >
                                <span class="h-2 w-2 rounded-full bg-vl-gate"></span>
                                AI Patch Gate
                            </button>
                            <button
                                type="button"
                                role="tab"
                                :aria-selected="(mode === 'sha').toString()"
                                @click="setMode('sha')"
                                :class="mode === 'sha' ? 'bg-vl-teal/15 text-vl-teal shadow-[inset_0_1px_0_rgb(var(--vl-teal)/0.25)]' : 'bg-vl-surface/65 text-vl-ink-secondary hover:text-vl-ink'"
                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors duration-150 ease-vl-out cursor-pointer"
                            >
                                <span class="h-2 w-2 rounded-full bg-vl-teal"></span>
                                SHA256 Supply Chain
                            </button>
                        </div>

                        <div class="vl-panel p-4 sm:p-5 max-w-xl">
                            <div class="flex items-center justify-between gap-3 mb-1.5">
                                <span class="text-xs font-semibold text-vl-ink" x-text="modes[mode].label"></span>
                                <span class="font-mono text-[11px] text-vl-ink-muted truncate" x-text="modes[mode].code"></span>
                            </div>
                            <p class="text-sm leading-relaxed text-vl-ink-secondary" x-text="modes[mode].detail"></p>
                        </div>
                    </div>
                </div>

                {{-- Interactive Three.js PBR 3D Companion & Sovereign Core Stage --}}
                <div class="lg:col-span-6 relative">
                    <div class="relative mx-auto w-full h-[420px] sm:h-[500px] lg:h-[540px] flex items-center justify-center">
                        {{-- Soft atmospheric halo beneath 3D model --}}
                        <div
                            aria-hidden="true"
                            class="pointer-events-none absolute inset-10 rounded-full transition-colors duration-300"
                            :class="{
                                'bg-vl-accent/15 blur-[90px]': mode === 'nix',
                                'bg-vl-gate/15 blur-[90px]': mode === 'ai',
                                'bg-vl-teal/15 blur-[90px]': mode === 'sha'
                            }"
                        ></div>

                        {{-- Live WebGL Canvas Mount --}}
                        <div
                            id="vl-hero-3d-stage"
                            class="relative z-10 w-full h-full cursor-grab active:cursor-grabbing"
                            aria-label="Interactive 3D VisionLab Autonomous Lab Companion and Sovereign Kernel Core"
                            role="img"
                        ></div>

                        {{-- Graceful Non-WebGL Fallback --}}
                        <div id="vl-hero-3d-fallback" class="hidden absolute inset-0 z-10 flex items-center justify-center p-6">
                            <div class="vl-panel p-8 text-center max-w-sm space-y-4">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-vl-accent/15">
                                    <x-logo size="h-9 w-9" :showText="false" />
                                </div>
                                <p class="text-sm font-semibold text-vl-ink">VisionLab Sovereign Core</p>
                                <p class="text-xs leading-relaxed text-vl-ink-secondary">
                                    Deterministic Nix workspaces, human-reviewed AI diffs, and SHA256-verified VS Code extensions.
                                </p>
                            </div>
                        </div>

                        {{-- Subtle bottom telemetry caption --}}
                        <div class="pointer-events-none absolute bottom-3 left-1/2 -translate-x-1/2 z-20 inline-flex items-center gap-2.5 rounded-full bg-vl-surface/80 backdrop-blur-xl px-4 py-1.5 shadow-vl-panel">
                            <span
                                class="h-2 w-2 rounded-full"
                                :class="{
                                    'bg-vl-accent': mode === 'nix',
                                    'bg-vl-gate': mode === 'ai',
                                    'bg-vl-teal': mode === 'sha'
                                }"
                            ></span>
                            <span class="font-mono text-[11px] font-medium text-vl-ink-secondary" x-text="'Interactive 3D · ' + modes[mode].label"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════════
         SECTION 2: EDITORIAL CAMPUS & LABORATORY ARCHITECTURE (REAL PHOTOGRAPHY)
         ════════════════════════════════════════════════════════════════════════ --}}
    <section class="relative py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-5 sm:px-8 space-y-24">
            {{-- Asymmetric Story 1: Campus-Scale Deterministic Sandboxes --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                <div class="lg:col-span-7 relative group">
                    <div class="relative overflow-hidden rounded-3xl shadow-vl-panel aspect-[16/10] bg-vl-surface-raised">
                        <img
                            src="{{ asset('images/redesign/campus-glass-lab.jpg') }}"
                            alt="Modern cantilevered glass and steel university computer science laboratory building at twilight"
                            loading="lazy"
                            decoding="async"
                            class="h-full w-full object-cover transition-transform duration-500 ease-vl-out group-hover:scale-[1.02]"
                        >
                        <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-[#090D14]/75 via-[#090D14]/15 to-transparent"></div>
                        <div class="absolute bottom-5 left-5 right-5 flex flex-wrap items-end justify-between gap-3">
                            <div class="rounded-2xl bg-[#090D14]/70 backdrop-blur-xl px-4 py-3 text-white shadow-lg">
                                <p class="font-mono text-[11px] text-sky-300">docker-engine · nix-profile · code-server</p>
                                <p class="text-sm font-semibold mt-0.5">Identical compiler &amp; runtime state on every student seat</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-6">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-[-0.025em] leading-[1.12] text-vl-ink text-balance">
                        Zero setup friction from first lecture to capstone defense.
                    </h2>
                    <p class="text-base leading-relaxed text-vl-ink-secondary">
                        Computer science labs lose weeks every semester to mismatched compilers, broken PATH variables, and incompatible student laptops. VisionLab provisions isolated container workspaces from instructor-approved templates in one click.
                    </p>
                    <dl class="space-y-4 pt-2">
                        <div class="flex items-start gap-3.5">
                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-vl-accent/12 text-vl-accent">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-vl-ink">Multi-runtime template catalog</dt>
                                <dd class="text-sm text-vl-ink-secondary mt-0.5">Pin Python, Node.js, C/C++, Java, PHP, or Go images with enforced RAM, CPU, and storage quotas.</dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3.5">
                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-vl-teal/12 text-vl-teal">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-vl-ink">Verified VSIX extension governance</dt>
                                <dd class="text-sm text-vl-ink-secondary mt-0.5">Every editor extension is verified against a SHA256 digest and scoped per course or globally by campus admins.</dd>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Asymmetric Story 2: Faculty Grading & Campus ERP Operations --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                <div class="lg:col-span-5 order-2 lg:order-1 space-y-6">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-[-0.025em] leading-[1.12] text-vl-ink text-balance">
                        Built to unify academic teaching and institutional administration.
                    </h2>
                    <p class="text-base leading-relaxed text-vl-ink-secondary">
                        Most cloud IDEs stop at the code editor. VisionLab connects coding labs directly to course rosters, immutable assignment snapshots, grading queues, and full campus ERP records—without duct-taping five separate tools.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="vl-panel p-4 space-y-1.5">
                            <p class="text-sm font-semibold text-vl-ink">Immutable Submissions</p>
                            <p class="text-xs leading-relaxed text-vl-ink-secondary">Students snapshot workspace files straight into the instructor grading queue with timestamped provenance.</p>
                        </div>
                        <div class="vl-panel p-4 space-y-1.5">
                            <p class="text-sm font-semibold text-vl-ink">Native Campus ERP</p>
                            <p class="text-xs leading-relaxed text-vl-ink-secondary">Manage campuses, departments, batches, fee challans, staff records, and library catalogs in one portal.</p>
                        </div>
                    </div>
                    <div class="pt-1">
                        <a href="{{ route('features') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-vl-accent hover:underline">
                            Explore full platform capabilities
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-7 order-1 lg:order-2 relative group">
                    <div class="relative overflow-hidden rounded-3xl shadow-vl-panel aspect-[16/10] bg-vl-surface-raised">
                        <img
                            src="{{ asset('images/redesign/cs-research-lab.jpg') }}"
                            alt="Two university computer science researchers collaborating at workstations inside a modern systems laboratory"
                            loading="lazy"
                            decoding="async"
                            class="h-full w-full object-cover transition-transform duration-500 ease-vl-out group-hover:scale-[1.02]"
                        >
                        <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-[#090D14]/70 via-transparent to-transparent"></div>
                        <div class="absolute bottom-5 left-5 right-5 flex items-center justify-between gap-3">
                            <div class="rounded-2xl bg-[#090D14]/70 backdrop-blur-xl px-4 py-3 text-white shadow-lg">
                                <p class="font-mono text-[11px] text-teal-300">courses · assignments · grading-queue · erp</p>
                                <p class="text-sm font-semibold mt-0.5">Course join codes, CSV roster imports, and real-time progress telemetry</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════════
         SECTION 3: INTERACTIVE AI PATCH REVIEW STAGE (SYNTHETIC DEMO · CS-301)
         ════════════════════════════════════════════════════════════════════════ --}}
    <section
        x-data="{
            scenario: 'allocator',
            status: 'pending',
            auditHash: 'sha256:9f4c2b81e7...d3a1',
            scenarios: {
                allocator: {
                    title: 'Fix heap buffer leak on early return',
                    file: 'src/lab04/allocator.c',
                    course: 'CS-301 Systems Lab',
                    summary: 'Frees temporary block header before propagating ENOMEM error.',
                    removed: '    if (!next_slot) return -ENOMEM;',
                    added: [
                        '    if (!next_slot) {',
                        '        arena_release(block_hdr);',
                        '        return -ENOMEM;',
                        '    }'
                    ]
                },
                scheduler: {
                    title: 'Guard shared ready-queue pointer',
                    file: 'src/lab06/scheduler.py',
                    course: 'CS-301 Operating Systems',
                    summary: 'Acquires queue_lock around popleft() to prevent worker race condition.',
                    removed: '        task = self.ready_queue.popleft()',
                    added: [
                        '        with self.queue_lock:',
                        '            task = self.ready_queue.popleft()'
                    ]
                }
            },
            switchScenario(key) {
                this.scenario = key;
                this.status = 'pending';
            },
            approve() {
                this.status = 'applied';
            },
            reject() {
                this.status = 'rejected';
            },
            reset() {
                this.status = 'pending';
            }
        }"
        class="relative py-20 lg:py-28 vl-rail-surface"
    >
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-vl-border/12 to-transparent"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-vl-border/12 to-transparent"></div>

        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
                {{-- Left Column: Governance Story + Dark-Teal Coding Workstation Image --}}
                <div class="lg:col-span-5 space-y-6">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-[-0.025em] leading-[1.12] text-vl-ink text-balance">
                        AI assistance that teaches, proposes diffs, and waits for human approval.
                    </h2>
                    <p class="text-base leading-relaxed text-vl-ink-secondary">
                        Unchecked code generation breaks academic integrity. VisionLab routes every AI-proposed file edit through an explicit patch-review queue (<code class="font-mono text-xs px-1.5 py-0.5 rounded bg-vl-surface-raised text-vl-ink">ai_pending_patches</code>) so learners inspect every line before writing to their container.
                    </p>

                    <div class="relative overflow-hidden rounded-2xl shadow-vl-panel aspect-[16/9]">
                        <img
                            src="{{ asset('images/redesign/workstation-night-lab.jpg') }}"
                            alt="Minimal obsidian-slate software engineering workstation displaying a side-by-side AI code diff review"
                            loading="lazy"
                            decoding="async"
                            class="h-full w-full object-cover"
                        >
                        <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-[#090D14]/80 via-[#090D14]/20 to-transparent"></div>
                        <div class="absolute bottom-3.5 left-4 right-4 flex items-center justify-between text-xs text-slate-200">
                            <span class="font-medium">Every prompt &amp; patch decision logged for faculty review</span>
                            <span class="font-mono text-[11px] text-teal-300">Audit Trail</span>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Live Interactive Floating Patch Review Stage --}}
                <div class="lg:col-span-7">
                    <div class="vl-code-stage overflow-hidden">
                        {{-- Stage Header --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 border-b border-white/10 bg-white/[0.02]">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/15 px-2.5 py-0.5 font-mono text-[11px] font-medium text-amber-300">
                                    SYNTHETIC DEMO · CS-301
                                </span>
                                <span class="font-mono text-xs text-slate-300 truncate" x-text="scenarios[scenario].file"></span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button
                                    type="button"
                                    @click="switchScenario('allocator')"
                                    :class="scenario === 'allocator' ? 'bg-sky-400/20 text-sky-200' : 'text-slate-400 hover:text-slate-200'"
                                    class="px-2.5 py-1 rounded-lg font-mono text-[11px] font-medium transition-colors duration-150 cursor-pointer"
                                >
                                    allocator.c
                                </button>
                                <button
                                    type="button"
                                    @click="switchScenario('scheduler')"
                                    :class="scenario === 'scheduler' ? 'bg-sky-400/20 text-sky-200' : 'text-slate-400 hover:text-slate-200'"
                                    class="px-2.5 py-1 rounded-lg font-mono text-[11px] font-medium transition-colors duration-150 cursor-pointer"
                                >
                                    scheduler.py
                                </button>
                            </div>
                        </div>

                        {{-- Patch Metadata & Status Strip --}}
                        <div class="px-5 py-4 border-b border-white/10 flex flex-wrap items-center justify-between gap-4">
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-white" x-text="scenarios[scenario].title"></p>
                                <p class="text-xs text-slate-400" x-text="scenarios[scenario].summary"></p>
                            </div>
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 font-mono text-[11px] font-semibold"
                                :class="{
                                    'bg-amber-400/15 text-amber-300': status === 'pending',
                                    'bg-teal-400/15 text-teal-300': status === 'applied',
                                    'bg-rose-400/15 text-rose-300': status === 'rejected'
                                }"
                            >
                                <span
                                    class="h-1.5 w-1.5 rounded-full"
                                    :class="{
                                        'bg-amber-400': status === 'pending',
                                        'bg-teal-400': status === 'applied',
                                        'bg-rose-400': status === 'rejected'
                                    }"
                                ></span>
                                <span x-text="status === 'pending' ? 'Awaiting Student Review' : (status === 'applied' ? 'Patch Applied & Audited' : 'Rejected · Buffer Untouched')"></span>
                            </span>
                        </div>

                        {{-- Unified Diff Viewport --}}
                        <div class="p-5 font-mono text-xs sm:text-[13px] leading-6 overflow-x-auto">
                            <div class="text-slate-500 select-none mb-2">@@ -42,4 +42,7 @@</div>
                            <div class="px-3 py-0.5 text-slate-400">     block_hdr = arena_alloc_header(size);</div>
                            <div
                                class="px-3 py-1 rounded bg-rose-500/15 text-rose-200 my-1"
                                :class="status === 'applied' ? 'line-through opacity-50' : ''"
                            >
                                <span class="select-none text-rose-400 mr-2">-</span><span x-text="scenarios[scenario].removed"></span>
                            </div>
                            <template x-for="(line, idx) in scenarios[scenario].added" :key="idx">
                                <div
                                    class="px-3 py-1 rounded my-0.5 transition-colors duration-150"
                                    :class="status === 'rejected' ? 'bg-slate-800/40 text-slate-500 line-through' : 'bg-teal-500/15 text-teal-200'"
                                >
                                    <span class="select-none text-teal-400 mr-2">+</span><span x-text="line"></span>
                                </div>
                            </template>
                            <div class="px-3 py-0.5 text-slate-400 mt-1">     return commit_slot(next_slot, block_hdr);</div>
                        </div>

                        {{-- Interactive Controls Bar --}}
                        <div class="px-5 py-4 border-t border-white/10 bg-white/[0.02] flex flex-wrap items-center justify-between gap-3">
                            <div class="font-mono text-[11px] text-slate-400">
                                <span x-show="status === 'pending'">Stage 3 of 5 · Pending explicit write approval</span>
                                <span x-show="status === 'applied'" x-cloak class="text-teal-300" x-text="'Committed to container · Audit ' + auditHash"></span>
                                <span x-show="status === 'rejected'" x-cloak class="text-rose-300">Discarded · Workspace disk state unchanged</span>
                            </div>

                            <div class="flex items-center gap-2.5">
                                <template x-if="status === 'pending'">
                                    <div class="flex items-center gap-2.5">
                                        <button
                                            type="button"
                                            @click="reject()"
                                            class="px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-200 bg-rose-500/15 hover:bg-rose-500/25 active:scale-[0.97] transition-[background-color,transform] duration-150 cursor-pointer"
                                        >
                                            Reject
                                        </button>
                                        <button
                                            type="button"
                                            @click="approve()"
                                            class="vl-btn-primary !py-2 !px-4 !text-xs"
                                        >
                                            Approve Patch
                                        </button>
                                    </div>
                                </template>
                                <template x-if="status !== 'pending'">
                                    <button
                                        type="button"
                                        @click="reset()"
                                        class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-200 bg-white/10 hover:bg-white/15 active:scale-[0.97] transition-[background-color,transform] duration-150 cursor-pointer"
                                    >
                                        Reset Demo
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════════
         SECTION 4: ROLE-BASED PLATFORM CLARITY + COLLABORATION PHOTOGRAPHY
         ════════════════════════════════════════════════════════════════════════ --}}
    <section
        x-data="{
            activeRole: 'students',
            roles: {
                students: {
                    heading: 'Focus on algorithms, systems, and architecture—not OS troubleshooting.',
                    summary: 'Students sign in with their institutional ID and immediately access assigned labs, browser VS Code containers, Socratic AI guidance, and public coding portfolios.',
                    points: [
                        'Instant browser VS Code with terminal, debugger, and course-pinned extensions',
                        'One-click assignment snapshots submitted straight to the grading queue',
                        'Personal contribution heatmap, XP milestones, and shareable /u/{student_id} portfolio'
                    ]
                },
                instructors: {
                    heading: 'Distribute reproducible labs and grade immutable student work cleanly.',
                    summary: 'Faculty author courses, distribute join codes or import CSV rosters, pin required IDE extensions, and inspect student submissions side-by-side.',
                    points: [
                        'Course templates with locked runtime specs and per-course extension allowlists',
                        'Dedicated submission grading queue with score, feedback, and status history',
                        'Course announcements and real-time cohort completion analytics'
                    ]
                },
                admins: {
                    heading: 'Full governance over containers, quotas, audits, and campus ERP.',
                    summary: 'University IT teams and department deans manage compute limits, user provisioning, system health probes, and academic ERP operations from a single unified console.',
                    points: [
                        'Live /healthz infrastructure telemetry across DB, Docker, Redis, and storage',
                        'Global resource quotas, user suspension controls, webhooks, and audit logs',
                        'Integrated Campuses, Departments, Batches, Fee Challans, Employees, and Library ERP'
                    ]
                }
            }
        }"
        class="py-20 lg:py-28"
    >
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div class="max-w-2xl space-y-3">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-[-0.025em] leading-[1.12] text-vl-ink text-balance">
                        Designed for every role on campus.
                    </h2>
                    <p class="text-base text-vl-ink-secondary">
                        One coherent workspace model shared by learners, faculty, and institutional infrastructure teams.
                    </p>
                </div>

                {{-- Segmented Role Switcher --}}
                <div class="inline-flex rounded-2xl p-1.5 bg-vl-surface-raised shadow-vl-panel self-start" role="tablist" aria-label="Platform roles">
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="(activeRole === 'students').toString()"
                        @click="activeRole = 'students'"
                        :class="activeRole === 'students' ? 'bg-vl-surface text-vl-ink shadow-sm' : 'text-vl-ink-secondary hover:text-vl-ink'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-[background-color,color,box-shadow] duration-150 cursor-pointer"
                    >
                        Students
                    </button>
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="(activeRole === 'instructors').toString()"
                        @click="activeRole = 'instructors'"
                        :class="activeRole === 'instructors' ? 'bg-vl-surface text-vl-ink shadow-sm' : 'text-vl-ink-secondary hover:text-vl-ink'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-[background-color,color,box-shadow] duration-150 cursor-pointer"
                    >
                        Instructors
                    </button>
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="(activeRole === 'admins').toString()"
                        @click="activeRole = 'admins'"
                        :class="activeRole === 'admins' ? 'bg-vl-surface text-vl-ink shadow-sm' : 'text-vl-ink-secondary hover:text-vl-ink'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-[background-color,color,box-shadow] duration-150 cursor-pointer"
                    >
                        University IT
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-6 relative overflow-hidden rounded-3xl shadow-vl-panel aspect-[16/11] bg-vl-surface-raised">
                    <img
                        src="{{ asset('images/redesign/students-collaborating.jpg') }}"
                        alt="Three university computer science students collaborating around a monitor inside a sunlit glass studio"
                        loading="lazy"
                        decoding="async"
                        class="h-full w-full object-cover"
                    >
                    <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-tr from-[#090D14]/65 via-transparent to-transparent"></div>
                </div>

                <div class="lg:col-span-6 vl-panel p-7 sm:p-9 space-y-6">
                    <h3 class="text-2xl font-bold tracking-tight text-vl-ink leading-snug" x-text="roles[activeRole].heading"></h3>
                    <p class="text-base leading-relaxed text-vl-ink-secondary" x-text="roles[activeRole].summary"></p>

                    <ul class="space-y-3.5 pt-1">
                        <template x-for="(point, index) in roles[activeRole].points" :key="index">
                            <li class="flex items-start gap-3 text-sm text-vl-ink">
                                <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-vl-teal/15 text-vl-teal">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </span>
                                <span class="leading-relaxed text-vl-ink-secondary" x-text="point"></span>
                            </li>
                        </template>
                    </ul>

                    <div class="pt-3 flex flex-wrap items-center gap-3">
                        <a href="{{ route('docs') }}" class="vl-btn-secondary !py-2.5 !px-4 !text-xs sm:!text-sm">
                            Read Architecture Docs
                        </a>
                        <a href="{{ route('contact') }}" class="vl-btn-primary !py-2.5 !px-4 !text-xs sm:!text-sm">
                            Schedule Department Walkthrough
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════════
         SECTION 5: SERENE OCEAN-SAPPHIRE & TEAL CAMPUS PILOT BAND
         ════════════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 lg:pb-28">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div class="relative overflow-hidden rounded-3xl px-7 py-14 sm:px-12 sm:py-16 lg:px-16 bg-gradient-to-br from-[#0B1F3A] via-[#0E3354] to-[#0B4B53] text-white shadow-vl-panel">
                <div aria-hidden="true" class="pointer-events-none absolute -top-24 right-0 h-80 w-80 rounded-full bg-sky-400/20 blur-[90px]"></div>
                <div aria-hidden="true" class="pointer-events-none absolute -bottom-24 left-12 h-72 w-72 rounded-full bg-teal-400/20 blur-[90px]"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <h2 class="text-3xl sm:text-4xl font-bold tracking-[-0.025em] leading-[1.12] text-white text-balance">
                            Bring sovereign cloud coding laboratories to your next academic term.
                        </h2>
                        <p class="text-base text-sky-100/85 leading-relaxed">
                            Deploy VisionLab on your institutional cluster or evaluate a dedicated department pilot with full Nix templates, AI patch governance, and campus ERP tools.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3.5 shrink-0">
                        <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-[#0B1F3A] shadow-lg hover:bg-sky-50 active:scale-[0.97] transition-[background-color,transform] duration-150">
                            Request Campus Pilot
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 px-6 py-3 text-sm font-semibold text-white backdrop-blur-md ring-1 ring-inset ring-white/20 hover:bg-white/15 active:scale-[0.97] transition-[background-color,transform] duration-150">
                            Sign in to Portal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('head-early')
    <script type="importmap">
        {
            "imports": {
                "three": "https://cdn.jsdelivr.net/npm/three@0.164.1/build/three.module.js",
                "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.164.1/examples/jsm/"
            }
        }
    </script>
@endpush

@push('scripts')
    <script type="module" src="{{ asset('js/hero-3d.js') }}" defer></script>
@endpush

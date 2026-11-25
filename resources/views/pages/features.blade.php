@extends('layouts.landing')

@section('title', 'Capabilities — VisionLab')
@section('meta_description', 'Explore VisionLab capabilities: Docker & Nix browser VS Code sandboxes, human-reviewed AI patches, SHA256 extension governance, assignment grading, and campus ERP.')

@section('content')
    <section class="vl-atmosphere pt-14 pb-16 lg:pt-20 lg:pb-20">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div class="max-w-3xl space-y-5">
                <h1 class="text-4xl sm:text-5xl font-bold tracking-[-0.03em] leading-[1.08] text-vl-ink text-balance">
                    Every subsystem built for university-scale software engineering.
                </h1>
                <p class="text-base sm:text-lg leading-relaxed text-vl-ink-secondary">
                    From isolated container runtimes to audited AI diffs and full institutional ERP management, every capability listed below runs natively inside VisionLab today.
                </p>
            </div>
        </div>
    </section>

    <section class="py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-5 sm:px-8 space-y-12">
            {{-- Bento Capability Matrix (Asymmetric 12-col layout) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- 1. Cloud IDE & Container Sandboxing --}}
                <div class="lg:col-span-7 vl-panel p-8 space-y-5">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-vl-accent/12 text-vl-accent">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <span class="font-mono text-xs text-vl-ink-muted">code-server · Docker · Nix</span>
                    </div>
                    <h2 class="text-2xl font-bold text-vl-ink">Browser VS Code &amp; Container Isolation</h2>
                    <p class="text-sm sm:text-base leading-relaxed text-vl-ink-secondary">
                        Students work inside full browser-hosted VS Code workspaces backed by Docker containers and pinned runtime templates (Python, Node.js, C/C++, Java, PHP, Go). Containers enforce per-seat CPU, memory, and disk quotas with clean one-click rebuild and restart recovery.
                    </p>
                    <div class="overflow-hidden rounded-2xl aspect-[16/8] bg-vl-surface-raised">
                        <img
                            src="{{ asset('images/redesign/workstation-night-lab.jpg') }}"
                            alt="Obsidian-slate developer workstation displaying VisionLab cloud IDE and patch diff review"
                            loading="lazy"
                            decoding="async"
                            class="h-full w-full object-cover"
                        >
                    </div>
                </div>

                {{-- 2. Human-Gated AI Patch Governance --}}
                <div class="lg:col-span-5 vl-panel p-8 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-vl-gate/12 text-vl-gate">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                            </div>
                            <span class="font-mono text-xs text-vl-ink-muted">ai_pending_patches</span>
                        </div>
                        <h2 class="text-2xl font-bold text-vl-ink">Audited AI Patch Governance</h2>
                        <p class="text-sm sm:text-base leading-relaxed text-vl-ink-secondary">
                            AI assistance runs through a governed patch queue. Proposed diffs display exact line changes and require explicit human Approve or Reject confirmation before modifying workspace files, while recording every action in the administrative audit log.
                        </p>
                    </div>
                    <div class="vl-code-stage p-4 font-mono text-xs leading-6">
                        <div class="text-slate-400"># Patch governance workflow</div>
                        <div class="text-sky-300">1. prompt &rarr; Socratic guidance</div>
                        <div class="text-amber-300">2. proposal &rarr; ai_pending_patches</div>
                        <div class="text-teal-300">3. review &rarr; approve / reject + audit</div>
                    </div>
                </div>

                {{-- 3. SHA256 Extension Supply Chain --}}
                <div class="lg:col-span-5 vl-panel p-8 space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-vl-teal/12 text-vl-teal">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>
                        <span class="font-mono text-xs text-vl-ink-muted">SHA256 VSIX</span>
                    </div>
                    <h2 class="text-xl font-bold text-vl-ink">Cryptographic Extension Supply Chain</h2>
                    <p class="text-sm leading-relaxed text-vl-ink-secondary">
                        Upload and verify VSIX packages against SHA256 hashes, mark institutional global defaults, or attach strict per-course extension allowlists so students never install unvetted marketplace binaries.
                    </p>
                </div>

                {{-- 4. Classroom LMS & Immutable Grading Queue --}}
                <div class="lg:col-span-7 vl-panel p-8 space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-vl-accent/12 text-vl-accent">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                            </svg>
                        </div>
                        <span class="font-mono text-xs text-vl-ink-muted">Courses · Grading · Announcements</span>
                    </div>
                    <h2 class="text-xl font-bold text-vl-ink">Courses, Assignments &amp; Immutable Submission Grading</h2>
                    <p class="text-sm leading-relaxed text-vl-ink-secondary">
                        Enroll cohorts via join codes, student ID lookup, or bulk CSV upload. Students launch assignment-scoped workspaces and commit immutable snapshots directly into the instructor grading queue with structured feedback, XP milestones, and verifiable student portfolios.
                    </p>
                </div>

                {{-- 5. Integrated Campus ERP --}}
                <div class="lg:col-span-7 vl-panel p-8 space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-vl-teal/12 text-vl-teal">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        </div>
                        <span class="font-mono text-xs text-vl-ink-muted">6 ERP Modules</span>
                    </div>
                    <h2 class="text-xl font-bold text-vl-ink">Unified Institutional ERP Suite</h2>
                    <p class="text-sm leading-relaxed text-vl-ink-secondary">
                        Operate multi-campus institutions inside the same role-governed workspace: Campuses, Academic Departments, Course Batches, Fee Challans, Staff &amp; Faculty Directory, and Campus Library holdings.
                    </p>
                </div>

                {{-- 6. Live Telemetry & Governance --}}
                <div class="lg:col-span-5 vl-panel p-8 space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-vl-accent/12 text-vl-accent">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </div>
                        <span class="font-mono text-xs text-vl-ink-muted">/healthz · Audits</span>
                    </div>
                    <h2 class="text-xl font-bold text-vl-ink">Telemetry, Webhooks &amp; Audit Forensics</h2>
                    <p class="text-sm leading-relaxed text-vl-ink-secondary">
                        Monitor database, Docker engine, Redis, and storage health in real time, configure signed institutional webhooks, and export GDPR user data with complete administrative audit logs.
                    </p>
                </div>
            </div>

            <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('contact') }}" class="vl-btn-primary !px-6 !py-3">Request Campus Pilot</a>
                <a href="{{ route('docs') }}" class="vl-btn-secondary !px-6 !py-3">Explore Technical Docs</a>
            </div>
        </div>
    </section>
@endsection

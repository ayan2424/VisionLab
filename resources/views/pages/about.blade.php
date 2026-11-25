@extends('layouts.landing')

@section('title', 'Product Philosophy — VisionLab')
@section('meta_description', 'How VisionLab combines deterministic Nix container workspaces, responsible AI governance, and unified campus LMS & ERP engineering for universities.')

@section('content')
    <section class="vl-atmosphere pt-14 pb-20 lg:pt-20 lg:pb-24">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-6 space-y-6">
                    <h1 class="text-4xl sm:text-5xl font-bold tracking-[-0.03em] leading-[1.08] text-vl-ink text-balance">
                        Engineering software laboratories worthy of modern computer science faculties.
                    </h1>
                    <p class="text-base sm:text-lg leading-relaxed text-vl-ink-secondary">
                        VisionLab removes hardware inequality and configuration drift from technical education. Whether a student connects from a thin library terminal or a personal laptop, every course opens into the exact same deterministic engineering environment.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="{{ route('features') }}" class="vl-btn-primary">Explore Capabilities</a>
                        <a href="{{ route('contact') }}" class="vl-btn-secondary">Request Campus Pilot</a>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="relative overflow-hidden rounded-3xl shadow-vl-panel aspect-[16/11] bg-vl-surface-raised">
                        <img
                            src="{{ asset('images/redesign/university-atrium-study.jpg') }}"
                            alt="Contemporary glass-walled university computer science library and engineering study atrium"
                            loading="eager"
                            decoding="async"
                            class="h-full w-full object-cover"
                        >
                        <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-[#090D14]/60 via-transparent to-transparent"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-5 sm:px-8 space-y-16">
            <div class="max-w-3xl space-y-4">
                <h2 class="text-3xl font-bold tracking-[-0.025em] text-vl-ink">
                    Three non-negotiable engineering principles
                </h2>
                <p class="text-base leading-relaxed text-vl-ink-secondary">
                    Every architectural decision inside VisionLab prioritizes reproducibility, human agency, and institutional data sovereignty.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <article class="lg:col-span-5 vl-panel p-8 space-y-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-vl-accent/12 text-vl-accent">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-vl-ink">Sovereign &amp; Reproducible Compute</h3>
                    <p class="text-sm leading-relaxed text-vl-ink-secondary">
                        Institutions retain ownership of every container image, student submission, and audit trail. Nix profiles and Docker quotas ensure predictable resource ceilings across hundreds of concurrent lab seats.
                    </p>
                </article>

                <article class="lg:col-span-7 vl-panel p-8 space-y-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-vl-gate/12 text-vl-gate">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-vl-ink">Pedagogical AI, Never Silent Auto-Completion</h3>
                    <p class="text-sm leading-relaxed text-vl-ink-secondary">
                        AI should strengthen engineering judgment, not bypass it. VisionLab's integrated assistant guides debugging through Socratic prompts and routes proposed code diffs through an explicit Approve/Reject review queue—creating a verifiable audit log for faculty.
                    </p>
                </article>

                <article class="lg:col-span-12 vl-panel p-8 sm:p-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-vl-ink">One Coherent Platform for Teaching and Operations</h3>
                        <p class="text-sm leading-relaxed text-vl-ink-secondary">
                            From enrollment codes and assignment grading queues to multi-campus ERP management (departments, batches, fee challans, staff directories, and library holdings), VisionLab eliminates brittle cross-vendor integrations.
                        </p>
                        <div class="pt-1 flex flex-wrap gap-3">
                            <a href="{{ route('docs') }}" class="vl-btn-secondary !py-2 !px-4 !text-xs">Read Documentation</a>
                            <a href="{{ route('login') }}" class="vl-btn-primary !py-2 !px-4 !text-xs">Sign in to Portal</a>
                        </div>
                    </div>
                    <div class="overflow-hidden rounded-2xl aspect-[16/9] bg-vl-surface-raised">
                        <img
                            src="{{ asset('images/redesign/workstation-night-lab.jpg') }}"
                            alt="Refined obsidian-slate software engineering workstation running a cloud IDE"
                            loading="lazy"
                            decoding="async"
                            class="h-full w-full object-cover"
                        >
                    </div>
                </article>
            </div>
        </div>
    </section>
@endsection

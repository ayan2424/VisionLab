@extends('layouts.landing')

@section('title', 'Request Campus Pilot — VisionLab')
@section('meta_description', 'Contact the VisionLab engineering team to schedule a campus pilot, architecture briefing, or multi-department deployment.')

@section('content')
    <section class="vl-atmosphere pt-14 pb-20 lg:pt-20 lg:pb-28">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                {{-- Left Column: Institutional Context & Campus Architecture Photo --}}
                <div class="lg:col-span-5 space-y-7">
                    <div class="space-y-4">
                        <h1 class="text-4xl sm:text-5xl font-bold tracking-[-0.03em] leading-[1.08] text-vl-ink text-balance">
                            Start a campus pilot with your engineering faculty.
                        </h1>
                        <p class="text-base leading-relaxed text-vl-ink-secondary">
                            Share your department size, course runtimes, and deployment preference. Our infrastructure engineers will configure a sandboxed pilot cohort and walkthrough for your faculty and IT leads.
                        </p>
                    </div>

                    <div class="relative overflow-hidden rounded-3xl shadow-vl-panel aspect-[16/10] bg-vl-surface-raised">
                        <img
                            src="{{ asset('images/redesign/campus-glass-lab.jpg') }}"
                            alt="Modern glass university architecture hosting VisionLab software engineering laboratories"
                            loading="eager"
                            decoding="async"
                            class="h-full w-full object-cover"
                        >
                        <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-[#090D14]/70 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-xs text-slate-200">
                            Self-hosted on campus hardware or private university cloud clusters
                        </div>
                    </div>

                    <div class="vl-panel p-6 space-y-4">
                        <h2 class="text-sm font-semibold text-vl-ink">What happens after you submit</h2>
                        <ol class="space-y-3 text-sm text-vl-ink-secondary">
                            <li class="flex items-start gap-3">
                                <span class="font-mono text-xs font-semibold text-vl-accent mt-0.5 tabular-nums">01</span>
                                <span>Architecture review covering Docker/Nix templates and quota ceilings.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="font-mono text-xs font-semibold text-vl-accent mt-0.5 tabular-nums">02</span>
                                <span>Provisioning of instructor &amp; student pilot accounts with course join codes.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="font-mono text-xs font-semibold text-vl-accent mt-0.5 tabular-nums">03</span>
                                <span>Live validation of AI patch governance, SHA256 extensions, and ERP modules.</span>
                            </li>
                        </ol>
                    </div>
                </div>

                {{-- Right Column: Real Throttled POST /contact Form --}}
                <div class="lg:col-span-7">
                    <div class="vl-panel p-7 sm:p-10 space-y-6">
                        @if(session('status'))
                            <div role="status" class="rounded-2xl bg-vl-teal/12 px-5 py-4 text-sm text-vl-teal flex items-start gap-3">
                                <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="leading-relaxed">{{ session('status') }}</span>
                            </div>
                        @endif

                        @if($errors->any())
                            <div role="alert" class="rounded-2xl bg-vl-danger/12 px-5 py-4 text-sm text-vl-danger space-y-1">
                                <p class="font-semibold">Please review the highlighted fields below.</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5" novalidate>
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="contact-name" class="block text-sm font-medium text-vl-ink mb-2">Full Name</label>
                                    <input
                                        id="contact-name"
                                        name="name"
                                        type="text"
                                        required
                                        autocomplete="name"
                                        value="{{ old('name') }}"
                                        placeholder="Dr. Nadia Rahman"
                                        class="vl-input @error('name') !border-vl-danger @enderror"
                                    >
                                    @error('name')
                                        <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact-email" class="block text-sm font-medium text-vl-ink mb-2">Institutional Email</label>
                                    <input
                                        id="contact-email"
                                        name="email"
                                        type="email"
                                        required
                                        autocomplete="email"
                                        value="{{ old('email') }}"
                                        placeholder="n.rahman@university.edu"
                                        class="vl-input @error('email') !border-vl-danger @enderror"
                                    >
                                    @error('email')
                                        <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="contact-institution" class="block text-sm font-medium text-vl-ink mb-2">University or Institution</label>
                                <input
                                    id="contact-institution"
                                    name="institution"
                                    type="text"
                                    required
                                    autocomplete="organization"
                                    value="{{ old('institution') }}"
                                    placeholder="Department of Computer Science, National Institute of Technology"
                                    class="vl-input @error('institution') !border-vl-danger @enderror"
                                >
                                @error('institution')
                                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="contact-role" class="block text-sm font-medium text-vl-ink mb-2">Your Role</label>
                                    <select id="contact-role" name="role" class="vl-input">
                                        <option value="faculty" @selected(old('role', 'faculty') === 'faculty')>Faculty / Instructor</option>
                                        <option value="it_admin" @selected(old('role') === 'it_admin')>Campus IT / Infrastructure Lead</option>
                                        <option value="dean" @selected(old('role') === 'dean')>Department Chair / Dean</option>
                                        <option value="student_lead" @selected(old('role') === 'student_lead')>Lab Coordinator / TA Lead</option>
                                        <option value="other" @selected(old('role') === 'other')>Other Academic Role</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="contact-cohort" class="block text-sm font-medium text-vl-ink mb-2">Estimated Active Students</label>
                                    <select id="contact-cohort" name="cohort_size" class="vl-input">
                                        <option value="under_100" @selected(old('cohort_size') === 'under_100')>Under 100 seats</option>
                                        <option value="100_500" @selected(old('cohort_size', '100_500') === '100_500')>100 – 500 seats</option>
                                        <option value="500_2000" @selected(old('cohort_size') === '500_2000')>500 – 2,000 seats</option>
                                        <option value="over_2000" @selected(old('cohort_size') === 'over_2000')>2,000+ multi-campus seats</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="contact-message" class="block text-sm font-medium text-vl-ink mb-2">Lab Requirements &amp; Target Term</label>
                                <textarea
                                    id="contact-message"
                                    name="message"
                                    rows="5"
                                    required
                                    placeholder="Share the programming languages, courses, and hosting requirements you would like to test in your campus pilot..."
                                    class="vl-input @error('message') !border-vl-danger @enderror"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="pt-2 flex flex-wrap items-center justify-between gap-4">
                                <p class="text-xs text-vl-ink-muted">
                                    Already provisioned by your campus? <a href="{{ route('login') }}" class="text-vl-accent font-medium hover:underline">Sign in to Portal</a>.
                                </p>
                                <button type="submit" class="vl-btn-primary !px-6 !py-3">
                                    Submit Pilot Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.landing')

@section('title', 'Documentation — VisionLab')
@section('meta_description', 'Technical documentation for VisionLab: Nix & Docker workspaces, human-reviewed AI patch governance, SHA256 VSIX extensions, assignments, and campus ERP operations.')

@section('content')
    <div
        x-data="{
            activeId: 'overview',
            copiedKey: null,
            init() {
                const sections = Array.from(document.querySelectorAll('[data-doc-section]'));
                if (!sections.length) return;
                const observer = new IntersectionObserver(
                    (entries) => {
                        for (const entry of entries) {
                            if (entry.isIntersecting) {
                                this.activeId = entry.target.id;
                            }
                        }
                    },
                    { rootMargin: '-96px 0px -65% 0px', threshold: 0.01 }
                );
                sections.forEach((el) => observer.observe(el));
            },
            copyCode(key, text) {
                if (!navigator.clipboard) return;
                navigator.clipboard.writeText(text).then(() => {
                    this.copiedKey = key;
                    setTimeout(() => {
                        if (this.copiedKey === key) this.copiedKey = null;
                    }, 1800);
                });
            }
        }"
        class="mx-auto max-w-7xl px-5 sm:px-8 py-10 lg:py-14"
    >
        @php
            $docSections = [
                ['id' => 'overview', 'label' => 'Platform Overview', 'group' => 'Getting Started'],
                ['id' => 'quickstart', 'label' => 'Course & Lab Quick Start', 'group' => 'Getting Started'],
                ['id' => 'workspaces', 'label' => 'Nix & Docker Sandboxes', 'group' => 'Compute & IDE'],
                ['id' => 'ai-governance', 'label' => 'AI Patch Governance', 'group' => 'Compute & IDE'],
                ['id' => 'extensions', 'label' => 'SHA256 Extension Registry', 'group' => 'Governance'],
                ['id' => 'grading', 'label' => 'Assignments & Grading Queue', 'group' => 'Governance'],
                ['id' => 'erp-health', 'label' => 'Campus ERP & /healthz', 'group' => 'Operations'],
            ];
            $groupedSections = collect($docSections)->groupBy('group');
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-[240px_minmax(0,1fr)_200px] gap-10 lg:gap-12 items-start">
            {{-- Left Column: 240px Tonal Section Navigation --}}
            <aside aria-label="Documentation sections" class="lg:sticky lg:top-[84px] rounded-2xl vl-rail-surface p-5 space-y-6">
                <div class="space-y-1">
                    <p class="text-sm font-bold text-vl-ink">Documentation</p>
                    <p class="font-mono text-[11px] text-vl-ink-muted">VisionLab Architecture Guide</p>
                </div>

                <nav class="space-y-5">
                    @foreach($groupedSections as $groupName => $items)
                        <div class="space-y-1.5">
                            <p class="px-2.5 text-[11px] font-semibold text-vl-ink-muted">{{ $groupName }}</p>
                            <ul class="space-y-0.5">
                                @foreach($items as $item)
                                    <li>
                                        <a
                                            href="#{{ $item['id'] }}"
                                            @click="activeId = '{{ $item['id'] }}'"
                                            :class="activeId === '{{ $item['id'] }}' ? 'bg-vl-surface text-vl-ink font-semibold shadow-sm' : 'text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface/50'"
                                            class="block rounded-xl px-2.5 py-2 text-[13px] transition-colors duration-150"
                                        >
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </nav>
            </aside>

            {{-- Center Column: Calm 68ch Reading Flow --}}
            <article class="max-w-[68ch] space-y-14 text-[16px] leading-[1.68] text-vl-ink-secondary">
                {{-- 1. Overview --}}
                <section id="overview" data-doc-section class="scroll-mt-24 space-y-4">
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-[-0.025em] leading-[1.15] text-vl-ink">
                        VisionLab Platform Documentation
                    </h1>
                    <p>
                        VisionLab is a self-hosted university coding laboratory, learning management system, and institutional ERP built on Laravel 11 and isolated <code class="font-mono text-[13px] px-1.5 py-0.5 rounded bg-vl-surface-raised text-vl-ink">code-server</code> containers. This guide covers every active subsystem available to students, faculty, and campus administrators.
                    </p>
                </section>

                {{-- 2. Quick Start --}}
                <section id="quickstart" data-doc-section class="scroll-mt-24 space-y-5 pt-6 border-t border-vl-border/10">
                    <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Course &amp; Lab Quick Start</h2>
                    <p>
                        Accounts are provisioned by campus administrators (public self-registration is intentionally disabled so student identities map cleanly to institutional records). Once authenticated, faculty create a course, choose a runtime template, and share the generated join code or import a CSV roster.
                    </p>

                    @php
                        $quickStartSnippet = <<<'TXT'
# 1. Sign in with your institutional credentials at /login
# 2. Instructors: create a course at /courses/create and share the join code
# 3. Students: enroll at /join and launch a course workspace
python3 -c "print('VisionLab deterministic container ready')"
TXT;
                    @endphp
                    <div class="vl-code-stage overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/10 text-xs text-slate-400">
                            <span class="font-mono">quickstart.sh</span>
                            <button
                                type="button"
                                @click="copyCode('quickstart', @js($quickStartSnippet))"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[11px] font-medium text-slate-300 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                            >
                                <span x-text="copiedKey === 'quickstart' ? 'Copied' : 'Copy'"></span>
                            </button>
                        </div>
                        <pre class="p-4 font-mono text-[13px] leading-6 overflow-x-auto text-slate-200"><code>{{ $quickStartSnippet }}</code></pre>
                    </div>
                </section>

                {{-- 3. Nix & Docker Sandboxes --}}
                <section id="workspaces" data-doc-section class="scroll-mt-24 space-y-5 pt-6 border-t border-vl-border/10">
                    <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Nix &amp; Docker Sandboxes</h2>
                    <p>
                        Each workspace runs inside an isolated Docker container executing <code class="font-mono text-[13px] px-1.5 py-0.5 rounded bg-vl-surface-raised text-vl-ink">code-server</code> on a dynamically allocated local port proxied safely behind Laravel policy checks. Default student workspaces enforce resource quotas (for example, 2 GB RAM, 1 vCPU, and capped workspace disk quotas) configurable globally at <code class="font-mono text-[13px] px-1.5 py-0.5 rounded bg-vl-surface-raised text-vl-ink">/admin/quotas</code>.
                    </p>
                    <p>
                        If a student misconfigures a local package or environment variable, dedicated <strong class="font-semibold text-vl-ink">Restart</strong> and <strong class="font-semibold text-vl-ink">Rebuild</strong> workflows restore a clean container image while preserving project files under the persistent workspace mount.
                    </p>

                    <div class="vl-panel p-5 space-y-2 text-sm">
                        <p class="font-semibold text-vl-ink">Workspace Lifecycle Endpoints</p>
                        <ul class="space-y-1.5 font-mono text-xs text-vl-ink-secondary">
                            <li>GET /workspace/{slug} &mdash; Launch slim 40px IDE shell &amp; readiness polling</li>
                            <li>GET /workspace/{slug}/status &mdash; Container CPU, memory, and pending AI patch count</li>
                            <li>GET /workspace/{slug}/ping &mdash; Readiness probe for code-server startup checklist</li>
                            <li>POST /workspace/{slug}/restart/process &mdash; Clean container restart</li>
                        </ul>
                    </div>
                </section>

                {{-- 4. AI Patch Governance --}}
                <section id="ai-governance" data-doc-section class="scroll-mt-24 space-y-5 pt-6 border-t border-vl-border/10">
                    <h2 class="text-2xl font-bold tracking-tight text-vl-ink">AI Patch Governance</h2>
                    <p>
                        VisionLab's AI tutor operates inside a strict governance loop designed for academic integrity. Instead of silently overwriting files in a student's container, every proposed code change is persisted in <code class="font-mono text-[13px] px-1.5 py-0.5 rounded bg-vl-surface-raised text-vl-ink">ai_pending_patches</code> and requires explicit review before disk application.
                    </p>

                    @php
                        $patchSnippet = <<<'JSON'
{
  "workspace_id": 42,
  "target_path": "src/lab04/allocator.c",
  "status": "pending",
  "governance": {
    "requires_human_approval": true,
    "audit_table": "ai_actions_logs"
  }
}
JSON;
                    @endphp
                    <div class="vl-code-stage overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/10 text-xs text-slate-400">
                            <span class="font-mono">ai_pending_patch.json</span>
                            <button
                                type="button"
                                @click="copyCode('patch', @js($patchSnippet))"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[11px] font-medium text-slate-300 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                            >
                                <span x-text="copiedKey === 'patch' ? 'Copied' : 'Copy'"></span>
                            </button>
                        </div>
                        <pre class="p-4 font-mono text-[13px] leading-6 overflow-x-auto text-slate-200"><code>{{ $patchSnippet }}</code></pre>
                    </div>
                </section>

                {{-- 5. SHA256 Extension Registry --}}
                <section id="extensions" data-doc-section class="scroll-mt-24 space-y-5 pt-6 border-t border-vl-border/10">
                    <h2 class="text-2xl font-bold tracking-tight text-vl-ink">SHA256 Extension Supply Chain</h2>
                    <p>
                        Administrators curate an institutional registry of VSIX editor extensions at <code class="font-mono text-[13px] px-1.5 py-0.5 rounded bg-vl-surface-raised text-vl-ink">/admin/extensions</code>. Each package is validated by SHA256 digest before installation inside student workspaces. Instructors can customize course-level extension sets so intro courses stay uncluttered while upper-division labs receive specialized linters and debuggers.
                    </p>
                </section>

                {{-- 6. Assignments & Grading --}}
                <section id="grading" data-doc-section class="scroll-mt-24 space-y-5 pt-6 border-t border-vl-border/10">
                    <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Assignments &amp; Grading Queue</h2>
                    <p>
                        When a student starts an assignment, VisionLab scopes a dedicated IDE workflow with a <strong class="font-semibold text-vl-ink">Save Snapshot</strong> and <strong class="font-semibold text-vl-ink">Submit</strong> cycle. Submissions freeze code snapshots for immutable evaluation inside the instructor grading queue (<code class="font-mono text-[13px] px-1.5 py-0.5 rounded bg-vl-surface-raised text-vl-ink">/grading/queue</code>), awarding grades, structured feedback, XP milestones, and verified portfolio badges.
                    </p>
                </section>

                {{-- 7. Campus ERP & Health --}}
                <section id="erp-health" data-doc-section class="scroll-mt-24 space-y-5 pt-6 border-t border-vl-border/10">
                    <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Campus ERP &amp; Live Health Telemetry</h2>
                    <p>
                        Administrators manage six native institutional ERP modules directly under <code class="font-mono text-[13px] px-1.5 py-0.5 rounded bg-vl-surface-raised text-vl-ink">/admin/*</code>: Campuses, Departments, Course Batches, Fee Challans, Staff &amp; Employees, and Library holdings. Infrastructure readiness is exposed in JSON at <a href="{{ route('health') }}" class="font-mono text-[13px] text-vl-accent hover:underline">/healthz</a> for load balancers and surfaced visually inside the Admin Status console.
                    </p>

                    @php
                        $healthSnippet = <<<'TXT'
curl -sS https://your-campus-visionlab.edu/healthz | jq .
TXT;
                    @endphp
                    <div class="vl-code-stage overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/10 text-xs text-slate-400">
                            <span class="font-mono">health-probe.sh</span>
                            <button
                                type="button"
                                @click="copyCode('health', @js($healthSnippet))"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[11px] font-medium text-slate-300 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                            >
                                <span x-text="copiedKey === 'health' ? 'Copied' : 'Copy'"></span>
                            </button>
                        </div>
                        <pre class="p-4 font-mono text-[13px] leading-6 overflow-x-auto text-slate-200"><code>{{ $healthSnippet }}</code></pre>
                    </div>
                </section>
            </article>

            {{-- Right Column: 200px Sticky On-This-Page TOC --}}
            <aside aria-label="On this page" class="hidden lg:block sticky top-[84px] space-y-3">
                <p class="text-xs font-semibold text-vl-ink">On this page</p>
                <ul class="space-y-1.5 border-l border-vl-border/12 pl-3.5 text-xs">
                    @foreach($docSections as $item)
                        <li>
                            <a
                                href="#{{ $item['id'] }}"
                                @click="activeId = '{{ $item['id'] }}'"
                                :class="activeId === '{{ $item['id'] }}' ? 'text-vl-accent font-semibold' : 'text-vl-ink-muted hover:text-vl-ink'"
                                class="block py-0.5 transition-colors duration-150"
                            >
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="pt-4 mt-4 border-t border-vl-border/10 space-y-2">
                    <p class="text-xs font-medium text-vl-ink-secondary">Evaluating for a department?</p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-vl-accent hover:underline">
                        Request Campus Pilot &rarr;
                    </a>
                </div>
            </aside>
        </div>
    </div>
@endsection

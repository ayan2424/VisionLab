@extends('layouts.dashboard')
@section('title', 'Submission — ' . $submission->student->name)
@section('page-title', 'Submission Review')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="vl-panel p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        <div class="space-y-2.5 min-w-0">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-vl-ink-muted" aria-label="Breadcrumb">
                <a href="{{ route('courses.index') }}" class="hover:text-vl-ink transition-colors">Courses</a>
                <span>/</span>
                <a href="{{ route('courses.show', $assignment->course->slug) }}" class="hover:text-vl-ink transition-colors">{{ $assignment->course->title }}</a>
                <span>/</span>
                <a href="{{ route('assignments.show', $assignment->id) }}" class="hover:text-vl-ink transition-colors">{{ $assignment->title }}</a>
                <span>/</span>
                <span class="text-vl-ink-secondary">{{ $submission->student->name }}</span>
            </nav>

            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center text-lg font-bold">
                    {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-vl-ink truncate">{{ $submission->student->name }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <x-badge :variant="$submission->status === 'graded' ? 'success' : ($submission->status === 'late' ? 'danger' : 'info')">
                            {{ ucfirst($submission->status) }}
                        </x-badge>
                        @if($submission->isLate())
                            <x-badge variant="danger">Late</x-badge>
                        @endif
                        <span class="font-mono text-xs text-vl-ink-secondary">{{ strtoupper($assignment->starter_language) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($submission->status === 'graded')
            @php
                $pct = $assignment->max_points > 0 ? ($submission->grade / $assignment->max_points) * 100 : 0;
                $c = 2 * pi() * 32;
                $d = ($pct / 100) * $c;
                $ringTone = $pct >= 80 ? 'rgb(var(--vl-teal))' : ($pct >= 60 ? 'rgb(var(--vl-gate))' : 'rgb(var(--vl-danger))');
            @endphp
            <div class="flex items-center gap-4 shrink-0">
                <div class="relative h-20 w-20">
                    <svg class="h-20 w-20 -rotate-90" viewBox="0 0 80 80" aria-hidden="true">
                        <circle cx="40" cy="40" r="32" fill="none" stroke="rgb(var(--vl-border) / 0.12)" stroke-width="6" />
                        <circle cx="40" cy="40" r="32" fill="none" stroke="{{ $ringTone }}" stroke-width="6" stroke-dasharray="{{ number_format($d, 2) }} {{ number_format($c, 2) }}" stroke-linecap="round" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center tabular-nums">
                        <span class="font-mono text-lg font-bold" style="color: {{ $ringTone }}">{{ $submission->grade }}</span>
                        <span class="font-mono text-[10px] text-vl-ink-muted">/{{ $assignment->max_points }}</span>
                    </div>
                </div>
                <div class="text-xs font-mono font-semibold text-vl-ink-secondary tabular-nums">{{ number_format($pct, 0) }}%</div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Left: Snapshot & VisionGuard Forensics --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="vl-panel p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div>
                    <p class="text-xs text-vl-ink-muted">Submitted</p>
                    <p class="mt-1 font-mono text-xs sm:text-sm font-semibold text-vl-ink tabular-nums">
                        {{ $submission->submitted_at ? $submission->submitted_at->format('M d, H:i') : 'Pending' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-vl-ink-muted">Due Date</p>
                    <p class="mt-1 font-mono text-xs sm:text-sm font-semibold tabular-nums {{ $assignment->isOverdue() ? 'text-vl-danger' : 'text-vl-ink' }}">
                        {{ $assignment->due_date ? $assignment->due_date->format('M d, H:i') : 'None' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-vl-ink-muted">Runtime</p>
                    <p class="mt-1 font-mono text-xs sm:text-sm font-semibold text-vl-accent">{{ strtoupper($assignment->starter_language) }}</p>
                </div>
                <div>
                    <p class="text-xs text-vl-ink-muted">Max Points</p>
                    <p class="mt-1 font-mono text-xs sm:text-sm font-semibold text-vl-ink tabular-nums">{{ $assignment->max_points }}</p>
                </div>
            </div>

            {{-- Source Code & Forensics Panel --}}
            <div x-data="{ activeTab: 'code' }" class="vl-panel overflow-hidden">
                <div class="flex items-center border-b border-vl-border/10 bg-vl-surface-raised/60 px-2">
                    <button
                        type="button"
                        @click="activeTab = 'code'"
                        :class="activeTab === 'code' ? 'border-vl-accent text-vl-ink' : 'border-transparent text-vl-ink-muted hover:text-vl-ink'"
                        class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors cursor-pointer"
                    >
                        Source Code
                    </button>
                    @if($canGrade)
                        <button
                            type="button"
                            @click="activeTab = 'forensics'; $nextTick(() => window.renderForensicsChart && window.renderForensicsChart())"
                            :class="activeTab === 'forensics' ? 'border-vl-accent text-vl-ink' : 'border-transparent text-vl-ink-muted hover:text-vl-ink'"
                            class="inline-flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 transition-colors cursor-pointer"
                        >
                            <svg class="h-4 w-4 text-vl-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            VisionGuard Forensics
                        </button>
                    @endif
                </div>

                {{-- Source Code Tab --}}
                <div x-show="activeTab === 'code'">
                    @php
                        $fileName = match($assignment->starter_language) {
                            'python' => 'solution.py',
                            'javascript' => 'solution.js',
                            'php' => 'solution.php',
                            default => 'solution.' . $assignment->starter_language,
                        };
                    @endphp
                    <div class="flex items-center justify-between px-4 py-2.5 bg-[#0D1422] border-b border-white/10 text-xs text-slate-400">
                        <span class="font-mono text-slate-200">{{ $fileName }}</span>
                        <div class="flex items-center gap-3">
                            <span id="line-count" class="font-mono text-[11px] tabular-nums"></span>
                            <button type="button" onclick="copyCode(this)" class="rounded-lg px-2.5 py-1 text-[11px] font-medium text-slate-300 hover:text-white hover:bg-white/10 transition-colors cursor-pointer">Copy</button>
                        </div>
                    </div>

                    @if($submission->code_snapshot)
                        <div class="overflow-auto bg-[#080D16] p-4 max-h-[520px]">
                            <pre><code id="code-viewer" class="language-{{ $assignment->starter_language }} text-xs sm:text-[13px] font-mono leading-6 text-slate-200"></code></pre>
                        </div>
                    @else
                        <div class="py-16 text-center text-sm text-vl-ink-muted">No code snapshot saved yet.</div>
                    @endif
                </div>

                {{-- Forensics Tab --}}
                @if($canGrade)
                    <div x-show="activeTab === 'forensics'" x-cloak class="p-6 space-y-6">
                        @if($submission->forensic)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                                <div class="relative h-48 w-48 mx-auto">
                                    <canvas id="forensicsChart"></canvas>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                        <span class="font-mono text-3xl font-bold text-vl-ink tabular-nums">{{ $submission->forensic->human_pct }}%</span>
                                        <span class="text-xs font-medium text-vl-ink-muted">Human</span>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <h3 class="text-base font-bold text-vl-ink">Contribution Analysis</h3>
                                    <div class="flex items-center justify-between rounded-xl bg-vl-surface-raised/75 px-4 py-3 text-sm">
                                        <span class="inline-flex items-center gap-2 text-vl-ink-secondary">
                                            <span class="h-2.5 w-2.5 rounded-full bg-vl-teal"></span>
                                            Human Typed
                                        </span>
                                        <span class="font-mono font-semibold text-vl-ink tabular-nums">{{ number_format($submission->forensic->human_keystrokes) }} chars</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-xl bg-vl-surface-raised/75 px-4 py-3 text-sm">
                                        <span class="inline-flex items-center gap-2 text-vl-ink-secondary">
                                            <span class="h-2.5 w-2.5 rounded-full bg-vl-gate"></span>
                                            AI / Pasted
                                        </span>
                                        <span class="font-mono font-semibold text-vl-ink tabular-nums">{{ number_format($submission->forensic->ai_patches_applied + $submission->forensic->pasted_count) }} chars</span>
                                    </div>
                                    <div class="pt-1">
                                        <x-badge variant="info">Confidence: {{ ucfirst($submission->forensic->confidence ?? 'high') }}</x-badge>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl bg-vl-surface-raised/70 p-4 text-xs leading-relaxed text-vl-ink-secondary">
                                <strong class="font-semibold text-vl-ink">Academic Note:</strong> VisionGuard compares organic typing cadence against bulk insertions and applied AI patches. Use telemetry as an advisory signal alongside code walkthroughs. Last synced {{ $submission->forensic->last_synced_at ? $submission->forensic->last_synced_at->diffForHumans() : 'never' }}.
                            </div>
                        @else
                            <x-empty-state
                                title="No forensics telemetry available"
                                description="VisionGuard has not received keystroke or AI patch telemetry for this submission."
                            />
                        @endif
                    </div>
                @endif
            </div>

            @if($submission->feedback)
                <div class="vl-panel p-5 bg-vl-teal/10 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-vl-teal">
                        <span>Instructor Feedback</span>
                        @if($submission->grader)
                            <span class="font-normal text-vl-ink-muted">&mdash; {{ $submission->grader->name }}</span>
                        @endif
                    </div>
                    <p class="text-sm leading-relaxed text-vl-ink whitespace-pre-wrap">{{ $submission->feedback }}</p>
                </div>
            @endif
        </div>

        {{-- Right: Grade Form & Navigation --}}
        <div class="lg:col-span-4 space-y-5">
            @if($canGrade)
                <x-card :title="$submission->status === 'graded' ? 'Update Grade' : 'Grade Submission'" :padding="true">
                    <form method="POST" action="{{ route('submissions.grade', $submission->id) }}" id="grade-form" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-2">
                            <label for="grade-input" class="block text-xs font-semibold text-vl-ink-secondary">
                                Score (0 &ndash; {{ $assignment->max_points }})
                            </label>
                            <div class="flex items-center gap-3">
                                <input
                                    type="number"
                                    name="grade"
                                    id="grade-input"
                                    value="{{ $submission->grade ?? '' }}"
                                    min="0"
                                    max="{{ $assignment->max_points }}"
                                    placeholder="85"
                                    oninput="updateGradeBar(this.value)"
                                    class="vl-input font-mono text-base font-bold tabular-nums"
                                >
                                <span class="font-mono text-sm font-semibold text-vl-ink-secondary shrink-0">/ {{ $assignment->max_points }}</span>
                            </div>

                            <div class="h-2 rounded-full bg-vl-surface-raised overflow-hidden">
                                <div id="grade-bar" class="h-full rounded-full bg-vl-accent transition-[width] duration-200" style="width: {{ $submission->grade ? ($submission->grade / $assignment->max_points * 100) : 0 }}%"></div>
                            </div>
                            <div class="flex justify-between font-mono text-[11px] text-vl-ink-muted tabular-nums">
                                <span>0</span>
                                <span id="grade-pct" class="text-vl-ink font-semibold">{{ $submission->grade ? number_format($submission->grade / $assignment->max_points * 100, 0) . '%' : '' }}</span>
                                <span>{{ $assignment->max_points }}</span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="grade-feedback" class="block text-xs font-semibold text-vl-ink-secondary">Feedback <span class="font-normal text-vl-ink-muted">(optional)</span></label>
                            <textarea
                                id="grade-feedback"
                                name="feedback"
                                rows="4"
                                placeholder="Provide constructive guidance on correctness, complexity, and edge cases..."
                                class="vl-input resize-none"
                            >{{ $submission->feedback }}</textarea>
                        </div>

                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['Excellent work!', 'Good effort!', 'Needs improvement', 'Check edge cases', 'Add error handling'] as $quick)
                                <button
                                    type="button"
                                    onclick="appendFeedback(@js($quick))"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-vl-surface-raised text-vl-ink-secondary hover:text-vl-ink transition-colors cursor-pointer"
                                >
                                    {{ $quick }}
                                </button>
                            @endforeach
                        </div>

                        <button type="submit" class="vl-btn-primary w-full">
                            {{ $submission->status === 'graded' ? 'Update Grade' : 'Save Grade' }}
                        </button>
                    </form>
                </x-card>
            @endif

            <x-card title="Assignment Reference" :padding="true">
                <div class="space-y-3 text-sm">
                    <p class="font-semibold text-vl-ink">{{ $assignment->title }}</p>
                    @if($assignment->description)
                        <p class="text-xs leading-relaxed text-vl-ink-secondary line-clamp-4">{{ $assignment->description }}</p>
                    @endif
                    <div class="pt-3 border-t border-vl-border/10">
                        <a href="{{ route('assignments.show', $assignment->id) }}" class="text-xs font-semibold text-vl-accent hover:underline">
                            View all submissions &rarr;
                        </a>
                    </div>
                </div>
            </x-card>

            @if($prevSubmission || $nextSubmission)
                <x-card title="Queue Navigation" :padding="true">
                    <div class="space-y-2.5">
                        @if($prevSubmission)
                            <a href="{{ route('submissions.show', $prevSubmission->id) }}" class="flex items-center justify-between gap-2 rounded-xl px-3.5 py-2.5 bg-vl-surface-raised/70 hover:bg-vl-surface-raised transition-colors">
                                <div class="min-w-0">
                                    <p class="text-xs text-vl-ink-muted">Previous</p>
                                    <p class="text-sm font-semibold text-vl-ink truncate">{{ $prevSubmission->student->name }}</p>
                                </div>
                                <x-badge variant="neutral">{{ ucfirst($prevSubmission->status) }}</x-badge>
                            </a>
                        @endif
                        @if($nextSubmission)
                            <a href="{{ route('submissions.show', $nextSubmission->id) }}" class="flex items-center justify-between gap-2 rounded-xl px-3.5 py-2.5 bg-vl-surface-raised/70 hover:bg-vl-surface-raised transition-colors">
                                <div class="min-w-0">
                                    <p class="text-xs text-vl-ink-muted">Next</p>
                                    <p class="text-sm font-semibold text-vl-ink truncate">{{ $nextSubmission->student->name }}</p>
                                </div>
                                <x-badge variant="neutral">{{ ucfirst($nextSubmission->status) }}</x-badge>
                            </a>
                        @endif
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/vs2015.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
const SUBMISSION = {
    code: @json($submission->code_snapshot ?? ''),
    maxPts: {{ (int) $assignment->max_points }},
};

@if($submission->code_snapshot)
document.addEventListener('DOMContentLoaded', () => {
    const viewer = document.getElementById('code-viewer');
    if (viewer) {
        viewer.textContent = SUBMISSION.code;
        if (window.hljs) hljs.highlightElement(viewer);
    }
    const lineCountEl = document.getElementById('line-count');
    if (lineCountEl) {
        lineCountEl.textContent = SUBMISSION.code.split('\n').length + ' lines';
    }
});
@endif

function updateGradeBar(val) {
    const v = Math.max(0, Math.min(Number(val) || 0, SUBMISSION.maxPts));
    const pct = SUBMISSION.maxPts > 0 ? (v / SUBMISSION.maxPts) * 100 : 0;
    const bar = document.getElementById('grade-bar');
    if (bar) {
        bar.style.width = pct + '%';
        bar.style.background = pct >= 80 ? 'rgb(var(--vl-teal))' : (pct >= 60 ? 'rgb(var(--vl-gate))' : 'rgb(var(--vl-danger))');
    }
    const pctEl = document.getElementById('grade-pct');
    if (pctEl) pctEl.textContent = pct > 0 ? Math.round(pct) + '%' : '';
}

function appendFeedback(text) {
    const ta = document.querySelector('textarea[name="feedback"]');
    if (!ta) return;
    ta.value = ta.value ? ta.value.trim() + ' ' + text : text;
    ta.focus();
}

function copyCode(btn) {
    if (!SUBMISSION.code || !navigator.clipboard) return;
    navigator.clipboard.writeText(SUBMISSION.code).then(() => {
        btn.textContent = 'Copied';
        setTimeout(() => { btn.textContent = 'Copy'; }, 1800);
    });
}

@if($canGrade && $submission->forensic)
let forensicsChartInstance = null;
window.renderForensicsChart = function () {
    const canvas = document.getElementById('forensicsChart');
    if (!canvas || !window.Chart) return;
    const styles = getComputedStyle(document.documentElement);
    const teal = `rgb(${styles.getPropertyValue('--vl-teal').trim()})`;
    const gate = `rgb(${styles.getPropertyValue('--vl-gate').trim()})`;

    if (forensicsChartInstance) {
        forensicsChartInstance.data.datasets[0].backgroundColor = [teal, gate];
        forensicsChartInstance.update();
        return;
    }

    forensicsChartInstance = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: ['Human Typed', 'AI / Pasted'],
            datasets: [{
                data: [{{ (float) $submission->forensic->human_pct }}, {{ (float) $submission->forensic->ai_pct }}],
                backgroundColor: [teal, gate],
                borderWidth: 0,
                hoverOffset: 4,
            }],
        },
        options: {
            cutout: '74%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.label}: ${ctx.parsed}%`,
                    },
                },
            },
        },
    });
};
window.addEventListener('vl:theme-changed', () => {
    if (forensicsChartInstance) window.renderForensicsChart();
});
@endif
</script>
@endpush

@extends('layouts.dashboard')

@section('title', 'Submission — ' . $submission->student->name)
@section('page-title', 'Submission Review')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Breadcrumbs / Header --}}
    <div class="vc-card p-6 border-l-4" style="border-left-color:var(--vc-accent);">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs mb-2" style="color:var(--vc-text-secondary);">
                    <a href="{{ route('courses.index') }}" class="hover:text-current transition-colors">Courses</a>
                    <span>/</span>
                    <a href="{{ route('courses.show', $assignment->course->slug) }}" class="hover:text-current transition-colors">{{ $assignment->course->title }}</a>
                    <span>/</span>
                    <a href="{{ route('assignments.show', $assignment->id) }}" class="hover:text-current transition-colors">{{ $assignment->title }}</a>
                    <span>/</span>
                    <span style="color:var(--vc-muted);">{{ $submission->student->name }}</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg font-black text-white" style="background:var(--vc-info);">
                        {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">{{ $submission->student->name }}</h1>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="badge {{ $submission->status_badge_class }}">{{ ucfirst($submission->status) }}</span>
                            @if($submission->isLate())
                            <span class="badge text-xs" style="color:var(--vc-danger);background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.2);">Late</span>
                            @endif
                            <span class="text-sm font-semibold" style="color:var(--vc-text-secondary);">{{ ucfirst($assignment->starter_language) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grade ring --}}
            @if($submission->status === 'graded')
            <div class="text-center animate-float">
                <div class="relative w-20 h-20 mx-auto mb-1">
                    @php
                        $pct = $assignment->max_points > 0 ? ($submission->grade / $assignment->max_points) * 100 : 0;
                        $c = 2 * pi() * 32;
                        $d = ($pct / 100) * $c;
                        $color = $pct >= 80 ? 'var(--vc-success)' : ($pct >= 60 ? 'var(--vc-warning)' : 'var(--vc-danger)');
                    @endphp
                    <svg class="w-20 h-20" viewBox="0 0 80 80" style="transform:rotate(-90deg)">
                        <circle cx="40" cy="40" r="32" fill="none" stroke="var(--vc-border)" stroke-width="6"/>
                        <circle cx="40" cy="40" r="32" fill="none" stroke="{{ $color }}" stroke-width="6"
                                stroke-dasharray="{{ number_format($d, 2) }} {{ number_format($c, 2) }}"
                                stroke-linecap="round" style="transition:stroke-dasharray 1s ease-out;"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-xl font-black" style="color:{{ $color }};">{{ $submission->grade }}</div>
                        <div class="text-[10px] font-semibold" style="color:var(--vc-muted);">/{{ $assignment->max_points }}</div>
                    </div>
                </div>
                <div class="text-xs font-bold" style="color:var(--vc-text-secondary);">{{ number_format($pct, 0) }}%</div>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── LEFT: CODE VIEWER ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Submission meta --}}
            <div class="vc-card p-5">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm text-center">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--vc-muted);">Submitted</div>
                        <div class="font-bold" style="color:var(--vc-text);">
                            {{ $submission->submitted_at ? $submission->submitted_at->format('M d, H:i') : 'Not yet' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--vc-muted);">Due date</div>
                        <div class="font-bold {{ $assignment->isOverdue() ? '' : '' }}" style="{{ $assignment->isOverdue() ? 'color:var(--vc-danger);' : 'color:var(--vc-text);' }}">
                            {{ $assignment->due_date ? $assignment->due_date->format('M d, H:i') : 'None' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--vc-muted);">Language</div>
                        <div class="font-bold font-mono" style="color:var(--vc-info);">{{ strtoupper($assignment->starter_language) }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--vc-muted);">Max Points</div>
                        <div class="font-bold" style="color:var(--vc-text);">{{ $assignment->max_points }}</div>
                    </div>
                </div>
            </div>

            {{-- Code viewer / Forensics Tabs --}}
            <div class="vc-card overflow-hidden flex flex-col">
                <div class="flex border-b" style="border-color:var(--vc-border);background:var(--vc-elevated);">
                    <button onclick="switchTab('code')" id="tab-code" class="px-6 py-3 text-sm font-bold border-b-2 transition-colors" style="border-color:var(--vc-accent);color:var(--vc-text);">Source Code</button>
                    @if($canGrade)
                    <button onclick="switchTab('forensics')" id="tab-forensics" class="px-6 py-3 text-sm font-bold border-b-2 border-transparent transition-colors" style="color:var(--vc-muted);" onmouseover="if(!this.classList.contains('active'))this.style.color='var(--vc-text)';" onmouseout="if(!this.classList.contains('active'))this.style.color='var(--vc-muted)';">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            VisionGuard Forensics
                        </span>
                    </button>
                    @endif
                </div>

                {{-- CODE TAB --}}
                <div id="content-code" class="flex-1">
                    <div class="flex items-center justify-between px-4 py-3 border-b" style="background:var(--vc-elevated);border-color:var(--vc-border);">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-500/70"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-cyan-500/70"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-green-500/70"></div>
                            <span class="ml-2 text-xs font-mono font-semibold" style="color:var(--vc-text-secondary);">{{ $assignment->starter_language === 'python' ? 'solution.py' : ($assignment->starter_language === 'javascript' ? 'solution.js' : ($assignment->starter_language === 'php' ? 'solution.php' : 'solution.' . $assignment->starter_language)) }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span id="line-count" class="text-xs font-semibold" style="color:var(--vc-muted);"></span>
                            <button onclick="copyCode()" class="text-xs font-bold transition-colors hover:text-current" style="color:var(--vc-text-secondary);" onmouseover="this.style.color='var(--vc-text)';" onmouseout="this.style.color='var(--vc-text-secondary)';">Copy</button>
                        </div>
                    </div>
                    @if($submission->code_snapshot)
                    <div class="overflow-auto bg-black p-4" style="height:500px;">
                        <pre><code id="code-viewer" class="language-{{ $assignment->starter_language }} text-sm font-mono text-gray-300"></code></pre>
                    </div>
                    @else
                    <div class="flex items-center justify-center" style="height:250px;">
                        <div class="text-center text-sm font-semibold" style="color:var(--vc-muted);">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            No code submitted yet
                        </div>
                    </div>
                    @endif
                </div>

                {{-- FORENSICS TAB --}}
                @if($canGrade)
                <div id="content-forensics" class="hidden flex-1 p-6">
                    @if($submission->forensic)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center mb-6">
                            <div class="relative w-48 h-48 mx-auto">
                                <canvas id="forensicsChart"></canvas>
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <div class="text-3xl font-black" style="color:var(--vc-text);">{{ $submission->forensic->human_pct }}%</div>
                                    <div class="text-xs font-bold" style="color:var(--vc-muted);">Human</div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold" style="color:var(--vc-text);">Contribution Analysis</h3>
                                <div class="flex justify-between items-center p-3 rounded-lg border" style="background:var(--vc-bg);border-color:var(--vc-border);">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                        <span class="text-sm font-semibold" style="color:var(--vc-text-secondary);">Human Typed</span>
                                    </div>
                                    <span class="font-mono text-sm" style="color:var(--vc-text);">{{ number_format($submission->forensic->human_keystrokes) }} chars</span>
                                </div>
                                <div class="flex justify-between items-center p-3 rounded-lg border" style="background:var(--vc-bg);border-color:var(--vc-border);">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                                        <span class="text-sm font-semibold" style="color:var(--vc-text-secondary);">AI / Pasted</span>
                                    </div>
                                    <span class="font-mono text-sm" style="color:var(--vc-text);">{{ number_format($submission->forensic->ai_patches_applied + $submission->forensic->pasted_count) }} chars</span>
                                </div>
                                <div class="pt-2">
                                    <span class="text-xs font-semibold px-2 py-1 rounded" style="background:rgba(99,102,241,0.1);color:#818cf8;">
                                        Confidence: {{ ucfirst($submission->forensic->confidence ?? 'high') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 rounded-xl text-xs leading-relaxed" style="background:var(--vc-surface);color:var(--vc-muted);">
                            <strong style="color:var(--vc-text-secondary);">Disclaimer:</strong> VisionGuard classifies keystrokes versus rapid block insertions to estimate AI assistance. This does not definitively prove cheating and should be used alongside academic judgment. Data synced {{ $submission->forensic->last_synced_at ? $submission->forensic->last_synced_at->diffForHumans() : 'never' }}.
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <svg class="w-12 h-12 mb-4 text-indigo-400 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <h3 class="text-lg font-bold" style="color:var(--vc-text);">No Forensics Data Available</h3>
                            <p class="text-sm mt-2 max-w-md" style="color:var(--vc-muted);">VisionGuard has not captured any telemetry for this submission. The student may not have used the VisionLab workspace, or telemetry was blocked.</p>
                        </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Feedback display (for students or if already graded) --}}
            @if($submission->feedback)
            <div class="p-5 rounded-2xl border" style="background:rgba(16,185,129,0.05);border-color:rgba(16,185,129,0.2);">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4" style="color:var(--vc-success);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    <span class="text-sm font-bold" style="color:var(--vc-success);">Instructor Feedback</span>
                    @if($submission->grader)
                    <span class="text-xs font-semibold" style="color:var(--vc-muted);">— {{ $submission->grader->name }}</span>
                    @endif
                </div>
                <p class="text-sm leading-relaxed whitespace-pre-wrap font-medium" style="color:var(--vc-text-secondary);">{{ $submission->feedback }}</p>
            </div>
            @endif
        </div>

        {{-- ── RIGHT: GRADE FORM + INFO ── --}}
        <div class="space-y-6">

            {{-- Grade form (instructors/admins only, and submission must be submitted/late/graded) --}}
            @if($canGrade)
            <div class="vc-card p-5 border-t-4" style="border-top-color:var(--vc-accent);">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center border" style="background:rgba(240,80,0,0.1);border-color:rgba(240,80,0,0.2);">
                        <svg class="w-4 h-4" style="color:var(--vc-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <span class="text-base font-bold" style="color:var(--vc-text);">
                        {{ $submission->status === 'graded' ? 'Update Grade' : 'Grade Submission' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('submissions.grade', $submission->id) }}" id="grade-form">
                    @csrf
                    @method('PATCH')

                    {{-- Grade input with visual indicator --}}
                    <div class="mb-5">
                        <label class="text-xs font-bold uppercase tracking-wider mb-2 block" style="color:var(--vc-muted);">
                            Grade <span style="color:var(--vc-accent);">(0 – {{ $assignment->max_points }})</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="number"
                                   name="grade"
                                   id="grade-input"
                                   value="{{ $submission->grade ?? '' }}"
                                   min="0"
                                   max="{{ $assignment->max_points }}"
                                   placeholder="e.g. 85"
                                   oninput="updateGradeBar(this.value)"
                                   class="vc-input font-bold text-lg"
                                   style="padding:10px 16px;">
                            <span class="text-lg font-bold" style="color:var(--vc-text-secondary);">/ {{ $assignment->max_points }}</span>
                        </div>
                        {{-- Grade bar --}}
                        <div class="mt-3 h-2 rounded-full overflow-hidden" style="background:var(--vc-elevated);">
                            <div id="grade-bar" class="h-full rounded-full transition-all duration-300" style="width:{{ $submission->grade ? ($submission->grade / $assignment->max_points * 100) : 0 }}%;background:var(--vc-accent);"></div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="text-xs font-bold" style="color:var(--vc-muted);">0</span>
                            <span id="grade-pct" class="text-xs font-bold" style="color:var(--vc-text-secondary);">{{ $submission->grade ? number_format($submission->grade / $assignment->max_points * 100, 0) . '%' : '' }}</span>
                            <span class="text-xs font-bold" style="color:var(--vc-muted);">{{ $assignment->max_points }}</span>
                        </div>
                    </div>

                    {{-- Feedback --}}
                    <div class="mb-5">
                        <label class="text-xs font-bold uppercase tracking-wider mb-2 block" style="color:var(--vc-muted);">Feedback <span style="font-weight:normal;">(optional)</span></label>
                        <textarea name="feedback"
                                  rows="5"
                                  placeholder="Great work! Consider improving the error handling in the loop…"
                                  class="vc-input font-medium resize-none"
                                  style="line-height:1.6;">{{ $submission->feedback }}</textarea>
                    </div>

                    {{-- Quick feedback buttons --}}
                    <div class="flex flex-wrap gap-2 mb-5">
                        @foreach(['Excellent work!', 'Good effort!', 'Needs improvement', 'See comments below', 'Check edge cases', 'Add error handling'] as $quick)
                        <button type="button"
                                onclick="appendFeedback('{{ $quick }}')"
                                class="px-2 py-1 rounded-lg text-xs font-semibold transition-all border"
                                style="background:var(--vc-bg);border-color:var(--vc-border);color:var(--vc-text-secondary);"
                                onmouseover="this.style.color='var(--vc-text)';this.style.borderColor='var(--vc-accent)';"
                                onmouseout="this.style.color='var(--vc-text-secondary)';this.style.borderColor='var(--vc-border)';">
                            {{ $quick }}
                        </button>
                        @endforeach
                    </div>

                    @if($errors->any())
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-semibold" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:var(--vc-danger);">{{ $errors->first() }}</div>
                    @endif

                    <button type="submit" class="btn-primary w-full justify-center text-base py-3">
                        {{ $submission->status === 'graded' ? 'Update Grade' : 'Save Grade' }}
                    </button>
                </form>

                @if(session('success'))
                <div class="mt-4 px-4 py-3 rounded-xl text-sm font-semibold" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:var(--vc-success);">{{ session('success') }}</div>
                @endif
            </div>
            @endif

            {{-- Assignment info --}}
            <div class="vc-card p-5">
                <h3 class="text-sm font-bold mb-4" style="color:var(--vc-text);">Assignment Details</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--vc-muted);">Title</div>
                        <div class="font-bold" style="color:var(--vc-text);">{{ $assignment->title }}</div>
                    </div>
                    @if($assignment->description)
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--vc-muted);">Instructions</div>
                        <div class="font-medium line-clamp-4" style="color:var(--vc-text-secondary);">{{ $assignment->description }}</div>
                    </div>
                    @endif
                    <div class="pt-3 border-t" style="border-color:var(--vc-border);">
                        <a href="{{ route('assignments.show', $assignment->id) }}"
                           class="flex items-center gap-2 text-xs font-bold transition-colors hover:text-current" style="color:var(--vc-text-secondary);" onmouseover="this.style.color='var(--vc-text)';" onmouseout="this.style.color='var(--vc-text-secondary)';">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            View all submissions
                        </a>
                    </div>
                </div>
            </div>

            {{-- Other submissions (navigation) --}}
            @if($prevSubmission || $nextSubmission)
            <div class="vc-card p-5">
                <div class="text-xs font-bold uppercase tracking-wider mb-4" style="color:var(--vc-muted);">Navigate Submissions</div>
                <div class="flex flex-col gap-3">
                    @if($prevSubmission)
                    <a href="{{ route('submissions.show', $prevSubmission->id) }}"
                       class="flex items-center gap-3 p-3 rounded-xl transition-all border" style="background:var(--vc-bg);border-color:var(--vc-border);" onmouseover="this.style.borderColor='var(--vc-accent)';" onmouseout="this.style.borderColor='var(--vc-border)';">
                        <svg class="w-4 h-4 flex-shrink-0" style="color:var(--vc-text-secondary);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        <div class="min-w-0">
                            <div class="text-sm font-bold truncate" style="color:var(--vc-text);">{{ $prevSubmission->student->name }}</div>
                            <div class="text-xs font-semibold" style="color:var(--vc-muted);">Previous</div>
                        </div>
                        <span class="ml-auto badge {{ $prevSubmission->status_badge_class }}">{{ ucfirst($prevSubmission->status) }}</span>
                    </a>
                    @endif
                    @if($nextSubmission)
                    <a href="{{ route('submissions.show', $nextSubmission->id) }}"
                       class="flex items-center gap-3 p-3 rounded-xl transition-all border" style="background:var(--vc-bg);border-color:var(--vc-border);" onmouseover="this.style.borderColor='var(--vc-accent)';" onmouseout="this.style.borderColor='var(--vc-border)';">
                        <svg class="w-4 h-4 flex-shrink-0" style="color:var(--vc-text-secondary);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <div class="min-w-0">
                            <div class="text-sm font-bold truncate" style="color:var(--vc-text);">{{ $nextSubmission->student->name }}</div>
                            <div class="text-xs font-semibold" style="color:var(--vc-muted);">Next</div>
                        </div>
                        <span class="ml-auto badge {{ $nextSubmission->status_badge_class }}">{{ ucfirst($nextSubmission->status) }}</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

    </div>
</div>

{{-- Load scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/vs2015.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
const SUBMISSION = {
    code:     @json($submission->code_snapshot ?? ''),
    language: '{{ $assignment->starter_language }}',
    maxPts:   {{ $assignment->max_points }},
};

@if($submission->code_snapshot)
document.addEventListener('DOMContentLoaded', () => {
    const viewer = document.getElementById('code-viewer');
    if (viewer) {
        viewer.textContent = SUBMISSION.code;
        hljs.highlightElement(viewer);
    }
    const lines = SUBMISSION.code.split('\n').length;
    const lineCountEl = document.getElementById('line-count');
    if (lineCountEl) {
        lineCountEl.textContent = lines + ' lines';
    }
});
@endif

// ── Grade bar ────────────────────────────────────────────────────
function updateGradeBar(val) {
    const v   = Math.max(0, Math.min(Number(val) || 0, SUBMISSION.maxPts));
    const pct = SUBMISSION.maxPts > 0 ? (v / SUBMISSION.maxPts * 100) : 0;
    const bar = document.getElementById('grade-bar');
    bar.style.width = pct + '%';
    bar.style.background = pct >= 80 ? 'var(--vc-success)' : pct >= 60 ? 'var(--vc-warning)' : 'var(--vc-danger)';
    document.getElementById('grade-pct').textContent = pct > 0 ? Math.round(pct) + '%' : '';
}

// ── Quick feedback ────────────────────────────────────────────────
function appendFeedback(text) {
    const ta = document.querySelector('textarea[name="feedback"]');
    if (!ta) return;
    ta.value = ta.value ? ta.value.trim() + ' ' + text : text;
    ta.focus();
}

// ── Tabs ─────────────────────────────────────────────────────────
function switchTab(tab) {
    document.getElementById('tab-code').classList.remove('active');
    document.getElementById('tab-code').style.borderColor = 'transparent';
    document.getElementById('tab-code').style.color = 'var(--vc-muted)';
    
    const tabForensics = document.getElementById('tab-forensics');
    if (tabForensics) {
        tabForensics.classList.remove('active');
        tabForensics.style.borderColor = 'transparent';
        tabForensics.style.color = 'var(--vc-muted)';
    }

    document.getElementById('content-code').classList.add('hidden');
    const contentForensics = document.getElementById('content-forensics');
    if (contentForensics) contentForensics.classList.add('hidden');

    const activeBtn = document.getElementById('tab-' + tab);
    if (activeBtn) {
        activeBtn.classList.add('active');
        activeBtn.style.borderColor = 'var(--vc-accent)';
        activeBtn.style.color = 'var(--vc-text)';
    }

    const activeContent = document.getElementById('content-' + tab);
    if (activeContent) activeContent.classList.remove('hidden');

    if (tab === 'forensics' && !window.forensicsChartRendered) {
        renderForensicsChart();
    }
}

// Ensure the first tab is styled correctly
document.addEventListener('DOMContentLoaded', () => {
    switchTab('code');
});

// ── Forensics Chart ──────────────────────────────────────────────
@if($canGrade && $submission->forensic)
function renderForensicsChart() {
    window.forensicsChartRendered = true;
    const ctx = document.getElementById('forensicsChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Human Typed', 'AI / Pasted'],
            datasets: [{
                data: [{{ $submission->forensic->human_pct }}, {{ $submission->forensic->ai_pct }}],
                backgroundColor: ['#10B981', '#8B5CF6'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
}
@endif

// ── Copy code ─────────────────────────────────────────────────────
function copyCode() {
    if (SUBMISSION.code) {
        navigator.clipboard.writeText(SUBMISSION.code).then(() => {
            const btn = event.target;
            btn.textContent = 'Copied!';
            setTimeout(() => btn.textContent = 'Copy', 2000);
        });
    }
}
</script>
@endsection



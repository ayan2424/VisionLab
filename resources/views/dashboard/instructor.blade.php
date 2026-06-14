@extends('layouts.dashboard')
@section('title', 'Instructor Dashboard')
@section('page-title', 'Instructor Command Center')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6"
         style="opacity:0;animation:fadeSlideUp .4s .05s ease forwards">
        <div>
            <h2 class="text-xl font-bold" style="color:var(--vc-text);">Hello, {{ Str::before(auth()->user()->name, ' ') }} 🎓</h2>
            <p class="text-sm mt-0.5" style="color:var(--vc-text-secondary);">Manage your courses and student submissions.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('courses.create') }}" class="btn-primary py-2 px-4 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Course
            </a>
            <a href="{{ route('submissions.queue') }}" class="btn-secondary py-2 px-4 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Grade Queue
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @foreach([
            ['label'=>'My Courses','value'=>$courses->count(),'color'=>'#F05000','icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ['label'=>'Students','value'=>$totalStudents,'color'=>'#0891B2','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label'=>'Pending Grading','value'=>$pendingGrading,'color'=>$pendingGrading > 0 ? '#D97706' : '#059669','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Avg Grade','value'=>$avgGrade ? round($avgGrade, 1) . '%' : '—','color'=>'#8B5CF6','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ] as $i => $s)
        <div class="stat-card group" style="animation:fadeSlideUp .35s ease both;animation-delay:{{ 100+$i*60 }}ms">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110"
                     style="background:{{ $s['color'] }}12;border:1px solid {{ $s['color'] }}18;">
                    <svg class="w-5 h-5" style="color:{{ $s['color'] }};" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $s['icon'] }}"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-black" style="color:var(--vc-text);">{{ $s['value'] }}</div>
                    <div class="text-[11px] font-medium" style="color:var(--vc-text-secondary);">{{ $s['label'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Secondary telemetry row --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        @foreach([
            ['label'=>'Late Submissions','value'=>$lateSubmissions,'color'=>'#DC2626'],
            ['label'=>'Active Workspaces','value'=>$activeWorkspaceCount ?? 0,'color'=>'#059669'],
            ['label'=>'AI Patches Pending','value'=>$pendingPatches ?? 0,'color'=>'#D97706'],
        ] as $i => $t)
        <div class="vc-card p-3 text-center" style="animation:fadeSlideUp .35s ease both;animation-delay:{{ 320+$i*60 }}ms">
            <div class="text-xl font-black" style="color:{{ $t['color'] }};">{{ $t['value'] }}</div>
            <div class="text-[10px] font-medium" style="color:var(--vc-text-secondary);">{{ $t['label'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        {{-- Courses + Submissions --}}
        <div class="lg:col-span-3 space-y-5">
            {{-- Courses --}}
            <div style="opacity:0;animation:fadeSlideUp .4s .25s ease forwards">
                <h3 class="text-xs font-bold uppercase tracking-widest flex items-center gap-2 mb-3" style="color:var(--vc-text);">
                    <span class="w-1 h-4 rounded-full" style="background:var(--vc-accent);"></span> Your Courses
                </h3>
                @forelse($courses as $course)
                <a href="{{ route('courses.show', $course->slug) }}" class="vc-card flex items-center gap-4 p-4 mb-2 hover:-translate-y-0.5 transition-all duration-200">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#F05000,#FF8147);">{{ strtoupper(substr($course->title, 0, 1)) }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold line-clamp-1" style="color:var(--vc-text);">{{ $course->title }}</div>
                        <div class="text-[11px]" style="color:var(--vc-muted);">{{ $course->students_count }} students · Code: {{ $course->enrollment_code }}</div>
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @empty
                <div class="vc-card p-8 text-center" style="border-style:dashed;">
                    <p class="text-sm" style="color:var(--vc-muted);">No courses created yet.</p>
                    <a href="{{ route('courses.create') }}" class="btn-primary py-2 px-5 text-xs mt-3 inline-flex">Create Your First Course</a>
                </div>
                @endforelse
            </div>

            {{-- Recent Submissions --}}
            <div style="opacity:0;animation:fadeSlideUp .4s .35s ease forwards">
                <h3 class="text-xs font-bold uppercase tracking-widest flex items-center gap-2 mb-3" style="color:var(--vc-text);">
                    <span class="w-1 h-4 rounded-full" style="background:#D97706;"></span> Recent Submissions
                </h3>
                <div class="vc-card overflow-hidden">
                    @forelse($recentSubmissions->take(6) as $sub)
                    <div class="flex items-center gap-3 px-4 py-3 transition-colors" style="border-bottom:1px solid var(--vc-border);">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                             style="background:{{ $sub->status === 'graded' ? '#059669' : ($sub->status === 'submitted' ? '#D97706' : 'var(--vc-muted)') }};">
                            {{ strtoupper(substr($sub->student->name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-medium line-clamp-1" style="color:var(--vc-text);">{{ $sub->student->name ?? 'Student' }}</div>
                            <div class="text-[10px]" style="color:var(--vc-muted);">{{ $sub->assignment->title ?? 'Assignment' }}</div>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium"
                              style="background:{{ $sub->status === 'graded' ? 'rgba(5,150,105,.1)' : 'rgba(217,119,6,.1)' }};color:{{ $sub->status === 'graded' ? 'var(--vc-success)' : 'var(--vc-warning)' }};">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-8"><p class="text-xs" style="color:var(--vc-muted);">No submissions yet.</p></div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right sidebar --}}
        <div class="lg:col-span-2 space-y-5">
            @if($pendingGrading > 0)
            <div class="rounded-xl p-4" style="background:rgba(217,119,6,.06);border:1px solid rgba(217,119,6,.15);opacity:0;animation:fadeSlideUp .4s .15s ease forwards">
                <div class="flex items-center gap-2 text-xs font-bold mb-1" style="color:var(--vc-warning);">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    Grading Alert
                </div>
                <p class="text-[11px]" style="color:var(--vc-text-secondary);">You have <strong>{{ $pendingGrading }}</strong> submissions waiting for grading.</p>
                <a href="{{ route('submissions.queue') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold mt-2" style="color:var(--vc-warning);">Go to Queue →</a>
            </div>
            @endif

            <div class="vc-card p-4" style="opacity:0;animation:fadeSlideUp .4s .3s ease forwards">
                <h3 class="text-xs font-bold uppercase tracking-widest flex items-center gap-2 mb-3" style="color:var(--vc-text);">
                    <span class="w-1 h-4 rounded-full" style="background:var(--vc-accent);"></span> Quick Actions
                </h3>
                <div class="space-y-1">
                    @foreach([
                        ['route'=>'courses.create','label'=>'Create New Course','c'=>'#F05000','i'=>'M12 4v16m8-8H4'],
                        ['route'=>'submissions.queue','label'=>'Grade Submissions','c'=>'#D97706','i'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ['route'=>'workspace.index','label'=>'Open Workspace IDE','c'=>'#059669','i'=>'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
                    ] as $act)
                    <a href="{{ route($act['route']) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-all" style="color:var(--vc-text-secondary);">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:{{ $act['c'] }}10;">
                            <svg class="w-3.5 h-3.5" style="color:{{ $act['c'] }};" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $act['i'] }}"/></svg>
                        </div>
                        {{ $act['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

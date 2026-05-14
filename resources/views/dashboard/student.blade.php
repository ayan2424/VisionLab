@extends('layouts.dashboard')
@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- ═══ Welcome + Actions ═══ --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6"
         style="opacity:0;animation:fadeSlideUp .4s .05s ease forwards">
        <div>
            <h2 class="text-xl font-bold" style="color:var(--vc-text);">Welcome back, {{ Str::before(auth()->user()->name, ' ') }} 👋</h2>
            <p class="text-sm mt-0.5" style="color:var(--vc-text-secondary);">{{ now()->format('l, M d Y') }} · Here's your learning overview.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('workspace.index') }}" class="btn-primary py-2 px-4 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                Open IDE
            </a>
            <a href="{{ route('enrollments.join') }}" class="btn-secondary py-2 px-4 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Join Course
            </a>
        </div>
    </div>

    {{-- ═══ Stats Row ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @foreach([
            ['label'=>'Courses','value'=>$courses->count(),'color'=>'#F05000','icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ['label'=>'Pending','value'=>$pendingSubmissions,'color'=>'#0891B2','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['label'=>'Due Soon','value'=>$upcomingAssignments->count(),'color'=>'#D97706','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'News','value'=>$recentAnnouncements->count(),'color'=>'#059669','icon'=>'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
        ] as $i => $s)
        <div class="stat-card group" style="animation:fadeSlideUp .35s ease both;animation-delay:{{ 100 + $i*60 }}ms">
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

    {{-- ═══ Content Grid ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- ── Left: Courses + Announcements (3 cols) ── --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Courses --}}
            <div style="opacity:0;animation:fadeSlideUp .4s .25s ease forwards">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold uppercase tracking-widest flex items-center gap-2" style="color:var(--vc-text);">
                        <span class="w-1 h-4 rounded-full" style="background:var(--vc-accent);"></span> My Courses
                    </h3>
                    <a href="{{ route('courses.index') }}" class="text-[11px] font-medium" style="color:var(--vc-accent);">View all →</a>
                </div>

                @if($courses->isEmpty())
                <div class="vc-card p-10 text-center" style="border-style:dashed;">
                    <svg class="w-10 h-10 mx-auto mb-3" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <p class="text-sm font-semibold mb-1" style="color:var(--vc-text);">No courses yet</p>
                    <p class="text-xs mb-4" style="color:var(--vc-muted);">Join your first course with an enrollment code.</p>
                    <a href="{{ route('enrollments.join') }}" class="btn-primary py-2 px-5 text-xs">Join a Course</a>
                </div>
                @else
                <div class="space-y-2">
                    @foreach($courses as $course)
                    <a href="{{ route('courses.show', $course->slug) }}"
                       class="vc-card flex items-center gap-4 p-4 hover:-translate-y-0.5 transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                             style="background:linear-gradient(135deg,#F05000,#FF8147);">
                            {{ strtoupper(substr($course->title, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold line-clamp-1" style="color:var(--vc-text);">{{ $course->title }}</div>
                            <div class="text-[11px]" style="color:var(--vc-muted);">{{ $course->instructor->name }} · {{ $course->students_count }} students</div>
                        </div>
                        <svg class="w-4 h-4 flex-shrink-0" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Announcements --}}
            <div style="opacity:0;animation:fadeSlideUp .4s .35s ease forwards">
                <h3 class="text-xs font-bold uppercase tracking-widest flex items-center gap-2 mb-3" style="color:var(--vc-text);">
                    <span class="w-1 h-4 rounded-full" style="background:#059669;"></span> Recent Announcements
                </h3>
                @forelse($recentAnnouncements as $ann)
                <div class="vc-card p-4 mb-2">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-orange-600 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0 mt-0.5">
                            {{ strtoupper(substr($ann->author->name ?? 'I', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold" style="color:var(--vc-text);">{{ $ann->title }}</div>
                            <div class="flex items-center gap-2 text-[10px] mt-0.5 mb-1.5" style="color:var(--vc-muted);">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-medium" style="background:rgba(240,80,0,0.06);color:var(--vc-accent);">{{ $ann->course->title }}</span>
                                {{ $ann->created_at->diffForHumans() }}
                            </div>
                            <p class="text-xs leading-relaxed line-clamp-2" style="color:var(--vc-text-secondary);">{{ $ann->body }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="vc-card p-6 text-center">
                    <p class="text-xs" style="color:var(--vc-muted);">No announcements yet.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Right sidebar (2 cols) ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Deadlines --}}
            <div class="vc-card overflow-hidden" style="opacity:0;animation:fadeSlideUp .4s .2s ease forwards">
                <div class="px-4 py-3 flex items-center gap-2" style="border-bottom:1px solid var(--vc-border);">
                    <span class="w-1 h-4 rounded-full" style="background:#D97706;"></span>
                    <h3 class="text-xs font-bold uppercase tracking-widest" style="color:var(--vc-text);">Upcoming Deadlines</h3>
                </div>
                @forelse($upcomingAssignments as $a)
                <a href="{{ route('assignments.show', $a->id) }}" class="flex items-center justify-between px-4 py-3 transition-colors"
                   style="border-bottom:1px solid var(--vc-border);">
                    <div class="min-w-0 mr-3">
                        <div class="text-xs font-medium line-clamp-1" style="color:var(--vc-text);">{{ $a->title }}</div>
                        <div class="text-[10px]" style="color:var(--vc-muted);">{{ $a->course->title }}</div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        @php $d = now()->diffInDays($a->due_date, false); @endphp
                        <div class="text-[11px] font-bold" style="color:{{ $d <= 2 ? 'var(--vc-danger)' : 'var(--vc-text-secondary)' }};">{{ $a->due_date->format('M d') }}</div>
                        <div class="text-[9px]" style="color:{{ $d <= 2 ? 'var(--vc-danger)' : 'var(--vc-muted)' }};">
                            @if($d <= 0) Overdue @elseif($d == 1) Tomorrow @else {{ $d }}d left @endif
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center py-8 px-4">
                    <svg class="w-6 h-6 mx-auto mb-2" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                    <p class="text-xs" style="color:var(--vc-muted);">All caught up!</p>
                </div>
                @endforelse
            </div>

            {{-- Quick Actions --}}
            <div class="vc-card p-4" style="opacity:0;animation:fadeSlideUp .4s .3s ease forwards">
                <h3 class="text-xs font-bold uppercase tracking-widest flex items-center gap-2 mb-3" style="color:var(--vc-text);">
                    <span class="w-1 h-4 rounded-full" style="background:var(--vc-accent);"></span> Quick Actions
                </h3>
                <div class="space-y-1">
                    @foreach([
                        ['route'=>'workspace.index','label'=>'Open Workspace IDE','c'=>'#F05000','i'=>'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
                        ['route'=>'progress.index','label'=>'View My Progress','c'=>'#059669','i'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ['route'=>'enrollments.join','label'=>'Join New Course','c'=>'#0891B2','i'=>'M12 4v16m8-8H4'],
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

            {{-- Platform tip --}}
            <div class="rounded-xl p-4" style="background:rgba(240,80,0,0.05);border:1px solid rgba(240,80,0,0.1);opacity:0;animation:fadeSlideUp .4s .4s ease forwards">
                <div class="flex items-center gap-2 text-xs font-bold mb-1.5" style="color:var(--vc-accent);">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    VisionLab Tip
                </div>
                <p class="text-[11px] leading-relaxed" style="color:var(--vc-text-secondary);">Open the IDE workspace and use the AI Agent in <strong style="color:var(--vc-accent);">AGENT mode</strong> to get code analysis, suggestions, and patch proposals.</p>
            </div>
        </div>
    </div>
</div>
@endsection

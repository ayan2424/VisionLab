@extends('layouts.dashboard')
@section('title', 'Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6" style="opacity:0;animation:fadeSlideUp .4s .05s ease forwards">
        <div>
            <h1 class="text-xl font-bold" style="color:var(--vc-text);">
                @if(auth()->user()->isInstructor()) My Courses
                @elseif(auth()->user()->isStudent()) Enrolled Courses
                @else All Courses @endif
            </h1>
            <p class="text-sm mt-0.5" style="color:var(--vc-text-secondary);">{{ $courses->total() }} course{{ $courses->total() !== 1 ? 's' : '' }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if(auth()->user()->isStudent())
            <a href="{{ route('enrollments.join') }}" class="btn-primary py-2 px-4 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Join Course
            </a>
            @endif
            @if(auth()->user()->isInstructor() || auth()->user()->isAdmin())
            <a href="{{ route('courses.create') }}" class="btn-primary py-2 px-4 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Course
            </a>
            @endif
        </div>
    </div>

    @if($courses->isEmpty())
    <div class="vc-card text-center py-20 px-6" style="opacity:0;animation:fadeSlideUp .4s .15s ease forwards; border-style:dashed;">
        <div class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center mb-4" style="background:rgba(240,80,0,0.08);">
            <svg class="w-8 h-8" style="color:var(--vc-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <h3 class="text-lg font-bold mb-2" style="color:var(--vc-text);">
            @if(auth()->user()->isStudent()) No courses enrolled yet
            @else No courses created yet @endif
        </h3>
        <p class="text-sm mb-6 max-w-md mx-auto" style="color:var(--vc-text-secondary);">
            @if(auth()->user()->isStudent()) Ask your instructor for an enrollment code to join a course.
            @else Create your first course to get started. @endif
        </p>
        @if(auth()->user()->isStudent())
        <a href="{{ route('enrollments.join') }}" class="btn-primary py-2.5 px-6 text-sm mx-auto inline-flex">Join a Course</a>
        @else
        <a href="{{ route('courses.create') }}" class="btn-primary py-2.5 px-6 text-sm mx-auto inline-flex">Create Course</a>
        @endif
    </div>
    @else

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($courses as $i => $course)
        <a href="{{ route('courses.show', $course->slug) }}"
           class="vc-card group overflow-hidden hover:-translate-y-1 hover:shadow-glow-brand transition-all duration-300"
           style="opacity:0;animation:fadeSlideUp .4s ease forwards;animation-delay:{{ $i * 60 + 100 }}ms;">

            {{-- Cover gradient --}}
            <div class="h-28 relative overflow-hidden" style="background:linear-gradient(135deg,rgba(240,80,0,.2),rgba(255,129,71,.1));">
                <div class="absolute inset-0 opacity-30" style="background:radial-gradient(ellipse at 30% 50%, rgba(240,80,0,.5) 0%, transparent 70%);"></div>
                {{-- Course initial --}}
                <div class="absolute bottom-4 left-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl font-black text-white/90"
                         style="background:rgba(0,0,0,.4);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.1);">
                        {{ strtoupper(substr($course->title, 0, 1)) }}
                    </div>
                </div>
                @if(auth()->user()->isInstructor() || auth()->user()->isAdmin())
                <div class="absolute top-3 right-3 px-2 py-0.5 rounded-lg text-xs font-mono font-bold text-white/80"
                     style="background:rgba(0,0,0,.5);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.1);">
                    {{ $course->enrollment_code }}
                </div>
                @endif
            </div>

            <div class="p-4">
                <h3 class="font-bold text-sm leading-tight mb-1 transition-colors" style="color:var(--vc-text);">{{ $course->title }}</h3>
                <p class="text-xs mb-4 line-clamp-2" style="color:var(--vc-text-secondary);">{{ $course->description ?? 'No description.' }}</p>

                <div class="flex items-center justify-between" style="border-top:1px solid var(--vc-border);padding-top:12px;">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                             style="background:linear-gradient(135deg,#F05000,#FF8147);">
                            {{ strtoupper(substr($course->instructor->name ?? 'I', 0, 1)) }}
                        </div>
                        <span class="text-xs font-medium" style="color:var(--vc-muted);">{{ $course->instructor->name ?? 'Instructor' }}</span>
                    </div>
                    <div class="flex items-center gap-1 text-[11px] font-medium" style="color:var(--vc-muted);">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ $course->students_count ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-8">{{ $courses->links() }}</div>
    @endif
</div>
@endsection

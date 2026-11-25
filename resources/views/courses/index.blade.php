@extends('layouts.dashboard')
@section('title', 'Courses')
@section('page-title', 'Courses')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">
                @if(auth()->user()->isInstructor())
                    My Courses
                @elseif(auth()->user()->isStudent())
                    Enrolled Courses
                @else
                    All Courses
                @endif
            </h2>
            <p class="text-sm text-vl-ink-secondary tabular-nums">
                {{ $courses->total() }} {{ \Illuminate\Support\Str::plural('course', $courses->total()) }} available in your catalog
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            @if(auth()->user()->isStudent())
                <a href="{{ route('enrollments.join') }}" class="vl-btn-primary !py-2 !px-4 !text-xs sm:!text-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Join Course
                </a>
            @endif
            @if(auth()->user()->isInstructor() || auth()->user()->isAdmin())
                <a href="{{ route('courses.create') }}" class="vl-btn-primary !py-2 !px-4 !text-xs sm:!text-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Course
                </a>
            @endif
        </div>
    </div>

    @if($courses->isEmpty())
        <x-card :padding="true">
            <x-empty-state
                :title="auth()->user()->isStudent() ? 'No courses enrolled yet' : 'No courses created yet'"
                :description="auth()->user()->isStudent() ? 'Enter an enrollment code from your instructor to join a course.' : 'Create your first course to distribute labs, extension allowlists, and assignments.'"
            >
                @if(auth()->user()->isStudent())
                    <a href="{{ route('enrollments.join') }}" class="vl-btn-primary !py-2 !px-5 !text-xs">Join a Course</a>
                @else
                    <a href="{{ route('courses.create') }}" class="vl-btn-primary !py-2 !px-5 !text-xs">Create Course</a>
                @endif
            </x-empty-state>
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($courses as $course)
                <a href="{{ route('courses.show', $course->slug) }}" class="vl-panel vl-panel-interactive overflow-hidden flex flex-col">
                    <div class="relative h-28 bg-gradient-to-br from-[#0E294B] via-[#103B5E] to-[#0D535B] p-4 flex items-end justify-between">
                        @if($course->cover_image)
                            <img src="{{ Storage::url($course->cover_image) }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-45">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#090D14]/80 via-[#090D14]/25 to-transparent"></div>
                        @endif

                        <div class="relative z-10 h-11 w-11 rounded-xl bg-white/15 backdrop-blur-md text-white font-bold text-base flex items-center justify-center">
                            {{ strtoupper(substr($course->title, 0, 2)) }}
                        </div>

                        @if((auth()->user()->isInstructor() || auth()->user()->isAdmin()) && $course->enrollment_code)
                            <span class="relative z-10 self-start rounded-lg bg-[#090D14]/65 backdrop-blur-md px-2.5 py-1 font-mono text-[11px] font-semibold text-sky-200">
                                {{ $course->enrollment_code }}
                            </span>
                        @endif
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between gap-4">
                        <div class="space-y-1.5">
                            <h3 class="text-base font-bold text-vl-ink leading-snug">{{ $course->title }}</h3>
                            <p class="text-xs leading-relaxed text-vl-ink-secondary line-clamp-2">
                                {{ $course->description ?? 'No course description provided.' }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-vl-border/10 flex items-center justify-between gap-3 text-xs text-vl-ink-muted">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="h-6 w-6 rounded-lg bg-vl-accent/14 text-vl-accent font-bold text-[10px] flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($course->instructor->name ?? 'I', 0, 1)) }}
                                </span>
                                <span class="truncate">{{ $course->instructor->name ?? 'Instructor' }}</span>
                            </div>

                            <div class="flex items-center gap-3 shrink-0 tabular-nums">
                                @if($course->duration)
                                    <span>{{ $course->duration }}</span>
                                @endif
                                <span>{{ $course->students_count ?? 0 }} students</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pt-2">{{ $courses->links() }}</div>
    @endif
</div>
@endsection

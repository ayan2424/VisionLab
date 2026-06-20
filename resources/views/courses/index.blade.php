@extends('layouts.dashboard')
@section('title', 'Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Premium 3D Animated Hero Section --}}
    <div class="relative w-full rounded-2xl overflow-hidden mb-10" style="opacity:0;animation:fadeSlideUp .6s .05s cubic-bezier(0.16, 1, 0.3, 1) forwards; box-shadow: 0 20px 40px rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.05); min-height: 240px; background: #0a0a0a;">
        <!-- Animated 3D Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[-20%] right-[-10%] w-[500px] h-[500px] rounded-full blur-[100px]" style="background: radial-gradient(circle, rgba(240,80,0,0.3) 0%, rgba(0,0,0,0) 70%); animation: pulse 8s infinite alternate;"></div>
            <div class="absolute bottom-[-30%] left-[-10%] w-[400px] h-[400px] rounded-full blur-[80px]" style="background: radial-gradient(circle, rgba(124,58,237,0.2) 0%, rgba(0,0,0,0) 70%); animation: pulse 10s infinite alternate-reverse;"></div>
            
            <!-- 3D Floating Grid -->
            <div class="absolute inset-0" style="background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 30px 30px; transform: perspective(500px) rotateX(60deg) scale(2); transform-origin: top center; opacity: 0.3;"></div>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between h-full p-8 md:p-12">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full mb-4" style="background: rgba(240,80,0,0.1); border: 1px solid rgba(240,80,0,0.2);">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background: var(--vc-brand);"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2" style="background: var(--vc-brand);"></span>
                    </span>
                    <span class="text-[10px] font-bold tracking-widest uppercase text-brand">VisionLab Workspace</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black mb-3 tracking-tight" style="color:var(--vc-text); text-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                    @if(auth()->user()->isInstructor()) My Courses
                    @elseif(auth()->user()->isStudent()) Enrolled Courses
                    @else All Courses @endif
                </h1>
                <p class="text-sm md:text-base text-gray-400 max-w-lg leading-relaxed">
                    Welcome to the next generation of collaborative learning. Manage your courses, launch instant cloud workspaces, and interact with the AI natively.
                </p>
                <div class="mt-6 flex items-center gap-3">
                    @if(auth()->user()->isStudent())
                    <a href="{{ route('enrollments.join') }}" class="btn-primary py-3 px-6 text-sm flex items-center gap-2 group transition-all duration-300 hover:scale-105 hover:shadow-[0_0_20px_rgba(240,80,0,0.4)]">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Join Course
                    </a>
                    @endif
                    @if(auth()->user()->isInstructor() || auth()->user()->isAdmin())
                    <a href="{{ route('courses.create') }}" class="btn-primary py-3 px-6 text-sm flex items-center gap-2 group transition-all duration-300 hover:scale-105 hover:shadow-[0_0_20px_rgba(240,80,0,0.4)]">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create New Course
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- 3D Interactive Mockup Element -->
            <div class="hidden md:block relative w-64 h-48 perspective-1000">
                <div class="absolute inset-0 rounded-xl bg-gray-900 border border-white/10 shadow-2xl transform rotate-y-[-20deg] rotate-x-[10deg] hover:rotate-y-0 hover:rotate-x-0 transition-transform duration-700 ease-out flex items-center justify-center group overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <svg class="w-16 h-16 text-brand/80 drop-shadow-[0_0_15px_rgba(240,80,0,0.5)] transform group-hover:scale-110 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="flex items-center gap-4 mb-8 pb-4 border-b border-white/5">
        <div class="text-sm">
            <span class="text-gray-400">Total Courses:</span>
            <span class="text-white font-bold ml-1">{{ $courses->total() }}</span>
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
                             style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);">
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



@extends('layouts.dashboard')
@section('title', $course->title)
@section('page-title', 'Course Details')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Course header --}}
    <div class="mb-6" style="opacity:0;animation:fadeSlideUp .4s .05s ease forwards">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs mb-2" style="color:var(--vc-text-secondary);">
                    <a href="{{ route('courses.index') }}" class="hover:text-current transition-colors">Courses</a>
                    <span>/</span>
                    <span style="color:var(--vc-muted);">{{ $course->title }}</span>
                </div>
                <h1 class="text-xl font-bold" style="color:var(--vc-text);">{{ $course->title }}</h1>
                <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">{{ $course->instructor->name }} &middot; {{ $course->students()->count() }} students</p>
            </div>
            <div class="flex items-center gap-3">
                @if($isInstructor)
                <span class="px-3 py-1 rounded-lg font-mono text-xs font-bold border"
                      style="color:var(--vc-accent);background:rgba(240,80,0,0.1);border-color:rgba(240,80,0,0.2);">
                    Code: {{ $course->enrollment_code }}
                </span>
                <a href="{{ route('courses.edit', $course->slug) }}" class="btn-ghost py-2 px-4 text-xs">Edit</a>
                @endif
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 mt-6" style="border-bottom:1px solid var(--vc-border);">
            @php
                $availableTabs = ['stream' => 'Stream', 'modules' => 'Curriculum', 'assignments' => 'Assignments', 'quizzes' => 'Quizzes', 'forums' => 'Forums', 'people' => 'People'];
                if ($isInstructor) {
                    $availableTabs['extensions'] = 'Extensions';
                }
            @endphp
            @foreach($availableTabs as $tabKey => $tabLabel)
            <a href="{{ route('courses.show', [$course->slug, 'tab' => $tabKey]) }}"
               class="px-4 py-2 text-sm font-semibold border-b-2 transition-all -mb-px"
               style="{{ $tab === $tabKey ? 'border-color:var(--vc-accent);color:var(--vc-accent);' : 'border-color:transparent;color:var(--vc-text-secondary);' }}">
                {{ $tabLabel }}
                @if($tabKey === 'assignments')
                <span class="ml-1.5 px-1.5 py-0.5 rounded-md text-xs" style="background:var(--vc-border);color:var(--vc-text-secondary);">{{ $course->assignments->count() }}</span>
                @endif
            </a>
            @endforeach
        </div>
    </div>

    {{-- STREAM TAB --}}
    @if($tab === 'stream')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4" style="opacity:0;animation:fadeSlideUp .4s .15s ease forwards">
            @if($isInstructor)
            <form method="POST" action="{{ route('announcements.store', $course->slug) }}" class="vc-card p-5">
                @csrf
                <h3 class="text-sm font-bold mb-4" style="color:var(--vc-text);">Post Announcement</h3>
                <input type="text" name="title" placeholder="Title" required class="vc-input mb-3">
                <textarea name="body" rows="3" placeholder="Share something with your class…" required class="vc-input resize-none mb-3"></textarea>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-xs cursor-pointer" style="color:var(--vc-text-secondary);">
                        <input type="checkbox" name="pinned" value="1" class="rounded" style="accent-color:var(--vc-accent);"> Pin announcement
                    </label>
                    <button type="submit" class="btn-primary py-2 px-5 text-xs">Post</button>
                </div>
            </form>
            @endif

            @forelse($course->announcements as $ann)
            <div class="vc-card p-5" style="{{ $ann->pinned ? 'border-color:rgba(217,119,6,0.3);background:rgba(217,119,6,0.03);' : '' }}">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white"
                             style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);">
                            {{ strtoupper(substr($ann->author->name ?? 'I', 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold" style="color:var(--vc-text);">{{ $ann->author->name }}</div>
                            <div class="text-xs" style="color:var(--vc-muted);">{{ $ann->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($ann->pinned)
                        <span class="text-xs font-semibold" style="color:#D97706;">📌 Pinned</span>
                        @endif
                        @if(!$isInstructor && !in_array($ann->id, $readAnnouncementIds ?? []))
                        <form method="POST" action="{{ route('announcements.read', $ann->id) }}">
                            @csrf
                            <button class="px-2 py-0.5 rounded-lg text-xs font-bold text-white transition-all hover:opacity-80" style="background:var(--vc-accent);">Mark Read</button>
                        </form>
                        @elseif(!$isInstructor && in_array($ann->id, $readAnnouncementIds ?? []))
                        <span class="text-xs font-semibold" style="color:var(--vc-muted);">Read</span>
                        @endif
                        @if($isInstructor)
                        <form method="POST" action="{{ route('announcements.destroy', $ann->id) }}" onsubmit="return confirm('Delete this announcement?')">
                            @csrf @method('DELETE')
                            <button class="text-xs transition-colors hover:text-red-500" style="color:var(--vc-muted);">Delete</button>
                        </form>
                        @endif
                    </div>
                </div>
                <h4 class="text-sm font-bold mb-2" style="color:var(--vc-text);">{{ $ann->title }}</h4>
                <p class="text-sm whitespace-pre-wrap" style="color:var(--vc-text-secondary);">{{ $ann->body }}</p>
            </div>
            @empty
            <div class="text-center py-12 text-sm" style="color:var(--vc-muted);">No announcements yet.</div>
            @endforelse
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4" style="opacity:0;animation:fadeSlideUp .4s .25s ease forwards">
            <div class="vc-card p-5">
                <h3 class="text-sm font-bold mb-3" style="color:var(--vc-text);">Course Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between" style="color:var(--vc-text-secondary);">
                        <span>Instructor</span><span style="color:var(--vc-text);">{{ $course->instructor->name }}</span>
                    </div>
                    <div class="flex justify-between" style="color:var(--vc-text-secondary);">
                        <span>Students</span><span style="color:var(--vc-text);">{{ $course->students()->count() }}</span>
                    </div>
                    <div class="flex justify-between" style="color:var(--vc-text-secondary);">
                        <span>Assignments</span><span style="color:var(--vc-text);">{{ $course->assignments->count() }}</span>
                    </div>

                    @if($course->start_date || $course->end_date || $course->schedule_time || $course->duration)
                    <div class="pt-3 space-y-2" style="border-top:1px solid var(--vc-border);">
                        @if($course->schedule_time)
                        <div>
                            <span class="block text-xs" style="color:var(--vc-muted);">Schedule</span>
                            <span class="font-semibold" style="color:var(--vc-text);">{{ $course->schedule_time }}</span>
                        </div>
                        @endif
                        @if($course->duration)
                        <div>
                            <span class="block text-xs" style="color:var(--vc-muted);">Course Duration</span>
                            <span class="font-semibold" style="color:var(--vc-text);">{{ $course->duration }}</span>
                        </div>
                        @endif
                        @if($course->start_date)
                        <div>
                            <span class="block text-xs" style="color:var(--vc-muted);">Dates</span>
                            <span class="font-semibold" style="color:var(--vc-text);">
                                {{ $course->start_date->format('M d, Y') }} - {{ $course->end_date ? $course->end_date->format('M d, Y') : 'Ongoing' }}
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($course->notes)
                    <div class="pt-3" style="border-top:1px solid var(--vc-border);">
                        <span class="block text-xs mb-1" style="color:var(--vc-muted);">Instructor Notes</span>
                        <div class="text-xs whitespace-pre-wrap" style="color:var(--vc-text-secondary);">{{ $course->notes }}</div>
                    </div>
                    @endif

                    @if($isInstructor)
                    <div class="pt-3" style="border-top:1px solid var(--vc-border);">
                        <div class="text-xs mb-1" style="color:var(--vc-muted);">Enrollment Code</div>
                        <div class="font-mono font-bold text-lg tracking-widest" style="color:var(--vc-accent);">{{ $course->enrollment_code }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Collaborative Workspace --}}
            <div class="rounded-2xl p-5 border" style="background:rgba(6,182,212,0.05);border-color:rgba(6,182,212,0.2);">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center border" style="background:rgba(6,182,212,0.1);border-color:rgba(6,182,212,0.2);">
                        <svg class="w-4 h-4" style="color:#06B6D4;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold" style="color:var(--vc-text);">Live Workspace</div>
                        <div class="text-xs" style="color:var(--vc-muted);">Real-time collaboration</div>
                    </div>
                </div>
                <a href="{{ route('workspace.show', 'course-' . $course->id) }}"
                   class="flex items-center justify-center gap-2 w-full py-2 rounded-xl text-xs font-bold border transition-all"
                   style="color:#06B6D4;border-color:rgba(6,182,212,0.3);" onmouseover="this.style.background='rgba(6,182,212,0.1)'" onmouseout="this.style.background='transparent'">
                    <span style="width:7px;height:7px;border-radius:50%;background:#4ade80;flex-shrink:0;"></span>
                    Open Collaborative Room
                </a>
                <div class="mt-2 text-xs text-center" style="color:var(--vc-muted);">Powered by Laravel Reverb</div>
            </div>

            {{-- My Workspace --}}
            <a href="{{ route('workspace.index') }}" class="vc-card flex items-center gap-3 p-4 hover:-translate-y-1 transition-all">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center border flex-shrink-0"
                     style="background:rgba(240,80,0,0.1);border-color:rgba(240,80,0,0.2);">
                    <svg class="w-4 h-4" style="color:var(--vc-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold" style="color:var(--vc-text);">My Workspace</div>
                    <div class="text-xs" style="color:var(--vc-muted);">VS Code + AI sidebar</div>
                </div>
            </a>
        </div>
    </div>

    {{-- CURRICULUM (MODULES) TAB --}}
    @elseif($tab === 'modules')
    <div style="opacity:0;animation:fadeSlideUp .4s .15s ease forwards">
        @if($isInstructor)
        <div class="flex justify-end mb-5">
            <a href="#" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-500 flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Module
            </a>
        </div>
        @endif

        <div class="space-y-4">
            @forelse($course->modules as $module)
                <div class="vc-card overflow-hidden">
                    <div class="p-4 bg-slate-50 flex justify-between items-center cursor-pointer border-b border-slate-200">
                        <div>
                            <h3 class="font-bold text-gray-900">Module {{ $module->order_index + 1 }}: {{ $module->title }}</h3>
                            @if($module->description)
                                <p class="text-sm text-gray-500 mt-1">{{ $module->description }}</p>
                            @endif
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    <div class="p-4 bg-white space-y-2">
                        @forelse($module->lessons as $lesson)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    @if($lesson->type === 'video')
                                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif($lesson->type === 'pdf')
                                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @endif
                                    <span class="font-medium text-gray-700">{{ $lesson->title }}</span>
                                </div>
                                <div class="text-sm text-gray-400">
                                    {{ $lesson->duration_minutes ? $lesson->duration_minutes . ' mins' : '' }}
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-2">No lessons in this module yet.</p>
                        @endforelse
                        
                        @if($isInstructor)
                        <div class="mt-3 text-right">
                            <a href="#" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ Add Lesson</a>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="vc-card p-10 flex flex-col items-center justify-center text-center">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">No Modules Yet</h3>
                    <p class="text-sm text-gray-500 mt-1">Get started by creating the first curriculum module.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- QUIZZES TAB --}}
    @elseif($tab === 'quizzes')
    <div style="opacity:0;animation:fadeSlideUp .4s .15s ease forwards">
        @if($isInstructor)
        <div class="flex justify-end mb-5">
            <a href="#" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-500 flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Quiz
            </a>
        </div>
        @endif
        <div class="vc-card p-10 flex flex-col items-center justify-center text-center">
            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900">No Quizzes Available</h3>
            <p class="text-sm text-gray-500 mt-1">This course currently has no active quizzes.</p>
        </div>
    </div>

    {{-- FORUMS TAB --}}
    @elseif($tab === 'forums')
    <div style="opacity:0;animation:fadeSlideUp .4s .15s ease forwards">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-gray-900">Discussion Forum</h3>
            <a href="#" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-500 flex items-center gap-2 transition-colors">
                New Topic
            </a>
        </div>
        <div class="vc-card p-10 flex flex-col items-center justify-center text-center">
            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900">No Discussions Yet</h3>
            <p class="text-sm text-gray-500 mt-1">Start a conversation by creating the first topic.</p>
        </div>
    </div>

    {{-- ASSIGNMENTS TAB --}}
    @elseif($tab === 'assignments')
    <div style="opacity:0;animation:fadeSlideUp .4s .15s ease forwards">
        @if($isInstructor)
        <div class="flex justify-end mb-5">
            <a href="{{ route('assignments.create', $course->slug) }}" class="btn-primary py-2 px-4 text-xs">
                <svg class="w-3.5 h-3.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Assignment
            </a>
        </div>
        @endif

        @forelse($course->assignments as $assignment)
        @php
            $sub = $userSubmissions[$assignment->id] ?? null;
            $isOverdue = $assignment->isOverdue();
        @endphp
        <div class="vc-card p-5 mb-4 hover:border-violet-500/30 transition-all">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-bold text-sm" style="color:var(--vc-text);">{{ $assignment->title }}</h3>
                        @if($isOverdue)<span class="px-2 py-0.5 rounded-md text-xs border" style="color:#ef4444;background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.2);">Past Due</span>@endif
                        @if($assignment->due_soon && !$isOverdue)<span class="px-2 py-0.5 rounded-md text-xs border" style="color:#F59E0B;background:rgba(245,158,11,0.1);border-color:rgba(245,158,11,0.2);">Due Soon</span>@endif
                    </div>
                    <p class="text-xs mb-3 line-clamp-2" style="color:var(--vc-text-secondary);">{{ $assignment->description ?? 'No description.' }}</p>
                    <div class="flex items-center gap-4 text-xs" style="color:var(--vc-muted);">
                        @if($assignment->due_date)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Due {{ $assignment->due_date->format('M d, Y H:i') }}
                        </span>
                        @endif
                        <span>{{ $assignment->max_points }} pts</span>
                        @if($isInstructor)
                        <span style="color:var(--vc-accent);">{{ $assignment->submissions()->whereIn('status', ['submitted','graded'])->count() }}/{{ $course->students()->count() }} submitted</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 ml-4">
                    @if($isInstructor)
                    <a href="{{ route('assignments.show', $assignment->id) }}" class="btn-ghost py-1.5 px-3 text-xs">View</a>
                    @else
                    @if(!$sub || $sub->status === 'not_started')
                    <form method="POST" action="{{ route('submissions.start', $assignment->id) }}">
                        @csrf
                        <button type="submit" class="btn-primary py-1.5 px-4 text-xs">Start</button>
                    </form>
                    @elseif($sub->status === 'in_progress')
                    <a href="{{ route('submissions.ide', $assignment->id) }}" class="btn-ghost py-1.5 px-3 text-xs" style="color:var(--vc-accent);border-color:rgba(240,80,0,0.3);">Continue</a>
                    @elseif($sub->status === 'graded')
                    <span class="px-3 py-1.5 rounded-lg text-xs font-bold border" style="color:#10B981;border-color:rgba(16,185,129,0.3);">{{ $sub->grade }}/{{ $assignment->max_points }}</span>
                    @else
                    <span class="px-3 py-1.5 rounded-lg text-xs font-semibold border {{ $sub->status_badge_class }}">{{ ucfirst($sub->status) }}</span>
                    @endif
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-sm" style="color:var(--vc-muted);">No assignments yet.
            @if($isInstructor) <a href="{{ route('assignments.create', $course->slug) }}" class="hover:underline" style="color:var(--vc-accent);">Create one</a>. @endif
        </div>
        @endforelse
    </div>

    {{-- PEOPLE TAB --}}
    @elseif($tab === 'people')
    <div class="space-y-4" style="opacity:0;animation:fadeSlideUp .4s .15s ease forwards">
        <div class="vc-card p-5">
            <h3 class="text-sm font-bold mb-4" style="color:var(--vc-text);">Instructor</h3>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white"
                     style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);">
                    {{ strtoupper(substr($course->instructor->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-semibold" style="color:var(--vc-text);">{{ $course->instructor->name }}</div>
                    <div class="text-xs" style="color:var(--vc-text-secondary);">{{ $course->instructor->email }}</div>
                </div>
            </div>
        </div>

        <div class="vc-card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold" style="color:var(--vc-text);">Students ({{ $students->count() }})</h3>
                @if($isInstructor)
                <a href="{{ route('courses.roster', $course->slug) }}" class="btn-ghost py-1 px-3 text-xs" style="color:var(--vc-accent);border-color:rgba(240,80,0,0.3);">Workspace Roster</a>
                @endif
            </div>

            @if($isInstructor)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6" style="border-bottom:1px solid var(--vc-border);">
                <!-- Single Invite -->
                <div>
                    <h4 class="text-xs font-bold mb-2 uppercase tracking-wide text-gray-500">Manual Invite</h4>
                    <form method="POST" action="{{ route('enrollments.invite', $course->slug) }}" class="flex gap-2">
                        @csrf
                        <input type="email" name="email" placeholder="Student email address..." required class="vc-input flex-1 text-sm">
                        <button type="submit" class="btn-primary py-2 px-4 text-xs whitespace-nowrap">Invite</button>
                    </form>
                </div>
                
                <!-- CSV Import -->
                <div>
                    <h4 class="text-xs font-bold mb-2 uppercase tracking-wide text-gray-500">Bulk Import via CSV</h4>
                    <form method="POST" action="{{ route('enrollments.import_csv', $course->slug) }}" enctype="multipart/form-data" class="flex gap-2 items-center">
                        @csrf
                        <div class="flex-1">
                            <input type="file" name="csv_file" accept=".csv" required 
                                   class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/20 transition-all">
                        </div>
                        <button type="submit" class="btn-ghost py-2 px-4 text-xs whitespace-nowrap" style="color:var(--vc-accent);border-color:rgba(240,80,0,0.3);">Import CSV</button>
                    </form>
                    <div class="mt-1 text-[10px] text-gray-500">CSV must contain an "email" column.</div>
                </div>
            </div>
            @endif

            @forelse($students as $student)
            <div class="flex items-center justify-between py-2.5 last:border-0" style="border-bottom:1px solid var(--vc-border);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white"
                         style="background:#0891B2;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-sm" style="color:var(--vc-text);">{{ $student->name }}</div>
                        <div class="text-xs" style="color:var(--vc-text-secondary);">{{ $student->email }}</div>
                    </div>
                </div>
                @if($isInstructor)
                <form method="POST" action="{{ route('enrollments.remove', [$course->slug, $student->id]) }}" onsubmit="return confirm('Remove this student?')">
                    @csrf @method('DELETE')
                    <button class="text-xs transition-colors hover:text-red-500" style="color:var(--vc-muted);">Remove</button>
                </form>
                @endif
            </div>
            @empty
            <div class="text-center py-8 text-sm" style="color:var(--vc-muted);">No students enrolled yet.</div>
            @endforelse
        </div>
    </div>
    @endif

    {{-- EXTENSIONS TAB --}}
    @if($tab === 'extensions' && $isInstructor)
    <div class="space-y-6" style="opacity:0;animation:fadeSlideUp .4s .15s ease forwards">
        <div class="vc-card p-5">
            <h3 class="text-sm font-bold mb-4" style="color:var(--vc-text);">Course Extensions Policy</h3>
            <p class="text-xs mb-6" style="color:var(--vc-text-secondary);">Select the extensions that should be automatically installed in student workspaces. This forces synchronization to active workspaces.</p>

            <form method="POST" action="{{ route('courses.extensions.update', $course->slug) }}">
                @csrf
                @method('PUT')
                
                @php
                    $allExtensions = \App\Models\Extension::where('is_builtin', false)->get();
                    $courseExtensionIds = $course->extensions()->pluck('extension_id')->toArray();
                @endphp

                <div class="space-y-3 mb-6">
                    @forelse($allExtensions as $index => $ext)
                    <div class="flex items-center justify-between p-3 rounded-lg border transition-colors hover:bg-black/5 dark:hover:bg-white/5" style="border-color:var(--vc-border);">
                        <div class="flex items-center gap-3">
                            <input type="hidden" name="extensions[{{$index}}][id]" value="{{ $ext->id }}">
                            <input type="hidden" name="extensions[{{$index}}][is_required]" value="0">
                            <input type="checkbox" id="ext_{{ $ext->id }}" name="extensions[{{$index}}][is_required]" value="1" 
                                   class="rounded" style="accent-color:var(--vc-accent);"
                                   {{ in_array($ext->id, $courseExtensionIds) ? 'checked' : '' }}>
                            <label for="ext_{{ $ext->id }}" class="cursor-pointer">
                                <div class="text-sm font-bold" style="color:var(--vc-text);">{{ $ext->name }}</div>
                                <div class="text-xs font-mono" style="color:var(--vc-text-secondary);">{{ $ext->package_identifier }} &middot; v{{ $ext->version }}</div>
                            </label>
                        </div>
                        @if($ext->is_global)
                        <span class="text-xs font-bold px-2 py-1 rounded border" style="color:var(--vc-accent);background:rgba(240,80,0,0.1);border-color:rgba(240,80,0,0.2);">Global Default</span>
                        @endif
                    </div>
                    @empty
                    <div class="text-xs" style="color:var(--vc-muted);">No custom extensions available in the registry.</div>
                    @endforelse
                </div>

                <button type="submit" class="btn-primary py-2 px-5 text-sm">Save Policy & Sync Workspaces</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection



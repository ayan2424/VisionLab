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
                $availableTabs = ['stream' => 'Stream', 'assignments' => 'Assignments', 'people' => 'People'];
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
            </div>

            @if($isInstructor)
            <form method="POST" action="{{ route('enrollments.invite', $course->slug) }}" class="flex gap-2 mb-6 pb-6" style="border-bottom:1px solid var(--vc-border);">
                @csrf
                <input type="email" name="email" placeholder="Student email address..." required class="vc-input flex-1 text-sm">
                <button type="submit" class="btn-primary py-2 px-4 text-xs">Invite Student</button>
            </form>
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



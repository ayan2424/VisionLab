@extends('layouts.dashboard')
@section('title', $course->title)
@section('page-title', $course->title)

@section('content')
<div class="space-y-6">
    {{-- Course Header & Tab Strip --}}
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-1.5">
                <nav class="flex items-center gap-2 text-xs text-vl-ink-muted" aria-label="Breadcrumb">
                    <a href="{{ route('courses.index') }}" class="hover:text-vl-ink transition-colors">Courses</a>
                    <span>/</span>
                    <span class="text-vl-ink-secondary truncate">{{ $course->title }}</span>
                </nav>
                <h2 class="text-2xl font-bold tracking-tight text-vl-ink">{{ $course->title }}</h2>
                <p class="text-sm text-vl-ink-secondary">
                    {{ $course->instructor->name }} &middot; <span class="tabular-nums">{{ $course->students()->count() }}</span> students
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if($isInstructor)
                    <span class="rounded-xl bg-vl-accent/12 px-3 py-1.5 font-mono text-xs font-semibold text-vl-accent">
                        Code: {{ $course->enrollment_code }}
                    </span>
                    <a href="{{ route('courses.roster', $course->slug) }}" class="vl-btn-secondary !py-2 !px-3.5 !text-xs">Roster</a>
                    <a href="{{ route('courses.edit', $course->slug) }}" class="vl-btn-secondary !py-2 !px-3.5 !text-xs">Edit Course</a>
                @endif
            </div>
        </div>

        @php
            $availableTabs = [
                'stream' => 'Stream',
                'modules' => 'Curriculum',
                'assignments' => 'Assignments',
                'quizzes' => 'Quizzes',
                'forums' => 'Forums',
                'people' => 'People',
            ];
            if ($isInstructor) {
                $availableTabs['extensions'] = 'Extensions';
            }
        @endphp
        <div class="flex items-center gap-1 overflow-x-auto border-b border-vl-border/10 pb-px" role="tablist">
            @foreach($availableTabs as $tabKey => $tabLabel)
                <a
                    href="{{ route('courses.show', [$course->slug, 'tab' => $tabKey]) }}"
                    class="inline-flex items-center gap-2 whitespace-nowrap px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors duration-150 -mb-px {{ $tab === $tabKey ? 'border-vl-accent text-vl-accent' : 'border-transparent text-vl-ink-secondary hover:text-vl-ink' }}"
                >
                    <span>{{ $tabLabel }}</span>
                    @if($tabKey === 'assignments')
                        <span class="rounded-md bg-vl-surface-raised px-1.5 py-0.5 font-mono text-[11px] text-vl-ink-secondary tabular-nums">{{ $course->assignments->count() }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- STREAM TAB --}}
    @if($tab === 'stream')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8 space-y-4">
                @if($isInstructor)
                    <x-card title="Post Course Announcement" :padding="true">
                        <form method="POST" action="{{ route('announcements.store', $course->slug) }}" class="space-y-3.5">
                            @csrf
                            <input type="text" name="title" placeholder="Announcement title" required class="vl-input">
                            <textarea name="body" rows="3" placeholder="Share lab notes, deadline changes, or clarifications with enrolled students..." required class="vl-input resize-none"></textarea>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <label class="inline-flex items-center gap-2 text-xs text-vl-ink-secondary cursor-pointer">
                                    <input type="checkbox" name="pinned" value="1" class="rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface">
                                    Pin announcement to top
                                </label>
                                <button type="submit" class="vl-btn-primary !py-2 !px-4 !text-xs">Post Announcement</button>
                            </div>
                        </form>
                    </x-card>
                @endif

                @forelse($course->announcements as $ann)
                    <div class="vl-panel p-5 space-y-3 {{ $ann->pinned ? 'ring-1 ring-vl-gate/30' : '' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-8 w-8 rounded-xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($ann->author->name ?? 'I', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-vl-ink truncate">{{ $ann->author->name }}</p>
                                    <p class="text-xs text-vl-ink-muted">{{ $ann->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($ann->pinned)
                                    <x-badge variant="warning">Pinned</x-badge>
                                @endif
                                @if(!$isInstructor && !in_array($ann->id, $readAnnouncementIds ?? []))
                                    <form method="POST" action="{{ route('announcements.read', $ann->id) }}">
                                        @csrf
                                        <button type="submit" class="vl-btn-primary !py-1 !px-2.5 !text-[11px]">Mark Read</button>
                                    </form>
                                @elseif(!$isInstructor && in_array($ann->id, $readAnnouncementIds ?? []))
                                    <x-badge variant="neutral">Read</x-badge>
                                @endif
                                @if($isInstructor)
                                    <form method="POST" action="{{ route('announcements.destroy', $ann->id) }}" onsubmit="event.preventDefault(); vcConfirm('Delete this announcement?', () => this.submit())">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-vl-ink-muted hover:text-vl-danger transition-colors cursor-pointer">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <h3 class="text-sm font-bold text-vl-ink">{{ $ann->title }}</h3>
                            <p class="text-sm leading-relaxed text-vl-ink-secondary whitespace-pre-wrap">{{ $ann->body }}</p>
                        </div>
                    </div>
                @empty
                    <x-card :padding="true">
                        <x-empty-state title="No course announcements yet" description="Announcements posted by the course instructor will appear here." />
                    </x-card>
                @endforelse
            </div>

            <div class="lg:col-span-4 space-y-4">
                <x-card title="Course Details" :padding="true">
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-vl-ink-secondary">Instructor</dt>
                            <dd class="font-medium text-vl-ink">{{ $course->instructor->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-vl-ink-secondary">Enrolled Students</dt>
                            <dd class="font-mono font-semibold text-vl-ink tabular-nums">{{ $course->students()->count() }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-vl-ink-secondary">Assignments</dt>
                            <dd class="font-mono font-semibold text-vl-ink tabular-nums">{{ $course->assignments->count() }}</dd>
                        </div>

                        @if($course->schedule_time || $course->duration || $course->start_date)
                            <div class="pt-3 border-t border-vl-border/10 space-y-2.5">
                                @if($course->schedule_time)
                                    <div>
                                        <dt class="text-xs text-vl-ink-muted">Schedule</dt>
                                        <dd class="font-medium text-vl-ink mt-0.5">{{ $course->schedule_time }}</dd>
                                    </div>
                                @endif
                                @if($course->duration)
                                    <div>
                                        <dt class="text-xs text-vl-ink-muted">Duration</dt>
                                        <dd class="font-medium text-vl-ink mt-0.5">{{ $course->duration }}</dd>
                                    </div>
                                @endif
                                @if($course->start_date)
                                    <div>
                                        <dt class="text-xs text-vl-ink-muted">Term Dates</dt>
                                        <dd class="font-medium text-vl-ink mt-0.5">
                                            {{ $course->start_date->format('M d, Y') }} &ndash; {{ $course->end_date ? $course->end_date->format('M d, Y') : 'Ongoing' }}
                                        </dd>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($course->notes)
                            <div class="pt-3 border-t border-vl-border/10">
                                <dt class="text-xs text-vl-ink-muted mb-1">Instructor Notes</dt>
                                <dd class="text-xs leading-relaxed text-vl-ink-secondary whitespace-pre-wrap">{{ $course->notes }}</dd>
                            </div>
                        @endif

                        @if($isInstructor)
                            <div class="pt-3 border-t border-vl-border/10">
                                <dt class="text-xs text-vl-ink-muted mb-1">Enrollment Join Code</dt>
                                <dd class="font-mono text-lg font-bold tracking-widest text-vl-accent">{{ $course->enrollment_code }}</dd>
                            </div>
                        @endif
                    </dl>
                </x-card>

                <a href="{{ route('workspace.index') }}" class="vl-panel vl-panel-interactive p-4 flex items-center gap-3.5">
                    <div class="h-10 w-10 rounded-xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-vl-ink">Open Cloud Workspace</p>
                        <p class="text-xs text-vl-ink-muted">Browser VS Code &amp; AI Patch Review</p>
                    </div>
                </a>
            </div>
        </div>

    {{-- CURRICULUM TAB --}}
    @elseif($tab === 'modules')
        <div class="space-y-4">
            @forelse($course->modules as $module)
                <x-card :title="'Module ' . ($module->order_index + 1) . ': ' . $module->title" :subtitle="$module->description" :padding="true">
                    <div class="space-y-2">
                        @forelse($module->lessons as $lesson)
                            <div class="flex items-center justify-between gap-3 rounded-xl px-3.5 py-2.5 bg-vl-surface-raised/65">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="font-mono text-[11px] uppercase px-2 py-0.5 rounded bg-vl-surface text-vl-ink-secondary">{{ $lesson->type ?? 'lesson' }}</span>
                                    <span class="text-sm font-medium text-vl-ink truncate">{{ $lesson->title }}</span>
                                </div>
                                @if($lesson->duration_minutes)
                                    <span class="font-mono text-xs text-vl-ink-muted tabular-nums shrink-0">{{ $lesson->duration_minutes }}m</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-vl-ink-muted py-2">No lessons published in this module yet.</p>
                        @endforelse
                    </div>
                </x-card>
            @empty
                <x-card :padding="true">
                    <x-empty-state title="No curriculum modules yet" description="Structured course modules and lesson outlines will appear here once published." />
                </x-card>
            @endforelse
        </div>

    {{-- QUIZZES TAB --}}
    @elseif($tab === 'quizzes')
        <x-card :padding="true">
            <x-empty-state title="No quizzes scheduled" description="This course currently has no active quizzes." />
        </x-card>

    {{-- FORUMS TAB --}}
    @elseif($tab === 'forums')
        <x-card :padding="true">
            <x-empty-state title="No discussion topics yet" description="Course discussion threads will appear here." />
        </x-card>

    {{-- ASSIGNMENTS TAB --}}
    @elseif($tab === 'assignments')
        <div class="space-y-4">
            @if($isInstructor)
                <div class="flex justify-end">
                    <a href="{{ route('assignments.create', $course->slug) }}" class="vl-btn-primary !py-2 !px-4 !text-xs">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Assignment
                    </a>
                </div>
            @endif

            @forelse($course->assignments as $assignment)
                @php
                    $sub = $userSubmissions[$assignment->id] ?? null;
                    $isOverdue = $assignment->isOverdue();
                @endphp
                <div class="vl-panel p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-2 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-base font-bold text-vl-ink">{{ $assignment->title }}</h3>
                            @if($isOverdue)
                                <x-badge variant="danger">Past Due</x-badge>
                            @elseif($assignment->due_soon)
                                <x-badge variant="warning">Due Soon</x-badge>
                            @endif
                        </div>

                        <p class="text-xs sm:text-sm text-vl-ink-secondary line-clamp-2">
                            {{ $assignment->description ?? 'No description provided.' }}
                        </p>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-vl-ink-muted tabular-nums">
                            @if($assignment->due_date)
                                <span>Due {{ $assignment->due_date->format('M d, Y · H:i') }}</span>
                            @endif
                            <span>{{ $assignment->max_points }} pts</span>
                            @if($isInstructor)
                                <span class="text-vl-accent font-medium">
                                    {{ $assignment->submissions()->whereIn('status', ['submitted', 'graded'])->count() }}/{{ $course->students()->count() }} submitted
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        @if($isInstructor)
                            <a href="{{ route('assignments.show', $assignment->id) }}" class="vl-btn-secondary !py-2 !px-4 !text-xs">View Submissions</a>
                        @else
                            @if(!$sub || $sub->status === 'not_started')
                                <form method="POST" action="{{ route('submissions.start', $assignment->id) }}">
                                    @csrf
                                    <button type="submit" class="vl-btn-primary !py-2 !px-4 !text-xs">Start Lab</button>
                                </form>
                            @elseif($sub->status === 'in_progress')
                                <a href="{{ route('submissions.ide', $assignment->id) }}" class="vl-btn-primary !py-2 !px-4 !text-xs">Continue in IDE</a>
                            @elseif($sub->status === 'graded')
                                <x-badge variant="success" class="tabular-nums font-mono !text-xs !px-3 !py-1">
                                    {{ $sub->grade }} / {{ $assignment->max_points }}
                                </x-badge>
                            @else
                                <x-badge variant="info">{{ ucfirst($sub->status) }}</x-badge>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <x-card :padding="true">
                    <x-empty-state title="No assignments published yet" description="Coding labs and graded assignments will appear here.">
                        @if($isInstructor)
                            <a href="{{ route('assignments.create', $course->slug) }}" class="vl-btn-primary !py-2 !px-4 !text-xs">Create First Assignment</a>
                        @endif
                    </x-empty-state>
                </x-card>
            @endforelse
        </div>

    {{-- PEOPLE TAB --}}
    @elseif($tab === 'people')
        <div class="space-y-5">
            <x-card title="Course Instructor" :padding="true">
                <div class="flex items-center gap-3.5">
                    <div class="h-10 w-10 rounded-xl bg-vl-accent/14 text-vl-accent flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr($course->instructor->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-vl-ink">{{ $course->instructor->name }}</p>
                        <p class="text-xs text-vl-ink-secondary">{{ $course->instructor->email }}</p>
                    </div>
                </div>
            </x-card>

            <x-card :title="'Enrolled Students (' . $students->count() . ')'" :padding="true">
                @if($isInstructor)
                    <x-slot:actions>
                        <a href="{{ route('courses.roster', $course->slug) }}" class="vl-btn-secondary !py-1.5 !px-3 !text-xs">Workspace Roster</a>
                    </x-slot:actions>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6 pb-6 border-b border-vl-border/10">
                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-vl-ink">Invite by Institutional Email</p>
                            <form method="POST" action="{{ route('enrollments.invite', $course->slug) }}" class="flex gap-2">
                                @csrf
                                <input type="email" name="email" placeholder="student@university.edu" required class="vl-input flex-1 !py-2">
                                <button type="submit" class="vl-btn-primary !py-2 !px-4 !text-xs shrink-0">Invite</button>
                            </form>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-vl-ink">Bulk Import via CSV</p>
                            <form method="POST" action="{{ route('enrollments.import_csv', $course->slug) }}" enctype="multipart/form-data" class="flex flex-wrap sm:flex-nowrap gap-2 items-center">
                                @csrf
                                <input type="file" name="csv_file" accept=".csv" required class="block w-full text-xs text-vl-ink-secondary file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-vl-surface-raised file:text-vl-ink hover:file:bg-vl-accent/15">
                                <button type="submit" class="vl-btn-secondary !py-2 !px-4 !text-xs shrink-0">Import CSV</button>
                            </form>
                            <p class="text-[11px] text-vl-ink-muted">CSV file must include an <code class="font-mono">email</code> column.</p>
                        </div>
                    </div>
                @endif

                <div class="divide-y divide-vl-border/10">
                    @forelse($students as $student)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-8 w-8 rounded-xl bg-vl-teal/14 text-vl-teal flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-vl-ink truncate">{{ $student->name }}</p>
                                    <p class="text-xs text-vl-ink-muted truncate">{{ $student->email }}</p>
                                </div>
                            </div>
                            @if($isInstructor)
                                <form method="POST" action="{{ route('enrollments.remove', [$course->slug, $student->id]) }}" onsubmit="event.preventDefault(); vcConfirm('Remove this student from the course?', () => this.submit())">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-vl-ink-muted hover:text-vl-danger transition-colors cursor-pointer">Remove</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-vl-ink-muted py-6 text-center">No students enrolled yet.</p>
                    @endforelse
                </div>
            </x-card>
        </div>

    {{-- EXTENSIONS TAB --}}
    @elseif($tab === 'extensions' && $isInstructor)
        <x-card title="Course Extensions Policy" subtitle="Selected SHA256-verified extensions synchronize automatically into student workspaces." :padding="true">
            <form method="POST" action="{{ route('courses.extensions.update', $course->slug) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @php
                    $allExtensions = \App\Models\Extension::where('is_builtin', false)->get();
                    $courseExtensionIds = $course->extensions()->pluck('extension_id')->toArray();
                @endphp

                <div class="space-y-2.5">
                    @forelse($allExtensions as $index => $ext)
                        <label for="ext_{{ $ext->id }}" class="flex items-center justify-between gap-3 rounded-xl p-3.5 bg-vl-surface-raised/65 hover:bg-vl-surface-raised transition-colors duration-150 cursor-pointer">
                            <div class="flex items-center gap-3 min-w-0">
                                <input type="hidden" name="extensions[{{ $index }}][id]" value="{{ $ext->id }}">
                                <input type="hidden" name="extensions[{{ $index }}][is_required]" value="0">
                                <input
                                    type="checkbox"
                                    id="ext_{{ $ext->id }}"
                                    name="extensions[{{ $index }}][is_required]"
                                    value="1"
                                    class="h-4 w-4 rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface"
                                    {{ in_array($ext->id, $courseExtensionIds) ? 'checked' : '' }}
                                >
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-vl-ink truncate">{{ $ext->name }}</p>
                                    <p class="font-mono text-xs text-vl-ink-muted truncate">{{ $ext->package_identifier }} &middot; v{{ $ext->version }}</p>
                                </div>
                            </div>
                            @if($ext->is_global)
                                <x-badge variant="info">Global Default</x-badge>
                            @endif
                        </label>
                    @empty
                        <p class="text-sm text-vl-ink-muted py-4">No custom extensions registered by administrators yet.</p>
                    @endforelse
                </div>

                <div class="pt-2">
                    <button type="submit" class="vl-btn-primary">Save Policy &amp; Sync Workspaces</button>
                </div>
            </form>
        </x-card>
    @endif
</div>
@endsection

@extends('layouts.dashboard')
@section('title', $course->title . ' — Roster')
@section('page-title', 'Workspace Roster')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div class="space-y-1.5">
            <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to {{ $course->title }}
            </a>
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">{{ $course->title }} — Roster</h2>
            <p class="text-sm text-vl-ink-secondary">Enroll students by ID, inspect container states, and synchronize course extensions.</p>
        </div>

        <form action="{{ route('enrollments.add_by_id', $course->slug) }}" method="POST" class="flex flex-wrap sm:flex-nowrap gap-2 items-center">
            @csrf
            <input list="students-list" name="student_id" class="vl-input !py-2 w-full sm:w-64" placeholder="Search by Name or Student ID..." autocomplete="off" required>
            <datalist id="students-list">
                @foreach($availableStudents as $student)
                    @if($student->student_id)
                        <option value="{{ $student->student_id }}">{{ $student->name }} ({{ $student->student_id }})</option>
                    @endif
                @endforeach
            </datalist>
            <button type="submit" class="vl-btn-primary !py-2 !px-4 !text-xs shrink-0">Enroll Student</button>
        </form>
    </div>

    <x-table>
        <x-slot:head>
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Student</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Enrolled</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Workspace Status</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-vl-ink-secondary">Install Extension</th>
                <th class="px-5 py-3.5 text-right text-xs font-semibold text-vl-ink-secondary">Actions</th>
            </tr>
        </x-slot:head>

        @forelse($enrollments as $enrollment)
            @php $workspace = $enrollment->student->workspaces->first(); @endphp
            <tr class="hover:bg-vl-surface-raised/50 transition-colors duration-150">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-xl bg-vl-accent/14 text-vl-accent flex items-center justify-center text-xs font-bold shrink-0">
                            {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-vl-ink truncate">{{ $enrollment->student->name }}</span>
                                @if($enrollment->student->student_id)
                                    <span class="rounded-md bg-vl-surface-raised px-1.5 py-0.5 font-mono text-[10px] font-semibold text-vl-accent">{{ $enrollment->student->student_id }}</span>
                                    <a href="{{ route('portfolio.show', $enrollment->student->student_id) }}" target="_blank" rel="noopener" title="View Public Portfolio" class="text-vl-ink-muted hover:text-vl-accent transition-colors">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    </a>
                                @endif
                            </div>
                            <p class="text-xs text-vl-ink-muted truncate">{{ $enrollment->student->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5 text-xs text-vl-ink-secondary tabular-nums">
                    {{ $enrollment->created_at->format('M d, Y') }}
                </td>
                <td class="px-5 py-3.5">
                    @if($workspace)
                        <x-badge :variant="$workspace->status === 'running' ? 'success' : ($workspace->status === 'stopped' ? 'neutral' : 'info')" dot>
                            {{ ucfirst($workspace->status) }}
                        </x-badge>
                    @else
                        <span class="text-xs text-vl-ink-muted">No workspace</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-center">
                    @if($workspace)
                        <form method="POST" action="{{ route('workspace.extensions.install', ['workspace' => $workspace->slug, 'extension' => 1]) }}" class="inline-flex items-center gap-2" id="form-install-{{ $workspace->id }}">
                            @csrf
                            <select name="extension" class="vl-input !py-1 !px-2.5 !text-xs w-36" onchange="this.form.action='/workspace/{{ $workspace->slug }}/extensions/' + this.value + '/install';">
                                <option value="">Select VSIX</option>
                                @foreach($extensions as $ext)
                                    <option value="{{ $ext->id }}">{{ $ext->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="vl-btn-secondary !py-1 !px-2.5 !text-xs">Add</button>
                        </form>
                    @else
                        <span class="text-xs text-vl-ink-muted">&mdash;</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-right">
                    <div class="inline-flex items-center justify-end gap-2">
                        @if($workspace)
                            <a href="{{ route('workspace.show', $workspace->slug) }}" target="_blank" rel="noopener" class="vl-btn-secondary !py-1 !px-2.5 !text-xs">Open IDE</a>
                            @if($workspace->status === 'running')
                                <form method="POST" action="{{ route('workspace.stop', $workspace->slug) }}" class="inline" onsubmit="event.preventDefault(); vcConfirm('Stop this workspace container?', () => this.submit())">
                                    @csrf
                                    <button type="submit" class="rounded-lg px-2.5 py-1 text-xs font-semibold text-vl-gate bg-vl-gate/12 hover:bg-vl-gate/20 transition-colors cursor-pointer">Stop</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('workspace.start', $workspace->slug) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-lg px-2.5 py-1 text-xs font-semibold text-vl-teal bg-vl-teal/12 hover:bg-vl-teal/20 transition-colors cursor-pointer">Start</button>
                                </form>
                            @endif
                        @endif
                        <form method="POST" action="{{ route('enrollments.remove', [$course->slug, $enrollment->student_id]) }}" class="inline" onsubmit="event.preventDefault(); vcConfirm('Remove student from course?', () => this.submit())">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Remove Student" class="p-1.5 rounded-lg text-vl-ink-muted hover:text-vl-danger hover:bg-vl-danger/10 transition-colors cursor-pointer">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-5 py-10 text-center text-sm text-vl-ink-muted">No students enrolled in this course yet.</td>
            </tr>
        @endforelse
    </x-table>

    @if($enrollments->hasPages())
        <div>{{ $enrollments->links() }}</div>
    @endif
</div>
@endsection

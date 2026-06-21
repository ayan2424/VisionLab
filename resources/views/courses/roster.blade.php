@extends('layouts.app')
@section('title', $course->title . ' - Roster')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('courses.show', $course->slug) }}" class="text-xs hover:underline flex items-center gap-1 w-fit" style="color:var(--vc-muted);">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Course
        </a>
        <h1 class="text-2xl font-bold mt-4" style="color:var(--vc-text);">{{ $course->title }} — Roster</h1>
        <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Manage students and inspect their workspaces.</p>
    </div>

    <div class="vc-card overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead style="background:var(--vc-surface);border-bottom:1px solid var(--vc-border);">
                <tr>
                    <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Student</th>
                    <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Enrolled</th>
                    <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Workspace Status</th>
                    <th class="px-4 py-3 font-semibold text-center" style="color:var(--vc-text-secondary);">Extensions</th>
                    <th class="px-4 py-3 font-semibold text-right" style="color:var(--vc-text-secondary);">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="divide-color:var(--vc-border);">
                @forelse($enrollments as $enrollment)
                @php
                    $workspace = $enrollment->student->workspaces->first();
                @endphp
                <tr class="hover:bg-opacity-5 transition-colors" style="hover:background:var(--vc-border);">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white bg-indigo-500">
                                {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium" style="color:var(--vc-text);">{{ $enrollment->student->name }}</div>
                                <div class="text-[11px]" style="color:var(--vc-muted);">{{ $enrollment->student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs" style="color:var(--vc-text-secondary);">
                        {{ $enrollment->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3">
                        @if($workspace)
                            @if($workspace->status === 'running')
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500">Running</span>
                            @elseif($workspace->status === 'stopped')
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-500/10 text-gray-400">Stopped</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-500">{{ ucfirst($workspace->status) }}</span>
                            @endif
                        @else
                            <span class="text-[10px]" style="color:var(--vc-muted);">No Workspace</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($workspace)
                            <div class="flex items-center justify-center gap-2">
                                <form method="POST" action="{{ route('workspace.extensions.install', ['workspace' => $workspace->id, 'extension' => 1]) }}" class="inline" id="form-install-{{$workspace->id}}">
                                    @csrf
                                    <select name="extension" class="vc-input py-1 px-2 text-[10px] w-32" onchange="this.form.action='/workspace/{{$workspace->id}}/extensions/'+this.value+'/install';">
                                        <option value="">Select Extension</option>
                                        @foreach($extensions as $ext)
                                            <option value="{{ $ext->id }}">{{ $ext->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-ghost text-indigo-400 py-1 px-2 text-[10px]">Add</button>
                                </form>
                            </div>
                        @else
                            <span class="text-[10px]" style="color:var(--vc-muted);">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($workspace)
                            <a href="{{ route('workspace.show', $workspace->id) }}" target="_blank" class="btn-ghost py-1 px-3 text-xs mr-2 text-indigo-400">Open IDE</a>
                            @if($workspace->status === 'running')
                                <form method="POST" action="{{ route('workspace.stop', $workspace->id) }}" class="inline" onsubmit="return confirm('Stop this workspace?');">
                                    @csrf
                                    <button class="btn-ghost text-red-400 py-1 px-3 text-xs">Stop</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('workspace.start', $workspace->id) }}" class="inline">
                                    @csrf
                                    <button class="btn-ghost text-emerald-400 py-1 px-3 text-xs">Start</button>
                                </form>
                            @endif
                        @endif
                        <form method="POST" action="{{ route('enrollments.remove', [$course->slug, $enrollment->student_id]) }}" class="inline ml-2" onsubmit="return confirm('Remove student from course?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:text-red-400 text-xs py-1 px-2"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-sm" style="color:var(--vc-muted);">No students enrolled yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($enrollments->hasPages())
        <div class="px-4 py-3 border-t" style="border-color:var(--vc-border);">
            {{ $enrollments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

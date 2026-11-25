@extends('layouts.dashboard')
@section('title', 'Edit Assignment')
@section('page-title', 'Edit Assignment')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="space-y-1.5">
        <a href="{{ route('assignments.show', $assignment->id) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Assignment
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Edit Assignment</h2>
    </div>

    <x-card :padding="true">
        <form method="POST" action="{{ route('assignments.update', $assignment->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="assignment-title" class="block text-sm font-medium text-vl-ink mb-2">Assignment Title</label>
                <input id="assignment-title" type="text" name="title" value="{{ old('title', $assignment->title) }}" required class="vl-input">
            </div>

            <div>
                <label for="assignment-desc" class="block text-sm font-medium text-vl-ink mb-2">Description / Instructions</label>
                <textarea id="assignment-desc" name="description" rows="5" class="vl-input font-mono text-xs leading-relaxed resize-y">{{ old('description', $assignment->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="max_points" class="block text-sm font-medium text-vl-ink mb-2">Max Points</label>
                    <input id="max_points" type="number" name="max_points" value="{{ old('max_points', $assignment->max_points) }}" min="1" max="1000" required class="vl-input tabular-nums">
                </div>
                <div>
                    <label for="due_date" class="block text-sm font-medium text-vl-ink mb-2">Due Date</label>
                    <input id="due_date" type="datetime-local" name="due_date" value="{{ old('due_date', $assignment->due_date?->format('Y-m-d\TH:i')) }}" class="vl-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="starter_language" class="block text-sm font-medium text-vl-ink mb-2">Language</label>
                    <select id="starter_language" name="starter_language" class="vl-input">
                        @foreach(['python', 'javascript', 'typescript', 'php', 'java', 'c', 'cpp', 'rust', 'go', 'ruby', 'bash'] as $lang)
                            <option value="{{ $lang }}" @selected(old('starter_language', $assignment->starter_language) === $lang)>{{ ucfirst($lang) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="template_id" class="block text-sm font-medium text-vl-ink mb-2">Workspace Template <span class="text-vl-ink-muted font-normal">(optional)</span></label>
                    <select id="template_id" name="template_id" class="vl-input">
                        <option value="">Default Runtime Environment</option>
                        @if(isset($templates))
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" @selected(old('template_id', $assignment->template_id) == $template->id)>
                                    {{ $template->name }} ({{ $template->language }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div>
                <label for="starter_code" class="block text-sm font-medium text-vl-ink mb-2">Starter Code</label>
                <textarea id="starter_code" name="starter_code" rows="8" class="vl-input font-mono text-xs leading-relaxed resize-y">{{ old('starter_code', $assignment->starter_code) }}</textarea>
            </div>

            <label class="flex items-start gap-3.5 rounded-2xl p-4 bg-vl-surface-raised/70 cursor-pointer">
                <input type="checkbox" name="is_locked" value="1" {{ old('is_locked', $assignment->is_locked) ? 'checked' : '' }} class="mt-0.5 h-4 w-4 rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface">
                <span class="space-y-1">
                    <span class="block text-sm font-semibold text-vl-ink">Enable Strict Lockdown (Exam Mode)</span>
                    <span class="block text-xs leading-relaxed text-vl-ink-secondary">Enforces fullscreen focus inside the IDE and logs tab-switch or exit violations.</span>
                </span>
            </label>

            <div class="pt-4 border-t border-vl-border/10 flex items-center justify-end gap-3">
                <a href="{{ route('assignments.show', $assignment->id) }}" class="vl-btn-secondary">Cancel</a>
                <button type="submit" class="vl-btn-primary">Save Changes</button>
            </div>
        </form>
    </x-card>

    {{-- Separate Danger Zone (Zero Nested Forms) --}}
    <div class="vl-panel p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 ring-1 ring-vl-danger/20">
        <div>
            <h3 class="text-sm font-bold text-vl-danger">Delete Assignment</h3>
            <p class="text-xs text-vl-ink-secondary mt-0.5">Permanently removes this assignment and all student submissions.</p>
        </div>
        <form method="POST" action="{{ route('assignments.destroy', $assignment->id) }}" onsubmit="event.preventDefault(); vcConfirm('Delete this assignment and all student submissions?', () => this.submit())">
            @csrf
            @method('DELETE')
            <button type="submit" class="vl-btn-danger !py-2 !px-4 !text-xs shrink-0">Delete Assignment</button>
        </form>
    </div>
</div>
@endsection

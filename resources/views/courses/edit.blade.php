@extends('layouts.dashboard')
@section('title', 'Edit Course')
@section('page-title', 'Edit Course')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="space-y-1.5">
        <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Course
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Edit Course</h2>
        <p class="text-sm text-vl-ink-secondary">Update syllabus metadata, scheduling, and extension marketplace policy.</p>
    </div>

    <x-card :padding="true">
        <form method="POST" action="{{ route('courses.update', $course->slug) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-vl-ink mb-2">Course Title <span class="text-vl-danger">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" required class="vl-input">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-vl-ink mb-2">Description <span class="text-vl-danger">*</span></label>
                <textarea id="description" name="description" rows="4" required class="vl-input resize-none">{{ old('description', $course->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="duration" class="block text-sm font-medium text-vl-ink mb-2">Course Duration</label>
                    <input type="text" id="duration" name="duration" value="{{ old('duration', $course->duration) }}" class="vl-input">
                </div>
                <div>
                    <label for="schedule_time" class="block text-sm font-medium text-vl-ink mb-2">Schedule / Timings</label>
                    <input type="text" id="schedule_time" name="schedule_time" value="{{ old('schedule_time', $course->schedule_time) }}" class="vl-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-vl-ink mb-2">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $course->start_date ? $course->start_date->format('Y-m-d') : '') }}" class="vl-input">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-vl-ink mb-2">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $course->end_date ? $course->end_date->format('Y-m-d') : '') }}" class="vl-input">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-vl-ink mb-2">Instructor Notes</label>
                <textarea id="notes" name="notes" rows="3" class="vl-input resize-none">{{ old('notes', $course->notes) }}</textarea>
            </div>

            <div class="space-y-3 pt-1">
                <label class="flex items-center justify-between gap-4 rounded-xl p-4 bg-vl-surface-raised/70 cursor-pointer">
                    <div>
                        <p class="text-sm font-semibold text-vl-ink">Make Course Active</p>
                        <p class="text-xs text-vl-ink-secondary mt-0.5">Inactive courses are hidden from student enrollment.</p>
                    </div>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface" {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                </label>

                <label class="flex items-center justify-between gap-4 rounded-xl p-4 bg-vl-surface-raised/70 cursor-pointer">
                    <div>
                        <p class="text-sm font-semibold text-vl-ink">Allow Extensions Marketplace</p>
                        <p class="text-xs text-vl-ink-secondary mt-0.5">Disable to restrict student IDEs strictly to course-pinned VSIX packages.</p>
                    </div>
                    <input type="hidden" name="allow_marketplace" value="0">
                    <input type="checkbox" name="allow_marketplace" value="1" class="h-4 w-4 rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface" {{ old('allow_marketplace', $course->allow_marketplace ?? true) ? 'checked' : '' }}>
                </label>
            </div>

            <div class="space-y-3">
                <label for="cover_image" class="block text-sm font-medium text-vl-ink">Cover Image <span class="text-vl-ink-muted font-normal">(optional)</span></label>
                @if($course->cover_image)
                    <div class="overflow-hidden rounded-xl h-32 w-full bg-vl-surface-raised">
                        <img src="{{ Storage::url($course->cover_image) }}" alt="{{ $course->title }} cover" class="h-full w-full object-cover">
                    </div>
                @endif
                <input type="file" id="cover_image" name="cover_image" accept="image/*" class="block w-full text-xs text-vl-ink-secondary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-vl-surface-raised file:text-vl-ink hover:file:bg-vl-accent/15">
            </div>

            <div class="pt-5 border-t border-vl-border/10 flex items-center justify-end gap-3">
                <a href="{{ route('courses.show', $course->slug) }}" class="vl-btn-secondary">Cancel</a>
                <button type="submit" class="vl-btn-primary">Save Changes</button>
            </div>
        </form>
    </x-card>

    {{-- Separate Danger Zone (Zero Nested Forms) --}}
    <div class="vl-panel p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 ring-1 ring-vl-danger/20">
        <div>
            <h3 class="text-sm font-bold text-vl-danger">Delete Course</h3>
            <p class="text-xs text-vl-ink-secondary mt-0.5">Permanently removes this course and associated enrollments. This cannot be undone.</p>
        </div>
        <form method="POST" action="{{ route('courses.destroy', $course->slug) }}" onsubmit="event.preventDefault(); vcConfirm('Delete this course permanently? This cannot be undone.', () => this.submit())">
            @csrf
            @method('DELETE')
            <button type="submit" class="vl-btn-danger !py-2 !px-4 !text-xs shrink-0">Delete Course</button>
        </form>
    </div>
</div>
@endsection

@extends('layouts.dashboard')
@section('title', 'Create Course')
@section('page-title', 'Create Course')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="space-y-1.5">
        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Courses
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Create New Course</h2>
        <p class="text-sm text-vl-ink-secondary">Configure course details and IDE extension rules. An enrollment code is generated automatically.</p>
    </div>

    <x-card :padding="true">
        <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-vl-ink mb-2">Course Title <span class="text-vl-danger">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g., CS-301 Systems Programming" class="vl-input">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-vl-ink mb-2">Description <span class="text-vl-danger">*</span></label>
                <textarea id="description" name="description" rows="4" required placeholder="Outline course objectives, lab expectations, and toolchain..." class="vl-input resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="duration" class="block text-sm font-medium text-vl-ink mb-2">Course Duration</label>
                    <input type="text" id="duration" name="duration" value="{{ old('duration') }}" placeholder="e.g., 16 Weeks" class="vl-input">
                </div>
                <div>
                    <label for="schedule_time" class="block text-sm font-medium text-vl-ink mb-2">Schedule / Timings</label>
                    <input type="text" id="schedule_time" name="schedule_time" value="{{ old('schedule_time') }}" placeholder="e.g., Mon/Wed 10:00 – 11:30" class="vl-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-vl-ink mb-2">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" class="vl-input">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-vl-ink mb-2">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" class="vl-input">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-vl-ink mb-2">Instructor Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Office hours, syllabus reference, or lab environment notes..." class="vl-input resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="space-y-3 pt-1">
                <label class="flex items-center justify-between gap-4 rounded-xl p-4 bg-vl-surface-raised/70 cursor-pointer">
                    <div>
                        <p class="text-sm font-semibold text-vl-ink">Make Course Active</p>
                        <p class="text-xs text-vl-ink-secondary mt-0.5">Students can enroll and access labs only while active.</p>
                    </div>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface" {{ old('is_active', true) ? 'checked' : '' }}>
                </label>

                <label class="flex items-center justify-between gap-4 rounded-xl p-4 bg-vl-surface-raised/70 cursor-pointer">
                    <div>
                        <p class="text-sm font-semibold text-vl-ink">Allow Extensions Marketplace</p>
                        <p class="text-xs text-vl-ink-secondary mt-0.5">Disable to restrict workspaces strictly to SHA256-verified course allowlists.</p>
                    </div>
                    <input type="hidden" name="allow_marketplace" value="0">
                    <input type="checkbox" name="allow_marketplace" value="1" class="h-4 w-4 rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface" {{ old('allow_marketplace', true) ? 'checked' : '' }}>
                </label>
            </div>

            <div>
                <label for="cover_image" class="block text-sm font-medium text-vl-ink mb-2">Cover Image <span class="text-vl-ink-muted font-normal">(optional)</span></label>
                <input type="file" id="cover_image" name="cover_image" accept="image/*" class="block w-full text-xs text-vl-ink-secondary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-vl-surface-raised file:text-vl-ink hover:file:bg-vl-accent/15">
                <p class="mt-1.5 text-xs text-vl-ink-muted">PNG or JPG up to 2 MB.</p>
            </div>

            <div class="pt-5 border-t border-vl-border/10 flex items-center justify-end gap-3">
                <a href="{{ route('courses.index') }}" class="vl-btn-secondary">Cancel</a>
                <button type="submit" class="vl-btn-primary">Create Course</button>
            </div>
        </form>
    </x-card>
</div>
@endsection

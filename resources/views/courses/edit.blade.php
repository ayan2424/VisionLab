@extends('layouts.dashboard')

@section('title', 'Edit Course')
@section('page-title', 'Edit Course')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('courses.show', $course->slug) }}" class="flex items-center gap-2 text-sm transition-colors mb-6 font-semibold" style="color:var(--vc-muted);" onmouseover="this.style.color='var(--vc-text)';" onmouseout="this.style.color='var(--vc-muted)';">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Course
        </a>
        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Edit Course</h1>
    </div>

    <form method="POST" action="{{ route('courses.update', $course->slug) }}" enctype="multipart/form-data"
          class="vc-card p-8 animate-float" style="animation:fadeSlideUp .4s ease-out forwards;">
        @csrf @method('PUT')

        @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl text-sm font-semibold" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:var(--vc-danger);">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="space-y-6">
            <div class="relative">
                <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" required
                       class="vc-input peer w-full placeholder-transparent pt-6 pb-2"
                       placeholder="Course Title">
                <label for="title" class="absolute left-4 top-2 text-xs font-bold text-brand transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand" style="color:var(--vc-accent);">Course Title <span style="color:var(--vc-danger);">*</span></label>
            </div>

            <div class="relative">
                <textarea id="description" name="description" rows="4" required
                          class="vc-input peer w-full placeholder-transparent pt-8 pb-2 resize-none"
                          placeholder="Description">{{ old('description', $course->description) }}</textarea>
                <label for="description" class="absolute left-4 top-2 text-xs font-bold text-brand transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand" style="color:var(--vc-accent);">Description <span style="color:var(--vc-danger);">*</span></label>
            </div>

            <div class="relative">
                <input type="text" id="duration" name="duration" value="{{ old('duration', $course->duration) }}"
                       class="vc-input peer w-full placeholder-transparent pt-6 pb-2"
                       placeholder="e.g., 6 Months, 12 Weeks">
                <label for="duration" class="absolute left-4 top-2 text-xs font-bold text-brand transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand" style="color:var(--vc-accent);">Course Duration</label>
            </div>

            <!-- Scheduling Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="relative">
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $course->start_date ? $course->start_date->format('Y-m-d') : '') }}"
                           class="vc-input peer w-full placeholder-transparent pt-6 pb-2">
                    <label for="start_date" class="absolute left-4 top-2 text-xs font-bold text-brand transition-all" style="color:var(--vc-accent);">Start Date</label>
                </div>
                <div class="relative">
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $course->end_date ? $course->end_date->format('Y-m-d') : '') }}"
                           class="vc-input peer w-full placeholder-transparent pt-6 pb-2">
                    <label for="end_date" class="absolute left-4 top-2 text-xs font-bold text-brand transition-all" style="color:var(--vc-accent);">End Date</label>
                </div>
            </div>

            <div class="relative">
                <input type="text" id="schedule_time" name="schedule_time" value="{{ old('schedule_time', $course->schedule_time) }}"
                       class="vc-input peer w-full placeholder-transparent pt-6 pb-2"
                       placeholder="e.g., Mon/Wed/Fri 10:00 AM - 12:00 PM">
                <label for="schedule_time" class="absolute left-4 top-2 text-xs font-bold text-brand transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand" style="color:var(--vc-accent);">Schedule / Timings</label>
            </div>

            <div class="relative">
                <textarea id="notes" name="notes" rows="3"
                          class="vc-input peer w-full placeholder-transparent pt-8 pb-2 resize-none"
                          placeholder="General notes, syllabus link, or meeting info">{{ old('notes', $course->notes) }}</textarea>
                <label for="notes" class="absolute left-4 top-2 text-xs font-bold text-brand transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand" style="color:var(--vc-accent);">Instructor Notes</label>
            </div>

            <!-- Is Active Toggle -->
            <div class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/5">
                <div>
                    <label class="block text-sm font-bold text-white mb-1">Make Course Active</label>
                    <p class="text-xs text-gray-400">If inactive, students won't be able to join or see this course yet.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand" style="peer-checked:background-color: var(--vc-brand);"></div>
                </label>
            </div>

            <!-- Allow Marketplace Toggle -->
            <div class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/5">
                <div>
                    <label class="block text-sm font-bold text-white mb-1">Allow Extensions Marketplace</label>
                    <p class="text-xs text-gray-400">If disabled, students cannot access the VS Code Extension Marketplace in their governed workspaces.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="allow_marketplace" value="0">
                    <input type="checkbox" name="allow_marketplace" value="1" class="sr-only peer" {{ old('allow_marketplace', $course->allow_marketplace ?? true) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand" style="peer-checked:background-color: var(--vc-brand);"></div>
                </label>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 text-white/80">Cover Image <span class="text-gray-500 font-normal">(optional)</span></label>
                @if($course->cover_image)
                <div class="mb-4 relative rounded-xl overflow-hidden h-32 w-full">
                    <img src="{{ Storage::url($course->cover_image) }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                        <span class="text-sm font-bold text-white">Current Cover</span>
                    </div>
                </div>
                @endif
                <div class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                     style="border-color:var(--vc-border);"
                     onmouseover="this.style.borderColor='var(--vc-accent)'"
                     onmouseout="this.style.borderColor='var(--vc-border)'"
                     onclick="document.getElementById('cover_image').click()">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-400">Click to upload new image</p>
                    <p class="text-xs mt-1 text-gray-500">PNG, JPG up to 2MB</p>
                </div>
                <input type="file" id="cover_image" name="cover_image" accept="image/*" class="hidden">
            </div>
        </div>

        <div class="mt-8 pt-6 border-t flex flex-col sm:flex-row items-center justify-between gap-4" style="border-color:var(--vc-border);">
            <form method="POST" action="{{ route('courses.destroy', $course->slug) }}" onsubmit="event.preventDefault(); vcConfirm('Delete this course? This cannot be undone.', () => this.submit())">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all border w-full sm:w-auto" style="color:var(--vc-danger);background:transparent;border-color:rgba(239,68,68,0.3);" onmouseover="this.style.background='rgba(239,68,68,0.1)';" onmouseout="this.style.background='transparent';">Delete Course</button>
            </form>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('courses.show', $course->slug) }}" class="btn-secondary w-full sm:w-auto text-center">Cancel</a>
                <button type="submit" class="btn-primary w-full sm:w-auto">Save Changes</button>
            </div>
        </div>
    </form>
</div>
@endsection


@extends('layouts.dashboard')
@section('title', 'Create Course')
@section('page-title', 'Create Course')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="mb-8" style="opacity:0;animation:fadeSlideUp .4s .05s ease forwards">
        <a href="{{ route('courses.index') }}" class="flex items-center gap-2 text-sm transition-colors mb-6" style="color:var(--vc-muted);">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Courses
        </a>
        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Create New Course</h1>
        <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Set up your course details. An enrollment code will be generated automatically.</p>
    </div>

    <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data"
          class="vc-card p-8" style="opacity:0;animation:fadeSlideUp .4s .15s ease forwards">
        @csrf

        @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl text-sm" style="background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2);color:var(--vc-danger);">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="space-y-6">
            <div class="relative">
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="vc-input peer w-full placeholder-transparent pt-6 pb-2"
                       placeholder="Course Title">
                <label for="title" class="absolute left-4 top-2 text-xs font-bold text-brand transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand" style="color:var(--vc-accent);">Course Title <span style="color:var(--vc-danger);">*</span></label>
            </div>

            <div class="relative">
                <textarea id="description" name="description" rows="4" required
                          class="vc-input peer w-full placeholder-transparent pt-8 pb-2 resize-none"
                          placeholder="Description">{{ old('description') }}</textarea>
                <label for="description" class="absolute left-4 top-2 text-xs font-bold text-brand transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand" style="color:var(--vc-accent);">Description <span style="color:var(--vc-danger);">*</span></label>
            </div>

            <!-- Is Active Toggle -->
            <div class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/5">
                <div>
                    <label class="block text-sm font-bold text-white mb-1">Make Course Active</label>
                    <p class="text-xs text-gray-400">If inactive, students won't be able to join or see this course yet.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
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
                    <input type="checkbox" name="allow_marketplace" value="1" class="sr-only peer" {{ old('allow_marketplace', true) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand" style="peer-checked:background-color: var(--vc-brand);"></div>
                </label>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 text-white/80">Cover Image <span class="text-gray-500 font-normal">(optional)</span></label>
                <div class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                     style="border-color:var(--vc-border);"
                     onmouseover="this.style.borderColor='var(--vc-accent)'"
                     onmouseout="this.style.borderColor='var(--vc-border)'"
                     onclick="document.getElementById('cover_image').click()">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-400">Click to upload or drag & drop</p>
                    <p class="text-xs mt-1 text-gray-500">PNG, JPG up to 2MB</p>
                </div>
                <input type="file" id="cover_image" name="cover_image" accept="image/*" class="hidden">
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end gap-3" style="border-top:1px solid var(--vc-border);padding-top:20px;">
            <a href="{{ route('courses.index') }}" class="btn-ghost py-2.5 px-5 text-sm">Cancel</a>
            <button type="submit" class="btn-primary py-2.5 px-6 text-sm">
                Create Course
            </button>
        </div>
    </form>
</div>
@endsection



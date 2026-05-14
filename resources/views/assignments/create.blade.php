@extends('layouts.dashboard')

@section('title', 'Create Assignment')
@section('page-title', 'Create Assignment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('courses.show', [$course->slug, 'tab' => 'assignments']) }}" class="flex items-center gap-2 text-sm transition-colors mb-6" style="color:var(--vc-text-secondary);">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span class="hover:text-current">Back to {{ $course->title }}</span>
        </a>
        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Create Assignment</h1>
        <p class="text-sm mt-1" style="color:var(--vc-muted);">For: <span style="color:var(--vc-accent);">{{ $course->title }}</span></p>
    </div>

    <form method="POST" action="{{ route('assignments.store', $course->slug) }}"
          class="vc-card p-8 space-y-6 animate-float" style="animation: fadeSlideUp 0.4s ease-out forwards;">
        @csrf

        @if($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:var(--vc-danger);">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Assignment Title <span style="color:var(--vc-danger);">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required class="vc-input">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Description / Instructions</label>
            <textarea name="description" rows="5" class="vc-input resize-none font-mono"
                      placeholder="Describe the assignment requirements in detail…">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Max Points <span style="color:var(--vc-danger);">*</span></label>
                <input type="number" name="max_points" value="{{ old('max_points', 100) }}" min="1" max="1000" required class="vc-input">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Due Date</label>
                <input type="datetime-local" name="due_date" value="{{ old('due_date') }}" class="vc-input">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Starter Language</label>
            <select name="starter_language" class="vc-input">
                @foreach(['python','javascript','typescript','php','java','c','cpp','rust','go','ruby','bash'] as $lang)
                <option value="{{ $lang }}" {{ old('starter_language') === $lang ? 'selected' : '' }}>{{ ucfirst($lang) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Starter Code <span class="font-normal" style="color:var(--vc-muted);">(optional — given to students)</span></label>
            <textarea name="starter_code" rows="8" class="vc-input resize-y font-mono"
                      placeholder="# Starter code for students…">{{ old('starter_code') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('courses.show', [$course->slug, 'tab' => 'assignments']) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                Create Assignment
            </button>
        </div>
    </form>
</div>
@endsection

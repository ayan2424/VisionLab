@extends('layouts.dashboard')

@section('title', 'Edit Assignment')
@section('page-title', 'Edit Assignment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('assignments.show', $assignment->id) }}" class="flex items-center gap-2 text-sm transition-colors mb-6" style="color:var(--vc-text-secondary);">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span class="hover:text-current">Back to Assignment</span>
        </a>
        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Edit Assignment</h1>
    </div>

    <form method="POST" action="{{ route('assignments.update', $assignment->id) }}"
          class="vc-card p-8 space-y-6 animate-float" style="animation: fadeSlideUp 0.4s ease-out forwards;">
        @csrf @method('PUT')

        @if($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:var(--vc-danger);">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Assignment Title</label>
            <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required class="vc-input">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Description</label>
            <textarea name="description" rows="5" class="vc-input resize-none font-mono">{{ old('description', $assignment->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Max Points</label>
                <input type="number" name="max_points" value="{{ old('max_points', $assignment->max_points) }}" min="1" max="1000" required class="vc-input">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Due Date</label>
                <input type="datetime-local" name="due_date" value="{{ old('due_date', $assignment->due_date?->format('Y-m-d\TH:i')) }}" class="vc-input">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Language</label>
            <select name="starter_language" class="vc-input">
                @foreach(['python','javascript','typescript','php','java','c','cpp','rust','go','ruby','bash'] as $lang)
                <option value="{{ $lang }}" {{ old('starter_language', $assignment->starter_language) === $lang ? 'selected' : '' }}>{{ ucfirst($lang) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Starter Code</label>
            <textarea name="starter_code" rows="8" class="vc-input resize-y font-mono">{{ old('starter_code', $assignment->starter_code) }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
            <form method="POST" action="{{ route('assignments.destroy', $assignment->id) }}" onsubmit="return confirm('Delete this assignment? All submissions will be lost.')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition-all" style="color:var(--vc-danger);border:1px solid rgba(239,68,68,0.3);">Delete</button>
            </form>
            <div class="flex items-center gap-3">
                <a href="{{ route('assignments.show', $assignment->id) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </div>
    </form>
</div>
@endsection



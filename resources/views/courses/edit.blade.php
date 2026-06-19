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
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2" style="color:var(--vc-muted);">Course Title</label>
                <input type="text" name="title" value="{{ old('title', $course->title) }}" required class="vc-input">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2" style="color:var(--vc-muted);">Description</label>
                <textarea name="description" rows="4" class="vc-input resize-none">{{ old('description', $course->description) }}</textarea>
            </div>
            <div class="flex items-center gap-3 p-4 rounded-xl border" style="background:var(--vc-bg);border-color:var(--vc-border);">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ $course->is_active ? 'checked' : '' }} class="w-5 h-5 rounded border" style="accent-color:var(--vc-accent);border-color:var(--vc-border);">
                <label for="is_active" class="text-sm font-semibold cursor-pointer" style="color:var(--vc-text);">Course is active (students can enroll)</label>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t flex flex-col sm:flex-row items-center justify-between gap-4" style="border-color:var(--vc-border);">
            <form method="POST" action="{{ route('courses.destroy', $course->slug) }}" onsubmit="return confirm('Delete this course? This cannot be undone.')">
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



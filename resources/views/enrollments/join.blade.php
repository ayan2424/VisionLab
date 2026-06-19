@extends('layouts.dashboard')

@section('title', 'Join a Course')
@section('page-title', 'Join a Course')

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5 animate-float" style="background:var(--vc-accent);box-shadow:0 0 30px rgba(var(--vc-accent), 0.4);">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <h1 class="text-3xl font-black" style="color:var(--vc-text);">Join a Course</h1>
            <p class="text-sm mt-3 font-medium" style="color:var(--vc-text-secondary);">Enter the enrollment code provided by your instructor.</p>
        </div>

        <form method="POST" action="{{ route('enrollments.join.post') }}"
              class="vc-card p-8 animate-float" style="animation:fadeSlideUp .4s ease-out forwards;">
            @csrf

            @if($errors->any())
            <div class="mb-6 px-4 py-3 rounded-xl text-sm font-semibold" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:var(--vc-danger);">
                {{ $errors->first() }}
            </div>
            @endif

            <div class="mb-8">
                <label class="block text-xs font-bold uppercase tracking-wider mb-3 text-center" style="color:var(--vc-muted);">Enrollment Code</label>
                <input type="text" name="enrollment_code" value="{{ old('enrollment_code') }}"
                       class="vc-input w-full text-center text-3xl font-mono font-black tracking-[0.25em] uppercase h-16"
                       placeholder="ABC123" maxlength="8" autocomplete="off"
                       oninput="this.value=this.value.toUpperCase()">
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-4 text-base">
                Join Course
            </button>

            <div class="text-center mt-6">
                <a href="{{ route('courses.index') }}" class="text-sm font-bold transition-colors hover:text-current" style="color:var(--vc-muted);" onmouseover="this.style.color='var(--vc-text)';" onmouseout="this.style.color='var(--vc-muted)';">← Back to my courses</a>
            </div>
        </form>
    </div>
</div>
@endsection



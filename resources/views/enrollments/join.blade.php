@extends('layouts.dashboard')
@section('title', 'Join a Course')
@section('page-title', 'Join a Course')

@section('content')
<div class="flex items-center justify-center min-h-[68vh]">
    <div class="w-full max-w-md space-y-6">
        <div class="text-center space-y-2.5">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-vl-accent/14 text-vl-accent">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Join a Course</h2>
            <p class="text-sm text-vl-ink-secondary">
                Enter the enrollment join code provided by your course instructor.
            </p>
        </div>

        <x-card :padding="true">
            <form method="POST" action="{{ route('enrollments.join.post') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="enrollment_code" class="block text-xs font-semibold text-vl-ink-secondary text-center mb-2.5">
                        Enrollment Code
                    </label>
                    <input
                        id="enrollment_code"
                        type="text"
                        name="enrollment_code"
                        value="{{ old('enrollment_code') }}"
                        placeholder="ABC123"
                        maxlength="8"
                        autocomplete="off"
                        required
                        oninput="this.value = this.value.toUpperCase()"
                        class="vl-input text-center font-mono text-2xl sm:text-3xl font-bold tracking-[0.26em] uppercase h-16"
                    >
                </div>

                <button type="submit" class="vl-btn-primary w-full !py-3">
                    Join Course
                </button>

                <div class="text-center">
                    <a href="{{ route('courses.index') }}" class="text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
                        &larr; Back to my courses
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection

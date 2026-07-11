@extends('layouts.admin')
@section('title', 'Edit Course Batch')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.batches.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/[0.05] transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Edit Course Batch</h1>
            <p class="text-slate-400 text-sm">Update schedule and details for {{ $batch->title }}.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.batches.update', $batch) }}" class="vc-card p-6 border-t-4" style="border-top-color:var(--vc-accent);">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Course</label>
                <select name="course_id" class="vc-input w-full" required>
                    <option value="">Select Course...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $batch->course_id) == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
                @error('course_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Department</label>
                <select name="department_id" class="vc-input w-full" required>
                    <option value="">Select Department...</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id', $batch->department_id) == $department->id ? 'selected' : '' }}>
                            {{ $department->name }} ({{ $department->campus->name ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('department_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Semester</label>
                <select name="semester_id" class="vc-input w-full" required>
                    <option value="">Select Semester...</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ old('semester_id', $batch->semester_id) == $semester->id ? 'selected' : '' }}>
                            {{ $semester->term }} {{ $semester->year }}
                        </option>
                    @endforeach
                </select>
                @error('semester_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Instructor</label>
                <select name="instructor_id" class="vc-input w-full">
                    <option value="">Select Instructor...</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ old('instructor_id', $batch->instructor_id) == $instructor->id ? 'selected' : '' }}>
                            {{ $instructor->name }}
                        </option>
                    @endforeach
                </select>
                @error('instructor_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-2 text-slate-300">Batch Title</label>
                <input type="text" name="title" value="{{ old('title', $batch->title) }}" required class="vc-input w-full" placeholder="e.g. Fall 2026 Morning Batch">
                @error('title') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Timing</label>
                <input type="text" name="timing" value="{{ old('timing', $batch->timing) }}" class="vc-input w-full" placeholder="e.g. 09:00 AM - 12:00 PM">
                @error('timing') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date', $batch->start_date?->format('Y-m-d')) }}" class="vc-input w-full">
                @error('start_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Max Capacity</label>
                <input type="number" name="max_capacity" value="{{ old('max_capacity', $batch->max_capacity) }}" class="vc-input w-full" placeholder="e.g. 30">
                @error('max_capacity') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Room Number</label>
                <input type="text" name="room_number" value="{{ old('room_number', $batch->room_number) }}" class="vc-input w-full" placeholder="e.g. Lab 3">
                @error('room_number') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-8">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $batch->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-600 text-indigo-600 focus:ring-indigo-500 bg-gray-800">
                <span class="text-sm font-bold text-slate-300">Is Active</span>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.batches.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-white/[0.05] hover:bg-white/[0.1] transition-colors">Cancel</a>
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold shadow-lg">Update Batch</button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Create User')
@section('page-title', 'Create User')

@section('content')
<div class="max-w-xl space-y-6">
    <div class="space-y-1.5">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Users
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Create New User</h2>
        <p class="text-sm text-vl-ink-secondary">Provision an institutional student, instructor, or administrator account.</p>
    </div>

    <x-card :padding="true">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="user-name" class="block text-sm font-medium text-vl-ink mb-2">Full Name</label>
                <input id="user-name" type="text" name="name" value="{{ old('name') }}" required class="vl-input">
            </div>

            <div>
                <label for="user-email" class="block text-sm font-medium text-vl-ink mb-2">Institutional Email</label>
                <input id="user-email" type="email" name="email" value="{{ old('email') }}" required class="vl-input">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="user-password" class="block text-sm font-medium text-vl-ink mb-2">Password</label>
                    <input id="user-password" type="password" name="password" required class="vl-input">
                </div>
                <div>
                    <label for="user-password-confirm" class="block text-sm font-medium text-vl-ink mb-2">Confirm Password</label>
                    <input id="user-password-confirm" type="password" name="password_confirmation" required class="vl-input">
                </div>
            </div>

            <div class="rounded-2xl bg-vl-accent/10 p-4 space-y-2.5">
                <p class="text-sm font-semibold text-vl-ink">Role Assignment</p>
                <p class="text-xs text-vl-ink-secondary">Selecting Student automatically generates and assigns a unique institutional Student ID.</p>
                <select id="role-select" name="role" class="vl-input">
                    <option value="student" @selected(old('role', 'student') === 'student')>Student</option>
                    <option value="instructor" @selected(old('role') === 'instructor')>Instructor</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                </select>
            </div>

            <div id="course-assignment-panel" class="rounded-2xl bg-vl-teal/10 p-4 space-y-3.5 {{ old('role', 'student') === 'student' ? 'block' : 'hidden' }}">
                <div>
                    <p class="text-sm font-semibold text-vl-ink">Instant Course Enrollment <span class="font-normal text-vl-ink-muted">(optional)</span></p>
                    <p class="text-xs text-vl-ink-secondary mt-0.5">Assign the student directly to an active course and batch schedule.</p>
                </div>

                <div>
                    <label for="course-select" class="block text-xs font-semibold text-vl-ink-secondary mb-1.5">Course</label>
                    <select id="course-select" name="course_id" class="vl-input">
                        <option value="">No course selected</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="batch-select" class="block text-xs font-semibold text-vl-ink-secondary mb-1.5">Batch / Timing</label>
                    <select id="batch-select" name="course_batch_id" class="vl-input" disabled>
                        <option value="">Select a course first</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="user-status" class="block text-sm font-medium text-vl-ink mb-2">Account Status</label>
                <select id="user-status" name="status" class="vl-input">
                    <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                    <option value="suspended" @selected(old('status') === 'suspended')>Suspended</option>
                </select>
            </div>

            <div class="pt-4 border-t border-vl-border/10 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="vl-btn-secondary">Cancel</a>
                <button type="submit" class="vl-btn-primary">Create User</button>
            </div>
        </form>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const roleSelect = document.getElementById('role-select');
    const coursePanel = document.getElementById('course-assignment-panel');
    const courseSelect = document.getElementById('course-select');
    const batchSelect = document.getElementById('batch-select');
    const coursesData = @json($courses->keyBy('id'));

    roleSelect?.addEventListener('change', () => {
        const isStudent = roleSelect.value === 'student';
        coursePanel.classList.toggle('block', isStudent);
        coursePanel.classList.toggle('hidden', !isStudent);
    });

    courseSelect?.addEventListener('change', () => {
        const courseId = courseSelect.value;
        batchSelect.innerHTML = '<option value="">No batch selected</option>';

        if (courseId && coursesData[courseId] && coursesData[courseId].batches?.length > 0) {
            batchSelect.disabled = false;
            coursesData[courseId].batches.forEach((batch) => {
                const option = document.createElement('option');
                option.value = batch.id;
                option.textContent = `${batch.title} (${batch.timing}) — Starts ${String(batch.start_date || '').substring(0, 10)}`;
                batchSelect.appendChild(option);
            });
        } else {
            batchSelect.disabled = true;
            batchSelect.innerHTML = courseId
                ? '<option value="">No batches available</option>'
                : '<option value="">Select a course first</option>';
        }
    });
});
</script>
@endpush

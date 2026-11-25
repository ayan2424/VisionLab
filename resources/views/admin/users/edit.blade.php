@extends('layouts.admin')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-xl space-y-6">
    <div class="space-y-1.5">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Users
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Edit User</h2>
    </div>

    <div class="vl-panel p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl shrink-0 flex items-center justify-center text-lg font-bold text-white {{ $user->role === 'admin' ? 'bg-vl-danger' : ($user->role === 'instructor' ? 'bg-vl-accent-cta' : 'bg-vl-teal') }}">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="min-w-0">
            <p class="text-base font-bold text-vl-ink truncate">{{ $user->name }}</p>
            <p class="text-xs text-vl-ink-secondary truncate">{{ $user->email }}</p>
            <p class="text-[11px] text-vl-ink-muted mt-0.5">Joined {{ $user->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    <x-card :padding="true">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="edit-name" class="block text-sm font-medium text-vl-ink mb-2">Full Name</label>
                <input id="edit-name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="vl-input">
            </div>

            <div>
                <label for="edit-email" class="block text-sm font-medium text-vl-ink mb-2">Email</label>
                <input id="edit-email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="vl-input">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit-role" class="block text-sm font-medium text-vl-ink mb-2">Role</label>
                    <select id="edit-role" name="role" class="vl-input">
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                        <option value="instructor" @selected(old('role', $user->role) === 'instructor')>Instructor</option>
                        <option value="student" @selected(old('role', $user->role) === 'student')>Student</option>
                    </select>
                </div>
                <div>
                    <label for="edit-status" class="block text-sm font-medium text-vl-ink mb-2">Status</label>
                    <select id="edit-status" name="status" class="vl-input">
                        <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
                        <option value="suspended" @selected(old('status', $user->status) === 'suspended')>Suspended</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="edit-student-id" class="block text-sm font-medium text-vl-ink mb-2">Student ID <span class="font-normal text-vl-ink-muted">(if student)</span></label>
                <input id="edit-student-id" type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}" class="vl-input font-mono">
            </div>

            <div class="pt-4 border-t border-vl-border/10 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="vl-btn-secondary">Cancel</a>
                <button type="submit" class="vl-btn-primary">Save Changes</button>
            </div>
        </form>
    </x-card>
</div>
@endsection

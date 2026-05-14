@extends('layouts.admin')
@section('title', 'Edit User')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-white">Edit User</h1>
    </div>

    <div class="flex items-center gap-4 p-5 rounded-2xl border border-white/[0.07] mb-6" style="background:#111111;">
        <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-black text-white
            {{ $user->role === 'admin' ? 'bg-red-600' : ($user->role === 'instructor' ? 'bg-violet-600' : 'bg-cyan-600') }}">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <div class="text-base font-bold text-white">{{ $user->name }}</div>
            <div class="text-sm text-slate-500">{{ $user->email }}</div>
            <div class="text-xs text-slate-600 mt-0.5">Joined {{ $user->created_at->format('M d, Y') }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}"
          class="rounded-2xl border border-white/[0.07] p-6 space-y-5" style="background:#111111;">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Role</label>
            <select name="role" class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="instructor" {{ old('role', $user->role) === 'instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Student</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Status</label>
            <select name="status" class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl text-sm text-slate-400 border border-white/[0.08] hover:text-white transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">Save Changes</button>
        </div>
    </form>
</div>
@endsection

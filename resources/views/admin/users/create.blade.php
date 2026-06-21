@extends('layouts.admin')
@section('title', 'Create User')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-white">Create New User</h1>
    </div>

    @if($errors->any())
    <div class="mb-6 px-4 py-3 rounded-xl text-sm font-semibold" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:var(--vc-danger);">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}"
          class="rounded-2xl border border-white/[0.07] p-6 space-y-5" style="background:#111111;">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
            </div>
        </div>

        <div class="bg-violet-500/10 border border-violet-500/20 rounded-xl p-4 mt-2 mb-2">
            <h4 class="text-sm font-bold text-violet-400 mb-1">Role Selection</h4>
            <p class="text-xs text-slate-400 mb-3">If you select "Student", the system will automatically generate and assign a unique Student ID to this account.</p>
            <select name="role" class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
                <option value="student" {{ old('role', 'student') === 'student' ? 'selected' : '' }}>Student</option>
                <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Status</label>
            <select name="status" class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl text-sm text-slate-400 border border-white/[0.08] hover:text-white transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">Create User</button>
        </div>
    </form>
</div>
@endsection

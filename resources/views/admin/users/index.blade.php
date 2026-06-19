@extends('layouts.admin')
@section('title', 'User Management')
@section('page-title', 'Users')

@section('content')
<div class="flex items-center justify-between mb-6" style="opacity:0;animation:fadeSlideUp .5s .05s ease forwards">
    <h1 class="text-xl font-bold" style="color:var(--vc-text);">User Management</h1>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6" style="opacity:0;animation:fadeSlideUp .5s .1s ease forwards">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email…"
           class="vc-input flex-1 min-w-[200px]">
    <select name="role" class="vc-input">
        <option value="">All Roles</option>
        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="instructor" {{ request('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
    </select>
    <select name="status" class="vc-input">
        <option value="">All Status</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
    </select>
    <button type="submit" class="btn-primary py-2.5 px-5 text-sm">Filter</button>
    <a href="{{ route('admin.users.index') }}" class="btn-ghost py-2.5 px-5 text-sm">Clear</a>
</form>

<div class="vc-card overflow-x-auto" style="opacity:0;animation:fadeSlideUp .5s .15s ease forwards">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr style="border-bottom:1px solid var(--vc-border);background:rgba(0,0,0,0.02);" class="dark:bg-[#0d1117]">
                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted);">User</th>
                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted);">Role</th>
                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted);">Status</th>
                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted);">Joined</th>
                <th class="px-5 py-3 text-xs font-semibold text-right" style="color:var(--vc-muted);">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="transition-colors hover:bg-black/5 dark:hover:bg-white/5" style="border-bottom:1px solid var(--vc-border);">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                             style="background:{{ $user->role === 'admin' ? '#ef4444' : ($user->role === 'instructor' ? 'linear-gradient(135deg,#7c3aed,#8b5cf6)' : '#06b6d4') }}">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold truncate" style="color:var(--vc-text);">{{ $user->name }}</div>
                            <div class="text-xs truncate" style="color:var(--vc-text-secondary);">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4">
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold border"
                          style="{{ $user->role === 'admin' ? 'color:#ef4444;background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.2)' : ($user->role === 'instructor' ? 'color:var(--vc-accent);background:rgba(240,80,0,0.1);border-color:rgba(240,80,0,0.2)' : 'color:#06b6d4;background:rgba(6,182,212,0.1);border-color:rgba(6,182,212,0.2)') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold border"
                          style="{{ $user->status === 'active' ? 'color:#10B981;background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.2)' : 'color:#ef4444;background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.2)' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td class="px-5 py-4 text-xs" style="color:var(--vc-text-secondary);">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="px-5 py-4">
                    <div class="flex items-center justify-end gap-2">
                        @if($user->id !== auth()->id())
                        <a href="{{ route('admin.users.impersonate', $user->id) }}" class="btn-ghost py-1 px-3 text-xs" style="color:var(--vc-info);border-color:rgba(59,130,246,0.3);" onmouseover="this.style.background='rgba(59,130,246,0.1)'" onmouseout="this.style.background='transparent'" title="Login as this user">Impersonate</a>
                        @endif
                        <a href="{{ route('admin.users.export', $user->id) }}" class="btn-ghost py-1 px-3 text-xs" style="color:var(--vc-accent);border-color:rgba(240,80,0,0.3);" onmouseover="this.style.background='rgba(240,80,0,0.1)'" onmouseout="this.style.background='transparent'" title="Download GDPR Data">Export</a>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-ghost py-1 px-3 text-xs">Edit</a>
                        @if($user->status === 'active' && $user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}">
                            @csrf
                            <button class="btn-ghost py-1 px-3 text-xs" style="color:#F59E0B;border-color:rgba(245,158,11,0.3);" onmouseover="this.style.background='rgba(245,158,11,0.1)'" onmouseout="this.style.background='transparent'">Suspend</button>
                        </form>
                        @elseif($user->status === 'suspended')
                        <form method="POST" action="{{ route('admin.users.activate', $user->id) }}">
                            @csrf
                            <button class="btn-ghost py-1 px-3 text-xs" style="color:#10B981;border-color:rgba(16,185,129,0.3);" onmouseover="this.style.background='rgba(16,185,129,0.1)'" onmouseout="this.style.background='transparent'">Activate</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-12 text-center text-sm" style="color:var(--vc-muted);">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $users->withQueryString()->links() }}</div>
@endsection



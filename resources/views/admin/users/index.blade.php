@extends('layouts.admin')
@section('title', 'User Management')
@section('page-title', 'Users')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">User Management</h2>
            <p class="text-sm text-vl-ink-secondary">Provision accounts, manage institutional roles, export GDPR data, and enforce suspensions.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="vl-btn-primary !py-2 !px-4 !text-xs sm:!text-sm shrink-0">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create New User
        </a>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" class="vl-panel p-4 flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, or student ID..." class="vl-input flex-1 min-w-[200px] !py-2">
        <select name="role" class="vl-input w-full sm:w-40 !py-2">
            <option value="">All Roles</option>
            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            <option value="instructor" @selected(request('role') === 'instructor')>Instructor</option>
            <option value="student" @selected(request('role') === 'student')>Student</option>
        </select>
        <select name="status" class="vl-input w-full sm:w-40 !py-2">
            <option value="">All Status</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
        </select>
        <button type="submit" class="vl-btn-primary !py-2 !px-4 !text-xs">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="vl-btn-secondary !py-2 !px-4 !text-xs">Clear</a>
    </form>

    <x-table>
        <x-slot:head>
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">User</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Student ID</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Role</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Status</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Joined</th>
                <th class="px-5 py-3.5 text-right text-xs font-semibold text-vl-ink-secondary">Actions</th>
            </tr>
        </x-slot:head>

        @forelse($users as $user)
            <tr class="hover:bg-vl-surface-raised/50 transition-colors duration-150">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-xl shrink-0 flex items-center justify-center text-xs font-bold text-white {{ $user->role === 'admin' ? 'bg-vl-danger' : ($user->role === 'instructor' ? 'bg-vl-accent-cta' : 'bg-vl-teal') }}">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-vl-ink truncate">{{ $user->name }}</p>
                            <p class="text-xs text-vl-ink-muted truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5 font-mono text-xs text-vl-ink-secondary">
                    {{ $user->student_id ?? '—' }}
                </td>
                <td class="px-5 py-3.5">
                    <x-badge :variant="$user->role === 'admin' ? 'danger' : ($user->role === 'instructor' ? 'info' : 'success')">
                        {{ ucfirst($user->role) }}
                    </x-badge>
                </td>
                <td class="px-5 py-3.5">
                    <x-badge :variant="$user->status === 'active' ? 'success' : 'danger'" dot>
                        {{ ucfirst($user->status) }}
                    </x-badge>
                </td>
                <td class="px-5 py-3.5 text-xs text-vl-ink-secondary tabular-nums">
                    {{ $user->created_at->format('M d, Y') }}
                </td>
                <td class="px-5 py-3.5 text-right">
                    <div class="inline-flex flex-wrap items-center justify-end gap-1.5">
                        @if($user->id !== auth()->id())
                            <a href="{{ route('admin.users.impersonate', $user->id) }}" class="vl-btn-secondary !py-1 !px-2.5 !text-xs" title="Impersonate User">Impersonate</a>
                        @endif
                        <a href="{{ route('admin.users.export', $user->id) }}" class="vl-btn-secondary !py-1 !px-2.5 !text-xs" title="Download GDPR Export">Export</a>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="vl-btn-secondary !py-1 !px-2.5 !text-xs">Edit</a>
                        @if($user->status === 'active' && $user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="rounded-xl px-2.5 py-1 text-xs font-semibold text-vl-gate bg-vl-gate/12 hover:bg-vl-gate/22 transition-colors cursor-pointer">Suspend</button>
                            </form>
                        @elseif($user->status === 'suspended')
                            <form method="POST" action="{{ route('admin.users.activate', $user->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="rounded-xl px-2.5 py-1 text-xs font-semibold text-vl-teal bg-vl-teal/12 hover:bg-vl-teal/22 transition-colors cursor-pointer">Activate</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-5 py-10 text-center text-sm text-vl-ink-muted">No users match your current filters.</td>
            </tr>
        @endforelse
    </x-table>

    <div>{{ $users->withQueryString()->links() }}</div>
</div>
@endsection

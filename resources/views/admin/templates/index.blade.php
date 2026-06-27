@extends('layouts.admin')
@section('title', 'Manage Workspace Templates')
@section('page-title', 'Workspace Templates')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Workspace Templates</h1>
        <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Manage dynamic Nix-based environments for workspaces</p>
    </div>
    <a href="{{ route('admin.templates.create') }}" class="btn-primary py-2 px-4 rounded-lg font-medium shadow-lg hover:shadow-xl transition-all">
        Create Template
    </a>
</div>

<div class="vc-card overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead style="background:var(--vc-surface);border-bottom:1px solid var(--vc-border);">
            <tr>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Name</th>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Language/Stack</th>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Git URL</th>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Status</th>
                <th class="px-4 py-3 font-semibold text-right" style="color:var(--vc-text-secondary);">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y" style="divide-color:var(--vc-border);">
            @forelse($templates as $template)
            <tr class="hover:bg-opacity-5 transition-colors" style="hover:background:var(--vc-border);">
                <td class="px-4 py-3">
                    <div class="font-medium" style="color:var(--vc-text);">{{ $template->name }}</div>
                    <div class="text-[11px]" style="color:var(--vc-muted);">{{ \Illuminate\Support\Str::limit($template->description, 50) }}</div>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-[11px] font-bold" style="background:var(--vc-accent);color:var(--vc-surface);">
                        {{ strtoupper($template->language) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    @if($template->git_url)
                        <a href="{{ $template->git_url }}" target="_blank" class="hover:underline" style="color:var(--vc-accent);">
                            {{ \Illuminate\Support\Str::limit($template->git_url, 30) }}
                        </a>
                    @else
                        <span style="color:var(--vc-muted);">-</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($template->is_active)
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500">Active</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-red-500/10 text-red-500">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn-ghost py-1 px-3 text-xs mr-2">Edit</a>
                    <form method="POST" action="{{ route('admin.templates.destroy', $template->id) }}" class="inline" onsubmit="return confirm('Delete this template? Note: Existing workspaces will not be affected.');">
                        @csrf
                        @method('DELETE')
                        <button class="btn-ghost text-red-500 py-1 px-3 text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-sm" style="color:var(--vc-muted);">No templates found. Create one to get started!</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

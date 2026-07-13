@extends('layouts.admin')
@section('title', 'Manage Quotas')
@section('page-title', 'System Quotas')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Quotas & Limits</h1>
        <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Configure resource limits per role</p>
    </div>
</div>

<div class="vc-card p-6">
    <form method="POST" action="{{ route('admin.quotas.updateGlobal') }}">
        @csrf
        <div class="space-y-6">
            @foreach($quotas as $i => $quota)
            <div class="p-4 rounded-xl" style="background:var(--vc-surface); border:1px solid var(--vc-border);">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-white text-xs" 
                         style="background:{{ $quota->scope === 'student' ? '#16A34A' : ($quota->scope === 'instructor' ? '#7c3aed' : '#3B82F6') }}">
                        {{ strtoupper(substr($quota->scope, 0, 1)) }}
                    </div>
                    <h3 class="font-bold uppercase tracking-wider text-sm" style="color:var(--vc-text);">
                        {{ ucfirst($quota->scope) }} Quota
                    </h3>
                </div>

                <input type="hidden" name="quotas[{{ $i }}][id]" value="{{ $quota->id }}">
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color:var(--vc-text-secondary);">Max Active Workspaces</label>
                        <input type="number" name="quotas[{{ $i }}][max_workspaces]" value="{{ old('quotas.'.$i.'.max_workspaces', $quota->max_workspaces) }}" class="w-full text-sm rounded-lg" style="background:var(--vc-bg);border:1px solid var(--vc-border);color:var(--vc-text);">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color:var(--vc-text-secondary);">Memory Limit (MB)</label>
                        <input type="number" name="quotas[{{ $i }}][memory_mb]" value="{{ old('quotas.'.$i.'.memory_mb', $quota->memory_mb) }}" class="w-full text-sm rounded-lg" style="background:var(--vc-bg);border:1px solid var(--vc-border);color:var(--vc-text);" placeholder="e.g. 512">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color:var(--vc-text-secondary);">CPU Shares</label>
                        <input type="number" name="quotas[{{ $i }}][cpu_shares]" value="{{ old('quotas.'.$i.'.cpu_shares', $quota->cpu_shares) }}" class="w-full text-sm rounded-lg" style="background:var(--vc-bg);border:1px solid var(--vc-border);color:var(--vc-text);" placeholder="e.g. 1024">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color:var(--vc-text-secondary);">Storage Limit (MB)</label>
                        <input type="number" name="quotas[{{ $i }}][disk_mb]" value="{{ old('quotas.'.$i.'.disk_mb', $quota->disk_mb) }}" class="w-full text-sm rounded-lg" style="background:var(--vc-bg);border:1px solid var(--vc-border);color:var(--vc-text);" placeholder="e.g. 5120">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color:var(--vc-text-secondary);">Timeout (mins)</label>
                        <input type="number" name="quotas[{{ $i }}][timeout_minutes]" value="{{ old('quotas.'.$i.'.timeout_minutes', $quota->timeout_minutes) }}" class="w-full text-sm rounded-lg" style="background:var(--vc-bg);border:1px solid var(--vc-border);color:var(--vc-text);">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn-primary">Save Quotas</button>
        </div>
    </form>
</div>
@endsection



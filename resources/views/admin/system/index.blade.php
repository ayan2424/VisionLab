@extends('layouts.admin')
@section('title', 'System Configuration')
@section('page-title', 'System')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold" style="color:var(--vc-text);">System Configuration</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Feature Flags --}}
    <div class="vc-card p-6">
        <h2 class="text-lg font-bold mb-4" style="color:var(--vc-text);">Feature Flags</h2>
        <form method="POST" action="{{ route('admin.system.flags.update') }}">
            @csrf
            
            @php
                $aiEnabled = \App\Models\SystemConfig::getVal('flag_ai_enabled', true);
                $videoEnabled = \App\Models\SystemConfig::getVal('flag_video_calls_enabled', true);
                $regOpen = \App\Models\SystemConfig::getVal('flag_registration_open', true);
                $marketplaceOpen = (bool) \App\Models\SystemConfig::getVal('global_allow_marketplace', true);
            @endphp

            <div class="space-y-4 mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="flags[]" value="ai_enabled" {{ $aiEnabled ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-600 bg-black/20 text-[#3b82f6] focus:ring-[#3b82f6]">
                    <div>
                        <div class="text-sm font-semibold" style="color:var(--vc-text);">AI Agent Enabled</div>
                        <div class="text-xs" style="color:var(--vc-muted);">Allow users to interact with Claude AI in IDE workspaces.</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="flags[]" value="video_calls_enabled" {{ $videoEnabled ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-600 bg-black/20 text-[#3b82f6] focus:ring-[#3b82f6]">
                    <div>
                        <div class="text-sm font-semibold" style="color:var(--vc-text);">Video Calls Enabled</div>
                        <div class="text-xs" style="color:var(--vc-muted);">Allow Jitsi video conferencing inside workspaces.</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="flags[]" value="registration_open" {{ $regOpen ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-600 bg-black/20 text-[#3b82f6] focus:ring-[#3b82f6]">
                    <div>
                        <div class="text-sm font-semibold" style="color:var(--vc-text);">Registration Open</div>
                        <div class="text-xs" style="color:var(--vc-muted);">Allow new users to register on the platform.</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="flags[]" value="global_allow_marketplace" {{ $marketplaceOpen ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-600 bg-black/20 text-[#3b82f6] focus:ring-[#3b82f6]">
                    <div>
                        <div class="text-sm font-semibold" style="color:var(--vc-text);">Global Extensions Marketplace</div>
                        <div class="text-xs" style="color:var(--vc-muted);">If disabled, the VS Code Marketplace will be blocked across ALL workspaces.</div>
                    </div>
                </label>
            </div>

            <button type="submit" class="btn-primary py-2 px-4 w-full justify-center">Save Feature Flags</button>
        </form>
    </div>

    {{-- Maintenance Mode --}}
    <div class="vc-card p-6 border {{ \App\Models\SystemConfig::getVal('maintenance_mode', false) ? 'border-red-500/30' : 'border-transparent' }}">
        <h2 class="text-lg font-bold mb-4" style="color:var(--vc-text);">Maintenance Mode</h2>
        <p class="text-sm mb-6" style="color:var(--vc-muted);">
            Enabling maintenance mode will restrict access to all non-admin users. Currently active user sessions will be terminated and they will see a "Maintenance" page.
        </p>

        <form method="POST" action="{{ route('admin.system.maintenance.toggle') }}">
            @csrf
            <label class="flex items-center gap-3 cursor-pointer mb-6">
                <input type="checkbox" name="maintenance_mode" value="1" {{ \App\Models\SystemConfig::getVal('maintenance_mode', false) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-600 bg-black/20 text-red-500 focus:ring-red-500">
                <div class="text-sm font-semibold text-red-400">Enable Maintenance Mode</div>
            </label>

            <button type="submit" class="btn-ghost w-full py-2 px-4 justify-center" style="color:#EF4444;border-color:rgba(239,68,68,0.3);background:rgba(239,68,68,0.05);">Update Maintenance Status</button>
        </form>
    </div>
</div>
@endsection

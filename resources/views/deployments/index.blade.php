@extends('layouts.app')
@section('title', 'Cloud Deployments')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--vc-text);">Cloud Deployments</h1>
            <p class="text-sm mt-1" style="color:var(--vc-muted);">Deploy your workspaces directly to Vercel or Railway.</p>
        </div>
        <button onclick="document.getElementById('deployModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Deployment
        </button>
    </div>

    {{-- Deployment List --}}
    <div class="rounded-2xl border border-white/[0.07] overflow-hidden" style="background:#111111;">
        @if($deployments->isEmpty())
        <div class="text-center py-16">
            <div class="w-16 h-16 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
            </div>
            <h3 class="text-white font-bold mb-1">No Deployments Found</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">You haven't deployed any workspaces yet. Deploy to the cloud and share your work with the world.</p>
        </div>
        @else
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/[0.06] bg-white/[0.02]">
                    <th class="p-4 text-xs font-semibold text-slate-400">Workspace</th>
                    <th class="p-4 text-xs font-semibold text-slate-400">Provider</th>
                    <th class="p-4 text-xs font-semibold text-slate-400">Status</th>
                    <th class="p-4 text-xs font-semibold text-slate-400">URL</th>
                    <th class="p-4 text-right text-xs font-semibold text-slate-400">Deployed At</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.04]">
                @foreach($deployments as $deploy)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="p-4">
                        <div class="text-sm font-medium text-white">{{ $deploy->workspace->name ?? 'Deleted Workspace' }}</div>
                    </td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-white/[0.05] text-slate-300 border border-white/[0.1]">
                            {{ ucfirst($deploy->provider) }}
                        </span>
                    </td>
                    <td class="p-4">
                        @if($deploy->status === 'deployed')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Deployed
                            </span>
                        @elseif($deploy->status === 'failed')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Failed
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> {{ ucfirst($deploy->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($deploy->public_url)
                            <a href="{{ $deploy->public_url }}" target="_blank" class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1">
                                {{ str_replace('https://', '', $deploy->public_url) }}
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @else
                            <span class="text-slate-500 text-sm">—</span>
                        @endif
                    </td>
                    <td class="p-4 text-right text-sm text-slate-500">
                        {{ $deploy->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

{{-- Deploy Modal --}}
<div id="deployModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-[#111111] rounded-2xl border border-white/[0.07] w-full max-w-md shadow-2xl overflow-hidden">
        <div class="p-5 border-b border-white/[0.06] flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">New Deployment</h3>
            <button onclick="document.getElementById('deployModal').classList.add('hidden')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Select Workspace</label>
                <select id="deployWorkspace" class="w-full bg-black/50 border border-white/[0.1] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-blue-500">
                    <option value="">-- Choose a Workspace --</option>
                    @foreach($workspaces as $ws)
                    <option value="{{ $ws->slug }}">{{ $ws->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Cloud Provider</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="provider" value="vercel" class="peer sr-only" checked>
                        <div class="p-3 rounded-xl border border-white/[0.1] peer-checked:border-blue-500 peer-checked:bg-blue-500/10 flex flex-col items-center gap-2 transition-all">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 512 512"><path fill="currentColor" fill-rule="evenodd" d="M256,48,496,464H16Z"/></svg>
                            <span class="text-sm font-medium text-white">Vercel</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="provider" value="railway" class="peer sr-only">
                        <div class="p-3 rounded-xl border border-white/[0.1] peer-checked:border-blue-500 peer-checked:bg-blue-500/10 flex flex-col items-center gap-2 transition-all">
                            <span class="text-2xl">🚂</span>
                            <span class="text-sm font-medium text-white">Railway</span>
                        </div>
                    </label>
                </div>
            </div>
            <button id="deployBtn" onclick="triggerDeploy()" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg shadow-lg shadow-blue-500/20 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Deploy to Cloud
            </button>
            <p id="deployMsg" class="mt-3 text-center text-sm hidden"></p>
        </div>
    </div>
</div>

<script>
async function triggerDeploy() {
    const wsId = document.getElementById('deployWorkspace').value;
    const provider = document.querySelector('input[name="provider"]:checked').value;
    const btn = document.getElementById('deployBtn');
    const msg = document.getElementById('deployMsg');

    if(!wsId) {
        msg.textContent = 'Please select a workspace.';
        msg.className = 'mt-3 text-center text-sm text-red-400';
        msg.classList.remove('hidden');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="animate-pulse">Deploying...</span>';
    msg.classList.add('hidden');

    try {
        const response = await fetch(`/workspace/${wsId}/deploy`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ provider })
        });

        const data = await response.json();
        
        if(response.ok) {
            msg.textContent = 'Deployment Queued! Reloading...';
            msg.className = 'mt-3 text-center text-sm text-emerald-400';
            msg.classList.remove('hidden');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(data.error || 'Failed to deploy');
        }
    } catch(err) {
        msg.textContent = err.message;
        msg.className = 'mt-3 text-center text-sm text-red-400';
        msg.classList.remove('hidden');
        btn.disabled = false;
        btn.textContent = 'Deploy to Cloud';
    }
}
</script>
@endsection

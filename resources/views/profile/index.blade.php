@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-16 h-16 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center text-2xl font-bold border border-blue-500/20">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--vc-text);">{{ $user->name }}</h1>
            <p class="text-sm mt-1" style="color:var(--vc-muted);">{{ ucfirst($user->role) }} &bull; Joined {{ $user->created_at->format('M Y') }}</p>
        </div>
    </div>

    {{-- Heatmap --}}
    <div class="rounded-2xl border border-white/[0.07] p-6 mb-8" style="background:#111111;">
        <h3 class="text-lg font-bold text-white mb-4">Contribution Activity</h3>
        <div id="heatmap-container" class="overflow-x-auto pb-4">
            {{-- Rendered via JS --}}
            <div class="animate-pulse flex space-x-1" id="heatmap-loading">
                @for($i=0; $i<52; $i++)
                <div class="space-y-1">
                    @for($j=0; $j<7; $j++)
                    <div class="w-3 h-3 bg-white/[0.05] rounded-sm"></div>
                    @endfor
                </div>
                @endfor
            </div>
        </div>
        <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
            <div id="streak-info">Loading streaks...</div>
            <div class="flex items-center gap-1">
                Less 
                <div class="w-3 h-3 bg-white/[0.04] rounded-sm"></div>
                <div class="w-3 h-3 bg-emerald-900 rounded-sm"></div>
                <div class="w-3 h-3 bg-emerald-700 rounded-sm"></div>
                <div class="w-3 h-3 bg-emerald-500 rounded-sm"></div>
                <div class="w-3 h-3 bg-emerald-400 rounded-sm"></div>
                More
            </div>
        </div>
    </div>

    {{-- Badges --}}
    <div class="rounded-2xl border border-white/[0.07] p-6 mb-8" style="background:#111111;">
        <h3 class="text-lg font-bold text-white mb-4">Achievements & Badges</h3>
        @if($badges->isEmpty())
        <div class="text-center py-8 text-slate-500 text-sm">No badges earned yet. Keep coding!</div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($badges as $badge)
            <div class="p-4 rounded-xl border border-white/[0.06] flex flex-col items-center text-center hover:bg-white/[0.02] transition-colors">
                <div class="text-4xl mb-3" title="{{ $badge->name }}">{!! $badge->icon !!}</div>
                <div class="font-bold text-sm text-white mb-1">{{ $badge->name }}</div>
                @if($badge->description)
                <div class="text-[10px] text-slate-400 leading-tight">{{ $badge->description }}</div>
                @endif
                <div class="mt-3 text-[10px] text-slate-500 bg-white/[0.05] px-2 py-0.5 rounded-full">
                    {{ $badge->earned_at->format('M d, Y') }}
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('/api/contributions?user_id={{ $user->id }}');
        const data = await response.json();
        
        const container = document.getElementById('heatmap-container');
        const streakInfo = document.getElementById('streak-info');
        
        streakInfo.innerHTML = `
            <span class="font-semibold text-white">${data.total_contributions}</span> contributions in the last year &bull;
            <span class="font-semibold text-emerald-400">${data.current_streak}</span> day streak (Max: ${data.max_streak})
        `;

        // Render heatmap (52 weeks x 7 days)
        const weeks = [];
        let currentWeek = [];
        
        // Pad first week if it doesn't start on Sunday
        const firstDate = new Date(data.heatmap[0].date);
        for(let i=0; i<firstDate.getDay(); i++) {
            currentWeek.push(null);
        }

        data.heatmap.forEach(day => {
            currentWeek.push(day);
            if(currentWeek.length === 7) {
                weeks.push(currentWeek);
                currentWeek = [];
            }
        });
        if(currentWeek.length > 0) weeks.push(currentWeek);

        let html = '<div class="flex gap-1">';
        weeks.forEach(week => {
            html += '<div class="flex flex-col gap-1">';
            for(let i=0; i<7; i++) {
                const day = week[i];
                if(!day) {
                    html += '<div class="w-[14px] h-[14px] rounded-[3px] bg-transparent"></div>';
                    continue;
                }

                let colorClass = 'bg-white/[0.04]'; // level 0
                if(day.level === 1) colorClass = 'bg-emerald-900';
                else if(day.level === 2) colorClass = 'bg-emerald-700';
                else if(day.level === 3) colorClass = 'bg-emerald-500';
                else if(day.level >= 4) colorClass = 'bg-emerald-400';

                html += `<div class="w-[14px] h-[14px] rounded-[3px] ${colorClass}" title="${day.count} contributions on ${day.date}"></div>`;
            }
            html += '</div>';
        });
        html += '</div>';
        
        container.innerHTML = html;
        // Scroll to the end (most recent)
        container.scrollLeft = container.scrollWidth;
        
    } catch (err) {
        console.error('Failed to load heatmap', err);
        document.getElementById('heatmap-loading').innerHTML = '<div class="text-red-400 text-sm">Failed to load heatmap</div>';
    }
});
</script>
@endsection

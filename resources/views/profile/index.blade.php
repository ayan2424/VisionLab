@extends('layouts.dashboard')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    
    <!-- Profile Header & Rank Info -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold border" style="background:var(--vc-accent-subtle); color:var(--vc-accent); border-color:var(--vc-accent);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-3xl font-bold" style="color:var(--vc-text);">{{ $user->name }}</h1>
                <p class="text-sm mt-1" style="color:var(--vc-muted);">{{ ucfirst($user->role) }} &bull; Joined {{ $user->created_at->format('M Y') }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4 px-5 py-3 rounded-xl border shadow-sm" style="background:var(--vc-card); border-color:var(--vc-border);">
            <div class="text-right">
                <div class="text-xs font-mono uppercase tracking-wider" style="color:var(--vc-muted);">Current Rank</div>
                <div class="text-base font-bold" style="color:var(--vc-text);">{{ $user->rank_title }}</div>
            </div>
            <div class="w-px h-10" style="background:var(--vc-border);"></div>
            <div class="text-left">
                <div class="text-xs font-mono uppercase tracking-wider" style="color:var(--vc-muted);">Level {{ $user->level }}</div>
                <div class="text-base font-bold" style="color:var(--vc-success);">{{ number_format($user->xp) }} XP</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Progress Card -->
            <div class="rounded-xl border p-6 shadow-sm overflow-hidden relative" style="background:var(--vc-card); border-color:var(--vc-border);">
                <!-- Decorative glow -->
                <div class="absolute -top-24 -right-24 w-48 h-48 opacity-10 blur-[80px] rounded-full pointer-events-none" style="background:var(--vc-success);"></div>
                
                <h3 class="text-lg font-bold mb-4" style="color:var(--vc-text);">Level Progress</h3>
                
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-medium" style="color:var(--vc-text);">Level {{ $user->level }}</span>
                    <span style="color:var(--vc-muted);">{{ number_format($user->xp) }} / {{ number_format($nextLevelXp) }} XP</span>
                </div>
                
                <div class="w-full rounded-full h-3 mb-2 overflow-hidden border" style="background:var(--vc-elevated); border-color:var(--vc-border);">
                    <div class="h-3 rounded-full relative transition-all duration-1000 ease-out" style="width: {{ $progressPercent }}%; background:var(--vc-success);">
                        <!-- Shine effect -->
                        <div class="absolute top-0 right-0 bottom-0 left-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] animate-[shimmer_2s_infinite]"></div>
                    </div>
                </div>
                
                <p class="text-xs" style="color:var(--vc-muted);">Earn {{ number_format($nextLevelXp - $user->xp) }} more XP to reach Level {{ $user->level + 1 }}. Keep coding!</p>
            </div>

            <!-- Heatmap -->
            <div class="rounded-xl border p-6 shadow-sm" style="background:var(--vc-card); border-color:var(--vc-border);">
                <h3 class="text-lg font-bold mb-4" style="color:var(--vc-text);">Contribution Activity</h3>
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
                <div class="mt-4 flex items-center justify-between text-xs" style="color:var(--vc-muted);">
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

            <!-- Badges Showcase -->
            <div class="rounded-xl border p-6 shadow-sm" style="background:var(--vc-card); border-color:var(--vc-border);">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2" style="color:var(--vc-text);">
                    <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Earned Badges
                </h3>
                
                @if($badges->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($badges as $badge)
                            <div class="flex flex-col items-center p-4 border rounded-lg transition-colors group cursor-default" style="background:var(--vc-elevated); border-color:var(--vc-border);" onmouseover="this.style.borderColor='var(--vc-accent)';" onmouseout="this.style.borderColor='var(--vc-border)';">
                                <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">{!! $badge->icon !!}</div>
                                <div class="text-sm font-semibold text-center" style="color:var(--vc-text);">{{ $badge->name }}</div>
                                <div class="text-xs text-center mt-1 opacity-0 group-hover:opacity-100 transition-opacity absolute border p-2 rounded shadow-lg -translate-y-12 pointer-events-none z-10 w-48" style="background:var(--vc-elevated); border-color:var(--vc-border); color:var(--vc-muted);">{{ $badge->description }}<br><span class="text-[10px] block mt-1" style="color:var(--vc-text-secondary);">Earned: {{ $badge->earned_at->format('M j, Y') }}</span></div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-sm" style="color:var(--vc-muted);">You haven't earned any badges yet. Complete assignments, attend classes, or launch workspaces to start unlocking them!</p>
                    </div>
                @endif
            </div>

        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Leaderboard Card -->
            <div class="rounded-xl border p-6 shadow-sm" style="background:var(--vc-card); border-color:var(--vc-border);">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2" style="color:var(--vc-text);">
                    <svg class="w-5 h-5 text-[#2f81f7]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Top Coders
                </h3>
                
                <div class="space-y-3">
                    @foreach($leaderboard as $index => $leader)
                        <div class="flex items-center justify-between p-2 rounded transition-colors" style="{{ $leader->id === $user->id ? 'background:rgba(31,111,235,0.1); border:1px solid rgba(31,111,235,0.3);' : '' }}" onmouseover="if('{{$leader->id}}'!='{{$user->id}}') this.style.background='var(--vc-elevated)';" onmouseout="if('{{$leader->id}}'!='{{$user->id}}') this.style.background='transparent';">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold {{ $index === 0 ? 'bg-yellow-500/20 text-yellow-500' : ($index === 1 ? 'bg-gray-300/20 text-gray-300' : ($index === 2 ? 'bg-amber-700/20 text-amber-600' : 'text-[#8b949e]')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($leader->avatar_url)
                                        <img src="{{ $leader->avatar_url }}" alt="{{ $leader->name }}" class="w-6 h-6 rounded-full">
                                    @else
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px]" style="background:var(--vc-elevated); color:var(--vc-text);">
                                            {{ $leader->avatar_initials }}
                                        </div>
                                    @endif
                                    <span class="text-sm font-medium" style="color:var(--vc-text);">{{ $leader->name }}</span>
                                </div>
                            </div>
                            <div class="text-xs font-mono" style="color:var(--vc-muted);">
                                {{ number_format($leader->xp) }} XP
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- XP Transactions -->
            <div class="rounded-xl border p-6 shadow-sm" style="background:var(--vc-card); border-color:var(--vc-border);">
                <h3 class="text-lg font-bold mb-4" style="color:var(--vc-text);">Recent Activity</h3>
                
                @if($transactions->count() > 0)
                    <div class="space-y-4">
                        @foreach($transactions as $tx)
                            <div class="flex items-center justify-between p-3 rounded-lg border transition-colors" style="background:var(--vc-elevated); border-color:rgba(255,255,255,0.05);">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:rgba(35,134,54,0.1); color:var(--vc-success);">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium" style="color:var(--vc-text);">{{ $tx->reason }}</p>
                                        <p class="text-xs" style="color:var(--vc-muted);">{{ $tx->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-sm font-bold" style="color:var(--vc-success);">
                                    +{{ $tx->amount }} XP
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-sm" style="color:var(--vc-muted);">
                        No recent activity found.
                    </div>
                @endif
            </div>

            <!-- Portfolio Link -->
            <div class="rounded-xl border p-6 shadow-sm text-center" style="background:var(--vc-card); border-color:var(--vc-border);">
                <h3 class="text-lg font-bold mb-2" style="color:var(--vc-text);">My Portfolio</h3>
                <p class="text-sm mb-4" style="color:var(--vc-muted);">Share your achievements and code with others.</p>
                <a href="{{ route('portfolio.show', $user->student_id) }}" target="_blank" class="inline-flex items-center justify-center gap-2 btn-primary px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm w-full">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Portfolio
                </a>
            </div>
            
        </div>
        
    </div>
</div>

<style>
    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }
</style>

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

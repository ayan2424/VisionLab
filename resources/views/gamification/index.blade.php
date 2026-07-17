@extends('layouts.dashboard')

@section('title', 'My Achievements')
@section('page-title', 'My Achievements')

@section('content')
    <div class="flex items-center justify-between mb-8 px-4 sm:px-6 lg:px-8">
        <div>
            <h2 class="text-2xl font-bold flex items-center gap-2" style="color:var(--vc-text);">
                <svg class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                My Achievements
            </h2>
            <p class="mt-1 text-sm" style="color:var(--vc-muted);">Track your progress, earn XP, and climb the ranks.</p>
        </div>
        
        <div class="flex items-center gap-4 px-4 py-2 rounded-lg border shadow-sm" style="background:var(--vc-card); border-color:var(--vc-border);">
            <div class="text-right">
                <div class="text-xs font-mono uppercase tracking-wider" style="color:var(--vc-muted);">Current Rank</div>
                <div class="text-sm font-bold" style="color:var(--vc-text);">{{ $user->rank_title }}</div>
            </div>
            <div class="w-px h-8" style="background:var(--vc-border);"></div>
            <div class="text-left">
                <div class="text-xs font-mono uppercase tracking-wider" style="color:var(--vc-muted);">Level {{ $user->level }}</div>
                <div class="text-sm font-bold" style="color:var(--vc-success);">{{ number_format($user->xp) }} XP</div>
            </div>
        </div>
        
        <a href="{{ route('portfolio.show', $user->student_id) }}" target="_blank" class="hidden sm:flex items-center gap-2 btn-primary px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm ml-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Share Portfolio
        </a>
    </div>

    <div class="py-8">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
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
                                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">{{ $badge->icon }}</div>
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
                                                <div class="w-6 h-6 rounded-full bg-[#30363d] flex items-center justify-center text-[10px] text-white">
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

                    <!-- Streak Card -->
                    <div class="rounded-xl border p-6 shadow-sm relative overflow-hidden" style="background:var(--vc-card); border-color:var(--vc-border);">
                        <div class="absolute -right-4 -top-4 text-7xl opacity-5">🔥</div>
                        <h3 class="text-lg font-bold text-white mb-2">Login Streak</h3>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-3xl font-bold text-orange-500">{{ $user->current_streak }}</span>
                            <span style="color:var(--vc-muted);">days</span>
                        </div>
                        <p class="text-xs" style="color:var(--vc-muted);">Longest streak: {{ $user->longest_streak }} days. Log in daily to earn bonus XP!</p>
                    </div>
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
@endsection

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    My Achievements
                </h2>
                <p class="mt-1 text-sm text-[#8b949e]">Track your progress, earn XP, and climb the ranks.</p>
            </div>
            
            <div class="flex items-center gap-4 bg-[#161b22] px-4 py-2 rounded-lg border border-[#30363d] shadow-sm">
                <div class="text-right">
                    <div class="text-xs text-[#8b949e] font-mono uppercase tracking-wider">Current Rank</div>
                    <div class="text-sm font-bold text-white">{{ $user->rank_title }}</div>
                </div>
                <div class="w-px h-8 bg-[#30363d]"></div>
                <div class="text-left">
                    <div class="text-xs text-[#8b949e] font-mono uppercase tracking-wider">Level {{ $user->level }}</div>
                    <div class="text-sm font-bold text-[#10b981]">{{ number_format($user->xp) }} XP</div>
                </div>
            </div>
            
            <a href="{{ route('portfolio.show', $user->student_id) }}" target="_blank" class="hidden sm:flex items-center gap-2 bg-[#1f6feb] hover:bg-[#388bfd] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm ml-4 border border-transparent">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Share Portfolio
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Progress Card -->
                    <div class="bg-[#0d1117] rounded-xl border border-[#30363d] p-6 shadow-sm overflow-hidden relative">
                        <!-- Decorative glow -->
                        <div class="absolute -top-24 -right-24 w-48 h-48 bg-[#10b981] opacity-10 blur-[80px] rounded-full pointer-events-none"></div>
                        
                        <h3 class="text-lg font-bold text-white mb-4">Level Progress</h3>
                        
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium text-white">Level {{ $user->level }}</span>
                            <span class="text-[#8b949e]">{{ number_format($user->xp) }} / {{ number_format($nextLevelXp) }} XP</span>
                        </div>
                        
                        <div class="w-full bg-[#161b22] rounded-full h-3 mb-2 overflow-hidden border border-[#30363d]">
                            <div class="bg-gradient-to-r from-[#238636] to-[#2ea043] h-3 rounded-full relative transition-all duration-1000 ease-out" style="width: {{ $progressPercent }}%">
                                <!-- Shine effect -->
                                <div class="absolute top-0 right-0 bottom-0 left-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] animate-[shimmer_2s_infinite]"></div>
                            </div>
                        </div>
                        
                        <p class="text-xs text-[#8b949e]">Earn {{ number_format($nextLevelXp - $user->xp) }} more XP to reach Level {{ $user->level + 1 }}. Keep coding!</p>
                    </div>

                    <!-- Badges Showcase -->
                    <div class="bg-[#0d1117] rounded-xl border border-[#30363d] p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Earned Badges
                        </h3>
                        
                        @if($badges->count() > 0)
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach($badges as $badge)
                                    <div class="flex flex-col items-center p-4 bg-[#161b22] border border-[#30363d] rounded-lg hover:border-[#8b949e] transition-colors group cursor-default">
                                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">{{ $badge->icon }}</div>
                                        <div class="text-sm font-semibold text-white text-center">{{ $badge->name }}</div>
                                        <div class="text-xs text-[#8b949e] text-center mt-1 opacity-0 group-hover:opacity-100 transition-opacity absolute bg-[#161b22] border border-[#30363d] p-2 rounded shadow-lg -translate-y-12 pointer-events-none z-10 w-48">{{ $badge->description }}<br><span class="text-[10px] text-gray-500 block mt-1">Earned: {{ $badge->earned_at->format('M j, Y') }}</span></div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-[#8b949e] text-sm">You haven't earned any badges yet. Complete assignments, attend classes, or launch workspaces to start unlocking them!</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- XP Transactions -->
                    <div class="bg-[#0d1117] rounded-xl border border-[#30363d] p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-white mb-4">Recent Activity</h3>
                        
                        @if($transactions->count() > 0)
                            <div class="space-y-4">
                                @foreach($transactions as $tx)
                                    <div class="flex items-center justify-between p-3 rounded-lg bg-[#161b22] border border-[#30363d]/50 hover:bg-[#1f242b] transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-[#238636]/10 flex items-center justify-center text-[#238636]">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-white">{{ $tx->reason }}</p>
                                                <p class="text-xs text-[#8b949e]">{{ $tx->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="text-sm font-bold text-[#10b981]">
                                            +{{ $tx->amount }} XP
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-sm text-[#8b949e]">
                                No recent activity found.
                            </div>
                        @endif
                    </div>

                </div>
                
                <!-- Sidebar -->
                <div class="space-y-6">
                    
                    <!-- Leaderboard Card -->
                    <div class="bg-[#0d1117] rounded-xl border border-[#30363d] p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#2f81f7]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            Top Coders
                        </h3>
                        
                        <div class="space-y-3">
                            @foreach($leaderboard as $index => $leader)
                                <div class="flex items-center justify-between p-2 rounded {{ $leader->id === $user->id ? 'bg-[#1f6feb]/10 border border-[#1f6feb]/30' : 'hover:bg-[#161b22]' }} transition-colors">
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
                                            <span class="text-sm font-medium text-white">{{ $leader->name }}</span>
                                        </div>
                                    </div>
                                    <div class="text-xs text-[#8b949e] font-mono">
                                        {{ number_format($leader->xp) }} XP
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Streak Card -->
                    <div class="bg-gradient-to-br from-[#161b22] to-[#0d1117] rounded-xl border border-[#30363d] p-6 shadow-sm relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 text-7xl opacity-5">🔥</div>
                        <h3 class="text-lg font-bold text-white mb-2">Login Streak</h3>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-3xl font-bold text-orange-500">{{ $user->current_streak }}</span>
                            <span class="text-[#8b949e]">days</span>
                        </div>
                        <p class="text-xs text-[#8b949e]">Longest streak: {{ $user->longest_streak }} days. Log in daily to earn bonus XP!</p>
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
</x-app-layout>

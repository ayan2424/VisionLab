<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight flex items-center space-x-3">
            <span class="p-2 bg-yellow-500/20 text-yellow-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </span>
            <span>{{ __('Gamification Dashboard') }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Welcome & Progress Banner -->
            <div class="bg-gray-800/50 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-2xl border border-gray-700/50 relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20 opacity-50"></div>
                <div class="p-8 relative z-10 flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center space-x-6 mb-6 md:mb-0">
                        <div class="relative">
                            <div class="w-24 h-24 rounded-full bg-gray-700 border-4 border-gray-600 flex items-center justify-center text-3xl overflow-hidden">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <span class="text-gray-300 font-bold">{{ $user->avatar_initials }}</span>
                                @endif
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-yellow-500 text-gray-900 text-xs font-bold px-2 py-1 rounded-full border-2 border-gray-800">
                                Lvl {{ $currentLevel }}
                            </div>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold text-white">{{ $user->name }}</h3>
                            <p class="text-gray-400 text-lg flex items-center mt-1">
                                <span class="mr-2">🏆</span> {{ $user->rank_title ?? 'Novice' }}
                            </p>
                        </div>
                    </div>

                    <div class="w-full md:w-1/2 bg-gray-900/50 p-6 rounded-xl border border-gray-700">
                        <div class="flex justify-between text-sm font-medium mb-2">
                            <span class="text-gray-300">Level {{ $currentLevel }}</span>
                            <span class="text-gray-400">{{ number_format($user->xp) }} / {{ number_format($xpForNextLevel) }} XP</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3 mb-2 overflow-hidden relative">
                            <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full transition-all duration-1000 ease-out" 
                                 style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 text-right">{{ $progressPercentage }}% to Level {{ $nextLevel }}</p>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Current Streak -->
                <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-2xl border border-gray-700/50 flex items-center space-x-4 transition hover:bg-gray-800">
                    <div class="p-4 bg-orange-500/20 text-orange-400 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">Current Streak</p>
                        <p class="text-2xl font-bold text-white">{{ $user->current_streak }} Days</p>
                    </div>
                </div>
                
                <!-- Longest Streak -->
                <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-2xl border border-gray-700/50 flex items-center space-x-4 transition hover:bg-gray-800">
                    <div class="p-4 bg-blue-500/20 text-blue-400 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">Longest Streak</p>
                        <p class="text-2xl font-bold text-white">{{ $user->longest_streak }} Days</p>
                    </div>
                </div>

                <!-- Total Workspaces -->
                <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-2xl border border-gray-700/50 flex items-center space-x-4 transition hover:bg-gray-800">
                    <div class="p-4 bg-purple-500/20 text-purple-400 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">Workspaces Launched</p>
                        <p class="text-2xl font-bold text-white">{{ $totalWorkspaces }}</p>
                    </div>
                </div>
            </div>

            <!-- Heatmap Section -->
            <div class="bg-gray-800/50 backdrop-blur-md p-8 rounded-2xl border border-gray-700/50" x-data="heatmapData()">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        365-Day Activity Heatmap
                    </h3>
                    <div class="text-sm text-gray-400">
                        <span x-text="totalContributions" class="font-bold text-white"></span> total contributions
                    </div>
                </div>

                <div x-show="loading" class="animate-pulse flex space-x-2 w-full h-32 bg-gray-700/30 rounded-lg"></div>
                
                <div x-show="!loading" class="overflow-x-auto pb-4 custom-scrollbar">
                    <div class="inline-flex flex-col space-y-1">
                        <!-- We map 52 columns x 7 days. Alpine dynamically renders the grid -->
                        <div class="grid grid-flow-col gap-1" style="grid-template-rows: repeat(7, minmax(0, 1fr));">
                            <template x-for="day in heatmap" :key="day.date">
                                <div 
                                    class="w-3 h-3 rounded-sm transition-colors cursor-pointer"
                                    :class="{
                                        'bg-gray-700': day.level === 0,
                                        'bg-green-900/60': day.level === 1,
                                        'bg-green-700': day.level === 2,
                                        'bg-green-500': day.level === 3,
                                        'bg-green-400': day.level === 4
                                    }"
                                    :title="`${day.count} contributions on ${day.date}`"
                                ></div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Earned Badges -->
                <div class="bg-gray-800/50 backdrop-blur-md p-8 rounded-2xl border border-gray-700/50">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        Unlocked Badges ({{ $user->badges->count() }})
                    </h3>
                    
                    @if($user->badges->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($user->badges as $badge)
                                <div class="bg-gray-900/50 border border-gray-700 rounded-xl p-4 flex flex-col items-center text-center transition hover:border-yellow-500/50 group">
                                    <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">
                                        {{ $badge->icon_emoji ?? '🏅' }}
                                    </div>
                                    <h4 class="text-white font-semibold text-sm">{{ $badge->name }}</h4>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $badge->description }}</p>
                                    <span class="text-[10px] text-gray-600 mt-2">{{ $badge->earned_at->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 bg-gray-900/30 rounded-xl border border-gray-800 border-dashed">
                            <div class="text-5xl mb-3 opacity-50">🔒</div>
                            <p>No badges unlocked yet.</p>
                            <p class="text-sm mt-1">Start coding and completing assignments to earn your first badge!</p>
                        </div>
                    @endif
                </div>

                <!-- Recent Activity Timeline -->
                <div class="bg-gray-800/50 backdrop-blur-md p-8 rounded-2xl border border-gray-700/50">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Recent XP Activity
                    </h3>

                    @if($user->xpTransactions->count() > 0)
                        <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-700 before:to-transparent">
                            @foreach($user->xpTransactions as $tx)
                                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-gray-800 bg-gray-900 text-blue-400 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </div>
                                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-xl border border-gray-700 bg-gray-900/50 shadow-sm">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="font-bold text-white">+{{ $tx->amount }} XP</div>
                                            <time class="text-xs text-gray-500 font-mono">{{ $tx->created_at->format('M d, g:i A') }}</time>
                                        </div>
                                        <div class="text-sm text-gray-400">{{ $tx->reason }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 bg-gray-900/30 rounded-xl border border-gray-800 border-dashed">
                            <p>No recent activity.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Heatmap Alpine Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('heatmapData', () => ({
                loading: true,
                heatmap: [],
                totalContributions: 0,
                
                async init() {
                    try {
                        const response = await fetch('/api/contributions');
                        const data = await response.json();
                        this.heatmap = data.heatmap;
                        this.totalContributions = data.total_contributions;
                    } catch (error) {
                        console.error("Error fetching heatmap:", error);
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(31, 41, 55, 0.5);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(75, 85, 99, 0.8);
            border-radius: 4px;
        }
    </style>
</x-app-layout>

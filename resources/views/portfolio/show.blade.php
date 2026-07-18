<x-guest-layout>
    <!-- Add SEO Meta Tags -->
    <x-slot name="title">{{ $user->name }} - Student Portfolio | VisionLab</x-slot>
    <x-slot name="meta_description">View the coding portfolio, stats, and achievements of {{ $user->name }} on VisionLab.</x-slot>

    <!-- Public Navigation (Simple) -->
    <nav class="sticky top-0 z-50 border-b transition-colors duration-300" style="background:var(--vc-nav);backdrop-filter:blur(16px);border-color:var(--vc-border);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14 items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <x-logo size="h-8 w-8" textSize="text-lg" />
                </a>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#8b949e] hover:text-white transition-colors">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-[#8b949e] hover:text-white transition-colors">Sign In</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Portfolio Content -->
    <div class="min-h-screen bg-[#010409] py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8 bg-[#0d1117] rounded-2xl border border-[#30363d] p-8 shadow-2xl relative overflow-hidden mb-8">
                <!-- Decorative Glow -->
                <div class="absolute -top-32 -left-32 w-64 h-64 bg-emerald-500 opacity-10 blur-[100px] rounded-full pointer-events-none"></div>

                <div class="relative w-32 h-32 flex-shrink-0">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full rounded-full border-4 border-[#30363d] object-cover">
                    @else
                        <div class="w-full h-full rounded-full border-4 border-[#30363d] bg-emerald-600 flex items-center justify-center text-4xl font-bold text-white shadow-inner">
                            {{ $user->avatar_initials }}
                        </div>
                    @endif
                    <!-- Level Badge -->
                    <div class="absolute -bottom-2 -right-2 bg-[#238636] text-white text-xs font-bold px-3 py-1 rounded-full border-2 border-[#0d1117] shadow-lg">
                        Lvl {{ $user->level }}
                    </div>
                </div>

                <div class="flex-1 text-center md:text-left space-y-4 z-10">
                    <div>
                        <h1 class="text-3xl font-extrabold text-white">{{ $user->name }}</h1>
                        <p class="text-[#8b949e] font-mono mt-1 flex items-center justify-center md:justify-start gap-2">
                            <span>ID: {{ $user->student_id }}</span>
                            <span>•</span>
                            <span class="text-emerald-400">{{ $user->rank_title }}</span>
                        </p>
                    </div>

                    <div class="flex flex-wrap justify-center md:justify-start gap-4">
                        <div class="bg-[#161b22] px-4 py-2 rounded-lg border border-[#30363d]">
                            <div class="text-xs text-[#8b949e] uppercase font-bold tracking-wider mb-1">Total XP</div>
                            <div class="text-xl font-bold text-white">{{ number_format($user->xp) }}</div>
                        </div>
                        <div class="bg-[#161b22] px-4 py-2 rounded-lg border border-[#30363d]">
                            <div class="text-xs text-[#8b949e] uppercase font-bold tracking-wider mb-1">Workspaces</div>
                            <div class="text-xl font-bold text-white">{{ number_format($totalWorkspaces) }}</div>
                        </div>
                        <div class="bg-[#161b22] px-4 py-2 rounded-lg border border-[#30363d]">
                            <div class="text-xs text-[#8b949e] uppercase font-bold tracking-wider mb-1">Longest Streak</div>
                            <div class="text-xl font-bold text-orange-400">{{ $user->longest_streak }} Days 🔥</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                
                <!-- Tech Stack Breakdown -->
                <div class="bg-[#0d1117] rounded-xl border border-[#30363d] p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        Tech Stack Mastery
                    </h3>
                    
                    @if($techStack->count() > 0)
                        <div class="space-y-4">
                            @foreach($techStack as $tech)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-semibold text-white capitalize">{{ $tech->language }}</span>
                                        <span class="text-[#8b949e]">{{ $tech->count }} projects</span>
                                    </div>
                                    <div class="w-full bg-[#161b22] rounded-full h-2">
                                        <!-- Calculate percentage based on total workspaces -->
                                        @php $pct = min(100, round(($tech->count / max(1, $totalWorkspaces)) * 100)); @endphp
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-[#8b949e] text-sm">No tech stack data available yet.</div>
                    @endif
                </div>

                <!-- Coding Habits -->
                <div class="bg-[#0d1117] rounded-xl border border-[#30363d] p-6 shadow-sm relative overflow-hidden">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Coding Habits
                    </h3>
                    
                    <div class="mb-6">
                        <div class="text-sm text-[#8b949e] mb-1">Dominant Persona</div>
                        <div class="text-2xl font-bold text-white">{{ $dominantTime }}</div>
                    </div>

                    <div class="space-y-3">
                        @foreach($schedulePercentages as $period => $pct)
                            <div class="flex items-center gap-3">
                                <div class="w-32 text-xs text-[#8b949e] font-medium truncate">{{ $period }}</div>
                                <div class="flex-1 bg-[#161b22] rounded-full h-1.5">
                                    <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="w-8 text-right text-xs font-bold text-white">{{ $pct }}%</div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Activity Heatmap -->
            <div class="bg-[#0d1117] rounded-xl border border-[#30363d] p-6 shadow-sm mb-8">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Activity Log (Last 60 Days)
                </h3>
                
                <div class="flex flex-wrap gap-1">
                    @for($i = 60; $i >= 0; $i--)
                        @php
                            $dateStr = now()->subDays($i)->format('Y-m-d');
                            $count = $heatmapData[$dateStr] ?? 0;
                            $colorClass = 'bg-[#161b22] border-[#30363d]'; // Empty
                            
                            if ($count > 0) {
                                if ($count < 3) $colorClass = 'bg-[#0e4429] border-[#0e4429]';
                                elseif ($count < 6) $colorClass = 'bg-[#006d32] border-[#006d32]';
                                elseif ($count < 10) $colorClass = 'bg-[#26a641] border-[#26a641]';
                                else $colorClass = 'bg-[#39d353] border-[#39d353]';
                            }
                        @endphp
                        <div class="w-3.5 h-3.5 rounded-sm border {{ $colorClass }}" title="{{ $dateStr }}: {{ $count }} activities"></div>
                    @endfor
                </div>
            </div>

            <!-- Earned Badges Showcase -->
            <div class="bg-[#0d1117] rounded-xl border border-[#30363d] p-6 shadow-sm">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Earned Badges ({{ $badges->count() }})
                </h3>
                
                @if($badges->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                        @foreach($badges as $badge)
                            <div class="flex flex-col items-center p-4 bg-[#161b22] border border-[#30363d] rounded-lg hover:border-[#8b949e] transition-colors group cursor-default">
                                <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">{{ $badge->icon }}</div>
                                <div class="text-sm font-semibold text-white text-center">{{ $badge->name }}</div>
                                <div class="text-xs text-[#8b949e] text-center mt-1">{{ $badge->description }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-[#8b949e] text-sm">
                        This student hasn't earned any badges yet.
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-guest-layout>

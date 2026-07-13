<aside id="dash-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 md:relative md:w-[280px] transition-all duration-400 transform -translate-x-full md:translate-x-0 flex flex-col flex-shrink-0 rounded-2xl md:rounded-2xl shadow-2xl overflow-hidden"
       style="background:var(--vc-surface); border:1px solid var(--vc-border);">

    <!-- ── Logo ── -->
    <div class="h-16 flex items-center px-6 border-b" style="border-color:rgba(255,255,255,0.05);">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <x-logo size="h-9 w-9" :showText="false" />
            <div>
                <span class="text-lg font-bold tracking-tight" style="color:var(--vc-text);">Vision<span style="color:var(--vc-accent);">Lab</span></span>
            </div>
        </a>
    </div>

    <!-- ── Navigation ── -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5">
        @php
            $navLinks = [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'match' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route' => 'courses.index', 'label' => 'Courses', 'match' => 'courses.*', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['route' => 'workspace.index', 'label' => 'Workspace IDE', 'match' => 'workspace.*', 'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
            ];
        @endphp

        <div class="px-3 mb-3 text-[10px] font-bold uppercase tracking-widest" style="color:var(--vc-muted);">Main</div>

        @foreach($navLinks as $link)
            @php $active = request()->routeIs($link['match']); @endphp
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $active
                    ? 'color:var(--vc-accent);background:var(--vc-accent-subtle);font-weight:600;'
                    : 'color:var(--vc-text-secondary);' }}">
                @if($active)
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full" style="background:var(--vc-accent);"></span>
                @endif
                <svg class="w-[18px] h-[18px] transition-colors duration-200 flex-shrink-0"
                     style="{{ $active ? 'color:var(--vc-accent);' : 'color:var(--vc-muted);' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                </svg>
                {{ $link['label'] }}
            </a>
        @endforeach

        <!-- ── Student-specific ── -->
        @if(Auth::check() && Auth::user()->isStudent())
        <div class="pt-6 mt-6 border-t" style="border-color:rgba(255,255,255,0.05);">
            <div class="px-3 mb-3 text-[10px] font-bold uppercase tracking-widest" style="color:var(--vc-muted);">Learning</div>
            @php $pActive = request()->routeIs('progress.*'); @endphp
            <a href="{{ route('progress.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $pActive ? 'color:var(--vc-accent);background:var(--vc-accent-subtle);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($pActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full" style="background:var(--vc-accent);"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $pActive ? 'color:var(--vc-accent);' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                My Progress
            </a>
            @php $jActive = request()->routeIs('enrollments.*'); @endphp
            <a href="{{ route('enrollments.join') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $jActive ? 'color:var(--vc-accent);background:var(--vc-accent-subtle);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($jActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full" style="background:var(--vc-accent);"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $jActive ? 'color:var(--vc-accent);' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Join Course
            </a>
        </div>
        @endif

        <!-- ── Instructor/Admin-specific ── -->
        @if(Auth::check() && (Auth::user()->isInstructor() || Auth::user()->isAdmin()))
        <div class="pt-6 mt-6 border-t" style="border-color:rgba(255,255,255,0.05);">
            <div class="px-3 mb-3 text-[10px] font-bold uppercase tracking-widest" style="color:var(--vc-muted);">Teaching</div>
            @php $gActive = request()->routeIs('submissions.*'); @endphp
            <a href="{{ route('submissions.queue') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $gActive ? 'color:var(--vc-warning);background:rgba(217,119,6,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($gActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full" style="background:var(--vc-warning);"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $gActive ? 'color:var(--vc-warning);' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Grading Queue
            </a>
        </div>
        @endif

        <!-- ── Admin Panel ── -->
        @if(Auth::check() && Auth::user()->isAdmin())
        <div class="pt-6 mt-6 border-t" style="border-color:rgba(255,255,255,0.05);">
            <div class="px-3 mb-3 text-[10px] font-bold uppercase tracking-widest text-red-400">Admin</div>
            @php $aActive = request()->routeIs('admin.*'); @endphp
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $aActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($aActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $aActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Admin Panel
            </a>
            @php $extActive = request()->routeIs('admin.extensions.*'); @endphp
            <a href="{{ route('admin.extensions.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $extActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($extActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $extActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                </svg>
                Extensions
            </a>
            @php $usActive = request()->routeIs('admin.users.*'); @endphp
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $usActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($usActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $usActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Users
            </a>
            @php $wsActive = request()->routeIs('admin.workspaces.*'); @endphp
            <a href="{{ route('admin.workspaces.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $wsActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($wsActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $wsActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Workspaces
            </a>

            <!-- LMS & ERP Extension -->
            <div class="px-3 pt-4 pb-2 text-[10px] font-bold tracking-wider uppercase" style="color:var(--vc-muted);">LMS & ERP Modules</div>
            
            @php $campActive = request()->routeIs('admin.campuses.*'); @endphp
            <a href="{{ route('admin.campuses.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $campActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($campActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $campActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Campuses
            </a>

            @php $deptActive = request()->routeIs('admin.departments.*'); @endphp
            <a href="{{ route('admin.departments.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $deptActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($deptActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $deptActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Departments
            </a>

            @php $batchActive = request()->routeIs('admin.batches.*'); @endphp
            <a href="{{ route('admin.batches.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $batchActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($batchActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $batchActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Batches
            </a>

            @php $challanActive = request()->routeIs('admin.challans.*'); @endphp
            <a href="{{ route('admin.challans.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $challanActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($challanActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $challanActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Fee Challans
            </a>
            @php $empActive = request()->routeIs('admin.employees.*'); @endphp
            <a href="{{ route('admin.employees.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $empActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($empActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $empActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                HR (Employees)
            </a>
            @php $libActive = request()->routeIs('admin.library.*'); @endphp
            <a href="{{ route('admin.library.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $libActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($libActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $libActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Library
            </a>

            @php $tmplActive = request()->routeIs('admin.templates.*'); @endphp
            <a href="{{ route('admin.templates.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $tmplActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($tmplActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $tmplActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
                Templates
            </a>
            @php $qActive = request()->routeIs('admin.quotas.*'); @endphp
            <a href="{{ route('admin.quotas.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $qActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($qActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $qActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                </svg>
                Quotas
            </a>
            @php $audActive = request()->routeIs('admin.audits.*'); @endphp
            <a href="{{ route('admin.audits.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $audActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($audActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $audActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Audit Logs
            </a>
            @php $anActive = request()->routeIs('admin.analytics'); @endphp
            <a href="{{ route('admin.analytics') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $anActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($anActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $anActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Analytics
            </a>
            @php $sysActive = request()->routeIs('admin.system.*'); @endphp
            <a href="{{ route('admin.system.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $sysActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($sysActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $sysActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                System Config
            </a>
            @php $whActive = request()->routeIs('admin.webhooks.*'); @endphp
            <a href="{{ route('admin.webhooks.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $whActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($whActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $whActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                Webhooks
            </a>
            @php $stActive = request()->routeIs('admin.status'); @endphp
            <a href="{{ route('admin.status') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $stActive ? 'color:#EF4444;background:rgba(239,68,68,0.08);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($stActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-red-500"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $stActive ? 'color:#EF4444;' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                System Status
            </a>
        </div>
        @endif

        <!-- ── Demo Link ── -->
        <div class="pt-6 mt-6 border-t" style="border-color:rgba(255,255,255,0.05);">
            @php $dActive = request()->routeIs('demo'); @endphp
            <a href="{{ route('demo') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 group relative overflow-hidden"
               style="{{ $dActive ? 'color:var(--vc-accent);background:var(--vc-accent-subtle);font-weight:600;' : 'color:var(--vc-text-secondary);' }}">
                @if($dActive)<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full" style="background:var(--vc-accent);"></span>@endif
                <svg class="w-[18px] h-[18px]" style="{{ $dActive ? 'color:var(--vc-accent);' : 'color:var(--vc-muted);' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Demo
            </a>
        </div>
    </nav>

    <!-- ── User Profile Footer ── -->
    <div class="p-5 border-t flex items-center gap-4" style="border-color:rgba(255,255,255,0.05);">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 flex-1 overflow-hidden group">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                 style="background:{{ Auth::user()->isAdmin() ? '#EF4444' : (Auth::user()->isInstructor() ? '#7c3aed' : '#16A34A') }};">
                {{ Auth::user()->avatar_initials }}
            </div>
            <div class="min-w-0">
                <p class="text-[13px] font-semibold truncate transition-colors" style="color:var(--vc-text);">{{ Auth::user()->name }}</p>
                <p class="text-[11px] truncate" style="color:var(--vc-muted);">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="p-1.5 rounded-lg transition-colors hover:bg-red-500/10 text-red-400" title="Sign Out">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </button>
        </form>
    </div>
</aside>



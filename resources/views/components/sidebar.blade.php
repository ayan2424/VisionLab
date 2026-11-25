@php
    $user = Auth::user();
    $isAdmin = $user && $user->isAdmin();
    $isInstructor = $user && $user->isInstructor();
    $isStudent = $user && $user->isStudent();

    $mainLinks = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'match' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route' => 'courses.index', 'label' => 'Courses', 'match' => 'courses.*', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['route' => 'workspace.index', 'label' => 'Workspaces', 'match' => 'workspace.*', 'icon' => 'M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z'],
        ['route' => 'announcements.index', 'label' => 'Announcements', 'match' => 'announcements.*', 'icon' => 'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46'],
    ];

    $studentLinks = [
        ['route' => 'progress.index', 'label' => 'My Progress', 'match' => 'progress.*', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ['route' => 'enrollments.join', 'label' => 'Join Course', 'match' => 'enrollments.*', 'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
        ['route' => 'gamification.index', 'label' => 'Badges & XP', 'match' => 'gamification.*', 'icon' => 'M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172'],
        ['route' => 'profile.index', 'label' => 'Student Profile', 'match' => 'profile.index', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
    ];

    $adminOpsLinks = [
        ['route' => 'admin.dashboard', 'label' => 'Admin Overview', 'match' => 'admin.dashboard', 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'],
        ['route' => 'admin.users.index', 'label' => 'Users', 'match' => 'admin.users.*', 'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
        ['route' => 'admin.workspaces.index', 'label' => 'Workspaces', 'match' => 'admin.workspaces.*', 'icon' => 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9'],
        ['route' => 'admin.extensions.index', 'label' => 'Extensions', 'match' => 'admin.extensions.*', 'icon' => 'M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z'],
        ['route' => 'admin.templates.index', 'label' => 'Templates', 'match' => 'admin.templates.*', 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h12A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h5.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25z'],
        ['route' => 'admin.quotas.index', 'label' => 'Quotas', 'match' => 'admin.quotas.*', 'icon' => 'M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z'],
    ];

    $adminErpLinks = [
        ['route' => 'admin.campuses.index', 'label' => 'Campuses', 'match' => 'admin.campuses.*', 'icon' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'],
        ['route' => 'admin.departments.index', 'label' => 'Departments', 'match' => 'admin.departments.*', 'icon' => 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z'],
        ['route' => 'admin.batches.index', 'label' => 'Course Batches', 'match' => 'admin.batches.*', 'icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197'],
        ['route' => 'admin.challans.index', 'label' => 'Fee Challans', 'match' => 'admin.challans.*', 'icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75'],
        ['route' => 'admin.employees.index', 'label' => 'HR & Staff', 'match' => 'admin.employees.*', 'icon' => 'M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z'],
        ['route' => 'admin.library.index', 'label' => 'Library', 'match' => 'admin.library.*', 'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
    ];

    $adminSystemLinks = [
        ['route' => 'admin.audits.index', 'label' => 'Audit Logs', 'match' => 'admin.audits.*', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['route' => 'admin.analytics', 'label' => 'Analytics', 'match' => 'admin.analytics', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
        ['route' => 'admin.system.index', 'label' => 'System Config', 'match' => 'admin.system.*', 'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z'],
        ['route' => 'admin.webhooks.index', 'label' => 'Webhooks', 'match' => 'admin.webhooks.*', 'icon' => 'M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244'],
        ['route' => 'admin.status', 'label' => 'System Status', 'match' => 'admin.status', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
@endphp

<aside
    id="dash-sidebar"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    class="fixed inset-y-0 left-0 z-50 w-[240px] md:relative flex flex-col shrink-0 vl-rail-surface border-r border-vl-border/10 transform transition-transform duration-200 ease-vl-drawer"
    aria-label="Workspace navigation"
>
    {{-- Brand Header (52px aligned with topbar) --}}
    <div class="h-[52px] shrink-0 flex items-center justify-between px-4 border-b border-vl-border/10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-lg py-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vl-accent">
            <x-logo size="h-6 w-6" textSize="text-[15px]" />
        </a>
        <button
            type="button"
            @click="sidebarOpen = false"
            class="md:hidden p-1.5 rounded-lg text-vl-ink-muted hover:text-vl-ink"
            aria-label="Close navigation menu"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Scrollable Navigation Groups --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5">
        {{-- Workspace Core --}}
        <div class="space-y-0.5">
            <p class="px-2.5 pb-1.5 text-[11px] font-semibold text-vl-ink-muted">Workspace</p>
            @foreach($mainLinks as $link)
                @php $active = request()->routeIs($link['match']); @endphp
                <a
                    href="{{ route($link['route']) }}"
                    @click="sidebarOpen = false"
                    class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-[13px] transition-colors duration-150 {{ $active ? 'bg-vl-accent/12 text-vl-accent font-semibold' : 'text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface/60 font-medium' }}"
                >
                    <svg class="h-[17px] w-[17px] shrink-0 {{ $active ? 'text-vl-accent' : 'text-vl-ink-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                    </svg>
                    <span class="truncate">{{ $link['label'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Student Learning --}}
        @if($isStudent)
            <div class="pt-4 border-t border-vl-border/10 space-y-0.5">
                <p class="px-2.5 pb-1.5 text-[11px] font-semibold text-vl-ink-muted">Learning</p>
                @foreach($studentLinks as $link)
                    @php $active = request()->routeIs($link['match']); @endphp
                    <a
                        href="{{ route($link['route']) }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-[13px] transition-colors duration-150 {{ $active ? 'bg-vl-accent/12 text-vl-accent font-semibold' : 'text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface/60 font-medium' }}"
                    >
                        <svg class="h-[17px] w-[17px] shrink-0 {{ $active ? 'text-vl-accent' : 'text-vl-ink-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                        </svg>
                        <span class="truncate">{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Teaching --}}
        @if($isInstructor || $isAdmin)
            <div class="pt-4 border-t border-vl-border/10 space-y-0.5">
                <p class="px-2.5 pb-1.5 text-[11px] font-semibold text-vl-ink-muted">Teaching</p>
                @php $gActive = request()->routeIs('submissions.*', 'grading.*'); @endphp
                <a
                    href="{{ route('submissions.queue') }}"
                    @click="sidebarOpen = false"
                    class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-[13px] transition-colors duration-150 {{ $gActive ? 'bg-vl-accent/12 text-vl-accent font-semibold' : 'text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface/60 font-medium' }}"
                >
                    <svg class="h-[17px] w-[17px] shrink-0 {{ $gActive ? 'text-vl-accent' : 'text-vl-ink-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                    </svg>
                    <span class="truncate">Grading Queue</span>
                </a>
            </div>
        @endif

        {{-- Admin Operations, ERP & System --}}
        @if($isAdmin)
            <div class="pt-4 border-t border-vl-border/10 space-y-0.5">
                <p class="px-2.5 pb-1.5 text-[11px] font-semibold text-vl-ink-muted">Operations</p>
                @foreach($adminOpsLinks as $link)
                    @php $active = request()->routeIs($link['match']); @endphp
                    <a
                        href="{{ route($link['route']) }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-[13px] transition-colors duration-150 {{ $active ? 'bg-vl-accent/12 text-vl-accent font-semibold' : 'text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface/60 font-medium' }}"
                    >
                        <svg class="h-[17px] w-[17px] shrink-0 {{ $active ? 'text-vl-accent' : 'text-vl-ink-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                        </svg>
                        <span class="truncate">{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="pt-4 border-t border-vl-border/10 space-y-0.5">
                <p class="px-2.5 pb-1.5 text-[11px] font-semibold text-vl-ink-muted">Institute ERP</p>
                @foreach($adminErpLinks as $link)
                    @php $active = request()->routeIs($link['match']); @endphp
                    <a
                        href="{{ route($link['route']) }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-[13px] transition-colors duration-150 {{ $active ? 'bg-vl-accent/12 text-vl-accent font-semibold' : 'text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface/60 font-medium' }}"
                    >
                        <svg class="h-[17px] w-[17px] shrink-0 {{ $active ? 'text-vl-accent' : 'text-vl-ink-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                        </svg>
                        <span class="truncate">{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="pt-4 border-t border-vl-border/10 space-y-0.5">
                <p class="px-2.5 pb-1.5 text-[11px] font-semibold text-vl-ink-muted">Governance &amp; System</p>
                @foreach($adminSystemLinks as $link)
                    @php $active = request()->routeIs($link['match']); @endphp
                    <a
                        href="{{ route($link['route']) }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-[13px] transition-colors duration-150 {{ $active ? 'bg-vl-accent/12 text-vl-accent font-semibold' : 'text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface/60 font-medium' }}"
                    >
                        <svg class="h-[17px] w-[17px] shrink-0 {{ $active ? 'text-vl-accent' : 'text-vl-ink-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                        </svg>
                        <span class="truncate">{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </nav>

    {{-- Compact User Footer --}}
    @if($user)
        <div class="shrink-0 p-3 border-t border-vl-border/10 flex items-center gap-2.5">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 flex-1 min-w-0 rounded-xl p-1.5 hover:bg-vl-surface/60 transition-colors duration-150">
                <div class="h-8 w-8 rounded-xl shrink-0 flex items-center justify-center text-xs font-bold text-white {{ $isAdmin ? 'bg-vl-danger' : ($isInstructor ? 'bg-vl-accent-cta' : 'bg-vl-teal') }}">
                    {{ $user->avatar_initials }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-vl-ink truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-vl-ink-muted truncate">{{ ucfirst($user->role) }}</p>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    title="Sign Out"
                    aria-label="Sign Out"
                    class="p-2 rounded-xl text-vl-ink-muted hover:text-vl-danger hover:bg-vl-danger/10 transition-colors duration-150 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    @endif
</aside>

@php
    $user = Auth::user();
    $unreadCount = (int) ($unreadGlobalAnnouncementsCount ?? 0);
@endphp

<header class="h-[52px] shrink-0 px-4 sm:px-6 bg-vl-surface/85 backdrop-blur-md border-b border-vl-border/10 flex items-center justify-between gap-4 z-30">
    {{-- Left: Mobile Drawer Trigger + Page Title --}}
    <div class="flex items-center gap-3 min-w-0">
        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            aria-label="Open navigation menu"
            class="md:hidden inline-flex h-8 w-8 items-center justify-center rounded-xl text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface-raised transition-colors duration-150"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <h1 class="text-sm font-semibold text-vl-ink truncate">
            @yield('page-title', 'Dashboard')
        </h1>
    </div>

    {{-- Right: Instant Cmd+K Trigger, Real Global Announcements Bell, Theme Toggle, Role Pill --}}
    <div class="flex items-center gap-2">
        {{-- Working 0ms Command Palette Trigger --}}
        <button
            type="button"
            @click="$dispatch('vl:open-cmdk')"
            class="hidden sm:inline-flex items-center justify-between gap-4 h-8 w-52 lg:w-60 px-3 rounded-xl bg-vl-surface-raised/85 hover:bg-vl-surface-raised text-xs text-vl-ink-muted hover:text-vl-ink transition-colors duration-150 cursor-pointer"
            aria-label="Open command palette"
        >
            <span class="inline-flex items-center gap-2 truncate">
                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <span>Jump to route...</span>
            </span>
            <kbd class="font-mono text-[10px] font-medium px-1.5 py-0.5 rounded bg-vl-surface text-vl-ink-secondary">⌘K</kbd>
        </button>

        {{-- Real Global Announcements Bell (All Authenticated Roles) --}}
        <a
            href="{{ route('announcements.index') }}"
            class="relative inline-flex h-8 w-8 items-center justify-center rounded-xl text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface-raised transition-colors duration-150"
            title="{{ $unreadCount > 0 ? $unreadCount . ' unread campus announcements' : 'Campus Announcements' }}"
            aria-label="{{ $unreadCount > 0 ? $unreadCount . ' unread announcements' : 'Announcements' }}"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            @if($unreadCount > 0)
                <span
                    data-unread-announcements="{{ $unreadCount }}"
                    class="absolute -top-0.5 -right-0.5 inline-flex min-w-[16px] h-4 px-1 items-center justify-center rounded-full bg-vl-accent-cta text-white font-mono text-[10px] font-bold leading-none tabular-nums"
                >
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </a>

        <x-theme-toggle />

        @if($user)
            <span class="hidden sm:inline-flex items-center rounded-lg px-2 py-1 font-mono text-[10px] font-semibold tracking-wide uppercase {{ $user->isAdmin() ? 'bg-vl-danger/12 text-vl-danger' : ($user->isInstructor() ? 'bg-vl-accent/12 text-vl-accent' : 'bg-vl-teal/12 text-vl-teal') }}">
                {{ $user->role }}
            </span>
        @endif
    </div>
</header>

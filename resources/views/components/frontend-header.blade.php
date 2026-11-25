<header
    x-data="{ mobileOpen: false, scrolled: false }"
    x-init="scrolled = window.scrollY > 12; window.addEventListener('scroll', () => { scrolled = window.scrollY > 12 }, { passive: true })"
    :class="scrolled ? 'bg-vl-bg/88 backdrop-blur-xl shadow-[0_8px_32px_-16px_rgba(9,13,20,0.28)]' : 'bg-vl-bg/65 backdrop-blur-md'"
    class="fixed inset-x-0 top-0 z-50 h-[60px] transition-[background-color,box-shadow] duration-200 ease-vl-out"
>
    {{-- Subtle specular hairline separator --}}
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-vl-border/10 to-transparent"></div>

    <div class="mx-auto flex h-full max-w-7xl items-center justify-between px-5 sm:px-8">
        {{-- Brand mark --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vl-accent rounded-lg py-1">
            <x-logo size="h-7 w-7" textSize="text-[17px]" />
        </a>

        {{-- Primary navigation --}}
        <nav aria-label="Primary" class="hidden md:flex items-center gap-1">
            @php
                $navItems = [
                    ['label' => 'Product', 'route' => 'about'],
                    ['label' => 'Features', 'route' => 'features'],
                    ['label' => 'Documentation', 'route' => 'docs'],
                    ['label' => 'Contact', 'route' => 'contact'],
                ];
            @endphp
            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors duration-150 ease-vl-out {{ $active ? 'text-vl-ink bg-vl-surface-raised/80' : 'text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface-raised/50' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Right controls --}}
        <div class="flex items-center gap-2.5">
            <x-theme-toggle />

            @auth
                <a href="{{ route('dashboard') }}" class="vl-btn-primary !py-2 !px-4 !text-xs sm:!text-sm">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-medium text-vl-ink-secondary hover:text-vl-ink transition-colors duration-150 ease-vl-out">
                    Sign in
                </a>
                <a href="{{ route('contact') }}" class="vl-btn-primary !py-2 !px-4 !text-xs sm:!text-sm">
                    Request Pilot
                </a>
            @endauth

            {{-- Mobile menu toggle --}}
            <button
                type="button"
                @click="mobileOpen = !mobileOpen"
                :aria-expanded="mobileOpen.toString()"
                aria-label="Toggle navigation menu"
                class="md:hidden inline-flex items-center justify-center h-9 w-9 rounded-xl text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface-raised/70 transition-colors duration-150"
            >
                <svg x-show="!mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
                <svg x-show="mobileOpen" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile drawer --}}
    <div
        x-show="mobileOpen"
        x-cloak
        x-transition:enter="transition ease-vl-drawer duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-vl-out duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        @click.outside="mobileOpen = false"
        class="md:hidden bg-vl-surface/95 backdrop-blur-2xl shadow-vl-overlay px-5 pt-3 pb-5"
    >
        <nav aria-label="Mobile Navigation" class="flex flex-col gap-1">
            <a href="{{ route('about') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('about') ? 'text-vl-ink bg-vl-surface-raised' : 'text-vl-ink-secondary hover:text-vl-ink' }}">Product</a>
            <a href="{{ route('features') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('features') ? 'text-vl-ink bg-vl-surface-raised' : 'text-vl-ink-secondary hover:text-vl-ink' }}">Features</a>
            <a href="{{ route('docs') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('docs') ? 'text-vl-ink bg-vl-surface-raised' : 'text-vl-ink-secondary hover:text-vl-ink' }}">Documentation</a>
            <a href="{{ route('contact') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('contact') ? 'text-vl-ink bg-vl-surface-raised' : 'text-vl-ink-secondary hover:text-vl-ink' }}">Contact</a>
        </nav>

        <div class="mt-4 pt-4 border-t border-vl-border/10 flex flex-col gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="vl-btn-primary w-full">Open Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="vl-btn-secondary w-full">Sign in to Portal</a>
                <a href="{{ route('contact') }}" class="vl-btn-primary w-full">Request Campus Pilot</a>
            @endauth
        </div>
    </div>
</header>

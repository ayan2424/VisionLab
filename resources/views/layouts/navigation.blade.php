{{--
  Navigation bar — theme-aware with dark/light toggle.
  Includes: theme switch, role badge, notification bell, user dropdown.
--}}
<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b transition-colors duration-300"
     style="background:var(--vc-nav);backdrop-filter:blur(16px);border-color:var(--vc-border);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">

            {{-- Logo + Nav links --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-shadow duration-200"
                         style="background:linear-gradient(135deg,#F05000,#FF8147);box-shadow:0 2px 8px rgba(240,80,0,0.3);">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold" style="color:var(--vc-text);">Vision<span style="color:var(--vc-accent);">Code</span> AI</span>
                </a>

                <div class="hidden sm:flex items-center gap-1">
                    @php
                        $navLinks = [
                            ['route' => 'dashboard', 'label' => 'Dashboard', 'match' => 'dashboard'],
                            ['route' => 'workspace.index', 'label' => 'Workspace', 'match' => 'workspace.*'],
                            ['route' => 'courses.index', 'label' => 'Courses', 'match' => 'courses.*'],
                        ];
                    @endphp

                    @foreach($navLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200"
                       style="{{ request()->routeIs($link['match'])
                            ? 'color:var(--vc-accent);background:rgba(240,80,0,0.1);'
                            : 'color:var(--vc-text-secondary);' }}">
                        {{ $link['label'] }}
                    </a>
                    @endforeach

                    @if(Auth::check() && Auth::user()->isStudent())
                    <a href="{{ route('progress.index') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200"
                       style="{{ request()->routeIs('progress.*')
                            ? 'color:#059669;background:rgba(5,150,105,0.1);'
                            : 'color:var(--vc-text-secondary);' }}">
                        Progress
                    </a>
                    @endif

                    @if(Auth::check() && (Auth::user()->isInstructor() || Auth::user()->isAdmin()))
                    <a href="{{ route('submissions.queue') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200"
                       style="{{ request()->routeIs('submissions.*')
                            ? 'color:#D97706;background:rgba(217,119,6,0.1);'
                            : 'color:var(--vc-text-secondary);' }}">
                        Grading
                    </a>
                    @endif

                    @if(Auth::check() && Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200"
                       style="{{ request()->routeIs('admin.*')
                            ? 'color:#DC2626;background:rgba(220,38,38,0.1);'
                            : 'color:var(--vc-text-secondary);' }}">
                        Admin
                    </a>
                    @endif

                    <a href="{{ route('demo') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200"
                       style="{{ request()->routeIs('demo')
                            ? 'color:var(--vc-accent);background:rgba(240,80,0,0.1);'
                            : 'color:var(--vc-text-secondary);' }}">
                        Demo
                    </a>

                    <button id="pwa-install-btn" style="display: none; background:var(--vc-accent); color:#fff;" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 shadow-md hover:scale-105">
                        Install App
                    </button>
                </div>
            </div>

            {{-- Right: theme toggle + bell + role badge + user dropdown --}}
            <div class="hidden sm:flex items-center gap-2">
                @auth

                {{-- ── Theme Toggle ─────────────────────────────────── --}}
                <button onclick="window.themeManager.toggle()"
                        class="theme-toggle" title="Toggle theme"
                        aria-label="Toggle dark/light mode">
                    {{-- Sun icon (shown in dark mode) --}}
                    <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{-- Moon icon (shown in light mode) --}}
                    <svg class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                {{-- ── Notification Bell ──────────────────────────────── --}}
                @if(Auth::user()->isStudent())
                <div x-data="notifBell()" class="relative">
                    <button @click="toggle()" title="Notifications"
                            class="theme-toggle">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notif-badge"
                              class="absolute -top-0.5 -right-0.5 hidden items-center justify-center w-4 h-4 rounded-full text-white text-[9px] font-black"
                              style="background:#F05000;">
                            <span id="notif-count">0</span>
                        </span>
                    </button>

                    <div x-show="open" @click.away="close()" x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute right-0 mt-2 w-80 rounded-2xl z-50 overflow-hidden"
                         style="background:var(--vc-card);border:1px solid var(--vc-border);box-shadow:var(--vc-shadow-lg);">

                        <div class="flex items-center justify-between px-4 py-3" style="border-bottom:1px solid var(--vc-border);">
                            <span class="text-sm font-bold" style="color:var(--vc-text);">Notifications</span>
                            <button @click="markAllRead()" class="text-xs font-semibold hover:underline" style="color:var(--vc-accent);" x-show="unread > 0">Mark all read</button>
                        </div>

                        <div class="max-h-80 overflow-y-auto" id="notif-list">
                            <template x-if="notifications.length === 0">
                                <div class="text-center py-10 px-4">
                                    <svg class="w-8 h-8 mx-auto mb-2" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <p class="text-xs" style="color:var(--vc-text-secondary);">No notifications yet</p>
                                    <p class="text-xs mt-1" style="color:var(--vc-muted);">You'll be notified when work is graded</p>
                                </div>
                            </template>

                            <template x-for="n in notifications" :key="n.id">
                                <div class="flex items-start gap-3 px-4 py-3 last:border-0 transition-colors"
                                     :style="'border-bottom:1px solid var(--vc-border);' + (!n.read ? 'background:rgba(240,80,0,0.05);' : '')">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 text-xs font-black"
                                         :style="n.pct >= 80 ? 'background:rgba(5,150,105,.12);color:var(--vc-success);' : n.pct >= 60 ? 'background:rgba(217,119,6,.12);color:var(--vc-warning);' : 'background:rgba(220,38,38,.12);color:var(--vc-danger);'">
                                        <span x-text="n.pct + '%'"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-semibold line-clamp-1" style="color:var(--vc-text);" x-text="'Graded: ' + n.title"></div>
                                        <div class="text-xs mt-0.5" style="color:var(--vc-text-secondary);">
                                            <span x-text="n.grade + '/' + n.max + ' pts'"></span>
                                            <span style="color:var(--vc-muted);"> · </span>
                                            <span style="color:var(--vc-text-secondary);" x-text="'by ' + n.from"></span>
                                        </div>
                                        <div class="text-[10px] mt-0.5" style="color:var(--vc-muted);" x-text="timeAgo(n.at)"></div>
                                    </div>
                                    <div x-show="!n.read" class="w-2 h-2 rounded-full flex-shrink-0 mt-1.5" style="background:var(--vc-accent);"></div>
                                </div>
                            </template>
                        </div>

                        <div class="px-4 py-2.5" style="border-top:1px solid var(--vc-border);">
                            <button @click="clearAll()" class="text-xs transition-colors" style="color:var(--vc-muted);">Clear all</button>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Role badge --}}
                <span class="px-2 py-0.5 rounded-md text-xs font-bold border
                    @if(Auth::user()->isAdmin()) text-red-500 bg-red-500/10 border-red-500/20
                    @elseif(Auth::user()->isInstructor()) text-orange-500 bg-orange-600/10 border-orange-500/20
                    @else text-emerald-500 bg-emerald-500/10 border-emerald-500/20 @endif">
                    {{ strtoupper(Auth::user()->role) }}
                </span>

                {{-- User dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl transition-all duration-200 cursor-pointer"
                            style="border:1px solid transparent;">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white
                            @if(Auth::user()->isAdmin()) bg-red-500
                            @elseif(Auth::user()->isInstructor()) bg-orange-600
                            @else bg-emerald-500 @endif">
                            {{ Auth::user()->avatar_initials }}
                        </div>
                        <span class="text-sm" style="color:var(--vc-text-secondary);">{{ Str::before(Auth::user()->name, ' ') }}</span>
                        <svg class="w-3.5 h-3.5 transition-transform" style="color:var(--vc-muted);" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 mt-2 w-56 rounded-xl py-1 z-50"
                         style="background:var(--vc-card);border:1px solid var(--vc-border);box-shadow:var(--vc-shadow-lg);">
                        <div class="px-3 py-2 mb-1" style="border-bottom:1px solid var(--vc-border);">
                            <div class="text-xs font-semibold" style="color:var(--vc-text);">{{ Auth::user()->name }}</div>
                            <div class="text-xs truncate" style="color:var(--vc-muted);">{{ Auth::user()->email }}</div>
                        </div>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs transition-all rounded-lg mx-1" style="color:var(--vc-text-secondary);">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('courses.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs transition-all rounded-lg mx-1" style="color:var(--vc-text-secondary);">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            My Courses
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 text-xs transition-all rounded-lg mx-1" style="color:var(--vc-text-secondary);">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profile Settings
                        </a>
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs transition-all rounded-lg mx-1 text-red-500">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            Admin Panel
                        </a>
                        @endif
                        <div class="mt-1 pt-1 mx-1" style="border-top:1px solid var(--vc-border);">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-500 hover:bg-red-500/10 transition-all rounded-lg cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <div class="-me-2 flex items-center sm:hidden gap-2">
                <button onclick="window.themeManager.toggle()" class="theme-toggle" aria-label="Toggle theme">
                    <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
                <button @click="open = !open" class="p-2 rounded-lg transition-all" style="color:var(--vc-muted);">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden" style="border-top:1px solid var(--vc-border);">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm transition-all" style="color:var(--vc-text-secondary);">Dashboard</a>
            <a href="{{ route('workspace.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-all" style="color:var(--vc-text-secondary);">Workspace</a>
            <a href="{{ route('courses.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-all" style="color:var(--vc-text-secondary);">Courses</a>
            @if(Auth::check() && Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-red-500 transition-all">Admin Panel</a>
            @endif
            <a href="{{ route('demo') }}" class="block px-3 py-2 rounded-lg text-sm transition-all" style="color:var(--vc-text-secondary);">Demo</a>
        </div>
        @auth
        <div class="px-4 py-3" style="border-top:1px solid var(--vc-border);">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-xs font-bold text-white">
                    {{ Auth::user()->avatar_initials }}
                </div>
                <div>
                    <div class="text-sm font-semibold" style="color:var(--vc-text);">{{ Auth::user()->name }}</div>
                    <div class="text-xs" style="color:var(--vc-muted);">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-sm transition-all" style="color:var(--vc-text-secondary);">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-red-500 transition-all cursor-pointer">Sign Out</button>
            </form>
        </div>
        @endauth
    </div>
</nav>

{{-- ── Notification Bell Alpine Component ───────────────────────────────── --}}
<script>
function notifBell() {
    return {
        open: false,
        notifications: [],
        get unread() { return this.notifications.filter(n => !n.read).length; },

        init() {
            this.load();
            window.addEventListener('storage', (e) => {
                if (e.key === 'vc_notifs') { this.load(); }
            });
            window.addEventListener('vc-notif-update', () => this.load());
        },

        load() {
            this.notifications = JSON.parse(localStorage.getItem('vc_notifs') || '[]');
        },

        toggle() {
            this.open = !this.open;
            if (this.open) this.load();
        },

        close() { this.open = false; },

        markAllRead() {
            this.notifications = this.notifications.map(n => ({ ...n, read: true }));
            localStorage.setItem('vc_notifs', JSON.stringify(this.notifications));
            if (typeof updateBellBadge === 'function') updateBellBadge();
        },

        clearAll() {
            this.notifications = [];
            localStorage.setItem('vc_notifs', '[]');
            this.open = false;
            if (typeof updateBellBadge === 'function') updateBellBadge();
        },

        timeAgo(ts) {
            const diff = (Date.now() - ts) / 1000;
            if (diff < 60)    return 'just now';
            if (diff < 3600)  return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            return Math.floor(diff / 86400) + 'd ago';
        },
    };
}
</script>

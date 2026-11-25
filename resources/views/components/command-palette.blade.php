@php
    $user = Auth::user();
    $commands = [
        ['label' => 'Dashboard', 'group' => 'Core', 'url' => route('dashboard'), 'keywords' => 'home overview stats'],
        ['label' => 'Courses', 'group' => 'Core', 'url' => route('courses.index'), 'keywords' => 'classes syllabus'],
        ['label' => 'Workspaces IDE', 'group' => 'Core', 'url' => route('workspace.index'), 'keywords' => 'vscode container ide sandbox'],
        ['label' => 'New Workspace', 'group' => 'Core', 'url' => route('workspace.create'), 'keywords' => 'create launch container'],
        ['label' => 'Campus Announcements', 'group' => 'Core', 'url' => route('announcements.index'), 'keywords' => 'notifications alerts broadcasts'],
        ['label' => 'Account Settings', 'group' => 'Account', 'url' => route('profile.edit'), 'keywords' => 'password email preferences'],
    ];

    if ($user && $user->isStudent()) {
        $commands = array_merge($commands, [
            ['label' => 'My Progress', 'group' => 'Learning', 'url' => route('progress.index'), 'keywords' => 'grades completion analytics'],
            ['label' => 'Join Course by Code', 'group' => 'Learning', 'url' => route('enrollments.join'), 'keywords' => 'enroll invite code'],
            ['label' => 'Badges & XP', 'group' => 'Learning', 'url' => route('gamification.index'), 'keywords' => 'achievements leaderboard streak'],
            ['label' => 'Student Profile & Portfolio', 'group' => 'Learning', 'url' => route('profile.index'), 'keywords' => 'heatmap portfolio rank'],
        ]);
    }

    if ($user && ($user->isInstructor() || $user->isAdmin())) {
        $commands = array_merge($commands, [
            ['label' => 'Grading Queue', 'group' => 'Teaching', 'url' => route('submissions.queue'), 'keywords' => 'submissions review grade'],
            ['label' => 'Create Course', 'group' => 'Teaching', 'url' => route('courses.create'), 'keywords' => 'new course syllabus'],
            ['label' => 'Publish Announcement', 'group' => 'Teaching', 'url' => route('announcements.create'), 'keywords' => 'broadcast notice'],
        ]);
    }

    if ($user && $user->isAdmin()) {
        $commands = array_merge($commands, [
            ['label' => 'Admin Dashboard', 'group' => 'Admin', 'url' => route('admin.dashboard'), 'keywords' => 'telemetry health overview'],
            ['label' => 'Manage Users', 'group' => 'Admin', 'url' => route('admin.users.index'), 'keywords' => 'accounts roles suspend impersonate'],
            ['label' => 'Manage Workspaces', 'group' => 'Admin', 'url' => route('admin.workspaces.index'), 'keywords' => 'containers stop archive'],
            ['label' => 'SHA256 Extensions', 'group' => 'Admin', 'url' => route('admin.extensions.index'), 'keywords' => 'vsix plugins registry'],
            ['label' => 'Workspace Templates', 'group' => 'Admin', 'url' => route('admin.templates.index'), 'keywords' => 'docker nix runtime'],
            ['label' => 'Resource Quotas', 'group' => 'Admin', 'url' => route('admin.quotas.index'), 'keywords' => 'ram cpu limits'],
            ['label' => 'Audit Logs', 'group' => 'Admin', 'url' => route('admin.audits.index'), 'keywords' => 'forensics security events'],
            ['label' => 'System Status', 'group' => 'Admin', 'url' => route('admin.status'), 'keywords' => 'healthz docker redis db'],
            ['label' => 'System Config', 'group' => 'Admin', 'url' => route('admin.system.index'), 'keywords' => 'flags maintenance'],
            ['label' => 'Webhooks', 'group' => 'Admin', 'url' => route('admin.webhooks.index'), 'keywords' => 'integrations events'],
            ['label' => 'ERP · Campuses', 'group' => 'Institute ERP', 'url' => route('admin.campuses.index'), 'keywords' => 'locations buildings'],
            ['label' => 'ERP · Departments', 'group' => 'Institute ERP', 'url' => route('admin.departments.index'), 'keywords' => 'faculty cs engineering'],
            ['label' => 'ERP · Course Batches', 'group' => 'Institute ERP', 'url' => route('admin.batches.index'), 'keywords' => 'cohorts terms'],
            ['label' => 'ERP · Fee Challans', 'group' => 'Institute ERP', 'url' => route('admin.challans.index'), 'keywords' => 'tuition billing finance'],
            ['label' => 'ERP · HR & Employees', 'group' => 'Institute ERP', 'url' => route('admin.employees.index'), 'keywords' => 'staff payroll directory'],
            ['label' => 'ERP · Library', 'group' => 'Institute ERP', 'url' => route('admin.library.index'), 'keywords' => 'books catalog'],
        ]);
    }
@endphp

<div
    x-data="{
        open: false,
        query: '',
        selectedIndex: 0,
        items: @js($commands),
        get filtered() {
            const q = this.query.trim().toLowerCase();
            if (!q) return this.items;
            return this.items.filter((item) =>
                item.label.toLowerCase().includes(q) ||
                item.group.toLowerCase().includes(q) ||
                item.keywords.toLowerCase().includes(q)
            );
        },
        openPalette() {
            this.open = true;
            this.query = '';
            this.selectedIndex = 0;
            this.$nextTick(() => this.$refs.searchInput?.focus());
        },
        closePalette() {
            this.open = false;
        },
        move(delta) {
            const list = this.filtered;
            if (!list.length) return;
            this.selectedIndex = (this.selectedIndex + delta + list.length) % list.length;
        },
        goSelected() {
            const target = this.filtered[this.selectedIndex];
            if (target && target.url) {
                window.location.href = target.url;
            }
        }
    }"
    @keydown.window.meta.k.prevent="open ? closePalette() : openPalette()"
    @keydown.window.ctrl.k.prevent="open ? closePalette() : openPalette()"
    @vl:open-cmdk.window="openPalette()"
    data-command-palette="visionlab-cmdk"
>
    <template x-teleport="body">
        {{-- Strictly 0ms instant keyboard-initiated command palette --}}
        <div
            x-show="open"
            x-cloak
            @keydown.escape.window="if (open) closePalette()"
            class="fixed inset-0 z-[9990] flex items-start justify-center pt-[14vh] px-4"
            role="dialog"
            aria-modal="true"
            aria-label="VisionLab Command Palette"
        >
            <div @click="closePalette()" class="fixed inset-0 bg-black/55 backdrop-blur-[2px]"></div>

            <div class="relative z-10 w-full max-w-xl overflow-hidden rounded-2xl bg-vl-surface shadow-vl-overlay">
                {{-- Search Input Bar --}}
                <div class="flex items-center gap-3 px-4 py-3.5 border-b border-vl-border/10">
                    <svg class="h-4 w-4 shrink-0 text-vl-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input
                        x-ref="searchInput"
                        x-model="query"
                        @input="selectedIndex = 0"
                        @keydown.arrow-down.prevent="move(1)"
                        @keydown.arrow-up.prevent="move(-1)"
                        @keydown.enter.prevent="goSelected()"
                        type="text"
                        placeholder="Type a destination, course, workspace, or ERP module..."
                        class="w-full bg-transparent border-0 p-0 text-sm text-vl-ink placeholder:text-vl-ink-muted focus:ring-0 focus:outline-none"
                    >
                    <kbd class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-vl-surface-raised text-vl-ink-muted">ESC</kbd>
                </div>

                {{-- Filtered Route List --}}
                <div class="max-h-[340px] overflow-y-auto p-2">
                    <template x-if="filtered.length === 0">
                        <div class="px-4 py-8 text-center text-sm text-vl-ink-muted">
                            No matching VisionLab routes found.
                        </div>
                    </template>

                    <template x-for="(item, idx) in filtered" :key="item.url + item.label">
                        <a
                            :href="item.url"
                            @mouseenter="selectedIndex = idx"
                            :class="selectedIndex === idx ? 'bg-vl-accent/12 text-vl-accent' : 'text-vl-ink-secondary hover:bg-vl-surface-raised/60'"
                            class="flex items-center justify-between gap-3 rounded-xl px-3.5 py-2.5 text-sm cursor-pointer"
                        >
                            <span class="font-medium truncate" :class="selectedIndex === idx ? 'text-vl-accent font-semibold' : 'text-vl-ink'" x-text="item.label"></span>
                            <span class="font-mono text-[11px] text-vl-ink-muted shrink-0" x-text="item.group"></span>
                        </a>
                    </template>
                </div>

                {{-- Footer Hints + Theme Action --}}
                <div class="flex items-center justify-between gap-4 px-4 py-2.5 border-t border-vl-border/10 bg-vl-surface-raised/50 text-[11px] text-vl-ink-muted">
                    <div class="flex items-center gap-3">
                        <span><kbd class="font-mono">↑↓</kbd> Navigate</span>
                        <span><kbd class="font-mono">↵</kbd> Open</span>
                    </div>
                    <button
                        type="button"
                        @click="$store.theme.toggle()"
                        class="font-medium text-vl-ink-secondary hover:text-vl-ink cursor-pointer"
                    >
                        Toggle Light / Dark Theme
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

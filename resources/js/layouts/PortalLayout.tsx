import React, { useEffect, useMemo, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { motion } from 'motion/react';
import {
    Activity,
    ArrowUpRight,
    Award,
    Bell,
    BookOpen,
    Building2,
    CheckSquare,
    Code2,
    Cpu,
    FileCode2,
    LayoutDashboard,
    LogOut,
    Menu,
    Plus,
    Search,
    Settings,
    ShieldCheck,
    Sliders,
    Terminal,
    TrendingUp,
    User,
    Users,
    Webhook,
    X,
} from 'lucide-react';
import { cn } from '@/lib/cn';
import { BrandLogo, FlashToast, ThemeToggle } from '@/components/ui/Primitives';
import { CommandPalette } from '@/components/CommandPalette';
import type { SharedProps } from '@/types';

interface NavEntry {
    label: string;
    href: string;
    icon: React.ComponentType<{ className?: string }>;
    matchPrefix?: string;
    hardNavigate?: boolean;
    badge?: number;
}

export function PortalLayout({
    title,
    pageTitle,
    adminMode = false,
    children,
}: {
    title?: string;
    pageTitle?: string;
    adminMode?: boolean;
    children: React.ReactNode;
}) {
    const { url, props } = usePage<SharedProps>();
    const user = props.auth.user!;
    const [paletteOpen, setPaletteOpen] = useState(false);
    const [mobileDrawer, setMobileDrawer] = useState(false);

    useEffect(() => {
        const onKey = (e: KeyboardEvent) => {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                setPaletteOpen((v) => !v);
            }
        };
        window.addEventListener('keydown', onKey);
        return () => window.removeEventListener('keydown', onKey);
    }, []);

    useEffect(() => {
        setMobileDrawer(false);
    }, [url]);

    // Top Floating Segmented Pill Tabs (Inspired by Reference V21!)
    const topTabs = useMemo<Array<{ label: string; href: string; hardNavigate?: boolean }>>(() => {
        if (adminMode) {
            return [
                { label: 'Command', href: '/admin' },
                { label: 'Telemetry', href: '/admin/status' },
                { label: 'Users', href: '/admin/users' },
                { label: 'Workspaces', href: '/admin/workspaces' },
                { label: 'ERP', href: '/admin/campuses', hardNavigate: true },
            ];
        }
        const tabs: Array<{ label: string; href: string; hardNavigate?: boolean }> = [
            { label: 'Overview', href: '/dashboard' },
            { label: 'Workspaces', href: '/workspace' },
            { label: 'Courses', href: '/courses', hardNavigate: true },
            { label: 'Progress', href: '/progress', hardNavigate: true },
        ];
        if (user.role === 'instructor' || user.role === 'admin') {
            tabs.splice(2, 0, { label: 'Grading', href: '/submissions', hardNavigate: true });
        }
        return tabs;
    }, [adminMode, user.role]);

    const sections = useMemo(() => {
        if (adminMode) {
            return [
                {
                    heading: 'Operations',
                    items: [
                        { label: 'Command Center', href: '/admin', icon: ShieldCheck },
                        { label: 'Cluster Telemetry', href: '/admin/status', icon: Activity },
                        { label: 'Platform Analytics', href: '/admin/analytics', icon: TrendingUp, hardNavigate: true },
                    ] as NavEntry[],
                },
                {
                    heading: 'Governance',
                    items: [
                        { label: 'Users & Roles', href: '/admin/users', icon: Users },
                        { label: 'Workspaces', href: '/admin/workspaces', icon: Code2 },
                        { label: 'VSIX Extensions', href: '/admin/extensions', icon: Cpu, hardNavigate: true },
                        { label: 'Resource Quotas', href: '/admin/quotas', icon: Sliders, hardNavigate: true },
                        { label: 'Audit Trail', href: '/admin/audits', icon: FileCode2, hardNavigate: true },
                        { label: 'Webhooks', href: '/admin/webhooks', icon: Webhook, hardNavigate: true },
                    ] as NavEntry[],
                },
                {
                    heading: 'Institute ERP',
                    items: [
                        { label: 'Campuses', href: '/admin/campuses', icon: Building2, hardNavigate: true },
                        { label: 'Departments', href: '/admin/departments', icon: BookOpen, hardNavigate: true },
                        { label: 'Course Batches', href: '/admin/batches', icon: Terminal, hardNavigate: true },
                    ] as NavEntry[],
                },
            ];
        }

        const primary: NavEntry[] = [
            { label: 'Overview', href: '/dashboard', icon: LayoutDashboard },
            { label: 'Workspaces', href: '/workspace', icon: Terminal, matchPrefix: '/workspace' },
            { label: 'Courses', href: '/courses', icon: BookOpen, matchPrefix: '/courses', hardNavigate: true },
        ];

        if (user.role === 'instructor' || user.role === 'admin') {
            primary.push({
                label: 'Grading Queue',
                href: '/submissions',
                icon: CheckSquare,
                matchPrefix: '/submissions',
                hardNavigate: true,
            });
        }

        const growth: NavEntry[] = [
            {
                label: 'Announcements',
                href: '/announcements',
                icon: Bell,
                badge: props.unreadAnnouncementsCount > 0 ? props.unreadAnnouncementsCount : undefined,
                hardNavigate: true,
            },
            { label: 'Progress', href: '/progress', icon: TrendingUp, hardNavigate: true },
            { label: 'Achievements', href: '/gamification', icon: Award, hardNavigate: true },
            { label: 'Profile', href: '/profile', icon: User, hardNavigate: true },
        ];

        const groups = [
            { heading: 'Workspace', items: primary },
            { heading: 'Campus', items: growth },
        ];

        if (user.role === 'admin') {
            groups.push({
                heading: 'Governance',
                items: [{ label: 'Admin Console', href: '/admin', icon: ShieldCheck }],
            });
        }

        return groups;
    }, [adminMode, user.role, props.unreadAnnouncementsCount]);

    const isActive = (href: string, matchPrefix?: string) => {
        const cleanUrl = url.split('?')[0];
        if (href === '/admin' || href === '/dashboard') {
            return cleanUrl === href;
        }
        return cleanUrl === href || cleanUrl.startsWith((matchPrefix || href) + '/');
    };

    const handleLogout = () => {
        router.post('/logout');
    };

    const renderSidebarContent = () => (
        <div className="flex h-full flex-col">
            <div className="flex h-[68px] items-center justify-between px-5">
                <Link href={adminMode ? '/admin' : '/dashboard'} prefetch="hover">
                    <BrandLogo />
                </Link>
                {adminMode && (
                    <span className="rounded-full bg-amber-500/15 px-2.5 py-0.5 font-mono text-[10px] font-semibold text-amber-400">
                        ADMIN
                    </span>
                )}
            </div>

            <div className="flex-1 overflow-y-auto px-3 py-2 space-y-6">
                {sections.map((section) => (
                    <div key={section.heading} className="space-y-1">
                        <p className="px-3.5 pb-1 text-[11px] font-medium text-vl-ink-muted">
                            {section.heading}
                        </p>
                        {section.items.map((item) => {
                            const Icon = item.icon;
                            const active = isActive(item.href, item.matchPrefix);
                            const linkClass = cn(
                                'relative flex items-center justify-between rounded-full px-3.5 py-2.5 text-[13px] font-medium transition-colors duration-150',
                                active
                                    ? 'text-white'
                                    : 'text-vl-ink-secondary hover:bg-vl-surface-raised/70 hover:text-vl-ink'
                            );

                            const inner = (
                                <>
                                    {active && (
                                        <motion.span
                                            layoutId="portal-sidebar-active"
                                            transition={{ type: 'spring', stiffness: 360, damping: 30 }}
                                            className="absolute inset-0 rounded-full bg-gradient-to-r from-sky-500 to-blue-600 shadow-[0_8px_20px_-6px_rgba(14,165,233,0.6)]"
                                        />
                                    )}
                                    <span className="relative z-10 flex items-center gap-2.5">
                                        <Icon className={cn('h-4 w-4', active ? 'text-white' : 'text-vl-ink-muted')} />
                                        <span>{item.label}</span>
                                    </span>
                                    {item.badge && (
                                        <span
                                            className={cn(
                                                'relative z-10 rounded-full px-2 py-0.5 font-mono text-[10px] font-bold tabular-nums',
                                                active ? 'bg-white/25 text-white' : 'bg-sky-500 text-white'
                                            )}
                                        >
                                            {item.badge}
                                        </span>
                                    )}
                                </>
                            );

                            return item.hardNavigate ? (
                                <a key={item.href} href={item.href} className={linkClass}>
                                    {inner}
                                </a>
                            ) : (
                                <Link key={item.href} href={item.href} prefetch="hover" className={linkClass}>
                                    {inner}
                                </Link>
                            );
                        })}
                    </div>
                ))}
            </div>

            {/* V21 Signature Floating Gradient Quick-Action Card at Sidebar Base */}
            <div className="px-3 pb-3">
                <div className="relative overflow-hidden rounded-[26px] bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-4 text-white shadow-[0_18px_36px_-12px_rgba(14,165,233,0.55)]">
                    <div
                        aria-hidden="true"
                        className="pointer-events-none absolute -top-10 -right-10 h-28 w-28 rounded-full bg-white/15 blur-xl"
                    />
                    <p className="relative z-10 font-display text-sm font-semibold">
                        {adminMode ? 'Provision Campus Seat' : 'Launch Cloud IDE'}
                    </p>
                    <p className="relative z-10 mt-1 text-[11px] leading-snug text-sky-100/90">
                        {adminMode
                            ? 'Create verified accounts or inspect container telemetry.'
                            : 'Spin up a Nix-backed VS Code container in seconds.'}
                    </p>
                    <div className="relative z-10 mt-3.5">
                        {adminMode ? (
                            <a
                                href="/admin/users/create"
                                className="inline-flex w-full items-center justify-center gap-1.5 rounded-full bg-white px-3.5 py-2 text-xs font-semibold text-blue-700 hover:bg-sky-50 transition-colors"
                            >
                                <Plus className="h-3.5 w-3.5" />
                                Provision Account
                            </a>
                        ) : (
                            <Link
                                href="/workspace/create"
                                prefetch="hover"
                                className="inline-flex w-full items-center justify-center gap-1.5 rounded-full bg-white px-3.5 py-2 text-xs font-semibold text-blue-700 hover:bg-sky-50 transition-colors"
                            >
                                <Plus className="h-3.5 w-3.5" />
                                New Workspace
                            </Link>
                        )}
                    </div>
                </div>
            </div>

            {adminMode && (
                <div className="px-3 pb-2">
                    <Link
                        href="/dashboard"
                        prefetch="hover"
                        className="flex items-center justify-between rounded-full bg-vl-surface-raised/75 px-4 py-2 text-xs font-medium text-vl-ink-secondary hover:text-vl-ink transition-colors"
                    >
                        <span className="flex items-center gap-2">
                            <LayoutDashboard className="h-3.5 w-3.5 text-sky-400" />
                            Campus Portal
                        </span>
                        <ArrowUpRight className="h-3.5 w-3.5" />
                    </Link>
                </div>
            )}

            <div className="p-3 pt-1">
                <div className="flex items-center justify-between gap-2 rounded-full bg-vl-surface-raised/70 p-2 pl-2.5 pr-3">
                    <div className="flex items-center gap-2.5 min-w-0">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-teal-500 text-xs font-bold text-white">
                            {user.avatar_initials}
                        </div>
                        <div className="min-w-0">
                            <p className="truncate text-xs font-semibold text-vl-ink">{user.name}</p>
                            <p className="truncate font-mono text-[10px] text-vl-ink-muted capitalize">{user.role}</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onClick={handleLogout}
                        title="Sign out"
                        aria-label="Sign out"
                        className="rounded-full p-1.5 text-vl-ink-muted hover:bg-rose-500/15 hover:text-rose-400 transition-colors"
                    >
                        <LogOut className="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>
        </div>
    );

    return (
        <div className="min-h-screen bg-vl-bg text-vl-ink">
            <Head title={title || pageTitle} />
            <FlashToast flash={props.flash} />
            <CommandPalette user={user} open={paletteOpen} onClose={() => setPaletteOpen(false)} />

            {props.auth.impersonating && (
                <div className="sticky top-0 z-50 flex items-center justify-between bg-amber-500 px-5 py-1.5 text-xs font-semibold text-slate-950">
                    <span>Impersonating {user.name} ({user.email})</span>
                    <a href="/stop-impersonating" className="rounded-full bg-slate-950/15 px-3 py-0.5 underline">
                        Stop Impersonating
                    </a>
                </div>
            )}

            <div className="flex min-h-screen">
                {/* Detached Floating Curved Desktop Rail (V21/V28 DNA!) */}
                <aside className="hidden lg:fixed lg:inset-y-3 lg:left-3 lg:flex lg:w-[254px] lg:flex-col overflow-hidden rounded-[34px] bg-vl-surface/90 dark:bg-[#0B1221]/90 backdrop-blur-2xl ring-1 ring-vl-border/15 shadow-[0_24px_64px_-24px_rgba(0,0,0,0.45)]">
                    {renderSidebarContent()}
                </aside>

                {/* Mobile Drawer */}
                {mobileDrawer && (
                    <div
                        className="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm lg:hidden"
                        onClick={() => setMobileDrawer(false)}
                    >
                        <aside
                            className="m-3 h-[calc(100%-1.5rem)] w-[274px] overflow-hidden rounded-[34px] bg-vl-surface shadow-2xl"
                            onClick={(e) => e.stopPropagation()}
                        >
                            <div className="flex justify-end px-4 pt-4">
                                <button
                                    type="button"
                                    onClick={() => setMobileDrawer(false)}
                                    className="rounded-full p-2 text-vl-ink-muted hover:bg-vl-surface-raised hover:text-vl-ink"
                                >
                                    <X className="h-4 w-4" />
                                </button>
                            </div>
                            {renderSidebarContent()}
                        </aside>
                    </div>
                )}

                {/* Main Content Column */}
                <div className="flex flex-1 flex-col lg:pl-[278px]">
                    {/* Floating Pill Topbar with Centered Segmented Tabs (V21 DNA!) */}
                    <header className="sticky top-0 z-30 px-4 pt-3 pb-2 sm:px-6 lg:px-8">
                        <div className="flex h-[62px] items-center justify-between gap-3 rounded-full bg-vl-surface/90 px-3.5 backdrop-blur-2xl ring-1 ring-vl-border/15 shadow-[0_14px_40px_-18px_rgba(0,0,0,0.35)] dark:bg-[#0B1221]/90 sm:px-5">
                            <div className="flex items-center gap-3 min-w-0">
                                <button
                                    type="button"
                                    onClick={() => setMobileDrawer(true)}
                                    aria-label="Open menu"
                                    className="inline-flex h-10 w-10 items-center justify-center rounded-full bg-vl-surface-raised text-vl-ink lg:hidden"
                                >
                                    <Menu className="h-4 w-4" />
                                </button>
                                <h1 className="truncate font-display text-[15px] font-semibold tracking-tight text-vl-ink">
                                    {pageTitle || title || 'VisionLab'}
                                </h1>
                            </div>

                            {/* Centered Top Floating Pill Tabs */}
                            <nav className="hidden xl:flex items-center gap-1 rounded-full bg-vl-bg/70 p-1">
                                {topTabs.map((tab) => {
                                    const active = isActive(tab.href);
                                    const cls = cn(
                                        'relative rounded-full px-4 py-1.5 text-xs font-medium transition-colors',
                                        active
                                            ? 'bg-gradient-to-b from-sky-500 to-blue-600 text-white shadow-sm'
                                            : 'text-vl-ink-secondary hover:text-vl-ink'
                                    );
                                    return tab.hardNavigate ? (
                                        <a key={tab.href} href={tab.href} className={cls}>
                                            {tab.label}
                                        </a>
                                    ) : (
                                        <Link key={tab.href} href={tab.href} prefetch="hover" className={cls}>
                                            {tab.label}
                                        </Link>
                                    );
                                })}
                            </nav>

                            <div className="flex items-center gap-2">
                                <button
                                    type="button"
                                    onClick={() => setPaletteOpen(true)}
                                    className="hidden sm:inline-flex items-center gap-2 rounded-full bg-vl-bg/75 px-4 py-2 text-xs text-vl-ink-muted ring-1 ring-inset ring-vl-border/15 hover:text-vl-ink transition-colors"
                                >
                                    <Search className="h-3.5 w-3.5" />
                                    <span className="hidden md:inline">Quick jump</span>
                                    <kbd className="rounded-full bg-vl-surface px-2 py-0.5 font-mono text-[10px]">
                                        ⌘K
                                    </kbd>
                                </button>

                                <a
                                    href="/announcements"
                                    aria-label="Campus announcements"
                                    className="relative inline-flex h-10 w-10 items-center justify-center rounded-full bg-vl-bg/75 text-vl-ink-secondary ring-1 ring-inset ring-vl-border/15 hover:bg-vl-surface-raised hover:text-vl-ink transition-colors"
                                >
                                    <Bell className="h-4 w-4" />
                                    {props.unreadAnnouncementsCount > 0 && (
                                        <span className="absolute -top-0.5 -right-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-sky-500 px-1 font-mono text-[9px] font-bold text-white tabular-nums">
                                            {props.unreadAnnouncementsCount > 9 ? '9+' : props.unreadAnnouncementsCount}
                                        </span>
                                    )}
                                </a>

                                <ThemeToggle />

                                <a
                                    href="/profile"
                                    title="Account settings"
                                    className="inline-flex h-10 w-10 items-center justify-center rounded-full bg-vl-bg/75 text-vl-ink-secondary ring-1 ring-inset ring-vl-border/15 hover:bg-vl-surface-raised hover:text-vl-ink transition-colors"
                                >
                                    <Settings className="h-4 w-4" />
                                </a>
                            </div>
                        </div>
                    </header>

                    <motion.main
                        key={url.split('?')[0]}
                        initial={{ opacity: 0, y: 8 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.24, ease: [0.16, 1, 0.3, 1] }}
                        className="flex-1 px-4 pt-5 pb-10 sm:px-6 lg:px-8"
                    >
                        <div className="mx-auto max-w-[1340px]">{children}</div>
                    </motion.main>
                </div>
            </div>
        </div>
    );
}

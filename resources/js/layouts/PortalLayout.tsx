import React, { useEffect, useLayoutEffect, useMemo, useRef, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'motion/react';
import {
    Activity,
    ArrowUpRight,
    Award,
    Bell,
    BookMarked,
    BookOpen,
    Briefcase,
    Building2,
    Check,
    CheckCircle2,
    CheckSquare,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    Code2,
    Cpu,
    FileCode2,
    Globe,
    GraduationCap,
    Layers,
    LayoutDashboard,
    LogOut,
    Menu,
    Moon,
    Pin,
    Plus,
    Receipt,
    Search,
    Settings,
    ShieldCheck,
    Sliders,
    Sparkles,
    Sun,
    Terminal,
    TrendingUp,
    User,
    Users,
    Volume2,
    VolumeX,
    Webhook,
    Wrench,
    X,
} from 'lucide-react';
import { cn } from '@/lib/cn';
import { useTheme } from '@/components/ThemeProvider';
import { PORTAL_LOCALES, usePortalShell } from '@/components/PortalShellContext';
import { BrandLogo, FlashToast } from '@/components/ui/Primitives';
import { CommandPalette } from '@/components/CommandPalette';
import { PortalAiDrawer } from '@/components/PortalAiDrawer';
import type { PortalNotification, SharedProps } from '@/types';

interface NavEntry {
    label: string;
    href: string;
    icon: React.ComponentType<{ className?: string }>;
    matchPrefix?: string;
    badge?: number;
}

export function PortalLayout({
    title,
    pageTitle,
    subtitle,
    adminMode = false,
    children,
}: {
    title?: string;
    pageTitle?: string;
    subtitle?: string;
    adminMode?: boolean;
    children: React.ReactNode;
}) {
    const { url, props } = usePage<SharedProps>();
    const { isDark, toggleTheme } = useTheme();
    const {
        aiOpen,
        toggleAi,
        sidebarCollapsed,
        setSidebarCollapsed,
        soundEnabled,
        toggleSound,
        locale,
        setLocale,
        sidebarScrollRef,
    } = usePortalShell();

    const user = props.auth.user!;
    const notifications: PortalNotification[] = props.recentNotifications ?? [];
    const unreadCount = props.unreadAnnouncementsCount ?? 0;
    const activeLocale = PORTAL_LOCALES.find((l) => l.code === locale) ?? PORTAL_LOCALES[0];
    const settingsHref = adminMode && user.role === 'admin' ? '/admin/system' : '/profile';

    const [paletteOpen, setPaletteOpen] = useState(false);
    const [mobileDrawer, setMobileDrawer] = useState(false);
    const [profileMenuOpen, setProfileMenuOpen] = useState(false);
    const [notifOpen, setNotifOpen] = useState(false);
    const [langOpen, setLangOpen] = useState(false);
    const [markingReadId, setMarkingReadId] = useState<number | null>(null);

    const profileRef = useRef<HTMLDivElement>(null);
    const notifRef = useRef<HTMLDivElement>(null);
    const langRef = useRef<HTMLDivElement>(null);
    const desktopNavRef = useRef<HTMLElement>(null);

    // Preserve exact left sidebar scroll position seamlessly across Inertia navigations
    useLayoutEffect(() => {
        if (desktopNavRef.current && sidebarScrollRef.current > 0) {
            desktopNavRef.current.scrollTop = sidebarScrollRef.current;
        }
    }, [sidebarScrollRef]);

    useEffect(() => {
        const onKey = (e: KeyboardEvent) => {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                setPaletteOpen((v) => !v);
            }
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'j') {
                e.preventDefault();
                toggleAi();
            }
            if (e.key === 'Escape') {
                setNotifOpen(false);
                setProfileMenuOpen(false);
                setLangOpen(false);
            }
        };
        window.addEventListener('keydown', onKey);
        return () => window.removeEventListener('keydown', onKey);
    }, [toggleAi]);

    useEffect(() => {
        setMobileDrawer(false);
        setProfileMenuOpen(false);
        setNotifOpen(false);
        setLangOpen(false);
    }, [url]);

    useEffect(() => {
        if (!profileMenuOpen && !notifOpen && !langOpen) return;
        const handleClickOutside = (e: MouseEvent) => {
            const target = e.target as Node;
            if (profileMenuOpen && profileRef.current && !profileRef.current.contains(target)) {
                setProfileMenuOpen(false);
            }
            if (notifOpen && notifRef.current && !notifRef.current.contains(target)) {
                setNotifOpen(false);
            }
            if (langOpen && langRef.current && !langRef.current.contains(target)) {
                setLangOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, [profileMenuOpen, notifOpen, langOpen]);

    const handleMarkNotificationRead = (e: React.MouseEvent, id: number) => {
        e.preventDefault();
        e.stopPropagation();
        if (markingReadId !== null) return;
        setMarkingReadId(id);
        router.post(
            `/announcements/${id}/read`,
            {},
            {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setMarkingReadId(null),
            }
        );
    };

    const sections = useMemo(() => {
        if (adminMode) {
            return [
                {
                    heading: 'Operations',
                    items: [
                        { label: 'Command Center', href: '/admin', icon: ShieldCheck },
                        { label: 'Platform Analytics', href: '/admin/analytics', icon: TrendingUp },
                        { label: 'Cluster Telemetry', href: '/admin/status', icon: Activity },
                    ] as NavEntry[],
                },
                {
                    heading: 'Governance',
                    items: [
                        { label: 'Users & Roles', href: '/admin/users', icon: Users, matchPrefix: '/admin/users' },
                        { label: 'Workspaces', href: '/admin/workspaces', icon: Code2, matchPrefix: '/admin/workspaces' },
                        { label: 'Templates', href: '/admin/templates', icon: Layers, matchPrefix: '/admin/templates' },
                        { label: 'VSIX Extensions', href: '/admin/extensions', icon: Cpu, matchPrefix: '/admin/extensions' },
                        { label: 'Resource Quotas', href: '/admin/quotas', icon: Sliders },
                        { label: 'Audit Trail', href: '/admin/audits', icon: FileCode2 },
                        { label: 'Webhooks', href: '/admin/webhooks', icon: Webhook, matchPrefix: '/admin/webhooks' },
                        { label: 'System Config', href: '/admin/system', icon: Wrench },
                    ] as NavEntry[],
                },
                {
                    heading: 'Institute ERP',
                    items: [
                        { label: 'Campuses', href: '/admin/campuses', icon: Building2, matchPrefix: '/admin/campuses' },
                        { label: 'Departments', href: '/admin/departments', icon: BookOpen, matchPrefix: '/admin/departments' },
                        { label: 'Course Batches', href: '/admin/batches', icon: GraduationCap, matchPrefix: '/admin/batches' },
                        { label: 'Employees', href: '/admin/employees', icon: Briefcase, matchPrefix: '/admin/employees' },
                        { label: 'Fee Challans', href: '/admin/challans', icon: Receipt, matchPrefix: '/admin/challans' },
                        { label: 'Library', href: '/admin/library', icon: BookMarked, matchPrefix: '/admin/library' },
                    ] as NavEntry[],
                },
            ];
        }

        const primary: NavEntry[] = [
            { label: 'Overview', href: '/dashboard', icon: LayoutDashboard },
            { label: 'Workspaces', href: '/workspace', icon: Terminal, matchPrefix: '/workspace' },
            { label: 'Courses', href: '/courses', icon: BookOpen, matchPrefix: '/courses' },
        ];

        if (user.role === 'instructor' || user.role === 'admin') {
            primary.push({
                label: 'Grading Queue',
                href: '/submissions',
                icon: CheckSquare,
                matchPrefix: '/submissions',
            });
        } else {
            primary.push({
                label: 'Join Course',
                href: '/join',
                icon: GraduationCap,
            });
        }

        const growth: NavEntry[] = [
            {
                label: 'Announcements',
                href: '/announcements',
                icon: Bell,
                matchPrefix: '/announcements',
                badge: unreadCount > 0 ? unreadCount : undefined,
            },
            { label: 'Progress', href: '/progress', icon: TrendingUp },
            { label: 'Achievements', href: '/gamification', icon: Award },
            { label: 'Profile', href: '/profile', icon: User, matchPrefix: '/profile' },
        ];

        const groups = [
            { heading: 'Main Menu', items: primary },
            { heading: 'Campus', items: growth },
        ];

        if (user.role === 'admin') {
            groups.push({
                heading: 'Governance',
                items: [{ label: 'Admin Console', href: '/admin', icon: ShieldCheck }],
            });
        }

        return groups;
    }, [adminMode, user.role, unreadCount]);

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

    const resolvedTitle = pageTitle || title || 'VisionLab';

    const renderSidebarNav = (compact = false, attachRef = false) => (
        <div className="flex h-full flex-col">
            {/* Hover-Only Zero-Shift Scrollbar Nav */}
            <nav
                ref={attachRef ? desktopNavRef : undefined}
                onScroll={
                    attachRef
                        ? (e) => {
                              sidebarScrollRef.current = e.currentTarget.scrollTop;
                          }
                        : undefined
                }
                className="vl-sidebar-scroll flex-1 px-3 pt-2 pb-6 space-y-5 [mask-image:linear-gradient(to_bottom,black_0%,black_93%,transparent_100%)]"
            >
                {sections.map((section) => (
                    <div key={section.heading} className="space-y-1">
                        {!compact && (
                            <p className="px-3.5 pb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                {section.heading}
                            </p>
                        )}
                        {section.items.map((item) => {
                            const Icon = item.icon;
                            const active = isActive(item.href, item.matchPrefix);

                            return (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    prefetch="hover"
                                    title={compact ? item.label : undefined}
                                    className={cn(
                                        'group relative flex items-center rounded-full py-2 text-[13px] font-medium transition-all duration-200 ease-out',
                                        compact ? 'justify-center px-0' : 'justify-between px-2.5 pr-3.5',
                                        active
                                            ? 'bg-gradient-to-r from-sky-500 via-blue-600 to-teal-500 text-white shadow-[0_12px_26px_-8px_rgba(2,132,199,0.5),inset_0_1px_0_rgba(255,255,255,0.35)] dark:bg-none dark:bg-white dark:text-slate-950 dark:shadow-[0_14px_30px_-8px_rgba(56,189,248,0.35),inset_0_1px_0_rgba(255,255,255,0.95)]'
                                            : 'text-slate-700 hover:translate-x-1 hover:bg-white/65 hover:text-slate-950 hover:shadow-[0_4px_16px_-6px_rgba(15,23,42,0.12),inset_0_1px_0_rgba(255,255,255,0.85)] dark:text-slate-200/85 dark:hover:bg-white/10 dark:hover:text-white dark:hover:shadow-[inset_0_1px_0_rgba(255,255,255,0.18)]'
                                    )}
                                >
                                    <span
                                        className={cn(
                                            'relative z-10 flex items-center',
                                            compact ? 'justify-center' : 'gap-3 min-w-0'
                                        )}
                                    >
                                        <span
                                            className={cn(
                                                'flex h-7 w-7 shrink-0 items-center justify-center rounded-full transition-all duration-200',
                                                active
                                                    ? 'bg-white/20 text-white dark:bg-gradient-to-br dark:from-sky-500 dark:via-blue-600 dark:to-teal-500 dark:text-white dark:shadow-sm'
                                                    : 'bg-white/65 text-slate-700 ring-1 ring-inset ring-white/80 group-hover:scale-105 group-hover:bg-sky-500/15 group-hover:text-sky-700 dark:bg-white/10 dark:text-slate-200 dark:ring-white/10 dark:group-hover:bg-sky-500/25 dark:group-hover:text-sky-200'
                                            )}
                                        >
                                            <Icon className="h-[15px] w-[15px]" />
                                        </span>
                                        {!compact && <span className="truncate font-semibold">{item.label}</span>}
                                    </span>

                                    {!compact && item.badge ? (
                                        <span
                                            className={cn(
                                                'relative z-10 rounded-full px-2 py-0.5 font-mono text-[10px] font-bold tabular-nums',
                                                active
                                                    ? 'bg-white/25 text-white dark:bg-blue-600 dark:text-white'
                                                    : 'bg-sky-500/15 text-sky-700 dark:bg-sky-400/20 dark:text-sky-200'
                                            )}
                                        >
                                            {item.badge}
                                        </span>
                                    ) : !compact && active ? (
                                        <span className="relative z-10 h-1.5 w-1.5 rounded-full bg-white/90 dark:bg-teal-500" />
                                    ) : null}
                                </Link>
                            );
                        })}
                    </div>
                ))}
            </nav>

            {/* Bottom Utility Links — Seamless Frosted Integration */}
            <div className="p-3 pt-2 pb-3.5 space-y-1">
                {adminMode && (
                    <Link
                        href="/dashboard"
                        prefetch="hover"
                        title={compact ? 'Campus Portal' : undefined}
                        className={cn(
                            'group flex items-center rounded-full bg-white/55 py-2 text-xs font-semibold text-slate-800 ring-1 ring-inset ring-white/80 hover:bg-white/90 hover:translate-x-0.5 dark:bg-white/10 dark:text-white dark:ring-white/15 dark:hover:bg-white/20 transition-all',
                            compact ? 'justify-center px-0' : 'justify-between px-3.5'
                        )}
                    >
                        <span className="flex items-center gap-2.5">
                            <LayoutDashboard className="h-4 w-4 text-sky-600 dark:text-sky-300" />
                            {!compact && <span>Campus Portal</span>}
                        </span>
                        {!compact && <ArrowUpRight className="h-3.5 w-3.5 text-slate-500 dark:text-slate-300" />}
                    </Link>
                )}

                <Link
                    href="/profile"
                    prefetch="hover"
                    title={compact ? 'Settings' : undefined}
                    className={cn(
                        'flex items-center gap-2.5 rounded-full py-2 text-xs font-medium text-slate-700 hover:bg-white/65 hover:text-slate-950 hover:translate-x-0.5 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white transition-all',
                        compact ? 'justify-center px-0' : 'px-3.5'
                    )}
                >
                    <Settings className="h-4 w-4" />
                    {!compact && <span>Account Settings</span>}
                </Link>

                <button
                    type="button"
                    onClick={handleLogout}
                    title={compact ? 'Sign out' : undefined}
                    className={cn(
                        'flex w-full items-center gap-2.5 rounded-full py-2 text-xs font-medium text-slate-700 hover:bg-rose-500/15 hover:text-rose-700 dark:text-slate-300 dark:hover:bg-rose-500/25 dark:hover:text-rose-200 transition-all',
                        compact ? 'justify-center px-0' : 'px-3.5'
                    )}
                >
                    <LogOut className="h-4 w-4" />
                    {!compact && <span>Sign out</span>}
                </button>
            </div>
        </div>
    );

    const glassIconBtnClass =
        'relative inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/55 text-slate-700 backdrop-blur-xl ring-1 ring-inset ring-white/80 hover:bg-white/90 hover:text-slate-950 hover:-translate-y-0.5 dark:bg-white/10 dark:text-white dark:ring-white/15 dark:hover:bg-white/20 transition-all';

    return (
        <div className="relative min-h-screen lg:h-screen lg:overflow-hidden flex flex-col text-vl-ink overflow-x-clip">
            <Head title={title || pageTitle} />
            <FlashToast flash={props.flash} />
            <CommandPalette user={user} open={paletteOpen} onClose={() => setPaletteOpen(false)} />

            {/* ═══════════════════════════════════════════════════════════════════════
                LAYER 0 & 1: FULL-VIEWPORT SCULPTED ARTWORK + CONTINUOUS LIQUID GLASS
                Header, Left Sidebar, Bottom/Right Framing Margin & Right Vision AI
                Sidebar sit directly on ONE seamless liquid-glass plane.
            ═══════════════════════════════════════════════════════════════════════ */}
            <div aria-hidden="true" className="pointer-events-none fixed inset-0 -z-20 overflow-hidden">
                <img
                    src={isDark ? '/images/redesign/portal-bg-dark.jpg' : '/images/redesign/portal-bg-light.jpg'}
                    alt=""
                    className="h-full w-full object-cover scale-[1.03]"
                />
            </div>
            <div
                aria-hidden="true"
                className="pointer-events-none fixed inset-0 -z-10 bg-white/35 backdrop-blur-2xl saturate-150 dark:bg-[#050C1C]/45 transition-colors duration-300"
            />

            {props.auth.impersonating && (
                <div className="relative z-50 flex shrink-0 items-center justify-between bg-amber-500 px-5 py-1.5 text-xs font-semibold text-slate-950">
                    <span>Impersonating {user.name} ({user.email})</span>
                    <Link href="/stop-impersonating" className="rounded-full bg-slate-950/15 px-3 py-0.5 underline">
                        Stop Impersonating
                    </Link>
                </div>
            )}

            {/* ═══════════════════════════════════════════════════════════════════════
                FUSED LIQUID-GLASS TOP HEADER (ZERO BORDERS)
                Utility cluster: Search · Language · Speaker · Bell · Settings · Theme
                · Profile · Far-Right Corner Vision AI Icon-Only Toggle
            ═══════════════════════════════════════════════════════════════════════ */}
            <header className="relative z-30 flex h-[68px] shrink-0 w-full items-center">
                {/* Left Brand Cap — Exact width of Left Sidebar, ZERO divider border */}
                <div
                    className={cn(
                        'hidden lg:flex h-full shrink-0 items-center justify-between px-5 transition-all duration-200',
                        sidebarCollapsed ? 'w-[84px] justify-center px-2' : 'w-[258px]'
                    )}
                >
                    <Link href={adminMode ? '/admin' : '/dashboard'} prefetch="hover" className="flex items-center">
                        <BrandLogo inverted={isDark} compact={sidebarCollapsed} size="h-8 w-8" />
                    </Link>
                    {!sidebarCollapsed && (
                        <button
                            type="button"
                            onClick={() => setSidebarCollapsed(true)}
                            title="Collapse sidebar"
                            aria-label="Collapse sidebar"
                            className="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/55 text-slate-700 ring-1 ring-inset ring-white/80 hover:bg-white/90 hover:text-slate-950 dark:bg-white/10 dark:text-slate-200 dark:ring-white/15 dark:hover:bg-white/20 dark:hover:text-white transition-all"
                        >
                            <ChevronLeft className="h-4 w-4" />
                        </button>
                    )}
                </div>

                {/* Right Header Bar — Continuous glass chrome */}
                <div className="flex flex-1 items-center justify-between gap-3 px-4 sm:px-5 lg:pl-6 lg:pr-4 min-w-0">
                    <div className="flex items-center gap-3 min-w-0">
                        <button
                            type="button"
                            onClick={() => setMobileDrawer(true)}
                            aria-label="Open navigation menu"
                            className="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/60 text-slate-800 ring-1 ring-inset ring-white/80 hover:bg-white/90 dark:bg-white/10 dark:text-white dark:ring-white/15 dark:hover:bg-white/20 transition-colors lg:hidden"
                        >
                            <Menu className="h-4 w-4" />
                        </button>
                        {sidebarCollapsed && (
                            <button
                                type="button"
                                onClick={() => setSidebarCollapsed(false)}
                                title="Expand sidebar"
                                aria-label="Expand sidebar"
                                className="hidden lg:inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/60 text-slate-700 ring-1 ring-inset ring-white/80 hover:bg-white/90 dark:bg-white/10 dark:text-white dark:ring-white/15 dark:hover:bg-white/20 transition-all"
                            >
                                <ChevronRight className="h-4 w-4" />
                            </button>
                        )}
                        <div className="min-w-0">
                            <h1 className="truncate font-display text-lg font-bold tracking-[-0.02em] text-slate-900 dark:text-white sm:text-xl">
                                {resolvedTitle}
                            </h1>
                            {subtitle && (
                                <p className="hidden xl:block truncate text-[11px] font-medium text-slate-600 dark:text-slate-300/80">
                                    {subtitle}
                                </p>
                            )}
                        </div>
                    </div>

                    {/* Interactive Liquid-Glass Header Controls */}
                    <div className="flex items-center gap-1.5 sm:gap-2">
                        {/* Command Palette Trigger */}
                        <button
                            type="button"
                            onClick={() => setPaletteOpen(true)}
                            className="hidden xl:inline-flex items-center gap-2.5 rounded-full bg-white/55 px-3.5 py-2 text-xs font-medium text-slate-700 backdrop-blur-xl ring-1 ring-inset ring-white/80 shadow-[0_6px_18px_-8px_rgba(15,23,42,0.12)] hover:bg-white/85 hover:text-slate-950 hover:-translate-y-0.5 dark:bg-white/10 dark:text-slate-200 dark:ring-white/15 dark:hover:bg-white/20 dark:hover:text-white transition-all"
                        >
                            <Search className="h-3.5 w-3.5 text-slate-500 dark:text-slate-300" />
                            <span className="w-24 text-left truncate">Search...</span>
                            <kbd className="rounded-full bg-slate-900/10 px-2 py-0.5 font-mono text-[10px] text-slate-700 dark:bg-white/15 dark:text-slate-200">
                                ⌘K
                            </kbd>
                        </button>

                        {/* Compact Search Icon on md–xl */}
                        <button
                            type="button"
                            onClick={() => setPaletteOpen(true)}
                            aria-label="Search portal"
                            title="Search portal (⌘K)"
                            className={cn(glassIconBtnClass, 'hidden md:inline-flex xl:hidden')}
                        >
                            <Search className="h-4 w-4" />
                        </button>

                        {/* Language Selector Button + Multilingual Popover */}
                        <div ref={langRef} className="relative">
                            <button
                                type="button"
                                onClick={() => {
                                    setLangOpen((v) => !v);
                                    setNotifOpen(false);
                                    setProfileMenuOpen(false);
                                }}
                                aria-expanded={langOpen}
                                aria-haspopup="listbox"
                                aria-label="Select language"
                                title={`Language: ${activeLocale.label}`}
                                className={cn(
                                    'inline-flex h-9 items-center gap-1.5 rounded-full px-3 text-xs font-semibold backdrop-blur-xl ring-1 ring-inset transition-all hover:-translate-y-0.5',
                                    langOpen
                                        ? 'bg-sky-500 text-white ring-sky-400 shadow-[0_10px_24px_-6px_rgba(2,132,199,0.5)]'
                                        : 'bg-white/55 text-slate-700 ring-white/80 hover:bg-white/90 hover:text-slate-950 dark:bg-white/10 dark:text-white dark:ring-white/15 dark:hover:bg-white/20'
                                )}
                            >
                                <Globe className="h-3.5 w-3.5 shrink-0" />
                                <span className="font-mono text-[11px]">{activeLocale.shortLabel}</span>
                                <ChevronDown className="hidden sm:block h-3 w-3 opacity-70" />
                            </button>

                            <AnimatePresence>
                                {langOpen && (
                                    <motion.div
                                        role="listbox"
                                        aria-label="Portal language"
                                        initial={{ opacity: 0, y: 8, scale: 0.96 }}
                                        animate={{ opacity: 1, y: 0, scale: 1 }}
                                        exit={{ opacity: 0, y: 6, scale: 0.96 }}
                                        transition={{ duration: 0.16, ease: [0.16, 1, 0.3, 1] }}
                                        className="absolute right-0 mt-2.5 w-60 z-50 overflow-hidden rounded-2xl bg-white/90 p-2 text-slate-900 shadow-[0_24px_60px_-14px_rgba(2,12,27,0.3)] backdrop-blur-3xl ring-1 ring-slate-900/10 dark:bg-[#091326]/95 dark:text-white dark:ring-white/15"
                                    >
                                        <div className="flex items-center justify-between px-3 py-2 border-b border-slate-900/10 dark:border-white/10 mb-1">
                                            <span className="font-display text-xs font-bold tracking-[-0.01em]">
                                                Interface Language
                                            </span>
                                            <span className="rounded-full bg-sky-500/15 px-2 py-0.5 font-mono text-[9px] font-semibold text-sky-700 dark:text-sky-300">
                                                i18n Ready
                                            </span>
                                        </div>

                                        <div className="space-y-0.5">
                                            {PORTAL_LOCALES.map((opt) => {
                                                const selected = opt.code === locale;
                                                return (
                                                    <button
                                                        key={opt.code}
                                                        type="button"
                                                        role="option"
                                                        aria-selected={selected}
                                                        onClick={() => {
                                                            setLocale(opt.code);
                                                            setLangOpen(false);
                                                        }}
                                                        className={cn(
                                                            'flex w-full items-center justify-between gap-2 rounded-full px-3 py-2 text-left text-xs transition-colors',
                                                            selected
                                                                ? 'bg-sky-500/15 font-semibold text-sky-700 dark:bg-sky-400/20 dark:text-sky-200'
                                                                : 'text-slate-700 hover:bg-slate-900/5 dark:text-slate-200 dark:hover:bg-white/10'
                                                        )}
                                                    >
                                                        <span className="flex items-center gap-2.5 min-w-0">
                                                            <span className="flex h-5 w-7 shrink-0 items-center justify-center rounded-full bg-slate-900/5 font-mono text-[10px] font-bold dark:bg-white/10">
                                                                {opt.shortLabel}
                                                            </span>
                                                            <span className="truncate">{opt.nativeLabel}</span>
                                                        </span>
                                                        {selected ? (
                                                            <Check className="h-3.5 w-3.5 shrink-0 text-sky-600 dark:text-sky-300" />
                                                        ) : !opt.ready ? (
                                                            <span className="shrink-0 font-mono text-[9px] text-slate-400">
                                                                Soon
                                                            </span>
                                                        ) : null}
                                                    </button>
                                                );
                                            })}
                                        </div>

                                        <p className="mt-1.5 border-t border-slate-900/10 px-3 pt-2 pb-1 text-[10px] leading-snug text-slate-500 dark:border-white/10 dark:text-slate-400">
                                            Multilingual packs are queued for upcoming campus rollout.
                                        </p>
                                    </motion.div>
                                )}
                            </AnimatePresence>
                        </div>

                        {/* Speaker / Audio Cues Toggle */}
                        <button
                            type="button"
                            onClick={toggleSound}
                            aria-pressed={soundEnabled}
                            aria-label={soundEnabled ? 'Mute portal sound cues' : 'Unmute portal sound cues'}
                            title={soundEnabled ? 'Sound cues on (click to mute)' : 'Sound cues muted (click to enable)'}
                            className={cn(
                                glassIconBtnClass,
                                'hidden sm:inline-flex',
                                !soundEnabled && 'opacity-75'
                            )}
                        >
                            {soundEnabled ? (
                                <Volume2 className="h-4 w-4 text-sky-600 dark:text-sky-300" />
                            ) : (
                                <VolumeX className="h-4 w-4 text-slate-500 dark:text-slate-400" />
                            )}
                        </button>

                        {/* Notifications Bell + Frosted Glass Popover Popup */}
                        <div ref={notifRef} className="relative">
                            <button
                                type="button"
                                onClick={() => {
                                    setNotifOpen((v) => !v);
                                    setProfileMenuOpen(false);
                                    setLangOpen(false);
                                }}
                                aria-expanded={notifOpen}
                                aria-haspopup="dialog"
                                aria-label="Notifications"
                                title="Notifications"
                                className={cn(
                                    'relative inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full backdrop-blur-xl ring-1 ring-inset transition-all hover:-translate-y-0.5',
                                    notifOpen
                                        ? 'bg-sky-500 text-white ring-sky-400 shadow-[0_10px_24px_-6px_rgba(2,132,199,0.5)]'
                                        : 'bg-white/55 text-slate-700 ring-white/80 hover:bg-white/90 hover:text-slate-950 dark:bg-white/10 dark:text-white dark:ring-white/15 dark:hover:bg-white/20'
                                )}
                            >
                                <Bell className="h-4 w-4" />
                                {unreadCount > 0 && (
                                    <span className="absolute -top-0.5 -right-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-gradient-to-r from-sky-500 to-teal-500 px-1 font-mono text-[9px] font-bold text-white shadow-sm tabular-nums">
                                        {unreadCount > 9 ? '9+' : unreadCount}
                                    </span>
                                )}
                            </button>

                            <AnimatePresence>
                                {notifOpen && (
                                    <motion.div
                                        role="dialog"
                                        aria-label="Campus notifications"
                                        initial={{ opacity: 0, y: 8, scale: 0.96 }}
                                        animate={{ opacity: 1, y: 0, scale: 1 }}
                                        exit={{ opacity: 0, y: 6, scale: 0.96 }}
                                        transition={{ duration: 0.16, ease: [0.16, 1, 0.3, 1] }}
                                        className="absolute right-0 mt-2.5 w-[330px] sm:w-[390px] z-50 overflow-hidden rounded-2xl bg-white/90 text-slate-900 shadow-[0_24px_60px_-14px_rgba(2,12,27,0.3)] backdrop-blur-3xl ring-1 ring-slate-900/10 dark:bg-[#091326]/95 dark:text-white dark:ring-white/15"
                                    >
                                        {/* Popover Header */}
                                        <div className="flex items-center justify-between gap-2 px-4 py-3.5 border-b border-slate-900/10 dark:border-white/10">
                                            <div className="flex items-center gap-2">
                                                <h3 className="font-display text-sm font-bold tracking-[-0.015em]">
                                                    Notifications
                                                </h3>
                                                <span
                                                    className={cn(
                                                        'rounded-full px-2.5 py-0.5 font-mono text-[10px] font-semibold tabular-nums',
                                                        unreadCount > 0
                                                            ? 'bg-sky-500/15 text-sky-700 dark:bg-sky-400/20 dark:text-sky-300'
                                                            : 'bg-slate-900/5 text-slate-500 dark:bg-white/10 dark:text-slate-300'
                                                    )}
                                                >
                                                    {unreadCount > 0 ? `${unreadCount} unread` : 'All caught up'}
                                                </span>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => setNotifOpen(false)}
                                                aria-label="Close notifications"
                                                className="flex h-7 w-7 items-center justify-center rounded-full text-slate-500 hover:bg-slate-900/5 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white transition-colors"
                                            >
                                                <X className="h-3.5 w-3.5" />
                                            </button>
                                        </div>

                                        {/* Notifications Feed */}
                                        <div className="vl-sidebar-scroll max-h-[340px] divide-y divide-slate-900/5 dark:divide-white/5">
                                            {notifications.length === 0 ? (
                                                <div className="flex flex-col items-center justify-center px-6 py-10 text-center">
                                                    <span className="flex h-11 w-11 items-center justify-center rounded-full bg-sky-500/10 text-sky-600 dark:bg-sky-400/15 dark:text-sky-300">
                                                        <CheckCircle2 className="h-5 w-5" />
                                                    </span>
                                                    <p className="mt-3 font-display text-sm font-semibold">
                                                        You&apos;re all caught up
                                                    </p>
                                                    <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                        New campus and course announcements will appear right here.
                                                    </p>
                                                </div>
                                            ) : (
                                                notifications.map((item) => {
                                                    const dest = item.course_slug
                                                        ? `/courses/${item.course_slug}`
                                                        : '/announcements';

                                                    return (
                                                        <div
                                                            key={item.id}
                                                            className={cn(
                                                                'group relative px-4 py-3 transition-colors hover:bg-sky-500/5 dark:hover:bg-white/5',
                                                                item.is_unread && 'bg-sky-500/5 dark:bg-sky-400/5'
                                                            )}
                                                        >
                                                            <Link
                                                                href={dest}
                                                                onClick={() => setNotifOpen(false)}
                                                                className="block"
                                                            >
                                                                <div className="flex items-start gap-2.5">
                                                                    <span
                                                                        className={cn(
                                                                            'mt-1.5 h-2 w-2 shrink-0 rounded-full',
                                                                            item.is_unread
                                                                                ? 'bg-sky-500 shadow-[0_0_8px_rgba(14,165,233,0.75)]'
                                                                                : 'bg-slate-300 dark:bg-slate-600'
                                                                        )}
                                                                    />
                                                                    <div className="min-w-0 flex-1">
                                                                        <div className="flex items-center gap-1.5">
                                                                            {item.pinned && (
                                                                                <Pin className="h-3 w-3 shrink-0 text-amber-500" />
                                                                            )}
                                                                            <p className="truncate text-xs font-semibold text-slate-900 dark:text-white">
                                                                                {item.title}
                                                                            </p>
                                                                        </div>
                                                                        <p className="mt-0.5 line-clamp-2 text-[11px] leading-relaxed text-slate-600 dark:text-slate-300/80">
                                                                            {item.body}
                                                                        </p>
                                                                        <div className="mt-2 flex items-center justify-between gap-2">
                                                                            <div className="flex items-center gap-1.5 min-w-0">
                                                                                <span className="truncate rounded-full bg-slate-900/5 px-2 py-0.5 text-[10px] font-medium text-slate-600 dark:bg-white/10 dark:text-slate-300">
                                                                                    {item.course_title ?? 'Campus Global'}
                                                                                </span>
                                                                                <span className="shrink-0 font-mono text-[10px] text-slate-400">
                                                                                    {item.created_human}
                                                                                </span>
                                                                            </div>
                                                                            {item.is_unread && (
                                                                                <button
                                                                                    type="button"
                                                                                    disabled={markingReadId === item.id}
                                                                                    onClick={(e) =>
                                                                                        handleMarkNotificationRead(e, item.id)
                                                                                    }
                                                                                    className="inline-flex shrink-0 items-center gap-1 rounded-full bg-sky-500/15 px-2.5 py-0.5 text-[10px] font-semibold text-sky-700 hover:bg-sky-500 hover:text-white dark:text-sky-300 dark:hover:bg-sky-500 dark:hover:text-white transition-colors disabled:opacity-50"
                                                                                >
                                                                                    <Check className="h-2.5 w-2.5" />
                                                                                    Mark read
                                                                                </button>
                                                                            )}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </Link>
                                                        </div>
                                                    );
                                                })
                                            )}
                                        </div>

                                        {/* Popover Footer */}
                                        <div className="flex items-center justify-between gap-2 px-4 py-2.5 border-t border-slate-900/10 bg-slate-50/70 dark:border-white/10 dark:bg-white/5">
                                            <Link
                                                href="/announcements"
                                                onClick={() => setNotifOpen(false)}
                                                className="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-500/10 dark:text-sky-300 dark:hover:bg-sky-400/15 transition-colors"
                                            >
                                                View all announcements
                                                <ArrowUpRight className="h-3.5 w-3.5" />
                                            </Link>
                                            {user.role === 'admin' && (
                                                <Link
                                                    href="/announcements/create"
                                                    onClick={() => setNotifOpen(false)}
                                                    className="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-sky-500 to-blue-600 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:brightness-110 transition-all"
                                                >
                                                    <Plus className="h-3 w-3" />
                                                    Broadcast
                                                </Link>
                                            )}
                                        </div>
                                    </motion.div>
                                )}
                            </AnimatePresence>
                        </div>

                        {/* Settings Icon Button */}
                        <Link
                            href={settingsHref}
                            prefetch="hover"
                            aria-label="Portal settings"
                            title={adminMode ? 'System Settings' : 'Account Settings'}
                            className={cn(glassIconBtnClass, 'hidden sm:inline-flex')}
                        >
                            <Settings className="h-4 w-4" />
                        </Link>

                        {/* Theme Toggle */}
                        <button
                            type="button"
                            onClick={toggleTheme}
                            aria-label={isDark ? 'Switch to light theme' : 'Switch to dark theme'}
                            title={isDark ? 'Switch to light mode' : 'Switch to dark mode'}
                            className={glassIconBtnClass}
                        >
                            {isDark ? <Sun className="h-4 w-4 text-amber-300" /> : <Moon className="h-4 w-4 text-sky-600" />}
                        </button>

                        {/* Profile Pill + Dropdown */}
                        <div ref={profileRef} className="relative">
                            <button
                                type="button"
                                onClick={() => {
                                    setProfileMenuOpen((v) => !v);
                                    setNotifOpen(false);
                                    setLangOpen(false);
                                }}
                                className="flex items-center gap-2 rounded-full bg-white/60 py-1 pl-1 pr-2 text-slate-900 backdrop-blur-xl ring-1 ring-inset ring-white/85 shadow-[0_6px_18px_-8px_rgba(15,23,42,0.12)] hover:bg-white/90 dark:bg-white/10 dark:text-white dark:ring-white/15 dark:hover:bg-white/20 transition-all sm:pr-3"
                            >
                                <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 text-[11px] font-bold text-white shadow-sm">
                                    {user.avatar_initials}
                                </span>
                                <div className="hidden md:block text-left leading-tight">
                                    <p className="max-w-[96px] truncate text-xs font-semibold text-slate-900 dark:text-white">
                                        {user.name}
                                    </p>
                                    <p className="font-mono text-[10px] capitalize text-slate-500 dark:text-slate-300/80">
                                        {user.role}
                                    </p>
                                </div>
                                <ChevronDown className="hidden md:block h-3.5 w-3.5 text-slate-500 dark:text-slate-300" />
                            </button>

                            <AnimatePresence>
                                {profileMenuOpen && (
                                    <motion.div
                                        initial={{ opacity: 0, y: 8, scale: 0.96 }}
                                        animate={{ opacity: 1, y: 0, scale: 1 }}
                                        exit={{ opacity: 0, y: 6, scale: 0.96 }}
                                        transition={{ duration: 0.16, ease: [0.16, 1, 0.3, 1] }}
                                        className="absolute right-0 mt-2.5 w-56 z-50 overflow-hidden rounded-2xl bg-white/90 p-2 text-vl-ink shadow-[0_24px_60px_-14px_rgba(2,12,27,0.3)] backdrop-blur-3xl ring-1 ring-slate-900/10 dark:bg-[#091326]/95 dark:ring-white/15"
                                    >
                                        <div className="px-3 py-2 border-b border-slate-900/10 dark:border-white/10 mb-1">
                                            <p className="truncate text-xs font-semibold text-vl-ink">{user.name}</p>
                                            <p className="truncate text-[11px] text-vl-ink-muted">{user.email}</p>
                                        </div>
                                        <Link
                                            href="/profile"
                                            className="flex items-center gap-2.5 rounded-full px-3 py-2 text-xs font-medium text-vl-ink-secondary hover:bg-sky-500/10 hover:text-vl-ink transition-colors"
                                        >
                                            <User className="h-3.5 w-3.5" />
                                            Profile &amp; Security
                                        </Link>
                                        <Link
                                            href="/profile/dashboard"
                                            className="flex items-center gap-2.5 rounded-full px-3 py-2 text-xs font-medium text-vl-ink-secondary hover:bg-sky-500/10 hover:text-vl-ink transition-colors"
                                        >
                                            <Award className="h-3.5 w-3.5" />
                                            Academic Showcase
                                        </Link>
                                        {user.role === 'admin' && (
                                            <Link
                                                href={adminMode ? '/dashboard' : '/admin'}
                                                className="flex items-center gap-2.5 rounded-full px-3 py-2 text-xs font-medium text-sky-600 dark:text-sky-400 hover:bg-sky-500/15 transition-colors"
                                            >
                                                <ShieldCheck className="h-3.5 w-3.5" />
                                                {adminMode ? 'Switch to Campus Portal' : 'Open Admin Console'}
                                            </Link>
                                        )}
                                        <button
                                            type="button"
                                            onClick={handleLogout}
                                            className="mt-1 flex w-full items-center gap-2.5 rounded-full px-3 py-2 text-xs font-medium text-rose-500 hover:bg-rose-500/15 transition-colors"
                                        >
                                            <LogOut className="h-3.5 w-3.5" />
                                            Sign out
                                        </button>
                                    </motion.div>
                                )}
                            </AnimatePresence>
                        </div>

                        {/* Vision AI — Far-Right Corner Icon-Only Orb (Directly above Right AI Sidebar) */}
                        <button
                            type="button"
                            onClick={toggleAi}
                            aria-pressed={aiOpen}
                            aria-label="Toggle Vision AI Sidebar"
                            title="Vision AI Copilot (⌘J)"
                            className={cn(
                                'inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.95]',
                                aiOpen
                                    ? 'bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 text-white shadow-[0_12px_28px_-6px_rgba(2,132,199,0.6),inset_0_1px_0_rgba(255,255,255,0.4)]'
                                    : 'bg-white/65 text-sky-600 backdrop-blur-xl ring-1 ring-inset ring-white/90 shadow-[0_6px_18px_-8px_rgba(2,132,199,0.28)] hover:bg-gradient-to-br hover:from-sky-500 hover:via-blue-600 hover:to-teal-500 hover:text-white hover:ring-transparent dark:bg-white/15 dark:text-sky-300 dark:ring-white/20 dark:hover:bg-gradient-to-br dark:hover:from-sky-500 dark:hover:to-teal-500 dark:hover:text-white'
                            )}
                        >
                            <Sparkles className="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </header>

            {/* ═══════════════════════════════════════════════════════════════════════
                LOWER FUSED LIQUID-GLASS ROW:
                [Left Rail] + [Floating Inset Rounded-24px Glass Canvas] + [Right AI]
                Workspace canvas now floats cleanly with breathing margin on bottom
                AND right edges so the center content area stands out distinctly!
            ═══════════════════════════════════════════════════════════════════════ */}
            <div className="flex flex-1 min-h-0 lg:overflow-hidden">
                {/* Borderless Left Sidebar Rail */}
                <aside
                    className={cn(
                        'hidden lg:flex lg:flex-col shrink-0 h-full transition-all duration-200',
                        sidebarCollapsed ? 'w-[84px]' : 'w-[258px]'
                    )}
                >
                    {renderSidebarNav(sidebarCollapsed, true)}
                </aside>

                {/* Mobile Navigation Drawer */}
                <AnimatePresence>
                    {mobileDrawer && (
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed inset-0 z-50 bg-slate-950/55 backdrop-blur-sm lg:hidden"
                            onClick={() => setMobileDrawer(false)}
                        >
                            <motion.aside
                                initial={{ x: -290 }}
                                animate={{ x: 0 }}
                                exit={{ x: -290 }}
                                transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                                className="h-full w-[274px] bg-white/90 backdrop-blur-3xl shadow-2xl ring-1 ring-slate-900/10 dark:bg-[#071124]/95 dark:ring-white/15"
                                onClick={(e) => e.stopPropagation()}
                            >
                                <div className="flex h-[68px] items-center justify-between px-5">
                                    <BrandLogo inverted={isDark} size="h-8 w-8" />
                                    <button
                                        type="button"
                                        onClick={() => setMobileDrawer(false)}
                                        aria-label="Close navigation menu"
                                        className="flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/5 text-slate-700 hover:bg-slate-900/10 dark:bg-white/10 dark:text-white dark:hover:bg-white/20"
                                    >
                                        <X className="h-4 w-4" />
                                    </button>
                                </div>
                                <div className="h-[calc(100%-68px)]">{renderSidebarNav(false, false)}</div>
                            </motion.aside>
                        </motion.div>
                    )}
                </AnimatePresence>

                {/* Framed Floating Center Workspace Sheet — Breathing Space on Bottom & Right */}
                <div
                    className={cn(
                        'flex flex-1 min-w-0 lg:h-full px-2.5 pb-3 sm:px-3.5 sm:pb-3.5 lg:pl-0 lg:pb-4 transition-[padding] duration-200',
                        aiOpen ? 'lg:pr-0' : 'lg:pr-4'
                    )}
                >
                    <div
                        className={cn(
                            'relative flex-1 min-w-0 lg:h-full vl-workspace-scroll rounded-[24px] p-4 sm:p-6 lg:p-7',
                            'bg-white/60 backdrop-blur-2xl ring-1 ring-inset ring-white/80 shadow-[0_24px_60px_-24px_rgba(2,12,27,0.18),inset_0_1px_0_rgba(255,255,255,0.9)]',
                            'dark:bg-[#050A17]/60 dark:ring-white/10 dark:shadow-[0_28px_70px_-24px_rgba(0,0,0,0.75),inset_0_1px_0_rgba(255,255,255,0.12)]'
                        )}
                    >
                        <motion.main
                            key={url.split('?')[0]}
                            initial={{ opacity: 0, y: 6 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                            className="relative z-10 min-w-0"
                        >
                            <div className="mx-auto max-w-[1360px] pb-4">{children}</div>
                        </motion.main>
                    </div>
                </div>

                {/* Persistent Header-Joined Right Vision AI Sidebar */}
                <PortalAiDrawer user={user} pageTitle={resolvedTitle} pageUrl={url} />
            </div>
        </div>
    );
}

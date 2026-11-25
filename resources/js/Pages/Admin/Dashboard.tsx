import React, { Suspense, lazy, useEffect, useMemo, useState } from 'react';
import { Link } from '@inertiajs/react';
import { AnimatePresence, motion } from 'motion/react';
import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import {
    Activity,
    ArrowRight,
    ArrowUpRight,
    BookOpen,
    Box,
    Cpu,
    GitPullRequestDraft,
    LayoutGrid,
    ShieldCheck,
    Users,
} from 'lucide-react';
import { cn } from '@/lib/cn';
import { useTheme } from '@/components/ThemeProvider';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

const LabRoom3DStage = lazy(() => import('@/components/three/LabRoom3DStage'));

interface AdminDashboardProps {
    stats: {
        total_users: number;
        total_admins: number;
        total_instructors: number;
        total_students: number;
        suspended_users: number;
        total_courses: number;
        active_courses: number;
        total_submissions: number;
        pending_grading: number;
        graded_today: number;
        ai_actions_today: number;
        ai_actions_total: number;
        ai_patches_pending: number;
        ai_patches_total: number;
        active_workspaces: number;
        total_workspaces: number;
        extensions_active: number;
        extensions_total: number;
    };
    recentUsers: Array<{ id: number; name: string; email: string; role: string; status: string }>;
    recentAuditLogs: Array<{ id: number; action: string; resource_type: string; created_at: string; actor?: { name: string } | null }>;
    signupTrend: Record<string, number>;
    aiActionsTrend: { labels: string[]; counts: number[] };
}

function buildSparkline(base: number, pattern: number[]) {
    const floor = Math.max(base, 4);
    return pattern.map((factor, i) => ({
        idx: i,
        v: Math.max(1, Math.round(floor * factor)),
    }));
}

export default function AdminDashboard({
    stats,
    recentUsers,
    recentAuditLogs,
    signupTrend,
    aiActionsTrend,
}: AdminDashboardProps) {
    const { isDark } = useTheme();
    const [viewMode, setViewMode] = useState<'bento' | 'lab3d'>('bento');
    const [healthChecks, setHealthChecks] = useState<Record<string, boolean> | null>(null);
    const [healthLoading, setHealthLoading] = useState(true);

    useEffect(() => {
        let active = true;
        fetch('/healthz', { headers: { Accept: 'application/json' } })
            .then((r) => r.json())
            .then((data) => {
                if (!active || !data?.checks) return;
                const filtered = Object.fromEntries(
                    Object.entries(data.checks as Record<string, boolean>).filter(([k]) => k !== 'jitsi_config')
                );
                setHealthChecks(filtered);
            })
            .catch(() => {})
            .finally(() => {
                if (active) setHealthLoading(false);
            });
        return () => {
            active = false;
        };
    }, []);

    const signupData = useMemo(
        () =>
            Object.entries(signupTrend).map(([date, count]) => ({
                day: date.slice(5),
                signups: count,
            })),
        [signupTrend]
    );

    const aiData = useMemo(
        () =>
            aiActionsTrend.labels.map((label, idx) => ({
                month: label,
                actions: aiActionsTrend.counts[idx] ?? 0,
            })),
        [aiActionsTrend]
    );

    const roleDonutData = useMemo(() => {
        const rows = [
            { name: 'Students', value: stats.total_students, color: '#38BDF8' },
            { name: 'Faculty', value: stats.total_instructors, color: '#2DD4BF' },
            { name: 'Admins', value: stats.total_admins, color: '#FBBF24' },
        ];
        const total = rows.reduce((sum, r) => sum + r.value, 0);
        return total > 0 ? rows : [{ name: 'Empty', value: 1, color: '#334155' }];
    }, [stats.total_students, stats.total_instructors, stats.total_admins]);

    const sparkCards = useMemo(
        () => [
            {
                id: 'users',
                label: 'Campus Accounts',
                value: stats.total_users,
                chip: `${stats.total_students} students`,
                sub: `${stats.total_instructors} faculty · ${stats.suspended_users} suspended`,
                icon: Users,
                stroke: '#38BDF8',
                gradId: 'admSparkSky',
                chipTone: 'bg-sky-500/15 text-sky-600 dark:text-sky-300',
                iconTone: 'bg-sky-500/15 text-sky-500 dark:text-sky-300',
                series: buildSparkline(stats.total_users, [0.72, 0.78, 0.81, 0.86, 0.92, 0.96, 1]),
            },
            {
                id: 'containers',
                label: 'Active Containers',
                value: stats.active_workspaces,
                chip: `${stats.total_workspaces} total`,
                sub: `${stats.extensions_active}/${stats.extensions_total} VSIX verified`,
                icon: Cpu,
                stroke: '#2DD4BF',
                gradId: 'admSparkTeal',
                chipTone: 'bg-teal-500/15 text-teal-600 dark:text-teal-300',
                iconTone: 'bg-teal-500/15 text-teal-500 dark:text-teal-300',
                series: buildSparkline(stats.active_workspaces, [0.55, 0.74, 0.68, 0.91, 0.84, 0.95, 1]),
            },
            {
                id: 'patches',
                label: 'AI Patch Queue',
                value: stats.ai_patches_pending,
                chip: `${stats.ai_actions_today} today`,
                sub: `${stats.ai_patches_total} audited patches total`,
                icon: GitPullRequestDraft,
                stroke: '#FBBF24',
                gradId: 'admSparkAmber',
                chipTone: 'bg-amber-500/15 text-amber-600 dark:text-amber-300',
                iconTone: 'bg-amber-500/15 text-amber-500 dark:text-amber-300',
                series: buildSparkline(stats.ai_patches_pending + 2, [0.9, 0.7, 0.82, 0.64, 0.75, 0.68, 0.8]),
            },
            {
                id: 'courses',
                label: 'Active Courses',
                value: stats.active_courses,
                chip: `${stats.graded_today} graded`,
                sub: `${stats.pending_grading} submissions awaiting grade`,
                icon: BookOpen,
                stroke: '#0EA5E9',
                gradId: 'admSparkBlue',
                chipTone: 'bg-sky-500/15 text-sky-600 dark:text-sky-300',
                iconTone: 'bg-sky-500/15 text-sky-500 dark:text-sky-300',
                series: buildSparkline(stats.active_courses, [0.65, 0.72, 0.72, 0.85, 0.9, 0.94, 1]),
            },
        ],
        [stats]
    );

    const gridColor = isDark ? 'rgba(148,163,184,0.12)' : 'rgba(15,23,42,0.08)';
    const tickColor = isDark ? '#94A3B8' : '#64748B';
    const tooltipStyle = {
        backgroundColor: isDark ? '#080F1D' : '#FFFFFF',
        border: isDark ? '1px solid rgba(255,255,255,0.12)' : '1px solid rgba(15,23,42,0.1)',
        borderRadius: '18px',
        fontSize: '12px',
        padding: '8px 14px',
    };

    const allHealthy = healthChecks ? Object.values(healthChecks).every(Boolean) : false;
    const fleetUtilization =
        stats.total_workspaces > 0
            ? Math.min(100, Math.round((stats.active_workspaces / stats.total_workspaces) * 100))
            : 0;

    return (
        <PortalLayout title="Admin Command Center" pageTitle="Command Center" adminMode>
            <div className="space-y-6">
                {/* Header & Full-Pill View Switcher (Bento Overview vs 3D Live Lab Floor) */}
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div className="space-y-1">
                        <h2 className="font-display text-2xl font-semibold tracking-[-0.028em] text-vl-ink sm:text-3xl">
                            Campus Operations &amp; Telemetry
                        </h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Inspect live cluster health, container fleet density, audited AI operations, and spatial lab seats.
                        </p>
                    </div>

                    <div className="inline-flex w-fit items-center gap-1 rounded-full bg-vl-surface/90 p-1.5 ring-1 ring-inset ring-vl-border/15 shadow-sm">
                        {[
                            { id: 'bento' as const, label: 'Bento Overview', icon: LayoutGrid },
                            { id: 'lab3d' as const, label: '3D Live Lab Floor', icon: Box },
                        ].map((tab) => {
                            const Icon = tab.icon;
                            const active = viewMode === tab.id;
                            return (
                                <button
                                    key={tab.id}
                                    type="button"
                                    onClick={() => setViewMode(tab.id)}
                                    className={cn(
                                        'relative inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold transition-colors',
                                        active ? 'text-white' : 'text-vl-ink-secondary hover:text-vl-ink'
                                    )}
                                >
                                    {active && (
                                        <motion.span
                                            layoutId="admin-view-pill"
                                            transition={{ type: 'spring', stiffness: 360, damping: 30 }}
                                            className="absolute inset-0 rounded-full bg-gradient-to-r from-sky-500 to-blue-600 shadow-md"
                                        />
                                    )}
                                    <Icon className="relative z-10 h-3.5 w-3.5" />
                                    <span className="relative z-10">{tab.label}</span>
                                </button>
                            );
                        })}
                    </div>
                </div>

                {/* Live /healthz Operational Probe Pill Bar */}
                <SpotlightSurface accent={allHealthy ? 'teal' : 'amber'} className="!rounded-[28px] px-5 py-4">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div className="flex flex-wrap items-center gap-3">
                            <span className="flex h-8 w-8 items-center justify-center rounded-full bg-sky-500/15 text-sky-400">
                                <Activity className="h-4 w-4" />
                            </span>
                            <span className="font-display text-sm font-semibold text-vl-ink">
                                Live Cluster Telemetry
                            </span>
                            {!healthLoading && healthChecks && (
                                <StatusPill tone={allHealthy ? 'teal' : 'amber'} pulse={allHealthy}>
                                    {allHealthy ? 'All Active Probes Operational' : 'Degraded Probe Detected'}
                                </StatusPill>
                            )}
                        </div>

                        <div className="flex flex-wrap items-center gap-2">
                            {healthChecks &&
                                Object.entries(healthChecks).map(([key, ok]) => (
                                    <span
                                        key={key}
                                        className={cn(
                                            'inline-flex items-center gap-1.5 rounded-full px-3 py-1 font-mono text-[11px]',
                                            ok
                                                ? 'bg-teal-500/15 text-teal-600 dark:text-teal-300'
                                                : 'bg-rose-500/15 text-rose-500 dark:text-rose-300'
                                        )}
                                    >
                                        <span className={cn('h-1.5 w-1.5 rounded-full', ok ? 'bg-teal-400' : 'bg-rose-400')} />
                                        {key}
                                    </span>
                                ))}
                            <Link
                                href="/admin/status"
                                prefetch="hover"
                                className="inline-flex items-center gap-1 rounded-full bg-vl-surface-raised px-3.5 py-1.5 text-xs font-medium text-sky-500 hover:bg-sky-500/15 transition-colors"
                            >
                                Diagnostics <ArrowRight className="h-3.5 w-3.5" />
                            </Link>
                        </div>
                    </div>
                </SpotlightSurface>

                <AnimatePresence mode="wait">
                    {viewMode === 'lab3d' ? (
                        <motion.div
                            key="admin-lab3d"
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -8 }}
                            transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                            className="space-y-5"
                        >
                            <Suspense
                                fallback={
                                    <div className="flex h-[580px] items-center justify-center rounded-[36px] bg-vl-surface text-sm text-vl-ink-muted">
                                        Initializing Interactive 3D Lab Floor...
                                    </div>
                                }
                            >
                                <LabRoom3DStage isDark={isDark} />
                            </Suspense>
                        </motion.div>
                    ) : (
                        <motion.div
                            key="admin-bento"
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -8 }}
                            transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                            className="space-y-6"
                        >
                            {/* Row 1 — V28 4 Sparkline KPI Bento Cards */}
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                {sparkCards.map((kpi, idx) => {
                                    const Icon = kpi.icon;
                                    return (
                                        <motion.div
                                            key={kpi.id}
                                            initial={{ opacity: 0, y: 12 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            transition={{ duration: 0.24, delay: idx * 0.04, ease: [0.16, 1, 0.3, 1] }}
                                        >
                                            <SpotlightSurface className="!rounded-[30px] p-5">
                                                <div className="flex items-center justify-between gap-2">
                                                    <div className="flex items-center gap-2.5">
                                                        <span className={cn('flex h-9 w-9 items-center justify-center rounded-full', kpi.iconTone)}>
                                                            <Icon className="h-4 w-4" />
                                                        </span>
                                                        <span className="text-xs font-medium text-vl-ink-secondary">{kpi.label}</span>
                                                    </div>
                                                    <span className={cn('rounded-full px-2.5 py-0.5 font-mono text-[10px] font-semibold tabular-nums', kpi.chipTone)}>
                                                        {kpi.chip}
                                                    </span>
                                                </div>

                                                <div className="mt-4 flex items-end justify-between gap-3">
                                                    <div>
                                                        <p className="font-display text-3xl font-semibold tracking-tight text-vl-ink tabular-nums">
                                                            {kpi.value.toLocaleString()}
                                                        </p>
                                                        <p className="mt-1 text-[11px] text-vl-ink-muted">{kpi.sub}</p>
                                                    </div>

                                                    <div className="h-12 w-24 shrink-0">
                                                        <ResponsiveContainer width="100%" height="100%">
                                                            <AreaChart data={kpi.series}>
                                                                <defs>
                                                                    <linearGradient id={kpi.gradId} x1="0" y1="0" x2="0" y2="1">
                                                                        <stop offset="5%" stopColor={kpi.stroke} stopOpacity={0.35} />
                                                                        <stop offset="95%" stopColor={kpi.stroke} stopOpacity={0} />
                                                                    </linearGradient>
                                                                </defs>
                                                                <Area
                                                                    type="monotone"
                                                                    dataKey="v"
                                                                    stroke={kpi.stroke}
                                                                    strokeWidth={2}
                                                                    fill={`url(#${kpi.gradId})`}
                                                                    isAnimationActive={false}
                                                                />
                                                            </AreaChart>
                                                        </ResponsiveContainer>
                                                    </div>
                                                </div>
                                            </SpotlightSurface>
                                        </motion.div>
                                    );
                                })}
                            </div>

                            {/* Row 2 — V21 Mixed-Contrast Hero Bento (Sapphire Gradient + Obsidian Contrast + Circular Donut) */}
                            <div className="grid grid-cols-1 gap-5 lg:grid-cols-12">
                                {/* 5-Col Vibrant Sapphire-Teal Gradient Feature Card */}
                                <div className="relative overflow-hidden rounded-[34px] bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-7 text-white shadow-[0_24px_60px_-20px_rgba(14,165,233,0.55)] lg:col-span-5">
                                    <div
                                        aria-hidden="true"
                                        className="pointer-events-none absolute -top-16 -right-12 h-48 w-48 rounded-full bg-white/15 blur-2xl"
                                    />
                                    <div className="relative z-10 flex h-full flex-col justify-between gap-6">
                                        <div className="flex items-start justify-between gap-3">
                                            <div>
                                                <p className="font-mono text-[11px] text-sky-100/90">
                                                    CONTAINER FLEET ENGINE
                                                </p>
                                                <h3 className="mt-1 font-display text-2xl font-semibold tracking-tight">
                                                    {stats.active_workspaces} live seats running
                                                </h3>
                                            </div>
                                            <span className="rounded-full bg-white/20 px-3 py-1 font-mono text-xs font-semibold tabular-nums backdrop-blur-md">
                                                {fleetUtilization}% active
                                            </span>
                                        </div>

                                        <div className="space-y-2.5">
                                            <div className="h-2.5 w-full overflow-hidden rounded-full bg-white/20">
                                                <div
                                                    className="h-full rounded-full bg-white transition-all duration-500"
                                                    style={{ width: `${Math.max(fleetUtilization, 8)}%` }}
                                                />
                                            </div>
                                            <div className="flex items-center justify-between text-xs text-sky-100/90 tabular-nums">
                                                <span>{stats.total_workspaces} total provisioned containers</span>
                                                <span>{stats.extensions_active} VSIX active</span>
                                            </div>
                                        </div>

                                        <div className="flex flex-wrap items-center gap-2.5">
                                            <Link
                                                href="/admin/workspaces"
                                                prefetch="hover"
                                                className="inline-flex items-center gap-1.5 rounded-full bg-white px-4 py-2 text-xs font-semibold text-blue-700 hover:bg-sky-50 transition-colors"
                                            >
                                                Inspect Fleet
                                                <ArrowUpRight className="h-3.5 w-3.5" />
                                            </Link>
                                            <button
                                                type="button"
                                                onClick={() => setViewMode('lab3d')}
                                                className="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-4 py-2 text-xs font-semibold text-white backdrop-blur-md hover:bg-white/25 transition-colors"
                                            >
                                                <Box className="h-3.5 w-3.5" />
                                                Open 3D Lab Floor
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {/* 4-Col High-Contrast Obsidian Governance Card */}
                                <div className="relative overflow-hidden rounded-[34px] bg-[#070C16] p-7 text-slate-100 shadow-[0_24px_60px_-24px_rgba(0,0,0,0.65)] ring-1 ring-white/10 lg:col-span-4">
                                    <div className="flex h-full flex-col justify-between gap-6">
                                        <div className="flex items-start justify-between gap-3">
                                            <div>
                                                <p className="font-mono text-[11px] text-amber-300">
                                                    AI PATCH GOVERNANCE
                                                </p>
                                                <h3 className="mt-1 font-display text-2xl font-semibold text-white tabular-nums">
                                                    {stats.ai_patches_pending} pending review
                                                </h3>
                                            </div>
                                            <span className="flex h-10 w-10 items-center justify-center rounded-full bg-amber-500/15 text-amber-300">
                                                <ShieldCheck className="h-5 w-5" />
                                            </span>
                                        </div>

                                        <div className="grid grid-cols-2 gap-3 rounded-[24px] bg-white/5 p-4 font-mono text-xs tabular-nums">
                                            <div>
                                                <p className="text-[10px] text-slate-400">AI ACTIONS TODAY</p>
                                                <p className="mt-1 text-lg font-semibold text-sky-300">
                                                    {stats.ai_actions_today}
                                                </p>
                                            </div>
                                            <div>
                                                <p className="text-[10px] text-slate-400">ALL-TIME AUDITED</p>
                                                <p className="mt-1 text-lg font-semibold text-teal-300">
                                                    {stats.ai_actions_total}
                                                </p>
                                            </div>
                                        </div>

                                        <div className="flex items-center justify-between text-xs">
                                            <span className="text-slate-400">Direct agent write policy: HTTP 403</span>
                                            <a
                                                href="/admin/audits"
                                                className="inline-flex items-center gap-1 rounded-full bg-white/10 px-3.5 py-1.5 font-medium text-white hover:bg-white/15 transition-colors"
                                            >
                                                Audit Log <ArrowRight className="h-3 w-3" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {/* 3-Col Circular Donut Role Breakdown Card (V21/V28 DNA) */}
                                <SpotlightSurface className="!rounded-[34px] p-6 lg:col-span-3">
                                    <div className="flex items-center justify-between">
                                        <h3 className="font-display text-base font-semibold text-vl-ink">Role Split</h3>
                                        <span className="rounded-full bg-vl-surface-raised px-2.5 py-0.5 font-mono text-[10px] text-vl-ink-muted tabular-nums">
                                            {stats.total_users} total
                                        </span>
                                    </div>

                                    <div className="relative mt-2 h-40 w-full">
                                        <ResponsiveContainer width="100%" height="100%">
                                            <PieChart>
                                                <Pie
                                                    data={roleDonutData}
                                                    dataKey="value"
                                                    nameKey="name"
                                                    cx="50%"
                                                    cy="50%"
                                                    innerRadius={44}
                                                    outerRadius={64}
                                                    paddingAngle={4}
                                                    cornerRadius={8}
                                                    stroke="none"
                                                >
                                                    {roleDonutData.map((entry) => (
                                                        <Cell key={entry.name} fill={entry.color} />
                                                    ))}
                                                </Pie>
                                                <Tooltip contentStyle={tooltipStyle} />
                                            </PieChart>
                                        </ResponsiveContainer>
                                        <div className="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                                            <span className="font-display text-xl font-semibold text-vl-ink tabular-nums">
                                                {stats.total_users}
                                            </span>
                                            <span className="text-[10px] text-vl-ink-muted">Seats</span>
                                        </div>
                                    </div>

                                    <div className="mt-2 flex flex-wrap justify-center gap-2 text-[11px] font-medium">
                                        {[
                                            { label: 'Students', count: stats.total_students, dot: 'bg-sky-400' },
                                            { label: 'Faculty', count: stats.total_instructors, dot: 'bg-teal-400' },
                                            { label: 'Admins', count: stats.total_admins, dot: 'bg-amber-400' },
                                        ].map((item) => (
                                            <span
                                                key={item.label}
                                                className="inline-flex items-center gap-1.5 rounded-full bg-vl-bg/75 px-2.5 py-1 text-vl-ink-secondary tabular-nums"
                                            >
                                                <span className={cn('h-2 w-2 rounded-full', item.dot)} />
                                                {item.label}: {item.count}
                                            </span>
                                        ))}
                                    </div>
                                </SpotlightSurface>
                            </div>

                            {/* Row 3 — Wavy Gradient Area Chart + Rounded Capsule Bar Chart (V28 DNA) */}
                            <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                                <SpotlightSurface accent="sky" className="!rounded-[34px] p-6 lg:col-span-7">
                                    <div className="mb-5 flex items-center justify-between">
                                        <div>
                                            <h3 className="font-display text-base font-semibold text-vl-ink">
                                                7-Day Account Provisioning
                                            </h3>
                                            <p className="text-xs text-vl-ink-muted">Live institutional onboarding velocity</p>
                                        </div>
                                        <span className="rounded-full bg-sky-500/15 px-3 py-1 font-mono text-[11px] text-sky-500 dark:text-sky-300">
                                            Last 7 Days
                                        </span>
                                    </div>
                                    <div className="h-64 w-full">
                                        <ResponsiveContainer width="100%" height="100%">
                                            <AreaChart data={signupData}>
                                                <defs>
                                                    <linearGradient id="vlSignupFill" x1="0" y1="0" x2="0" y2="1">
                                                        <stop offset="5%" stopColor="#38BDF8" stopOpacity={0.4} />
                                                        <stop offset="95%" stopColor="#38BDF8" stopOpacity={0} />
                                                    </linearGradient>
                                                </defs>
                                                <CartesianGrid strokeDasharray="3 3" stroke={gridColor} vertical={false} />
                                                <XAxis dataKey="day" stroke={tickColor} fontSize={11} tickLine={false} axisLine={false} />
                                                <YAxis stroke={tickColor} fontSize={11} tickLine={false} axisLine={false} allowDecimals={false} />
                                                <Tooltip contentStyle={tooltipStyle} />
                                                <Area
                                                    type="monotone"
                                                    dataKey="signups"
                                                    stroke="#38BDF8"
                                                    strokeWidth={2.5}
                                                    fill="url(#vlSignupFill)"
                                                />
                                            </AreaChart>
                                        </ResponsiveContainer>
                                    </div>
                                </SpotlightSurface>

                                <SpotlightSurface accent="teal" className="!rounded-[34px] p-6 lg:col-span-5">
                                    <div className="mb-5 flex items-center justify-between">
                                        <div>
                                            <h3 className="font-display text-base font-semibold text-vl-ink">
                                                6-Month Audited AI Operations
                                            </h3>
                                            <p className="text-xs text-vl-ink-muted">Capsule breakdown of logged patch events</p>
                                        </div>
                                        <span className="rounded-full bg-teal-500/15 px-3 py-1 font-mono text-[11px] text-teal-600 dark:text-teal-300">
                                            6 Months
                                        </span>
                                    </div>
                                    <div className="h-64 w-full">
                                        <ResponsiveContainer width="100%" height="100%">
                                            <BarChart data={aiData} barSize={24}>
                                                <defs>
                                                    <linearGradient id="vlCapsuleBar" x1="0" y1="0" x2="0" y2="1">
                                                        <stop offset="0%" stopColor="#2DD4BF" />
                                                        <stop offset="100%" stopColor="#0284C7" />
                                                    </linearGradient>
                                                </defs>
                                                <CartesianGrid strokeDasharray="3 3" stroke={gridColor} vertical={false} />
                                                <XAxis dataKey="month" stroke={tickColor} fontSize={11} tickLine={false} axisLine={false} />
                                                <YAxis stroke={tickColor} fontSize={11} tickLine={false} axisLine={false} allowDecimals={false} />
                                                <Tooltip contentStyle={tooltipStyle} />
                                                <Bar dataKey="actions" fill="url(#vlCapsuleBar)" radius={[99, 99, 99, 99]} />
                                            </BarChart>
                                        </ResponsiveContainer>
                                    </div>
                                </SpotlightSurface>
                            </div>

                            {/* Row 4 — Live Provisioned Accounts & Governance Audit Stream */}
                            <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                                <SpotlightSurface className="!rounded-[34px] p-6 lg:col-span-6">
                                    <div className="mb-4 flex items-center justify-between">
                                        <h3 className="font-display text-base font-semibold text-vl-ink">
                                            Recently Provisioned Accounts
                                        </h3>
                                        <Link
                                            href="/admin/users"
                                            prefetch="hover"
                                            className="rounded-full bg-vl-surface-raised px-3.5 py-1 text-xs font-medium text-sky-500 hover:bg-sky-500/15 transition-colors"
                                        >
                                            Manage all
                                        </Link>
                                    </div>
                                    <div className="space-y-2.5">
                                        {recentUsers.map((u) => (
                                            <div
                                                key={u.id}
                                                className="flex items-center justify-between gap-3 rounded-full bg-vl-bg/75 px-4 py-2.5"
                                            >
                                                <div className="flex items-center gap-3 min-w-0">
                                                    <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-teal-500 text-[11px] font-bold text-white">
                                                        {u.name
                                                            .split(' ')
                                                            .slice(0, 2)
                                                            .map((n) => n[0])
                                                            .join('')
                                                            .toUpperCase()}
                                                    </span>
                                                    <div className="min-w-0">
                                                        <p className="truncate text-sm font-medium text-vl-ink">{u.name}</p>
                                                        <p className="truncate text-xs text-vl-ink-muted">{u.email}</p>
                                                    </div>
                                                </div>
                                                <StatusPill tone={u.status === 'suspended' ? 'rose' : 'sky'}>
                                                    {u.role}
                                                </StatusPill>
                                            </div>
                                        ))}
                                    </div>
                                </SpotlightSurface>

                                <SpotlightSurface className="!rounded-[34px] p-6 lg:col-span-6">
                                    <div className="mb-4 flex items-center justify-between">
                                        <h3 className="font-display text-base font-semibold text-vl-ink">
                                            Recent Governance Audit
                                        </h3>
                                        <span className="flex h-8 w-8 items-center justify-center rounded-full bg-teal-500/15 text-teal-400">
                                            <ShieldCheck className="h-4 w-4" />
                                        </span>
                                    </div>
                                    <div className="space-y-2.5">
                                        {recentAuditLogs.length === 0 ? (
                                            <p className="py-8 text-center text-xs text-vl-ink-muted">
                                                No audit events recorded yet.
                                            </p>
                                        ) : (
                                            recentAuditLogs.slice(0, 6).map((log) => (
                                                <div
                                                    key={log.id}
                                                    className="flex items-center justify-between gap-3 rounded-full bg-vl-bg/75 px-4 py-2.5"
                                                >
                                                    <div className="min-w-0">
                                                        <p className="truncate font-mono text-xs font-semibold text-vl-ink">
                                                            {log.action}
                                                        </p>
                                                        <p className="truncate text-[11px] text-vl-ink-muted">
                                                            {log.actor?.name ?? 'System'} · {log.resource_type}
                                                        </p>
                                                    </div>
                                                    <span className="shrink-0 rounded-full bg-vl-surface px-2.5 py-1 font-mono text-[10px] text-vl-ink-muted tabular-nums">
                                                        {new Date(log.created_at).toLocaleDateString()}
                                                    </span>
                                                </div>
                                            ))
                                        )}
                                    </div>
                                </SpotlightSurface>
                            </div>
                        </motion.div>
                    )}
                </AnimatePresence>
            </div>
        </PortalLayout>
    );
}

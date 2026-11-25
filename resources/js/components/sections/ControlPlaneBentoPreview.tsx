import React, { useState } from 'react';
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
} from 'recharts';
import {
    Activity,
    ArrowUpRight,
    BookOpen,
    CheckSquare,
    Cpu,
    GitPullRequestDraft,
    GraduationCap,
    LayoutDashboard,
    Plus,
    ShieldCheck,
    Terminal,
    Users,
} from 'lucide-react';
import { cn } from '@/lib/cn';
import { useTheme } from '@/components/ThemeProvider';
import { StatusPill } from '@/components/ui/Primitives';

type PreviewRole = 'admin' | 'faculty' | 'student';

interface RolePreviewData {
    id: PreviewRole;
    tabLabel: string;
    portalTitle: string;
    sidebarCta: string;
    heroTitle: string;
    heroMetric: string;
    heroChip: string;
    heroFootLeft: string;
    heroFootRight: string;
    heroPct: number;
    obsidianTitle: string;
    obsidianMetric: string;
    obsidianLeftLabel: string;
    obsidianLeftVal: string;
    obsidianRightLabel: string;
    obsidianRightVal: string;
    obsidianNote: string;
    donutTitle: string;
    donutCenterVal: string;
    donutCenterSub: string;
    donutSlices: Array<{ name: string; value: number; color: string }>;
    barTitle: string;
    barSub: string;
    barData: Array<{ label: string; value: number }>;
    kpis: Array<{
        label: string;
        value: string;
        chip: string;
        stroke: string;
        series: Array<{ i: number; v: number }>;
    }>;
}

const PREVIEW_PROFILES: Record<PreviewRole, RolePreviewData> = {
    admin: {
        id: 'admin',
        tabLabel: 'Admin Command',
        portalTitle: 'Command Center',
        sidebarCta: 'Provision Seat',
        heroTitle: 'Live Container Fleet',
        heroMetric: '148 Active Seats',
        heroChip: '86% capacity',
        heroFootLeft: '172 provisioned containers',
        heroFootRight: '24 signed VSIX active',
        heroPct: 86,
        obsidianTitle: 'AI Patch Governance',
        obsidianMetric: '19 Staged Diffs',
        obsidianLeftLabel: 'AUDITED TODAY',
        obsidianLeftVal: '312',
        obsidianRightLabel: 'POLICY VIOLATIONS',
        obsidianRightVal: '0',
        obsidianNote: 'Direct agent write requests are blocked with HTTP 403.',
        donutTitle: 'Role Split',
        donutCenterVal: '642',
        donutCenterSub: 'Seats',
        donutSlices: [
            { name: 'Students', value: 584, color: '#38BDF8' },
            { name: 'Faculty', value: 48, color: '#2DD4BF' },
            { name: 'Admins', value: 10, color: '#FBBF24' },
        ],
        barTitle: 'Audited AI Patch Volume',
        barSub: 'Capsule view across academic weeks',
        barData: [
            { label: 'W1', value: 44 },
            { label: 'W2', value: 62 },
            { label: 'W3', value: 58 },
            { label: 'W4', value: 84 },
            { label: 'W5', value: 76 },
            { label: 'W6', value: 94 },
        ],
        kpis: [
            {
                label: 'Campus Accounts',
                value: '642',
                chip: '+18 wk',
                stroke: '#38BDF8',
                series: [{ i: 0, v: 42 }, { i: 1, v: 48 }, { i: 2, v: 53 }, { i: 3, v: 59 }, { i: 4, v: 64 }],
            },
            {
                label: 'Active Containers',
                value: '148',
                chip: 'crun ok',
                stroke: '#2DD4BF',
                series: [{ i: 0, v: 90 }, { i: 1, v: 120 }, { i: 2, v: 112 }, { i: 3, v: 138 }, { i: 4, v: 148 }],
            },
            {
                label: 'Pending Patches',
                value: '19',
                chip: 'HITL',
                stroke: '#FBBF24',
                series: [{ i: 0, v: 32 }, { i: 1, v: 26 }, { i: 2, v: 28 }, { i: 3, v: 21 }, { i: 4, v: 19 }],
            },
        ],
    },
    faculty: {
        id: 'faculty',
        tabLabel: 'Faculty Studio',
        portalTitle: 'CS-301 Grading Studio',
        sidebarCta: 'Create Course',
        heroTitle: 'Active Course Sections',
        heroMetric: '4 Lab Cohorts',
        heroChip: '92% synced',
        heroFootLeft: '126 enrolled engineering students',
        heroFootRight: 'Pinned Nix GCC 13 shell',
        heroPct: 92,
        obsidianTitle: 'VisionGuard Forensics',
        obsidianMetric: '88.4% Avg Human',
        obsidianLeftLabel: 'QUEUED TO GRADE',
        obsidianLeftVal: '14',
        obsidianRightLabel: 'EXAM LOCKED',
        obsidianRightVal: '32',
        obsidianNote: 'Keystroke cadence & approved AI diffs attached to every turn-in.',
        donutTitle: 'Grading Mix',
        donutCenterVal: '126',
        donutCenterSub: 'Turn-ins',
        donutSlices: [
            { name: 'Graded', value: 104, color: '#2DD4BF' },
            { name: 'Queued', value: 14, color: '#38BDF8' },
            { name: 'Late', value: 8, color: '#FBBF24' },
        ],
        barTitle: 'Weekly Lab Submissions',
        barSub: 'Completed allocator & kernel assignments',
        barData: [
            { label: 'L1', value: 118 },
            { label: 'L2', value: 122 },
            { label: 'L3', value: 115 },
            { label: 'L4', value: 124 },
            { label: 'L5', value: 119 },
            { label: 'L6', value: 126 },
        ],
        kpis: [
            {
                label: 'Pending Grading',
                value: '14',
                chip: '2 urgent',
                stroke: '#FBBF24',
                series: [{ i: 0, v: 28 }, { i: 1, v: 24 }, { i: 2, v: 19 }, { i: 3, v: 16 }, { i: 4, v: 14 }],
            },
            {
                label: 'Live Class Seats',
                value: '36',
                chip: 'Hall 04',
                stroke: '#2DD4BF',
                series: [{ i: 0, v: 18 }, { i: 1, v: 26 }, { i: 2, v: 31 }, { i: 3, v: 34 }, { i: 4, v: 36 }],
            },
            {
                label: 'Cohort Average',
                value: '87.6%',
                chip: '+4.2%',
                stroke: '#38BDF8',
                series: [{ i: 0, v: 79 }, { i: 1, v: 82 }, { i: 2, v: 84 }, { i: 3, v: 86 }, { i: 4, v: 88 }],
            },
        ],
    },
    student: {
        id: 'student',
        tabLabel: 'Student Workspace',
        portalTitle: 'Student Workspace',
        sidebarCta: 'Launch Cloud IDE',
        heroTitle: 'Academic Mastery Track',
        heroMetric: 'Level 7 · Systems',
        heroChip: '14d streak',
        heroFootLeft: '3,420 verified engineering XP',
        heroFootRight: '78% to Level 8',
        heroPct: 78,
        obsidianTitle: 'Running Cloud Containers',
        obsidianMetric: '2 Active IDEs',
        obsidianLeftLabel: 'HUMAN AUTHORSHIP',
        obsidianLeftVal: '91%',
        obsidianRightLabel: 'NEXT DEADLINE',
        obsidianRightVal: '48h',
        obsidianNote: 'Persistent Nix volume mounts your compiler cache instantly.',
        donutTitle: 'Term Progress',
        donutCenterVal: '18',
        donutCenterSub: 'Labs',
        donutSlices: [
            { name: 'Graded', value: 15, color: '#2DD4BF' },
            { name: 'In Progress', value: 3, color: '#FBBF24' },
        ],
        barTitle: 'Focused Coding Sessions',
        barSub: 'Verified container hours over 6 days',
        barData: [
            { label: 'Mon', value: 2.4 },
            { label: 'Tue', value: 3.1 },
            { label: 'Wed', value: 1.8 },
            { label: 'Thu', value: 4.2 },
            { label: 'Fri', value: 3.6 },
            { label: 'Sat', value: 2.9 },
        ],
        kpis: [
            {
                label: 'Active Containers',
                value: '2',
                chip: 'Nix VS Code',
                stroke: '#38BDF8',
                series: [{ i: 0, v: 1 }, { i: 1, v: 1 }, { i: 2, v: 2 }, { i: 3, v: 2 }, { i: 4, v: 2 }],
            },
            {
                label: 'Graded Score Avg',
                value: '94.2%',
                chip: 'Top 10%',
                stroke: '#2DD4BF',
                series: [{ i: 0, v: 86 }, { i: 1, v: 89 }, { i: 2, v: 91 }, { i: 3, v: 93 }, { i: 4, v: 94 }],
            },
            {
                label: 'Daily Lab Streak',
                value: '14d',
                chip: 'Active',
                stroke: '#FBBF24',
                series: [{ i: 0, v: 6 }, { i: 1, v: 8 }, { i: 2, v: 10 }, { i: 3, v: 12 }, { i: 4, v: 14 }],
            },
        ],
    },
};

const SIDEBAR_ITEMS: Record<PreviewRole, Array<{ label: string; icon: React.ComponentType<{ className?: string }>; active?: boolean }>> = {
    admin: [
        { label: 'Command', icon: ShieldCheck, active: true },
        { label: 'Telemetry', icon: Activity },
        { label: 'Users', icon: Users },
        { label: 'Containers', icon: Cpu },
        { label: 'AI Patches', icon: GitPullRequestDraft },
    ],
    faculty: [
        { label: 'Studio', icon: LayoutDashboard, active: true },
        { label: 'Grading', icon: CheckSquare },
        { label: 'Courses', icon: BookOpen },
        { label: '3D Classroom', icon: GraduationCap },
        { label: 'Workspaces', icon: Terminal },
    ],
    student: [
        { label: 'Overview', icon: LayoutDashboard, active: true },
        { label: 'Workspaces', icon: Terminal },
        { label: 'Courses', icon: BookOpen },
        { label: 'AI Diffs', icon: GitPullRequestDraft },
        { label: 'Progress', icon: Activity },
    ],
};

export function ControlPlaneBentoPreview() {
    const { isDark } = useTheme();
    const [role, setRole] = useState<PreviewRole>('admin');
    const data = PREVIEW_PROFILES[role];

    const gridColor = isDark ? 'rgba(148,163,184,0.12)' : 'rgba(15,23,42,0.08)';
    const tickColor = isDark ? '#94A3B8' : '#64748B';
    const tooltipStyle = {
        backgroundColor: isDark ? '#080F1D' : '#FFFFFF',
        border: isDark ? '1px solid rgba(255,255,255,0.12)' : '1px solid rgba(15,23,42,0.1)',
        borderRadius: '16px',
        fontSize: '11px',
        padding: '6px 12px',
    };

    return (
        <div className="space-y-6">
            {/* Floating Pill Control Switcher */}
            <div className="flex flex-wrap items-center justify-between gap-4">
                <div className="inline-flex flex-wrap items-center gap-1 rounded-full bg-vl-surface/90 p-1.5 ring-1 ring-inset ring-vl-border/15 shadow-sm">
                    {(['admin', 'faculty', 'student'] as const).map((r) => {
                        const active = role === r;
                        return (
                            <button
                                key={r}
                                type="button"
                                onClick={() => setRole(r)}
                                className={cn(
                                    'relative rounded-full px-5 py-2 text-xs font-semibold transition-colors',
                                    active ? 'text-white' : 'text-vl-ink-secondary hover:text-vl-ink'
                                )}
                            >
                                {active && (
                                    <motion.span
                                        layoutId="home-bento-preview-tab"
                                        transition={{ type: 'spring', stiffness: 360, damping: 30 }}
                                        className="absolute inset-0 rounded-full bg-gradient-to-r from-sky-500 to-blue-600 shadow-md"
                                    />
                                )}
                                <span className="relative z-10">{PREVIEW_PROFILES[r].tabLabel}</span>
                            </button>
                        );
                    })}
                </div>

                <StatusPill tone="sky" pulse>
                    SYNTHETIC DEMO · LIVE PORTAL ARCHITECTURE
                </StatusPill>
            </div>

            {/* Outer Sculptural Curved Glass Dashboard Frame (V21 + V28 Fusion) */}
            <div className="relative overflow-hidden rounded-[40px] bg-vl-surface/85 p-4 ring-1 ring-vl-border/15 shadow-[0_36px_96px_-30px_rgba(0,0,0,0.6)] backdrop-blur-2xl sm:p-6">
                <div className="grid grid-cols-1 gap-5 lg:grid-cols-12">
                    {/* Left Detached Floating Curved Sidebar Preview */}
                    <div className="hidden lg:col-span-3 lg:flex lg:flex-col justify-between rounded-[32px] bg-vl-bg/85 p-4 ring-1 ring-vl-border/15">
                        <div className="space-y-4">
                            <div className="flex items-center justify-between px-2 pt-1">
                                <span className="font-display text-sm font-semibold text-vl-ink">VisionLab</span>
                                <span className="rounded-full bg-sky-500/15 px-2.5 py-0.5 font-mono text-[10px] font-semibold text-sky-500 dark:text-sky-300 capitalize">
                                    {role}
                                </span>
                            </div>

                            <div className="space-y-1">
                                {SIDEBAR_ITEMS[role].map((item) => {
                                    const Icon = item.icon;
                                    return (
                                        <div
                                            key={item.label}
                                            className={cn(
                                                'flex items-center gap-2.5 rounded-full px-3.5 py-2 text-xs font-medium',
                                                item.active
                                                    ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-sm'
                                                    : 'text-vl-ink-secondary'
                                            )}
                                        >
                                            <Icon className="h-3.5 w-3.5" />
                                            <span>{item.label}</span>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>

                        {/* V21 Sidebar Bottom Gradient Promo Card */}
                        <div className="mt-6 rounded-[26px] bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-4 text-white shadow-lg">
                            <p className="font-display text-xs font-semibold">{data.sidebarCta}</p>
                            <p className="mt-1 text-[11px] leading-snug text-sky-100/90">
                                Instant Nix container &amp; audited AI tutor ready.
                            </p>
                            <span className="mt-3 inline-flex w-full items-center justify-center gap-1 rounded-full bg-white px-3 py-1.5 text-[11px] font-semibold text-blue-700">
                                <Plus className="h-3 w-3" />
                                Quick Launch
                            </span>
                        </div>
                    </div>

                    {/* Right Main Bento Canvas */}
                    <div className="space-y-4 lg:col-span-9">
                        {/* Mini Floating Pill Topbar */}
                        <div className="flex flex-wrap items-center justify-between gap-3 rounded-full bg-vl-bg/85 px-5 py-2.5 ring-1 ring-vl-border/15">
                            <span className="font-display text-sm font-semibold text-vl-ink">
                                {data.portalTitle}
                            </span>
                            <div className="hidden sm:flex items-center gap-1 rounded-full bg-vl-surface p-1 text-[11px]">
                                <span className="rounded-full bg-sky-500 px-3 py-0.5 font-semibold text-white">
                                    Overview
                                </span>
                                <span className="px-3 py-0.5 text-vl-ink-muted">Analytics</span>
                                <span className="px-3 py-0.5 text-vl-ink-muted">3D Floor</span>
                            </div>
                            <span className="rounded-full bg-teal-500/15 px-3 py-1 font-mono text-[10px] font-semibold text-teal-600 dark:text-teal-300">
                                ⌘K Ready
                            </span>
                        </div>

                        <AnimatePresence mode="wait">
                            <motion.div
                                key={role}
                                initial={{ opacity: 0, y: 8 }}
                                animate={{ opacity: 1, y: 0 }}
                                exit={{ opacity: 0, y: -6 }}
                                transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                                className="space-y-4"
                            >
                                {/* Row 1 — 3 Mini Sparkline KPI Cards */}
                                <div className="grid grid-cols-1 gap-3.5 sm:grid-cols-3">
                                    {data.kpis.map((kpi, idx) => (
                                        <div
                                            key={kpi.label}
                                            className="rounded-[26px] bg-vl-bg/85 p-4 ring-1 ring-vl-border/15"
                                        >
                                            <div className="flex items-center justify-between gap-2">
                                                <span className="text-[11px] font-medium text-vl-ink-secondary">
                                                    {kpi.label}
                                                </span>
                                                <span className="rounded-full bg-sky-500/15 px-2 py-0.5 font-mono text-[10px] font-semibold text-sky-600 dark:text-sky-300">
                                                    {kpi.chip}
                                                </span>
                                            </div>
                                            <div className="mt-3 flex items-end justify-between gap-2">
                                                <span className="font-display text-2xl font-semibold text-vl-ink tabular-nums">
                                                    {kpi.value}
                                                </span>
                                                <div className="h-10 w-20">
                                                    <ResponsiveContainer width="100%" height="100%">
                                                        <AreaChart data={kpi.series}>
                                                            <defs>
                                                                <linearGradient id={`prevSpark-${role}-${idx}`} x1="0" y1="0" x2="0" y2="1">
                                                                    <stop offset="5%" stopColor={kpi.stroke} stopOpacity={0.35} />
                                                                    <stop offset="95%" stopColor={kpi.stroke} stopOpacity={0} />
                                                                </linearGradient>
                                                            </defs>
                                                            <Area
                                                                type="monotone"
                                                                dataKey="v"
                                                                stroke={kpi.stroke}
                                                                strokeWidth={2}
                                                                fill={`url(#prevSpark-${role}-${idx})`}
                                                                isAnimationActive={false}
                                                            />
                                                        </AreaChart>
                                                    </ResponsiveContainer>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                {/* Row 2 — Mixed-Contrast Gradient + Obsidian Hero Bento */}
                                <div className="grid grid-cols-1 gap-4 md:grid-cols-12">
                                    <div className="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-5 text-white shadow-lg md:col-span-6">
                                        <div className="flex items-start justify-between gap-2">
                                            <div>
                                                <p className="text-[11px] font-medium text-sky-100/90">{data.heroTitle}</p>
                                                <h4 className="mt-1 font-display text-xl font-semibold tabular-nums">
                                                    {data.heroMetric}
                                                </h4>
                                            </div>
                                            <span className="rounded-full bg-white/20 px-2.5 py-0.5 font-mono text-[10px] font-semibold">
                                                {data.heroChip}
                                            </span>
                                        </div>
                                        <div className="mt-4 h-2 w-full overflow-hidden rounded-full bg-white/20">
                                            <div
                                                className="h-full rounded-full bg-white"
                                                style={{ width: `${data.heroPct}%` }}
                                            />
                                        </div>
                                        <div className="mt-3 flex items-center justify-between text-[11px] text-sky-100/90">
                                            <span>{data.heroFootLeft}</span>
                                            <span>{data.heroFootRight}</span>
                                        </div>
                                    </div>

                                    <div className="rounded-[28px] bg-[#070C16] p-5 text-slate-100 ring-1 ring-white/10 md:col-span-6">
                                        <div className="flex items-start justify-between gap-2">
                                            <div>
                                                <p className="text-[11px] font-medium text-amber-300">{data.obsidianTitle}</p>
                                                <h4 className="mt-1 font-display text-xl font-semibold text-white tabular-nums">
                                                    {data.obsidianMetric}
                                                </h4>
                                            </div>
                                            <span className="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-sky-300">
                                                <ArrowUpRight className="h-4 w-4" />
                                            </span>
                                        </div>
                                        <div className="mt-3.5 grid grid-cols-2 gap-2.5 rounded-[20px] bg-white/5 p-3 font-mono text-xs tabular-nums">
                                            <div>
                                                <p className="text-[9px] text-slate-400">{data.obsidianLeftLabel}</p>
                                                <p className="mt-0.5 text-sm font-semibold text-sky-300">{data.obsidianLeftVal}</p>
                                            </div>
                                            <div>
                                                <p className="text-[9px] text-slate-400">{data.obsidianRightLabel}</p>
                                                <p className="mt-0.5 text-sm font-semibold text-teal-300">{data.obsidianRightVal}</p>
                                            </div>
                                        </div>
                                        <p className="mt-2.5 text-[11px] text-slate-400">{data.obsidianNote}</p>
                                    </div>
                                </div>

                                {/* Row 3 — Capsule Bar Chart + Circular Donut Gauge (V28 DNA) */}
                                <div className="grid grid-cols-1 gap-4 md:grid-cols-12">
                                    <div className="rounded-[28px] bg-vl-bg/85 p-5 ring-1 ring-vl-border/15 md:col-span-7">
                                        <div className="mb-3 flex items-center justify-between">
                                            <div>
                                                <p className="font-display text-sm font-semibold text-vl-ink">{data.barTitle}</p>
                                                <p className="text-[11px] text-vl-ink-muted">{data.barSub}</p>
                                            </div>
                                            <span className="rounded-full bg-teal-500/15 px-2.5 py-0.5 font-mono text-[10px] text-teal-600 dark:text-teal-300">
                                                Live Sample
                                            </span>
                                        </div>
                                        <div className="h-40 w-full">
                                            <ResponsiveContainer width="100%" height="100%">
                                                <BarChart data={data.barData} barSize={20}>
                                                    <defs>
                                                        <linearGradient id={`prevBarGrad-${role}`} x1="0" y1="0" x2="0" y2="1">
                                                            <stop offset="0%" stopColor="#2DD4BF" />
                                                            <stop offset="100%" stopColor="#0284C7" />
                                                        </linearGradient>
                                                    </defs>
                                                    <CartesianGrid strokeDasharray="3 3" stroke={gridColor} vertical={false} />
                                                    <XAxis dataKey="label" stroke={tickColor} fontSize={10} tickLine={false} axisLine={false} />
                                                    <Tooltip contentStyle={tooltipStyle} />
                                                    <Bar dataKey="value" fill={`url(#prevBarGrad-${role})`} radius={[99, 99, 99, 99]} />
                                                </BarChart>
                                            </ResponsiveContainer>
                                        </div>
                                    </div>

                                    <div className="rounded-[28px] bg-vl-bg/85 p-5 ring-1 ring-vl-border/15 md:col-span-5">
                                        <div className="flex items-center justify-between">
                                            <p className="font-display text-sm font-semibold text-vl-ink">{data.donutTitle}</p>
                                            <span className="font-mono text-[10px] text-vl-ink-muted">Breakdown</span>
                                        </div>
                                        <div className="relative mt-1 h-32 w-full">
                                            <ResponsiveContainer width="100%" height="100%">
                                                <PieChart>
                                                    <Pie
                                                        data={data.donutSlices}
                                                        dataKey="value"
                                                        nameKey="name"
                                                        cx="50%"
                                                        cy="50%"
                                                        innerRadius={36}
                                                        outerRadius={52}
                                                        paddingAngle={4}
                                                        cornerRadius={8}
                                                        stroke="none"
                                                    >
                                                        {data.donutSlices.map((s) => (
                                                            <Cell key={s.name} fill={s.color} />
                                                        ))}
                                                    </Pie>
                                                    <Tooltip contentStyle={tooltipStyle} />
                                                </PieChart>
                                            </ResponsiveContainer>
                                            <div className="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                                                <span className="font-display text-base font-semibold text-vl-ink tabular-nums">
                                                    {data.donutCenterVal}
                                                </span>
                                                <span className="text-[9px] text-vl-ink-muted">{data.donutCenterSub}</span>
                                            </div>
                                        </div>
                                        <div className="mt-1 flex flex-wrap justify-center gap-1.5 text-[10px]">
                                            {data.donutSlices.map((s) => (
                                                <span
                                                    key={s.name}
                                                    className="inline-flex items-center gap-1 rounded-full bg-vl-surface px-2.5 py-0.5 text-vl-ink-secondary tabular-nums"
                                                >
                                                    <span
                                                        className="h-1.5 w-1.5 rounded-full"
                                                        style={{ backgroundColor: s.color }}
                                                    />
                                                    {s.name}: {s.value}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </motion.div>
                        </AnimatePresence>
                    </div>
                </div>
            </div>
        </div>
    );
}

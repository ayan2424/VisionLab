import React, { Suspense, lazy, useMemo, useState } from 'react';
import { AnimatePresence, motion } from 'motion/react';
import {
    Area,
    AreaChart,
    Cell,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
} from 'recharts';
import {
    AlertTriangle,
    ArrowRight,
    ArrowUpRight,
    BookOpen,
    Box,
    CheckSquare,
    Cpu,
    LayoutGrid,
    Plus,
    ShieldAlert,
    Users,
} from 'lucide-react';
import { cn } from '@/lib/cn';
import { useTheme } from '@/components/ThemeProvider';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

const LabRoom3DStage = lazy(() => import('@/components/three/LabRoom3DStage'));

interface InstructorCourse {
    id: number;
    title: string;
    slug: string;
    enrollment_code?: string;
    students_count?: number;
}

interface RecentSubmission {
    id: number;
    status: string;
    grade: number | null;
    created_at: string;
    student?: { name: string } | null;
    assignment?: { title: string; max_points: number; course?: { title: string } | null } | null;
}

interface InstructorDashboardProps {
    courses: InstructorCourse[];
    pendingGrading: number;
    totalStudents: number;
    recentSubmissions: RecentSubmission[];
    lateSubmissions: number;
    activeWorkspaceCount: number;
    avgGrade: number | null;
    pendingPatches: number;
}

function makeSpark(base: number, pattern: number[]) {
    const floor = Math.max(base, 3);
    return pattern.map((m, idx) => ({ idx, v: Math.max(1, Math.round(floor * m)) }));
}

export default function InstructorDashboard({
    courses,
    pendingGrading,
    totalStudents,
    recentSubmissions,
    lateSubmissions,
    activeWorkspaceCount,
    avgGrade,
    pendingPatches,
}: InstructorDashboardProps) {
    const { isDark } = useTheme();
    const [viewMode, setViewMode] = useState<'studio' | 'lab3d'>('studio');

    const gradedCount = useMemo(
        () => recentSubmissions.filter((s) => s.status === 'graded').length,
        [recentSubmissions]
    );

    const donutData = useMemo(() => {
        const rows = [
            { name: 'Graded', value: gradedCount, color: '#2DD4BF' },
            { name: 'Pending', value: pendingGrading, color: '#38BDF8' },
            { name: 'Late', value: lateSubmissions, color: '#FBBF24' },
        ];
        const total = rows.reduce((s, r) => s + r.value, 0);
        return total > 0 ? rows : [{ name: 'No Data', value: 1, color: '#334155' }];
    }, [gradedCount, pendingGrading, lateSubmissions]);

    const sparkStats = useMemo(
        () => [
            {
                id: 'grading',
                label: 'Pending Grading',
                value: pendingGrading,
                chip: `${lateSubmissions} late`,
                icon: CheckSquare,
                stroke: '#FBBF24',
                gradId: 'instSparkAmber',
                chipTone: 'bg-amber-500/15 text-amber-600 dark:text-amber-300',
                iconTone: 'bg-amber-500/15 text-amber-500 dark:text-amber-300',
                series: makeSpark(pendingGrading + 2, [0.85, 0.78, 0.92, 0.65, 0.72, 0.8, 0.75]),
            },
            {
                id: 'students',
                label: 'Enrolled Students',
                value: totalStudents,
                chip: `${courses.length} courses`,
                icon: Users,
                stroke: '#38BDF8',
                gradId: 'instSparkSky',
                chipTone: 'bg-sky-500/15 text-sky-600 dark:text-sky-300',
                iconTone: 'bg-sky-500/15 text-sky-500 dark:text-sky-300',
                series: makeSpark(totalStudents, [0.7, 0.76, 0.82, 0.88, 0.94, 0.98, 1]),
            },
            {
                id: 'containers',
                label: 'Live Containers',
                value: activeWorkspaceCount,
                chip: 'Nix crun',
                icon: Cpu,
                stroke: '#2DD4BF',
                gradId: 'instSparkTeal',
                chipTone: 'bg-teal-500/15 text-teal-600 dark:text-teal-300',
                iconTone: 'bg-teal-500/15 text-teal-500 dark:text-teal-300',
                series: makeSpark(activeWorkspaceCount + 1, [0.6, 0.82, 0.7, 0.95, 0.88, 0.92, 1]),
            },
            {
                id: 'patches',
                label: 'Pending AI Patches',
                value: pendingPatches,
                chip: 'HITL gate',
                icon: ShieldAlert,
                stroke: '#0EA5E9',
                gradId: 'instSparkBlue',
                chipTone: 'bg-sky-500/15 text-sky-600 dark:text-sky-300',
                iconTone: 'bg-sky-500/15 text-sky-500 dark:text-sky-300',
                series: makeSpark(pendingPatches + 1, [0.95, 0.72, 0.86, 0.64, 0.78, 0.7, 0.8]),
            },
        ],
        [pendingGrading, lateSubmissions, totalStudents, courses.length, activeWorkspaceCount, pendingPatches]
    );

    const tooltipStyle = {
        backgroundColor: isDark ? '#080F1D' : '#FFFFFF',
        border: isDark ? '1px solid rgba(255,255,255,0.12)' : '1px solid rgba(15,23,42,0.1)',
        borderRadius: '18px',
        fontSize: '12px',
        padding: '8px 14px',
    };

    return (
        <PortalLayout title="Faculty Dashboard" pageTitle="Faculty Studio">
            <div className="space-y-6">
                {/* Header + Full-Pill Bento Studio / 3D Live Classroom Switcher */}
                <div className="flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
                    <div className="space-y-1">
                        <h2 className="font-display text-2xl font-semibold tracking-[-0.028em] text-vl-ink sm:text-3xl">
                            Instruction &amp; Grading Studio
                        </h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Monitor active lab containers, inspect live classroom seats in 3D, and review VisionGuard submissions.
                        </p>
                    </div>

                    <div className="flex flex-wrap items-center gap-2.5">
                        <div className="inline-flex items-center gap-1 rounded-full bg-vl-surface/90 p-1.5 ring-1 ring-inset ring-vl-border/15">
                            {[
                                { id: 'studio' as const, label: 'Bento Studio', icon: LayoutGrid },
                                { id: 'lab3d' as const, label: '3D Classroom', icon: Box },
                            ].map((tab) => {
                                const Icon = tab.icon;
                                const active = viewMode === tab.id;
                                return (
                                    <button
                                        key={tab.id}
                                        type="button"
                                        onClick={() => setViewMode(tab.id)}
                                        className={cn(
                                            'relative inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-xs font-semibold transition-colors',
                                            active ? 'text-white' : 'text-vl-ink-secondary hover:text-vl-ink'
                                        )}
                                    >
                                        {active && (
                                            <motion.span
                                                layoutId="instructor-view-pill"
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

                        <MagneticButton href="/courses/create" external size="sm" variant="primary">
                            <Plus className="h-3.5 w-3.5" />
                            Create Course
                        </MagneticButton>
                    </div>
                </div>

                <AnimatePresence mode="wait">
                    {viewMode === 'lab3d' ? (
                        <motion.div
                            key="inst-lab3d"
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -8 }}
                            transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                        >
                            <Suspense
                                fallback={
                                    <div className="flex h-[560px] items-center justify-center rounded-[36px] bg-vl-surface text-sm text-vl-ink-muted">
                                        Loading Interactive 3D Classroom Telemetry...
                                    </div>
                                }
                            >
                                <LabRoom3DStage isDark={isDark} />
                            </Suspense>
                        </motion.div>
                    ) : (
                        <motion.div
                            key="inst-studio"
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -8 }}
                            transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                            className="space-y-6"
                        >
                            {/* Row 1 — V28 4 Sparkline KPI Bento Cards */}
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                {sparkStats.map((stat, idx) => {
                                    const Icon = stat.icon;
                                    return (
                                        <motion.div
                                            key={stat.id}
                                            initial={{ opacity: 0, y: 12 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            transition={{ duration: 0.24, delay: idx * 0.04, ease: [0.16, 1, 0.3, 1] }}
                                        >
                                            <SpotlightSurface className="!rounded-[30px] p-5">
                                                <div className="flex items-center justify-between gap-2">
                                                    <div className="flex items-center gap-2.5">
                                                        <span className={cn('flex h-9 w-9 items-center justify-center rounded-full', stat.iconTone)}>
                                                            <Icon className="h-4 w-4" />
                                                        </span>
                                                        <span className="text-xs font-medium text-vl-ink-secondary">{stat.label}</span>
                                                    </div>
                                                    <span className={cn('rounded-full px-2.5 py-0.5 font-mono text-[10px] font-semibold', stat.chipTone)}>
                                                        {stat.chip}
                                                    </span>
                                                </div>

                                                <div className="mt-4 flex items-end justify-between gap-3">
                                                    <p className="font-display text-3xl font-semibold tracking-tight text-vl-ink tabular-nums">
                                                        {stat.value}
                                                    </p>
                                                    <div className="h-11 w-24 shrink-0">
                                                        <ResponsiveContainer width="100%" height="100%">
                                                            <AreaChart data={stat.series}>
                                                                <defs>
                                                                    <linearGradient id={stat.gradId} x1="0" y1="0" x2="0" y2="1">
                                                                        <stop offset="5%" stopColor={stat.stroke} stopOpacity={0.35} />
                                                                        <stop offset="95%" stopColor={stat.stroke} stopOpacity={0} />
                                                                    </linearGradient>
                                                                </defs>
                                                                <Area
                                                                    type="monotone"
                                                                    dataKey="v"
                                                                    stroke={stat.stroke}
                                                                    strokeWidth={2}
                                                                    fill={`url(#${stat.gradId})`}
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

                            {/* Row 2 — V21 Mixed-Contrast Bento Trio (Gradient Hero + Obsidian Forensics + Circular Donut) */}
                            <div className="grid grid-cols-1 gap-5 lg:grid-cols-12">
                                {/* 5-Col Sapphire-Teal Gradient Course Launch Card */}
                                <div className="relative overflow-hidden rounded-[34px] bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-7 text-white shadow-[0_24px_60px_-20px_rgba(14,165,233,0.55)] lg:col-span-5">
                                    <div
                                        aria-hidden="true"
                                        className="pointer-events-none absolute -top-14 -right-12 h-44 w-44 rounded-full bg-white/15 blur-2xl"
                                    />
                                    <div className="relative z-10 flex h-full flex-col justify-between gap-6">
                                        <div>
                                            <p className="font-mono text-[11px] text-sky-100/90">FACULTY COMMAND</p>
                                            <h3 className="mt-1 font-display text-2xl font-semibold tracking-tight">
                                                {courses.length} active course section{courses.length === 1 ? '' : 's'}
                                            </h3>
                                            <p className="mt-2 text-xs leading-relaxed text-sky-100/90">
                                                Launch deterministic Nix profiles, open live student workspaces, and inspect exam sessions in real time.
                                            </p>
                                        </div>

                                        <div className="flex flex-wrap items-center gap-2.5">
                                            <a
                                                href="/submissions"
                                                className="inline-flex items-center gap-1.5 rounded-full bg-white px-4 py-2 text-xs font-semibold text-blue-700 hover:bg-sky-50 transition-colors"
                                            >
                                                Open Grading Queue ({pendingGrading})
                                                <ArrowUpRight className="h-3.5 w-3.5" />
                                            </a>
                                            <button
                                                type="button"
                                                onClick={() => setViewMode('lab3d')}
                                                className="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-4 py-2 text-xs font-semibold text-white hover:bg-white/25 transition-colors"
                                            >
                                                <Box className="h-3.5 w-3.5" />
                                                3D Classroom View
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {/* 4-Col High-Contrast Obsidian VisionGuard Pulse Card */}
                                <div className="rounded-[34px] bg-[#070C16] p-7 text-slate-100 shadow-[0_24px_60px_-24px_rgba(0,0,0,0.65)] ring-1 ring-white/10 lg:col-span-4">
                                    <div className="flex h-full flex-col justify-between gap-6">
                                        <div className="flex items-start justify-between gap-3">
                                            <div>
                                                <p className="font-mono text-[11px] text-amber-300">COHORT PULSE</p>
                                                <h3 className="mt-1 font-display text-2xl font-semibold text-white tabular-nums">
                                                    {avgGrade !== null ? `${Number(avgGrade).toFixed(1)}% Avg` : 'Awaiting Grades'}
                                                </h3>
                                            </div>
                                            <span className="flex h-10 w-10 items-center justify-center rounded-full bg-amber-500/15 text-amber-300">
                                                <AlertTriangle className="h-5 w-5" />
                                            </span>
                                        </div>

                                        <div className="grid grid-cols-2 gap-3 rounded-[24px] bg-white/5 p-4 font-mono text-xs tabular-nums">
                                            <div>
                                                <p className="text-[10px] text-slate-400">LATE SUBMISSIONS</p>
                                                <p className="mt-1 text-lg font-semibold text-amber-300">{lateSubmissions}</p>
                                            </div>
                                            <div>
                                                <p className="text-[10px] text-slate-400">STAGED AI PATCHES</p>
                                                <p className="mt-1 text-lg font-semibold text-sky-300">{pendingPatches}</p>
                                            </div>
                                        </div>

                                        <p className="text-xs text-slate-400">
                                            VisionGuard records keystroke cadence and approved AI diffs alongside each submission.
                                        </p>
                                    </div>
                                </div>

                                {/* 3-Col Circular Donut Grading Ratio Card */}
                                <SpotlightSurface className="!rounded-[34px] p-6 lg:col-span-3">
                                    <div className="flex items-center justify-between">
                                        <h3 className="font-display text-base font-semibold text-vl-ink">Grading Mix</h3>
                                        <span className="rounded-full bg-vl-surface-raised px-2.5 py-0.5 font-mono text-[10px] text-vl-ink-muted tabular-nums">
                                            {pendingGrading + gradedCount} items
                                        </span>
                                    </div>

                                    <div className="relative mt-2 h-36 w-full">
                                        <ResponsiveContainer width="100%" height="100%">
                                            <PieChart>
                                                <Pie
                                                    data={donutData}
                                                    dataKey="value"
                                                    nameKey="name"
                                                    cx="50%"
                                                    cy="50%"
                                                    innerRadius={42}
                                                    outerRadius={60}
                                                    paddingAngle={4}
                                                    cornerRadius={8}
                                                    stroke="none"
                                                >
                                                    {donutData.map((entry) => (
                                                        <Cell key={entry.name} fill={entry.color} />
                                                    ))}
                                                </Pie>
                                                <Tooltip contentStyle={tooltipStyle} />
                                            </PieChart>
                                        </ResponsiveContainer>
                                        <div className="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                                            <span className="font-display text-xl font-semibold text-vl-ink tabular-nums">
                                                {pendingGrading}
                                            </span>
                                            <span className="text-[10px] text-vl-ink-muted">Queued</span>
                                        </div>
                                    </div>

                                    <div className="mt-2 flex flex-wrap justify-center gap-1.5 text-[11px]">
                                        {[
                                            { label: 'Graded', val: gradedCount, dot: 'bg-teal-400' },
                                            { label: 'Pending', val: pendingGrading, dot: 'bg-sky-400' },
                                            { label: 'Late', val: lateSubmissions, dot: 'bg-amber-400' },
                                        ].map((item) => (
                                            <span
                                                key={item.label}
                                                className="inline-flex items-center gap-1 rounded-full bg-vl-bg/75 px-2.5 py-0.5 text-vl-ink-secondary tabular-nums"
                                            >
                                                <span className={cn('h-1.5 w-1.5 rounded-full', item.dot)} />
                                                {item.label}: {item.val}
                                            </span>
                                        ))}
                                    </div>
                                </SpotlightSurface>
                            </div>

                            {/* Row 3 — Submissions Queue & Courses List */}
                            <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                                <SpotlightSurface accent="sky" className="!rounded-[34px] p-6 lg:col-span-7">
                                    <div className="mb-5 flex items-center justify-between">
                                        <h3 className="font-display text-lg font-semibold text-vl-ink">
                                            Recent Submissions
                                        </h3>
                                        <a
                                            href="/submissions"
                                            className="inline-flex items-center gap-1 rounded-full bg-vl-surface-raised px-3.5 py-1.5 text-xs font-medium text-sky-500 hover:bg-sky-500/15 transition-colors"
                                        >
                                            Open queue <ArrowRight className="h-3.5 w-3.5" />
                                        </a>
                                    </div>

                                    {recentSubmissions.length === 0 ? (
                                        <p className="py-10 text-center text-sm text-vl-ink-muted">
                                            No recent student submissions.
                                        </p>
                                    ) : (
                                        <div className="space-y-2.5">
                                            {recentSubmissions.map((sub) => (
                                                <a
                                                    key={sub.id}
                                                    href={`/submissions/${sub.id}`}
                                                    className="flex items-center justify-between gap-4 rounded-full bg-vl-bg/75 px-5 py-3 hover:bg-vl-surface-raised/85 transition-colors"
                                                >
                                                    <div className="min-w-0">
                                                        <p className="truncate text-sm font-semibold text-vl-ink">
                                                            {sub.student?.name ?? 'Student'} — {sub.assignment?.title ?? 'Assignment'}
                                                        </p>
                                                        <p className="truncate text-xs text-vl-ink-muted">
                                                            {sub.assignment?.course?.title ?? ''}
                                                        </p>
                                                    </div>
                                                    <StatusPill
                                                        tone={
                                                            sub.status === 'graded'
                                                                ? 'teal'
                                                                : sub.status === 'late'
                                                                ? 'rose'
                                                                : 'amber'
                                                        }
                                                    >
                                                        {sub.status}
                                                    </StatusPill>
                                                </a>
                                            ))}
                                        </div>
                                    )}
                                </SpotlightSurface>

                                <SpotlightSurface className="!rounded-[34px] p-6 lg:col-span-5">
                                    <div className="mb-5 flex items-center justify-between">
                                        <h3 className="font-display text-lg font-semibold text-vl-ink">Your Courses</h3>
                                        <span className="flex h-8 w-8 items-center justify-center rounded-full bg-sky-500/15 text-sky-400">
                                            <BookOpen className="h-4 w-4" />
                                        </span>
                                    </div>
                                    <div className="space-y-2.5">
                                        {courses.length === 0 ? (
                                            <p className="py-6 text-center text-xs text-vl-ink-muted">
                                                No courses created yet.
                                            </p>
                                        ) : (
                                            courses.map((course) => (
                                                <a
                                                    key={course.id}
                                                    href={`/courses/${course.slug}`}
                                                    className="flex items-center justify-between gap-3 rounded-[26px] bg-vl-bg/75 px-4 py-3.5 hover:bg-vl-surface-raised/85 transition-colors"
                                                >
                                                    <div className="min-w-0">
                                                        <p className="truncate text-sm font-semibold text-vl-ink">
                                                            {course.title}
                                                        </p>
                                                        <p className="font-mono text-[11px] text-vl-ink-muted">
                                                            Code: {course.enrollment_code ?? '—'}
                                                        </p>
                                                    </div>
                                                    <span className="shrink-0 rounded-full bg-sky-500/15 px-3 py-1 font-mono text-xs font-semibold text-sky-500 dark:text-sky-300 tabular-nums">
                                                        {course.students_count ?? 0} seats
                                                    </span>
                                                </a>
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

import React, { useMemo } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { motion } from 'motion/react';
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
    ArrowRight,
    ArrowUpRight,
    Award,
    Bell,
    BookOpen,
    Calendar,
    CheckCircle2,
    Flame,
    Play,
    Plus,
    Terminal,
} from 'lucide-react';
import { cn } from '@/lib/cn';
import { useTheme } from '@/components/ThemeProvider';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';
import type { SharedProps } from '@/types';

interface CourseSummary {
    id: number;
    title: string;
    slug: string;
    enrollment_code?: string;
    instructor?: { name: string } | null;
    students_count?: number;
}

interface AssignmentSummary {
    id: number;
    title: string;
    due_date: string | null;
    max_points: number;
    course?: { title: string; slug: string } | null;
}

interface AnnouncementSummary {
    id: number;
    title: string;
    created_at: string;
    is_unread?: boolean;
    course?: { title: string } | null;
    author?: { name: string } | null;
}

interface WorkspaceSummary {
    id: number;
    name: string;
    slug: string;
    language: string;
    status: string;
    course?: { title: string } | null;
}

interface GradedSummary {
    id: number;
    grade: number | null;
    assignment?: { title: string; max_points: number; course?: { title: string } | null } | null;
}

interface StudentDashboardProps {
    courses: CourseSummary[];
    upcomingAssignments: AssignmentSummary[];
    recentAnnouncements: AnnouncementSummary[];
    unreadAnnouncementCount: number;
    pendingSubmissions: number;
    gradedSubmissions: GradedSummary[];
    streak: number;
    activeWorkspaces: WorkspaceSummary[];
}

function buildSpark(base: number, pattern: number[]) {
    const floor = Math.max(base, 2);
    return pattern.map((m, idx) => ({ idx, v: Math.max(1, Math.round(floor * m)) }));
}

export default function StudentDashboard({
    courses,
    upcomingAssignments,
    recentAnnouncements,
    unreadAnnouncementCount,
    pendingSubmissions,
    gradedSubmissions,
    streak,
    activeWorkspaces,
}: StudentDashboardProps) {
    const { isDark } = useTheme();
    const { props } = usePage<SharedProps>();
    const user = props.auth.user!;

    const nextLevelXp = Math.max(100, user.level * 500);
    const xpProgressPct = Math.min(100, Math.round(((user.xp % nextLevelXp) / nextLevelXp) * 100));

    const completionDonut = useMemo(() => {
        const done = gradedSubmissions.length;
        const open = pendingSubmissions;
        if (done + open === 0) {
            return [{ name: 'Ready', value: 1, color: '#38BDF8' }];
        }
        return [
            { name: 'Graded', value: done, color: '#2DD4BF' },
            { name: 'Pending', value: open, color: '#FBBF24' },
        ];
    }, [gradedSubmissions.length, pendingSubmissions]);

    const sparkCards = useMemo(
        () => [
            {
                id: 'containers',
                label: 'Active Containers',
                value: activeWorkspaces.length,
                chip: 'Nix IDE',
                icon: Terminal,
                stroke: '#38BDF8',
                gradId: 'stuSparkSky',
                chipTone: 'bg-sky-500/15 text-sky-600 dark:text-sky-300',
                iconTone: 'bg-sky-500/15 text-sky-500 dark:text-sky-300',
                series: buildSpark(activeWorkspaces.length + 1, [0.6, 0.8, 0.72, 0.9, 0.85, 0.95, 1]),
            },
            {
                id: 'pending',
                label: 'Pending Lab Work',
                value: pendingSubmissions,
                chip: `${upcomingAssignments.length} due`,
                icon: Calendar,
                stroke: '#FBBF24',
                gradId: 'stuSparkAmber',
                chipTone: 'bg-amber-500/15 text-amber-600 dark:text-amber-300',
                iconTone: 'bg-amber-500/15 text-amber-500 dark:text-amber-300',
                series: buildSpark(pendingSubmissions + 1, [0.95, 0.85, 0.78, 0.82, 0.7, 0.65, 0.74]),
            },
            {
                id: 'streak',
                label: 'Daily Streak',
                value: `${streak}d`,
                chip: `Lvl ${user.level}`,
                icon: Flame,
                stroke: '#2DD4BF',
                gradId: 'stuSparkTeal',
                chipTone: 'bg-teal-500/15 text-teal-600 dark:text-teal-300',
                iconTone: 'bg-teal-500/15 text-teal-500 dark:text-teal-300',
                series: buildSpark(streak + 2, [0.5, 0.62, 0.7, 0.8, 0.86, 0.94, 1]),
            },
            {
                id: 'notices',
                label: 'Unread Notices',
                value: unreadAnnouncementCount,
                chip: `${courses.length} courses`,
                icon: Bell,
                stroke: '#0EA5E9',
                gradId: 'stuSparkBlue',
                chipTone: 'bg-sky-500/15 text-sky-600 dark:text-sky-300',
                iconTone: 'bg-sky-500/15 text-sky-500 dark:text-sky-300',
                series: buildSpark(unreadAnnouncementCount + 1, [0.8, 0.7, 0.9, 0.65, 0.75, 0.6, 0.8]),
            },
        ],
        [
            activeWorkspaces.length,
            pendingSubmissions,
            upcomingAssignments.length,
            streak,
            user.level,
            unreadAnnouncementCount,
            courses.length,
        ]
    );

    const tooltipStyle = {
        backgroundColor: isDark ? '#080F1D' : '#FFFFFF',
        border: isDark ? '1px solid rgba(255,255,255,0.12)' : '1px solid rgba(15,23,42,0.1)',
        borderRadius: '18px',
        fontSize: '12px',
        padding: '8px 14px',
    };

    return (
        <PortalLayout title="Student Dashboard" pageTitle="Student Workspace">
            <div className="space-y-6">
                {/* Greeting Band */}
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div className="space-y-1">
                        <h2 className="font-display text-2xl font-semibold tracking-[-0.028em] text-vl-ink sm:text-3xl">
                            Welcome back, {user.name.split(' ')[0]}
                        </h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Level {user.level} {user.rank_title} ·{' '}
                            <span className="font-mono tabular-nums text-sky-500 dark:text-sky-400">
                                {user.xp.toLocaleString()} XP
                            </span>
                        </p>
                    </div>
                    <div className="flex flex-wrap items-center gap-2.5">
                        <a
                            href="/join"
                            className="inline-flex items-center gap-2 rounded-full bg-vl-surface px-4 py-2 text-xs font-semibold text-vl-ink ring-1 ring-inset ring-vl-border/15 hover:bg-vl-surface-raised transition-colors"
                        >
                            Join Course
                        </a>
                        <MagneticButton href="/workspace/create" size="sm" variant="primary">
                            <Plus className="h-3.5 w-3.5" />
                            New Workspace
                        </MagneticButton>
                    </div>
                </div>

                {/* Row 1 — V28 4 Sparkline KPI Bento Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {sparkCards.map((stat, idx) => {
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

                {/* Row 2 — V21 Mixed-Contrast Hero Bento (Gradient XP Hero + Obsidian Containers + Donut Progress) */}
                <div className="grid grid-cols-1 gap-5 lg:grid-cols-12">
                    {/* 5-Col Vibrant Sapphire-Teal Gradient XP & Launch Card */}
                    <div className="relative overflow-hidden rounded-[34px] bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-7 text-white shadow-[0_24px_60px_-20px_rgba(14,165,233,0.55)] lg:col-span-5">
                        <div
                            aria-hidden="true"
                            className="pointer-events-none absolute -top-14 -right-10 h-44 w-44 rounded-full bg-white/15 blur-2xl"
                        />
                        <div className="relative z-10 flex h-full flex-col justify-between gap-6">
                            <div className="flex items-start justify-between gap-3">
                                <div>
                                    <p className="font-mono text-[11px] text-sky-100/90">ACADEMIC MASTERY</p>
                                    <h3 className="mt-1 font-display text-2xl font-semibold tracking-tight">
                                        Level {user.level} · {user.rank_title}
                                    </h3>
                                </div>
                                <span className="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 font-mono text-xs font-semibold tabular-nums backdrop-blur-md">
                                    <Flame className="h-3.5 w-3.5" />
                                    {streak}d streak
                                </span>
                            </div>

                            <div className="space-y-2">
                                <div className="flex items-center justify-between text-xs text-sky-100/90 tabular-nums">
                                    <span>{user.xp.toLocaleString()} XP earned</span>
                                    <span>{xpProgressPct}% to Level {user.level + 1}</span>
                                </div>
                                <div className="h-2.5 w-full overflow-hidden rounded-full bg-white/20">
                                    <div
                                        className="h-full rounded-full bg-white transition-all duration-500"
                                        style={{ width: `${Math.max(xpProgressPct, 8)}%` }}
                                    />
                                </div>
                            </div>

                            <div className="flex flex-wrap items-center gap-2.5">
                                <Link
                                    href="/workspace/create"
                                    prefetch="hover"
                                    className="inline-flex items-center gap-1.5 rounded-full bg-white px-4 py-2 text-xs font-semibold text-blue-700 hover:bg-sky-50 transition-colors"
                                >
                                    Launch Cloud IDE
                                    <ArrowUpRight className="h-3.5 w-3.5" />
                                </Link>
                                <a
                                    href="/gamification"
                                    className="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-4 py-2 text-xs font-semibold text-white hover:bg-white/25 transition-colors"
                                >
                                    Achievements
                                </a>
                            </div>
                        </div>
                    </div>

                    {/* 4-Col High-Contrast Obsidian Running Containers Card */}
                    <div className="rounded-[34px] bg-[#070C16] p-6 text-slate-100 shadow-[0_24px_60px_-24px_rgba(0,0,0,0.65)] ring-1 ring-white/10 lg:col-span-4">
                        <div className="mb-4 flex items-center justify-between">
                            <div>
                                <p className="font-mono text-[10px] text-teal-300">LIVE WORKSPACES</p>
                                <h3 className="font-display text-lg font-semibold text-white">Quick Resume</h3>
                            </div>
                            <Link
                                href="/workspace"
                                prefetch="hover"
                                className="inline-flex items-center gap-1 rounded-full bg-white/10 px-3 py-1 text-[11px] font-medium text-sky-300 hover:bg-white/15 transition-colors"
                            >
                                All <ArrowRight className="h-3 w-3" />
                            </Link>
                        </div>

                        {activeWorkspaces.length === 0 ? (
                            <div className="flex flex-col items-center justify-center rounded-[24px] bg-white/5 px-4 py-7 text-center">
                                <p className="text-xs text-slate-300">No active containers running right now.</p>
                                <Link
                                    href="/workspace/create"
                                    prefetch="hover"
                                    className="mt-3 inline-flex items-center gap-1.5 rounded-full bg-sky-500 px-4 py-1.5 text-xs font-semibold text-white hover:bg-sky-400 transition-colors"
                                >
                                    Provision Seat
                                </Link>
                            </div>
                        ) : (
                            <div className="space-y-2.5">
                                {activeWorkspaces.slice(0, 3).map((ws) => (
                                    <Link
                                        key={ws.id}
                                        href={`/workspace/${ws.slug}`}
                                        prefetch="hover"
                                        className="group flex items-center justify-between gap-3 rounded-full bg-white/5 px-4 py-2.5 hover:bg-white/10 transition-colors"
                                    >
                                        <div className="min-w-0">
                                            <div className="flex items-center gap-2">
                                                <span className="h-2 w-2 shrink-0 rounded-full bg-teal-400 animate-pulse" />
                                                <span className="truncate text-xs font-semibold text-white">
                                                    {ws.name}
                                                </span>
                                            </div>
                                            <p className="truncate pl-4 font-mono text-[10px] text-slate-400">
                                                {ws.language} · {ws.course?.title ?? 'Personal Lab'}
                                            </p>
                                        </div>
                                        <Play className="h-3.5 w-3.5 shrink-0 text-sky-400 transition-transform group-hover:translate-x-0.5" />
                                    </Link>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* 3-Col Circular Donut Lab Progress Card */}
                    <SpotlightSurface className="!rounded-[34px] p-6 lg:col-span-3">
                        <div className="flex items-center justify-between">
                            <h3 className="font-display text-base font-semibold text-vl-ink">Lab Progress</h3>
                            <span className="rounded-full bg-vl-surface-raised px-2.5 py-0.5 font-mono text-[10px] text-vl-ink-muted tabular-nums">
                                {gradedSubmissions.length} graded
                            </span>
                        </div>

                        <div className="relative mt-2 h-36 w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <PieChart>
                                    <Pie
                                        data={completionDonut}
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
                                        {completionDonut.map((entry) => (
                                            <Cell key={entry.name} fill={entry.color} />
                                        ))}
                                    </Pie>
                                    <Tooltip contentStyle={tooltipStyle} />
                                </PieChart>
                            </ResponsiveContainer>
                            <div className="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                                <span className="font-display text-xl font-semibold text-vl-ink tabular-nums">
                                    {courses.length}
                                </span>
                                <span className="text-[10px] text-vl-ink-muted">Courses</span>
                            </div>
                        </div>

                        <div className="mt-2 flex justify-center gap-2 text-[11px]">
                            <span className="inline-flex items-center gap-1 rounded-full bg-vl-bg/75 px-2.5 py-0.5 text-vl-ink-secondary tabular-nums">
                                <span className="h-1.5 w-1.5 rounded-full bg-teal-400" />
                                Done: {gradedSubmissions.length}
                            </span>
                            <span className="inline-flex items-center gap-1 rounded-full bg-vl-bg/75 px-2.5 py-0.5 text-vl-ink-secondary tabular-nums">
                                <span className="h-1.5 w-1.5 rounded-full bg-amber-400" />
                                Due: {pendingSubmissions}
                            </span>
                        </div>
                    </SpotlightSurface>
                </div>

                {/* Row 3 — Enrolled Courses + Deadlines, Grades & Notices */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <SpotlightSurface className="!rounded-[34px] p-6 lg:col-span-7">
                        <div className="mb-5 flex items-center justify-between">
                            <div>
                                <h3 className="font-display text-lg font-semibold text-vl-ink">Enrolled Courses</h3>
                                <p className="text-xs text-vl-ink-muted">Pinned Nix toolchains &amp; course syllabi</p>
                            </div>
                            <a
                                href="/courses"
                                className="rounded-full bg-vl-surface-raised px-3.5 py-1.5 text-xs font-medium text-sky-500 hover:bg-sky-500/15 transition-colors"
                            >
                                View catalog
                            </a>
                        </div>

                        {courses.length === 0 ? (
                            <p className="py-10 text-center text-sm text-vl-ink-muted">
                                You are not enrolled in any active courses yet.
                            </p>
                        ) : (
                            <div className="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                                {courses.map((course) => (
                                    <a
                                        key={course.id}
                                        href={`/courses/${course.slug}`}
                                        className="group rounded-[26px] bg-vl-bg/75 p-5 hover:bg-vl-surface-raised/85 transition-colors"
                                    >
                                        <div className="flex items-center justify-between gap-3">
                                            <span className="flex h-9 w-9 items-center justify-center rounded-full bg-sky-500/15 text-sky-400">
                                                <BookOpen className="h-4 w-4" />
                                            </span>
                                            <span className="rounded-full bg-vl-surface px-2.5 py-0.5 font-mono text-[10px] text-vl-ink-muted tabular-nums">
                                                {course.students_count ?? 0} seats
                                            </span>
                                        </div>
                                        <p className="mt-4 font-display text-base font-semibold text-vl-ink group-hover:text-sky-400 transition-colors">
                                            {course.title}
                                        </p>
                                        <p className="mt-1 text-xs text-vl-ink-muted">
                                            {course.instructor?.name ?? 'Department Faculty'}
                                        </p>
                                    </a>
                                ))}
                            </div>
                        )}
                    </SpotlightSurface>

                    <div className="space-y-6 lg:col-span-5">
                        <SpotlightSurface accent="amber" className="!rounded-[34px] p-6">
                            <div className="flex items-center justify-between">
                                <h3 className="font-display text-base font-semibold text-vl-ink">Upcoming Deadlines</h3>
                                <StatusPill tone="amber">{upcomingAssignments.length} open</StatusPill>
                            </div>
                            <div className="mt-4 space-y-2.5">
                                {upcomingAssignments.length === 0 ? (
                                    <p className="text-xs text-vl-ink-muted">No upcoming deadlines on your schedule.</p>
                                ) : (
                                    upcomingAssignments.map((a) => (
                                        <a
                                            key={a.id}
                                            href={`/assignments/${a.id}`}
                                            className="flex items-center justify-between gap-3 rounded-full bg-vl-bg/75 px-4 py-3 hover:bg-vl-surface-raised/85 transition-colors"
                                        >
                                            <div className="min-w-0">
                                                <p className="truncate text-sm font-medium text-vl-ink">{a.title}</p>
                                                <p className="truncate text-[11px] text-vl-ink-muted">
                                                    {a.course?.title ?? 'Course'}
                                                </p>
                                            </div>
                                            <span className="shrink-0 rounded-full bg-amber-500/15 px-3 py-1 font-mono text-[11px] font-semibold text-amber-500 dark:text-amber-300 tabular-nums">
                                                {a.due_date ? new Date(a.due_date).toLocaleDateString() : 'Open'}
                                            </span>
                                        </a>
                                    ))
                                )}
                            </div>
                        </SpotlightSurface>

                        <SpotlightSurface accent="teal" className="!rounded-[34px] p-6">
                            <div className="flex items-center justify-between">
                                <h3 className="font-display text-base font-semibold text-vl-ink">Recent Grades</h3>
                                <span className="flex h-8 w-8 items-center justify-center rounded-full bg-teal-500/15 text-teal-400">
                                    <Award className="h-4 w-4" />
                                </span>
                            </div>
                            <div className="mt-4 space-y-2.5">
                                {gradedSubmissions.length === 0 ? (
                                    <p className="text-xs text-vl-ink-muted">No graded submissions yet.</p>
                                ) : (
                                    gradedSubmissions.map((s) => (
                                        <div
                                            key={s.id}
                                            className="flex items-center justify-between gap-3 rounded-full bg-vl-bg/75 px-4 py-3"
                                        >
                                            <div className="min-w-0">
                                                <p className="truncate text-xs font-semibold text-vl-ink">
                                                    {s.assignment?.title ?? 'Assignment'}
                                                </p>
                                                <p className="truncate text-[11px] text-vl-ink-muted">
                                                    {s.assignment?.course?.title ?? ''}
                                                </p>
                                            </div>
                                            <span className="shrink-0 rounded-full bg-teal-500/15 px-3 py-1 font-mono text-xs font-semibold text-teal-600 dark:text-teal-300 tabular-nums">
                                                {s.grade ?? 0}/{s.assignment?.max_points ?? 100}
                                            </span>
                                        </div>
                                    ))
                                )}
                            </div>
                        </SpotlightSurface>

                        <SpotlightSurface className="!rounded-[34px] p-6">
                            <div className="flex items-center justify-between">
                                <h3 className="font-display text-base font-semibold text-vl-ink">Course Notices</h3>
                                <span className="flex h-8 w-8 items-center justify-center rounded-full bg-sky-500/15 text-sky-400">
                                    <CheckCircle2 className="h-4 w-4" />
                                </span>
                            </div>
                            <div className="mt-4 space-y-2.5">
                                {recentAnnouncements.length === 0 ? (
                                    <p className="text-xs text-vl-ink-muted">You are all caught up.</p>
                                ) : (
                                    recentAnnouncements.map((ann) => (
                                        <div key={ann.id} className="rounded-[24px] bg-vl-bg/75 px-4 py-3">
                                            <div className="flex items-center justify-between gap-2">
                                                <p className="truncate text-xs font-semibold text-vl-ink">{ann.title}</p>
                                                {ann.is_unread && (
                                                    <span className="h-2 w-2 shrink-0 rounded-full bg-sky-400" />
                                                )}
                                            </div>
                                            <p className="mt-0.5 text-[11px] text-vl-ink-muted">
                                                {ann.course?.title ?? 'Campus'}
                                            </p>
                                        </div>
                                    ))
                                )}
                            </div>
                        </SpotlightSurface>
                    </div>
                </div>
            </div>
        </PortalLayout>
    );
}

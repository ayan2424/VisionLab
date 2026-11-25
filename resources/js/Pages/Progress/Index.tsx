import React from 'react';
import { Link } from '@inertiajs/react';
import { Award, BookOpen, CheckCircle2, Clock, TrendingUp } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface ProgressRow {
    assignment: { id: number; title: string; max_points: number; due_date?: string | null };
    submission: { id: number; status: string; grade: number | null } | null;
    pct: number | null;
}

interface CourseProgressItem {
    course: { id: number; title: string; slug: string; instructor?: { name: string } | null };
    assignments: ProgressRow[];
    graded_count: number;
    total_count: number;
    completion_pct: number;
    average: number | null;
}

interface ProgressProps {
    courseProgress: CourseProgressItem[];
    overallPct: number | null;
    gradeLetter: string;
    stats: { courses: number; graded: number; in_progress: number; pending: number };
}

export default function ProgressIndex({
    courseProgress,
    overallPct,
    gradeLetter,
    stats,
}: ProgressProps) {
    return (
        <PortalLayout title="Academic Progress" pageTitle="Academic Progress">
            <div className="space-y-6">
                {/* Summary Hero + KPI Cards */}
                <div className="grid grid-cols-1 gap-5 lg:grid-cols-12">
                    <div className="rounded-2xl bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-7 text-white shadow-xl lg:col-span-5">
                        <p className="font-mono text-xs text-sky-100/90">CUMULATIVE STANDING</p>
                        <div className="mt-2 flex items-baseline gap-4">
                            <span className="font-display text-5xl font-bold tabular-nums">
                                {overallPct !== null ? `${overallPct}%` : '—'}
                            </span>
                            <span className="rounded-full bg-white/20 px-4 py-1 font-display text-xl font-semibold backdrop-blur-md">
                                Grade {gradeLetter}
                            </span>
                        </div>
                        <p className="mt-3 text-xs text-sky-100/90">
                            Calculated across all graded course labs and engineering assessments.
                        </p>
                    </div>

                    <div className="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:col-span-7">
                        {[
                            { label: 'Courses', value: stats.courses, icon: BookOpen, tone: 'bg-sky-500/15 text-sky-500' },
                            { label: 'Graded', value: stats.graded, icon: CheckCircle2, tone: 'bg-teal-500/15 text-teal-500' },
                            { label: 'In Progress', value: stats.in_progress, icon: Clock, tone: 'bg-amber-500/15 text-amber-500' },
                            { label: 'Submitted', value: stats.pending, icon: TrendingUp, tone: 'bg-sky-500/15 text-sky-500' },
                        ].map((s) => {
                            const Icon = s.icon;
                            return (
                                <SpotlightSurface key={s.label} className="!rounded-2xl p-5 flex flex-col justify-between">
                                    <span className={`flex h-9 w-9 items-center justify-center rounded-full ${s.tone}`}>
                                        <Icon className="h-4 w-4" />
                                    </span>
                                    <div className="mt-4">
                                        <p className="font-display text-2xl font-semibold text-vl-ink tabular-nums">{s.value}</p>
                                        <p className="text-xs text-vl-ink-muted">{s.label}</p>
                                    </div>
                                </SpotlightSurface>
                            );
                        })}
                    </div>
                </div>

                {/* Per-Course Breakdown */}
                {courseProgress.length === 0 ? (
                    <SpotlightSurface className="!rounded-2xl p-12 text-center space-y-3">
                        <Award className="mx-auto h-8 w-8 text-vl-ink-muted" />
                        <p className="text-sm text-vl-ink-secondary">No enrolled courses found yet.</p>
                        <Link href="/join" className="vl-btn-primary inline-flex">Join a Course</Link>
                    </SpotlightSurface>
                ) : (
                    <div className="space-y-5">
                        {courseProgress.map((cp) => (
                            <SpotlightSurface key={cp.course.id} accent="sky" className="!rounded-2xl p-6 space-y-5">
                                <div className="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <Link
                                            href={`/courses/${cp.course.slug}`}
                                            className="font-display text-lg font-semibold text-vl-ink hover:text-sky-500 transition-colors"
                                        >
                                            {cp.course.title}
                                        </Link>
                                        <p className="text-xs text-vl-ink-muted">
                                            {cp.course.instructor?.name ?? 'Department Faculty'} · {cp.graded_count}/{cp.total_count} graded
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <StatusPill tone="teal">
                                            {cp.average !== null ? `Avg ${cp.average}%` : 'No grades yet'}
                                        </StatusPill>
                                        <span className="font-mono text-xs text-vl-ink-muted tabular-nums">
                                            {cp.completion_pct}% done
                                        </span>
                                    </div>
                                </div>

                                <div className="h-2 w-full overflow-hidden rounded-full bg-vl-bg">
                                    <div
                                        className="h-full rounded-full bg-gradient-to-r from-sky-500 to-teal-400"
                                        style={{ width: `${Math.max(cp.completion_pct, 4)}%` }}
                                    />
                                </div>

                                <div className="space-y-2">
                                    {cp.assignments.map((row) => (
                                        <Link
                                            key={row.assignment.id}
                                            href={`/assignments/${row.assignment.id}`}
                                            className="flex items-center justify-between gap-4 rounded-xl bg-vl-bg/75 px-5 py-3 hover:bg-vl-surface-raised transition-colors"
                                        >
                                            <span className="truncate text-sm font-medium text-vl-ink">
                                                {row.assignment.title}
                                            </span>
                                            <div className="flex items-center gap-3 shrink-0">
                                                {row.submission?.status === 'graded' ? (
                                                    <span className="font-mono text-xs font-semibold text-teal-500 tabular-nums">
                                                        {row.submission.grade ?? 0}/{row.assignment.max_points} ({row.pct ?? 0}%)
                                                    </span>
                                                ) : (
                                                    <StatusPill tone={row.submission ? 'amber' : 'neutral'}>
                                                        {row.submission?.status ?? 'not started'}
                                                    </StatusPill>
                                                )}
                                            </div>
                                        </Link>
                                    ))}
                                </div>
                            </SpotlightSurface>
                        ))}
                    </div>
                )}
            </div>
        </PortalLayout>
    );
}

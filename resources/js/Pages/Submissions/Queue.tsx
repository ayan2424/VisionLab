import React from 'react';
import { Link, router } from '@inertiajs/react';
import { CheckCircle2, CheckSquare, Clock, AlertTriangle } from 'lucide-react';
import { cn } from '@/lib/cn';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface QueueSubmission {
    id: number;
    status: string;
    grade: number | null;
    submitted_at?: string | null;
    student?: { id: number; name: string; email: string } | null;
    assignment?: {
        id: number;
        title: string;
        max_points: number;
        course?: { id: number; title: string; slug: string } | null;
    } | null;
}

interface SubmissionsQueueProps {
    submissions: {
        data: QueueSubmission[];
        links?: Array<{ url: string | null; label: string; active: boolean }>;
    };
    courses: Array<{ id: number; title: string }>;
    courseFilter: string | null;
    statusFilter: string;
    stats: { pending: number; late: number; graded: number; total: number };
    perCoursePending: Record<number, number>;
}

export default function SubmissionsQueue({
    submissions,
    courses,
    courseFilter,
    statusFilter,
    stats,
    perCoursePending,
}: SubmissionsQueueProps) {
    const applyFilter = (next: { course?: string | null; status?: string }) => {
        const params: Record<string, string> = {};
        const c = next.course !== undefined ? next.course : courseFilter;
        const s = next.status !== undefined ? next.status : statusFilter;
        if (c) params.course = String(c);
        if (s) params.status = s;
        router.get('/submissions', params, { preserveState: true, preserveScroll: true });
    };

    return (
        <PortalLayout title="Grading Queue" pageTitle="Grading Queue">
            <div className="space-y-6">
                {/* Summary KPI Strip */}
                <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    {[
                        { label: 'Pending Review', val: stats.pending, icon: Clock, tone: 'bg-amber-500/15 text-amber-500' },
                        { label: 'Late Turn-ins', val: stats.late, icon: AlertTriangle, tone: 'bg-rose-500/15 text-rose-500' },
                        { label: 'Graded Total', val: stats.graded, icon: CheckCircle2, tone: 'bg-teal-500/15 text-teal-500' },
                        { label: 'All Submissions', val: stats.total, icon: CheckSquare, tone: 'bg-sky-500/15 text-sky-500' },
                    ].map((s) => {
                        const Icon = s.icon;
                        return (
                            <SpotlightSurface key={s.label} className="!rounded-2xl p-5 flex items-center justify-between">
                                <div>
                                    <p className="text-xs text-vl-ink-secondary">{s.label}</p>
                                    <p className="mt-1 font-display text-2xl font-semibold text-vl-ink tabular-nums">{s.val}</p>
                                </div>
                                <span className={`flex h-10 w-10 items-center justify-center rounded-full ${s.tone}`}>
                                    <Icon className="h-5 w-5" />
                                </span>
                            </SpotlightSurface>
                        );
                    })}
                </div>

                {/* Filter Bar */}
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div className="inline-flex items-center gap-1 rounded-full bg-vl-surface p-1.5 ring-1 ring-inset ring-vl-border/15">
                        {(['pending', 'graded', 'all'] as const).map((st) => (
                            <button
                                key={st}
                                type="button"
                                onClick={() => applyFilter({ status: st })}
                                className={cn(
                                    'rounded-full px-4 py-1.5 text-xs font-semibold capitalize transition-colors',
                                    statusFilter === st
                                        ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-sm'
                                        : 'text-vl-ink-secondary hover:text-vl-ink'
                                )}
                            >
                                {st}
                            </button>
                        ))}
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            onClick={() => applyFilter({ course: null })}
                            className={cn(
                                'rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors',
                                !courseFilter
                                    ? 'bg-sky-500 text-white'
                                    : 'bg-vl-surface text-vl-ink-secondary hover:text-vl-ink'
                            )}
                        >
                            All Courses
                        </button>
                        {courses.map((c) => (
                            <button
                                key={c.id}
                                type="button"
                                onClick={() => applyFilter({ course: String(c.id) })}
                                className={cn(
                                    'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors',
                                    String(courseFilter) === String(c.id)
                                        ? 'bg-sky-500 text-white'
                                        : 'bg-vl-surface text-vl-ink-secondary hover:text-vl-ink'
                                )}
                            >
                                <span>{c.title}</span>
                                {perCoursePending[c.id] ? (
                                    <span className="rounded-full bg-white/20 px-1.5 font-mono text-[10px] tabular-nums">
                                        {perCoursePending[c.id]}
                                    </span>
                                ) : null}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Submissions Queue List */}
                <SpotlightSurface className="!rounded-2xl p-6">
                    {submissions.data.length === 0 ? (
                        <p className="py-12 text-center text-sm text-vl-ink-muted">
                            No submissions match the selected filters.
                        </p>
                    ) : (
                        <div className="space-y-2.5">
                            {submissions.data.map((sub) => (
                                <Link
                                    key={sub.id}
                                    href={`/submissions/${sub.id}`}
                                    className="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-vl-bg/80 px-5 py-3.5 hover:bg-vl-surface-raised transition-colors"
                                >
                                    <div className="min-w-0">
                                        <p className="truncate text-sm font-semibold text-vl-ink">
                                            {sub.student?.name ?? 'Student'} — {sub.assignment?.title ?? 'Assignment'}
                                        </p>
                                        <p className="truncate text-xs text-vl-ink-muted">
                                            {sub.assignment?.course?.title ?? ''}
                                            {sub.submitted_at
                                                ? ` · Submitted ${new Date(sub.submitted_at).toLocaleString()}`
                                                : ''}
                                        </p>
                                    </div>

                                    <div className="flex items-center gap-3 shrink-0">
                                        {sub.grade !== null && (
                                            <span className="font-mono text-xs font-semibold text-teal-500 tabular-nums">
                                                {sub.grade}/{sub.assignment?.max_points ?? 100}
                                            </span>
                                        )}
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
                                    </div>
                                </Link>
                            ))}
                        </div>
                    )}
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

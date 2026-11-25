import React from 'react';
import { Link, router } from '@inertiajs/react';
import {
    ArrowLeft,
    Calendar,
    CheckCircle2,
    Code2,
    Edit,
    Lock,
    Play,
    Send,
    Trash2,
} from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface SubmissionItem {
    id: number;
    status: string;
    grade: number | null;
    feedback?: string | null;
    submitted_at?: string | null;
    student?: { id: number; name: string; email: string } | null;
}

interface AssignmentShowProps {
    assignment: {
        id: number;
        title: string;
        description?: string | null;
        max_points: number;
        due_date?: string | null;
        starter_language?: string | null;
        starter_code?: string | null;
        is_locked?: boolean;
    };
    course: {
        id: number;
        title: string;
        slug: string;
        instructor?: { name: string } | null;
    };
    isInstructor: boolean;
    submission: SubmissionItem | null;
    submissions: SubmissionItem[];
}

export default function AssignmentShow({
    assignment,
    course,
    isInstructor,
    submission,
    submissions,
}: AssignmentShowProps) {
    const startLab = () => {
        router.post(`/assignments/${assignment.id}/start`);
    };

    const submitLab = () => {
        if (!window.confirm('Submit your current workspace snapshot for grading?')) return;
        router.post(`/assignments/${assignment.id}/submit`);
    };

    const deleteAssignment = () => {
        if (!window.confirm(`Delete assignment "${assignment.title}"?`)) return;
        router.delete(`/assignments/${assignment.id}`);
    };

    return (
        <PortalLayout title={assignment.title} pageTitle={assignment.title}>
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <Link href={`/courses/${course.slug}?tab=assignments`} className="vl-btn-secondary">
                        <ArrowLeft className="h-4 w-4" />
                        Back to {course.title}
                    </Link>

                    {isInstructor && (
                        <div className="flex items-center gap-2">
                            <Link href={`/assignments/${assignment.id}/edit`} className="vl-btn-secondary">
                                <Edit className="h-3.5 w-3.5" />
                                Edit
                            </Link>
                            <button
                                type="button"
                                onClick={deleteAssignment}
                                className="inline-flex items-center gap-1.5 rounded-full bg-rose-500/15 px-4 py-2 text-xs font-semibold text-rose-500 hover:bg-rose-500/25 transition-colors"
                            >
                                <Trash2 className="h-3.5 w-3.5" />
                                Delete
                            </button>
                        </div>
                    )}
                </div>

                {/* Spec Hero */}
                <SpotlightSurface accent="sky" className="!rounded-xl p-8 space-y-6">
                    <div className="flex flex-wrap items-start justify-between gap-4">
                        <div className="space-y-2 max-w-3xl">
                            <div className="flex flex-wrap items-center gap-2">
                                <StatusPill tone="sky">
                                    <Code2 className="h-3 w-3" />
                                    {assignment.starter_language ?? 'python'}
                                </StatusPill>
                                <StatusPill tone="teal">{assignment.max_points} pts</StatusPill>
                                {assignment.is_locked && (
                                    <StatusPill tone="amber" pulse>
                                        <Lock className="h-3 w-3" />
                                        Exam Lockdown
                                    </StatusPill>
                                )}
                                {assignment.due_date && (
                                    <span className="inline-flex items-center gap-1 font-mono text-xs text-vl-ink-muted">
                                        <Calendar className="h-3.5 w-3.5" />
                                        Due {new Date(assignment.due_date).toLocaleString()}
                                    </span>
                                )}
                            </div>
                            <h2 className="font-display text-2xl font-semibold text-vl-ink sm:text-3xl">
                                {assignment.title}
                            </h2>
                            {assignment.description && (
                                <p className="whitespace-pre-line text-sm leading-relaxed text-vl-ink-secondary">
                                    {assignment.description}
                                </p>
                            )}
                        </div>

                        {!isInstructor && (
                            <div className="flex flex-wrap items-center gap-2.5">
                                {!submission || submission.status === 'not_started' ? (
                                    <button type="button" onClick={startLab} className="vl-btn-primary">
                                        <Play className="h-4 w-4" />
                                        Launch Cloud Lab
                                    </button>
                                ) : (
                                    <>
                                        <Link href={`/assignments/${assignment.id}/ide`} className="vl-btn-primary">
                                            <Play className="h-4 w-4" />
                                            Resume Cloud IDE
                                        </Link>
                                        {submission.status === 'in_progress' && (
                                            <button type="button" onClick={submitLab} className="vl-btn-secondary">
                                                <Send className="h-4 w-4" />
                                                Submit for Grading
                                            </button>
                                        )}
                                    </>
                                )}
                            </div>
                        )}
                    </div>

                    {assignment.starter_code && (
                        <div className="space-y-2">
                            <p className="font-mono text-[11px] text-vl-ink-muted">STARTER TEMPLATE</p>
                            <pre className="overflow-x-auto rounded-xl bg-[#05080F] p-5 font-mono text-xs text-sky-200 ring-1 ring-white/10">
                                {assignment.starter_code}
                            </pre>
                        </div>
                    )}
                </SpotlightSurface>

                {/* Student Submission Status Card */}
                {!isInstructor && submission && (
                    <SpotlightSurface accent="teal" className="!rounded-2xl p-6 space-y-3">
                        <div className="flex flex-wrap items-center justify-between gap-3">
                            <h3 className="flex items-center gap-2 font-display text-lg font-semibold text-vl-ink">
                                <CheckCircle2 className="h-5 w-5 text-teal-400" />
                                Your Submission
                            </h3>
                            <StatusPill tone={submission.status === 'graded' ? 'teal' : 'sky'}>
                                {submission.status}
                            </StatusPill>
                        </div>
                        {submission.status === 'graded' && (
                            <div className="rounded-xl bg-vl-bg/80 p-4 space-y-1.5">
                                <p className="font-mono text-lg font-bold text-teal-500 tabular-nums">
                                    Score: {submission.grade ?? 0} / {assignment.max_points}
                                </p>
                                {submission.feedback && (
                                    <p className="text-sm text-vl-ink-secondary">{submission.feedback}</p>
                                )}
                            </div>
                        )}
                    </SpotlightSurface>
                )}

                {/* Instructor Submissions Table */}
                {isInstructor && (
                    <SpotlightSurface className="!rounded-2xl p-6 space-y-4">
                        <h3 className="font-display text-lg font-semibold text-vl-ink">
                            Student Submissions ({submissions.length})
                        </h3>
                        {submissions.length === 0 ? (
                            <p className="py-8 text-center text-sm text-vl-ink-muted">No submissions recorded yet.</p>
                        ) : (
                            <div className="space-y-2.5">
                                {submissions.map((sub) => (
                                    <Link
                                        key={sub.id}
                                        href={`/submissions/${sub.id}`}
                                        className="flex items-center justify-between gap-4 rounded-xl bg-vl-bg/80 px-5 py-3 hover:bg-vl-surface-raised transition-colors"
                                    >
                                        <div className="min-w-0">
                                            <p className="truncate text-sm font-semibold text-vl-ink">
                                                {sub.student?.name ?? 'Student'}
                                            </p>
                                            <p className="truncate text-xs text-vl-ink-muted">
                                                {sub.student?.email}
                                            </p>
                                        </div>
                                        <div className="flex items-center gap-3 shrink-0">
                                            {sub.grade !== null && (
                                                <span className="font-mono text-xs font-semibold text-teal-500 tabular-nums">
                                                    {sub.grade}/{assignment.max_points}
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
                )}
            </div>
        </PortalLayout>
    );
}

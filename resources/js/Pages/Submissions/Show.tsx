import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Code2,
    ShieldCheck,
} from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface SubmissionShowProps {
    submission: {
        id: number;
        status: string;
        grade: number | null;
        feedback?: string | null;
        code_snapshot?: string | null;
        submitted_at?: string | null;
        student?: { id: number; name: string; email: string } | null;
    };
    assignment: {
        id: number;
        title: string;
        max_points: number;
        starter_language?: string | null;
    };
    course: { id: number; title: string; slug: string };
    canGrade: boolean;
    prevSubmission: { id: number; student?: { name: string } | null } | null;
    nextSubmission: { id: number; student?: { name: string } | null } | null;
}

export default function SubmissionShow({
    submission,
    assignment,
    course,
    canGrade,
    prevSubmission,
    nextSubmission,
}: SubmissionShowProps) {
    const form = useForm({
        grade: submission.grade ?? '',
        feedback: submission.feedback ?? '',
    });

    const submitGrade = (e: React.FormEvent) => {
        e.preventDefault();
        form.patch(`/submissions/${submission.id}/grade`);
    };

    return (
        <PortalLayout
            title={`Review — ${submission.student?.name ?? 'Submission'}`}
            pageTitle="Submission Review & Forensics"
        >
            <div className="space-y-6">
                {/* Top Navigation Bar */}
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <Link href="/submissions" className="vl-btn-secondary">
                        <ArrowLeft className="h-4 w-4" />
                        Back to Queue
                    </Link>

                    <div className="flex items-center gap-2">
                        {prevSubmission && (
                            <Link href={`/submissions/${prevSubmission.id}`} className="vl-btn-secondary">
                                <ArrowLeft className="h-3.5 w-3.5" />
                                Prev
                            </Link>
                        )}
                        {nextSubmission && (
                            <Link href={`/submissions/${nextSubmission.id}`} className="vl-btn-secondary">
                                Next
                                <ArrowRight className="h-3.5 w-3.5" />
                            </Link>
                        )}
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    {/* Left 8-col: Code Snapshot */}
                    <SpotlightSurface accent="sky" className="!rounded-2xl p-6 lg:col-span-8 space-y-4">
                        <div className="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 className="font-display text-xl font-semibold text-vl-ink">
                                    {assignment.title}
                                </h2>
                                <p className="text-xs text-vl-ink-muted">
                                    {course.title} · Student: {submission.student?.name ?? 'Student'} ({submission.student?.email})
                                </p>
                            </div>
                            <StatusPill
                                tone={
                                    submission.status === 'graded'
                                        ? 'teal'
                                        : submission.status === 'late'
                                        ? 'rose'
                                        : 'amber'
                                }
                            >
                                {submission.status}
                            </StatusPill>
                        </div>

                        <div className="space-y-2">
                            <div className="flex items-center justify-between text-xs text-vl-ink-muted font-mono">
                                <span className="inline-flex items-center gap-1.5">
                                    <Code2 className="h-3.5 w-3.5 text-sky-400" />
                                    SUBMITTED SNAPSHOT ({assignment.starter_language ?? 'code'})
                                </span>
                                {submission.submitted_at && (
                                    <span>{new Date(submission.submitted_at).toLocaleString()}</span>
                                )}
                            </div>
                            <pre className="max-h-[520px] overflow-auto rounded-xl bg-[#05080F] p-5 font-mono text-xs leading-relaxed text-sky-100 ring-1 ring-white/10">
                                {submission.code_snapshot || '// No code snapshot recorded for this submission.'}
                            </pre>
                        </div>
                    </SpotlightSurface>

                    {/* Right 4-col: Grading Panel & VisionGuard Summary */}
                    <div className="space-y-6 lg:col-span-4">
                        <SpotlightSurface accent="teal" className="!rounded-2xl p-6 space-y-4">
                            <div className="flex items-center gap-2.5">
                                <span className="flex h-9 w-9 items-center justify-center rounded-full bg-teal-500/15 text-teal-400">
                                    <ShieldCheck className="h-4 w-4" />
                                </span>
                                <div>
                                    <h3 className="font-display text-base font-semibold text-vl-ink">
                                        Grade &amp; Rubric Feedback
                                    </h3>
                                    <p className="font-mono text-[11px] text-vl-ink-muted">
                                        Max: {assignment.max_points} points
                                    </p>
                                </div>
                            </div>

                            {canGrade ? (
                                <form onSubmit={submitGrade} className="space-y-4">
                                    <div className="space-y-1.5">
                                        <label htmlFor="grade" className="block text-xs font-medium text-vl-ink-secondary">
                                            Score (0 – {assignment.max_points})
                                        </label>
                                        <input
                                            id="grade"
                                            type="number"
                                            min={0}
                                            max={assignment.max_points}
                                            required
                                            value={form.data.grade}
                                            onChange={(e) => form.setData('grade', e.target.value)}
                                            className="vl-input w-full font-mono tabular-nums"
                                        />
                                        {form.errors.grade && <p className="text-xs text-rose-500">{form.errors.grade}</p>}
                                    </div>

                                    <div className="space-y-1.5">
                                        <label htmlFor="feedback" className="block text-xs font-medium text-vl-ink-secondary">
                                            Instructor Feedback
                                        </label>
                                        <textarea
                                            id="feedback"
                                            rows={5}
                                            value={form.data.feedback}
                                            onChange={(e) => form.setData('feedback', e.target.value)}
                                            placeholder="Highlight architecture strengths, edge cases, or suggestions..."
                                            className="w-full rounded-xl border-0 bg-vl-bg/85 px-4 py-3 text-sm text-vl-ink ring-1 ring-inset ring-vl-border/15 focus:outline-none focus:ring-2 focus:ring-sky-500"
                                        />
                                        {form.errors.feedback && (
                                            <p className="text-xs text-rose-500">{form.errors.feedback}</p>
                                        )}
                                    </div>

                                    <button type="submit" disabled={form.processing} className="vl-btn-primary w-full justify-center">
                                        <CheckCircle2 className="h-4 w-4" />
                                        {form.processing ? 'Saving Grade...' : 'Save Grade & Award XP'}
                                    </button>
                                </form>
                            ) : (
                                <div className="rounded-xl bg-vl-bg/80 p-4 space-y-2">
                                    <p className="font-mono text-xl font-bold text-teal-500 tabular-nums">
                                        {submission.grade !== null
                                            ? `${submission.grade} / ${assignment.max_points}`
                                            : 'Awaiting Faculty Grade'}
                                    </p>
                                    {submission.feedback && (
                                        <p className="text-sm text-vl-ink-secondary">{submission.feedback}</p>
                                    )}
                                </div>
                            )}
                        </SpotlightSurface>
                    </div>
                </div>
            </div>
        </PortalLayout>
    );
}

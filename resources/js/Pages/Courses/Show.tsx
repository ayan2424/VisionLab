import React, { useState } from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import {
    Bell,
    Code2,
    Edit,
    FileCode2,
    Lock,
    Plus,
    Send,
    Trash2,
    Users,
} from 'lucide-react';
import { cn } from '@/lib/cn';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface AssignmentItem {
    id: number;
    title: string;
    description?: string | null;
    max_points: number;
    due_date?: string | null;
    starter_language?: string | null;
    is_locked?: boolean;
}

interface AnnouncementItem {
    id: number;
    title: string;
    body: string;
    created_at: string;
    author?: { name: string } | null;
}

interface CourseShowProps {
    course: {
        id: number;
        title: string;
        slug: string;
        description?: string | null;
        enrollment_code?: string | null;
        duration?: string | null;
        schedule_time?: string | null;
        instructor?: { name: string } | null;
        assignments: AssignmentItem[];
        announcements: AnnouncementItem[];
    };
    isInstructor: boolean;
    students: Array<{ id: number; name: string; email: string; student_id?: string | null }>;
    userSubmissions: Record<number, { id: number; status: string; grade: number | null } | null>;
    tab: string;
}

export default function CourseShow({
    course,
    isInstructor,
    students,
    userSubmissions,
    tab: initialTab,
}: CourseShowProps) {
    const [activeTab, setActiveTab] = useState<'stream' | 'assignments' | 'people'>(
        initialTab === 'assignments' || initialTab === 'people' ? initialTab : 'stream'
    );

    const announcementForm = useForm({ title: '', body: '' });

    const postAnnouncement = (e: React.FormEvent) => {
        e.preventDefault();
        announcementForm.post(`/courses/${course.slug}/announcements`, {
            onSuccess: () => announcementForm.reset(),
        });
    };

    const deleteCourse = () => {
        if (!window.confirm(`Permanently delete ${course.title}?`)) return;
        router.delete(`/courses/${course.slug}`);
    };

    return (
        <PortalLayout title={course.title} pageTitle={course.title}>
            <div className="space-y-6">
                {/* Course Hero Header */}
                <div className="rounded-xl bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-7 text-white shadow-xl">
                    <div className="flex flex-wrap items-start justify-between gap-6">
                        <div className="space-y-2 max-w-2xl">
                            <div className="flex flex-wrap items-center gap-2 font-mono text-xs text-sky-100">
                                <span>{course.instructor?.name ?? 'Department Faculty'}</span>
                                {course.enrollment_code && (
                                    <span className="rounded-full bg-white/20 px-3 py-0.5 font-semibold backdrop-blur-md">
                                        Code: {course.enrollment_code}
                                    </span>
                                )}
                                {course.schedule_time && <span>· {course.schedule_time}</span>}
                            </div>
                            <h2 className="font-display text-3xl font-semibold tracking-tight">{course.title}</h2>
                            {course.description && (
                                <p className="text-sm leading-relaxed text-sky-100/90">{course.description}</p>
                            )}
                        </div>

                        {isInstructor && (
                            <div className="flex flex-wrap items-center gap-2">
                                <Link
                                    href={`/courses/${course.slug}/assignments/create`}
                                    className="inline-flex items-center gap-1.5 rounded-full bg-white px-4 py-2 text-xs font-semibold text-blue-700 hover:bg-sky-50 transition-colors"
                                >
                                    <Plus className="h-3.5 w-3.5" />
                                    New Assignment
                                </Link>
                                <Link
                                    href={`/courses/${course.slug}/roster`}
                                    className="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-4 py-2 text-xs font-semibold text-white hover:bg-white/30 transition-colors"
                                >
                                    <Users className="h-3.5 w-3.5" />
                                    Roster &amp; VSIX
                                </Link>
                                <Link
                                    href={`/courses/${course.slug}/edit`}
                                    className="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30 transition-colors"
                                    title="Edit course"
                                >
                                    <Edit className="h-4 w-4" />
                                </Link>
                                <button
                                    type="button"
                                    onClick={deleteCourse}
                                    className="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-500/30 text-white hover:bg-rose-500/50 transition-colors"
                                    title="Delete course"
                                >
                                    <Trash2 className="h-4 w-4" />
                                </button>
                            </div>
                        )}
                    </div>
                </div>

                {/* Full-Pill Course Tabs */}
                <div className="inline-flex items-center gap-1 rounded-full bg-vl-surface p-1.5 ring-1 ring-inset ring-vl-border/15">
                    {[
                        { id: 'stream' as const, label: `Stream (${course.announcements.length})`, icon: Bell },
                        { id: 'assignments' as const, label: `Assignments (${course.assignments.length})`, icon: Code2 },
                        { id: 'people' as const, label: `Students (${students.length})`, icon: Users },
                    ].map((t) => {
                        const Icon = t.icon;
                        const active = activeTab === t.id;
                        return (
                            <button
                                key={t.id}
                                type="button"
                                onClick={() => setActiveTab(t.id)}
                                className={cn(
                                    'inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold transition-colors',
                                    active
                                        ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-sm'
                                        : 'text-vl-ink-secondary hover:text-vl-ink'
                                )}
                            >
                                <Icon className="h-3.5 w-3.5" />
                                {t.label}
                            </button>
                        );
                    })}
                </div>

                {/* Tab Content */}
                {activeTab === 'stream' && (
                    <div className="space-y-5">
                        {isInstructor && (
                            <SpotlightSurface accent="sky" className="!rounded-2xl p-6">
                                <form onSubmit={postAnnouncement} className="space-y-3">
                                    <h3 className="font-display text-sm font-semibold text-vl-ink">
                                        Post Course Announcement
                                    </h3>
                                    <input
                                        type="text"
                                        required
                                        placeholder="Announcement headline"
                                        value={announcementForm.data.title}
                                        onChange={(e) => announcementForm.setData('title', e.target.value)}
                                        className="vl-input w-full"
                                    />
                                    <textarea
                                        rows={3}
                                        required
                                        placeholder="Share lab instructions, exam policies, or schedule updates..."
                                        value={announcementForm.data.body}
                                        onChange={(e) => announcementForm.setData('body', e.target.value)}
                                        className="w-full rounded-xl border-0 bg-vl-bg/85 px-4 py-3 text-sm text-vl-ink ring-1 ring-inset ring-vl-border/15 focus:outline-none focus:ring-2 focus:ring-sky-500"
                                    />
                                    <button type="submit" disabled={announcementForm.processing} className="vl-btn-primary">
                                        <Send className="h-3.5 w-3.5" />
                                        Post Notice
                                    </button>
                                </form>
                            </SpotlightSurface>
                        )}

                        {course.announcements.length === 0 ? (
                            <SpotlightSurface className="!rounded-2xl p-10 text-center text-sm text-vl-ink-muted">
                                No announcements posted in this course yet.
                            </SpotlightSurface>
                        ) : (
                            course.announcements.map((ann) => (
                                <SpotlightSurface key={ann.id} className="!rounded-2xl p-6 space-y-2">
                                    <div className="flex items-center justify-between gap-3">
                                        <h4 className="font-display text-base font-semibold text-vl-ink">{ann.title}</h4>
                                        <span className="font-mono text-[11px] text-vl-ink-muted">
                                            {ann.author?.name ?? 'Instructor'} · {new Date(ann.created_at).toLocaleDateString()}
                                        </span>
                                    </div>
                                    <p className="whitespace-pre-line text-sm leading-relaxed text-vl-ink-secondary">
                                        {ann.body}
                                    </p>
                                </SpotlightSurface>
                            ))
                        )}
                    </div>
                )}

                {activeTab === 'assignments' && (
                    <div className="space-y-4">
                        {course.assignments.length === 0 ? (
                            <SpotlightSurface className="!rounded-2xl p-10 text-center space-y-3">
                                <FileCode2 className="mx-auto h-8 w-8 text-vl-ink-muted" />
                                <p className="text-sm text-vl-ink-muted">No assignments published in this course yet.</p>
                                {isInstructor && (
                                    <MagneticButton href={`/courses/${course.slug}/assignments/create`} size="sm" variant="primary">
                                        Create First Assignment
                                    </MagneticButton>
                                )}
                            </SpotlightSurface>
                        ) : (
                            course.assignments.map((a) => {
                                const sub = userSubmissions[a.id];
                                return (
                                    <Link key={a.id} href={`/assignments/${a.id}`} className="block group">
                                        <SpotlightSurface accent="sky" className="!rounded-2xl p-5 flex flex-wrap items-center justify-between gap-4">
                                            <div className="space-y-1 min-w-0">
                                                <div className="flex flex-wrap items-center gap-2">
                                                    <h4 className="font-display text-base font-semibold text-vl-ink group-hover:text-sky-500 transition-colors">
                                                        {a.title}
                                                    </h4>
                                                    {a.is_locked && (
                                                        <StatusPill tone="amber">
                                                            <Lock className="h-3 w-3" />
                                                            Exam Lock
                                                        </StatusPill>
                                                    )}
                                                </div>
                                                <p className="font-mono text-xs text-vl-ink-muted">
                                                    {a.starter_language ?? 'python'} · {a.max_points} pts ·{' '}
                                                    {a.due_date ? `Due ${new Date(a.due_date).toLocaleDateString()}` : 'Open deadline'}
                                                </p>
                                            </div>

                                            <div className="flex items-center gap-3">
                                                {sub && (
                                                    <StatusPill tone={sub.status === 'graded' ? 'teal' : 'sky'}>
                                                        {sub.status === 'graded'
                                                            ? `${sub.grade ?? 0}/${a.max_points}`
                                                            : sub.status}
                                                    </StatusPill>
                                                )}
                                                <span className="rounded-full bg-vl-surface-raised px-4 py-1.5 text-xs font-semibold text-sky-500">
                                                    Open Lab
                                                </span>
                                            </div>
                                        </SpotlightSurface>
                                    </Link>
                                );
                            })
                        )}
                    </div>
                )}

                {activeTab === 'people' && (
                    <SpotlightSurface className="!rounded-2xl p-6 space-y-4">
                        <div className="flex items-center justify-between">
                            <h3 className="font-display text-lg font-semibold text-vl-ink">
                                Enrolled Students ({students.length})
                            </h3>
                            {isInstructor && (
                                <Link href={`/courses/${course.slug}/roster`} className="vl-btn-primary text-xs">
                                    Manage Roster
                                </Link>
                            )}
                        </div>
                        {students.length === 0 ? (
                            <p className="py-8 text-center text-sm text-vl-ink-muted">No students enrolled yet.</p>
                        ) : (
                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                {students.map((st) => (
                                    <div key={st.id} className="flex items-center justify-between rounded-xl bg-vl-bg/80 px-4 py-3">
                                        <div className="min-w-0">
                                            <p className="truncate text-sm font-semibold text-vl-ink">{st.name}</p>
                                            <p className="truncate text-xs text-vl-ink-muted">{st.email}</p>
                                        </div>
                                        {st.student_id && (
                                            <span className="font-mono text-[11px] text-sky-500">{st.student_id}</span>
                                        )}
                                    </div>
                                ))}
                            </div>
                        )}
                    </SpotlightSurface>
                )}
            </div>
        </PortalLayout>
    );
}

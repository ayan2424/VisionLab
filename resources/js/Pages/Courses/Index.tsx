import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { ArrowUpRight, BookOpen, Calendar, Plus, Users } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';
import type { SharedProps } from '@/types';

interface CourseCard {
    id: number;
    title: string;
    slug: string;
    description?: string | null;
    enrollment_code?: string | null;
    duration?: string | null;
    is_active: boolean;
    students_count?: number;
    instructor?: { name: string } | null;
}

interface CoursesIndexProps {
    courses: {
        data: CourseCard[];
        links?: Array<{ url: string | null; label: string; active: boolean }>;
    };
}

export default function CoursesIndex({ courses }: CoursesIndexProps) {
    const { props } = usePage<SharedProps>();
    const role = props.auth.user?.role;
    const canCreate = role === 'admin' || role === 'instructor';

    return (
        <PortalLayout title="Courses" pageTitle="Courses & Curriculum">
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">
                            {canCreate ? 'Managed Courses' : 'Enrolled Courses'}
                        </h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Access course announcements, Nix lab assignments, grading rubrics, and enrollment rosters.
                        </p>
                    </div>

                    <div className="flex flex-wrap items-center gap-2.5">
                        {role === 'student' && (
                            <MagneticButton href="/join" size="sm" variant="primary">
                                <Plus className="h-4 w-4" />
                                Join Course by Code
                            </MagneticButton>
                        )}
                        {canCreate && (
                            <MagneticButton href="/courses/create" size="sm" variant="primary">
                                <Plus className="h-4 w-4" />
                                Create Course
                            </MagneticButton>
                        )}
                    </div>
                </div>

                {courses.data.length === 0 ? (
                    <SpotlightSurface className="!rounded-xl p-12 text-center space-y-4">
                        <BookOpen className="mx-auto h-9 w-9 text-vl-ink-muted" />
                        <p className="text-sm text-vl-ink-secondary">
                            {canCreate
                                ? 'No courses created yet. Create your first course to start onboarding students.'
                                : 'You are not enrolled in any active courses yet.'}
                        </p>
                        <div className="pt-1">
                            <Link
                                href={canCreate ? '/courses/create' : '/join'}
                                className="vl-btn-primary inline-flex"
                            >
                                {canCreate ? 'Create Course' : 'Enter Enrollment Code'}
                            </Link>
                        </div>
                    </SpotlightSurface>
                ) : (
                    <div className="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        {courses.data.map((course) => (
                            <Link
                                key={course.id}
                                href={`/courses/${course.slug}`}
                                prefetch="hover"
                                className="group block"
                            >
                                <SpotlightSurface accent="sky" className="h-full !rounded-2xl p-6 flex flex-col justify-between gap-5">
                                    <div className="space-y-3">
                                        <div className="flex items-center justify-between gap-2">
                                            <span className="flex h-10 w-10 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                                                <BookOpen className="h-5 w-5" />
                                            </span>
                                            <StatusPill tone={course.is_active ? 'teal' : 'neutral'}>
                                                {course.is_active ? 'Active' : 'Archived'}
                                            </StatusPill>
                                        </div>

                                        <div>
                                            <h3 className="font-display text-lg font-semibold text-vl-ink group-hover:text-sky-500 transition-colors">
                                                {course.title}
                                            </h3>
                                            {course.description && (
                                                <p className="mt-1.5 line-clamp-2 text-xs leading-relaxed text-vl-ink-secondary">
                                                    {course.description}
                                                </p>
                                            )}
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between border-t border-vl-border/10 pt-4 text-xs text-vl-ink-muted">
                                        <div className="flex items-center gap-3">
                                            <span className="inline-flex items-center gap-1 tabular-nums">
                                                <Users className="h-3.5 w-3.5 text-sky-400" />
                                                {course.students_count ?? 0}
                                            </span>
                                            {course.duration && (
                                                <span className="inline-flex items-center gap-1">
                                                    <Calendar className="h-3.5 w-3.5 text-teal-400" />
                                                    {course.duration}
                                                </span>
                                            )}
                                        </div>
                                        <span className="inline-flex items-center gap-1 font-semibold text-sky-500">
                                            Open <ArrowUpRight className="h-3.5 w-3.5" />
                                        </span>
                                    </div>
                                </SpotlightSurface>
                            </Link>
                        ))}
                    </div>
                )}

                {courses.links && courses.links.length > 3 && (
                    <div className="flex flex-wrap justify-center gap-2 pt-2">
                        {courses.links.map((link, idx) =>
                            link.url ? (
                                <Link
                                    key={idx}
                                    href={link.url}
                                    className={`rounded-full px-4 py-1.5 text-xs font-semibold transition-colors ${
                                        link.active
                                            ? 'bg-sky-500 text-white'
                                            : 'bg-vl-surface text-vl-ink-secondary hover:text-vl-ink'
                                    }`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ) : null
                        )}
                    </div>
                )}
            </div>
        </PortalLayout>
    );
}

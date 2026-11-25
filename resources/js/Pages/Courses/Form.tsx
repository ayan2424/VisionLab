import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface CourseRecord {
    id: number;
    slug: string;
    title: string;
    description?: string | null;
    duration?: string | null;
    is_active: boolean;
    allow_marketplace: boolean;
    start_date?: string | null;
    end_date?: string | null;
    schedule_time?: string | null;
    notes?: string | null;
}

export default function CourseForm({ course }: { course?: CourseRecord | null }) {
    const isEdit = Boolean(course);
    const form = useForm({
        title: course?.title ?? '',
        description: course?.description ?? '',
        duration: course?.duration ?? '',
        schedule_time: course?.schedule_time ?? '',
        start_date: course?.start_date ? course.start_date.slice(0, 10) : '',
        end_date: course?.end_date ? course.end_date.slice(0, 10) : '',
        notes: course?.notes ?? '',
        is_active: course?.is_active ?? true,
        allow_marketplace: course?.allow_marketplace ?? true,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit && course) {
            form.put(`/courses/${course.slug}`);
        } else {
            form.post('/courses');
        }
    };

    return (
        <PortalLayout
            title={isEdit ? `Edit ${course?.title}` : 'Create Course'}
            pageTitle={isEdit ? 'Edit Course' : 'Create Course'}
        >
            <div className="mx-auto max-w-3xl">
                <SpotlightSurface accent="sky" className="!rounded-xl p-8 space-y-6">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">
                            {isEdit ? 'Update Course Configuration' : 'Launch a New Academic Course'}
                        </h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Define curriculum details, schedule windows, and container extension permissions.
                        </p>
                    </div>

                    <form onSubmit={submit} className="space-y-5">
                        <div className="space-y-1.5">
                            <label htmlFor="title" className="block text-xs font-medium text-vl-ink-secondary">
                                Course Title
                            </label>
                            <input
                                id="title"
                                type="text"
                                required
                                value={form.data.title}
                                onChange={(e) => form.setData('title', e.target.value)}
                                placeholder="e.g. CS-301 Operating Systems"
                                className="vl-input w-full"
                            />
                            {form.errors.title && <p className="text-xs text-rose-500">{form.errors.title}</p>}
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="description" className="block text-xs font-medium text-vl-ink-secondary">
                                Description (minimum 10 characters)
                            </label>
                            <textarea
                                id="description"
                                rows={4}
                                required
                                value={form.data.description}
                                onChange={(e) => form.setData('description', e.target.value)}
                                placeholder="Overview of learning outcomes, compilers, and lab expectations..."
                                className="w-full rounded-xl border-0 bg-vl-bg/85 px-4 py-3 text-sm text-vl-ink ring-1 ring-inset ring-vl-border/15 focus:outline-none focus:ring-2 focus:ring-sky-500"
                            />
                            {form.errors.description && <p className="text-xs text-rose-500">{form.errors.description}</p>}
                        </div>

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5">
                                <label htmlFor="duration" className="block text-xs font-medium text-vl-ink-secondary">
                                    Duration
                                </label>
                                <input
                                    id="duration"
                                    type="text"
                                    value={form.data.duration}
                                    onChange={(e) => form.setData('duration', e.target.value)}
                                    placeholder="e.g. 16 Weeks"
                                    className="vl-input w-full"
                                />
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="schedule_time" className="block text-xs font-medium text-vl-ink-secondary">
                                    Lab Schedule
                                </label>
                                <input
                                    id="schedule_time"
                                    type="text"
                                    value={form.data.schedule_time}
                                    onChange={(e) => form.setData('schedule_time', e.target.value)}
                                    placeholder="e.g. Mon / Wed 10:00 AM"
                                    className="vl-input w-full"
                                />
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="start_date" className="block text-xs font-medium text-vl-ink-secondary">
                                    Start Date
                                </label>
                                <input
                                    id="start_date"
                                    type="date"
                                    value={form.data.start_date}
                                    onChange={(e) => form.setData('start_date', e.target.value)}
                                    className="vl-input w-full"
                                />
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="end_date" className="block text-xs font-medium text-vl-ink-secondary">
                                    End Date
                                </label>
                                <input
                                    id="end_date"
                                    type="date"
                                    value={form.data.end_date}
                                    onChange={(e) => form.setData('end_date', e.target.value)}
                                    className="vl-input w-full"
                                />
                                {form.errors.end_date && <p className="text-xs text-rose-500">{form.errors.end_date}</p>}
                            </div>
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="notes" className="block text-xs font-medium text-vl-ink-secondary">
                                Instructor Notes
                            </label>
                            <textarea
                                id="notes"
                                rows={3}
                                value={form.data.notes}
                                onChange={(e) => form.setData('notes', e.target.value)}
                                className="w-full rounded-xl border-0 bg-vl-bg/85 px-4 py-3 text-sm text-vl-ink ring-1 ring-inset ring-vl-border/15 focus:outline-none focus:ring-2 focus:ring-sky-500"
                            />
                        </div>

                        <div className="flex flex-wrap gap-6 pt-1">
                            <label className="flex items-center gap-2 text-xs font-medium text-vl-ink-secondary cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={form.data.is_active}
                                    onChange={(e) => form.setData('is_active', e.target.checked)}
                                    className="h-4 w-4 rounded border-vl-border/25 text-sky-500"
                                />
                                Course Active
                            </label>
                            <label className="flex items-center gap-2 text-xs font-medium text-vl-ink-secondary cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={form.data.allow_marketplace}
                                    onChange={(e) => form.setData('allow_marketplace', e.target.checked)}
                                    className="h-4 w-4 rounded border-vl-border/25 text-sky-500"
                                />
                                Allow Approved Course Extensions
                            </label>
                        </div>

                        <div className="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" disabled={form.processing} className="vl-btn-primary">
                                <Save className="h-4 w-4" />
                                {form.processing ? 'Saving...' : isEdit ? 'Save Course Changes' : 'Create Course'}
                            </button>
                            <Link
                                href={isEdit && course ? `/courses/${course.slug}` : '/courses'}
                                className="vl-btn-secondary"
                            >
                                <ArrowLeft className="h-4 w-4" />
                                Cancel
                            </Link>
                        </div>
                    </form>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

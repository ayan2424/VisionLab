import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface TemplateOpt {
    id: number;
    name: string;
    language: string;
}

interface AssignmentRecord {
    id: number;
    title: string;
    description?: string | null;
    max_points: number;
    due_date?: string | null;
    starter_code?: string | null;
    starter_language: string;
    template_id?: number | null;
    is_locked?: boolean;
    course?: { id: number; slug: string; title: string };
}

const LANGUAGES = ['python', 'javascript', 'typescript', 'php', 'java', 'c', 'cpp', 'rust', 'go', 'ruby', 'bash'];

export default function AssignmentForm({
    course,
    assignment,
    templates,
}: {
    course?: { id: number; slug: string; title: string };
    assignment?: AssignmentRecord | null;
    templates: TemplateOpt[];
}) {
    const isEdit = Boolean(assignment);
    const parentCourse = course ?? assignment?.course;

    const form = useForm({
        title: assignment?.title ?? '',
        description: assignment?.description ?? '',
        max_points: assignment?.max_points ?? 100,
        due_date: assignment?.due_date ? assignment.due_date.slice(0, 16) : '',
        starter_language: assignment?.starter_language ?? 'python',
        template_id: assignment?.template_id ? String(assignment.template_id) : '',
        starter_code: assignment?.starter_code ?? '',
        is_locked: Boolean(assignment?.is_locked ?? false),
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit && assignment) {
            form.put(`/assignments/${assignment.id}`);
        } else if (parentCourse) {
            form.post(`/courses/${parentCourse.slug}/assignments`);
        }
    };

    return (
        <PortalLayout
            title={isEdit ? `Edit ${assignment?.title}` : 'New Assignment'}
            pageTitle={isEdit ? 'Edit Assignment' : 'Create Lab Assignment'}
        >
            <div className="mx-auto max-w-3xl">
                <SpotlightSurface accent="sky" className="!rounded-xl p-8 space-y-6">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">
                            {isEdit ? 'Update Assignment Spec' : `New Assignment for ${parentCourse?.title ?? 'Course'}`}
                        </h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Configure starter code, pinned workspace template, point cap, and optional Exam Lockdown mode.
                        </p>
                    </div>

                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-1.5">
                            <label htmlFor="title" className="block text-xs font-medium text-vl-ink-secondary">
                                Assignment Title
                            </label>
                            <input
                                id="title"
                                type="text"
                                required
                                value={form.data.title}
                                onChange={(e) => form.setData('title', e.target.value)}
                                placeholder="e.g. Lab 04 — Custom Slab Memory Allocator"
                                className="vl-input w-full"
                            />
                            {form.errors.title && <p className="text-xs text-rose-500">{form.errors.title}</p>}
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="description" className="block text-xs font-medium text-vl-ink-secondary">
                                Instructions &amp; Rubric
                            </label>
                            <textarea
                                id="description"
                                rows={4}
                                value={form.data.description}
                                onChange={(e) => form.setData('description', e.target.value)}
                                className="w-full rounded-xl border-0 bg-vl-bg/85 px-4 py-3 text-sm text-vl-ink ring-1 ring-inset ring-vl-border/15 focus:outline-none focus:ring-2 focus:ring-sky-500"
                            />
                        </div>

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div className="space-y-1.5">
                                <label htmlFor="max_points" className="block text-xs font-medium text-vl-ink-secondary">
                                    Max Points
                                </label>
                                <input
                                    id="max_points"
                                    type="number"
                                    min={1}
                                    max={1000}
                                    required
                                    value={form.data.max_points}
                                    onChange={(e) => form.setData('max_points', Number(e.target.value))}
                                    className="vl-input w-full tabular-nums"
                                />
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="starter_language" className="block text-xs font-medium text-vl-ink-secondary">
                                    Language
                                </label>
                                <select
                                    id="starter_language"
                                    value={form.data.starter_language}
                                    onChange={(e) => form.setData('starter_language', e.target.value)}
                                    className="vl-input w-full"
                                >
                                    {LANGUAGES.map((lang) => (
                                        <option key={lang} value={lang}>
                                            {lang}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="due_date" className="block text-xs font-medium text-vl-ink-secondary">
                                    Due Date
                                </label>
                                <input
                                    id="due_date"
                                    type="datetime-local"
                                    value={form.data.due_date}
                                    onChange={(e) => form.setData('due_date', e.target.value)}
                                    className="vl-input w-full"
                                />
                            </div>
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="template_id" className="block text-xs font-medium text-vl-ink-secondary">
                                Workspace Nix Template (optional)
                            </label>
                            <select
                                id="template_id"
                                value={form.data.template_id}
                                onChange={(e) => form.setData('template_id', e.target.value)}
                                className="vl-input w-full"
                            >
                                <option value="">Default course container</option>
                                {templates.map((tpl) => (
                                    <option key={tpl.id} value={tpl.id}>
                                        {tpl.name} ({tpl.language})
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="starter_code" className="block text-xs font-medium text-vl-ink-secondary">
                                Starter Code
                            </label>
                            <textarea
                                id="starter_code"
                                rows={6}
                                value={form.data.starter_code}
                                onChange={(e) => form.setData('starter_code', e.target.value)}
                                className="w-full rounded-xl border-0 bg-[#05080F] px-4 py-3 font-mono text-xs text-sky-200 ring-1 ring-inset ring-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500"
                            />
                        </div>

                        <label className="flex items-center gap-2.5 text-xs font-medium text-vl-ink-secondary cursor-pointer">
                            <input
                                type="checkbox"
                                checked={form.data.is_locked}
                                onChange={(e) => form.setData('is_locked', e.target.checked)}
                                className="h-4 w-4 rounded text-amber-500"
                            />
                            Enable VisionGuard Exam Lockdown
                        </label>

                        <div className="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" disabled={form.processing} className="vl-btn-primary">
                                <Save className="h-4 w-4" />
                                {form.processing ? 'Saving...' : isEdit ? 'Save Assignment' : 'Publish Assignment'}
                            </button>
                            <Link
                                href={
                                    isEdit && assignment
                                        ? `/assignments/${assignment.id}`
                                        : parentCourse
                                        ? `/courses/${parentCourse.slug}`
                                        : '/courses'
                                }
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

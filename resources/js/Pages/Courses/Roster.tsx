import React from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import { ArrowLeft, Cpu, Mail, Trash2, Upload, UserPlus } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface RosterProps {
    course: { id: number; title: string; slug: string; enrollment_code?: string | null };
    enrollments: {
        data: Array<{
            id: number;
            status: string;
            student?: { id: number; name: string; email: string; student_id?: string | null };
        }>;
    };
    extensions: Array<{ id: number; name: string; package_identifier: string; version: string }>;
    availableStudents: Array<{ id: number; name: string; student_id?: string | null }>;
    courseExtensionIds?: number[];
}

export default function CourseRoster({
    course,
    enrollments,
    extensions,
    availableStudents,
    courseExtensionIds = [],
}: RosterProps) {
    const idForm = useForm({ student_id: '' });
    const emailForm = useForm({ email: '' });
    const csvForm = useForm<{ csv_file: File | null }>({ csv_file: null });
    const extForm = useForm<{ extensions: number[] }>({ extensions: courseExtensionIds });

    const toggleExt = (id: number) => {
        const exists = extForm.data.extensions.includes(id);
        extForm.setData(
            'extensions',
            exists ? extForm.data.extensions.filter((x) => x !== id) : [...extForm.data.extensions, id]
        );
    };

    return (
        <PortalLayout title={`Roster — ${course.title}`} pageTitle="Roster & Course Extensions">
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">{course.title}</h2>
                        <p className="font-mono text-xs text-vl-ink-muted">
                            Enrollment Code: {course.enrollment_code ?? '—'}
                        </p>
                    </div>
                    <Link href={`/courses/${course.slug}`} className="vl-btn-secondary">
                        <ArrowLeft className="h-4 w-4" />
                        Back to Course
                    </Link>
                </div>

                {/* Add / Invite / Import Row */}
                <div className="grid grid-cols-1 gap-5 lg:grid-cols-3">
                    <SpotlightSurface accent="sky" className="!rounded-2xl p-6 space-y-3">
                        <h3 className="flex items-center gap-2 font-display text-sm font-semibold text-vl-ink">
                            <UserPlus className="h-4 w-4 text-sky-400" />
                            Enroll by Student ID
                        </h3>
                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                idForm.post(`/courses/${course.slug}/enrollments/add`, {
                                    onSuccess: () => idForm.reset(),
                                });
                            }}
                            className="space-y-3"
                        >
                            <select
                                value={idForm.data.student_id}
                                onChange={(e) => idForm.setData('student_id', e.target.value)}
                                required
                                className="vl-input w-full"
                            >
                                <option value="">Select available student...</option>
                                {availableStudents.map((s) => (
                                    <option key={s.id} value={s.student_id ?? ''}>
                                        {s.name} ({s.student_id ?? 'No ID'})
                                    </option>
                                ))}
                            </select>
                            {idForm.errors.student_id && <p className="text-xs text-rose-500">{idForm.errors.student_id}</p>}
                            <button type="submit" disabled={idForm.processing} className="vl-btn-primary w-full justify-center">
                                Enroll Student
                            </button>
                        </form>
                    </SpotlightSurface>

                    <SpotlightSurface className="!rounded-2xl p-6 space-y-3">
                        <h3 className="flex items-center gap-2 font-display text-sm font-semibold text-vl-ink">
                            <Mail className="h-4 w-4 text-teal-400" />
                            Invite by Email
                        </h3>
                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                emailForm.post(`/courses/${course.slug}/invite`, {
                                    onSuccess: () => emailForm.reset(),
                                });
                            }}
                            className="space-y-3"
                        >
                            <input
                                type="email"
                                required
                                placeholder="student@university.edu"
                                value={emailForm.data.email}
                                onChange={(e) => emailForm.setData('email', e.target.value)}
                                className="vl-input w-full"
                            />
                            {emailForm.errors.email && <p className="text-xs text-rose-500">{emailForm.errors.email}</p>}
                            <button type="submit" disabled={emailForm.processing} className="vl-btn-primary w-full justify-center">
                                Send Invite
                            </button>
                        </form>
                    </SpotlightSurface>

                    <SpotlightSurface className="!rounded-2xl p-6 space-y-3">
                        <h3 className="flex items-center gap-2 font-display text-sm font-semibold text-vl-ink">
                            <Upload className="h-4 w-4 text-amber-400" />
                            Batch CSV Import
                        </h3>
                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                csvForm.post(`/courses/${course.slug}/import-csv`, {
                                    forceFormData: true,
                                    onSuccess: () => csvForm.reset(),
                                });
                            }}
                            className="space-y-3"
                        >
                            <input
                                type="file"
                                accept=".csv,.txt"
                                required
                                onChange={(e) => csvForm.setData('csv_file', e.target.files?.[0] ?? null)}
                                className="block w-full text-xs text-vl-ink-secondary file:mr-3 file:rounded-full file:border-0 file:bg-sky-500/15 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-sky-600 dark:file:text-sky-300"
                            />
                            {csvForm.errors.csv_file && <p className="text-xs text-rose-500">{csvForm.errors.csv_file}</p>}
                            <button type="submit" disabled={csvForm.processing} className="vl-btn-primary w-full justify-center">
                                Upload Roster CSV
                            </button>
                        </form>
                    </SpotlightSurface>
                </div>

                {/* Enrolled Table & Course Extensions */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <SpotlightSurface className="!rounded-2xl p-6 lg:col-span-7 space-y-4">
                        <h3 className="font-display text-lg font-semibold text-vl-ink">
                            Active Roster ({enrollments.data.length})
                        </h3>
                        <div className="space-y-2.5">
                            {enrollments.data.length === 0 ? (
                                <p className="py-8 text-center text-sm text-vl-ink-muted">No students enrolled yet.</p>
                            ) : (
                                enrollments.data.map((enr) => (
                                    <div key={enr.id} className="flex items-center justify-between gap-3 rounded-xl bg-vl-bg/80 px-5 py-3">
                                        <div className="min-w-0">
                                            <p className="truncate text-sm font-semibold text-vl-ink">
                                                {enr.student?.name ?? 'Student'}
                                            </p>
                                            <p className="truncate font-mono text-[11px] text-vl-ink-muted">
                                                {enr.student?.email} · {enr.student?.student_id ?? '—'}
                                            </p>
                                        </div>
                                        <div className="flex items-center gap-2 shrink-0">
                                            <StatusPill tone={enr.status === 'active' ? 'teal' : 'amber'}>
                                                {enr.status}
                                            </StatusPill>
                                            {enr.student && (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        router.delete(`/courses/${course.slug}/students/${enr.student!.id}`)
                                                    }
                                                    className="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-500/15 text-rose-500 hover:bg-rose-500/25"
                                                    title="Remove student"
                                                >
                                                    <Trash2 className="h-3.5 w-3.5" />
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                    </SpotlightSurface>

                    <SpotlightSurface accent="teal" className="!rounded-2xl p-6 lg:col-span-5 space-y-4">
                        <div className="flex items-center justify-between">
                            <h3 className="flex items-center gap-2 font-display text-lg font-semibold text-vl-ink">
                                <Cpu className="h-4 w-4 text-teal-400" />
                                Course VSIX Extensions
                            </h3>
                        </div>

                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                extForm.put(`/courses/${course.slug}/extensions`);
                            }}
                            className="space-y-3"
                        >
                            <div className="max-h-72 space-y-2 overflow-y-auto pr-1">
                                {extensions.map((ext) => (
                                    <label
                                        key={ext.id}
                                        className="flex items-center justify-between gap-3 rounded-xl bg-vl-bg/80 px-4 py-3 cursor-pointer"
                                    >
                                        <div className="min-w-0">
                                            <p className="truncate text-xs font-semibold text-vl-ink">{ext.name}</p>
                                            <p className="truncate font-mono text-[10px] text-vl-ink-muted">
                                                {ext.package_identifier} · v{ext.version}
                                            </p>
                                        </div>
                                        <input
                                            type="checkbox"
                                            checked={extForm.data.extensions.includes(ext.id)}
                                            onChange={() => toggleExt(ext.id)}
                                            className="h-4 w-4 rounded text-teal-500"
                                        />
                                    </label>
                                ))}
                            </div>
                            <button type="submit" disabled={extForm.processing} className="vl-btn-primary w-full justify-center">
                                Save Extension Policy
                            </button>
                        </form>
                    </SpotlightSurface>
                </div>
            </div>
        </PortalLayout>
    );
}

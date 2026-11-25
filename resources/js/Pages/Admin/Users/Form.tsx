import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save, UserPlus } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface CourseWithBatches {
    id: number;
    title: string;
    batches?: Array<{ id: number; title: string; timing?: string | null }>;
}

interface AdminUserFormProps {
    user?: {
        id: number;
        name: string;
        email: string;
        role: 'admin' | 'instructor' | 'student';
        status: 'active' | 'suspended';
        student_id?: string | null;
    } | null;
    courses?: CourseWithBatches[];
}

export default function AdminUserForm({ user, courses = [] }: AdminUserFormProps) {
    const isEdit = Boolean(user);

    const form = useForm({
        name: user?.name ?? '',
        email: user?.email ?? '',
        password: '',
        password_confirmation: '',
        role: (user?.role ?? 'student') as 'admin' | 'instructor' | 'student',
        status: (user?.status ?? 'active') as 'active' | 'suspended',
        student_id: user?.student_id ?? '',
        course_id: '',
        course_batch_id: '',
    });

    const selectedCourse = courses.find((c) => String(c.id) === form.data.course_id);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit && user) {
            form.put(`/admin/users/${user.id}`);
        } else {
            form.post('/admin/users');
        }
    };

    return (
        <PortalLayout
            title={isEdit ? `Edit ${user?.name}` : 'Provision Account'}
            pageTitle={isEdit ? 'Edit Campus Account' : 'Provision Campus Account'}
            adminMode
        >
            <div className="mx-auto max-w-2xl">
                <SpotlightSurface accent="sky" className="p-7 space-y-6">
                    <div className="flex items-center gap-3">
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                            <UserPlus className="h-5 w-5" />
                        </span>
                        <div>
                            <h2 className="font-display text-xl font-semibold text-vl-ink">
                                {isEdit ? `Update ${user?.name}` : 'Create Verified Institutional Account'}
                            </h2>
                            <p className="text-xs text-vl-ink-secondary">
                                Public registration is disabled by policy. Accounts are provisioned directly by campus admins.
                            </p>
                        </div>
                    </div>

                    <form onSubmit={submit} className="space-y-4">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5">
                                <label htmlFor="name" className="block text-xs font-medium text-vl-ink-secondary">Full Name</label>
                                <input
                                    id="name"
                                    type="text"
                                    required
                                    value={form.data.name}
                                    onChange={(e) => form.setData('name', e.target.value)}
                                    className="vl-input w-full"
                                />
                                {form.errors.name && <p className="text-xs text-rose-500">{form.errors.name}</p>}
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="email" className="block text-xs font-medium text-vl-ink-secondary">Email</label>
                                <input
                                    id="email"
                                    type="email"
                                    required
                                    value={form.data.email}
                                    onChange={(e) => form.setData('email', e.target.value)}
                                    className="vl-input w-full"
                                />
                                {form.errors.email && <p className="text-xs text-rose-500">{form.errors.email}</p>}
                            </div>
                        </div>

                        {!isEdit && (
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-1.5">
                                    <label htmlFor="password" className="block text-xs font-medium text-vl-ink-secondary">Password</label>
                                    <input
                                        id="password"
                                        type="password"
                                        required
                                        value={form.data.password}
                                        onChange={(e) => form.setData('password', e.target.value)}
                                        className="vl-input w-full"
                                    />
                                    {form.errors.password && <p className="text-xs text-rose-500">{form.errors.password}</p>}
                                </div>
                                <div className="space-y-1.5">
                                    <label htmlFor="password_confirmation" className="block text-xs font-medium text-vl-ink-secondary">Confirm Password</label>
                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        required
                                        value={form.data.password_confirmation}
                                        onChange={(e) => form.setData('password_confirmation', e.target.value)}
                                        className="vl-input w-full"
                                    />
                                </div>
                            </div>
                        )}

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5">
                                <label htmlFor="role" className="block text-xs font-medium text-vl-ink-secondary">Role</label>
                                <select
                                    id="role"
                                    value={form.data.role}
                                    onChange={(e) => form.setData('role', e.target.value as 'admin' | 'instructor' | 'student')}
                                    className="vl-input w-full"
                                >
                                    <option value="student">Student</option>
                                    <option value="instructor">Faculty / Instructor</option>
                                    <option value="admin">Campus Admin</option>
                                </select>
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="status" className="block text-xs font-medium text-vl-ink-secondary">Status</label>
                                <select
                                    id="status"
                                    value={form.data.status}
                                    onChange={(e) => form.setData('status', e.target.value as 'active' | 'suspended')}
                                    className="vl-input w-full"
                                >
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>

                        {isEdit && (
                            <div className="space-y-1.5">
                                <label htmlFor="student_id" className="block text-xs font-medium text-vl-ink-secondary">Student ID</label>
                                <input
                                    id="student_id"
                                    type="text"
                                    value={form.data.student_id}
                                    onChange={(e) => form.setData('student_id', e.target.value)}
                                    className="vl-input w-full font-mono"
                                />
                                {form.errors.student_id && <p className="text-xs text-rose-500">{form.errors.student_id}</p>}
                            </div>
                        )}

                        {!isEdit && form.data.role === 'student' && courses.length > 0 && (
                            <div className="grid grid-cols-1 gap-4 rounded-xl bg-vl-bg/80 p-4 sm:grid-cols-2">
                                <div className="space-y-1.5">
                                    <label htmlFor="course_id" className="block text-xs font-medium text-vl-ink-secondary">
                                        Initial Course Enrollment (optional)
                                    </label>
                                    <select
                                        id="course_id"
                                        value={form.data.course_id}
                                        onChange={(e) => {
                                            form.setData('course_id', e.target.value);
                                            form.setData('course_batch_id', '');
                                        }}
                                        className="vl-input w-full"
                                    >
                                        <option value="">None</option>
                                        {courses.map((c) => (
                                            <option key={c.id} value={c.id}>{c.title}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="space-y-1.5">
                                    <label htmlFor="course_batch_id" className="block text-xs font-medium text-vl-ink-secondary">
                                        Course Batch (optional)
                                    </label>
                                    <select
                                        id="course_batch_id"
                                        value={form.data.course_batch_id}
                                        onChange={(e) => form.setData('course_batch_id', e.target.value)}
                                        disabled={!selectedCourse?.batches?.length}
                                        className="vl-input w-full disabled:opacity-50"
                                    >
                                        <option value="">Select batch...</option>
                                        {selectedCourse?.batches?.map((b) => (
                                            <option key={b.id} value={b.id}>
                                                {b.title} {b.timing ? `(${b.timing})` : ''}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                        )}

                        <div className="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" disabled={form.processing} className="vl-btn-primary">
                                <Save className="h-4 w-4" />
                                {form.processing ? 'Saving...' : isEdit ? 'Update Account' : 'Provision Account'}
                            </button>
                            <Link href="/admin/users" className="vl-btn-secondary">
                                <ArrowLeft className="h-4 w-4" />
                                Back to Users
                            </Link>
                        </div>
                    </form>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

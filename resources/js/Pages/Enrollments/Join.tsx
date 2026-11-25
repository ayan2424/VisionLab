import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, GraduationCap, KeyRound } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

export default function JoinCourse() {
    const form = useForm({ enrollment_code: '' });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/join');
    };

    return (
        <PortalLayout title="Join Course" pageTitle="Join a Course">
            <div className="mx-auto max-w-lg py-8">
                <SpotlightSurface accent="sky" className="!rounded-xl p-8 space-y-6">
                    <div className="flex items-center gap-3.5">
                        <span className="flex h-12 w-12 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                            <GraduationCap className="h-6 w-6" />
                        </span>
                        <div>
                            <h2 className="font-display text-xl font-semibold text-vl-ink">
                                Enter Course Enrollment Code
                            </h2>
                            <p className="text-xs text-vl-ink-secondary">
                                Get the 6-character code from your instructor to unlock course labs &amp; Nix profiles.
                            </p>
                        </div>
                    </div>

                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-1.5">
                            <label htmlFor="enrollment_code" className="block text-xs font-medium text-vl-ink-secondary">
                                Enrollment Code
                            </label>
                            <div className="relative">
                                <KeyRound className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-vl-ink-muted" />
                                <input
                                    id="enrollment_code"
                                    type="text"
                                    required
                                    autoFocus
                                    value={form.data.enrollment_code}
                                    onChange={(e) => form.setData('enrollment_code', e.target.value.toUpperCase())}
                                    placeholder="e.g. CS301A"
                                    className="vl-input w-full pl-11 font-mono uppercase tracking-widest"
                                />
                            </div>
                            {form.errors.enrollment_code && (
                                <p className="text-xs text-rose-500">{form.errors.enrollment_code}</p>
                            )}
                        </div>

                        <div className="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" disabled={form.processing} className="vl-btn-primary">
                                {form.processing ? 'Enrolling...' : 'Enroll in Course'}
                            </button>
                            <Link href="/courses" className="vl-btn-secondary">
                                <ArrowLeft className="h-4 w-4" />
                                My Courses
                            </Link>
                        </div>
                    </form>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

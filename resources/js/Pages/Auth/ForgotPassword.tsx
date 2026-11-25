import React from 'react';
import { Link, useForm, usePage } from '@inertiajs/react';
import { ArrowLeft, Mail, Send } from 'lucide-react';
import { AuthLayout } from '@/layouts/AuthLayout';
import type { SharedProps } from '@/types';

export default function ForgotPassword() {
    const { props } = usePage<SharedProps>();
    const form = useForm({ email: '' });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/forgot-password');
    };

    return (
        <AuthLayout title="Reset Password">
            <div className="space-y-6">
                <div className="space-y-2">
                    <h2 className="font-display text-2xl font-semibold tracking-tight text-vl-ink">
                        Reset your campus password
                    </h2>
                    <p className="text-sm text-vl-ink-secondary">
                        Enter your institutional email address and we will send a secure password recovery link.
                    </p>
                </div>

                {props.flash.status && (
                    <div className="rounded-xl bg-teal-500/15 px-4 py-3 text-xs font-medium text-teal-600 dark:text-teal-300">
                        {props.flash.status}
                    </div>
                )}

                <form onSubmit={submit} className="space-y-4">
                    <div className="space-y-1.5">
                        <label htmlFor="email" className="block text-xs font-medium text-vl-ink-secondary">
                            Institutional Email
                        </label>
                        <div className="relative">
                            <Mail className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-vl-ink-muted" />
                            <input
                                id="email"
                                type="email"
                                required
                                autoFocus
                                value={form.data.email}
                                onChange={(e) => form.setData('email', e.target.value)}
                                placeholder="name@university.edu"
                                className="vl-input w-full pl-11"
                            />
                        </div>
                        {form.errors.email && (
                            <p className="text-xs text-rose-500">{form.errors.email}</p>
                        )}
                    </div>

                    <button
                        type="submit"
                        disabled={form.processing}
                        className="vl-btn-primary w-full justify-center"
                    >
                        <Send className="h-4 w-4" />
                        {form.processing ? 'Sending recovery link...' : 'Email Reset Link'}
                    </button>
                </form>

                <div className="pt-2 text-center">
                    <Link
                        href="/login"
                        className="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-secondary hover:text-sky-500 transition-colors"
                    >
                        <ArrowLeft className="h-3.5 w-3.5" />
                        Back to Sign in
                    </Link>
                </div>
            </div>
        </AuthLayout>
    );
}

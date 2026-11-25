import React from 'react';
import { router, useForm, usePage } from '@inertiajs/react';
import { LogOut, MailCheck, Send } from 'lucide-react';
import { AuthLayout } from '@/layouts/AuthLayout';
import type { SharedProps } from '@/types';

export default function VerifyEmail() {
    const { props } = usePage<SharedProps>();
    const form = useForm({});

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/email/verification-notification');
    };

    return (
        <AuthLayout title="Verify Email">
            <div className="space-y-6">
                <div className="inline-flex h-12 w-12 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                    <MailCheck className="h-6 w-6" />
                </div>

                <div className="space-y-2">
                    <h2 className="font-display text-2xl font-semibold tracking-tight text-vl-ink">
                        Verify your campus email
                    </h2>
                    <p className="text-sm leading-relaxed text-vl-ink-secondary">
                        Before accessing your cloud workspaces, please click the verification link we sent to your email address.
                    </p>
                </div>

                {props.flash.status === 'verification-link-sent' && (
                    <div className="rounded-xl bg-teal-500/15 px-4 py-3 text-xs font-medium text-teal-600 dark:text-teal-300">
                        A fresh verification link has been sent to your registered email address.
                    </div>
                )}

                <form onSubmit={submit} className="space-y-3">
                    <button
                        type="submit"
                        disabled={form.processing}
                        className="vl-btn-primary w-full justify-center"
                    >
                        <Send className="h-4 w-4" />
                        {form.processing ? 'Sending...' : 'Resend Verification Email'}
                    </button>

                    <button
                        type="button"
                        onClick={() => router.post('/logout')}
                        className="inline-flex w-full items-center justify-center gap-2 rounded-full bg-vl-surface-raised px-5 py-2.5 text-xs font-semibold text-vl-ink-secondary hover:text-vl-ink transition-colors"
                    >
                        <LogOut className="h-3.5 w-3.5" />
                        Sign out
                    </button>
                </form>
            </div>
        </AuthLayout>
    );
}

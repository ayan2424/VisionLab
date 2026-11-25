import React from 'react';
import { useForm } from '@inertiajs/react';
import { KeyRound, Lock, Mail } from 'lucide-react';
import { AuthLayout } from '@/layouts/AuthLayout';

export default function ResetPassword({
    token,
    email,
}: {
    token: string;
    email: string;
}) {
    const form = useForm({
        token,
        email: email || '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/reset-password', {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };

    return (
        <AuthLayout title="Set New Password">
            <div className="space-y-6">
                <div className="space-y-2">
                    <h2 className="font-display text-2xl font-semibold tracking-tight text-vl-ink">
                        Choose a new password
                    </h2>
                    <p className="text-sm text-vl-ink-secondary">
                        Confirm your account email and enter a strong institutional password.
                    </p>
                </div>

                <form onSubmit={submit} className="space-y-4">
                    <div className="space-y-1.5">
                        <label htmlFor="email" className="block text-xs font-medium text-vl-ink-secondary">
                            Email
                        </label>
                        <div className="relative">
                            <Mail className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-vl-ink-muted" />
                            <input
                                id="email"
                                type="email"
                                required
                                value={form.data.email}
                                onChange={(e) => form.setData('email', e.target.value)}
                                className="vl-input w-full pl-11"
                            />
                        </div>
                        {form.errors.email && <p className="text-xs text-rose-500">{form.errors.email}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <label htmlFor="password" className="block text-xs font-medium text-vl-ink-secondary">
                            New Password
                        </label>
                        <div className="relative">
                            <Lock className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-vl-ink-muted" />
                            <input
                                id="password"
                                type="password"
                                required
                                autoFocus
                                value={form.data.password}
                                onChange={(e) => form.setData('password', e.target.value)}
                                className="vl-input w-full pl-11"
                            />
                        </div>
                        {form.errors.password && <p className="text-xs text-rose-500">{form.errors.password}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <label htmlFor="password_confirmation" className="block text-xs font-medium text-vl-ink-secondary">
                            Confirm Password
                        </label>
                        <div className="relative">
                            <KeyRound className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-vl-ink-muted" />
                            <input
                                id="password_confirmation"
                                type="password"
                                required
                                value={form.data.password_confirmation}
                                onChange={(e) => form.setData('password_confirmation', e.target.value)}
                                className="vl-input w-full pl-11"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        disabled={form.processing}
                        className="vl-btn-primary w-full justify-center"
                    >
                        {form.processing ? 'Updating password...' : 'Reset Password'}
                    </button>
                </form>
            </div>
        </AuthLayout>
    );
}

import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowRight, Lock } from 'lucide-react';
import { AuthLayout } from '@/layouts/AuthLayout';
import { MagneticButton } from '@/components/ui/Primitives';

export default function Login({
    status,
    canResetPassword = true,
}: {
    status?: string | null;
    canResetPassword?: boolean;
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/login', {
            onFinish: () => reset('password'),
        });
    };

    return (
        <AuthLayout title="Sign in to Portal">
            <div className="space-y-7">
                <div className="space-y-2">
                    <h1 className="font-display text-3xl font-semibold tracking-[-0.03em] text-vl-ink">
                        Sign in to Portal
                    </h1>
                    <p className="text-sm text-vl-ink-secondary">
                        Use your institutionally provisioned VisionLab credentials.
                    </p>
                </div>

                {status && (
                    <div className="rounded-full bg-teal-500/12 px-5 py-3 text-xs font-medium text-teal-600 dark:text-teal-300">
                        {status}
                    </div>
                )}

                <form onSubmit={submit} className="space-y-5">
                    <div>
                        <label htmlFor="email" className="mb-2 block text-xs font-medium text-vl-ink-secondary">
                            Institutional Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            autoComplete="username"
                            autoFocus
                            required
                            placeholder="student@university.edu"
                            className="vl-input w-full"
                        />
                        {errors.email && <p className="mt-1.5 text-xs text-rose-400">{errors.email}</p>}
                    </div>

                    <div>
                        <div className="mb-2 flex items-center justify-between">
                            <label htmlFor="password" className="block text-xs font-medium text-vl-ink-secondary">
                                Password
                            </label>
                            {canResetPassword && (
                                <a
                                    href="/forgot-password"
                                    className="text-xs font-medium text-sky-500 hover:text-sky-400 transition-colors"
                                >
                                    Forgot password?
                                </a>
                            )}
                        </div>
                        <input
                            id="password"
                            type="password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            autoComplete="current-password"
                            required
                            placeholder="Enter your password"
                            className="vl-input w-full"
                        />
                        {errors.password && <p className="mt-1.5 text-xs text-rose-400">{errors.password}</p>}
                    </div>

                    <label className="flex items-center gap-2.5 text-xs text-vl-ink-secondary cursor-pointer select-none">
                        <input
                            type="checkbox"
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                            className="h-4 w-4 rounded border-vl-border/30 bg-vl-surface text-sky-500 focus:ring-sky-400"
                        />
                        Keep me signed in on this workstation
                    </label>

                    <MagneticButton
                        type="submit"
                        size="lg"
                        variant="primary"
                        disabled={processing}
                        className="w-full"
                    >
                        <Lock className="h-4 w-4" />
                        {processing ? 'Authenticating...' : 'Sign in to Portal'}
                        <ArrowRight className="h-4 w-4" />
                    </MagneticButton>
                </form>

                <div className="rounded-2xl bg-vl-surface/80 p-5 text-xs leading-relaxed text-vl-ink-secondary">
                    Accounts are provisioned by campus administrators. Need access for a department?{' '}
                    <Link href="/contact" prefetch="hover" className="font-medium text-sky-500 hover:underline">
                        Request a Campus Pilot
                    </Link>
                    .
                </div>
            </div>
        </AuthLayout>
    );
}

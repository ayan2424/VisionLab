import React from 'react';
import { useForm } from '@inertiajs/react';
import { Lock, ShieldCheck } from 'lucide-react';
import { AuthLayout } from '@/layouts/AuthLayout';

export default function ConfirmPassword() {
    const form = useForm({ password: '' });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/confirm-password', {
            onFinish: () => form.reset('password'),
        });
    };

    return (
        <AuthLayout title="Confirm Password">
            <div className="space-y-6">
                <div className="inline-flex h-12 w-12 items-center justify-center rounded-full bg-amber-500/15 text-amber-500">
                    <ShieldCheck className="h-6 w-6" />
                </div>

                <div className="space-y-2">
                    <h2 className="font-display text-2xl font-semibold tracking-tight text-vl-ink">
                        Confirm security access
                    </h2>
                    <p className="text-sm text-vl-ink-secondary">
                        This is a protected institutional zone. Please verify your password before continuing.
                    </p>
                </div>

                <form onSubmit={submit} className="space-y-4">
                    <div className="space-y-1.5">
                        <label htmlFor="password" className="block text-xs font-medium text-vl-ink-secondary">
                            Password
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

                    <button
                        type="submit"
                        disabled={form.processing}
                        className="vl-btn-primary w-full justify-center"
                    >
                        {form.processing ? 'Verifying...' : 'Confirm Password'}
                    </button>
                </form>
            </div>
        </AuthLayout>
    );
}

import React from 'react';
import { useForm } from '@inertiajs/react';
import { KeyRound, Save, ShieldAlert, User } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface ProfileEditProps {
    user: {
        id: number;
        name: string;
        email: string;
        role: string;
        student_id?: string | null;
    };
}

export default function ProfileEdit({ user }: ProfileEditProps) {
    const profileForm = useForm({
        name: user.name,
        email: user.email,
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const deleteForm = useForm({
        password: '',
    });

    return (
        <PortalLayout title="Account Settings" pageTitle="Profile & Security">
            <div className="mx-auto max-w-3xl space-y-6">
                {/* Profile Details */}
                <SpotlightSurface accent="sky" className="!rounded-2xl p-7 space-y-5">
                    <div className="flex items-center gap-3">
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                            <User className="h-5 w-5" />
                        </span>
                        <div>
                            <h2 className="font-display text-lg font-semibold text-vl-ink">Profile Information</h2>
                            <p className="text-xs text-vl-ink-secondary">
                                Update your institutional display name and email address.
                            </p>
                        </div>
                    </div>

                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            profileForm.patch('/profile');
                        }}
                        className="space-y-4"
                    >
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5">
                                <label htmlFor="name" className="block text-xs font-medium text-vl-ink-secondary">
                                    Full Name
                                </label>
                                <input
                                    id="name"
                                    type="text"
                                    required
                                    value={profileForm.data.name}
                                    onChange={(e) => profileForm.setData('name', e.target.value)}
                                    className="vl-input w-full"
                                />
                                {profileForm.errors.name && <p className="text-xs text-rose-500">{profileForm.errors.name}</p>}
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="email" className="block text-xs font-medium text-vl-ink-secondary">
                                    Email Address
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    required
                                    value={profileForm.data.email}
                                    onChange={(e) => profileForm.setData('email', e.target.value)}
                                    className="vl-input w-full"
                                />
                                {profileForm.errors.email && <p className="text-xs text-rose-500">{profileForm.errors.email}</p>}
                            </div>
                        </div>

                        <button type="submit" disabled={profileForm.processing} className="vl-btn-primary">
                            <Save className="h-4 w-4" />
                            {profileForm.processing ? 'Saving...' : 'Save Changes'}
                        </button>
                    </form>
                </SpotlightSurface>

                {/* Password Update */}
                <SpotlightSurface accent="teal" className="!rounded-2xl p-7 space-y-5">
                    <div className="flex items-center gap-3">
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-teal-500/15 text-teal-500">
                            <KeyRound className="h-5 w-5" />
                        </span>
                        <div>
                            <h2 className="font-display text-lg font-semibold text-vl-ink">Update Password</h2>
                            <p className="text-xs text-vl-ink-secondary">
                                Use a long, unique password to protect your cloud containers.
                            </p>
                        </div>
                    </div>

                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            passwordForm.put('/password', {
                                onSuccess: () => passwordForm.reset(),
                            });
                        }}
                        className="space-y-4"
                    >
                        <div className="space-y-1.5">
                            <label htmlFor="current_password" className="block text-xs font-medium text-vl-ink-secondary">
                                Current Password
                            </label>
                            <input
                                id="current_password"
                                type="password"
                                required
                                value={passwordForm.data.current_password}
                                onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                className="vl-input w-full"
                            />
                            {passwordForm.errors.current_password && (
                                <p className="text-xs text-rose-500">{passwordForm.errors.current_password}</p>
                            )}
                        </div>

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5">
                                <label htmlFor="new_password" className="block text-xs font-medium text-vl-ink-secondary">
                                    New Password
                                </label>
                                <input
                                    id="new_password"
                                    type="password"
                                    required
                                    value={passwordForm.data.password}
                                    onChange={(e) => passwordForm.setData('password', e.target.value)}
                                    className="vl-input w-full"
                                />
                                {passwordForm.errors.password && (
                                    <p className="text-xs text-rose-500">{passwordForm.errors.password}</p>
                                )}
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="password_confirmation" className="block text-xs font-medium text-vl-ink-secondary">
                                    Confirm New Password
                                </label>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    value={passwordForm.data.password_confirmation}
                                    onChange={(e) => passwordForm.setData('password_confirmation', e.target.value)}
                                    className="vl-input w-full"
                                />
                            </div>
                        </div>

                        <button type="submit" disabled={passwordForm.processing} className="vl-btn-primary">
                            {passwordForm.processing ? 'Updating...' : 'Update Password'}
                        </button>
                    </form>
                </SpotlightSurface>

                {/* Danger Zone */}
                <SpotlightSurface className="!rounded-2xl p-7 space-y-4">
                    <div className="flex items-center gap-3">
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-rose-500/15 text-rose-500">
                            <ShieldAlert className="h-5 w-5" />
                        </span>
                        <div>
                            <h2 className="font-display text-lg font-semibold text-vl-ink">Delete Account</h2>
                            <p className="text-xs text-vl-ink-secondary">
                                Permanently delete your account and associated workspace records.
                            </p>
                        </div>
                    </div>

                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            if (!window.confirm('Permanently delete your account? This cannot be undone.')) return;
                            deleteForm.delete('/profile');
                        }}
                        className="flex flex-wrap items-center gap-3"
                    >
                        <input
                            type="password"
                            required
                            placeholder="Confirm current password"
                            value={deleteForm.data.password}
                            onChange={(e) => deleteForm.setData('password', e.target.value)}
                            className="vl-input flex-1 min-w-[220px]"
                        />
                        <button
                            type="submit"
                            disabled={deleteForm.processing}
                            className="inline-flex items-center gap-2 rounded-full bg-rose-500 px-5 py-2.5 text-xs font-semibold text-white hover:bg-rose-600 transition-colors"
                        >
                            Delete Account
                        </button>
                    </form>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

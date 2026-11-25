import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { Plus, Search, ShieldOff, UserCheck } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface UserRecord {
    id: number;
    name: string;
    email: string;
    role: string;
    status: string;
    student_id: string | null;
    created_at: string;
}

interface PaginatedUsers {
    data: UserRecord[];
    current_page: number;
    last_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

export default function AdminUsersIndex({
    users,
    filters,
}: {
    users: PaginatedUsers;
    filters: { search?: string; role?: string; status?: string };
}) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [role, setRole] = useState(filters.role ?? '');
    const [status, setStatus] = useState(filters.status ?? '');

    const applyFilter = (next: { search?: string; role?: string; status?: string }) => {
        const merged = { search, role, status, ...next };
        router.get('/admin/users', merged, { preserveState: true, replace: true });
    };

    const toggleStatus = (u: UserRecord) => {
        const endpoint = u.status === 'suspended' ? `/admin/users/${u.id}/activate` : `/admin/users/${u.id}/suspend`;
        router.post(endpoint, {}, { preserveScroll: true });
    };

    return (
        <PortalLayout title="Users & Roles" pageTitle="User Directory" adminMode>
            <div className="space-y-6">
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">Institutional Accounts</h2>
                        <p className="text-sm text-vl-ink-secondary">
                            {users.total.toLocaleString()} provisioned accounts across all roles.
                        </p>
                    </div>
                    <MagneticButton href="/admin/users/create" external size="sm" variant="primary">
                        <Plus className="h-3.5 w-3.5" />
                        Provision User
                    </MagneticButton>
                </div>

                {/* Instant Inertia Filter Bar */}
                <SpotlightSurface className="p-4">
                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-12">
                        <div className="relative sm:col-span-6">
                            <Search className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-vl-ink-muted" />
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => {
                                    setSearch(e.target.value);
                                    applyFilter({ search: e.target.value });
                                }}
                                placeholder="Search by name or email..."
                                className="vl-input w-full !pl-10"
                            />
                        </div>
                        <div className="sm:col-span-3">
                            <select
                                value={role}
                                onChange={(e) => {
                                    setRole(e.target.value);
                                    applyFilter({ role: e.target.value });
                                }}
                                className="vl-input w-full"
                            >
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="instructor">Instructor</option>
                                <option value="student">Student</option>
                            </select>
                        </div>
                        <div className="sm:col-span-3">
                            <select
                                value={status}
                                onChange={(e) => {
                                    setStatus(e.target.value);
                                    applyFilter({ status: e.target.value });
                                }}
                                className="vl-input w-full"
                            >
                                <option value="">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                </SpotlightSurface>

                {/* Users Table */}
                <SpotlightSurface className="overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead className="border-b border-vl-border/12 text-xs text-vl-ink-muted">
                                <tr>
                                    <th className="px-5 py-3.5 font-medium">User</th>
                                    <th className="px-5 py-3.5 font-medium">Student ID</th>
                                    <th className="px-5 py-3.5 font-medium">Role</th>
                                    <th className="px-5 py-3.5 font-medium">Status</th>
                                    <th className="px-5 py-3.5 text-right font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-vl-border/10">
                                {users.data.map((u) => (
                                    <tr key={u.id} className="hover:bg-vl-surface-raised/40 transition-colors">
                                        <td className="px-5 py-3.5">
                                            <p className="font-medium text-vl-ink">{u.name}</p>
                                            <p className="text-xs text-vl-ink-muted">{u.email}</p>
                                        </td>
                                        <td className="px-5 py-3.5 font-mono text-xs text-vl-ink-secondary">
                                            {u.student_id || '—'}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <StatusPill tone={u.role === 'admin' ? 'amber' : u.role === 'instructor' ? 'teal' : 'sky'}>
                                                {u.role}
                                            </StatusPill>
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <StatusPill tone={u.status === 'suspended' ? 'rose' : 'teal'}>
                                                {u.status}
                                            </StatusPill>
                                        </td>
                                        <td className="px-5 py-3.5 text-right">
                                            <div className="inline-flex items-center gap-2">
                                                <a
                                                    href={`/admin/users/${u.id}/edit`}
                                                    className="rounded-full bg-vl-bg/80 px-2.5 py-1.5 text-xs font-medium text-vl-ink-secondary hover:text-vl-ink"
                                                >
                                                    Edit
                                                </a>
                                                <button
                                                    type="button"
                                                    onClick={() => toggleStatus(u)}
                                                    className="inline-flex items-center gap-1 rounded-full bg-vl-bg/80 px-2.5 py-1.5 text-xs font-medium text-vl-ink-secondary hover:text-amber-400"
                                                >
                                                    {u.status === 'suspended' ? (
                                                        <>
                                                            <UserCheck className="h-3.5 w-3.5 text-teal-400" />
                                                            Activate
                                                        </>
                                                    ) : (
                                                        <>
                                                            <ShieldOff className="h-3.5 w-3.5 text-rose-400" />
                                                            Suspend
                                                        </>
                                                    )}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

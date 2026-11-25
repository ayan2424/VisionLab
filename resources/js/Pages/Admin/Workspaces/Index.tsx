import React from 'react';
import { Link, router } from '@inertiajs/react';
import { ExternalLink, Square, Terminal } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface AdminWorkspaceItem {
    id: number;
    name: string;
    slug: string;
    status: string;
    language: string;
    port: number | null;
    owner?: { name: string; email: string } | null;
    course?: { title: string } | null;
}

export default function AdminWorkspacesIndex({
    workspaces,
    statusFilter,
}: {
    workspaces: { data: AdminWorkspaceItem[]; total: number };
    statusFilter?: string;
}) {
    const filterBy = (nextStatus: string) => {
        router.get('/admin/workspaces', nextStatus ? { status: nextStatus } : {}, {
            preserveState: true,
            replace: true,
        });
    };

    const forceStop = (slug: string) => {
        if (!window.confirm(`Force stop container ${slug}?`)) return;
        router.post(`/admin/workspaces/${slug}/stop`, {}, { preserveScroll: true });
    };

    return (
        <PortalLayout title="Workspace Fleet" pageTitle="Container Workspaces" adminMode>
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">Container Fleet</h2>
                        <p className="text-sm text-vl-ink-secondary">
                            {workspaces.total} workspaces tracked across campus cohorts.
                        </p>
                    </div>

                    <div className="flex items-center gap-1.5 rounded-xl bg-vl-surface p-1">
                        {['', 'running', 'stopped', 'pending', 'archived'].map((st) => (
                            <button
                                key={st || 'all'}
                                type="button"
                                onClick={() => filterBy(st)}
                                className={`rounded-full px-3 py-1.5 text-xs font-medium capitalize transition-colors ${
                                    (statusFilter || '') === st
                                        ? 'bg-sky-500/20 text-vl-ink'
                                        : 'text-vl-ink-muted hover:text-vl-ink'
                                }`}
                            >
                                {st || 'All'}
                            </button>
                        ))}
                    </div>
                </div>

                <SpotlightSurface className="overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead className="border-b border-vl-border/15 text-xs text-vl-ink-muted">
                                <tr>
                                    <th className="px-5 py-3.5 font-medium">Workspace</th>
                                    <th className="px-5 py-3.5 font-medium">Owner</th>
                                    <th className="px-5 py-3.5 font-medium">Course</th>
                                    <th className="px-5 py-3.5 font-medium">Status</th>
                                    <th className="px-5 py-3.5 text-right font-medium">Controls</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-vl-border/10">
                                {workspaces.data.map((ws) => (
                                    <tr key={ws.id} className="hover:bg-vl-surface-raised/40 transition-colors">
                                        <td className="px-5 py-3.5">
                                            <div className="flex items-center gap-2.5">
                                                <Terminal className="h-4 w-4 text-sky-400 shrink-0" />
                                                <div>
                                                    <p className="font-mono text-xs font-semibold text-vl-ink">{ws.slug}</p>
                                                    <p className="text-[11px] text-vl-ink-muted">{ws.language}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <p className="text-xs font-medium text-vl-ink">{ws.owner?.name ?? '—'}</p>
                                            <p className="text-[11px] text-vl-ink-muted">{ws.owner?.email ?? ''}</p>
                                        </td>
                                        <td className="px-5 py-3.5 text-xs text-vl-ink-secondary">
                                            {ws.course?.title ?? 'Personal Lab'}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <StatusPill
                                                tone={ws.status === 'running' ? 'teal' : ws.status === 'error' ? 'rose' : 'neutral'}
                                                pulse={ws.status === 'running'}
                                            >
                                                {ws.status}
                                            </StatusPill>
                                        </td>
                                        <td className="px-5 py-3.5 text-right">
                                            <div className="inline-flex items-center gap-2">
                                                <Link
                                                    href={`/admin/workspaces/${ws.slug}`}
                                                    className="inline-flex items-center gap-1 rounded-full bg-vl-bg/80 px-3 py-1.5 text-xs font-medium text-vl-ink-secondary hover:text-vl-ink"
                                                >
                                                    Inspect
                                                    <ExternalLink className="h-3 w-3" />
                                                </Link>
                                                {ws.status === 'running' && (
                                                    <button
                                                        type="button"
                                                        onClick={() => forceStop(ws.slug)}
                                                        className="inline-flex items-center gap-1 rounded-full bg-rose-500/15 px-3 py-1.5 text-xs font-medium text-rose-400 hover:bg-rose-500/25"
                                                    >
                                                        <Square className="h-3 w-3" />
                                                        Stop
                                                    </button>
                                                )}
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

import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Archive, ArrowLeft, Cpu, ShieldCheck, Square, Trash2 } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface AdminWorkspaceShowProps {
    workspace: {
        id: number;
        name: string;
        slug: string;
        language: string;
        status: string;
        port?: number | null;
        owner?: { name: string; email: string } | null;
        course?: { title: string } | null;
    };
    auditLogs: Array<{ id: number; action: string; created_at: string; actor?: { name: string } | null }>;
    allExtensions: Array<{ id: number; name: string; package_identifier: string; version: string }>;
    workspaceExtMap: Record<number, { is_enabled: boolean; sync_status?: string }>;
}

export default function AdminWorkspaceShow({
    workspace,
    auditLogs,
    allExtensions,
    workspaceExtMap: initialMap,
}: AdminWorkspaceShowProps) {
    const [extState, setExtState] = useState<Record<number, boolean>>(() => {
        const map: Record<number, boolean> = {};
        for (const ext of allExtensions) {
            map[ext.id] = Boolean(initialMap[ext.id]?.is_enabled);
        }
        return map;
    });

    const toggleExtension = async (extId: number) => {
        const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
        const res = await fetch(`/admin/workspaces/${workspace.slug}/extensions/${extId}/toggle`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        });
        if (res.ok) {
            const data = await res.json();
            setExtState((prev) => ({ ...prev, [extId]: Boolean(data.is_enabled) }));
        }
    };

    return (
        <PortalLayout title={`Workspace — ${workspace.name}`} pageTitle="Workspace Inspector" adminMode>
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <Link href="/admin/workspaces" className="vl-btn-secondary">
                        <ArrowLeft className="h-4 w-4" />
                        Back to Fleet
                    </Link>

                    <div className="flex flex-wrap items-center gap-2">
                        {workspace.status === 'running' && (
                            <button
                                type="button"
                                onClick={() => router.post(`/admin/workspaces/${workspace.slug}/stop`)}
                                className="inline-flex items-center gap-1.5 rounded-full bg-amber-500/15 px-4 py-2 text-xs font-semibold text-amber-600 dark:text-amber-300 hover:bg-amber-500/25"
                            >
                                <Square className="h-3.5 w-3.5" />
                                Force Stop
                            </button>
                        )}
                        <button
                            type="button"
                            onClick={() => router.post(`/admin/workspaces/${workspace.slug}/archive`)}
                            className="vl-btn-secondary"
                        >
                            <Archive className="h-3.5 w-3.5" />
                            Archive
                        </button>
                        <button
                            type="button"
                            onClick={() => {
                                if (!window.confirm('Permanently wipe this workspace and its storage volume?')) return;
                                router.delete(`/admin/workspaces/${workspace.slug}`);
                            }}
                            className="vl-btn-danger"
                        >
                            <Trash2 className="h-3.5 w-3.5" />
                            Delete Permanently
                        </button>
                    </div>
                </div>

                {/* Metadata Summary */}
                <SpotlightSurface accent="sky" className="p-6">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p className="font-mono text-xs text-sky-500">{workspace.slug}</p>
                            <h2 className="mt-1 font-display text-2xl font-semibold text-vl-ink">{workspace.name}</h2>
                            <p className="mt-1 text-xs text-vl-ink-secondary">
                                Owner: {workspace.owner?.name ?? 'Unknown'} ({workspace.owner?.email ?? '—'}) ·{' '}
                                {workspace.course?.title ?? 'Personal Lab'} · Runtime: {workspace.language}
                            </p>
                        </div>
                        <StatusPill tone={workspace.status === 'running' ? 'teal' : 'neutral'} pulse={workspace.status === 'running'}>
                            {workspace.status}
                        </StatusPill>
                    </div>
                </SpotlightSurface>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    {/* Extension Override Matrix */}
                    <SpotlightSurface accent="teal" className="p-6 lg:col-span-6 space-y-4">
                        <div className="flex items-center justify-between">
                            <h3 className="flex items-center gap-2 font-display text-base font-semibold text-vl-ink">
                                <Cpu className="h-4 w-4 text-teal-400" />
                                Container VSIX Policy
                            </h3>
                            <span className="font-mono text-xs text-vl-ink-muted">{allExtensions.length} available</span>
                        </div>

                        <div className="space-y-2">
                            {allExtensions.map((ext) => {
                                const enabled = Boolean(extState[ext.id]);
                                return (
                                    <div key={ext.id} className="flex items-center justify-between gap-3 rounded-xl bg-vl-bg/80 px-4 py-3">
                                        <div className="min-w-0">
                                            <p className="truncate text-xs font-semibold text-vl-ink">{ext.name}</p>
                                            <p className="truncate font-mono text-[10px] text-vl-ink-muted">
                                                {ext.package_identifier} · v{ext.version}
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => void toggleExtension(ext.id)}
                                            className={`rounded-full px-3.5 py-1 text-[11px] font-semibold transition-colors ${
                                                enabled
                                                    ? 'bg-teal-500 text-white'
                                                    : 'bg-vl-surface-raised text-vl-ink-muted hover:text-vl-ink'
                                            }`}
                                        >
                                            {enabled ? 'Enabled' : 'Disabled'}
                                        </button>
                                    </div>
                                );
                            })}
                        </div>
                    </SpotlightSurface>

                    {/* Audit History */}
                    <SpotlightSurface className="p-6 lg:col-span-6 space-y-4">
                        <div className="flex items-center justify-between">
                            <h3 className="flex items-center gap-2 font-display text-base font-semibold text-vl-ink">
                                <ShieldCheck className="h-4 w-4 text-sky-400" />
                                Workspace Audit Trail
                            </h3>
                        </div>
                        {auditLogs.length === 0 ? (
                            <p className="py-8 text-center text-xs text-vl-ink-muted">No governance audit events for this workspace.</p>
                        ) : (
                            <div className="space-y-2">
                                {auditLogs.map((log) => (
                                    <div key={log.id} className="flex items-center justify-between rounded-xl bg-vl-bg/80 px-4 py-2.5 text-xs">
                                        <div className="min-w-0">
                                            <p className="truncate font-mono font-semibold text-vl-ink">{log.action}</p>
                                            <p className="text-[11px] text-vl-ink-muted">{log.actor?.name ?? 'System'}</p>
                                        </div>
                                        <span className="font-mono text-[10px] text-vl-ink-muted">
                                            {new Date(log.created_at).toLocaleDateString()}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </SpotlightSurface>
                </div>
            </div>
        </PortalLayout>
    );
}

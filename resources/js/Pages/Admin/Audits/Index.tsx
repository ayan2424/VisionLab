import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { FileCode2, Search, ShieldCheck } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface AuditRow {
    id: number;
    action: string;
    resource_type: string;
    resource_id?: number | null;
    ip_address?: string | null;
    created_at: string;
    actor?: { name: string; email: string } | null;
}

export default function AdminAuditsIndex({
    audits,
    filters,
}: {
    audits: { data: AuditRow[]; links?: Array<{ url: string | null; label: string; active: boolean }> };
    filters?: { action?: string };
}) {
    const [action, setAction] = useState(filters?.action ?? '');

    const applySearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/admin/audits', action ? { action } : {}, { preserveState: true });
    };

    return (
        <PortalLayout title="Audit Trail" pageTitle="Immutable Governance Audit Trail" adminMode>
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <form onSubmit={applySearch} className="relative w-full max-w-sm">
                        <Search className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-vl-ink-muted" />
                        <input
                            type="text"
                            value={action}
                            onChange={(e) => setAction(e.target.value)}
                            placeholder="Filter by action name..."
                            className="vl-input w-full !rounded-full pl-11"
                        />
                    </form>
                    <StatusPill tone="teal">
                        <ShieldCheck className="h-3.5 w-3.5" />
                        Append-only log
                    </StatusPill>
                </div>

                <SpotlightSurface className="p-6">
                    {audits.data.length === 0 ? (
                        <div className="py-12 text-center space-y-2">
                            <FileCode2 className="mx-auto h-8 w-8 text-vl-ink-muted" />
                            <p className="text-sm text-vl-ink-secondary">No matching audit entries found.</p>
                        </div>
                    ) : (
                        <div className="space-y-2">
                            {audits.data.map((log) => (
                                <div key={log.id} className="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-vl-bg/80 px-4 py-3">
                                    <div className="min-w-0">
                                        <p className="truncate font-mono text-xs font-semibold text-vl-ink">{log.action}</p>
                                        <p className="truncate text-[11px] text-vl-ink-muted">
                                            {log.actor?.name ?? 'System'} · {log.resource_type}
                                            {log.resource_id ? ` #${log.resource_id}` : ''}
                                            {log.ip_address ? ` · ${log.ip_address}` : ''}
                                        </p>
                                    </div>
                                    <span className="font-mono text-[11px] text-vl-ink-muted tabular-nums">
                                        {new Date(log.created_at).toLocaleString()}
                                    </span>
                                </div>
                            ))}
                        </div>
                    )}
                </SpotlightSurface>

                {audits.links && audits.links.length > 3 && (
                    <div className="flex flex-wrap justify-center gap-2">
                        {audits.links.map((link, idx) =>
                            link.url ? (
                                <Link
                                    key={idx}
                                    href={link.url}
                                    className={`rounded-full px-4 py-1.5 text-xs font-semibold ${
                                        link.active ? 'bg-sky-500 text-white' : 'bg-vl-surface text-vl-ink-secondary'
                                    }`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ) : null
                        )}
                    </div>
                )}
            </div>
        </PortalLayout>
    );
}

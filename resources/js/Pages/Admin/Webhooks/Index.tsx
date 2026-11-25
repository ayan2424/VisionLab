import React from 'react';
import { Link, router } from '@inertiajs/react';
import { Edit, Plus, Trash2, Webhook } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface WebhookRow {
    id: number;
    url: string;
    events: string[];
    is_active: boolean;
}

export default function AdminWebhooksIndex({ webhooks }: { webhooks: WebhookRow[] }) {
    return (
        <PortalLayout title="Webhooks" pageTitle="Outbound Event Webhooks" adminMode>
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">Webhook Endpoints</h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Stream submission, enrollment, and governance audit events to external SIS/LMS integrations.
                        </p>
                    </div>
                    <MagneticButton href="/admin/webhooks/create" size="sm" variant="primary">
                        <Plus className="h-4 w-4" />
                        Add Webhook
                    </MagneticButton>
                </div>

                <SpotlightSurface className="p-6">
                    {webhooks.length === 0 ? (
                        <div className="py-12 text-center space-y-2">
                            <Webhook className="mx-auto h-8 w-8 text-vl-ink-muted" />
                            <p className="text-sm text-vl-ink-secondary">No webhooks configured yet.</p>
                        </div>
                    ) : (
                        <div className="space-y-2.5">
                            {webhooks.map((wh) => (
                                <div key={wh.id} className="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-vl-bg/80 px-5 py-4">
                                    <div className="min-w-0 space-y-1.5">
                                        <p className="truncate font-mono text-xs font-semibold text-vl-ink">{wh.url}</p>
                                        <div className="flex flex-wrap gap-1.5">
                                            {(wh.events ?? []).map((ev) => (
                                                <span key={ev} className="rounded-full bg-sky-500/15 px-2.5 py-0.5 font-mono text-[10px] text-sky-600 dark:text-sky-300">
                                                    {ev}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2 shrink-0">
                                        <StatusPill tone={wh.is_active ? 'teal' : 'neutral'}>
                                            {wh.is_active ? 'Active' : 'Paused'}
                                        </StatusPill>
                                        <Link href={`/admin/webhooks/${wh.id}/edit`} className="inline-flex h-8 w-8 items-center justify-center rounded-full bg-vl-surface-raised text-vl-ink-secondary hover:text-sky-500">
                                            <Edit className="h-3.5 w-3.5" />
                                        </Link>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                if (!window.confirm('Delete this webhook endpoint?')) return;
                                                router.delete(`/admin/webhooks/${wh.id}`);
                                            }}
                                            className="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-500/15 text-rose-500 hover:bg-rose-500/25"
                                        >
                                            <Trash2 className="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

import React from 'react';
import { useForm } from '@inertiajs/react';
import { Save, Sliders } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface QuotaRow {
    id: number;
    scope: string;
    scope_id?: number | null;
    max_workspaces: number;
    memory_mb: number;
    cpu_shares: number;
    disk_mb: number;
    timeout_minutes: number;
}

export default function AdminQuotasIndex({ quotas }: { quotas: QuotaRow[] }) {
    const form = useForm({
        quotas: quotas.map((q) => ({ ...q })),
    });

    const updateField = (idx: number, field: keyof QuotaRow, val: number) => {
        const next = [...form.data.quotas];
        next[idx] = { ...next[idx], [field]: val };
        form.setData('quotas', next);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/admin/quotas');
    };

    return (
        <PortalLayout title="Resource Quotas" pageTitle="Container Resource Quotas" adminMode>
            <form onSubmit={submit} className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">Compute &amp; Storage Limits</h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Enforce per-role memory ceilings, CPU weights, persistent volume sizes, and idle auto-stop timers.
                        </p>
                    </div>
                    <button type="submit" disabled={form.processing} className="vl-btn-primary">
                        <Save className="h-4 w-4" />
                        {form.processing ? 'Applying...' : 'Save Quota Policies'}
                    </button>
                </div>

                {form.data.quotas.length === 0 ? (
                    <SpotlightSurface className="p-12 text-center">
                        <Sliders className="mx-auto h-8 w-8 text-vl-ink-muted" />
                        <p className="mt-2 text-sm text-vl-ink-secondary">No resource quotas seeded yet.</p>
                    </SpotlightSurface>
                ) : (
                    <div className="space-y-4">
                        {form.data.quotas.map((q, idx) => (
                            <SpotlightSurface key={q.id} accent="sky" className="p-6 space-y-4">
                                <div className="flex items-center justify-between">
                                    <h3 className="font-display text-base font-semibold capitalize text-vl-ink">
                                        {q.scope} Scope {q.scope_id ? `#${q.scope_id}` : '(Default)'}
                                    </h3>
                                    <StatusPill tone="sky">Enforced via crun</StatusPill>
                                </div>

                                <div className="grid grid-cols-2 gap-4 sm:grid-cols-5">
                                    {[
                                        { label: 'Max Workspaces', key: 'max_workspaces' as const },
                                        { label: 'Memory (MB)', key: 'memory_mb' as const },
                                        { label: 'CPU Shares', key: 'cpu_shares' as const },
                                        { label: 'Disk (MB)', key: 'disk_mb' as const },
                                        { label: 'Idle Timeout (m)', key: 'timeout_minutes' as const },
                                    ].map((f) => (
                                        <div key={f.key} className="space-y-1">
                                            <label className="block text-[11px] font-medium text-vl-ink-muted">{f.label}</label>
                                            <input
                                                type="number"
                                                value={q[f.key]}
                                                onChange={(e) => updateField(idx, f.key, Number(e.target.value))}
                                                className="vl-input w-full font-mono tabular-nums"
                                            />
                                        </div>
                                    ))}
                                </div>
                            </SpotlightSurface>
                        ))}
                    </div>
                )}
            </form>
        </PortalLayout>
    );
}

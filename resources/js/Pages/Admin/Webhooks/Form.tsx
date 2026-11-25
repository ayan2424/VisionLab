import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

const EVENT_OPTIONS = [
    'submission.created',
    'submission.graded',
    'workspace.started',
    'workspace.stopped',
    'ai.patch_proposed',
    'user.suspended',
];

export default function AdminWebhookForm({
    webhook,
}: {
    webhook?: { id: number; url: string; events: string[]; secret?: string | null; is_active: boolean } | null;
}) {
    const isEdit = Boolean(webhook);
    const form = useForm({
        url: webhook?.url ?? '',
        events: webhook?.events ?? ['submission.graded'],
        secret: webhook?.secret ?? '',
        is_active: webhook?.is_active ?? true,
    });

    const toggleEvent = (ev: string) => {
        const has = form.data.events.includes(ev);
        form.setData('events', has ? form.data.events.filter((x) => x !== ev) : [...form.data.events, ev]);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit && webhook) {
            form.put(`/admin/webhooks/${webhook.id}`);
        } else {
            form.post('/admin/webhooks');
        }
    };

    return (
        <PortalLayout title={isEdit ? 'Edit Webhook' : 'Create Webhook'} pageTitle={isEdit ? 'Edit Webhook' : 'New Webhook'} adminMode>
            <div className="mx-auto max-w-2xl">
                <SpotlightSurface accent="sky" className="p-7 space-y-6">
                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-1.5">
                            <label htmlFor="url" className="block text-xs font-medium text-vl-ink-secondary">Endpoint URL</label>
                            <input id="url" type="url" required placeholder="https://sis.university.edu/hooks/visionlab" value={form.data.url} onChange={(e) => form.setData('url', e.target.value)} className="vl-input w-full font-mono" />
                            {form.errors.url && <p className="text-xs text-rose-500">{form.errors.url}</p>}
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="secret" className="block text-xs font-medium text-vl-ink-secondary">HMAC Signing Secret (optional)</label>
                            <input id="secret" type="text" value={form.data.secret} onChange={(e) => form.setData('secret', e.target.value)} className="vl-input w-full font-mono" />
                        </div>

                        <div className="space-y-2">
                            <p className="text-xs font-medium text-vl-ink-secondary">Subscribed Events</p>
                            <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                {EVENT_OPTIONS.map((ev) => (
                                    <label key={ev} className="flex items-center gap-2.5 rounded-xl bg-vl-bg/80 px-4 py-2.5 text-xs font-mono text-vl-ink cursor-pointer">
                                        <input type="checkbox" checked={form.data.events.includes(ev)} onChange={() => toggleEvent(ev)} className="h-4 w-4 rounded text-sky-500" />
                                        {ev}
                                    </label>
                                ))}
                            </div>
                            {form.errors.events && <p className="text-xs text-rose-500">{form.errors.events}</p>}
                        </div>

                        <label className="flex items-center gap-2 text-xs font-medium text-vl-ink-secondary cursor-pointer">
                            <input type="checkbox" checked={form.data.is_active} onChange={(e) => form.setData('is_active', e.target.checked)} className="h-4 w-4 rounded text-sky-500" />
                            Active Endpoint
                        </label>

                        <div className="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" disabled={form.processing} className="vl-btn-primary">
                                <Save className="h-4 w-4" />
                                {form.processing ? 'Saving...' : 'Save Webhook'}
                            </button>
                            <Link href="/admin/webhooks" className="vl-btn-secondary">
                                <ArrowLeft className="h-4 w-4" />
                                Cancel
                            </Link>
                        </div>
                    </form>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

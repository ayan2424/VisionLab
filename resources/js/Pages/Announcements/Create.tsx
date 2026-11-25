import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Send } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

export default function AnnouncementsCreate() {
    const form = useForm({
        title: '',
        body: '',
        pinned: false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/announcements');
    };

    return (
        <PortalLayout title="Post Global Announcement" pageTitle="New Announcement">
            <div className="mx-auto max-w-2xl">
                <SpotlightSurface accent="sky" className="!rounded-xl p-8 space-y-6">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">
                            Broadcast Campus Announcement
                        </h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Visible across every active student and faculty workspace.
                        </p>
                    </div>

                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-1.5">
                            <label htmlFor="title" className="block text-xs font-medium text-vl-ink-secondary">
                                Headline
                            </label>
                            <input
                                id="title"
                                type="text"
                                required
                                value={form.data.title}
                                onChange={(e) => form.setData('title', e.target.value)}
                                placeholder="Scheduled maintenance or semester announcement"
                                className="vl-input w-full"
                            />
                            {form.errors.title && <p className="text-xs text-rose-500">{form.errors.title}</p>}
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="body" className="block text-xs font-medium text-vl-ink-secondary">
                                Message Body
                            </label>
                            <textarea
                                id="body"
                                rows={6}
                                required
                                value={form.data.body}
                                onChange={(e) => form.setData('body', e.target.value)}
                                placeholder="Write the full campus notice..."
                                className="w-full rounded-xl border-0 bg-vl-bg/85 px-4 py-3 text-sm text-vl-ink ring-1 ring-inset ring-vl-border/15 focus:outline-none focus:ring-2 focus:ring-sky-500"
                            />
                            {form.errors.body && <p className="text-xs text-rose-500">{form.errors.body}</p>}
                        </div>

                        <label className="flex items-center gap-2.5 text-xs font-medium text-vl-ink-secondary cursor-pointer">
                            <input
                                type="checkbox"
                                checked={form.data.pinned}
                                onChange={(e) => form.setData('pinned', e.target.checked)}
                                className="h-4 w-4 rounded border-vl-border/25 text-sky-500 focus:ring-sky-500"
                            />
                            Pin announcement to top of feed
                        </label>

                        <div className="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" disabled={form.processing} className="vl-btn-primary">
                                <Send className="h-4 w-4" />
                                {form.processing ? 'Publishing...' : 'Publish Announcement'}
                            </button>
                            <Link href="/announcements" className="vl-btn-secondary">
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

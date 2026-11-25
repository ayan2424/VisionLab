import React from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { Bell, Pin, Plus, Trash2 } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';
import type { SharedProps } from '@/types';

interface AnnouncementItem {
    id: number;
    title: string;
    body: string;
    pinned: boolean;
    created_at: string;
    author?: { name: string } | null;
}

interface AnnouncementsIndexProps {
    announcements: {
        data: AnnouncementItem[];
        links?: Array<{ url: string | null; label: string; active: boolean }>;
    };
}

export default function AnnouncementsIndex({ announcements }: AnnouncementsIndexProps) {
    const { props } = usePage<SharedProps>();
    const isAdmin = props.auth.user?.role === 'admin';

    const handleDelete = (id: number) => {
        if (!window.confirm('Delete this global announcement?')) return;
        router.delete(`/announcements/global/${id}`);
    };

    return (
        <PortalLayout title="Announcements" pageTitle="Campus Announcements">
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">
                            Global Notices &amp; Updates
                        </h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Official department bulletins, scheduled maintenance windows, and semester alerts.
                        </p>
                    </div>
                    {isAdmin && (
                        <MagneticButton href="/announcements/create" size="sm" variant="primary">
                            <Plus className="h-4 w-4" />
                            Post Announcement
                        </MagneticButton>
                    )}
                </div>

                {announcements.data.length === 0 ? (
                    <SpotlightSurface className="!rounded-2xl p-12 text-center">
                        <Bell className="mx-auto h-8 w-8 text-vl-ink-muted" />
                        <p className="mt-3 text-sm text-vl-ink-secondary">No global announcements posted yet.</p>
                    </SpotlightSurface>
                ) : (
                    <div className="space-y-4">
                        {announcements.data.map((ann) => (
                            <SpotlightSurface
                                key={ann.id}
                                accent={ann.pinned ? 'sky' : 'none'}
                                className="!rounded-2xl p-6"
                            >
                                <div className="flex flex-wrap items-start justify-between gap-4">
                                    <div className="space-y-2 max-w-3xl">
                                        <div className="flex flex-wrap items-center gap-2">
                                            {ann.pinned && (
                                                <StatusPill tone="sky">
                                                    <Pin className="h-3 w-3" />
                                                    Pinned
                                                </StatusPill>
                                            )}
                                            <span className="font-mono text-xs text-vl-ink-muted">
                                                {new Date(ann.created_at).toLocaleDateString()}
                                            </span>
                                        </div>
                                        <h3 className="font-display text-lg font-semibold text-vl-ink">
                                            {ann.title}
                                        </h3>
                                        <p className="whitespace-pre-line text-sm leading-relaxed text-vl-ink-secondary">
                                            {ann.body}
                                        </p>
                                    </div>

                                    {isAdmin && (
                                        <button
                                            type="button"
                                            onClick={() => handleDelete(ann.id)}
                                            aria-label="Delete announcement"
                                            className="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-500/15 text-rose-500 hover:bg-rose-500/25 transition-colors"
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </button>
                                    )}
                                </div>
                            </SpotlightSurface>
                        ))}
                    </div>
                )}

                {announcements.links && announcements.links.length > 3 && (
                    <div className="flex flex-wrap justify-center gap-2 pt-2">
                        {announcements.links.map((link, idx) =>
                            link.url ? (
                                <Link
                                    key={idx}
                                    href={link.url}
                                    className={`rounded-full px-4 py-1.5 text-xs font-semibold transition-colors ${
                                        link.active
                                            ? 'bg-sky-500 text-white'
                                            : 'bg-vl-surface text-vl-ink-secondary hover:text-vl-ink'
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

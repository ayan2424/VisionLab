import React from 'react';
import { Link, router } from '@inertiajs/react';
import { Edit, Layers, Plus, Trash2 } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface TemplateItem {
    id: number;
    name: string;
    description?: string | null;
    language: string;
    git_url?: string | null;
    is_active: boolean;
}

export default function AdminTemplatesIndex({ templates }: { templates: TemplateItem[] }) {
    return (
        <PortalLayout title="Workspace Templates" pageTitle="Nix Workspace Templates" adminMode>
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">Declarative Runtime Templates</h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Pre-configured .vision/dev.nix environments available when students create workspaces.
                        </p>
                    </div>
                    <MagneticButton href="/admin/templates/create" size="sm" variant="primary">
                        <Plus className="h-4 w-4" />
                        New Template
                    </MagneticButton>
                </div>

                {templates.length === 0 ? (
                    <SpotlightSurface className="p-12 text-center space-y-2">
                        <Layers className="mx-auto h-8 w-8 text-vl-ink-muted" />
                        <p className="text-sm text-vl-ink-secondary">No workspace templates configured yet.</p>
                    </SpotlightSurface>
                ) : (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {templates.map((tpl) => (
                            <SpotlightSurface key={tpl.id} accent="sky" className="p-6 flex flex-col justify-between gap-5">
                                <div className="space-y-2.5">
                                    <div className="flex items-center justify-between">
                                        <StatusPill tone="sky">{tpl.language}</StatusPill>
                                        <StatusPill tone={tpl.is_active ? 'teal' : 'neutral'}>
                                            {tpl.is_active ? 'Active' : 'Inactive'}
                                        </StatusPill>
                                    </div>
                                    <h3 className="font-display text-lg font-semibold text-vl-ink">{tpl.name}</h3>
                                    {tpl.description && (
                                        <p className="text-xs leading-relaxed text-vl-ink-secondary">{tpl.description}</p>
                                    )}
                                </div>

                                <div className="flex items-center justify-end gap-2 border-t border-vl-border/10 pt-4">
                                    <Link href={`/admin/templates/${tpl.id}/edit`} className="vl-btn-secondary !px-3.5 !py-1.5 text-xs">
                                        <Edit className="h-3.5 w-3.5" />
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            if (!window.confirm(`Delete template "${tpl.name}"?`)) return;
                                            router.delete(`/admin/templates/${tpl.id}`);
                                        }}
                                        className="vl-btn-danger !px-3.5 !py-1.5 text-xs"
                                    >
                                        <Trash2 className="h-3.5 w-3.5" />
                                        Delete
                                    </button>
                                </div>
                            </SpotlightSurface>
                        ))}
                    </div>
                )}
            </div>
        </PortalLayout>
    );
}

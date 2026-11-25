import React from 'react';
import { Link, router } from '@inertiajs/react';
import { Building2, Edit, Eye, Plus, Trash2 } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface ColumnDef {
    key: string;
    label: string;
    mono?: boolean;
    badge?: boolean;
}

interface ErpIndexProps {
    title: string;
    subtitle: string;
    basePath: string;
    createLabel: string;
    columns: ColumnDef[];
    rows: Array<Record<string, unknown> & { id: number }>;
    links?: Array<{ url: string | null; label: string; active: boolean }>;
    canEdit?: boolean;
    canView?: boolean;
    canDelete?: boolean;
}

export default function ErpResourceIndex({
    title,
    subtitle,
    basePath,
    createLabel,
    columns,
    rows,
    links,
    canEdit = true,
    canView = false,
    canDelete = true,
}: ErpIndexProps) {
    return (
        <PortalLayout title={title} pageTitle={title} subtitle={subtitle} adminMode>
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">{title}</h2>
                        <p className="text-sm text-vl-ink-secondary">{subtitle}</p>
                    </div>
                    <MagneticButton href={`${basePath}/create`} size="sm" variant="primary">
                        <Plus className="h-4 w-4" />
                        {createLabel}
                    </MagneticButton>
                </div>

                <SpotlightSurface className="p-6">
                    {rows.length === 0 ? (
                        <div className="py-12 text-center space-y-2">
                            <Building2 className="mx-auto h-8 w-8 text-vl-ink-muted" />
                            <p className="text-sm text-vl-ink-secondary">No records added yet.</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead>
                                    <tr className="border-b border-vl-border/10 text-xs font-medium text-vl-ink-muted">
                                        {columns.map((col) => (
                                            <th key={col.key} className="pb-3 pr-4">
                                                {col.label}
                                            </th>
                                        ))}
                                        <th className="pb-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-vl-border/10">
                                    {rows.map((row) => (
                                        <tr key={row.id} className="hover:bg-vl-bg/60 transition-colors">
                                            {columns.map((col) => {
                                                const raw = row[col.key];
                                                if (col.badge) {
                                                    const positive =
                                                        raw === true ||
                                                        raw === 1 ||
                                                        raw === 'active' ||
                                                        raw === 'paid' ||
                                                        raw === 'Active';
                                                    return (
                                                        <td key={col.key} className="py-3.5 pr-4">
                                                            <StatusPill tone={positive ? 'teal' : 'amber'}>
                                                                {typeof raw === 'boolean'
                                                                    ? raw
                                                                        ? 'Active'
                                                                        : 'Inactive'
                                                                    : String(raw ?? '—')}
                                                            </StatusPill>
                                                        </td>
                                                    );
                                                }
                                                return (
                                                    <td
                                                        key={col.key}
                                                        className={`py-3.5 pr-4 text-vl-ink ${col.mono ? 'font-mono text-xs tabular-nums' : ''}`}
                                                    >
                                                        {raw !== null && raw !== undefined && raw !== '' ? String(raw) : '—'}
                                                    </td>
                                                );
                                            })}
                                            <td className="py-3.5 text-right">
                                                <div className="inline-flex items-center gap-1.5">
                                                    {canView && (
                                                        <Link
                                                            href={`${basePath}/${row.id}`}
                                                            className="inline-flex h-8 w-8 items-center justify-center rounded-full bg-vl-surface-raised text-vl-ink-secondary hover:text-sky-500"
                                                            title="Inspect"
                                                        >
                                                            <Eye className="h-3.5 w-3.5" />
                                                        </Link>
                                                    )}
                                                    {canEdit && (
                                                        <Link
                                                            href={`${basePath}/${row.id}/edit`}
                                                            className="inline-flex h-8 w-8 items-center justify-center rounded-full bg-vl-surface-raised text-vl-ink-secondary hover:text-sky-500"
                                                            title="Edit"
                                                        >
                                                            <Edit className="h-3.5 w-3.5" />
                                                        </Link>
                                                    )}
                                                    {canDelete && (
                                                        <button
                                                            type="button"
                                                            onClick={() => {
                                                                if (!window.confirm('Delete this record?')) return;
                                                                router.delete(`${basePath}/${row.id}`);
                                                            }}
                                                            className="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-500/15 text-rose-500 hover:bg-rose-500/25"
                                                            title="Delete"
                                                        >
                                                            <Trash2 className="h-3.5 w-3.5" />
                                                        </button>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </SpotlightSurface>

                {links && links.length > 3 && (
                    <div className="flex flex-wrap justify-center gap-2">
                        {links.map((link, idx) =>
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

import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Cpu, Edit, Plus, Search, ShieldCheck, Trash2 } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface ExtensionRecord {
    id: number;
    name: string;
    package_identifier: string;
    version: string;
    description?: string | null;
    checksum?: string | null;
    is_global: boolean;
    is_builtin: boolean;
    is_active: boolean;
}

interface AdminExtensionsIndexProps {
    extensions: {
        data: ExtensionRecord[];
        links?: Array<{ url: string | null; label: string; active: boolean }>;
    };
    search?: string;
}

export default function AdminExtensionsIndex({ extensions, search: initialSearch = '' }: AdminExtensionsIndexProps) {
    const [search, setSearch] = useState(initialSearch);
    const [activeMap, setActiveMap] = useState<Record<number, boolean>>(() =>
        Object.fromEntries(extensions.data.map((e) => [e.id, e.is_active]))
    );

    const toggleExtension = async (id: number) => {
        const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
        const res = await fetch(`/admin/extensions/${id}/toggle`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        });
        if (res.ok) {
            const data = await res.json();
            setActiveMap((prev) => ({ ...prev, [id]: Boolean(data.is_active) }));
        }
    };

    const submitSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/admin/extensions', search ? { search } : {}, { preserveState: true });
    };

    return (
        <PortalLayout title="VSIX Extensions" pageTitle="Offline VSIX Extension Registry" adminMode>
            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <form onSubmit={submitSearch} className="relative w-full max-w-sm">
                        <Search className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-vl-ink-muted" />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Search extensions or package IDs..."
                            className="vl-input w-full !rounded-full pl-11"
                        />
                    </form>

                    <MagneticButton href="/admin/extensions/create" size="sm" variant="primary">
                        <Plus className="h-4 w-4" />
                        Upload Signed VSIX
                    </MagneticButton>
                </div>

                <SpotlightSurface className="p-6">
                    {extensions.data.length === 0 ? (
                        <div className="py-12 text-center space-y-2">
                            <Cpu className="mx-auto h-8 w-8 text-vl-ink-muted" />
                            <p className="text-sm text-vl-ink-secondary">No extensions found in the registry.</p>
                        </div>
                    ) : (
                        <div className="space-y-2.5">
                            {extensions.data.map((ext) => {
                                const isActive = activeMap[ext.id] ?? ext.is_active;
                                return (
                                    <div
                                        key={ext.id}
                                        className="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-vl-bg/80 px-5 py-4"
                                    >
                                        <div className="min-w-0 space-y-1">
                                            <div className="flex flex-wrap items-center gap-2">
                                                <span className="font-display text-sm font-semibold text-vl-ink">
                                                    {ext.name}
                                                </span>
                                                <span className="rounded-full bg-vl-surface px-2.5 py-0.5 font-mono text-[10px] text-vl-ink-muted">
                                                    v{ext.version}
                                                </span>
                                                {ext.is_global && <StatusPill tone="sky">Global</StatusPill>}
                                                {ext.is_builtin && <StatusPill tone="teal">Built-in</StatusPill>}
                                            </div>
                                            <p className="truncate font-mono text-[11px] text-vl-ink-muted">
                                                {ext.package_identifier}
                                                {ext.checksum ? ` · sha256:${ext.checksum.slice(0, 14)}...` : ''}
                                            </p>
                                        </div>

                                        <div className="flex items-center gap-2 shrink-0">
                                            {ext.checksum && (
                                                <span className="hidden sm:inline-flex items-center gap-1 rounded-full bg-teal-500/15 px-3 py-1 font-mono text-[10px] text-teal-600 dark:text-teal-300">
                                                    <ShieldCheck className="h-3 w-3" />
                                                    Verified
                                                </span>
                                            )}
                                            <button
                                                type="button"
                                                onClick={() => void toggleExtension(ext.id)}
                                                className={`rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors ${
                                                    isActive
                                                        ? 'bg-teal-500 text-white'
                                                        : 'bg-vl-surface-raised text-vl-ink-muted hover:text-vl-ink'
                                                }`}
                                            >
                                                {isActive ? 'Active' : 'Disabled'}
                                            </button>
                                            <Link
                                                href={`/admin/extensions/${ext.id}/edit`}
                                                className="inline-flex h-8 w-8 items-center justify-center rounded-full bg-vl-surface-raised text-vl-ink-secondary hover:text-sky-500"
                                            >
                                                <Edit className="h-3.5 w-3.5" />
                                            </Link>
                                            {!ext.is_builtin && (
                                                <button
                                                    type="button"
                                                    onClick={() => {
                                                        if (!window.confirm(`Remove extension ${ext.name}?`)) return;
                                                        router.delete(`/admin/extensions/${ext.id}`);
                                                    }}
                                                    className="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-500/15 text-rose-500 hover:bg-rose-500/25"
                                                >
                                                    <Trash2 className="h-3.5 w-3.5" />
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

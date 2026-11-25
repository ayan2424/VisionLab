import React from 'react';
import { Link, router } from '@inertiajs/react';
import { motion } from 'motion/react';
import { Play, Plus, RefreshCw, Square, Terminal, Trash2 } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface WorkspaceRow {
    id: number;
    name: string;
    slug: string;
    language: string;
    status: string;
    port: number | null;
    created_at: string;
}

export default function WorkspaceIndex({
    workspaces,
    quota,
}: {
    workspaces: WorkspaceRow[];
    quota: { max_workspaces: number; memory_mb?: number; cpu_shares?: number };
}) {
    const handleDelete = (slug: string, name: string) => {
        if (!window.confirm(`Permanently delete workspace "${name}" and all container files?`)) return;
        router.delete(`/workspace/${slug}`);
    };

    const handleStop = async (slug: string) => {
        const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '';
        await fetch(`/workspace/${slug}/stop`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' },
        });
        router.reload({ only: ['workspaces'] });
    };

    return (
        <PortalLayout title="Cloud Workspaces" pageTitle="Cloud Workspaces">
            <div className="space-y-8">
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div className="space-y-1">
                        <h2 className="font-display text-2xl font-semibold tracking-[-0.025em] text-vl-ink sm:text-3xl">
                            Isolated Nix &amp; VS Code Containers
                        </h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Using{' '}
                            <span className="font-mono text-sky-500 dark:text-sky-400 tabular-nums">
                                {workspaces.length} / {quota.max_workspaces}
                            </span>{' '}
                            active container slots ({quota.memory_mb ?? 2048} MB RAM limit per workspace).
                        </p>
                    </div>
                    <MagneticButton href="/workspace/create" size="md" variant="primary">
                        <Plus className="h-4 w-4" />
                        New Workspace
                    </MagneticButton>
                </div>

                {workspaces.length === 0 ? (
                    <SpotlightSurface accent="sky" className="p-12 text-center">
                        <Terminal className="mx-auto h-9 w-9 text-sky-400" />
                        <h3 className="mt-4 font-display text-xl font-semibold text-vl-ink">
                            No workspaces provisioned yet
                        </h3>
                        <p className="mx-auto mt-2 max-w-md text-sm text-vl-ink-secondary">
                            Launch a preconfigured Python, Node.js, C/C++, or PHP Nix environment directly in your browser.
                        </p>
                        <div className="mt-6">
                            <MagneticButton href="/workspace/create" size="lg" variant="primary">
                                Create First Workspace
                            </MagneticButton>
                        </div>
                    </SpotlightSurface>
                ) : (
                    <div className="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        {workspaces.map((ws, idx) => (
                            <motion.div
                                key={ws.id}
                                initial={{ opacity: 0, y: 12 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.25, delay: idx * 0.04 }}
                            >
                                <SpotlightSurface accent={ws.status === 'running' ? 'teal' : 'sky'} className="h-full p-6">
                                    <div className="flex h-full flex-col justify-between gap-6">
                                        <div className="space-y-3">
                                            <div className="flex items-start justify-between gap-3">
                                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-vl-bg/80 text-sky-400">
                                                    <Terminal className="h-5 w-5" />
                                                </div>
                                                <StatusPill
                                                    tone={ws.status === 'running' ? 'teal' : ws.status === 'error' ? 'rose' : 'neutral'}
                                                    pulse={ws.status === 'running'}
                                                >
                                                    {ws.status}
                                                </StatusPill>
                                            </div>
                                            <div>
                                                <h3 className="font-display text-lg font-semibold text-vl-ink">{ws.name}</h3>
                                                <p className="mt-0.5 font-mono text-xs text-vl-ink-muted">
                                                    {ws.slug} · {ws.language}
                                                </p>
                                            </div>
                                        </div>

                                        <div className="flex flex-wrap items-center justify-between gap-2 border-t border-vl-border/12 pt-4">
                                            <Link
                                                href={`/workspace/${ws.slug}`}
                                                prefetch="hover"
                                                className="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-b from-sky-500 to-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:from-sky-400 hover:to-blue-500 transition-all"
                                            >
                                                <Play className="h-3.5 w-3.5" />
                                                Open IDE
                                            </Link>

                                            <div className="flex items-center gap-1.5">
                                                {ws.status === 'running' && (
                                                    <button
                                                        type="button"
                                                        onClick={() => handleStop(ws.slug)}
                                                        title="Stop container"
                                                        className="rounded-full bg-vl-bg/75 p-2 text-vl-ink-secondary hover:text-amber-400 transition-colors"
                                                    >
                                                        <Square className="h-3.5 w-3.5" />
                                                    </button>
                                                )}
                                                <a
                                                    href={`/workspace/${ws.slug}/rebuild`}
                                                    title="Rebuild Nix environment"
                                                    className="rounded-full bg-vl-bg/75 p-2 text-vl-ink-secondary hover:text-sky-400 transition-colors"
                                                >
                                                    <RefreshCw className="h-3.5 w-3.5" />
                                                </a>
                                                <button
                                                    type="button"
                                                    onClick={() => handleDelete(ws.slug, ws.name)}
                                                    title="Delete workspace"
                                                    className="rounded-full bg-vl-bg/75 p-2 text-vl-ink-secondary hover:text-rose-400 transition-colors"
                                                >
                                                    <Trash2 className="h-3.5 w-3.5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </SpotlightSurface>
                            </motion.div>
                        ))}
                    </div>
                )}
            </div>
        </PortalLayout>
    );
}

import React, { useEffect, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { ArrowLeft, CheckCircle2, Loader2, RefreshCw } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface WorkspaceActionProps {
    workspace: {
        id: number;
        name: string;
        slug: string;
        language: string;
        status: string;
    };
    mode: 'rebuild' | 'restart';
}

export default function WorkspaceAction({ workspace, mode }: WorkspaceActionProps) {
    const [phase, setPhase] = useState<'working' | 'ready' | 'error'>('working');
    const [message, setMessage] = useState(
        mode === 'rebuild'
            ? 'Stopping container safely (preserving persistent volume) and re-evaluating .vision/dev.nix...'
            : 'Gracefully restarting your isolated code-server container...'
    );

    useEffect(() => {
        let cancelled = false;
        const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '';
        const endpoint =
            mode === 'rebuild'
                ? `/workspace/${workspace.slug}/rebuild/process`
                : `/workspace/${workspace.slug}/restart/process`;

        const run = async () => {
            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        Accept: 'application/json',
                    },
                });
                if (!res.ok) throw new Error('Container operation returned an error.');
                if (cancelled) return;
                setPhase('ready');
                setMessage('Container environment is ready. Redirecting into your cloud IDE...');
                window.setTimeout(() => {
                    if (!cancelled) {
                        router.visit(`/workspace/${workspace.slug}`);
                    }
                }, 1100);
            } catch (err) {
                if (!cancelled) {
                    setPhase('error');
                    setMessage(err instanceof Error ? err.message : 'Unable to complete operation.');
                }
            }
        };

        void run();
        return () => {
            cancelled = true;
        };
    }, [mode, workspace.slug]);

    return (
        <PortalLayout
            title={mode === 'rebuild' ? 'Rebuild Workspace' : 'Restart Workspace'}
            pageTitle={mode === 'rebuild' ? 'Rebuilding Environment' : 'Restarting Container'}
        >
            <div className="mx-auto max-w-xl py-10">
                <SpotlightSurface accent="sky" className="!rounded-xl p-8 text-center space-y-6">
                    <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                        {phase === 'ready' ? (
                            <CheckCircle2 className="h-8 w-8 text-teal-400" />
                        ) : phase === 'error' ? (
                            <RefreshCw className="h-7 w-7 text-rose-400" />
                        ) : (
                            <Loader2 className="h-8 w-8 animate-spin" />
                        )}
                    </div>

                    <div className="space-y-2">
                        <h2 className="font-display text-2xl font-semibold text-vl-ink">
                            {mode === 'rebuild' ? 'Rebuilding' : 'Restarting'} {workspace.name}
                        </h2>
                        <p className="font-mono text-xs text-vl-ink-muted">
                            {workspace.slug} · {workspace.language}
                        </p>
                        <p className="pt-2 text-sm text-vl-ink-secondary">{message}</p>
                    </div>

                    <div className="flex flex-wrap justify-center gap-3 pt-2">
                        <Link href={`/workspace/${workspace.slug}`} className="vl-btn-primary">
                            Open Cloud IDE
                        </Link>
                        <Link href="/workspace" className="vl-btn-secondary">
                            <ArrowLeft className="h-4 w-4" />
                            Back to Workspaces
                        </Link>
                    </div>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

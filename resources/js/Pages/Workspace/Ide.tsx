import React, { useEffect, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import { motion } from 'motion/react';
import {
    ArrowLeft,
    CheckCircle2,
    Cpu,
    GitPullRequestDraft,
    Loader2,
    Lock,
    RefreshCw,
} from 'lucide-react';
import { BrandLogo, StatusPill, ThemeToggle } from '@/components/ui/Primitives';

interface IdeProps {
    workspace: {
        id: number;
        name: string;
        slug: string;
        language: string;
        status: string;
    };
    workspaceName: string;
    vscodeUrl: string | null;
    needsBoot: boolean;
    isAssignmentLocked?: boolean;
}

export default function WorkspaceIde({
    workspace,
    workspaceName,
    vscodeUrl: initialUrl,
    needsBoot,
    isAssignmentLocked = false,
}: IdeProps) {
    const [ideUrl, setIdeUrl] = useState<string | null>(initialUrl);
    const [bootStep, setBootStep] = useState<number>(needsBoot ? 1 : 3);
    const [bootError, setBootError] = useState<string | null>(null);
    const [cpu, setCpu] = useState<string>('—');
    const [memory, setMemory] = useState<string>('—');
    const [pendingPatches, setPendingPatches] = useState<number>(0);

    // Boot container if not already running
    useEffect(() => {
        if (!needsBoot && initialUrl) return;

        let cancelled = false;
        const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '';

        const boot = async () => {
            try {
                setBootStep(1);
                const res = await fetch(`/workspace/${workspace.slug}/start`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                });
                if (!res.ok) throw new Error('Container startup returned an error.');
                const data = await res.json();
                if (cancelled) return;

                setBootStep(2);
                const startedUrl = data.url || null;

                // Poll ping until code-server responds
                for (let attempt = 0; attempt < 25; attempt++) {
                    if (cancelled) return;
                    const pingRes = await fetch(`/workspace/${workspace.slug}/ping`, {
                        headers: { Accept: 'application/json' },
                    });
                    if (pingRes.ok) {
                        const pingData = await pingRes.json();
                        if (pingData.ready) {
                            setBootStep(3);
                            setIdeUrl(startedUrl);
                            return;
                        }
                    }
                    await new Promise((r) => setTimeout(r, 1500));
                }

                if (!cancelled && startedUrl) {
                    setBootStep(3);
                    setIdeUrl(startedUrl);
                }
            } catch (err) {
                if (!cancelled) {
                    setBootError(err instanceof Error ? err.message : 'Unable to start container.');
                }
            }
        };

        boot();
        return () => {
            cancelled = true;
        };
    }, [workspace.slug, needsBoot, initialUrl]);

    // Poll live /status telemetry every 8s
    useEffect(() => {
        let active = true;
        const poll = async () => {
            try {
                const res = await fetch(`/workspace/${workspace.slug}/status`, {
                    headers: { Accept: 'application/json' },
                });
                if (!res.ok || !active) return;
                const data = await res.json();
                if (data.stats) {
                    setCpu(data.stats.CPUPerc || data.stats.cpu || '0%');
                    setMemory(data.stats.MemUsage || data.stats.memory || '—');
                }
                if (typeof data.pending_patches_count === 'number') {
                    setPendingPatches(data.pending_patches_count);
                }
            } catch {
                // ignore transient network poll errors
            }
        };

        poll();
        const id = window.setInterval(poll, 8000);
        return () => {
            active = false;
            window.clearInterval(id);
        };
    }, [workspace.slug]);

    const steps = [
        'Allocating isolated crun container & persistent volume',
        'Evaluating .vision/dev.nix & verifying SHA-256 extensions',
        'Attaching live browser VS Code session',
    ];

    return (
        <div className="flex h-screen w-screen flex-col overflow-hidden bg-[#05080F] text-vl-ink">
            <Head title={`${workspaceName} — IDE`} />

            {/* Slim 40px Sovereign IDE Command Bar */}
            <header className="flex h-10 shrink-0 items-center justify-between gap-3 border-b border-vl-border/15 bg-vl-rail/95 px-3 text-xs backdrop-blur-xl">
                <div className="flex items-center gap-3 min-w-0">
                    <Link
                        href="/workspace"
                        prefetch="hover"
                        className="inline-flex items-center gap-1.5 rounded-full px-2 py-1 text-vl-ink-secondary hover:bg-vl-surface-raised hover:text-vl-ink transition-colors"
                    >
                        <ArrowLeft className="h-3.5 w-3.5" />
                        <span className="hidden sm:inline">Workspaces</span>
                    </Link>

                    <span className="h-4 w-px bg-vl-border/20" />
                    <BrandLogo compact />
                    <span className="truncate font-display text-xs font-semibold text-vl-ink">
                        {workspaceName}
                    </span>
                    <span className="hidden md:inline font-mono text-[11px] text-vl-ink-muted">
                        {workspace.slug}
                    </span>
                    {isAssignmentLocked && (
                        <StatusPill tone="amber" pulse>
                            <Lock className="h-3 w-3" />
                            Exam Lockdown
                        </StatusPill>
                    )}
                </div>

                <div className="flex items-center gap-2.5">
                    <div className="hidden lg:flex items-center gap-3 rounded-full bg-vl-surface/70 px-2.5 py-1 font-mono text-[11px] text-vl-ink-secondary tabular-nums">
                        <span className="inline-flex items-center gap-1">
                            <Cpu className="h-3 w-3 text-sky-400" />
                            {cpu}
                        </span>
                        <span className="text-vl-ink-muted">·</span>
                        <span>{memory}</span>
                    </div>

                    <div className="inline-flex items-center gap-1.5 rounded-full bg-amber-500/15 px-2.5 py-1 font-mono text-[11px] text-amber-400 tabular-nums">
                        <GitPullRequestDraft className="h-3.5 w-3.5" />
                        <span>{pendingPatches} patches</span>
                    </div>

                    <Link
                        href={`/workspace/${workspace.slug}/rebuild`}
                        title="Rebuild Nix environment"
                        className="inline-flex h-7 w-7 items-center justify-center rounded-full bg-vl-surface text-vl-ink-secondary hover:text-sky-400 transition-colors"
                    >
                        <RefreshCw className="h-3.5 w-3.5" />
                    </Link>

                    <ThemeToggle className="!h-7 !w-7 !rounded-full" />
                </div>
            </header>

            {/* Full-Viewport IDE Stage */}
            <main className="relative flex-1 bg-[#05080F]">
                {ideUrl ? (
                    <iframe
                        id="vscode-frame"
                        src={ideUrl}
                        title={`VisionLab IDE — ${workspaceName}`}
                        className="h-[calc(100vh-40px)] w-full border-0"
                        allow="clipboard-read; clipboard-write"
                    />
                ) : (
                    <div className="flex h-full items-center justify-center p-6">
                        <motion.div
                            initial={{ opacity: 0, y: 12 }}
                            animate={{ opacity: 1, y: 0 }}
                            className="w-full max-w-md rounded-2xl bg-[#0B1220] p-7 text-slate-200 shadow-2xl ring-1 ring-white/10"
                        >
                            <div className="flex items-center gap-3">
                                <Loader2 className="h-5 w-5 animate-spin text-sky-400" />
                                <h2 className="font-display text-lg font-semibold text-white">
                                    Booting {workspaceName}
                                </h2>
                            </div>

                            {bootError ? (
                                <div className="mt-5 rounded-xl bg-rose-500/15 p-4 text-xs text-rose-200">
                                    {bootError}
                                </div>
                            ) : (
                                <ul className="mt-6 space-y-3.5">
                                    {steps.map((label, i) => {
                                        const stepNum = i + 1;
                                        const done = bootStep > stepNum;
                                        const current = bootStep === stepNum;
                                        return (
                                            <li key={label} className="flex items-center gap-3 text-xs">
                                                {done ? (
                                                    <CheckCircle2 className="h-4 w-4 shrink-0 text-teal-400" />
                                                ) : current ? (
                                                    <Loader2 className="h-4 w-4 shrink-0 animate-spin text-sky-400" />
                                                ) : (
                                                    <span className="h-4 w-4 shrink-0 rounded-full border border-white/20" />
                                                )}
                                                <span className={done || current ? 'text-white' : 'text-slate-500'}>
                                                    {label}
                                                </span>
                                            </li>
                                        );
                                    })}
                                </ul>
                            )}
                        </motion.div>
                    </div>
                )}
            </main>
        </div>
    );
}

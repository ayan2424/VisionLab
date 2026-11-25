import React, { useState } from 'react';
import { motion } from 'motion/react';
import { BookOpen, Copy, Check, Cpu, Lock, ShieldCheck, Terminal } from 'lucide-react';
import { cn } from '@/lib/cn';
import { PublicLayout } from '@/layouts/PublicLayout';

const DOC_SECTIONS = [
    {
        id: 'quickstart',
        title: 'Container Provisioning & Nix',
        icon: Terminal,
        summary: 'Define deterministic per-course packages inside .vision/dev.nix so every student seat boots identically.',
        codeTitle: '.vision/dev.nix',
        code: `{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  packages = with pkgs; [
    python312
    gcc13
    gdb
    nodejs_22
  ];
  shellHook = ''
    echo "VisionLab CS-301 Environment Ready"
  '';
}`,
        notes: [
            'Container volumes mount persistently at /home/coder/{workspace-slug}.',
            'Editing and saving .vision/dev.nix prompts the built-in VisionLab Nix Watcher extension to trigger a clean environment rebuild without losing student source files.',
            'CPU shares, memory caps, and idle timeouts resolve hierarchically via WorkspaceQuota (User -> Course -> Global).',
        ],
    },
    {
        id: 'ai-governance',
        title: 'Human-Gated AI Patch Pipeline',
        icon: ShieldCheck,
        summary: 'All AI agent file modifications transit through ai_pending_patches with strict WorkspacePolicy authorization.',
        codeTitle: 'API Endpoints (Sanctum Protected)',
        code: `POST /api/ai/v1/chat/completions   # SSE stream scoped via X-Workspace-Id
GET  /api/ai/patches/pending       # Pending unified diffs for authorized workspace
POST /api/ai/patches/{id}/approve  # Applies patch via CodeServerManager + logs audit
POST /api/ai/patches/{id}/reject   # Marks rejected; container filesystem untouched`,
        notes: [
            'Direct execution endpoint POST /api/ai/execute returns HTTP 403 by design.',
            'Every file path is validated against traversal (..), absolute paths, shell metacharacters, and protected targets (.env, .git, vendor, node_modules).',
            'Each approval records a snapshot in ai_snapshots and an audit line in ai_actions_log.',
        ],
    },
    {
        id: 'extensions',
        title: 'SHA-256 VSIX Supply Chain',
        icon: Cpu,
        summary: 'Upload verified .vsix archives once in Admin -> Extensions and enforce checksum verification before container sync.',
        codeTitle: 'Verification Flow',
        code: `1. Admin uploads signed .vsix artifact -> SHA-256 computed & persisted
2. Policy assigned globally, per-course, or per-workspace
3. SyncWorkspaceExtensions verifies hash_file('sha256', artifact)
4. Mismatch dispatches ExtensionIntegrityFailed & aborts installation`,
        notes: [
            'Built-in extensions are protected against accidental deletion.',
            'Course instructors can toggle approved course-level extensions without exposing unverified third-party registries.',
        ],
    },
    {
        id: 'forensics',
        title: 'VisionGuard Exam Forensics',
        icon: Lock,
        summary: 'Objective authorship telemetry synced into submission_forensics during locked assignments.',
        codeTitle: 'POST /api/workspace/{slug}/forensics/sync',
        code: `{
  "humanKeystrokeDelta": 142,
  "aiInjectedCharDelta": 28,
  "pastedCharDelta": 0,
  "focusLossDelta": 0
}`,
        notes: [
            'Calculates live human_pct, ai_pct, and confidence rating (high / medium / low) inside the instructor grading view.',
            'Suspending a user immediately revokes Sanctum API tokens and dispatches WorkspaceSuspendedUser to halt running containers.',
        ],
    },
];

export default function Docs() {
    const [activeId, setActiveId] = useState(DOC_SECTIONS[0].id);
    const [copiedId, setCopiedId] = useState<string | null>(null);

    const copyCode = (id: string, code: string) => {
        navigator.clipboard?.writeText(code);
        setCopiedId(id);
        setTimeout(() => setCopiedId(null), 1800);
    };

    return (
        <PublicLayout title="Platform Documentation">
            <section className="relative pt-36 pb-24">
                <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10">
                    <div className="mb-14 max-w-3xl space-y-4">
                        <div className="inline-flex items-center gap-2 text-xs font-mono text-sky-500 dark:text-sky-400">
                            <BookOpen className="h-3.5 w-3.5" />
                            <span>TECHNICAL REFERENCE</span>
                        </div>
                        <h1 className="font-display text-[clamp(2.4rem,4.8vw,4.2rem)] font-semibold leading-[1.04] tracking-[-0.035em] text-vl-ink">
                            VisionLab Platform Documentation
                        </h1>
                        <p className="max-w-[66ch] text-base leading-relaxed text-vl-ink-secondary">
                            Reference specifications for Nix workspace provisioning, human-gated AI patch review, VSIX cryptographic verification, and VisionGuard exam telemetry.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 gap-10 lg:grid-cols-12">
                        {/* Sticky Left Rail */}
                        <aside className="lg:col-span-3">
                            <div className="sticky top-28 space-y-1.5 rounded-2xl bg-vl-surface/75 p-2.5 shadow-vl-panel">
                                {DOC_SECTIONS.map((sec) => {
                                    const Icon = sec.icon;
                                    const active = activeId === sec.id;
                                    return (
                                        <a
                                            key={sec.id}
                                            href={`#${sec.id}`}
                                            onClick={() => setActiveId(sec.id)}
                                            className={cn(
                                                'flex items-center gap-2.5 rounded-full px-3.5 py-2.5 text-sm font-medium transition-colors',
                                                active
                                                    ? 'bg-sky-500/14 text-vl-ink'
                                                    : 'text-vl-ink-secondary hover:bg-vl-surface-raised/60 hover:text-vl-ink'
                                            )}
                                        >
                                            <Icon className={cn('h-4 w-4 shrink-0', active ? 'text-sky-400' : 'text-vl-ink-muted')} />
                                            <span className="truncate">{sec.title}</span>
                                        </a>
                                    );
                                })}
                            </div>
                        </aside>

                        {/* 68ch Reading Column */}
                        <div className="space-y-14 lg:col-span-9">
                            {DOC_SECTIONS.map((sec) => (
                                <motion.article
                                    key={sec.id}
                                    id={sec.id}
                                    initial={{ opacity: 0, y: 14 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true, amount: 0.2 }}
                                    transition={{ duration: 0.35 }}
                                    className="scroll-mt-28 space-y-5 rounded-2xl bg-vl-surface/75 p-7 shadow-vl-panel sm:p-9"
                                >
                                    <h2 className="font-display text-2xl font-semibold tracking-tight text-vl-ink">
                                        {sec.title}
                                    </h2>
                                    <p className="max-w-[68ch] text-sm leading-relaxed text-vl-ink-secondary">
                                        {sec.summary}
                                    </p>

                                    <div className="overflow-hidden rounded-xl bg-[#060A13] ring-1 ring-white/[0.08]">
                                        <div className="flex items-center justify-between border-b border-white/[0.08] px-4 py-2.5">
                                            <span className="font-mono text-xs text-slate-400">{sec.codeTitle}</span>
                                            <button
                                                type="button"
                                                onClick={() => copyCode(sec.id, sec.code)}
                                                className="inline-flex items-center gap-1.5 rounded-full bg-white/[0.06] px-2.5 py-1 font-mono text-[11px] text-slate-300 hover:bg-white/[0.12] transition-colors"
                                            >
                                                {copiedId === sec.id ? (
                                                    <>
                                                        <Check className="h-3 w-3 text-teal-400" />
                                                        Copied
                                                    </>
                                                ) : (
                                                    <>
                                                        <Copy className="h-3 w-3" />
                                                        Copy
                                                    </>
                                                )}
                                            </button>
                                        </div>
                                        <pre className="overflow-x-auto p-4 font-mono text-xs leading-relaxed text-sky-200">
                                            {sec.code}
                                        </pre>
                                    </div>

                                    <ul className="max-w-[68ch] space-y-2.5 pt-2 text-sm leading-relaxed text-vl-ink-secondary">
                                        {sec.notes.map((note) => (
                                            <li key={note} className="flex items-start gap-2.5">
                                                <span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-400" />
                                                <span>{note}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </motion.article>
                            ))}
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

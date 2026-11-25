import React from 'react';
import { motion } from 'motion/react';
import { ArrowRight, CheckCircle2, Cpu, FileCode2, Lock, Server, ShieldCheck, Terminal } from 'lucide-react';
import { PublicLayout } from '@/layouts/PublicLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

const CAPABILITIES = [
    {
        icon: Terminal,
        accent: 'sky' as const,
        title: 'Deterministic Nix-Backed Workspaces',
        body: 'Define exact packages, language toolchains, linters, and test runners inside .vision/dev.nix. Every student workspace boots identically on isolated crun containers with persistent storage volumes.',
        specs: ['Zero host dependency drift', 'Per-course CPU, RAM & disk quotas', 'Hot rebuild watcher on .vision/dev.nix save'],
    },
    {
        icon: ShieldCheck,
        accent: 'amber' as const,
        title: 'Human-in-the-Loop AI Patch Sandbox',
        body: 'Direct AI writes are forbidden at the API boundary. Every assistant edit stages inside ai_pending_patches with unified diffs, snapshots, one-click rollback, and immutable audit entries.',
        specs: ['OpenAI-compatible SSE proxy', 'Explicit Approve / Reject student review', 'Zero unauthorized workspace file writes'],
    },
    {
        icon: Lock,
        accent: 'teal' as const,
        title: 'VisionGuard Exam Lockdown & Forensics',
        body: 'Faculty configure locked assessments that collect authorship signals — keystroke cadence, reviewed AI patch deltas, focus-loss counts, and paste volumes — directly inside the grading workflow.',
        specs: ['Confidence-scored authorship ratio', 'Live grading queue forensics view', 'Privacy-respecting telemetry without spyware'],
    },
    {
        icon: Cpu,
        accent: 'sky' as const,
        title: 'SHA-256 Verified VSIX Extension Supply Chain',
        body: 'Admins curate an institutional VS Code extension catalog. Every uploaded .vsix artifact is SHA-256 hashed and verified before syncing across global, course-level, or per-workspace containers.',
        specs: ['Integrity failure event telemetry', 'Automated background sync jobs', 'Zero unmanaged third-party extension installs'],
    },
    {
        icon: Server,
        accent: 'teal' as const,
        title: 'Multi-Campus LMS & Academic ERP',
        body: 'Manage campuses, academic departments, course batches, rosters, CSV bulk enrollments, fee challans, faculty records, and digital library assets alongside your live container fleet.',
        specs: ['Role-scoped RBAC (Admin, Instructor, Student)', 'Real-time /healthz infrastructure probes', 'Sanctum token revocation on suspension'],
    },
    {
        icon: FileCode2,
        accent: 'amber' as const,
        title: 'Instant SPA Performance on Laravel 11',
        body: 'React 19 + Inertia.js v2 hover prefetching eliminates full-page reload latency while retaining Laravel server-side authorization, policies, FormRequests, and Reverb WebSockets.',
        specs: ['Sub-50ms client route transitions', 'Persistent keyboard Command Palette (⌘K)', 'Dual-light Obsidian & Pearl studio themes'],
    },
];

export default function Features() {
    return (
        <PublicLayout title="Architecture & Capabilities">
            <section className="relative pt-36 pb-20">
                <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10">
                    <motion.div
                        initial={{ opacity: 0, y: 18 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5, ease: [0.16, 1, 0.3, 1] }}
                        className="max-w-3xl space-y-6"
                    >
                        <StatusPill tone="sky">Sovereign Platform Architecture</StatusPill>
                        <h1 className="font-display text-[clamp(2.6rem,5.2vw,4.8rem)] font-semibold leading-[1.02] tracking-[-0.035em] text-vl-ink text-balance">
                            Every container, extension, and AI diff governed under one roof.
                        </h1>
                        <p className="max-w-[60ch] text-base leading-relaxed text-vl-ink-secondary sm:text-lg">
                            VisionLab replaces fragmented student VMs, unmanaged AI plugins, and disconnected grading spreadsheets with a verifiable university coding platform.
                        </p>
                    </motion.div>

                    <div className="mt-16 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        {CAPABILITIES.map((cap, idx) => {
                            const Icon = cap.icon;
                            return (
                                <motion.div
                                    key={cap.title}
                                    initial={{ opacity: 0, y: 20 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true, amount: 0.2 }}
                                    transition={{ duration: 0.45, delay: idx * 0.05, ease: [0.16, 1, 0.3, 1] }}
                                >
                                    <SpotlightSurface accent={cap.accent} className="h-full p-7 sm:p-8">
                                        <div className="flex h-full flex-col justify-between gap-6">
                                            <div className="space-y-4">
                                                <div className="inline-flex h-10 w-10 items-center justify-center rounded-full bg-vl-bg/80 text-sky-400">
                                                    <Icon className="h-5 w-5" />
                                                </div>
                                                <h2 className="font-display text-2xl font-semibold tracking-tight text-vl-ink">
                                                    {cap.title}
                                                </h2>
                                                <p className="text-sm leading-relaxed text-vl-ink-secondary">{cap.body}</p>
                                            </div>

                                            <ul className="space-y-2 border-t border-vl-border/12 pt-5">
                                                {cap.specs.map((spec) => (
                                                    <li key={spec} className="flex items-center gap-2.5 text-xs text-vl-ink-secondary">
                                                        <CheckCircle2 className="h-3.5 w-3.5 shrink-0 text-teal-400" />
                                                        <span>{spec}</span>
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>
                                    </SpotlightSurface>
                                </motion.div>
                            );
                        })}
                    </div>

                    <div className="mt-20 flex flex-wrap items-center justify-between gap-6 rounded-2xl bg-vl-surface/80 p-8 shadow-vl-panel">
                        <div className="space-y-1.5">
                            <h3 className="font-display text-2xl font-semibold text-vl-ink">
                                Ready to inspect a dedicated institutional cluster?
                            </h3>
                            <p className="text-sm text-vl-ink-secondary">
                                Request a campus pilot or review the platform documentation.
                            </p>
                        </div>
                        <div className="flex flex-wrap gap-3">
                            <MagneticButton href="/contact" variant="primary">
                                Request Campus Pilot
                                <ArrowRight className="h-4 w-4" />
                            </MagneticButton>
                            <MagneticButton href="/docs" variant="secondary">
                                Read Documentation
                            </MagneticButton>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

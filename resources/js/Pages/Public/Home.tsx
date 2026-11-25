import React, { Suspense, lazy, useRef, useState } from 'react';
import { AnimatePresence, motion } from 'motion/react';
import {
    ArrowRight,
    Check,
    CheckCircle2,
    Cpu,
    GitCommitHorizontal,
    GraduationCap,
    Lock,
    Minus,
    Plus,
    RotateCcw,
    Server,
    ShieldCheck,
    Terminal,
    Users,
    X,
} from 'lucide-react';
import { gsap, useGSAP } from '@/lib/gsap';
import { cn } from '@/lib/cn';
import { useTheme } from '@/components/ThemeProvider';
import { PublicLayout } from '@/layouts/PublicLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';
import { ControlPlaneBentoPreview } from '@/components/sections/ControlPlaneBentoPreview';
import type { HeroCoreMode } from '@/components/three/HeroStudioStage';

const HeroStudioStage = lazy(() => import('@/components/three/HeroStudioStage'));
const LabRoom3DStage = lazy(() => import('@/components/three/LabRoom3DStage'));

const HERO_MODES: Array<{
    id: HeroCoreMode;
    label: string;
    spec: string;
    detail: string;
    tone: 'sky' | 'amber' | 'teal';
}> = [
    {
        id: 'nix',
        label: 'Nix Runtime',
        spec: '.vision/dev.nix · crun isolated',
        detail: 'Inspect how every student laptop boots identical pinned compilers with zero host drift.',
        tone: 'sky',
    },
    {
        id: 'ai',
        label: 'AI Patch Gate',
        spec: 'ai_pending_patches · HITL audit',
        detail: 'Watch proposed AI edits stage safely as diffs inside the IDE until the student approves.',
        tone: 'amber',
    },
    {
        id: 'sha',
        label: 'VSIX SHA-256',
        spec: 'signed offline registry · verified',
        detail: 'See institutional VS Code extensions verified against cryptographic SHA-256 digests before mounting.',
        tone: 'teal',
    },
];

const TICKER_ITEMS = [
    'Deterministic Nix Shells',
    'Human-Gated AI Patch Queue',
    'crun Isolated Containers',
    'SHA-256 Signed VSIX Registry',
    'VisionGuard Exam Lockdown',
    'Authorship Keystroke Forensics',
    'Multi-Campus Academic ERP',
    'Zero Self-Registration Leakage',
];

const CAMPUS_ROLES = [
    {
        id: 'student',
        label: 'Student Seat',
        icon: Terminal,
        headline: 'Zero setup friction. Full VS Code power from minute one.',
        summary:
            'Open your browser in lecture hall or dorm and land inside a persistent Nix-backed VS Code workspace preloaded with your course compilers, rubrics, and gated AI tutor.',
        highlights: [
            'One-click assignment workspace launch with persistent Docker volume',
            'Inspect every AI-suggested diff line-by-line before writing to disk',
            'Track course deadlines, graded rubric scores, and verified skill XP',
        ],
        badge: 'cs301-systems-lab · Running',
        image: '/images/redesign/campus-glass-lab.jpg',
        alt: 'Modern architectural university glass computer science studio with students coding',
    },
    {
        id: 'faculty',
        label: 'Faculty Studio',
        icon: GraduationCap,
        headline: 'Grade real engineering work with objective authorship telemetry.',
        summary:
            'Lock course toolchains once in .vision/dev.nix, inspect student workspaces directly, and review VisionGuard authorship forensics right beside submitted code.',
        highlights: [
            'Inspect live student containers inside your assigned courses',
            'Review human keystrokes vs reviewed AI patches and paste events',
            'Approve course-specific extensions without opening unverified marketplaces',
        ],
        badge: 'VisionGuard · Exam Forensics',
        image: '/images/redesign/cs-research-lab.jpg',
        alt: 'University computer science research lab with dual-monitor engineering workstations',
    },
    {
        id: 'admin',
        label: 'Campus Admin',
        icon: Users,
        headline: 'Govern containers, quotas, extensions, and ERP from one console.',
        summary:
            'Provision verified institutional accounts, enforce per-cohort memory & CPU quotas, audit every sensitive action, and operate campuses, batches, challans, and library records.',
        highlights: [
            'Live /healthz infrastructure probes and container fleet force-stop controls',
            'Instant Sanctum token revocation and container suspension on account lock',
            'Unified campus ERP covering departments, batches, employees, and fee challans',
        ],
        badge: 'Cluster Telemetry · Operational',
        image: '/images/redesign/students-collaborating.jpg',
        alt: 'University engineering students collaborating in a modern studio lab',
    },
];

interface PatchScenario {
    id: string;
    file: string;
    title: string;
    summary: string;
    hunk: string;
    contextBefore: string;
    removed: string;
    added: string[];
    contextAfter: string;
    hash: string;
    aiDeltaPct: number;
}

const PATCH_SCENARIOS: PatchScenario[] = [
    {
        id: 'allocator',
        file: 'src/allocator.c',
        title: 'Guard slab boundary before coalescing free block',
        summary: 'Prevents out-of-bounds pointer read when adjacent block header sits at arena tail.',
        hunk: '@@ -42,4 +42,7 @@',
        contextBefore: '    block_hdr = arena_alloc_header(size);',
        removed: '    next_slot = block_hdr->next;',
        added: [
            '    if (block_hdr == NULL || block_hdr->offset + size > arena->limit) {',
            '        return ARENA_ERR_BOUNDS;',
            '    }',
            '    next_slot = block_hdr->next;',
        ],
        contextAfter: '    return commit_slot(next_slot, block_hdr);',
        hash: 'sha256:9f4b...c71e',
        aiDeltaPct: 3.4,
    },
    {
        id: 'scheduler',
        file: 'lab/scheduler.py',
        title: 'Enforce deterministic tie-break by arrival timestamp',
        summary: 'Guarantees identical round-robin queue ordering across automated grading suites.',
        hunk: '@@ -18,3 +18,6 @@',
        contextBefore: '    ready = [p for p in tasks if p.arrival <= clock]',
        removed: '    ready.sort(key=lambda p: p.priority)',
        added: [
            '    ready.sort(key=lambda p: (p.priority, p.arrival, p.pid))',
            '    if not ready:',
            '        return None',
        ],
        contextAfter: '    return ready[0]',
        hash: 'sha256:3c8a...19d4',
        aiDeltaPct: 2.1,
    },
    {
        id: 'nix',
        file: '.vision/dev.nix',
        title: 'Pin GCC 13 and Valgrind for memory-safety assignment',
        summary: 'Locks reproducible compiler binaries across every enrolled seat in CS-301.',
        hunk: '@@ -3,3 +3,5 @@',
        contextBefore: '  packages = with pkgs; [',
        removed: '    gcc',
        added: ['    gcc13', '    valgrind', '    gdb'],
        contextAfter: '  ];',
        hash: 'sha256:7d12...e4a0',
        aiDeltaPct: 1.5,
    },
];

const NIX_PRESETS = [
    {
        id: 'python',
        label: 'Python & Data',
        quota: '2 vCPU · 2048 MB · 2 GB Vol',
        code: `{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  name = "cs204-python-ml";
  packages = with pkgs; [
    python312
    python312Packages.numpy
    python312Packages.pytest
    ruff
  ];
}`,
    },
    {
        id: 'systems',
        label: 'Systems C / Rust',
        quota: '2 vCPU · 4096 MB · 4 GB Vol',
        code: `{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  name = "cs301-operating-systems";
  packages = with pkgs; [
    gcc13
    gdb
    valgrind
    rustc
    cargo
  ];
}`,
    },
    {
        id: 'web',
        label: 'Full-Stack Web',
        quota: '2 vCPU · 2048 MB · 3 GB Vol',
        code: `{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  name = "se210-web-engineering";
  packages = with pkgs; [
    nodejs_22
    php83
    postgresql_16
    git
  ];
}`,
    },
];

const LIFECYCLE_STAGES = [
    {
        step: '01',
        title: 'Provision Cohort',
        detail: 'Admins batch-create institutional seats, assign campus ERP departments, and enforce CPU/RAM quotas.',
    },
    {
        step: '02',
        title: 'Boot Nix Seat',
        detail: 'Students click into a browser IDE where .vision/dev.nix mounts identical pinned compilers inside crun.',
    },
    {
        step: '03',
        title: 'Code with Gated AI',
        detail: 'Every AI tutor suggestion queues as a reviewable diff in ai_pending_patches — zero silent edits.',
    },
    {
        step: '04',
        title: 'Grade with Forensics',
        detail: 'Faculty inspect running student containers and grade alongside verified keystroke & patch telemetry.',
    },
];

const SECURITY_INVARIANTS = [
    {
        title: 'Rootless crun Container Isolation',
        detail: 'Strict path-traversal guards, non-root Linux namespaces, and per-cohort memory/CPU limits.',
    },
    {
        title: 'Zero Public Self-Registration',
        detail: 'Accounts are exclusively provisioned by institutional admins to prevent unauthorized cluster abuse.',
    },
    {
        title: 'Instant Account & Token Revocation',
        detail: 'Suspending a seat immediately revokes all Sanctum tokens and halts live Docker containers.',
    },
    {
        title: 'SHA-256 Verified Offline VSIX Registry',
        detail: 'Unapproved marketplace binaries are blocked; every extension hash is validated before container mount.',
    },
    {
        title: 'OWASP ASVS Security Headers',
        detail: 'Strict CSP framing rules, HSTS, X-Frame-Options, and private Reverb WebSocket channel authorization.',
    },
    {
        title: 'Immutable Institutional Audit Logs',
        detail: 'Every admin override, AI patch decision, extension install, and container stop writes to audit_logs.',
    },
];

const FAQ_ITEMS = [
    {
        q: 'Can VisionLab run completely on our own campus servers or private VPC?',
        a: 'Yes. VisionLab is designed as sovereign institutional software. You can deploy Laravel, PostgreSQL/MySQL, Redis, and the Docker/crun workspace host inside your on-premise university data center or private cloud with zero third-party code exfiltration.',
    },
    {
        q: 'How does VisionLab stop students from letting AI silently write entire assignments?',
        a: 'Direct agent write endpoints return HTTP 403 by policy. Every AI suggestion is staged inside ai_pending_patches so students must explicitly inspect and approve diffs, while VisionGuard logs human keystroke velocity versus reviewed AI changes for faculty review.',
    },
    {
        q: 'What happens during timed lab exams and practical assessments?',
        a: 'Instructors can enable Exam Lockdown per course or assignment. VisionLab restricts external extensions, freezes AI tutor access according to exam policy, and streams live container status and forensic activity to the Faculty Studio.',
    },
    {
        q: 'How are VS Code extensions kept safe in air-gapped or strict campus networks?',
        a: 'Admins upload approved .vsix packages to VisionLab’s offline extension registry. Before syncing into any student workspace, the runtime verifies the cryptographic SHA-256 checksum and rejects any tampered artifact.',
    },
    {
        q: 'Does VisionLab replace separate spreadsheet tracking for campuses and batches?',
        a: 'Yes. Built directly into the Admin Console is a multi-campus ERP module managing campuses, academic departments, course batches, employee records, fee challans, library inventories, and role-based audit trails.',
    },
];

export default function Home() {
    const { isDark } = useTheme();
    const containerRef = useRef<HTMLDivElement>(null);

    const [coreMode, setCoreMode] = useState<HeroCoreMode>('nix');
    const [roleId, setRoleId] = useState<string>('student');
    const [scenarioId, setScenarioId] = useState<string>('allocator');
    const [patchState, setPatchState] = useState<'pending' | 'applied' | 'rejected'>('pending');
    const [nixPresetId, setNixPresetId] = useState<string>('python');
    const [verifyStep, setVerifyStep] = useState<'idle' | 'hashing' | 'verified'>('verified');
    const [openFaq, setOpenFaq] = useState<number>(0);

    const [auditEntries, setAuditEntries] = useState<Array<{ time: string; text: string; tone: string }>>([
        { time: '14:02:11', text: 'Agent proposed patch #408 -> staged in ai_pending_patches', tone: 'text-amber-300' },
    ]);

    const activeHeroMode = HERO_MODES.find((m) => m.id === coreMode) ?? HERO_MODES[0];
    const activeRole = CAMPUS_ROLES.find((r) => r.id === roleId) ?? CAMPUS_ROLES[0];
    const activeScenario = PATCH_SCENARIOS.find((s) => s.id === scenarioId) ?? PATCH_SCENARIOS[0];
    const activeNix = NIX_PRESETS.find((p) => p.id === nixPresetId) ?? NIX_PRESETS[0];

    const humanPct = patchState === 'applied' ? Number((86.4 - activeScenario.aiDeltaPct).toFixed(1)) : 86.4;
    const aiPct = Number((100 - humanPct).toFixed(1));

    useGSAP(
        () => {
            const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduced) return;

            gsap.from('.vl-hero-stagger', {
                y: 16,
                opacity: 0.01,
                duration: 0.7,
                stagger: 0.07,
                ease: 'power3.out',
                clearProps: 'transform,opacity',
            });

            gsap.utils.toArray<HTMLElement>('.vl-glide-in').forEach((el) => {
                gsap.fromTo(
                    el,
                    { y: 16 },
                    {
                        y: 0,
                        duration: 0.65,
                        ease: 'power3.out',
                        clearProps: 'transform',
                        scrollTrigger: {
                            trigger: el,
                            start: 'top 90%',
                            once: true,
                        },
                    }
                );
            });
        },
        { scope: containerRef }
    );

    const switchScenario = (id: string) => {
        setScenarioId(id);
        setPatchState('pending');
    };

    const handlePatchDecision = (decision: 'applied' | 'rejected') => {
        setPatchState(decision);
        const now = new Date().toTimeString().slice(0, 8);
        setAuditEntries((prev) => [
            {
                time: now,
                text:
                    decision === 'applied'
                        ? `Approved ${activeScenario.file} -> written to container & audited`
                        : `Rejected ${activeScenario.file} -> container buffer untouched`,
                tone: decision === 'applied' ? 'text-teal-300' : 'text-rose-300',
            },
            ...prev.slice(0, 2),
        ]);
    };

    const runHashCheck = () => {
        setVerifyStep('hashing');
        window.setTimeout(() => setVerifyStep('verified'), 650);
    };

    return (
        <PublicLayout title="Sovereign University Coding Labs">
            <div ref={containerRef} className="overflow-x-clip">
                {/* ═══════════════════════════════════════════════════════════════════════
                    SECTION 1 — LIVE 3D CNC ENGINEERING WORKSTATION HERO (100svh)
                ═══════════════════════════════════════════════════════════════════════ */}
                <section className="relative flex min-h-[100svh] items-center pt-28 pb-16">
                    <Suspense
                        fallback={<div className="pointer-events-none absolute inset-0 bg-vl-bg" />}
                    >
                        <HeroStudioStage mode={coreMode} isDark={isDark} />
                    </Suspense>

                    <div
                        aria-hidden="true"
                        className="pointer-events-none absolute inset-0 bg-gradient-to-r from-vl-bg/95 via-vl-bg/55 to-transparent"
                    />
                    <div
                        aria-hidden="true"
                        className="pointer-events-none absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-vl-bg to-transparent"
                    />

                    <div className="relative z-10 mx-auto w-full max-w-[1380px] px-4 sm:px-6 lg:px-10">
                        <div className="grid grid-cols-1 items-center gap-12 lg:grid-cols-12">
                            <div className="max-w-[540px] space-y-7 lg:col-span-5">
                                <h1 className="vl-hero-stagger font-display text-[clamp(2.65rem,4.45vw,4.3rem)] font-semibold leading-[1.01] tracking-[-0.035em] text-vl-ink text-balance">
                                    Cloud coding labs engineered for real university courses.
                                </h1>

                                <p className="vl-hero-stagger max-w-[46ch] text-base leading-relaxed text-vl-ink-secondary sm:text-[17px]">
                                    Deterministic Nix-backed VS Code containers, student-approved AI patch diffs, SHA-256 verified extensions, and campus ERP — built for institutional control.
                                </p>

                                <div className="vl-hero-stagger flex flex-wrap items-center gap-3 pt-1">
                                    <MagneticButton href="/login" size="lg" variant="primary">
                                        Sign in to Portal
                                        <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                                    </MagneticButton>
                                    <MagneticButton href="/contact" size="lg" variant="secondary">
                                        Request Campus Pilot
                                    </MagneticButton>
                                </div>

                                {/* Full-Pill Interactive Switcher Driving Live 3D Laptop Screen */}
                                <div className="vl-hero-stagger pt-4">
                                    <div className="inline-flex flex-wrap items-center gap-1 rounded-full bg-vl-surface/85 p-1.5 backdrop-blur-xl ring-1 ring-vl-border/15 shadow-[0_18px_40px_-18px_rgba(0,0,0,0.45)]">
                                        {HERO_MODES.map((m) => {
                                            const active = m.id === coreMode;
                                            return (
                                                <button
                                                    key={m.id}
                                                    type="button"
                                                    onClick={() => setCoreMode(m.id)}
                                                    className={cn(
                                                        'relative rounded-full px-4 py-2 text-xs font-medium transition-colors duration-150',
                                                        active ? 'text-vl-ink' : 'text-vl-ink-muted hover:text-vl-ink'
                                                    )}
                                                >
                                                    {active && (
                                                        <motion.span
                                                            layoutId="hero-mode-pill"
                                                            transition={{ type: 'spring', stiffness: 360, damping: 30 }}
                                                            className="absolute inset-0 rounded-full bg-vl-surface-raised dark:bg-white/15 shadow-sm"
                                                        />
                                                    )}
                                                    <span className="relative z-10 flex items-center gap-2">
                                                        <span
                                                            className={cn(
                                                                'h-1.5 w-1.5 rounded-full',
                                                                m.tone === 'sky' && 'bg-sky-400',
                                                                m.tone === 'amber' && 'bg-amber-400',
                                                                m.tone === 'teal' && 'bg-teal-400'
                                                            )}
                                                        />
                                                        {m.label}
                                                    </span>
                                                </button>
                                            );
                                        })}
                                    </div>

                                    <AnimatePresence mode="wait">
                                        <motion.div
                                            key={activeHeroMode.id}
                                            initial={{ opacity: 0, y: 6 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            exit={{ opacity: 0, y: -4 }}
                                            transition={{ duration: 0.18, ease: [0.16, 1, 0.3, 1] }}
                                            className="mt-4 space-y-1"
                                        >
                                            <p className="font-mono text-xs text-sky-600 dark:text-sky-300">
                                                {activeHeroMode.spec}
                                            </p>
                                            <p className="text-sm leading-relaxed text-vl-ink-secondary">
                                                {activeHeroMode.detail}
                                            </p>
                                        </motion.div>
                                    </AnimatePresence>
                                </div>
                            </div>

                            <div className="hidden lg:col-span-7 lg:block" />
                        </div>
                    </div>
                </section>

                {/* ═══════════════════════════════════════════════════════════════════════
                    SECTION 2 — KINETIC ARCHITECTURE RIBBON & EDITORIAL MANIFESTO
                ═══════════════════════════════════════════════════════════════════════ */}
                <section className="relative py-12 border-y border-vl-border/10">
                    <div
                        className="overflow-hidden"
                        style={{
                            maskImage: 'linear-gradient(to right, transparent, black 10%, black 90%, transparent)',
                            WebkitMaskImage: 'linear-gradient(to right, transparent, black 10%, black 90%, transparent)',
                        }}
                    >
                        <motion.div
                            animate={{ x: ['0%', '-50%'] }}
                            transition={{ duration: 34, ease: 'linear', repeat: Infinity }}
                            className="flex w-max items-center gap-4"
                        >
                            {[...TICKER_ITEMS, ...TICKER_ITEMS].map((item, idx) => (
                                <span
                                    key={idx}
                                    className="inline-flex items-center gap-3 rounded-full bg-vl-surface/80 px-5 py-2 font-mono text-xs text-vl-ink-secondary ring-1 ring-inset ring-vl-border/15"
                                >
                                    <span className="h-1.5 w-1.5 rounded-full bg-sky-400" />
                                    {item}
                                </span>
                            ))}
                        </motion.div>
                    </div>
                </section>

                <section className="relative py-20 sm:py-28">
                    <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10">
                        <div className="vl-glide-in grid grid-cols-1 items-end gap-10 lg:grid-cols-12">
                            <h2 className="font-display text-3xl font-semibold leading-[1.06] tracking-[-0.032em] text-vl-ink sm:text-5xl lg:col-span-7 text-balance">
                                Replace broken student laptop setups and blind AI autocomplete with one verifiable campus runtime.
                            </h2>
                            <p className="text-base leading-relaxed text-vl-ink-secondary lg:col-span-5">
                                Consumer editors silently rewrite files and standalone LMS portals leave students debugging local installations. VisionLab unites isolated browser containers, explicit diff governance, and university operations under your institution's direct policy.
                            </p>
                        </div>
                    </div>
                </section>

                {/* ═══════════════════════════════════════════════════════════════════════
                    SECTION 3 — INTERACTIVE 3D UNIVERSITY CODING LAB FLOOR (SPATIAL TELEMETRY)
                ═══════════════════════════════════════════════════════════════════════ */}
                <section className="relative py-12 sm:py-20">
                    <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10 space-y-8">
                        <div className="vl-glide-in flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
                            <div className="max-w-2xl space-y-3">
                                <h2 className="font-display text-3xl font-semibold leading-[1.06] tracking-[-0.03em] text-vl-ink sm:text-5xl text-balance">
                                    Step inside a live 3D university coding lab
                                </h2>
                                <p className="text-base leading-relaxed text-vl-ink-secondary">
                                    Click any student workstation in Hall 04 to inspect container CPU, memory, human authorship ratios, or launch the read-only AI Lab Copilot drawer.
                                </p>
                            </div>

                            <div className="flex flex-wrap items-center gap-2">
                                <span className="inline-flex items-center gap-2 rounded-full bg-sky-500/15 px-3.5 py-1.5 font-mono text-[11px] font-medium text-sky-600 dark:text-sky-300">
                                    <span className="h-2 w-2 rounded-full bg-sky-400" />
                                    Running Seat
                                </span>
                                <span className="inline-flex items-center gap-2 rounded-full bg-amber-500/15 px-3.5 py-1.5 font-mono text-[11px] font-medium text-amber-600 dark:text-amber-300">
                                    <span className="h-2 w-2 rounded-full bg-amber-400" />
                                    AI Patch Staged
                                </span>
                                <span className="inline-flex items-center gap-2 rounded-full bg-teal-500/15 px-3.5 py-1.5 font-mono text-[11px] font-medium text-teal-600 dark:text-teal-300">
                                    <span className="h-2 w-2 rounded-full bg-teal-400" />
                                    Exam Lockdown
                                </span>
                            </div>
                        </div>

                        <div className="vl-glide-in">
                            <Suspense
                                fallback={
                                    <div className="flex h-[580px] items-center justify-center rounded-xl bg-vl-surface text-sm text-vl-ink-muted ring-1 ring-vl-border/15">
                                        Loading Interactive 3D Lab Floor...
                                    </div>
                                }
                            >
                                <LabRoom3DStage isDark={isDark} />
                            </Suspense>
                        </div>
                    </div>
                </section>

                {/* ═══════════════════════════════════════════════════════════════════════
                    SECTION 4 — LIVE V21/V28 CONTROL PLANE BENTO SHOWCASE
                ═══════════════════════════════════════════════════════════════════════ */}
                <section className="relative py-20 sm:py-28">
                    <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10 space-y-10">
                        <div className="vl-glide-in grid grid-cols-1 items-end gap-8 lg:grid-cols-12">
                            <h2 className="font-display text-3xl font-semibold leading-[1.06] tracking-[-0.03em] text-vl-ink sm:text-5xl lg:col-span-7 text-balance">
                                One luminous control plane for admins, faculty, and students
                            </h2>
                            <p className="text-base leading-relaxed text-vl-ink-secondary lg:col-span-5">
                                Switch between institutional perspectives below to explore the live floating-rail bento dashboards with real-time sparklines, container density, and capsule telemetry charts.
                            </p>
                        </div>

                        <div className="vl-glide-in">
                            <ControlPlaneBentoPreview />
                        </div>
                    </div>
                </section>

                {/* ═══════════════════════════════════════════════════════════════════════
                    SECTION 5 — INTERACTIVE CAMPUS ROLE STUDIO (PHOTOGRAPHY SHOWCASE)
                ═══════════════════════════════════════════════════════════════════════ */}
                <section className="relative py-12 sm:py-20">
                    <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10 space-y-8">
                        <div className="vl-glide-in flex flex-col justify-between gap-6 md:flex-row md:items-center">
                            <h2 className="font-display text-2xl font-semibold tracking-[-0.028em] text-vl-ink sm:text-4xl">
                                Built for every seat in the department
                            </h2>

                            <div className="inline-flex w-fit items-center gap-1 rounded-full bg-vl-surface/90 p-1.5 ring-1 ring-inset ring-vl-border/15">
                                {CAMPUS_ROLES.map((role) => {
                                    const Icon = role.icon;
                                    const active = role.id === roleId;
                                    return (
                                        <button
                                            key={role.id}
                                            type="button"
                                            onClick={() => setRoleId(role.id)}
                                            className={cn(
                                                'relative inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-xs font-medium transition-colors',
                                                active ? 'text-white' : 'text-vl-ink-secondary hover:text-vl-ink'
                                            )}
                                        >
                                            {active && (
                                                <motion.span
                                                    layoutId="role-studio-pill"
                                                    transition={{ type: 'spring', stiffness: 360, damping: 32 }}
                                                    className="absolute inset-0 rounded-full bg-gradient-to-b from-sky-500 to-blue-600 shadow-md"
                                                />
                                            )}
                                            <Icon className="relative z-10 h-3.5 w-3.5" />
                                            <span className="relative z-10">{role.label}</span>
                                        </button>
                                    );
                                })}
                            </div>
                        </div>

                        <SpotlightSurface accent="sky" className="vl-glide-in !rounded-xl p-6 sm:p-10">
                            <AnimatePresence mode="wait">
                                <motion.div
                                    key={activeRole.id}
                                    initial={{ opacity: 0, y: 10 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -8 }}
                                    transition={{ duration: 0.24, ease: [0.16, 1, 0.3, 1] }}
                                    className="grid grid-cols-1 items-center gap-10 lg:grid-cols-12"
                                >
                                    <div className="space-y-6 lg:col-span-5">
                                        <StatusPill tone="sky">{activeRole.badge}</StatusPill>
                                        <h3 className="font-display text-2xl font-semibold leading-[1.12] tracking-[-0.025em] text-vl-ink sm:text-4xl text-balance">
                                            {activeRole.headline}
                                        </h3>
                                        <p className="text-sm leading-relaxed text-vl-ink-secondary sm:text-base">
                                            {activeRole.summary}
                                        </p>
                                        <ul className="space-y-3 pt-1">
                                            {activeRole.highlights.map((pt) => (
                                                <li key={pt} className="flex items-start gap-3 text-sm text-vl-ink">
                                                    <CheckCircle2 className="mt-0.5 h-4 w-4 shrink-0 text-teal-400" />
                                                    <span>{pt}</span>
                                                </li>
                                            ))}
                                        </ul>
                                        <div className="pt-2">
                                            <MagneticButton href="/features" variant="secondary" size="md">
                                                Inspect full capabilities
                                                <ArrowRight className="h-4 w-4" />
                                            </MagneticButton>
                                        </div>
                                    </div>

                                    <div className="lg:col-span-7">
                                        <div className="relative overflow-hidden rounded-2xl aspect-[16/10] shadow-[0_28px_70px_-24px_rgba(0,0,0,0.7)]">
                                            <img
                                                src={activeRole.image}
                                                alt={activeRole.alt}
                                                className="h-full w-full object-cover"
                                            />
                                            <div
                                                aria-hidden="true"
                                                className="absolute inset-0 bg-gradient-to-t from-[#05080F]/80 via-transparent to-transparent"
                                            />
                                            <div className="absolute bottom-5 left-5 right-5 flex items-center justify-between gap-3">
                                                <span className="rounded-full bg-[#070C16]/85 px-4 py-1.5 font-mono text-[11px] text-sky-300 backdrop-blur-md ring-1 ring-white/15">
                                                    {activeRole.badge}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </motion.div>
                            </AnimatePresence>
                        </SpotlightSurface>
                    </div>
                </section>

                {/* ═══════════════════════════════════════════════════════════════════════
                    SECTION 6 — LIVE AI PATCH REVIEW WORKBENCH (SYNTHETIC DEMO · CS-301)
                ═══════════════════════════════════════════════════════════════════════ */}
                <section className="relative py-20 sm:py-28">
                    <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10">
                        <div className="grid grid-cols-1 items-start gap-12 lg:grid-cols-12">
                            <div className="vl-glide-in space-y-6 lg:col-span-5">
                                <StatusPill tone="amber" pulse>
                                    SYNTHETIC DEMO · CS-301
                                </StatusPill>

                                <h2 className="font-display text-3xl font-semibold leading-[1.06] tracking-[-0.03em] text-vl-ink sm:text-5xl text-balance">
                                    AI suggests diffs. Students approve every write.
                                </h2>

                                <p className="text-base leading-relaxed text-vl-ink-secondary">
                                    Direct agent execution returns HTTP 403 by policy. Every suggestion queues inside{' '}
                                    <code className="rounded-full bg-vl-surface-raised px-2.5 py-0.5 font-mono text-xs text-vl-ink">
                                        ai_pending_patches
                                    </code>{' '}
                                    and updates the submission's VisionGuard authorship balance live.
                                </p>

                                <SpotlightSurface accent="teal" className="!rounded-2xl p-6 space-y-4">
                                    <div className="flex items-center justify-between">
                                        <span className="flex items-center gap-2 text-xs font-semibold text-vl-ink">
                                            <Lock className="h-3.5 w-3.5 text-teal-400" />
                                            Live VisionGuard Authorship Split
                                        </span>
                                        <span className="rounded-full bg-teal-500/15 px-2.5 py-0.5 font-mono text-[11px] text-teal-600 dark:text-teal-300">
                                            Confidence: High
                                        </span>
                                    </div>

                                    <div className="h-3 w-full overflow-hidden rounded-full bg-vl-bg/80 p-0.5">
                                        <motion.div
                                            animate={{ width: `${humanPct}%` }}
                                            transition={{ type: 'spring', stiffness: 220, damping: 26 }}
                                            className="h-full rounded-full bg-gradient-to-r from-sky-500 to-teal-400"
                                        />
                                    </div>

                                    <div className="flex items-center justify-between font-mono text-xs tabular-nums">
                                        <span className="text-vl-ink">Human Keystrokes: {humanPct}%</span>
                                        <span className="text-amber-500 dark:text-amber-400">Reviewed AI: {aiPct}%</span>
                                    </div>
                                </SpotlightSurface>

                                <div className="relative overflow-hidden rounded-2xl aspect-[16/9] shadow-[0_24px_60px_-24px_rgba(0,0,0,0.6)]">
                                    <img
                                        src="/images/redesign/workstation-night-lab.jpg"
                                        alt="Dark obsidian software engineering desk showing code diff review"
                                        loading="lazy"
                                        decoding="async"
                                        className="h-full w-full object-cover"
                                    />
                                    <div
                                        aria-hidden="true"
                                        className="absolute inset-0 bg-gradient-to-t from-[#05080F]/85 via-transparent to-transparent"
                                    />
                                    <div className="absolute bottom-4 left-5 right-5 flex items-center justify-between text-xs text-slate-200">
                                        <span>Immutable diff &amp; rollback snapshots</span>
                                        <span className="rounded-full bg-amber-400/15 px-3 py-1 font-mono text-[11px] text-amber-300">
                                            HITL Enforced
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div className="vl-glide-in lg:col-span-7">
                                <div className="overflow-hidden rounded-xl bg-[#080E1A] text-slate-200 shadow-[0_38px_90px_-26px_rgba(0,0,0,0.88),inset_0_1px_0_rgba(255,255,255,0.1)] ring-1 ring-white/10">
                                    <div className="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 bg-white/5 px-6 py-4">
                                        <div className="flex items-center gap-2">
                                            <span className="h-2.5 w-2.5 rounded-full bg-rose-400/80" />
                                            <span className="h-2.5 w-2.5 rounded-full bg-amber-400/80" />
                                            <span className="h-2.5 w-2.5 rounded-full bg-teal-400/80" />
                                            <span className="ml-2 font-mono text-xs text-slate-400">
                                                {activeScenario.file}
                                            </span>
                                        </div>

                                        <div className="flex items-center gap-1 rounded-full bg-white/5 p-1">
                                            {PATCH_SCENARIOS.map((sc) => {
                                                const active = sc.id === scenarioId;
                                                return (
                                                    <button
                                                        key={sc.id}
                                                        type="button"
                                                        onClick={() => switchScenario(sc.id)}
                                                        className={cn(
                                                            'relative rounded-full px-3.5 py-1.5 font-mono text-[11px] transition-colors',
                                                            active ? 'text-white' : 'text-slate-400 hover:text-slate-200'
                                                        )}
                                                    >
                                                        {active && (
                                                            <motion.span
                                                                layoutId="patch-file-pill"
                                                                transition={{ type: 'spring', stiffness: 380, damping: 32 }}
                                                                className="absolute inset-0 rounded-full bg-sky-500/25"
                                                            />
                                                        )}
                                                        <span className="relative z-10">{sc.file.split('/').pop()}</span>
                                                    </button>
                                                );
                                            })}
                                        </div>
                                    </div>

                                    <div className="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 px-6 py-5">
                                        <div className="space-y-1">
                                            <p className="text-sm font-semibold text-white">{activeScenario.title}</p>
                                            <p className="text-xs text-slate-400">{activeScenario.summary}</p>
                                        </div>

                                        <span
                                            className={cn(
                                                'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 font-mono text-[11px] font-medium',
                                                patchState === 'pending' && 'bg-amber-400/15 text-amber-300',
                                                patchState === 'applied' && 'bg-teal-400/15 text-teal-300',
                                                patchState === 'rejected' && 'bg-rose-400/15 text-rose-300'
                                            )}
                                        >
                                            <span
                                                className={cn(
                                                    'h-1.5 w-1.5 rounded-full',
                                                    patchState === 'pending' && 'bg-amber-400 animate-pulse',
                                                    patchState === 'applied' && 'bg-teal-400',
                                                    patchState === 'rejected' && 'bg-rose-400'
                                                )}
                                            />
                                            {patchState === 'pending'
                                                ? 'Awaiting Student Review'
                                                : patchState === 'applied'
                                                ? 'Approved & Written'
                                                : 'Rejected · Unchanged'}
                                        </span>
                                    </div>

                                    <AnimatePresence mode="wait">
                                        <motion.div
                                            key={activeScenario.id}
                                            initial={{ opacity: 0, y: 6 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            exit={{ opacity: 0, y: -4 }}
                                            transition={{ duration: 0.18 }}
                                            className="overflow-x-auto p-6 font-mono text-xs leading-7 sm:text-[13px]"
                                        >
                                            <div className="mb-2 select-none text-slate-500">{activeScenario.hunk}</div>
                                            <div className="px-4 py-0.5 text-slate-400">{activeScenario.contextBefore}</div>
                                            <div
                                                className={cn(
                                                    'my-1 rounded-2xl bg-rose-500/15 px-4 py-1 text-rose-200 transition-opacity',
                                                    patchState === 'applied' && 'line-through opacity-45'
                                                )}
                                            >
                                                <span className="mr-2 select-none text-rose-400">-</span>
                                                {activeScenario.removed}
                                            </div>
                                            {activeScenario.added.map((line, i) => (
                                                <div
                                                    key={i}
                                                    className={cn(
                                                        'my-1 rounded-2xl px-4 py-1 transition-all duration-200',
                                                        patchState === 'rejected'
                                                            ? 'bg-slate-800/40 text-slate-500 line-through'
                                                            : 'bg-teal-500/15 text-teal-200'
                                                    )}
                                                >
                                                    <span className="mr-2 select-none text-teal-400">+</span>
                                                    {line}
                                                </div>
                                            ))}
                                            <div className="mt-1 px-4 py-0.5 text-slate-400">{activeScenario.contextAfter}</div>
                                        </motion.div>
                                    </AnimatePresence>

                                    <div className="flex flex-wrap items-center justify-between gap-3 border-t border-white/10 bg-white/5 px-6 py-4">
                                        <div className="flex items-center gap-2 font-mono text-[11px] text-slate-400">
                                            <GitCommitHorizontal className="h-3.5 w-3.5 text-sky-400" />
                                            <span>{activeScenario.hash}</span>
                                        </div>

                                        <div className="flex flex-wrap items-center gap-2">
                                            {patchState !== 'pending' && (
                                                <button
                                                    type="button"
                                                    onClick={() => setPatchState('pending')}
                                                    className="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-white/15 transition-colors"
                                                >
                                                    <RotateCcw className="h-3.5 w-3.5" />
                                                    Reset
                                                </button>
                                            )}
                                            <button
                                                type="button"
                                                onClick={() => handlePatchDecision('rejected')}
                                                className="inline-flex items-center gap-1.5 rounded-full bg-rose-500/15 px-4 py-2 text-xs font-medium text-rose-200 hover:bg-rose-500/25 transition-colors"
                                            >
                                                <X className="h-3.5 w-3.5" />
                                                Reject Patch
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => handlePatchDecision('applied')}
                                                className="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-b from-sky-500 to-blue-600 px-5 py-2 text-xs font-semibold text-white shadow-[0_10px_24px_-8px_rgba(14,165,233,0.65)] hover:from-sky-400 hover:to-blue-500 transition-all"
                                            >
                                                <Check className="h-3.5 w-3.5" />
                                                Approve &amp; Write
                                            </button>
                                        </div>
                                    </div>

                                    <div className="border-t border-white/10 bg-[#05080F]/90 px-6 py-4 font-mono text-[11px]">
                                        <p className="mb-2 text-[10px] text-slate-500">AI_ACTIONS_LOG · LIVE AUDIT TAPE</p>
                                        <div className="space-y-1">
                                            {auditEntries.map((entry, idx) => (
                                                <div key={idx} className="flex items-start gap-2.5">
                                                    <span className="shrink-0 text-slate-500 tabular-nums">{entry.time}</span>
                                                    <span className={entry.tone}>{entry.text}</span>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* ═══════════════════════════════════════════════════════════════════════
                    SECTION 7 — INTERACTIVE INFRASTRUCTURE LABS (NIX SWITCHER + SHA-256)
                ═══════════════════════════════════════════════════════════════════════ */}
                <section className="relative py-12 sm:py-20">
                    <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10">
                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                            <SpotlightSurface accent="sky" className="vl-glide-in !rounded-xl p-7 sm:p-9 lg:col-span-7">
                                <div className="flex h-full flex-col justify-between gap-6">
                                    <div className="flex flex-wrap items-start justify-between gap-4">
                                        <div className="space-y-2">
                                            <h3 className="font-display text-2xl font-semibold tracking-tight text-vl-ink">
                                                Declarative Course Profiles
                                            </h3>
                                            <p className="max-w-lg text-sm text-vl-ink-secondary">
                                                Switch course tracks to inspect the exact pinned Nix packages and container quotas deployed to every student seat.
                                            </p>
                                        </div>
                                        <span className="rounded-full bg-sky-500/15 px-3.5 py-1.5 font-mono text-[11px] text-sky-600 dark:text-sky-300">
                                            {activeNix.quota}
                                        </span>
                                    </div>

                                    <div className="flex flex-wrap gap-2">
                                        {NIX_PRESETS.map((preset) => {
                                            const active = preset.id === nixPresetId;
                                            return (
                                                <button
                                                    key={preset.id}
                                                    type="button"
                                                    onClick={() => setNixPresetId(preset.id)}
                                                    className={cn(
                                                        'rounded-full px-4 py-2 text-xs font-medium transition-all',
                                                        active
                                                            ? 'bg-sky-500 text-white shadow-sm'
                                                            : 'bg-vl-bg/80 text-vl-ink-secondary hover:text-vl-ink'
                                                    )}
                                                >
                                                    {preset.label}
                                                </button>
                                            );
                                        })}
                                    </div>

                                    <AnimatePresence mode="wait">
                                        <motion.pre
                                            key={activeNix.id}
                                            initial={{ opacity: 0, y: 6 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            exit={{ opacity: 0, y: -4 }}
                                            transition={{ duration: 0.18 }}
                                            className="overflow-x-auto rounded-xl bg-[#05080F] p-5 font-mono text-xs leading-relaxed text-sky-200 ring-1 ring-white/10"
                                        >
                                            {activeNix.code}
                                        </motion.pre>
                                    </AnimatePresence>
                                </div>
                            </SpotlightSurface>

                            <SpotlightSurface accent="teal" className="vl-glide-in !rounded-xl p-7 sm:p-9 lg:col-span-5">
                                <div className="flex h-full flex-col justify-between gap-6">
                                    <div className="space-y-3">
                                        <div className="inline-flex h-11 w-11 items-center justify-center rounded-full bg-teal-500/15 text-teal-500 dark:text-teal-300">
                                            <ShieldCheck className="h-5 w-5" />
                                        </div>
                                        <h3 className="font-display text-2xl font-semibold tracking-tight text-vl-ink">
                                            SHA-256 Extension Supply Chain
                                        </h3>
                                        <p className="text-sm leading-relaxed text-vl-ink-secondary">
                                            Every VSIX extension hashes cryptographically before container sync. Checksum mismatches abort installation and fire an integrity audit event.
                                        </p>
                                    </div>

                                    <div className="space-y-3 rounded-xl bg-[#05080F] p-5 font-mono text-xs text-slate-200 ring-1 ring-white/10">
                                        <div className="flex items-center justify-between text-[11px]">
                                            <span className="text-slate-400">ARTIFACT</span>
                                            <span
                                                className={cn(
                                                    'rounded-full px-2.5 py-0.5 text-[10px] font-semibold',
                                                    verifyStep === 'verified'
                                                        ? 'bg-teal-500/20 text-teal-300'
                                                        : 'bg-amber-500/20 text-amber-300'
                                                )}
                                            >
                                                {verifyStep === 'hashing' ? 'HASHING...' : 'SHA-256 MATCH'}
                                            </span>
                                        </div>
                                        <p className="text-white">visionlab-python-2.4.1.vsix</p>
                                        <p className="truncate text-[11px] text-teal-300">
                                            {verifyStep === 'hashing'
                                                ? 'computing hash_file("sha256", artifact)...'
                                                : 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b'}
                                        </p>
                                    </div>

                                    <div className="flex items-center justify-between gap-3">
                                        <button
                                            type="button"
                                            onClick={runHashCheck}
                                            className="inline-flex items-center gap-2 rounded-full bg-vl-surface-raised px-5 py-2.5 text-xs font-semibold text-vl-ink ring-1 ring-vl-border/15 hover:bg-teal-500/15 hover:text-teal-400 transition-colors"
                                        >
                                            <Cpu className="h-3.5 w-3.5" />
                                            Re-run Checksum Probe
                                        </button>
                                        <span className="font-mono text-[11px] text-vl-ink-muted">42 containers synced</span>
                                    </div>
                                </div>
                            </SpotlightSurface>
                        </div>
                    </div>
                </section>

                {/* ═══════════════════════════════════════════════════════════════════════
                    SECTION 8 — 4-STAGE CAMPUS LIFECYCLE & ZERO-TRUST SECURITY MATRIX
                ═══════════════════════════════════════════════════════════════════════ */}
                <section className="relative py-20 sm:py-28">
                    <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10 space-y-12">
                        <div className="vl-glide-in max-w-3xl space-y-3">
                            <h2 className="font-display text-3xl font-semibold leading-[1.06] tracking-[-0.03em] text-vl-ink sm:text-5xl text-balance">
                                From cohort provisioning to audited grading in four deterministic stages
                            </h2>
                            <p className="text-base leading-relaxed text-vl-ink-secondary">
                                Every layer of the academic lifecycle runs inside your campus boundary with verifiable policy gates at each handoff.
                            </p>
                        </div>

                        {/* Connected 4-Stage Institutional Track */}
                        <div className="vl-glide-in grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                            {LIFECYCLE_STAGES.map((item) => (
                                <SpotlightSurface key={item.step} className="!rounded-2xl p-6">
                                    <div className="flex items-center justify-between">
                                        <span className="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-teal-500 font-mono text-xs font-bold text-white tabular-nums">
                                            {item.step}
                                        </span>
                                        <span className="h-px flex-1 mx-3 bg-vl-border/15" />
                                    </div>
                                    <h3 className="mt-5 font-display text-lg font-semibold text-vl-ink">
                                        {item.title}
                                    </h3>
                                    <p className="mt-2 text-sm leading-relaxed text-vl-ink-secondary">
                                        {item.detail}
                                    </p>
                                </SpotlightSurface>
                            ))}
                        </div>

                        {/* Asymmetric High-Contrast Security Invariants Bento */}
                        <div className="vl-glide-in grid grid-cols-1 gap-6 lg:grid-cols-12">
                            <div className="rounded-xl bg-[#070C16] p-8 text-slate-100 shadow-[0_32px_80px_-28px_rgba(0,0,0,0.8)] ring-1 ring-white/10 lg:col-span-8 sm:p-10">
                                <div className="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-white/10">
                                    <div>
                                        <h3 className="font-display text-2xl font-semibold text-white">
                                            Hardened Institutional Security Guarantees
                                        </h3>
                                        <p className="mt-1 text-sm text-slate-400">
                                            Enforced across container orchestration, Sanctum auth, and WebSocket channels.
                                        </p>
                                    </div>
                                    <span className="inline-flex items-center gap-2 rounded-full bg-teal-500/15 px-3.5 py-1.5 font-mono text-[11px] font-semibold text-teal-300">
                                        <ShieldCheck className="h-3.5 w-3.5" />
                                        OWASP ASVS Hardened
                                    </span>
                                </div>

                                <div className="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                                    {SECURITY_INVARIANTS.map((sec) => (
                                        <div key={sec.title} className="rounded-xl bg-white/5 p-5">
                                            <div className="flex items-start gap-3">
                                                <CheckCircle2 className="mt-0.5 h-4 w-4 shrink-0 text-teal-400" />
                                                <div>
                                                    <p className="text-sm font-semibold text-white">{sec.title}</p>
                                                    <p className="mt-1 text-xs leading-relaxed text-slate-400">
                                                        {sec.detail}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="relative overflow-hidden rounded-xl bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-8 text-white shadow-[0_32px_80px_-24px_rgba(14,165,233,0.55)] lg:col-span-4 sm:p-10">
                                <div
                                    aria-hidden="true"
                                    className="pointer-events-none absolute -bottom-16 -right-12 h-52 w-52 rounded-full bg-white/15 blur-2xl"
                                />
                                <div className="relative z-10 flex h-full flex-col justify-between gap-8">
                                    <div className="space-y-4">
                                        <span className="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/20 backdrop-blur-md">
                                            <Server className="h-6 w-6 text-white" />
                                        </span>
                                        <h3 className="font-display text-2xl font-semibold leading-tight">
                                            Deploy on-premise or private campus cloud
                                        </h3>
                                        <p className="text-sm leading-relaxed text-sky-100/90">
                                            Standard Docker + crun Linux host, Laravel 11 API, Redis pub/sub, and offline Nix binary cache. Read our deployment runbook for exact hardware sizing.
                                        </p>
                                    </div>

                                    <div className="flex flex-wrap gap-3">
                                        <MagneticButton
                                            href="/docs"
                                            size="md"
                                            variant="secondary"
                                            className="!bg-white !text-blue-700 hover:!bg-sky-50 !ring-0"
                                        >
                                            Read Architecture Docs
                                            <ArrowRight className="h-4 w-4" />
                                        </MagneticButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* ═══════════════════════════════════════════════════════════════════════
                    SECTION 9 — INTERACTIVE INSTITUTIONAL FAQ + CAMPUS SANCTUARY CTA
                ═══════════════════════════════════════════════════════════════════════ */}
                <section className="relative py-16 sm:py-24">
                    <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10">
                        <div className="vl-glide-in grid grid-cols-1 gap-10 lg:grid-cols-12">
                            <div className="space-y-5 lg:col-span-4">
                                <h2 className="font-display text-3xl font-semibold leading-[1.06] tracking-[-0.03em] text-vl-ink sm:text-4xl text-balance">
                                    Questions from department chairs &amp; campus IT
                                </h2>
                                <p className="text-sm leading-relaxed text-vl-ink-secondary">
                                    Clear answers on sovereignty, AI governance, offline VSIX security, and hardware requirements.
                                </p>
                                <div className="pt-2">
                                    <MagneticButton href="/contact" variant="secondary" size="md">
                                        Schedule an architecture walkthrough
                                        <ArrowRight className="h-4 w-4" />
                                    </MagneticButton>
                                </div>
                            </div>

                            <div className="space-y-3 lg:col-span-8">
                                {FAQ_ITEMS.map((faq, idx) => {
                                    const expanded = openFaq === idx;
                                    return (
                                        <div
                                            key={faq.q}
                                            className="overflow-hidden rounded-2xl bg-vl-surface/90 ring-1 ring-vl-border/15 transition-colors"
                                        >
                                            <button
                                                type="button"
                                                onClick={() => setOpenFaq(expanded ? -1 : idx)}
                                                aria-expanded={expanded}
                                                className="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
                                            >
                                                <span className="font-display text-base font-semibold text-vl-ink sm:text-lg">
                                                    {faq.q}
                                                </span>
                                                <span
                                                    className={cn(
                                                        'flex h-9 w-9 shrink-0 items-center justify-center rounded-full transition-colors',
                                                        expanded
                                                            ? 'bg-sky-500 text-white'
                                                            : 'bg-vl-surface-raised text-vl-ink-secondary'
                                                    )}
                                                >
                                                    {expanded ? (
                                                        <Minus className="h-4 w-4" />
                                                    ) : (
                                                        <Plus className="h-4 w-4" />
                                                    )}
                                                </span>
                                            </button>

                                            <AnimatePresence initial={false}>
                                                {expanded && (
                                                    <motion.div
                                                        initial={{ height: 0, opacity: 0 }}
                                                        animate={{ height: 'auto', opacity: 1 }}
                                                        exit={{ height: 0, opacity: 0 }}
                                                        transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                                                        className="overflow-hidden"
                                                    >
                                                        <div className="px-6 pb-6 text-sm leading-relaxed text-vl-ink-secondary">
                                                            {faq.a}
                                                        </div>
                                                    </motion.div>
                                                )}
                                            </AnimatePresence>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    </div>
                </section>

                {/* Architectural Campus Pilot Sanctuary CTA */}
                <section className="relative py-16 sm:py-24">
                    <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10">
                        <div className="vl-glide-in relative overflow-hidden rounded-xl bg-[#060C18] px-8 py-16 text-white shadow-[0_38px_95px_-28px_rgba(2,12,27,0.88)] sm:px-16 sm:py-20">
                            <img
                                src="/images/redesign/university-atrium-study.jpg"
                                alt="Modern university atrium with computer science students collaborating"
                                loading="lazy"
                                decoding="async"
                                className="absolute inset-0 h-full w-full object-cover opacity-30"
                            />
                            <div
                                aria-hidden="true"
                                className="absolute inset-0 bg-gradient-to-r from-[#05080F]/95 via-[#07192E]/85 to-[#04242A]/75"
                            />

                            <div className="relative z-10 flex flex-col justify-between gap-10 lg:flex-row lg:items-end">
                                <div className="max-w-2xl space-y-4">
                                    <h2 className="font-display text-3xl font-semibold leading-[1.06] tracking-[-0.03em] text-white sm:text-5xl text-balance">
                                        Bring sovereign cloud coding labs to your next academic term.
                                    </h2>
                                    <p className="text-base leading-relaxed text-sky-100/80">
                                        Launch a dedicated department cluster with pinned Nix templates, audited AI patch governance, and campus ERP tools.
                                    </p>
                                </div>

                                <div className="flex flex-wrap items-center gap-3.5 shrink-0">
                                    <MagneticButton href="/contact" size="lg" variant="primary">
                                        Request Campus Pilot
                                        <ArrowRight className="h-4 w-4" />
                                    </MagneticButton>
                                    <MagneticButton
                                        href="/login"
                                        size="lg"
                                        variant="secondary"
                                        className="!bg-white/15 !text-white hover:!bg-white/25 !ring-white/20"
                                    >
                                        Sign in to Portal
                                    </MagneticButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </PublicLayout>
    );
}

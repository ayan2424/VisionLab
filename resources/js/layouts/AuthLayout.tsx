import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'motion/react';
import { ArrowLeft, ShieldCheck } from 'lucide-react';
import { BrandLogo, FlashToast, ThemeToggle } from '@/components/ui/Primitives';
import type { SharedProps } from '@/types';

export function AuthLayout({
    title,
    children,
}: {
    title: string;
    children: React.ReactNode;
}) {
    const { props } = usePage<SharedProps>();

    return (
        <div className="relative min-h-screen grid grid-cols-1 lg:grid-cols-12 bg-vl-bg text-vl-ink">
            <Head title={title} />
            <FlashToast flash={props.flash} />

            {/* Left Architectural Studio Panel */}
            <div className="relative hidden lg:flex lg:col-span-6 flex-col justify-between overflow-hidden bg-[#050911] p-12 text-white">
                <img
                    src="/images/redesign/campus-glass-lab.jpg"
                    alt="VisionLab modern university glass computer science studio"
                    className="absolute inset-0 h-full w-full object-cover opacity-35"
                />
                <div
                    aria-hidden="true"
                    className="absolute inset-0 bg-gradient-to-br from-[#050911]/95 via-[#071529]/80 to-[#04232A]/85"
                />

                <div className="relative z-10 flex items-center justify-between">
                    <Link href="/" prefetch="hover">
                        <BrandLogo />
                    </Link>
                    <Link
                        href="/"
                        prefetch="hover"
                        className="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-4 py-2 text-xs font-medium text-white/85 hover:bg-white/18 hover:text-white transition-colors"
                    >
                        <ArrowLeft className="h-3.5 w-3.5" />
                        Back to VisionLab
                    </Link>
                </div>

                <div className="relative z-10 max-w-lg space-y-6">
                    <h2 className="font-display text-4xl font-semibold tracking-[-0.03em] leading-[1.08] text-white">
                        Deterministic cloud labs provisioned for every enrolled cohort.
                    </h2>
                    <p className="text-sm leading-relaxed text-slate-300">
                        Sign in with your institutional email to launch isolated Nix containers, review AI-proposed code patches, and submit graded coursework with full integrity telemetry.
                    </p>
                    <div className="flex items-center gap-3.5 rounded-2xl bg-white/[0.06] p-5 backdrop-blur-md ring-1 ring-inset ring-white/10">
                        <ShieldCheck className="h-5 w-5 shrink-0 text-teal-300" />
                        <p className="text-xs text-slate-200 leading-relaxed">
                            Institutional identity enforced — self-registration is disabled so container quotas and academic records remain verified by your campus administrator.
                        </p>
                    </div>
                </div>

                <div className="relative z-10 flex items-center justify-between text-xs font-mono text-slate-400">
                    <span>Sovereign VS Code + Nix</span>
                    <span>SHA-256 Verified Runtime</span>
                </div>
            </div>

            {/* Right Clean Auth Column */}
            <div className="relative flex flex-col justify-between p-6 sm:p-10 lg:col-span-6">
                <div className="flex items-center justify-between">
                    <Link href="/" prefetch="hover" className="lg:hidden">
                        <BrandLogo />
                    </Link>
                    <div className="ml-auto flex items-center gap-3">
                        <Link
                            href="/contact"
                            prefetch="hover"
                            className="text-xs font-medium text-vl-ink-secondary hover:text-vl-ink transition-colors"
                        >
                            Request Campus Pilot
                        </Link>
                        <ThemeToggle />
                    </div>
                </div>

                <motion.div
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.3, ease: [0.16, 1, 0.3, 1] }}
                    className="mx-auto my-auto w-full max-w-[410px] py-10"
                >
                    {children}
                </motion.div>

                <p className="text-center text-xs text-vl-ink-muted">
                    Protected by Sanctum &amp; OWASP ASVS session hardening.
                </p>
            </div>
        </div>
    );
}

import React, { useEffect, useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'motion/react';
import { ArrowRight, Menu, X } from 'lucide-react';
import { cn } from '@/lib/cn';
import { SmoothScroll } from '@/components/SmoothScroll';
import { BrandLogo, FlashToast, MagneticButton, ThemeToggle } from '@/components/ui/Primitives';
import type { SharedProps } from '@/types';

const NAV_LINKS = [
    { label: 'Architecture', href: '/features' },
    { label: 'Documentation', href: '/docs' },
    { label: 'Institution', href: '/about' },
    { label: 'Campus Pilot', href: '/contact' },
];

export function PublicLayout({
    title,
    children,
}: {
    title?: string;
    children: React.ReactNode;
}) {
    const { url, props } = usePage<SharedProps>();
    const user = props.auth.user;
    const [scrolled, setScrolled] = useState(false);
    const [mobileOpen, setMobileOpen] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 20);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    useEffect(() => {
        setMobileOpen(false);
    }, [url]);

    return (
        <SmoothScroll>
            <Head title={title} />
            <FlashToast flash={props.flash} />

            <div className="relative min-h-screen flex flex-col bg-vl-bg text-vl-ink">
                {/* Floating Full-Pill Architectural Navigation Island */}
                <header
                    className={cn(
                        'fixed inset-x-0 top-0 z-50 transition-all duration-300 ease-vl-out',
                        scrolled ? 'py-3' : 'py-5'
                    )}
                >
                    <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10">
                        <div
                            className={cn(
                                'flex h-[62px] items-center justify-between rounded-full px-3 sm:px-4 transition-all duration-300 ease-vl-out',
                                scrolled
                                    ? 'bg-vl-surface/88 dark:bg-[#080E1A]/86 backdrop-blur-2xl shadow-[0_20px_50px_-16px_rgba(0,0,0,0.55),inset_0_1px_0_rgba(255,255,255,0.1)] ring-1 ring-vl-border/12'
                                    : 'bg-vl-surface/45 dark:bg-[#080E1A]/42 backdrop-blur-xl ring-1 ring-vl-border/10'
                            )}
                        >
                            <Link href="/" prefetch="hover" className="pl-1 focus-visible:outline-none">
                                <BrandLogo />
                            </Link>

                            <nav aria-label="Primary navigation" className="hidden md:flex items-center gap-1 rounded-full bg-vl-bg/45 p-1">
                                {NAV_LINKS.map((item) => {
                                    const active = url === item.href || url.startsWith(item.href + '/');
                                    return (
                                        <Link
                                            key={item.href}
                                            href={item.href}
                                            prefetch="hover"
                                            className={cn(
                                                'relative rounded-full px-4 py-2 text-[13px] font-medium transition-colors duration-200',
                                                active
                                                    ? 'text-vl-ink'
                                                    : 'text-vl-ink-secondary hover:text-vl-ink'
                                            )}
                                        >
                                            {active && (
                                                <motion.span
                                                    layoutId="public-nav-pill"
                                                    transition={{ type: 'spring', stiffness: 360, damping: 32 }}
                                                    className="absolute inset-0 rounded-full bg-vl-surface dark:bg-white/[0.11] shadow-sm"
                                                />
                                            )}
                                            <span className="relative z-10">{item.label}</span>
                                        </Link>
                                    );
                                })}
                            </nav>

                            <div className="flex items-center gap-2">
                                <ThemeToggle />

                                {user ? (
                                    <MagneticButton href="/dashboard" size="sm" variant="primary" className="hidden sm:inline-flex">
                                        Open Portal
                                        <ArrowRight className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />
                                    </MagneticButton>
                                ) : (
                                    <>
                                        <Link
                                            href="/login"
                                            prefetch="hover"
                                            className="hidden sm:inline-flex items-center rounded-full px-4 py-2 text-[13px] font-medium text-vl-ink-secondary hover:bg-vl-surface-raised/70 hover:text-vl-ink transition-colors"
                                        >
                                            Sign in
                                        </Link>
                                        <MagneticButton href="/contact" size="sm" variant="primary" className="hidden sm:inline-flex">
                                            Request Pilot
                                            <ArrowRight className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />
                                        </MagneticButton>
                                    </>
                                )}

                                <button
                                    type="button"
                                    onClick={() => setMobileOpen((v) => !v)}
                                    aria-label="Toggle navigation menu"
                                    className="inline-flex h-10 w-10 items-center justify-center rounded-full bg-vl-surface/85 text-vl-ink md:hidden"
                                >
                                    {mobileOpen ? <X className="h-4 w-4" /> : <Menu className="h-4 w-4" />}
                                </button>
                            </div>
                        </div>

                        <AnimatePresence>
                            {mobileOpen && (
                                <motion.div
                                    initial={{ opacity: 0, y: -8, scale: 0.98 }}
                                    animate={{ opacity: 1, y: 0, scale: 1 }}
                                    exit={{ opacity: 0, y: -8, scale: 0.98 }}
                                    transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                                    className="mt-3 overflow-hidden rounded-2xl bg-vl-surface/95 p-5 shadow-2xl backdrop-blur-2xl ring-1 ring-vl-border/15 md:hidden"
                                >
                                    <div className="flex flex-col gap-1">
                                        {NAV_LINKS.map((item) => (
                                            <Link
                                                key={item.href}
                                                href={item.href}
                                                className="rounded-full px-4 py-2.5 text-sm font-medium text-vl-ink-secondary hover:bg-vl-surface-raised hover:text-vl-ink"
                                            >
                                                {item.label}
                                            </Link>
                                        ))}
                                    </div>
                                    <div className="mt-4 flex flex-col gap-2.5 border-t border-vl-border/15 pt-4">
                                        <Link
                                            href={user ? '/dashboard' : '/login'}
                                            className="flex items-center justify-center rounded-full bg-gradient-to-b from-sky-500 to-blue-600 px-5 py-3 text-sm font-semibold text-white"
                                        >
                                            {user ? 'Open Portal' : 'Sign in to Portal'}
                                        </Link>
                                        {!user && (
                                            <Link
                                                href="/contact"
                                                className="flex items-center justify-center rounded-full bg-vl-surface-raised px-5 py-3 text-sm font-medium text-vl-ink"
                                            >
                                                Request Campus Pilot
                                            </Link>
                                        )}
                                    </div>
                                </motion.div>
                            )}
                        </AnimatePresence>
                    </div>
                </header>

                <main id="main-content" className="flex-1">
                    {children}
                </main>

                {/* Architectural Footer */}
                <footer className="relative border-t border-vl-border/10 bg-vl-bg">
                    <div className="mx-auto max-w-[1380px] px-4 py-16 sm:px-6 lg:px-10">
                        <div className="grid grid-cols-1 gap-12 lg:grid-cols-12">
                            <div className="space-y-4 lg:col-span-5">
                                <BrandLogo />
                                <p className="max-w-sm text-sm leading-relaxed text-vl-ink-secondary">
                                    Sovereign cloud coding laboratories engineered for universities: deterministic Nix containers, human-in-the-loop AI patch review, and campus-wide LMS &amp; ERP governance.
                                </p>
                            </div>

                            <div className="grid grid-cols-2 gap-8 sm:grid-cols-3 lg:col-span-7">
                                <div className="space-y-3">
                                    <p className="text-xs font-semibold text-vl-ink">Platform</p>
                                    <ul className="space-y-2.5 text-sm text-vl-ink-secondary">
                                        <li><Link href="/features" prefetch="hover" className="hover:text-vl-ink transition-colors">Architecture</Link></li>
                                        <li><Link href="/docs" prefetch="hover" className="hover:text-vl-ink transition-colors">Documentation</Link></li>
                                        <li><Link href="/about" prefetch="hover" className="hover:text-vl-ink transition-colors">Institution</Link></li>
                                    </ul>
                                </div>
                                <div className="space-y-3">
                                    <p className="text-xs font-semibold text-vl-ink">Access</p>
                                    <ul className="space-y-2.5 text-sm text-vl-ink-secondary">
                                        <li><Link href="/login" prefetch="hover" className="hover:text-vl-ink transition-colors">Sign in to Portal</Link></li>
                                        <li><Link href="/contact" prefetch="hover" className="hover:text-vl-ink transition-colors">Request Campus Pilot</Link></li>
                                    </ul>
                                </div>
                                <div className="space-y-3">
                                    <p className="text-xs font-semibold text-vl-ink">Runtime Guarantees</p>
                                    <ul className="space-y-2 text-xs font-mono text-vl-ink-muted">
                                        <li>Nix Flake / dev.nix</li>
                                        <li>SHA-256 VSIX Supply Chain</li>
                                        <li>crun Isolated Containers</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div className="mt-14 flex flex-col items-start justify-between gap-4 border-t border-vl-border/10 pt-8 text-xs text-vl-ink-muted sm:flex-row sm:items-center">
                            <p>&copy; {new Date().getFullYear()} VisionLab Academic Infrastructure. All rights reserved.</p>
                            <p className="font-mono">React 19 · Inertia v2 · Laravel 11 · Sovereign Docker</p>
                        </div>
                    </div>
                </footer>
            </div>
        </SmoothScroll>
    );
}

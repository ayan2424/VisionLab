import React, { useRef, useState } from 'react';
import { Link } from '@inertiajs/react';
import { AnimatePresence, motion } from 'motion/react';
import { AlertCircle, CheckCircle2, Moon, Sun, X } from 'lucide-react';
import { cn } from '@/lib/cn';
import { useTheme } from '@/components/ThemeProvider';
import type { FlashMessages } from '@/types';

/* ── Brand Monogram & Wordmark (Full Circle Badge) ───────────────────────── */
export function BrandLogo({
    compact = false,
    className,
}: {
    compact?: boolean;
    className?: string;
}) {
    return (
        <span className={cn('inline-flex items-center gap-2.5 select-none', className)}>
            <span className="relative flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-sky-400 via-blue-600 to-teal-500 shadow-[0_8px_22px_-4px_rgba(14,165,233,0.55),inset_0_1px_0_rgba(255,255,255,0.48)]">
                <svg className="h-[18px] w-[18px] text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path
                        d="M4.5 6.5L10.2 18.2C10.55 18.92 11.58 18.92 11.93 18.2L17.6 6.5"
                        stroke="currentColor"
                        strokeWidth="2.3"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                    />
                    <path
                        d="M15.2 17.5H20.2"
                        stroke="currentColor"
                        strokeWidth="2.3"
                        strokeLinecap="round"
                    />
                </svg>
            </span>
            {!compact && (
                <span className="font-display text-[17px] font-semibold tracking-[-0.025em] text-vl-ink">
                    Vision<span className="text-sky-500 dark:text-sky-400">Lab</span>
                </span>
            )}
        </span>
    );
}

/* ── Circular Theme Toggle ───────────────────────────────────────────────── */
export function ThemeToggle({ className }: { className?: string }) {
    const { isDark, toggleTheme } = useTheme();

    return (
        <button
            type="button"
            onClick={toggleTheme}
            aria-label={isDark ? 'Switch to light theme' : 'Switch to dark theme'}
            title={isDark ? 'Switch to light mode' : 'Switch to dark mode'}
            className={cn(
                'relative inline-flex h-10 w-10 items-center justify-center rounded-full text-vl-ink-secondary transition-colors duration-200',
                'bg-vl-surface/85 hover:bg-vl-surface-raised hover:text-vl-ink',
                'ring-1 ring-inset ring-vl-border/15 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400',
                className
            )}
        >
            <AnimatePresence mode="wait" initial={false}>
                {isDark ? (
                    <motion.span
                        key="sun"
                        initial={{ opacity: 0, rotate: -45, scale: 0.8 }}
                        animate={{ opacity: 1, rotate: 0, scale: 1 }}
                        exit={{ opacity: 0, rotate: 45, scale: 0.8 }}
                        transition={{ duration: 0.16, ease: [0.16, 1, 0.3, 1] }}
                    >
                        <Sun className="h-4 w-4 text-amber-300" strokeWidth={1.8} />
                    </motion.span>
                ) : (
                    <motion.span
                        key="moon"
                        initial={{ opacity: 0, rotate: 45, scale: 0.8 }}
                        animate={{ opacity: 1, rotate: 0, scale: 1 }}
                        exit={{ opacity: 0, rotate: -45, scale: 0.8 }}
                        transition={{ duration: 0.16, ease: [0.16, 1, 0.3, 1] }}
                    >
                        <Moon className="h-4 w-4 text-sky-600" strokeWidth={1.8} />
                    </motion.span>
                )}
            </AnimatePresence>
        </button>
    );
}

/* ── Full-Pill Magnetic Button / Inertia Prefetch Link ───────────────────── */
interface MagneticButtonProps {
    children: React.ReactNode;
    href?: string;
    external?: boolean;
    onClick?: () => void;
    type?: 'button' | 'submit' | 'reset';
    variant?: 'primary' | 'secondary' | 'ghost' | 'danger';
    size?: 'sm' | 'md' | 'lg';
    disabled?: boolean;
    className?: string;
}

export function MagneticButton({
    children,
    href,
    external = false,
    onClick,
    type = 'button',
    variant = 'primary',
    size = 'md',
    disabled = false,
    className,
}: MagneticButtonProps) {
    const ref = useRef<HTMLDivElement>(null);
    const [offset, setOffset] = useState({ x: 0, y: 0 });

    const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
        if (disabled || !ref.current) return;
        const rect = ref.current.getBoundingClientRect();
        const x = (e.clientX - rect.left - rect.width / 2) * 0.18;
        const y = (e.clientY - rect.top - rect.height / 2) * 0.18;
        setOffset({ x, y });
    };

    const reset = () => setOffset({ x: 0, y: 0 });

    const styles = cn(
        'group relative inline-flex items-center justify-center gap-2 font-medium rounded-full transition-[box-shadow,background-color,color,border-color] duration-200 select-none whitespace-nowrap',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 focus-visible:ring-offset-2 focus-visible:ring-offset-vl-bg',
        disabled && 'opacity-50 pointer-events-none',
        size === 'sm' && 'px-4 py-2 text-xs',
        size === 'md' && 'px-6 py-2.5 text-sm',
        size === 'lg' && 'px-8 py-3.5 text-[15px]',
        variant === 'primary' &&
            'text-white bg-gradient-to-b from-sky-500 via-blue-600 to-blue-700 hover:from-sky-400 hover:via-blue-500 hover:to-blue-600 shadow-[0_14px_34px_-8px_rgba(14,165,233,0.55),inset_0_1px_0_rgba(255,255,255,0.36)]',
        variant === 'secondary' &&
            'text-vl-ink bg-vl-surface/90 hover:bg-vl-surface-raised ring-1 ring-inset ring-vl-border/20 shadow-[0_8px_24px_-8px_rgba(2,6,23,0.35),inset_0_1px_0_rgba(255,255,255,0.1)] backdrop-blur-md',
        variant === 'ghost' &&
            'text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface-raised/75',
        variant === 'danger' &&
            'text-white bg-gradient-to-b from-rose-500 to-rose-600 hover:from-rose-400 hover:to-rose-500 shadow-[0_10px_24px_-8px_rgba(225,29,72,0.5),inset_0_1px_0_rgba(255,255,255,0.28)]',
        className
    );

    return (
        <motion.div
            ref={ref}
            onMouseMove={handleMouseMove}
            onMouseLeave={reset}
            animate={{ x: offset.x, y: offset.y }}
            whileTap={disabled ? undefined : { scale: 0.97 }}
            transition={{ type: 'spring', stiffness: 280, damping: 20, mass: 0.6 }}
            className="inline-flex"
        >
            {href ? (
                external ? (
                    <a href={href} className={styles}>
                        {children}
                    </a>
                ) : (
                    <Link href={href} prefetch="hover" className={styles}>
                        {children}
                    </Link>
                )
            ) : (
                <button type={type} onClick={onClick} disabled={disabled} className={styles}>
                    {children}
                </button>
            )}
        </motion.div>
    );
}

/* ── Super-Rounded Organic Spotlight Container (rounded-[32px]) ──────────── */
interface SpotlightSurfaceProps extends React.HTMLAttributes<HTMLDivElement> {
    children: React.ReactNode;
    accent?: 'sky' | 'teal' | 'amber';
}

export function SpotlightSurface({
    children,
    accent = 'sky',
    className,
    onMouseMove,
    ...rest
}: SpotlightSurfaceProps) {
    const surfaceRef = useRef<HTMLDivElement>(null);

    const handleMove = (e: React.MouseEvent<HTMLDivElement>) => {
        if (surfaceRef.current) {
            const rect = surfaceRef.current.getBoundingClientRect();
            surfaceRef.current.style.setProperty('--spot-x', `${e.clientX - rect.left}px`);
            surfaceRef.current.style.setProperty('--spot-y', `${e.clientY - rect.top}px`);
        }
        onMouseMove?.(e);
    };

    const glowColor =
        accent === 'teal'
            ? 'rgba(45, 212, 191, 0.13)'
            : accent === 'amber'
            ? 'rgba(251, 191, 36, 0.13)'
            : 'rgba(56, 189, 248, 0.14)';

    return (
        <div
            ref={surfaceRef}
            onMouseMove={handleMove}
            style={{ '--spot-color': glowColor } as React.CSSProperties}
            className={cn(
                'group/spot relative overflow-hidden rounded-[32px] transition-transform duration-300 ease-vl-out',
                'bg-gradient-to-b from-vl-surface/95 to-vl-surface-raised/80 dark:from-[#0E1625]/95 dark:to-[#080D17]/95',
                'shadow-[0_24px_54px_-24px_rgba(2,8,20,0.35),inset_0_1px_0_rgba(255,255,255,0.1)]',
                'dark:shadow-[0_28px_68px_-24px_rgba(0,0,0,0.82),inset_0_1px_0_rgba(255,255,255,0.08)]',
                className
            )}
            {...rest}
        >
            <div
                aria-hidden="true"
                className="pointer-events-none absolute -inset-px opacity-0 transition-opacity duration-300 group-hover/spot:opacity-100"
                style={{
                    background:
                        'radial-gradient(520px circle at var(--spot-x, 50%) var(--spot-y, 0%), var(--spot-color), transparent 65%)',
                }}
            />
            <div className="relative z-10 h-full">{children}</div>
        </div>
    );
}

/* ── Full-Pill Status Badge ──────────────────────────────────────────────── */
export function StatusPill({
    children,
    tone = 'neutral',
    pulse = false,
    className,
}: {
    children: React.ReactNode;
    tone?: 'sky' | 'teal' | 'amber' | 'rose' | 'neutral';
    pulse?: boolean;
    className?: string;
}) {
    const toneMap = {
        sky: 'bg-sky-500/12 text-sky-600 dark:text-sky-300',
        teal: 'bg-teal-500/12 text-teal-700 dark:text-teal-300',
        amber: 'bg-amber-500/14 text-amber-700 dark:text-amber-300',
        rose: 'bg-rose-500/14 text-rose-700 dark:text-rose-300',
        neutral: 'bg-vl-surface-raised text-vl-ink-secondary',
    };
    const dotMap = {
        sky: 'bg-sky-400',
        teal: 'bg-teal-400',
        amber: 'bg-amber-400',
        rose: 'bg-rose-400',
        neutral: 'bg-vl-ink-muted',
    };

    return (
        <span
            className={cn(
                'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1 text-[11px] font-medium tracking-tight',
                toneMap[tone],
                className
            )}
        >
            <span className={cn('h-1.5 w-1.5 rounded-full', dotMap[tone], pulse && 'animate-pulse')} />
            {children}
        </span>
    );
}

/* ── Full-Pill Floating Flash Toast ──────────────────────────────────────── */
export function FlashToast({ flash }: { flash?: FlashMessages }) {
    const message = flash?.success || flash?.status || flash?.error;
    const isError = Boolean(flash?.error && !flash?.success && !flash?.status);
    const [dismissedKey, setDismissedKey] = useState<string | null>(null);

    const visible = Boolean(message && dismissedKey !== message);

    return (
        <AnimatePresence>
            {visible && message && (
                <motion.div
                    initial={{ opacity: 0, y: -12, scale: 0.96 }}
                    animate={{ opacity: 1, y: 0, scale: 1 }}
                    exit={{ opacity: 0, y: -10, scale: 0.96 }}
                    transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                    role="status"
                    className={cn(
                        'fixed top-20 right-5 z-50 flex max-w-md items-center gap-3 rounded-full px-5 py-3 text-sm shadow-2xl backdrop-blur-xl',
                        isError
                            ? 'bg-rose-950/92 text-rose-100 ring-1 ring-rose-400/30'
                            : 'bg-[#091323]/94 text-sky-50 ring-1 ring-sky-400/25'
                    )}
                >
                    {isError ? (
                        <AlertCircle className="h-4 w-4 shrink-0 text-rose-400" />
                    ) : (
                        <CheckCircle2 className="h-4 w-4 shrink-0 text-teal-400" />
                    )}
                    <p className="leading-snug">{message}</p>
                    <button
                        type="button"
                        onClick={() => setDismissedKey(message)}
                        aria-label="Dismiss notification"
                        className="ml-1 rounded-full p-1 text-white/60 hover:bg-white/10 hover:text-white"
                    >
                        <X className="h-3.5 w-3.5" />
                    </button>
                </motion.div>
            )}
        </AnimatePresence>
    );
}

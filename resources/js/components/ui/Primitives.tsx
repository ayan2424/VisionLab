import React, { useRef, useState } from 'react';
import { Link } from '@inertiajs/react';
import { AnimatePresence, motion } from 'motion/react';
import { AlertCircle, CheckCircle2, Moon, Sun, X } from 'lucide-react';
import { cn } from '@/lib/cn';
import { useTheme } from '@/components/ThemeProvider';
import type { FlashMessages } from '@/types';

/* ── Authentic 6-Path VisionLab Geometric Logo Mark ──────────────────────── */
export function BrandLogo({
    compact = false,
    inverted = false,
    size = 'h-9 w-9',
    className,
}: {
    compact?: boolean;
    inverted?: boolean;
    size?: string;
    className?: string;
}) {
    return (
        <span className={cn('inline-flex items-center gap-2.5 select-none group', className)}>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 144 144"
                className={cn(
                    size,
                    'shrink-0 transition-transform duration-200 group-hover:scale-[1.04]',
                    inverted ? 'text-white' : 'text-vl-ink'
                )}
                aria-hidden="true"
            >
                <path
                    fill="currentColor"
                    d="M 11.3,33.3 L 54.6,110.5 L 63.4,110.5 L 78.9,83.2 L 63.2,83.2 L 59.1,89.9 L 27.3,33.3 Z"
                />
                <path
                    fill="currentColor"
                    opacity={inverted ? 0.8 : 0.72}
                    d="M 44.2,48.9 L 51.7,62.5 L 61.3,47.5 L 118.4,47.5 L 126.4,33.3 L 54.7,33.3 Z"
                />
                <path
                    fill={inverted ? '#7DD3FC' : '#0284C7'}
                    d="M 62.8,57.4 L 60.5,59.6 L 67.2,66.1 L 60.6,73.3 L 62.9,75.5 L 71.9,66.1 Z"
                />
                <path
                    fill="currentColor"
                    opacity={inverted ? 0.92 : 0.88}
                    d="M 105.7,55.7 L 89.8,55.7 L 97.0,68.7 L 73.5,110.7 L 89.5,110.7 L 113.3,68.9 Z"
                />
                <path
                    fill="currentColor"
                    opacity={inverted ? 0.8 : 0.72}
                    d="M 90.1,110.6 L 126.3,110.6 L 134.5,96.2 L 98.1,96.2 Z"
                />
                <path
                    fill={inverted ? '#5EEAD4' : '#0D9488'}
                    d="M 72.3,72.5 L 85.8,72.5 L 85.8,75.6 L 72.3,75.6 Z"
                />
            </svg>
            {!compact && (
                <span
                    className={cn(
                        'font-display text-[17px] font-bold tracking-[-0.025em]',
                        inverted ? 'text-white' : 'text-vl-ink'
                    )}
                >
                    Vision
                    <span className={inverted ? 'text-sky-200 font-medium' : 'text-sky-500 dark:text-sky-400 font-medium'}>
                        Lab
                    </span>
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

/* ── Executive Spotlight Card Container (rounded-2xl) ────────────────────── */
interface SpotlightSurfaceProps extends React.HTMLAttributes<HTMLDivElement> {
    children: React.ReactNode;
    accent?: 'sky' | 'teal' | 'amber' | 'none';
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
        accent === 'none'
            ? 'transparent'
            : accent === 'teal'
            ? 'rgba(45, 212, 191, 0.12)'
            : accent === 'amber'
            ? 'rgba(251, 191, 36, 0.12)'
            : 'rgba(56, 189, 248, 0.13)';

    return (
        <div
            ref={surfaceRef}
            onMouseMove={handleMove}
            style={{ '--spot-color': glowColor } as React.CSSProperties}
            className={cn(
                'group/spot relative overflow-hidden rounded-2xl backdrop-blur-2xl transition-all duration-300 ease-vl-out',
                'bg-white/70 dark:bg-[#0A1326]/65',
                'shadow-[0_18px_46px_-22px_rgba(2,12,27,0.16),inset_0_1px_0_rgba(255,255,255,0.82)]',
                'dark:shadow-[0_24px_58px_-22px_rgba(0,0,0,0.72),inset_0_1px_0_rgba(255,255,255,0.12)]',
                'ring-1 ring-inset ring-slate-900/10 dark:ring-white/10',
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
        sky: 'bg-sky-500/15 text-sky-600 dark:text-sky-300',
        teal: 'bg-teal-500/15 text-teal-700 dark:text-teal-300',
        amber: 'bg-amber-500/15 text-amber-700 dark:text-amber-300',
        rose: 'bg-rose-500/15 text-rose-700 dark:text-rose-300',
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
                'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-[11px] font-medium tracking-tight',
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
                            ? 'bg-rose-950/95 text-rose-100 ring-1 ring-rose-400/30'
                            : 'bg-[#091323]/95 text-sky-50 ring-1 ring-sky-400/25'
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

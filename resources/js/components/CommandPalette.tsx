import React, { useEffect, useMemo, useRef, useState } from 'react';
import { router } from '@inertiajs/react';
import {
    Activity,
    Award,
    Bell,
    BookOpen,
    Building2,
    CheckSquare,
    Code2,
    CornerDownLeft,
    FileCode2,
    LayoutDashboard,
    PlusCircle,
    Search,
    ShieldCheck,
    Terminal,
    TrendingUp,
    Users,
} from 'lucide-react';
import { cn } from '@/lib/cn';
import type { AuthUser } from '@/types';

interface PaletteItem {
    id: string;
    label: string;
    group: string;
    href: string;
    icon: React.ComponentType<{ className?: string }>;
    hardNavigate?: boolean;
}

export function CommandPalette({
    user,
    open,
    onClose,
}: {
    user: AuthUser;
    open: boolean;
    onClose: () => void;
}) {
    const [query, setQuery] = useState('');
    const [activeIndex, setActiveIndex] = useState(0);
    const inputRef = useRef<HTMLInputElement>(null);

    const items = useMemo<PaletteItem[]>(() => {
        const list: PaletteItem[] = [
            { id: 'dash', label: 'Dashboard Overview', group: 'Navigate', href: '/dashboard', icon: LayoutDashboard },
            { id: 'ws', label: 'My Cloud Workspaces', group: 'Workspaces', href: '/workspace', icon: Terminal },
            { id: 'ws-new', label: 'Provision New Workspace', group: 'Workspaces', href: '/workspace/create', icon: PlusCircle },
            { id: 'courses', label: 'Courses & Curriculum', group: 'Academic', href: '/courses', icon: BookOpen },
            { id: 'announcements', label: 'Campus Announcements', group: 'Academic', href: '/announcements', icon: Bell },
            { id: 'progress', label: 'Progress & Analytics', group: 'Academic', href: '/progress', icon: TrendingUp },
            { id: 'gamification', label: 'XP & Badges', group: 'Academic', href: '/gamification', icon: Award },
            { id: 'docs', label: 'Platform Documentation', group: 'Resources', href: '/docs', icon: FileCode2 },
        ];

        if (user.role === 'instructor' || user.role === 'admin') {
            list.push({ id: 'queue', label: 'Grading Queue', group: 'Faculty', href: '/submissions', icon: CheckSquare });
        }

        if (user.role === 'admin') {
            list.push(
                { id: 'adm-dash', label: 'Admin Command Center', group: 'Governance', href: '/admin', icon: ShieldCheck },
                { id: 'adm-status', label: 'Cluster Telemetry & Healthz', group: 'Governance', href: '/admin/status', icon: Activity },
                { id: 'adm-users', label: 'User Directory', group: 'Governance', href: '/admin/users', icon: Users },
                { id: 'adm-ws', label: 'All Container Workspaces', group: 'Governance', href: '/admin/workspaces', icon: Code2 },
                { id: 'adm-campuses', label: 'Campuses & ERP', group: 'Governance', href: '/admin/campuses', icon: Building2, hardNavigate: true }
            );
        }

        return list;
    }, [user.role]);

    const filtered = useMemo(() => {
        const q = query.trim().toLowerCase();
        if (!q) return items;
        return items.filter(
            (item) => item.label.toLowerCase().includes(q) || item.group.toLowerCase().includes(q)
        );
    }, [items, query]);

    useEffect(() => {
        if (open) {
            setQuery('');
            setActiveIndex(0);
            requestAnimationFrame(() => inputRef.current?.focus());
        }
    }, [open]);

    useEffect(() => {
        if (activeIndex >= filtered.length) setActiveIndex(0);
    }, [filtered.length, activeIndex]);

    const go = (item: PaletteItem) => {
        onClose();
        if (item.hardNavigate) {
            window.location.href = item.href;
        } else {
            router.visit(item.href);
        }
    };

    if (!open) return null;

    return (
        <div
            className="fixed inset-0 z-[90] flex items-start justify-center bg-black/60 px-4 pt-[12vh] backdrop-blur-sm"
            onMouseDown={(e) => {
                if (e.target === e.currentTarget) onClose();
            }}
        >
            <div
                role="dialog"
                aria-modal="true"
                aria-label="Command palette"
                className="w-full max-w-xl overflow-hidden rounded-[32px] bg-vl-surface shadow-[0_32px_90px_-16px_rgba(0,0,0,0.75),inset_0_1px_0_rgba(255,255,255,0.1)] ring-1 ring-vl-border/15"
            >
                <div className="flex items-center gap-3 border-b border-vl-border/12 px-6 py-4">
                    <Search className="h-4 w-4 text-vl-ink-muted" />
                    <input
                        ref={inputRef}
                        type="text"
                        value={query}
                        onChange={(e) => setQuery(e.target.value)}
                        onKeyDown={(e) => {
                            if (e.key === 'Escape') {
                                e.preventDefault();
                                onClose();
                            } else if (e.key === 'ArrowDown') {
                                e.preventDefault();
                                setActiveIndex((i) => (i + 1) % Math.max(filtered.length, 1));
                            } else if (e.key === 'ArrowUp') {
                                e.preventDefault();
                                setActiveIndex((i) => (i - 1 + Math.max(filtered.length, 1)) % Math.max(filtered.length, 1));
                            } else if (e.key === 'Enter' && filtered[activeIndex]) {
                                e.preventDefault();
                                go(filtered[activeIndex]);
                            }
                        }}
                        placeholder="Jump to workspaces, courses, governance, telemetry..."
                        className="w-full border-0 bg-transparent text-sm text-vl-ink placeholder:text-vl-ink-muted focus:outline-none focus:ring-0"
                    />
                    <kbd className="rounded-full bg-vl-surface-raised px-2.5 py-0.5 font-mono text-[10px] text-vl-ink-muted">
                        ESC
                    </kbd>
                </div>

                <div className="max-h-80 overflow-y-auto p-3">
                    {filtered.length === 0 ? (
                        <p className="py-10 text-center text-sm text-vl-ink-muted">No matching routes.</p>
                    ) : (
                        filtered.map((item, idx) => {
                            const Icon = item.icon;
                            const active = idx === activeIndex;
                            return (
                                <button
                                    key={item.id}
                                    type="button"
                                    onMouseEnter={() => setActiveIndex(idx)}
                                    onClick={() => go(item)}
                                    className={cn(
                                        'flex w-full items-center justify-between rounded-full px-4 py-2.5 text-left text-sm transition-colors duration-100',
                                        active
                                            ? 'bg-sky-500/14 text-vl-ink'
                                            : 'text-vl-ink-secondary hover:bg-vl-surface-raised/60'
                                    )}
                                >
                                    <span className="flex items-center gap-3">
                                        <Icon className={cn('h-4 w-4', active ? 'text-sky-400' : 'text-vl-ink-muted')} />
                                        <span className="font-medium">{item.label}</span>
                                    </span>
                                    <span className="flex items-center gap-2 text-[11px] text-vl-ink-muted">
                                        <span>{item.group}</span>
                                        {active && <CornerDownLeft className="h-3 w-3 text-sky-400" />}
                                    </span>
                                </button>
                            );
                        })
                    )}
                </div>
            </div>
        </div>
    );
}

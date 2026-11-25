import React, { useEffect, useLayoutEffect, useMemo, useRef } from 'react';
import { Link } from '@inertiajs/react';
import { AnimatePresence, motion } from 'motion/react';
import {
    ArrowUpRight,
    Bot,
    Loader2,
    PanelRightClose,
    RotateCcw,
    Send,
    Sparkles,
    X,
} from 'lucide-react';
import { cn } from '@/lib/cn';
import { usePortalShell } from '@/components/PortalShellContext';
import type { AuthUser } from '@/types';

function getRoleStarters(role: AuthUser['role']): string[] {
    if (role === 'admin') {
        return [
            'Cluster health & active containers',
            'Institute ERP & accounts summary',
            'Recent security audit events',
        ];
    }
    if (role === 'instructor') {
        return [
            'Triage my pending grading queue',
            'Active student containers',
            'VisionGuard authorship signals',
        ];
    }
    return [
        'Upcoming assignment deadlines',
        'Active workspaces & AI patches',
        'Debug a Nix environment issue',
    ];
}

export function PortalAiDrawer({
    user,
    pageTitle,
    pageUrl,
}: {
    user: AuthUser;
    pageTitle: string;
    pageUrl: string;
}) {
    const {
        aiOpen,
        setAiOpen,
        aiMessages,
        aiDraft,
        setAiDraft,
        aiSending,
        ensureAiSession,
        sendAiMessage,
        resetAiChat,
        aiScrollRef,
    } = usePortalShell();

    const starters = useMemo(() => getRoleStarters(user.role), [user.role]);
    const scrollContainerRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);
    const prevMsgCountRef = useRef<number>(aiMessages.length);

    useEffect(() => {
        ensureAiSession(user);
    }, [ensureAiSession, user]);

    // Synchronously restore exact scroll position on route change so the pane never jumps
    useLayoutEffect(() => {
        if (scrollContainerRef.current && aiScrollRef.current > 0) {
            scrollContainerRef.current.scrollTop = aiScrollRef.current;
        }
    }, [aiScrollRef]);

    // Scroll to bottom only when a new message arrives or sending starts
    useEffect(() => {
        if (aiMessages.length > prevMsgCountRef.current || aiSending) {
            if (scrollContainerRef.current) {
                scrollContainerRef.current.scrollTop = scrollContainerRef.current.scrollHeight;
                aiScrollRef.current = scrollContainerRef.current.scrollTop;
            }
        }
        prevMsgCountRef.current = aiMessages.length;
    }, [aiMessages.length, aiSending, aiScrollRef]);

    const handleScroll = () => {
        if (scrollContainerRef.current) {
            aiScrollRef.current = scrollContainerRef.current.scrollTop;
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        void sendAiMessage(aiDraft, user, pageTitle, pageUrl);
    };

    const closeDrawer = () => setAiOpen(false);

    const panelBody = (isMobile: boolean) => (
        <div className="flex h-full w-full flex-col">
            {/* Seamless Top Sub-Bar (No heavy divider — flows directly down from Top Header) */}
            <div className="flex items-center justify-between gap-2 px-4 pt-2 pb-3">
                <div className="flex items-center gap-2.5 min-w-0">
                    <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 text-white shadow-[0_8px_20px_-6px_rgba(2,132,199,0.55)]">
                        <Sparkles className="h-4 w-4" />
                    </span>
                    <div className="min-w-0">
                        <h2 className="truncate font-display text-sm font-bold tracking-[-0.02em] text-slate-900 dark:text-white">
                            Vision AI
                        </h2>
                        <p className="truncate font-mono text-[10px] text-slate-600 dark:text-slate-300/75">
                            {pageTitle} · {user.role}
                        </p>
                    </div>
                </div>

                <div className="flex items-center gap-1">
                    <button
                        type="button"
                        onClick={() => resetAiChat(user)}
                        title="Reset chat"
                        aria-label="Reset conversation"
                        className="flex h-8 w-8 items-center justify-center rounded-full bg-white/55 text-slate-700 hover:bg-white/90 hover:text-slate-950 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/20 dark:hover:text-white transition-all"
                    >
                        <RotateCcw className="h-3.5 w-3.5" />
                    </button>
                    <button
                        type="button"
                        onClick={closeDrawer}
                        title="Hide AI sidebar"
                        aria-label="Close Vision AI"
                        className="flex h-8 w-8 items-center justify-center rounded-full bg-white/55 text-slate-700 hover:bg-white/90 hover:text-slate-950 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/20 dark:hover:text-white transition-all"
                    >
                        {isMobile ? <X className="h-4 w-4" /> : <PanelRightClose className="h-4 w-4" />}
                    </button>
                </div>
            </div>

            {/* Role-Aware Quick Prompt Pills */}
            <div className="px-4 pb-3">
                <div className="flex flex-wrap gap-1.5">
                    {starters.map((prompt) => (
                        <button
                            key={prompt}
                            type="button"
                            disabled={aiSending}
                            onClick={() => void sendAiMessage(prompt, user, pageTitle, pageUrl)}
                            className="rounded-full bg-white/60 px-3 py-1 text-[11px] font-medium text-slate-700 ring-1 ring-inset ring-white/80 hover:bg-gradient-to-r hover:from-sky-500 hover:to-blue-600 hover:text-white hover:ring-transparent dark:bg-white/10 dark:text-slate-200 dark:ring-white/15 dark:hover:bg-sky-500 dark:hover:text-white transition-all disabled:opacity-50"
                        >
                            {prompt}
                        </button>
                    ))}
                </div>
            </div>

            {/* Persistent Conversation Feed (Hover-only zero-shift scrollbar) */}
            <div
                ref={scrollContainerRef}
                onScroll={handleScroll}
                className="vl-sidebar-scroll flex-1 space-y-3.5 px-4 py-2"
            >
                {aiMessages.map((msg) => (
                    <div
                        key={msg.id}
                        className={cn(
                            'flex flex-col gap-1.5',
                            msg.role === 'user' ? 'items-end' : 'items-start'
                        )}
                    >
                        {msg.role === 'assistant' && msg.badge && (
                            <span className="inline-flex items-center gap-1 px-1.5 font-mono text-[10px] font-medium text-sky-700 dark:text-sky-300">
                                <Bot className="h-3 w-3" />
                                {msg.badge}
                            </span>
                        )}
                        <div
                            className={cn(
                                'max-w-[92%] whitespace-pre-line rounded-2xl px-3.5 py-2.5 text-[13px] leading-relaxed',
                                msg.role === 'user'
                                    ? 'bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 text-white shadow-[0_10px_24px_-8px_rgba(2,132,199,0.5)]'
                                    : 'bg-white/75 text-slate-900 shadow-[0_6px_18px_-8px_rgba(15,23,42,0.12),inset_0_1px_0_rgba(255,255,255,0.9)] dark:bg-white/10 dark:text-slate-100 dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.12)]'
                            )}
                        >
                            {msg.content}
                        </div>

                        {msg.actions && msg.actions.length > 0 && (
                            <div className="mt-0.5 flex flex-wrap gap-1.5 pl-0.5">
                                {msg.actions.map((act) => (
                                    <Link
                                        key={act.href + act.label}
                                        href={act.href}
                                        onClick={isMobile ? closeDrawer : undefined}
                                        className="inline-flex items-center gap-1 rounded-full bg-white/75 px-3 py-1 text-[11px] font-semibold text-sky-700 shadow-sm hover:bg-sky-600 hover:text-white dark:bg-sky-500/20 dark:text-sky-200 dark:hover:bg-sky-500 dark:hover:text-white transition-colors"
                                    >
                                        {act.label}
                                        <ArrowUpRight className="h-3 w-3" />
                                    </Link>
                                ))}
                            </div>
                        )}
                    </div>
                ))}

                {aiSending && (
                    <div className="flex items-center gap-2 px-1.5 text-xs text-slate-600 dark:text-slate-300">
                        <Loader2 className="h-3.5 w-3.5 animate-spin text-sky-500" />
                        <span>Reading live portal context...</span>
                    </div>
                )}
            </div>

            {/* Docked Liquid-Glass Composer */}
            <form onSubmit={handleSubmit} className="px-3.5 pt-2.5 pb-4">
                <div className="flex items-center gap-2 rounded-full bg-white/80 p-1.5 pl-4 shadow-[0_8px_24px_-10px_rgba(15,23,42,0.16),inset_0_1px_0_rgba(255,255,255,0.95)] ring-1 ring-inset ring-slate-900/10 focus-within:ring-2 focus-within:ring-sky-500 dark:bg-white/10 dark:ring-white/15 dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.15)]">
                    <input
                        ref={inputRef}
                        type="text"
                        value={aiDraft}
                        onChange={(e) => setAiDraft(e.target.value)}
                        placeholder="Ask Vision AI..."
                        className="flex-1 border-0 bg-transparent px-0 py-1 text-xs text-slate-900 shadow-none ring-0 placeholder:text-slate-500 focus:border-0 focus:outline-none focus:ring-0 focus-visible:outline-none dark:text-white dark:placeholder:text-slate-400"
                    />
                    <button
                        type="submit"
                        disabled={aiSending || !aiDraft.trim()}
                        aria-label="Send message"
                        className="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 text-white shadow-sm hover:brightness-110 transition-all disabled:opacity-40"
                    >
                        <Send className="h-3.5 w-3.5" />
                    </button>
                </div>
            </form>
        </div>
    );

    return (
        <>
            {/* Desktop Header-Joined Right Liquid-Glass Sidebar (Mirrors Left Sidebar — Zero Reload on Navigation!) */}
            <AnimatePresence initial={false}>
                {aiOpen && (
                    <motion.aside
                        key="desktop-fused-ai-sidebar"
                        initial={{ width: 0, opacity: 0 }}
                        animate={{ width: 368, opacity: 1 }}
                        exit={{ width: 0, opacity: 0 }}
                        transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                        aria-label="Vision AI Portal Assistant"
                        className="hidden lg:flex h-full shrink-0 overflow-hidden"
                    >
                        <div className="flex h-full w-[368px] shrink-0 flex-col">
                            {panelBody(false)}
                        </div>
                    </motion.aside>
                )}
            </AnimatePresence>

            {/* Mobile/Tablet Slide-In Liquid Glass Fallback (< lg) */}
            <AnimatePresence>
                {aiOpen && (
                    <div className="lg:hidden">
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={closeDrawer}
                            className="fixed inset-0 z-50 bg-slate-950/45 backdrop-blur-sm"
                        />
                        <motion.aside
                            key="mobile-fused-ai-sidebar"
                            initial={{ x: '100%' }}
                            animate={{ x: 0 }}
                            exit={{ x: '100%' }}
                            transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                            aria-label="Vision AI Portal Assistant"
                            className="fixed inset-y-2 right-2 z-50 flex w-[calc(100%-1rem)] max-w-[380px] flex-col overflow-hidden rounded-2xl bg-white/90 backdrop-blur-3xl shadow-2xl ring-1 ring-slate-900/10 dark:bg-[#081226]/95 dark:ring-white/15"
                        >
                            {panelBody(true)}
                        </motion.aside>
                    </div>
                )}
            </AnimatePresence>
        </>
    );
}

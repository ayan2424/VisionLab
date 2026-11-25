import React, { createContext, useCallback, useContext, useEffect, useRef, useState } from 'react';
import type { AuthUser } from '@/types';

export interface AiQuickAction {
    label: string;
    href: string;
}

export interface AiChatMessage {
    id: string;
    role: 'user' | 'assistant';
    content: string;
    actions?: AiQuickAction[];
    badge?: string;
}

export type PortalLocaleCode = 'en' | 'ur' | 'ar' | 'zh' | 'es' | 'fr';

export interface PortalLocaleOption {
    code: PortalLocaleCode;
    shortLabel: string;
    label: string;
    nativeLabel: string;
    ready: boolean;
}

export const PORTAL_LOCALES: PortalLocaleOption[] = [
    { code: 'en', shortLabel: 'EN', label: 'English', nativeLabel: 'English (US)', ready: true },
    { code: 'ur', shortLabel: 'UR', label: 'Urdu', nativeLabel: 'اردو', ready: false },
    { code: 'ar', shortLabel: 'AR', label: 'Arabic', nativeLabel: 'العربية', ready: false },
    { code: 'zh', shortLabel: 'ZH', label: 'Chinese', nativeLabel: '中文', ready: false },
    { code: 'es', shortLabel: 'ES', label: 'Spanish', nativeLabel: 'Español', ready: false },
    { code: 'fr', shortLabel: 'FR', label: 'French', nativeLabel: 'Français', ready: false },
];

interface PortalShellContextValue {
    aiOpen: boolean;
    setAiOpen: React.Dispatch<React.SetStateAction<boolean>>;
    toggleAi: () => void;
    sidebarCollapsed: boolean;
    setSidebarCollapsed: React.Dispatch<React.SetStateAction<boolean>>;
    soundEnabled: boolean;
    toggleSound: () => void;
    locale: PortalLocaleCode;
    setLocale: (code: PortalLocaleCode) => void;
    aiMessages: AiChatMessage[];
    aiDraft: string;
    setAiDraft: React.Dispatch<React.SetStateAction<string>>;
    aiSending: boolean;
    ensureAiSession: (user: AuthUser) => void;
    sendAiMessage: (text: string, user: AuthUser, pageTitle: string, pageUrl: string) => Promise<void>;
    resetAiChat: (user: AuthUser) => void;
    sidebarScrollRef: React.MutableRefObject<number>;
    aiScrollRef: React.MutableRefObject<number>;
}

const PortalShellContext = createContext<PortalShellContextValue | null>(null);

function getCsrfToken(): string {
    const meta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    return meta?.content ?? '';
}

function playSubtleChime(): void {
    try {
        const AudioCtx = window.AudioContext || (window as unknown as { webkitAudioContext?: typeof AudioContext }).webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(660, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.08);
        gain.gain.setValueAtTime(0.001, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.04, ctx.currentTime + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start();
        osc.stop(ctx.currentTime + 0.13);
        window.setTimeout(() => void ctx.close(), 200);
    } catch {
        // Ignore browser autoplay restrictions gracefully
    }
}

export function buildWelcomeMessage(user: AuthUser): AiChatMessage {
    const firstName = user.name.split(' ')[0] || 'there';
    const roleLabel = `${user.role.charAt(0).toUpperCase() + user.role.slice(1)} Copilot`;

    const content =
        user.role === 'admin'
            ? `Hi ${firstName} — I'm docked right beside your workspace with live read-only access to cluster telemetry, containers, ERP records, and audits. This chat stays open across every page you visit.`
            : user.role === 'instructor'
            ? `Hi ${firstName} — I'm docked beside Faculty Studio to help triage grading, inspect live class seats, and review VisionGuard telemetry as you navigate.`
            : `Hi ${firstName} — I'm docked beside your workspace. Ask about deadlines, Nix containers, pending AI diffs, or coding concepts while you browse.`;

    return {
        id: 'welcome',
        role: 'assistant',
        content,
        badge: roleLabel,
    };
}

export function PortalShellProvider({ children }: { children: React.ReactNode }) {
    const [aiOpen, setAiOpen] = useState(false);
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
    const [soundEnabled, setSoundEnabled] = useState<boolean>(() => {
        if (typeof window === 'undefined') return true;
        return window.localStorage.getItem('vl_sound_enabled') !== 'false';
    });
    const [locale, setLocaleState] = useState<PortalLocaleCode>(() => {
        if (typeof window === 'undefined') return 'en';
        const stored = window.localStorage.getItem('vl_locale') as PortalLocaleCode | null;
        return PORTAL_LOCALES.some((l) => l.code === stored) ? (stored as PortalLocaleCode) : 'en';
    });
    const [aiMessages, setAiMessages] = useState<AiChatMessage[]>([]);
    const [aiDraft, setAiDraft] = useState('');
    const [aiSending, setAiSending] = useState(false);

    const activeUserIdRef = useRef<number | null>(null);
    const sidebarScrollRef = useRef<number>(0);
    const aiScrollRef = useRef<number>(0);

    useEffect(() => {
        if (typeof document !== 'undefined') {
            document.documentElement.lang = locale;
        }
    }, [locale]);

    const toggleAi = useCallback(() => {
        setAiOpen((prev) => !prev);
    }, []);

    const toggleSound = useCallback(() => {
        setSoundEnabled((prev) => {
            const next = !prev;
            try {
                window.localStorage.setItem('vl_sound_enabled', String(next));
            } catch {
                // Ignore storage quota errors
            }
            if (next) {
                playSubtleChime();
            }
            return next;
        });
    }, []);

    const setLocale = useCallback((code: PortalLocaleCode) => {
        setLocaleState(code);
        try {
            window.localStorage.setItem('vl_locale', code);
        } catch {
            // Ignore storage quota errors
        }
    }, []);

    const ensureAiSession = useCallback((user: AuthUser) => {
        if (activeUserIdRef.current !== user.id) {
            activeUserIdRef.current = user.id;
            setAiMessages([buildWelcomeMessage(user)]);
            setAiDraft('');
            aiScrollRef.current = 0;
        }
    }, []);

    const resetAiChat = useCallback((user: AuthUser) => {
        activeUserIdRef.current = user.id;
        setAiMessages([buildWelcomeMessage(user)]);
        setAiDraft('');
        aiScrollRef.current = 0;
    }, []);

    const sendAiMessage = useCallback(
        async (text: string, user: AuthUser, pageTitle: string, pageUrl: string) => {
            const trimmed = text.trim();
            if (!trimmed || aiSending) return;

            const userMsg: AiChatMessage = {
                id: `u-${Date.now()}`,
                role: 'user',
                content: trimmed,
            };

            let historySnapshot: AiChatMessage[] = [];
            setAiMessages((prev) => {
                const base = prev.length > 0 ? prev : [buildWelcomeMessage(user)];
                historySnapshot = [...base, userMsg];
                return historySnapshot;
            });
            setAiDraft('');
            setAiSending(true);

            try {
                const response = await fetch('/api/portal-ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        message: trimmed,
                        page_title: pageTitle,
                        page_url: pageUrl,
                        history: historySnapshot
                            .filter((m) => m.id !== 'welcome')
                            .slice(-10)
                            .map((m) => ({ role: m.role, content: m.content })),
                    }),
                });

                if (!response.ok) {
                    throw new Error(`Request failed (${response.status})`);
                }

                const data = (await response.json()) as {
                    reply: string;
                    actions?: AiQuickAction[];
                    context_badge?: string;
                };

                setAiMessages((prev) => [
                    ...prev,
                    {
                        id: `a-${Date.now()}`,
                        role: 'assistant',
                        content: data.reply,
                        actions: data.actions,
                        badge: data.context_badge,
                    },
                ]);
            } catch {
                setAiMessages((prev) => [
                    ...prev,
                    {
                        id: `err-${Date.now()}`,
                        role: 'assistant',
                        content:
                            'I could not reach the Vision AI service right now. Please try again in a moment.',
                    },
                ]);
            } finally {
                setAiSending(false);
            }
        },
        [aiSending]
    );

    return (
        <PortalShellContext.Provider
            value={{
                aiOpen,
                setAiOpen,
                toggleAi,
                sidebarCollapsed,
                setSidebarCollapsed,
                soundEnabled,
                toggleSound,
                locale,
                setLocale,
                aiMessages,
                aiDraft,
                setAiDraft,
                aiSending,
                ensureAiSession,
                sendAiMessage,
                resetAiChat,
                sidebarScrollRef,
                aiScrollRef,
            }}
        >
            {children}
        </PortalShellContext.Provider>
    );
}

export function usePortalShell(): PortalShellContextValue {
    const ctx = useContext(PortalShellContext);
    if (!ctx) {
        throw new Error('usePortalShell must be used within a PortalShellProvider');
    }
    return ctx;
}

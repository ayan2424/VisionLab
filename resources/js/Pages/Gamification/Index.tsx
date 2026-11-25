import React from 'react';
import { Award, Flame, Terminal, Trophy, Zap } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface GamificationProps {
    user: {
        name: string;
        xp: number;
        level: number;
        rank_title: string;
        current_streak?: number;
        badges?: Array<{ id: number; name?: string; badge_name?: string; description?: string; earned_at?: string }>;
        xp_transactions?: Array<{ id: number; amount: number; reason: string; created_at: string }>;
    };
    currentLevel: number;
    nextLevel: number;
    xpForNextLevel: number;
    progressPercentage: number;
    totalWorkspaces: number;
}

export default function GamificationIndex({
    user,
    currentLevel,
    nextLevel,
    xpForNextLevel,
    progressPercentage,
    totalWorkspaces,
}: GamificationProps) {
    const badges = user.badges ?? [];
    const transactions = user.xp_transactions ?? [];

    return (
        <PortalLayout title="Achievements & XP" pageTitle="Achievements & Mastery">
            <div className="space-y-6">
                <div className="grid grid-cols-1 gap-5 lg:grid-cols-12">
                    <div className="rounded-2xl bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-7 text-white shadow-xl lg:col-span-7">
                        <div className="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p className="font-mono text-xs text-sky-100/90">RANK &amp; MASTERY</p>
                                <h2 className="mt-1 font-display text-3xl font-semibold">
                                    Level {currentLevel} · {user.rank_title}
                                </h2>
                            </div>
                            <span className="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-4 py-1.5 font-mono text-xs font-semibold tabular-nums backdrop-blur-md">
                                <Zap className="h-3.5 w-3.5" />
                                {user.xp.toLocaleString()} XP
                            </span>
                        </div>

                        <div className="mt-6 space-y-2">
                            <div className="flex items-center justify-between text-xs text-sky-100/90 tabular-nums">
                                <span>{progressPercentage}% toward Level {nextLevel}</span>
                                <span>Target: {xpForNextLevel.toLocaleString()} XP</span>
                            </div>
                            <div className="h-3 w-full overflow-hidden rounded-full bg-white/20">
                                <div
                                    className="h-full rounded-full bg-white transition-all duration-500"
                                    style={{ width: `${Math.max(progressPercentage, 6)}%` }}
                                />
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4 lg:col-span-5">
                        <SpotlightSurface className="!rounded-2xl p-6 flex flex-col justify-between">
                            <span className="flex h-10 w-10 items-center justify-center rounded-full bg-teal-500/15 text-teal-500">
                                <Flame className="h-5 w-5" />
                            </span>
                            <div className="mt-4">
                                <p className="font-display text-3xl font-semibold text-vl-ink tabular-nums">
                                    {user.current_streak ?? 0}d
                                </p>
                                <p className="text-xs text-vl-ink-muted">Active Coding Streak</p>
                            </div>
                        </SpotlightSurface>

                        <SpotlightSurface className="!rounded-2xl p-6 flex flex-col justify-between">
                            <span className="flex h-10 w-10 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                                <Terminal className="h-5 w-5" />
                            </span>
                            <div className="mt-4">
                                <p className="font-display text-3xl font-semibold text-vl-ink tabular-nums">
                                    {totalWorkspaces}
                                </p>
                                <p className="text-xs text-vl-ink-muted">Lab Sessions Launched</p>
                            </div>
                        </SpotlightSurface>
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <SpotlightSurface accent="amber" className="!rounded-2xl p-6 lg:col-span-5">
                        <div className="mb-5 flex items-center justify-between">
                            <h3 className="font-display text-lg font-semibold text-vl-ink">Earned Badges</h3>
                            <Trophy className="h-4 w-4 text-amber-400" />
                        </div>
                        {badges.length === 0 ? (
                            <p className="py-8 text-center text-sm text-vl-ink-muted">
                                Complete labs and assignments to unlock verified badges.
                            </p>
                        ) : (
                            <div className="space-y-3">
                                {badges.map((badge) => (
                                    <div key={badge.id} className="flex items-center justify-between rounded-xl bg-vl-bg/80 p-4">
                                        <div className="flex items-center gap-3">
                                            <span className="flex h-9 w-9 items-center justify-center rounded-full bg-amber-500/15 text-amber-500">
                                                <Award className="h-4 w-4" />
                                            </span>
                                            <div>
                                                <p className="text-sm font-semibold text-vl-ink">
                                                    {badge.name || badge.badge_name || 'Milestone Badge'}
                                                </p>
                                                {badge.description && (
                                                    <p className="text-xs text-vl-ink-muted">{badge.description}</p>
                                                )}
                                            </div>
                                        </div>
                                        {badge.earned_at && (
                                            <span className="font-mono text-[11px] text-vl-ink-muted">
                                                {new Date(badge.earned_at).toLocaleDateString()}
                                            </span>
                                        )}
                                    </div>
                                ))}
                            </div>
                        )}
                    </SpotlightSurface>

                    <SpotlightSurface accent="sky" className="!rounded-2xl p-6 lg:col-span-7">
                        <div className="mb-5 flex items-center justify-between">
                            <h3 className="font-display text-lg font-semibold text-vl-ink">Recent XP Ledger</h3>
                            <StatusPill tone="sky">{transactions.length} entries</StatusPill>
                        </div>
                        {transactions.length === 0 ? (
                            <p className="py-8 text-center text-sm text-vl-ink-muted">No XP events recorded yet.</p>
                        ) : (
                            <div className="space-y-2.5">
                                {transactions.map((tx) => (
                                    <div key={tx.id} className="flex items-center justify-between gap-3 rounded-xl bg-vl-bg/80 px-5 py-3">
                                        <div className="min-w-0">
                                            <p className="truncate text-sm font-medium text-vl-ink">{tx.reason}</p>
                                            <p className="font-mono text-[11px] text-vl-ink-muted">
                                                {new Date(tx.created_at).toLocaleDateString()}
                                            </p>
                                        </div>
                                        <span className="shrink-0 rounded-full bg-teal-500/15 px-3 py-1 font-mono text-xs font-semibold text-teal-600 dark:text-teal-300 tabular-nums">
                                            +{tx.amount} XP
                                        </span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </SpotlightSurface>
                </div>
            </div>
        </PortalLayout>
    );
}

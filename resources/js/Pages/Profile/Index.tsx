import React from 'react';
import { Link } from '@inertiajs/react';
import { Award, ExternalLink, Settings, Trophy, Zap } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface ProfileShowcaseProps {
    user: {
        id: number;
        name: string;
        email: string;
        role: string;
        student_id?: string | null;
        xp: number;
        level: number;
        rank_title: string;
    };
    badges: Array<{ id: number; badge_name?: string; name?: string; earned_at?: string }>;
    leaderboard: Array<{ id: number; name: string; xp: number; level: number; rank_title: string }>;
    transactions: Array<{ id: number; amount: number; reason: string; created_at: string }>;
    nextLevelXp: number;
    progressPercent: number;
}

export default function ProfileIndex({
    user,
    badges,
    leaderboard,
    transactions,
    nextLevelXp,
    progressPercent,
}: ProfileShowcaseProps) {
    return (
        <PortalLayout title="Academic Showcase" pageTitle="Academic Showcase">
            <div className="space-y-6">
                <div className="rounded-xl bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-7 text-white shadow-xl">
                    <div className="flex flex-wrap items-center justify-between gap-6">
                        <div className="space-y-1">
                            <p className="font-mono text-xs text-sky-100/90 capitalize">
                                {user.role} {user.student_id ? `· ${user.student_id}` : ''}
                            </p>
                            <h2 className="font-display text-3xl font-semibold">{user.name}</h2>
                            <p className="text-sm text-sky-100">
                                Level {user.level} {user.rank_title} · {user.xp.toLocaleString()} / {nextLevelXp.toLocaleString()} XP
                            </p>
                        </div>

                        <div className="flex flex-wrap gap-2.5">
                            {user.student_id && (
                                <Link
                                    href={`/u/${user.student_id}`}
                                    className="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-4 py-2 text-xs font-semibold text-white hover:bg-white/30 transition-colors"
                                >
                                    Public Portfolio
                                    <ExternalLink className="h-3.5 w-3.5" />
                                </Link>
                            )}
                            <Link
                                href="/profile"
                                className="inline-flex items-center gap-1.5 rounded-full bg-white px-4 py-2 text-xs font-semibold text-blue-700 hover:bg-sky-50 transition-colors"
                            >
                                <Settings className="h-3.5 w-3.5" />
                                Account Settings
                            </Link>
                        </div>
                    </div>

                    <div className="mt-5 h-2.5 w-full overflow-hidden rounded-full bg-white/20">
                        <div
                            className="h-full rounded-full bg-white"
                            style={{ width: `${Math.max(Math.round(progressPercent), 6)}%` }}
                        />
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <SpotlightSurface accent="amber" className="!rounded-2xl p-6 lg:col-span-5">
                        <div className="mb-4 flex items-center justify-between">
                            <h3 className="font-display text-lg font-semibold text-vl-ink">Campus Leaderboard</h3>
                            <Trophy className="h-4 w-4 text-amber-400" />
                        </div>
                        <div className="space-y-2.5">
                            {leaderboard.map((entry, idx) => (
                                <div
                                    key={entry.id}
                                    className="flex items-center justify-between rounded-xl bg-vl-bg/80 px-4 py-2.5"
                                >
                                    <div className="flex items-center gap-3 min-w-0">
                                        <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sky-500/15 font-mono text-xs font-bold text-sky-500 tabular-nums">
                                            #{idx + 1}
                                        </span>
                                        <div className="min-w-0">
                                            <p className="truncate text-sm font-semibold text-vl-ink">{entry.name}</p>
                                            <p className="text-[11px] text-vl-ink-muted">
                                                Lvl {entry.level} · {entry.rank_title}
                                            </p>
                                        </div>
                                    </div>
                                    <span className="font-mono text-xs font-semibold text-teal-500 tabular-nums">
                                        {entry.xp.toLocaleString()} XP
                                    </span>
                                </div>
                            ))}
                        </div>
                    </SpotlightSurface>

                    <div className="space-y-6 lg:col-span-7">
                        <SpotlightSurface className="!rounded-2xl p-6">
                            <div className="mb-4 flex items-center justify-between">
                                <h3 className="font-display text-lg font-semibold text-vl-ink">Badges</h3>
                                <Award className="h-4 w-4 text-sky-400" />
                            </div>
                            {badges.length === 0 ? (
                                <p className="text-xs text-vl-ink-muted">No badges earned yet.</p>
                            ) : (
                                <div className="flex flex-wrap gap-2">
                                    {badges.map((b) => (
                                        <span
                                            key={b.id}
                                            className="rounded-full bg-sky-500/15 px-3.5 py-1.5 text-xs font-semibold text-sky-600 dark:text-sky-300"
                                        >
                                            {b.badge_name || b.name || 'Milestone'}
                                        </span>
                                    ))}
                                </div>
                            )}
                        </SpotlightSurface>

                        <SpotlightSurface accent="teal" className="!rounded-2xl p-6">
                            <div className="mb-4 flex items-center justify-between">
                                <h3 className="font-display text-lg font-semibold text-vl-ink">XP History</h3>
                                <Zap className="h-4 w-4 text-teal-400" />
                            </div>
                            <div className="space-y-2">
                                {transactions.length === 0 ? (
                                    <p className="text-xs text-vl-ink-muted">No XP activity yet.</p>
                                ) : (
                                    transactions.slice(0, 10).map((t) => (
                                        <div key={t.id} className="flex items-center justify-between rounded-xl bg-vl-bg/80 px-4 py-2.5 text-xs">
                                            <span className="truncate text-vl-ink">{t.reason}</span>
                                            <span className="font-mono font-semibold text-teal-500 tabular-nums">+{t.amount} XP</span>
                                        </div>
                                    ))
                                )}
                            </div>
                        </SpotlightSurface>
                    </div>
                </div>
            </div>
        </PortalLayout>
    );
}

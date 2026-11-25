import React from 'react';
import { Award, Code2, Clock, Flame, Terminal } from 'lucide-react';
import { PublicLayout } from '@/layouts/PublicLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface PortfolioProps {
    user: {
        name: string;
        student_id: string;
        xp: number;
        level: number;
        rank_title: string;
        current_streak?: number;
    };
    techStack: Array<{ language: string; count: number }>;
    schedulePercentages: Record<string, number>;
    dominantTime: string;
    totalWorkspaces: number;
    badges: Array<{ id: number; name?: string; badge_name?: string; description?: string; earned_at?: string }>;
    heatmapData: Record<string, number>;
}

export default function Portfolio({
    user,
    techStack,
    schedulePercentages,
    dominantTime,
    totalWorkspaces,
    badges,
    heatmapData,
}: PortfolioProps) {
    const heatEntries = Object.entries(heatmapData).slice(-35);

    return (
        <PublicLayout title={`${user.name} — Student Portfolio`}>
            <div className="mx-auto max-w-[1200px] px-4 pt-32 pb-24 sm:px-6 lg:px-10 space-y-8">
                {/* Hero Banner */}
                <div className="relative overflow-hidden rounded-xl bg-gradient-to-br from-sky-500 via-blue-600 to-teal-500 p-8 text-white shadow-2xl sm:p-10">
                    <div className="flex flex-wrap items-center justify-between gap-6">
                        <div className="flex items-center gap-5">
                            <span className="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 font-display text-2xl font-bold backdrop-blur-md">
                                {user.name.slice(0, 2).toUpperCase()}
                            </span>
                            <div>
                                <p className="font-mono text-xs text-sky-100/90">{user.student_id}</p>
                                <h1 className="mt-1 font-display text-3xl font-semibold tracking-tight">{user.name}</h1>
                                <p className="mt-1 text-sm text-sky-100">
                                    Level {user.level} {user.rank_title} · {user.xp.toLocaleString()} Verified XP
                                </p>
                            </div>
                        </div>

                        <div className="flex flex-wrap gap-3">
                            <span className="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-2 font-mono text-xs font-semibold backdrop-blur-md tabular-nums">
                                <Terminal className="h-4 w-4" />
                                {totalWorkspaces} Workspaces
                            </span>
                            <span className="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-2 font-mono text-xs font-semibold backdrop-blur-md tabular-nums">
                                <Flame className="h-4 w-4" />
                                {user.current_streak ?? 0}d Streak
                            </span>
                        </div>
                    </div>
                </div>

                {/* Tech Stack & Schedule */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <SpotlightSurface accent="sky" className="!rounded-2xl p-7 lg:col-span-6">
                        <div className="mb-5 flex items-center justify-between">
                            <h2 className="font-display text-lg font-semibold text-vl-ink">Verified Tech Stack</h2>
                            <Code2 className="h-4 w-4 text-sky-400" />
                        </div>
                        {techStack.length === 0 ? (
                            <p className="py-6 text-sm text-vl-ink-muted">No workspace language activity recorded yet.</p>
                        ) : (
                            <div className="space-y-3">
                                {techStack.map((item) => (
                                    <div key={item.language} className="flex items-center justify-between rounded-xl bg-vl-bg/80 px-4 py-2.5">
                                        <span className="font-mono text-xs font-semibold text-vl-ink capitalize">{item.language}</span>
                                        <StatusPill tone="sky">{item.count} workspace(s)</StatusPill>
                                    </div>
                                ))}
                            </div>
                        )}
                    </SpotlightSurface>

                    <SpotlightSurface accent="teal" className="!rounded-2xl p-7 lg:col-span-6">
                        <div className="mb-5 flex items-center justify-between">
                            <div>
                                <h2 className="font-display text-lg font-semibold text-vl-ink">Coding Rhythm</h2>
                                <p className="text-xs text-vl-ink-muted">Peak window: {dominantTime}</p>
                            </div>
                            <Clock className="h-4 w-4 text-teal-400" />
                        </div>
                        <div className="space-y-3.5">
                            {Object.entries(schedulePercentages).map(([slot, pct]) => (
                                <div key={slot} className="space-y-1.5">
                                    <div className="flex items-center justify-between text-xs">
                                        <span className="text-vl-ink-secondary">{slot}</span>
                                        <span className="font-mono font-semibold text-vl-ink tabular-nums">{pct}%</span>
                                    </div>
                                    <div className="h-2 w-full overflow-hidden rounded-full bg-vl-bg">
                                        <div
                                            className="h-full rounded-full bg-gradient-to-r from-sky-500 to-teal-400"
                                            style={{ width: `${Math.max(pct, 4)}%` }}
                                        />
                                    </div>
                                </div>
                            ))}
                        </div>
                    </SpotlightSurface>
                </div>

                {/* Activity Heatmap & Badges */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <SpotlightSurface className="!rounded-2xl p-7 lg:col-span-7">
                        <h2 className="font-display text-lg font-semibold text-vl-ink">Recent Lab Activity</h2>
                        <div className="mt-5 flex flex-wrap gap-2">
                            {heatEntries.length === 0 ? (
                                <p className="text-sm text-vl-ink-muted">No recent lab sessions logged in the last 60 days.</p>
                            ) : (
                                heatEntries.map(([date, count]) => (
                                    <div
                                        key={date}
                                        title={`${date}: ${count} events`}
                                        className={`h-8 w-8 rounded-xl flex items-center justify-center font-mono text-[10px] font-semibold tabular-nums ${
                                            count > 6
                                                ? 'bg-sky-500 text-white'
                                                : count > 2
                                                ? 'bg-sky-500/40 text-vl-ink'
                                                : 'bg-sky-500/15 text-vl-ink-secondary'
                                        }`}
                                    >
                                        {count}
                                    </div>
                                ))
                            )}
                        </div>
                    </SpotlightSurface>

                    <SpotlightSurface accent="amber" className="!rounded-2xl p-7 lg:col-span-5">
                        <div className="mb-4 flex items-center justify-between">
                            <h2 className="font-display text-lg font-semibold text-vl-ink">Earned Badges</h2>
                            <Award className="h-4 w-4 text-amber-400" />
                        </div>
                        {badges.length === 0 ? (
                            <p className="text-sm text-vl-ink-muted">No badges earned yet.</p>
                        ) : (
                            <div className="flex flex-wrap gap-2">
                                {badges.map((b) => (
                                    <span
                                        key={b.id}
                                        className="rounded-full bg-amber-500/15 px-3.5 py-1.5 text-xs font-semibold text-amber-600 dark:text-amber-300"
                                    >
                                        {b.name || b.badge_name || 'Verified Milestone'}
                                    </span>
                                ))}
                            </div>
                        )}
                    </SpotlightSurface>
                </div>
            </div>
        </PublicLayout>
    );
}

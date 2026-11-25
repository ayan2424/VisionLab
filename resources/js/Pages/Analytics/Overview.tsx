import React, { useMemo } from 'react';
import {
    Area,
    AreaChart,
    CartesianGrid,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import { Activity, Bot, CheckSquare, Terminal } from 'lucide-react';
import { useTheme } from '@/components/ThemeProvider';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface AnalyticsOverviewProps {
    mode: 'student' | 'instructor';
    executions: number;
    aiInteractions: number;
    activeSessions?: number;
    pendingGrading?: number;
    activityChart: { labels: string[]; values: number[] };
    recentEvents?: Array<{ id: number; event_type: string; created_at: string; user?: { name: string } | null }>;
}

export default function AnalyticsOverview({
    mode,
    executions,
    aiInteractions,
    activeSessions = 0,
    pendingGrading = 0,
    activityChart,
    recentEvents = [],
}: AnalyticsOverviewProps) {
    const { isDark } = useTheme();
    const chartData = useMemo(
        () =>
            activityChart.labels.map((label, idx) => ({
                day: label,
                events: activityChart.values[idx] ?? 0,
            })),
        [activityChart]
    );

    const gridColor = isDark ? 'rgba(148,163,184,0.12)' : 'rgba(15,23,42,0.08)';
    const tickColor = isDark ? '#94A3B8' : '#64748B';

    return (
        <PortalLayout title="Analytics" pageTitle={mode === 'instructor' ? 'Cohort Analytics' : 'My Lab Analytics'}>
            <div className="space-y-6">
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <SpotlightSurface className="!rounded-2xl p-5 flex items-center justify-between">
                        <div>
                            <p className="text-xs text-vl-ink-secondary">Terminal Executions</p>
                            <p className="mt-1 font-display text-3xl font-semibold text-vl-ink tabular-nums">{executions}</p>
                        </div>
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                            <Terminal className="h-5 w-5" />
                        </span>
                    </SpotlightSurface>
                    <SpotlightSurface className="!rounded-2xl p-5 flex items-center justify-between">
                        <div>
                            <p className="text-xs text-vl-ink-secondary">Audited AI Interactions</p>
                            <p className="mt-1 font-display text-3xl font-semibold text-vl-ink tabular-nums">{aiInteractions}</p>
                        </div>
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-teal-500/15 text-teal-500">
                            <Bot className="h-5 w-5" />
                        </span>
                    </SpotlightSurface>
                    <SpotlightSurface className="!rounded-2xl p-5 flex items-center justify-between">
                        <div>
                            <p className="text-xs text-vl-ink-secondary">
                                {mode === 'instructor' ? 'Pending Grading' : 'Active Containers'}
                            </p>
                            <p className="mt-1 font-display text-3xl font-semibold text-vl-ink tabular-nums">
                                {mode === 'instructor' ? pendingGrading : activeSessions}
                            </p>
                        </div>
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-amber-500/15 text-amber-500">
                            {mode === 'instructor' ? <CheckSquare className="h-5 w-5" /> : <Activity className="h-5 w-5" />}
                        </span>
                    </SpotlightSurface>
                </div>

                <SpotlightSurface accent="sky" className="!rounded-2xl p-6">
                    <h3 className="font-display text-base font-semibold text-vl-ink mb-4">
                        14-Day Coding Activity
                    </h3>
                    <div className="h-64 w-full">
                        <ResponsiveContainer width="100%" height="100%">
                            <AreaChart data={chartData}>
                                <defs>
                                    <linearGradient id="anaFill" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="5%" stopColor="#38BDF8" stopOpacity={0.35} />
                                        <stop offset="95%" stopColor="#38BDF8" stopOpacity={0} />
                                    </linearGradient>
                                </defs>
                                <CartesianGrid strokeDasharray="3 3" stroke={gridColor} vertical={false} />
                                <XAxis dataKey="day" stroke={tickColor} fontSize={11} tickLine={false} axisLine={false} />
                                <YAxis stroke={tickColor} fontSize={11} tickLine={false} axisLine={false} allowDecimals={false} />
                                <Tooltip />
                                <Area type="monotone" dataKey="events" stroke="#38BDF8" strokeWidth={2.5} fill="url(#anaFill)" />
                            </AreaChart>
                        </ResponsiveContainer>
                    </div>
                </SpotlightSurface>

                {recentEvents.length > 0 && (
                    <SpotlightSurface className="!rounded-2xl p-6 space-y-3">
                        <h3 className="font-display text-base font-semibold text-vl-ink">Recent Cohort Events</h3>
                        <div className="space-y-2">
                            {recentEvents.map((ev) => (
                                <div key={ev.id} className="flex items-center justify-between rounded-full bg-vl-bg/80 px-5 py-2.5 text-xs">
                                    <span className="font-mono text-vl-ink">
                                        {ev.user?.name ?? 'Student'} · {ev.event_type}
                                    </span>
                                    <span className="text-vl-ink-muted">
                                        {new Date(ev.created_at).toLocaleDateString()}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </SpotlightSurface>
                )}
            </div>
        </PortalLayout>
    );
}

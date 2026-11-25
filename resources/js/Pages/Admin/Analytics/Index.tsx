import React, { useMemo } from 'react';
import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import { Activity, BookOpen, Bot, Cpu, Users } from 'lucide-react';
import { useTheme } from '@/components/ThemeProvider';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface ChartSeries {
    labels: string[];
    values: number[];
}

interface AdminAnalyticsProps {
    userChart: ChartSeries;
    submissionChart: ChartSeries;
    aiChart: ChartSeries;
    workspaceChart: ChartSeries;
    roleDistribution: Record<string, number>;
    stats: {
        total_users: number;
        total_courses: number;
        total_workspaces: number;
        total_submissions: number;
        pending_grading: number;
        ai_actions_today: number;
        ai_actions_total: number;
        telemetry_events: number;
    };
    recentEvents: Array<{ id: number; event_type: string; created_at: string; user?: { name: string } | null }>;
}

export default function AdminAnalyticsIndex({
    userChart,
    submissionChart,
    aiChart,
    workspaceChart,
    roleDistribution,
    stats,
    recentEvents,
}: AdminAnalyticsProps) {
    const { isDark } = useTheme();
    const gridColor = isDark ? 'rgba(148,163,184,0.12)' : 'rgba(15,23,42,0.08)';
    const tickColor = isDark ? '#94A3B8' : '#64748B';
    const tooltipStyle = {
        backgroundColor: isDark ? '#080F1D' : '#FFFFFF',
        border: isDark ? '1px solid rgba(255,255,255,0.12)' : '1px solid rgba(15,23,42,0.1)',
        borderRadius: '14px',
        fontSize: '12px',
    };

    const combinedDaily = useMemo(
        () =>
            userChart.labels.slice(-14).map((label, idx) => {
                const offset = Math.max(0, userChart.labels.length - 14) + idx;
                return {
                    day: label,
                    users: userChart.values[offset] ?? 0,
                    submissions: submissionChart.values[offset] ?? 0,
                    ai: aiChart.values[offset] ?? 0,
                };
            }),
        [userChart, submissionChart, aiChart]
    );

    const wsSeries = useMemo(
        () => workspaceChart.labels.map((label, i) => ({ day: label, starts: workspaceChart.values[i] ?? 0 })),
        [workspaceChart]
    );

    const roleSlices = useMemo(() => {
        const palette = ['#38BDF8', '#2DD4BF', '#FBBF24', '#0EA5E9'];
        const entries = Object.entries(roleDistribution);
        if (entries.length === 0) return [{ name: 'Empty', value: 1, color: '#334155' }];
        return entries.map(([name, value], i) => ({
            name,
            value,
            color: palette[i % palette.length],
        }));
    }, [roleDistribution]);

    return (
        <PortalLayout title="Platform Analytics" pageTitle="Platform Analytics" adminMode>
            <div className="space-y-6">
                <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    {[
                        { label: 'Total Users', val: stats.total_users, icon: Users, tone: 'bg-sky-500/15 text-sky-500' },
                        { label: 'Total Workspaces', val: stats.total_workspaces, icon: Cpu, tone: 'bg-teal-500/15 text-teal-500' },
                        { label: 'AI Operations', val: stats.ai_actions_total, icon: Bot, tone: 'bg-amber-500/15 text-amber-500' },
                        { label: 'Telemetry Events', val: stats.telemetry_events, icon: Activity, tone: 'bg-sky-500/15 text-sky-500' },
                    ].map((kpi) => {
                        const Icon = kpi.icon;
                        return (
                            <SpotlightSurface key={kpi.label} className="p-5 flex items-center justify-between">
                                <div>
                                    <p className="text-xs text-vl-ink-secondary">{kpi.label}</p>
                                    <p className="mt-1 font-display text-2xl font-semibold text-vl-ink tabular-nums">
                                        {kpi.val.toLocaleString()}
                                    </p>
                                </div>
                                <span className={`flex h-10 w-10 items-center justify-center rounded-full ${kpi.tone}`}>
                                    <Icon className="h-5 w-5" />
                                </span>
                            </SpotlightSurface>
                        );
                    })}
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <SpotlightSurface accent="sky" className="p-6 lg:col-span-8">
                        <h3 className="font-display text-base font-semibold text-vl-ink mb-4">
                            14-Day Activity Velocity (Submissions &amp; AI Actions)
                        </h3>
                        <div className="h-64 w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <AreaChart data={combinedDaily}>
                                    <defs>
                                        <linearGradient id="admSubFill" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="5%" stopColor="#38BDF8" stopOpacity={0.35} />
                                            <stop offset="95%" stopColor="#38BDF8" stopOpacity={0} />
                                        </linearGradient>
                                        <linearGradient id="admAiFill" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="5%" stopColor="#2DD4BF" stopOpacity={0.35} />
                                            <stop offset="95%" stopColor="#2DD4BF" stopOpacity={0} />
                                        </linearGradient>
                                    </defs>
                                    <CartesianGrid strokeDasharray="3 3" stroke={gridColor} vertical={false} />
                                    <XAxis dataKey="day" stroke={tickColor} fontSize={11} tickLine={false} axisLine={false} />
                                    <YAxis stroke={tickColor} fontSize={11} tickLine={false} axisLine={false} allowDecimals={false} />
                                    <Tooltip contentStyle={tooltipStyle} />
                                    <Area type="monotone" dataKey="submissions" stroke="#38BDF8" strokeWidth={2} fill="url(#admSubFill)" />
                                    <Area type="monotone" dataKey="ai" stroke="#2DD4BF" strokeWidth={2} fill="url(#admAiFill)" />
                                </AreaChart>
                            </ResponsiveContainer>
                        </div>
                    </SpotlightSurface>

                    <SpotlightSurface className="p-6 lg:col-span-4">
                        <div className="flex items-center justify-between mb-2">
                            <h3 className="font-display text-base font-semibold text-vl-ink">Role Distribution</h3>
                            <BookOpen className="h-4 w-4 text-sky-400" />
                        </div>
                        <div className="h-52 w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <PieChart>
                                    <Pie data={roleSlices} dataKey="value" nameKey="name" cx="50%" cy="50%" innerRadius={48} outerRadius={72} paddingAngle={4} cornerRadius={6} stroke="none">
                                        {roleSlices.map((s) => (
                                            <Cell key={s.name} fill={s.color} />
                                        ))}
                                    </Pie>
                                    <Tooltip contentStyle={tooltipStyle} />
                                </PieChart>
                            </ResponsiveContainer>
                        </div>
                        <div className="flex flex-wrap justify-center gap-2 text-xs">
                            {roleSlices.map((s) => (
                                <span key={s.name} className="inline-flex items-center gap-1.5 rounded-full bg-vl-bg/80 px-3 py-1 capitalize text-vl-ink-secondary tabular-nums">
                                    <span className="h-2 w-2 rounded-full" style={{ backgroundColor: s.color }} />
                                    {s.name}: {s.value}
                                </span>
                            ))}
                        </div>
                    </SpotlightSurface>
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <SpotlightSurface accent="teal" className="p-6 lg:col-span-7">
                        <h3 className="font-display text-base font-semibold text-vl-ink mb-4">
                            Workspace Container Boots (14 Days)
                        </h3>
                        <div className="h-56 w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <BarChart data={wsSeries} barSize={18}>
                                    <CartesianGrid strokeDasharray="3 3" stroke={gridColor} vertical={false} />
                                    <XAxis dataKey="day" stroke={tickColor} fontSize={10} tickLine={false} axisLine={false} />
                                    <YAxis stroke={tickColor} fontSize={11} tickLine={false} axisLine={false} allowDecimals={false} />
                                    <Tooltip contentStyle={tooltipStyle} />
                                    <Bar dataKey="starts" fill="#2DD4BF" radius={[99, 99, 99, 99]} />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
                    </SpotlightSurface>

                    <SpotlightSurface className="p-6 lg:col-span-5 space-y-3">
                        <h3 className="font-display text-base font-semibold text-vl-ink">Live Telemetry Stream</h3>
                        <div className="space-y-2">
                            {recentEvents.length === 0 ? (
                                <p className="py-8 text-center text-xs text-vl-ink-muted">No telemetry events recorded yet.</p>
                            ) : (
                                recentEvents.map((ev) => (
                                    <div key={ev.id} className="flex items-center justify-between rounded-xl bg-vl-bg/80 px-4 py-2.5 text-xs">
                                        <span className="truncate font-mono text-vl-ink">
                                            {ev.user?.name ?? 'System'} · {ev.event_type}
                                        </span>
                                        <span className="font-mono text-[10px] text-vl-ink-muted">
                                            {new Date(ev.created_at).toLocaleDateString()}
                                        </span>
                                    </div>
                                ))
                            )}
                        </div>
                    </SpotlightSurface>
                </div>
            </div>
        </PortalLayout>
    );
}

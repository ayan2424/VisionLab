import React from 'react';
import { router } from '@inertiajs/react';
import { Activity, CheckCircle2, RefreshCw, XCircle } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

const PROBE_META: Record<string, { label: string; description: string }> = {
    database: {
        label: 'Primary Database',
        description: 'Active PDO connection to VisionLab academic & governance tables.',
    },
    redis: {
        label: 'Redis Cache & Queue Broker',
        description: 'Rate limiting, session caching, and asynchronous extension sync queues.',
    },
    reverb: {
        label: 'Laravel Reverb WebSockets',
        description: 'Real-time broadcast channels for AI patch proposals and grading alerts.',
    },
    storage: {
        label: 'Artifact & Local Storage Disk',
        description: 'Read/write probe for SHA-256 verified VSIX extension archives and exports.',
    },
    ai_config: {
        label: 'Sovereign AI Provider Config',
        description: 'Configured provider endpoint and credentials for human-gated AI completions.',
    },
};

export default function AdminStatus({ checks }: { checks: Record<string, boolean> }) {
    // Filter out decommissioned jitsi_config from UI operational status
    const activeProbes = Object.entries(checks).filter(([key]) => key !== 'jitsi_config');
    const allOperational = activeProbes.every(([, ok]) => ok);

    return (
        <PortalLayout title="System Status" pageTitle="Cluster Telemetry" adminMode>
            <div className="space-y-6">
                <SpotlightSurface accent={allOperational ? 'teal' : 'amber'} className="p-7">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div className="space-y-2">
                            <StatusPill tone={allOperational ? 'teal' : 'amber'} pulse={allOperational}>
                                {allOperational ? 'All Active Systems Operational' : 'Attention Required'}
                            </StatusPill>
                            <h2 className="font-display text-2xl font-semibold text-vl-ink">
                                Infrastructure Health Probes
                            </h2>
                            <p className="text-sm text-vl-ink-secondary">
                                Live diagnostic telemetry for VisionLab active dependencies.
                            </p>
                        </div>

                        <MagneticButton
                            onClick={() => router.reload({ only: ['checks'] })}
                            variant="secondary"
                            size="sm"
                        >
                            <RefreshCw className="h-3.5 w-3.5" />
                            Re-run Probes
                        </MagneticButton>
                    </div>
                </SpotlightSurface>

                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {activeProbes.map(([key, ok]) => {
                        const meta = PROBE_META[key] ?? {
                            label: key,
                            description: 'Active system probe.',
                        };
                        return (
                            <SpotlightSurface key={key} accent={ok ? 'teal' : 'amber'} className="p-6">
                                <div className="flex items-start justify-between gap-3">
                                    <div className="flex items-center gap-2.5">
                                        <Activity className={`h-4 w-4 ${ok ? 'text-teal-400' : 'text-rose-400'}`} />
                                        <h3 className="font-display text-base font-semibold text-vl-ink">
                                            {meta.label}
                                        </h3>
                                    </div>
                                    {ok ? (
                                        <CheckCircle2 className="h-5 w-5 shrink-0 text-teal-400" />
                                    ) : (
                                        <XCircle className="h-5 w-5 shrink-0 text-rose-400" />
                                    )}
                                </div>
                                <p className="mt-3 text-xs leading-relaxed text-vl-ink-secondary">{meta.description}</p>
                                <p className="mt-4 font-mono text-[11px] text-vl-ink-muted">
                                    probe: {key} · {ok ? 'PASS' : 'DEGRADED'}
                                </p>
                            </SpotlightSurface>
                        );
                    })}
                </div>
            </div>
        </PortalLayout>
    );
}

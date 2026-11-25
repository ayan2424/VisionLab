import React from 'react';
import { router, useForm } from '@inertiajs/react';
import { AlertTriangle, Save, Wrench } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface SystemIndexProps {
    configs: Record<string, { key: string; value: string | boolean | null }>;
}

function isFlagEnabled(configs: SystemIndexProps['configs'], key: string): boolean {
    const raw = configs[key]?.value;
    return raw === true || raw === 'true' || raw === '1';
}

export default function AdminSystemIndex({ configs }: SystemIndexProps) {
    const initialFlags: string[] = [];
    if (isFlagEnabled(configs, 'flag_ai_enabled')) initialFlags.push('ai_enabled');
    if (isFlagEnabled(configs, 'flag_video_calls_enabled')) initialFlags.push('video_calls_enabled');
    if (isFlagEnabled(configs, 'flag_registration_open')) initialFlags.push('registration_open');
    if (isFlagEnabled(configs, 'global_allow_marketplace')) initialFlags.push('global_allow_marketplace');

    const maintenanceOn = isFlagEnabled(configs, 'maintenance_mode');

    const form = useForm({ flags: initialFlags });

    const toggleFlag = (name: string) => {
        const exists = form.data.flags.includes(name);
        form.setData(
            'flags',
            exists ? form.data.flags.filter((x) => x !== name) : [...form.data.flags, name]
        );
    };

    return (
        <PortalLayout title="System Config" pageTitle="Platform Feature Flags & Maintenance" adminMode>
            <div className="mx-auto max-w-3xl space-y-6">
                <SpotlightSurface accent="sky" className="p-7 space-y-5">
                    <div className="flex items-center gap-3">
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                            <Wrench className="h-5 w-5" />
                        </span>
                        <div>
                            <h2 className="font-display text-xl font-semibold text-vl-ink">Global Feature Flags</h2>
                            <p className="text-xs text-vl-ink-secondary">
                                Control live platform capabilities across all student and faculty containers.
                            </p>
                        </div>
                    </div>

                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            form.post('/admin/system/flags');
                        }}
                        className="space-y-3"
                    >
                        {[
                            { id: 'ai_enabled', label: 'Enable VisionLab AI Tutor & HITL Patch Gate' },
                            { id: 'global_allow_marketplace', label: 'Allow Global Offline VSIX Registry Sync' },
                            { id: 'registration_open', label: 'Allow Open Registration (Disabled by Campus Policy)' },
                        ].map((item) => (
                            <label
                                key={item.id}
                                className="flex items-center justify-between rounded-xl bg-vl-bg/80 px-4 py-3.5 cursor-pointer"
                            >
                                <span className="text-sm font-medium text-vl-ink">{item.label}</span>
                                <input
                                    type="checkbox"
                                    checked={form.data.flags.includes(item.id)}
                                    onChange={() => toggleFlag(item.id)}
                                    className="h-4 w-4 rounded text-sky-500"
                                />
                            </label>
                        ))}

                        <div className="pt-2">
                            <button type="submit" disabled={form.processing} className="vl-btn-primary">
                                <Save className="h-4 w-4" />
                                {form.processing ? 'Saving...' : 'Save Feature Flags'}
                            </button>
                        </div>
                    </form>
                </SpotlightSurface>

                <SpotlightSurface accent="amber" className="p-7">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <span className="flex h-10 w-10 items-center justify-center rounded-full bg-amber-500/15 text-amber-500">
                                <AlertTriangle className="h-5 w-5" />
                            </span>
                            <div>
                                <div className="flex items-center gap-2">
                                    <h3 className="font-display text-lg font-semibold text-vl-ink">Maintenance Mode</h3>
                                    <StatusPill tone={maintenanceOn ? 'amber' : 'teal'}>
                                        {maintenanceOn ? 'Enabled' : 'Live'}
                                    </StatusPill>
                                </div>
                                <p className="text-xs text-vl-ink-secondary">
                                    Restrict portal access exclusively to campus administrators during upgrades.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            onClick={() =>
                                router.post('/admin/system/maintenance', maintenanceOn ? {} : { maintenance_mode: true })
                            }
                            className={maintenanceOn ? 'vl-btn-primary' : 'vl-btn-danger'}
                        >
                            {maintenanceOn ? 'Disable Maintenance' : 'Enable Maintenance'}
                        </button>
                    </div>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

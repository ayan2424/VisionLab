import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Cpu, Terminal } from 'lucide-react';
import { cn } from '@/lib/cn';
import { PortalLayout } from '@/layouts/PortalLayout';
import { MagneticButton, SpotlightSurface } from '@/components/ui/Primitives';

interface TemplateItem {
    id: number;
    name: string;
    description: string | null;
    language: string;
    image?: string | null;
}

export default function WorkspaceCreate({ templates }: { templates: TemplateItem[] }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        template_id: templates[0]?.id ? String(templates[0].id) : '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/workspace');
    };

    return (
        <PortalLayout title="Provision Workspace" pageTitle="Provision Workspace">
            <div className="mx-auto max-w-3xl space-y-6">
                <Link
                    href="/workspace"
                    prefetch="hover"
                    className="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink"
                >
                    <ArrowLeft className="h-3.5 w-3.5" />
                    Back to Workspaces
                </Link>

                <SpotlightSurface accent="sky" className="p-7 sm:p-9">
                    <div className="mb-7 space-y-1.5">
                        <h2 className="font-display text-2xl font-semibold tracking-tight text-vl-ink">
                            Create a Sovereign Container Workspace
                        </h2>
                        <p className="text-sm text-vl-ink-secondary">
                            Choose an approved department toolchain template. Your persistent Docker volume initializes automatically.
                        </p>
                    </div>

                    <form onSubmit={submit} className="space-y-6">
                        <div>
                            <label htmlFor="ws-name" className="mb-2 block text-xs font-medium text-vl-ink-secondary">
                                Workspace Name
                            </label>
                            <input
                                id="ws-name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="cs301-allocator-lab"
                                required
                                className="vl-input w-full"
                            />
                            {errors.name && <p className="mt-1.5 text-xs text-rose-400">{errors.name}</p>}
                        </div>

                        <div>
                            <p className="mb-3 text-xs font-medium text-vl-ink-secondary">Select Environment Template</p>
                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                {templates.map((tpl) => {
                                    const selected = String(tpl.id) === data.template_id;
                                    return (
                                        <button
                                            key={tpl.id}
                                            type="button"
                                            onClick={() => setData('template_id', String(tpl.id))}
                                            className={cn(
                                                'flex flex-col items-start gap-2 rounded-full p-4 text-left transition-all',
                                                selected
                                                    ? 'bg-sky-500/15 ring-1 ring-sky-400/60'
                                                    : 'bg-vl-bg/75 ring-1 ring-vl-border/15 hover:bg-vl-surface-raised/80'
                                            )}
                                        >
                                            <div className="flex w-full items-center justify-between">
                                                <Cpu className={cn('h-4 w-4', selected ? 'text-sky-400' : 'text-vl-ink-muted')} />
                                                <span className="font-mono text-[11px] uppercase text-vl-ink-muted">
                                                    {tpl.language}
                                                </span>
                                            </div>
                                            <p className="font-display text-sm font-semibold text-vl-ink">{tpl.name}</p>
                                            <p className="text-xs leading-relaxed text-vl-ink-secondary">
                                                {tpl.description || 'Deterministic Nix-backed container image.'}
                                            </p>
                                        </button>
                                    );
                                })}
                            </div>
                            {errors.template_id && <p className="mt-1.5 text-xs text-rose-400">{errors.template_id}</p>}
                        </div>

                        <div className="flex items-center justify-end gap-3 pt-2">
                            <Link
                                href="/workspace"
                                prefetch="hover"
                                className="rounded-[24px] px-4 py-2.5 text-xs font-medium text-vl-ink-secondary hover:text-vl-ink"
                            >
                                Cancel
                            </Link>
                            <MagneticButton type="submit" size="md" variant="primary" disabled={processing}>
                                <Terminal className="h-4 w-4" />
                                {processing ? 'Provisioning...' : 'Launch Container'}
                            </MagneticButton>
                        </div>
                    </form>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

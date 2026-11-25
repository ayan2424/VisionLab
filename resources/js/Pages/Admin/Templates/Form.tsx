import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface TemplateRecord {
    id: number;
    name: string;
    description?: string | null;
    git_url?: string | null;
    language: string;
    start_command?: string | null;
    nix_config?: string | null;
    bootstrap_script?: string | null;
    ui_parameters?: Record<string, unknown> | string | null;
    is_active: boolean;
}

export default function AdminTemplateForm({ template }: { template?: TemplateRecord | null }) {
    const isEdit = Boolean(template);
    const uiParamsInitial =
        template?.ui_parameters && typeof template.ui_parameters === 'object'
            ? JSON.stringify(template.ui_parameters, null, 2)
            : (template?.ui_parameters as string) ?? '';

    const form = useForm({
        name: template?.name ?? '',
        description: template?.description ?? '',
        language: template?.language ?? 'python',
        git_url: template?.git_url ?? '',
        start_command: template?.start_command ?? '',
        nix_config: template?.nix_config ?? '',
        bootstrap_script: template?.bootstrap_script ?? '',
        ui_parameters: uiParamsInitial,
        is_active: template?.is_active ?? true,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit && template) {
            form.put(`/admin/templates/${template.id}`);
        } else {
            form.post('/admin/templates');
        }
    };

    return (
        <PortalLayout
            title={isEdit ? `Edit ${template?.name}` : 'Create Template'}
            pageTitle={isEdit ? 'Edit Workspace Template' : 'New Workspace Template'}
            adminMode
        >
            <div className="mx-auto max-w-3xl">
                <SpotlightSurface accent="sky" className="p-7 space-y-6">
                    <form onSubmit={submit} className="space-y-4">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5">
                                <label htmlFor="name" className="block text-xs font-medium text-vl-ink-secondary">Template Name</label>
                                <input id="name" type="text" required value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} className="vl-input w-full" />
                                {form.errors.name && <p className="text-xs text-rose-500">{form.errors.name}</p>}
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="language" className="block text-xs font-medium text-vl-ink-secondary">Primary Language</label>
                                <input id="language" type="text" required value={form.data.language} onChange={(e) => form.setData('language', e.target.value)} className="vl-input w-full font-mono" />
                            </div>
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="description" className="block text-xs font-medium text-vl-ink-secondary">Description</label>
                            <textarea id="description" rows={2} value={form.data.description} onChange={(e) => form.setData('description', e.target.value)} className="vl-input w-full" />
                        </div>

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5">
                                <label htmlFor="git_url" className="block text-xs font-medium text-vl-ink-secondary">Starter Git URL (optional)</label>
                                <input id="git_url" type="url" value={form.data.git_url} onChange={(e) => form.setData('git_url', e.target.value)} className="vl-input w-full" />
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="start_command" className="block text-xs font-medium text-vl-ink-secondary">Start Command</label>
                                <input id="start_command" type="text" value={form.data.start_command} onChange={(e) => form.setData('start_command', e.target.value)} className="vl-input w-full font-mono" />
                            </div>
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="nix_config" className="block text-xs font-medium text-vl-ink-secondary">.vision/dev.nix Expression</label>
                            <textarea id="nix_config" rows={5} value={form.data.nix_config} onChange={(e) => form.setData('nix_config', e.target.value)} className="w-full rounded-xl bg-[#05080F] p-4 font-mono text-xs text-sky-200 ring-1 ring-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500" />
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="bootstrap_script" className="block text-xs font-medium text-vl-ink-secondary">Bootstrap Script</label>
                            <textarea id="bootstrap_script" rows={3} value={form.data.bootstrap_script} onChange={(e) => form.setData('bootstrap_script', e.target.value)} className="w-full rounded-xl bg-[#05080F] p-4 font-mono text-xs text-teal-200 ring-1 ring-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500" />
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="ui_parameters" className="block text-xs font-medium text-vl-ink-secondary">UI Parameters (JSON)</label>
                            <textarea id="ui_parameters" rows={3} value={form.data.ui_parameters} onChange={(e) => form.setData('ui_parameters', e.target.value)} className="vl-input w-full font-mono text-xs" />
                            {form.errors.ui_parameters && <p className="text-xs text-rose-500">{form.errors.ui_parameters}</p>}
                        </div>

                        <label className="flex items-center gap-2 text-xs font-medium text-vl-ink-secondary cursor-pointer">
                            <input type="checkbox" checked={form.data.is_active} onChange={(e) => form.setData('is_active', e.target.checked)} className="h-4 w-4 rounded text-sky-500" />
                            Active Template
                        </label>

                        <div className="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" disabled={form.processing} className="vl-btn-primary">
                                <Save className="h-4 w-4" />
                                {form.processing ? 'Saving...' : 'Save Template'}
                            </button>
                            <Link href="/admin/templates" className="vl-btn-secondary">
                                <ArrowLeft className="h-4 w-4" />
                                Cancel
                            </Link>
                        </div>
                    </form>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

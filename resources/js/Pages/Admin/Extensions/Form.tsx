import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save, Upload } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface ExtensionRecord {
    id: number;
    name: string;
    package_identifier: string;
    version: string;
    description?: string | null;
    is_global: boolean;
}

export default function AdminExtensionForm({ extension }: { extension?: ExtensionRecord | null }) {
    const isEdit = Boolean(extension);
    const form = useForm<{
        name: string;
        package_identifier: string;
        version: string;
        description: string;
        is_global: boolean;
        artifact: File | null;
        _method?: string;
    }>({
        name: extension?.name ?? '',
        package_identifier: extension?.package_identifier ?? '',
        version: extension?.version ?? '1.0.0',
        description: extension?.description ?? '',
        is_global: extension?.is_global ?? false,
        artifact: null,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit && extension) {
            form.transform((data) => ({ ...data, _method: 'PUT' }));
            form.post(`/admin/extensions/${extension.id}`, { forceFormData: true });
        } else {
            form.post('/admin/extensions', { forceFormData: true });
        }
    };

    return (
        <PortalLayout
            title={isEdit ? `Edit ${extension?.name}` : 'Upload VSIX Extension'}
            pageTitle={isEdit ? 'Edit VSIX Package' : 'Upload Signed VSIX Package'}
            adminMode
        >
            <div className="mx-auto max-w-2xl">
                <SpotlightSurface accent="teal" className="p-7 space-y-6">
                    <div>
                        <h2 className="font-display text-xl font-semibold text-vl-ink">
                            {isEdit ? 'Update VSIX Metadata' : 'Register Offline VSIX Extension'}
                        </h2>
                        <p className="text-xs text-vl-ink-secondary">
                            Uploaded VSIX archives are automatically hashed with SHA-256 prior to container distribution.
                        </p>
                    </div>

                    <form onSubmit={submit} className="space-y-4">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5">
                                <label htmlFor="name" className="block text-xs font-medium text-vl-ink-secondary">Display Name</label>
                                <input
                                    id="name"
                                    type="text"
                                    required
                                    value={form.data.name}
                                    onChange={(e) => form.setData('name', e.target.value)}
                                    className="vl-input w-full"
                                />
                                {form.errors.name && <p className="text-xs text-rose-500">{form.errors.name}</p>}
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="version" className="block text-xs font-medium text-vl-ink-secondary">Version</label>
                                <input
                                    id="version"
                                    type="text"
                                    required
                                    value={form.data.version}
                                    onChange={(e) => form.setData('version', e.target.value)}
                                    className="vl-input w-full font-mono"
                                />
                                {form.errors.version && <p className="text-xs text-rose-500">{form.errors.version}</p>}
                            </div>
                        </div>

                        {!isEdit && (
                            <div className="space-y-1.5">
                                <label htmlFor="package_identifier" className="block text-xs font-medium text-vl-ink-secondary">
                                    Package Identifier
                                </label>
                                <input
                                    id="package_identifier"
                                    type="text"
                                    required
                                    placeholder="e.g. ms-python.python"
                                    value={form.data.package_identifier}
                                    onChange={(e) => form.setData('package_identifier', e.target.value)}
                                    className="vl-input w-full font-mono"
                                />
                                {form.errors.package_identifier && (
                                    <p className="text-xs text-rose-500">{form.errors.package_identifier}</p>
                                )}
                            </div>
                        )}

                        <div className="space-y-1.5">
                            <label htmlFor="description" className="block text-xs font-medium text-vl-ink-secondary">Description</label>
                            <textarea
                                id="description"
                                rows={3}
                                value={form.data.description}
                                onChange={(e) => form.setData('description', e.target.value)}
                                className="vl-input w-full"
                            />
                        </div>

                        <div className="space-y-1.5">
                            <label htmlFor="artifact" className="block text-xs font-medium text-vl-ink-secondary">
                                VSIX / ZIP Package {isEdit ? '(optional replacement)' : '(required)'}
                            </label>
                            <input
                                id="artifact"
                                type="file"
                                accept=".vsix,.zip"
                                required={!isEdit}
                                onChange={(e) => form.setData('artifact', e.target.files?.[0] ?? null)}
                                className="block w-full text-xs text-vl-ink-secondary file:mr-3 file:rounded-full file:border-0 file:bg-teal-500/15 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-teal-600 dark:file:text-teal-300"
                            />
                            {form.errors.artifact && <p className="text-xs text-rose-500">{form.errors.artifact}</p>}
                        </div>

                        <label className="flex items-center gap-2 text-xs font-medium text-vl-ink-secondary cursor-pointer">
                            <input
                                type="checkbox"
                                checked={form.data.is_global}
                                onChange={(e) => form.setData('is_global', e.target.checked)}
                                className="h-4 w-4 rounded text-teal-500"
                            />
                            Install globally in all provisioned containers
                        </label>

                        <div className="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" disabled={form.processing} className="vl-btn-primary">
                                {isEdit ? <Save className="h-4 w-4" /> : <Upload className="h-4 w-4" />}
                                {form.processing ? 'Hashing & Saving...' : isEdit ? 'Update Extension' : 'Upload & Hash VSIX'}
                            </button>
                            <Link href="/admin/extensions" className="vl-btn-secondary">
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

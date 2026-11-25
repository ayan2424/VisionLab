import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface } from '@/components/ui/Primitives';

interface FieldDef {
    name: string;
    label: string;
    type: 'text' | 'email' | 'number' | 'date' | 'textarea' | 'select' | 'checkbox';
    required?: boolean;
    placeholder?: string;
    options?: Array<{ value: string | number; label: string }>;
}

interface ErpFormProps {
    title: string;
    subtitle: string;
    submitUrl: string;
    method: 'post' | 'put';
    cancelUrl: string;
    fields: FieldDef[];
    initialValues: Record<string, string | number | boolean>;
}

export default function ErpResourceForm({
    title,
    subtitle,
    submitUrl,
    method,
    cancelUrl,
    fields,
    initialValues,
}: ErpFormProps) {
    const form = useForm<Record<string, string | number | boolean>>(initialValues);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (method === 'put') {
            form.put(submitUrl);
        } else {
            form.post(submitUrl);
        }
    };

    return (
        <PortalLayout title={title} pageTitle={title} subtitle={subtitle} adminMode>
            <div className="mx-auto max-w-2xl">
                <SpotlightSurface accent="sky" className="p-7 space-y-6">
                    <div>
                        <h2 className="font-display text-xl font-semibold text-vl-ink">{title}</h2>
                        <p className="text-xs text-vl-ink-secondary">{subtitle}</p>
                    </div>

                    <form onSubmit={submit} className="space-y-4">
                        {fields.map((field) => {
                            const val = form.data[field.name];
                            const err = form.errors[field.name];

                            if (field.type === 'checkbox') {
                                return (
                                    <label key={field.name} className="flex items-center gap-2.5 text-xs font-medium text-vl-ink-secondary cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={Boolean(val)}
                                            onChange={(e) => form.setData(field.name, e.target.checked)}
                                            className="h-4 w-4 rounded text-sky-500"
                                        />
                                        {field.label}
                                    </label>
                                );
                            }

                            if (field.type === 'select') {
                                return (
                                    <div key={field.name} className="space-y-1.5">
                                        <label htmlFor={field.name} className="block text-xs font-medium text-vl-ink-secondary">
                                            {field.label}
                                        </label>
                                        <select
                                            id={field.name}
                                            required={field.required}
                                            value={String(val ?? '')}
                                            onChange={(e) => form.setData(field.name, e.target.value)}
                                            className="vl-input w-full"
                                        >
                                            <option value="">Select {field.label.toLowerCase()}...</option>
                                            {(field.options ?? []).map((opt) => (
                                                <option key={opt.value} value={opt.value}>
                                                    {opt.label}
                                                </option>
                                            ))}
                                        </select>
                                        {err && <p className="text-xs text-rose-500">{err}</p>}
                                    </div>
                                );
                            }

                            if (field.type === 'textarea') {
                                return (
                                    <div key={field.name} className="space-y-1.5">
                                        <label htmlFor={field.name} className="block text-xs font-medium text-vl-ink-secondary">
                                            {field.label}
                                        </label>
                                        <textarea
                                            id={field.name}
                                            rows={3}
                                            required={field.required}
                                            value={String(val ?? '')}
                                            onChange={(e) => form.setData(field.name, e.target.value)}
                                            className="vl-input w-full"
                                        />
                                        {err && <p className="text-xs text-rose-500">{err}</p>}
                                    </div>
                                );
                            }

                            return (
                                <div key={field.name} className="space-y-1.5">
                                    <label htmlFor={field.name} className="block text-xs font-medium text-vl-ink-secondary">
                                        {field.label}
                                    </label>
                                    <input
                                        id={field.name}
                                        type={field.type}
                                        required={field.required}
                                        placeholder={field.placeholder}
                                        value={String(val ?? '')}
                                        onChange={(e) => form.setData(field.name, e.target.value)}
                                        className="vl-input w-full"
                                    />
                                    {err && <p className="text-xs text-rose-500">{err}</p>}
                                </div>
                            );
                        })}

                        <div className="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" disabled={form.processing} className="vl-btn-primary">
                                <Save className="h-4 w-4" />
                                {form.processing ? 'Saving...' : 'Save Record'}
                            </button>
                            <Link href={cancelUrl} className="vl-btn-secondary">
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

import React from 'react';
import { Link } from '@inertiajs/react';
import { ArrowLeft, Printer, Receipt } from 'lucide-react';
import { PortalLayout } from '@/layouts/PortalLayout';
import { SpotlightSurface, StatusPill } from '@/components/ui/Primitives';

interface ChallanShowProps {
    challan: {
        id: number;
        challan_number: string;
        amount: number;
        late_fee?: number | null;
        due_date: string;
        status?: string;
        user?: { name: string; email: string; student_id?: string | null } | null;
    };
}

export default function ChallanShow({ challan }: ChallanShowProps) {
    const total = Number(challan.amount) + Number(challan.late_fee ?? 0);

    return (
        <PortalLayout title={`Challan ${challan.challan_number}`} pageTitle="Fee Challan Voucher" adminMode>
            <div className="mx-auto max-w-2xl space-y-5">
                <div className="flex items-center justify-between">
                    <Link href="/admin/challans" className="vl-btn-secondary">
                        <ArrowLeft className="h-4 w-4" />
                        Back to Challans
                    </Link>
                    <button type="button" onClick={() => window.print()} className="vl-btn-primary">
                        <Printer className="h-4 w-4" />
                        Print Voucher
                    </button>
                </div>

                <SpotlightSurface accent="sky" className="p-8 space-y-6">
                    <div className="flex items-start justify-between gap-4 border-b border-vl-border/10 pb-5">
                        <div className="flex items-center gap-3">
                            <span className="flex h-11 w-11 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                                <Receipt className="h-5 w-5" />
                            </span>
                            <div>
                                <p className="font-mono text-xs text-sky-500">{challan.challan_number}</p>
                                <h2 className="font-display text-xl font-semibold text-vl-ink">
                                    Institutional Fee Voucher
                                </h2>
                            </div>
                        </div>
                        <StatusPill tone={challan.status === 'paid' ? 'teal' : 'amber'}>
                            {challan.status ?? 'unpaid'}
                        </StatusPill>
                    </div>

                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div className="rounded-xl bg-vl-bg/80 p-4">
                            <p className="text-xs text-vl-ink-muted">Student</p>
                            <p className="mt-1 font-semibold text-vl-ink">{challan.user?.name ?? 'Student'}</p>
                            <p className="font-mono text-xs text-vl-ink-muted">
                                {challan.user?.student_id ?? challan.user?.email ?? '—'}
                            </p>
                        </div>
                        <div className="rounded-xl bg-vl-bg/80 p-4">
                            <p className="text-xs text-vl-ink-muted">Due Date</p>
                            <p className="mt-1 font-mono text-base font-semibold text-vl-ink tabular-nums">
                                {new Date(challan.due_date).toLocaleDateString()}
                            </p>
                        </div>
                    </div>

                    <div className="space-y-2 rounded-xl bg-vl-bg/80 p-5 font-mono text-xs tabular-nums">
                        <div className="flex justify-between">
                            <span className="text-vl-ink-secondary">Base Tuition / Lab Fee</span>
                            <span className="text-vl-ink">{Number(challan.amount).toLocaleString()}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-vl-ink-secondary">Late Surcharge</span>
                            <span className="text-vl-ink">{Number(challan.late_fee ?? 0).toLocaleString()}</span>
                        </div>
                        <div className="flex justify-between border-t border-vl-border/15 pt-3 text-base font-bold text-vl-ink">
                            <span>Total Payable</span>
                            <span>{total.toLocaleString()}</span>
                        </div>
                    </div>
                </SpotlightSurface>
            </div>
        </PortalLayout>
    );
}

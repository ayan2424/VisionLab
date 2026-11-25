import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { RefreshCw, WifiOff } from 'lucide-react';
import { BrandLogo } from '@/components/ui/Primitives';

export default function Offline() {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-vl-bg px-4 text-vl-ink">
            <Head title="Offline" />
            <div className="w-full max-w-md rounded-xl bg-vl-surface p-9 text-center shadow-2xl ring-1 ring-vl-border/15 space-y-6">
                <div className="flex justify-center">
                    <BrandLogo />
                </div>
                <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-500/15 text-amber-500">
                    <WifiOff className="h-7 w-7" />
                </div>
                <div className="space-y-2">
                    <h1 className="font-display text-2xl font-semibold tracking-tight">
                        Campus cluster unreachable
                    </h1>
                    <p className="text-sm leading-relaxed text-vl-ink-secondary">
                        Your browser appears to be offline. Reconnect to your university network to resume your live VS Code containers.
                    </p>
                </div>
                <div className="flex flex-wrap justify-center gap-3 pt-2">
                    <button
                        type="button"
                        onClick={() => window.location.reload()}
                        className="vl-btn-primary"
                    >
                        <RefreshCw className="h-4 w-4" />
                        Retry Connection
                    </button>
                    <Link href="/" className="vl-btn-secondary">
                        Home
                    </Link>
                </div>
            </div>
        </div>
    );
}

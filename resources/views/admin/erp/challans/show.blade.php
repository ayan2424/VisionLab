@extends('layouts.admin')
@section('title', 'Challan Details')
@section('page-title', 'Challan Details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.challans.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/[0.05] transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Challan #{{ $challan->challan_number }}</h1>
            <p class="text-slate-400 text-sm">Review details and payment status.</p>
        </div>
    </div>

    <div class="vc-card p-6 border-t-4" style="border-top-color: {{ $challan->status === 'paid' ? 'var(--vc-success)' : 'var(--vc-warning)' }};">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Student / User</p>
                <p class="text-lg font-medium text-white">{{ $challan->user->name ?? 'Unknown' }} <span class="text-slate-400 text-sm">({{ $challan->user->email ?? 'N/A' }})</span></p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Status</p>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold {{ $challan->status === 'paid' ? 'bg-green-500/10 text-green-400' : 'bg-orange-500/10 text-orange-400' }}">
                    <span class="w-2 h-2 rounded-full {{ $challan->status === 'paid' ? 'bg-green-400' : 'bg-orange-400' }}"></span>
                    {{ ucfirst($challan->status ?? 'unpaid') }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 bg-black/20 p-5 rounded-2xl border border-white/5">
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Base Amount</p>
                <p class="text-2xl font-bold text-white">${{ number_format($challan->amount, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Late Fee</p>
                <p class="text-2xl font-bold text-red-400">${{ number_format($challan->late_fee ?? 0, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Payable</p>
                <p class="text-2xl font-bold" style="color:var(--vc-accent);">${{ number_format($challan->amount + ($challan->late_fee ?? 0), 2) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Due Date</p>
                <p class="text-md text-slate-300 font-mono">{{ $challan->due_date ? \Carbon\Carbon::parse($challan->due_date)->format('F d, Y') : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Paid Date</p>
                <p class="text-md text-slate-300 font-mono">{{ $challan->paid_date ? \Carbon\Carbon::parse($challan->paid_date)->format('F d, Y') : 'Not Paid' }}</p>
            </div>
        </div>
        
    </div>
</div>
@endsection

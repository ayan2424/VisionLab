@extends('layouts.admin')
@section('title', 'Generate Fee Challan')
@section('page-title', 'Generate Fee Challan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.challans.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/[0.05] transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Generate Challan</h1>
            <p class="text-slate-400 text-sm">Issue a new fee challan for a student.</p>
        </div>
    </div>

    <form action="{{ route('admin.challans.store') }}" method="POST" class="vc-card p-6 border-t-4" style="border-top-color:var(--vc-accent);">
        @csrf
        
        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">Student ID</label>
            <input type="number" name="user_id" class="vc-input w-full" required placeholder="User ID">
            @error('user_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">Challan Number</label>
            <input type="text" name="challan_number" value="CHL-{{ strtoupper(uniqid()) }}" class="vc-input w-full bg-black/20" readonly>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Amount ($)</label>
                <input type="number" step="0.01" name="amount" class="vc-input w-full" required placeholder="0.00">
                @error('amount') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Due Date</label>
                <input type="date" name="due_date" class="vc-input w-full" required>
                @error('due_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.challans.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-white/[0.05] hover:bg-white/[0.1] transition-colors">Cancel</a>
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold shadow-lg">Generate Challan</button>
        </div>
    </form>
</div>
@endsection

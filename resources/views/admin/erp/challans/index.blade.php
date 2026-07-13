@extends('layouts.admin')

@section('title', 'Fee Challans (ERP)')
@section('page-title', 'Fee Challans (ERP)')

@section('content')
<div class="vc-card overflow-hidden p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-bold mb-4" style="color:var(--vc-text);">All Fee Challans</h1>
                    <a href="{{ route('admin.challans.create') }}" class="btn-primary py-2 px-4 text-sm">Generate Challan</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Challan No</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Student</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Amount</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Due Date</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Status</th>
                            </tr>
                        </thead>
                        <tbody >
                            @forelse ($challans as $challan)
                                <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors border-b" style="border-color:var(--vc-border);">
                                    <td class="px-5 py-4 whitespace-nowrap text-sm font-medium ">{{ $challan->challan_number }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">{{ $challan->user->name }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">${{ number_format($challan->amount, 2) }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">{{ \Carbon\Carbon::parse($challan->due_date)->format('M d, Y') }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $challan->status === 'paid' ? 'green' : ($challan->status === 'overdue' ? 'red' : 'yellow') }}-100 text-{{ $challan->status === 'paid' ? 'green' : ($challan->status === 'overdue' ? 'red' : 'yellow') }}-800">
                                            {{ ucfirst($challan->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No fee challans found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
@endsection

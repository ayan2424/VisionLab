@extends('layouts.admin')

@section('title', 'Challan Details')
@section('page-title', 'Challan Details')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6 flex justify-between items-center border-b pb-4">
                    <h2 class="text-xl font-bold" style="color:var(--vc-text);">Challan #{{ $challan->challan_number }}</h2>
                    <a href="{{ route('admin.challans.index') }}" class="text-indigo-600 hover:underline">Back to List</a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase tracking-wider mb-1">Student / User</p>
                        <p class="text-lg font-medium" style="color:var(--vc-text);">{{ $challan->user->name ?? 'Unknown' }} ({{ $challan->user->email ?? 'N/A' }})</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase tracking-wider mb-1">Status</p>
                        <p class="text-lg font-medium {{ $challan->status === 'paid' ? 'text-green-600' : 'text-orange-500' }}">
                            {{ ucfirst($challan->status ?? 'unpaid') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 bg-gray-50 p-4 rounded-xl border">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">Amount</p>
                        <p class="text-xl font-bold" style="color:var(--vc-text);">${{ number_format($challan->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">Late Fee</p>
                        <p class="text-xl font-bold text-red-500">${{ number_format($challan->late_fee ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">Total Payable</p>
                        <p class="text-xl font-bold" style="color:var(--vc-text);">${{ number_format($challan->amount + ($challan->late_fee ?? 0), 2) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">Due Date</p>
                        <p class="text-md" style="color:var(--vc-text);">{{ $challan->due_date ? \Carbon\Carbon::parse($challan->due_date)->format('F d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">Paid Date</p>
                        <p class="text-md" style="color:var(--vc-text);">{{ $challan->paid_date ? \Carbon\Carbon::parse($challan->paid_date)->format('F d, Y') : 'Not Paid' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

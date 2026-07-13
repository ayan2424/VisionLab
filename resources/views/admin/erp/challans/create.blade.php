@extends('layouts.admin')

@section('title', 'Generate Fee Challan')
@section('page-title', 'Generate Fee Challan')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 ">
                <form action="{{ route('admin.challans.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Student ID</label>
                        <input type="number" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Challan Number</label>
                        <input type="text" name="challan_number" value="CHL-{{ strtoupper(uniqid()) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Amount ($)</label>
                        <input type="number" step="0.01" name="amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Due Date</label>
                        <input type="date" name="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('admin.challans.index') }}" class="mr-4 text-sm text-gray-600 hover:">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                            Generate Challan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

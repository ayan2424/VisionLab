@extends('layouts.admin')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Generate Fee Challan') }}
</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <form action="{{ route('admin.challans.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Student ID</label>
                        <input type="number" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Challan Number</label>
                        <input type="text" name="challan_number" value="CHL-{{ strtoupper(uniqid()) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Amount ($)</label>
                        <input type="number" step="0.01" name="amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input type="date" name="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('admin.challans.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Cancel</a>
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

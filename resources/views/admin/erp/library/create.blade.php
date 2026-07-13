@extends('layouts.admin')

@section('title', 'Add New Book')
@section('page-title', 'Add New Book')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 ">
                <form action="{{ route('admin.library.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">ISBN</label>
                        <input type="text" name="isbn" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Title</label>
                        <input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Author</label>
                        <input type="text" name="author" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Total Copies</label>
                        <input type="number" name="total_copies" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Available Copies</label>
                        <input type="number" name="available_copies" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('admin.library.index') }}" class="mr-4 text-sm text-gray-600 hover:">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                            Save Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

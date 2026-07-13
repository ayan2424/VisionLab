@extends('layouts.admin')

@section('title', 'Edit Book')
@section('page-title', 'Edit Book')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.library.update', $library->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">ISBN</label>
                        <input type="text" name="isbn" value="{{ $library->isbn }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Title</label>
                        <input type="text" name="title" value="{{ $library->title }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Author</label>
                        <input type="text" name="author" value="{{ $library->author }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Total Copies</label>
                        <input type="number" name="total_copies" value="{{ $library->total_copies }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required min="1">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Available Copies</label>
                        <input type="number" name="available_copies" value="{{ $library->available_copies }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required min="0">
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('admin.library.index') }}" class="mr-4 text-sm text-gray-600 hover:underline">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                            Update Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

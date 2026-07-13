@extends('layouts.admin')
@section('title', 'Edit Book')
@section('page-title', 'Edit Book')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.library.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/[0.05] transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Edit Book</h1>
            <p class="text-slate-400 text-sm">Update details for "{{ $library->title }}".</p>
        </div>
    </div>

    <form action="{{ route('admin.library.update', $library->id) }}" method="POST" class="vc-card p-6 border-t-4" style="border-top-color:var(--vc-accent);">
        @csrf
        @method('PUT')
        
        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">ISBN</label>
            <input type="text" name="isbn" value="{{ $library->isbn }}" class="vc-input w-full" placeholder="e.g. 978-3-16-148410-0">
            @error('isbn') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Title</label>
                <input type="text" name="title" value="{{ $library->title }}" class="vc-input w-full" required>
                @error('title') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Author</label>
                <input type="text" name="author" value="{{ $library->author }}" class="vc-input w-full" required>
                @error('author') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Total Copies</label>
                <input type="number" name="total_copies" value="{{ $library->total_copies }}" class="vc-input w-full" required min="1">
                @error('total_copies') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Available Copies</label>
                <input type="number" name="available_copies" value="{{ $library->available_copies }}" class="vc-input w-full" required min="0">
                @error('available_copies') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.library.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-white/[0.05] hover:bg-white/[0.1] transition-colors">Cancel</a>
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold shadow-lg">Update Book</button>
        </div>
    </form>
</div>
@endsection

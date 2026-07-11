@extends('layouts.admin')
@section('title', 'Edit Campus')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.campuses.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/[0.05] transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Edit Campus</h1>
            <p class="text-slate-400 text-sm">Update details for {{ $campus->name }}.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.campuses.update', $campus) }}" class="vc-card p-6 border-t-4" style="border-top-color:var(--vc-accent);">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Name</label>
                <input type="text" name="name" value="{{ old('name', $campus->name) }}" required class="vc-input w-full" placeholder="e.g. Main Campus">
                @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Code</label>
                <input type="text" name="code" value="{{ old('code', $campus->code) }}" class="vc-input w-full" placeholder="e.g. MAIN">
                @error('code') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">City</label>
                <input type="text" name="city" value="{{ old('city', $campus->city) }}" class="vc-input w-full" placeholder="e.g. New York">
                @error('city') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Address</label>
                <input type="text" name="address" value="{{ old('address', $campus->address) }}" class="vc-input w-full" placeholder="Full address">
                @error('address') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Email</label>
                <input type="email" name="email" value="{{ old('email', $campus->email) }}" class="vc-input w-full" placeholder="contact@campus.edu">
                @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $campus->phone) }}" class="vc-input w-full" placeholder="+1 234 567 890">
                @error('phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-8">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $campus->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-600 text-indigo-600 focus:ring-indigo-500 bg-gray-800">
                <span class="text-sm font-bold text-slate-300">Is Active</span>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.campuses.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-white/[0.05] hover:bg-white/[0.1] transition-colors">Cancel</a>
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold shadow-lg">Update Campus</button>
        </div>
    </form>
</div>
@endsection

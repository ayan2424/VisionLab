@extends('layouts.admin')
@section('title', 'Edit Extension')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.extensions.index') }}" class="text-slate-500 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-white">Edit Extension</h1>
    </div>

    <form method="POST" action="{{ route('admin.extensions.update', $extension->id) }}"
          class="rounded-2xl border border-white/[0.07] p-6 space-y-5" style="background:#111111;">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Extension Name</label>
            <input type="text" name="name" value="{{ old('name', $extension->name) }}" required
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Package Identifier <span class="text-slate-500 font-normal">(read-only)</span></label>
            <input type="text" value="{{ $extension->package_identifier }}" readonly
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.06] bg-white/[0.02] text-slate-500 text-sm font-mono cursor-not-allowed">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Version</label>
            <input type="text" name="version" value="{{ old('version', $extension->version) }}" required
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Description</label>
            <textarea name="description" rows="3"
                      class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm resize-none focus:outline-none focus:border-violet-500/60 transition-all">{{ old('description', $extension->description) }}</textarea>
        </div>
        <div class="flex items-center gap-3">
            <input type="checkbox" id="is_global" name="is_global" value="1" {{ old('is_global', $extension->is_global) ? 'checked' : '' }} class="rounded accent-violet-500">
            <label for="is_global" class="text-sm text-slate-300">Enable globally by default</label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.extensions.index') }}" class="px-5 py-2.5 rounded-xl text-sm text-slate-400 border border-white/[0.08] hover:text-white transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">Save Changes</button>
        </div>
    </form>
</div>
@endsection

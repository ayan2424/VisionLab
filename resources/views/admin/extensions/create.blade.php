@extends('layouts.admin')
@section('title', 'Add Extension')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.extensions.index') }}" class="text-slate-500 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-white">Add Extension</h1>
    </div>

    <form method="POST" action="{{ route('admin.extensions.store') }}" enctype="multipart/form-data"
          class="rounded-2xl border border-white/[0.07] p-6 space-y-5" style="background:#111111;">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Extension Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. My Extension"
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Package Identifier</label>
            <input type="text" name="package_identifier" value="{{ old('package_identifier') }}" required placeholder="publisher.extension-name"
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm font-mono focus:outline-none focus:border-violet-500/60 transition-all">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Version</label>
            <input type="text" name="version" value="{{ old('version', '1.0.0') }}" required placeholder="1.0.0"
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm focus:outline-none focus:border-violet-500/60 transition-all">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Description</label>
            <textarea name="description" rows="3" placeholder="Brief description of what this extension does."
                      class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-white text-sm resize-none focus:outline-none focus:border-violet-500/60 transition-all">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Extension File (.vsix)</label>
            <input type="file" name="artifact" accept=".vsix" required
                   class="w-full px-4 py-3 rounded-xl border border-white/[0.08] bg-white/[0.04] text-slate-400 text-sm focus:outline-none focus:border-violet-500/60 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-500/10 file:text-violet-400 hover:file:bg-violet-500/20">
            <p class="mt-2 text-xs text-slate-500">Upload the compiled .vsix extension file. Max size 50MB.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="checkbox" id="is_global" name="is_global" value="1" {{ old('is_global') ? 'checked' : '' }} class="rounded accent-violet-500">
            <label for="is_global" class="text-sm text-slate-300">Enable globally by default</label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.extensions.index') }}" class="px-5 py-2.5 rounded-xl text-sm text-slate-400 border border-white/[0.08] hover:text-white transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">Add Extension</button>
        </div>
    </form>
</div>
@endsection



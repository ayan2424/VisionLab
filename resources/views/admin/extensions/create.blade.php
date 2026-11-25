@extends('layouts.admin')
@section('title', 'Add Extension')
@section('page-title', 'Add Extension')

@section('content')
<div class="max-w-xl space-y-6">
    <div class="space-y-1.5">
        <a href="{{ route('admin.extensions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Extensions
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Register VSIX Extension</h2>
        <p class="text-sm text-vl-ink-secondary">Upload a signed .vsix bundle for SHA256 verification and campus workspace distribution.</p>
    </div>

    <x-card :padding="true">
        <form method="POST" action="{{ route('admin.extensions.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="ext-name" class="block text-sm font-medium text-vl-ink mb-2">Extension Name</label>
                <input id="ext-name" type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Python Language Server" class="vl-input">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="ext-pkg" class="block text-sm font-medium text-vl-ink mb-2">Package Identifier</label>
                    <input id="ext-pkg" type="text" name="package_identifier" value="{{ old('package_identifier') }}" required placeholder="publisher.extension-name" class="vl-input font-mono">
                </div>
                <div>
                    <label for="ext-version" class="block text-sm font-medium text-vl-ink mb-2">Version</label>
                    <input id="ext-version" type="text" name="version" value="{{ old('version', '1.0.0') }}" required placeholder="1.0.0" class="vl-input font-mono">
                </div>
            </div>

            <div>
                <label for="ext-desc" class="block text-sm font-medium text-vl-ink mb-2">Description</label>
                <textarea id="ext-desc" name="description" rows="3" placeholder="Summarize what this extension provides inside student containers..." class="vl-input resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="ext-artifact" class="block text-sm font-medium text-vl-ink mb-2">Extension Artifact (.vsix)</label>
                <input id="ext-artifact" type="file" name="artifact" accept=".vsix" required class="block w-full text-xs text-vl-ink-secondary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-vl-surface-raised file:text-vl-ink hover:file:bg-vl-accent/15">
                <p class="mt-1.5 text-xs text-vl-ink-muted">Compiled .vsix archive up to 50 MB. SHA256 digest is computed automatically.</p>
            </div>

            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" id="is_global" name="is_global" value="1" {{ old('is_global') ? 'checked' : '' }} class="h-4 w-4 rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface">
                <span class="text-sm text-vl-ink-secondary">Enable globally across all new workspaces</span>
            </label>

            <div class="pt-4 border-t border-vl-border/10 flex items-center justify-end gap-3">
                <a href="{{ route('admin.extensions.index') }}" class="vl-btn-secondary">Cancel</a>
                <button type="submit" class="vl-btn-primary">Add Extension</button>
            </div>
        </form>
    </x-card>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Edit Extension')
@section('page-title', 'Edit Extension')

@section('content')
<div class="max-w-xl space-y-6">
    <div class="space-y-1.5">
        <a href="{{ route('admin.extensions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Extensions
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Edit Extension</h2>
    </div>

    <x-card :padding="true">
        <form method="POST" action="{{ route('admin.extensions.update', $extension->id) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="ext-name" class="block text-sm font-medium text-vl-ink mb-2">Extension Name</label>
                <input id="ext-name" type="text" name="name" value="{{ old('name', $extension->name) }}" required class="vl-input">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="ext-pkg" class="block text-sm font-medium text-vl-ink mb-2">Package Identifier <span class="font-normal text-vl-ink-muted">(read-only)</span></label>
                    <input id="ext-pkg" type="text" value="{{ $extension->package_identifier }}" readonly class="vl-input font-mono opacity-65 cursor-not-allowed">
                </div>
                <div>
                    <label for="ext-version" class="block text-sm font-medium text-vl-ink mb-2">Version</label>
                    <input id="ext-version" type="text" name="version" value="{{ old('version', $extension->version) }}" required class="vl-input font-mono">
                </div>
            </div>

            <div>
                <label for="ext-desc" class="block text-sm font-medium text-vl-ink mb-2">Description</label>
                <textarea id="ext-desc" name="description" rows="3" class="vl-input resize-none">{{ old('description', $extension->description) }}</textarea>
            </div>

            <div>
                <label for="ext-artifact" class="block text-sm font-medium text-vl-ink mb-2">Extension Artifact (.vsix) <span class="font-normal text-vl-ink-muted">(leave empty to keep current)</span></label>
                <input id="ext-artifact" type="file" name="artifact" accept=".vsix" class="block w-full text-xs text-vl-ink-secondary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-vl-surface-raised file:text-vl-ink hover:file:bg-vl-accent/15">
                @if($extension->artifact_path)
                    <p class="mt-1.5 text-xs text-vl-teal">Current verified artifact is uploaded.</p>
                @else
                    <p class="mt-1.5 text-xs text-vl-gate">No VSIX artifact uploaded yet.</p>
                @endif
            </div>

            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" id="is_global" name="is_global" value="1" {{ old('is_global', $extension->is_global) ? 'checked' : '' }} class="h-4 w-4 rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface">
                <span class="text-sm text-vl-ink-secondary">Enable globally by default</span>
            </label>

            <div class="pt-4 border-t border-vl-border/10 flex items-center justify-end gap-3">
                <a href="{{ route('admin.extensions.index') }}" class="vl-btn-secondary">Cancel</a>
                <button type="submit" class="vl-btn-primary">Save Changes</button>
            </div>
        </form>
    </x-card>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Edit Workspace Template')
@section('page-title', 'Edit Template')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('admin.templates.index') }}" class="text-sm hover:underline" style="color:var(--vc-text-secondary);">Templates</a>
        <span style="color:var(--vc-muted);">/</span>
        <span class="text-sm" style="color:var(--vc-text);">Edit</span>
    </div>
    <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Edit Workspace Template</h1>
    <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Update the configuration for <strong>{{ $template->name }}</strong>.</p>
</div>

<div class="vc-card max-w-4xl">
    <form method="POST" action="{{ route('admin.templates.update', $template->id) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Template Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors" style="background:var(--vc-surface-dark);border-color:var(--vc-border);color:var(--vc-text);focus:border-color:var(--vc-accent);focus:ring-color:var(--vc-accent);" required value="{{ old('name', $template->name) }}">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Language / Stack <span class="text-red-500">*</span></label>
                <input type="text" name="language" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors" style="background:var(--vc-surface-dark);border-color:var(--vc-border);color:var(--vc-text);" required value="{{ old('language', $template->language) }}">
                @error('language')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Description</label>
            <textarea name="description" rows="2" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors" style="background:var(--vc-surface-dark);border-color:var(--vc-border);color:var(--vc-text);">{{ old('description', $template->description) }}</textarea>
            @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Git Repository URL (Optional)</label>
                <input type="url" name="git_url" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors" style="background:var(--vc-surface-dark);border-color:var(--vc-border);color:var(--vc-text);" value="{{ old('git_url', $template->git_url) }}">
                <p class="text-xs mt-1" style="color:var(--vc-muted);">Automatically clone this repository when the workspace initializes.</p>
                @error('git_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Start Command (Optional)</label>
                <input type="text" name="start_command" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors" style="background:var(--vc-surface-dark);border-color:var(--vc-border);color:var(--vc-text);" value="{{ old('start_command', $template->start_command) }}">
                <p class="text-xs mt-1" style="color:var(--vc-muted);">Command to run automatically when the workspace starts.</p>
                @error('start_command')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-2 flex items-center justify-between" style="color:var(--vc-text);">
                <span>Nix Configuration (`dev.nix`)</span>
                <a href="https://nix.dev" target="_blank" class="text-xs hover:underline" style="color:var(--vc-accent);">Nix Documentation &nearr;</a>
            </label>
            <textarea name="nix_config" rows="12" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors font-mono" style="background:#0a0a0a;border-color:var(--vc-border);color:#e5e7eb;">{{ old('nix_config', $template->nix_config) }}</textarea>
            <p class="text-xs mt-2" style="color:var(--vc-text-secondary);">This raw Nix configuration will be injected as <code>dev.nix</code> into the student's workspace root.</p>
            @error('nix_config')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-8">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                <span class="text-sm font-medium" style="color:var(--vc-text);">Active Template</span>
            </label>
            <p class="text-xs mt-1 ml-6" style="color:var(--vc-muted);">If unchecked, students will not be able to select this template for new workspaces.</p>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t" style="border-color:var(--vc-border);">
            <a href="{{ route('admin.templates.index') }}" class="btn-ghost py-2 px-4 rounded-lg font-medium">Cancel</a>
            <button type="submit" class="btn-primary py-2 px-6 rounded-lg font-medium shadow-lg hover:shadow-xl transition-all">Save Changes</button>
        </div>
    </form>
</div>
@endsection

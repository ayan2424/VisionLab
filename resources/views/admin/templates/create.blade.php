@extends('layouts.admin')
@section('title', 'Create Workspace Template')
@section('page-title', 'Create Template')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('admin.templates.index') }}" class="text-sm hover:underline" style="color:var(--vc-text-secondary);">Templates</a>
        <span style="color:var(--vc-muted);">/</span>
        <span class="text-sm" style="color:var(--vc-text);">Create</span>
    </div>
    <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Create Workspace Template</h1>
    <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Define a new Nix-based environment for student workspaces.</p>
</div>

<div class="vc-card max-w-4xl">
    <form method="POST" action="{{ route('admin.templates.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Template Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors" style="background:var(--vc-surface-dark);border-color:var(--vc-border);color:var(--vc-text);focus:border-color:var(--vc-accent);focus:ring-color:var(--vc-accent);" placeholder="e.g., Laravel 11 / PHP 8.3" required value="{{ old('name') }}">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Language / Stack <span class="text-red-500">*</span></label>
                <input type="text" name="language" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors" style="background:var(--vc-surface-dark);border-color:var(--vc-border);color:var(--vc-text);" placeholder="e.g., PHP, Node.js, Python" required value="{{ old('language') }}">
                @error('language')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Description</label>
            <textarea name="description" rows="2" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors" style="background:var(--vc-surface-dark);border-color:var(--vc-border);color:var(--vc-text);" placeholder="Briefly describe what this environment contains...">{{ old('description') }}</textarea>
            @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Git Repository URL (Optional)</label>
                <input type="url" name="git_url" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors" style="background:var(--vc-surface-dark);border-color:var(--vc-border);color:var(--vc-text);" placeholder="https://github.com/example/repo.git" value="{{ old('git_url') }}">
                <p class="text-xs mt-1" style="color:var(--vc-muted);">Automatically clone this repository when the workspace initializes.</p>
                @error('git_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2" style="color:var(--vc-text);">Start Command (Optional)</label>
                <input type="text" name="start_command" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors" style="background:var(--vc-surface-dark);border-color:var(--vc-border);color:var(--vc-text);" placeholder="e.g., npm run dev" value="{{ old('start_command') }}">
                <p class="text-xs mt-1" style="color:var(--vc-muted);">Command to run automatically when the workspace starts.</p>
                @error('start_command')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-2 flex items-center justify-between" style="color:var(--vc-text);">
                <span>Nix Configuration (`dev.nix`)</span>
                <a href="https://nix.dev" target="_blank" class="text-xs hover:underline" style="color:var(--vc-accent);">Nix Documentation &nearr;</a>
            </label>
            <textarea name="nix_config" rows="12" class="w-full rounded-md px-3 py-2 text-sm border focus:ring-1 focus:outline-none transition-colors font-mono" style="background:#0a0a0a;border-color:var(--vc-border);color:#e5e7eb;" placeholder="# Write Nix expression here...&#10;{ pkgs ? import <nixpkgs> {} }:&#10;pkgs.mkShell {&#10;  buildInputs = [&#10;    pkgs.php83&#10;    pkgs.php83Packages.composer&#10;  ];&#10;}">{{ old('nix_config') }}</textarea>
            <p class="text-xs mt-2" style="color:var(--vc-text-secondary);">This raw Nix configuration will be injected as <code>dev.nix</code> into the student's workspace root to provision isolated toolchains (PHP, Node, Python, etc.) without requiring apt or root access.</p>
            @error('nix_config')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-8">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" checked>
                <span class="text-sm font-medium" style="color:var(--vc-text);">Active Template</span>
            </label>
            <p class="text-xs mt-1 ml-6" style="color:var(--vc-muted);">If unchecked, students will not be able to select this template for new workspaces.</p>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t" style="border-color:var(--vc-border);">
            <a href="{{ route('admin.templates.index') }}" class="btn-ghost py-2 px-4 rounded-lg font-medium">Cancel</a>
            <button type="submit" class="btn-primary py-2 px-6 rounded-lg font-medium shadow-lg hover:shadow-xl transition-all">Create Template</button>
        </div>
    </form>
</div>
@endsection

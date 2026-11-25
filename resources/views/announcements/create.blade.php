@extends('layouts.dashboard')
@section('title', 'Post Global Announcement')
@section('page-title', 'New Announcement')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="space-y-1.5">
        <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Announcements
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Publish Campus Announcement</h2>
        <p class="text-sm text-vl-ink-secondary">Global announcements notify all authenticated students, faculty, and administrators via the topbar indicator.</p>
    </div>

    <x-card :padding="true">
        <form action="{{ route('announcements.store_global') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-vl-ink mb-2">Announcement Title</label>
                <input type="text" name="title" id="title" required value="{{ old('title') }}" placeholder="e.g., Scheduled Maintenance Window" class="vl-input">
                @error('title')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="body" class="block text-sm font-medium text-vl-ink mb-2">Message Details</label>
                <textarea name="body" id="body" rows="6" required placeholder="Share campus schedule updates, runtime upgrades, or important deadlines..." class="vl-input resize-y">{{ old('body') }}</textarea>
                @error('body')
                    <p class="mt-1.5 text-xs text-vl-danger">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="pinned" id="pinned" value="1" {{ old('pinned') ? 'checked' : '' }} class="h-4 w-4 rounded border-vl-border/25 text-vl-accent-cta focus:ring-vl-accent bg-vl-surface">
                <span class="text-sm text-vl-ink-secondary">Pin this announcement at the top of the feed</span>
            </label>

            <div class="pt-4 border-t border-vl-border/10 flex items-center justify-end gap-3">
                <a href="{{ route('announcements.index') }}" class="vl-btn-secondary">Cancel</a>
                <button type="submit" class="vl-btn-primary">Publish Announcement</button>
            </div>
        </form>
    </x-card>
</div>
@endsection

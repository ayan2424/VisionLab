@extends('layouts.dashboard')
@section('title', 'Setup Personal Workspace')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold mb-3" style="color:var(--vc-text);">Setup Your Workspace</h1>
        <p class="text-base max-w-2xl mx-auto" style="color:var(--vc-text-secondary);">
            Choose an environment template to initialize your cloud IDE.
            This will provision an isolated container tailored for your tech stack.
        </p>
    </div>

    @if($templates->isEmpty())
        <div class="vc-card text-center py-12">
            <svg class="w-12 h-12 mx-auto mb-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="text-lg font-bold mb-2" style="color:var(--vc-text);">No Templates Available</h3>
            <p class="text-sm" style="color:var(--vc-text-secondary);">Please ask your administrator to configure Workspace Templates before you can create a workspace.</p>
        </div>
    @else
        <form method="POST" action="{{ route('workspace.store') }}">
            @csrf
            <div class="mb-8">
                <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Workspace Name</label>
                <input type="text" name="name" value="{{ old('name', 'personal-' . $user->id) }}" required placeholder="e.g. personal-project-1"
                       class="w-full px-4 py-3 rounded-xl border border-white/[0.08] focus:outline-none focus:border-[var(--vc-accent)] transition-all"
                       style="background:var(--vc-surface-dark); color:var(--vc-text);">
                <p class="text-xs mt-2" style="color:var(--vc-text-secondary);">Give your workspace a unique, memorable name (only letters, numbers, hyphens, and spaces).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($templates as $index => $template)
                <label class="block cursor-pointer group relative">
                    <input type="radio" name="template_id" value="{{ $template->id }}" class="peer sr-only" {{ $index === 0 ? 'checked' : '' }} required>
                    
                    <div class="vc-card h-full border-2 transition-all duration-200 peer-checked:ring-2" 
                         style="border-color:transparent; peer-checked:border-color:var(--vc-accent); peer-checked:ring-color:rgba(var(--vc-accent-rgb),0.2);">
                        
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold" style="color:var(--vc-text);">{{ $template->name }}</h3>
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider" style="background:var(--vc-surface-dark);color:var(--vc-accent);">
                                {{ $template->language }}
                            </span>
                        </div>
                        
                        <p class="text-sm mb-6" style="color:var(--vc-text-secondary);">
                            {{ $template->description ?? 'Standard configured environment.' }}
                        </p>

                        <div class="absolute top-4 right-4 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-white" style="background:var(--vc-accent);">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="flex justify-center">
                <button type="submit" class="btn-primary py-3 px-8 text-lg rounded-xl font-bold shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Provision Workspace
                </button>
            </div>
        </form>
    @endif
</div>
@endsection

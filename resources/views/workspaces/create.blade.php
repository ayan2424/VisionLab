@extends('layouts.dashboard')
@section('title', 'New Workspace')
@section('page-title', 'New Workspace')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="space-y-1.5">
        <a href="{{ route('workspace.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Workspaces
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Provision a New Workspace</h2>
        <p class="text-sm text-vl-ink-secondary">Select a pinned runtime template to spin up an isolated browser VS Code container.</p>
    </div>

    @if($templates->isEmpty())
        <x-card :padding="true">
            <x-empty-state
                title="No workspace templates configured"
                description="Ask your campus administrator to add Docker/Nix runtime templates under Admin Templates."
            />
        </x-card>
    @else
        <form method="POST" action="{{ route('workspace.store') }}" class="space-y-6">
            @csrf

            <x-card :padding="true">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="workspace-name" class="block text-sm font-medium text-vl-ink mb-2">Workspace Name</label>
                        <input
                            id="workspace-name"
                            type="text"
                            name="name"
                            value="{{ old('name', 'personal-' . $user->id) }}"
                            required
                            placeholder="e.g., cs301-systems-lab"
                            class="vl-input"
                        >
                        <p class="mt-1.5 text-xs text-vl-ink-muted">Letters, numbers, hyphens, and spaces.</p>
                    </div>

                    <div>
                        <label for="templateSearch" class="block text-sm font-medium text-vl-ink mb-2">Filter Templates</label>
                        <input
                            type="text"
                            id="templateSearch"
                            placeholder="Search by runtime (Python, Node, C++, PHP)..."
                            class="vl-input"
                        >
                    </div>
                </div>
            </x-card>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="templatesGrid">
                @foreach($templates as $index => $template)
                    <label class="block cursor-pointer template-card">
                        <input
                            type="radio"
                            name="template_id"
                            value="{{ $template->id }}"
                            class="peer sr-only"
                            {{ old('template_id') ? (old('template_id') == $template->id ? 'checked' : '') : ($index === 0 ? 'checked' : '') }}
                            required
                        >

                        <div class="vl-panel h-full p-5 ring-2 ring-transparent peer-checked:ring-vl-accent peer-checked:bg-vl-accent/8 transition-[box-shadow,background-color] duration-150 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-base font-bold text-vl-ink template-title">{{ $template->name }}</h3>
                                <x-badge variant="info" class="template-language shrink-0">{{ strtoupper($template->language) }}</x-badge>
                            </div>
                            <p class="text-xs leading-relaxed text-vl-ink-secondary">
                                {{ $template->description ?? 'Pre-configured container environment with deterministic toolchain.' }}
                            </p>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('workspace.index') }}" class="vl-btn-secondary">Cancel</a>
                <button type="submit" class="vl-btn-primary !px-6 !py-2.5">Provision Workspace</button>
            </div>
        </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('templateSearch');
    const cards = document.querySelectorAll('.template-card');
    if (!searchInput) return;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        cards.forEach((card) => {
            const title = card.querySelector('.template-title')?.textContent.toLowerCase() || '';
            const lang = card.querySelector('.template-language')?.textContent.toLowerCase() || '';
            card.style.display = (title.includes(query) || lang.includes(query)) ? '' : 'none';
        });
    });
});
</script>
@endpush

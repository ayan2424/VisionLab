@extends('layouts.dashboard')
@section('title', 'Campus Announcements')
@section('page-title', 'Campus Announcements')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Campus Announcements</h2>
            <p class="text-sm text-vl-ink-secondary">Institutional broadcasts, maintenance windows, and academic updates.</p>
        </div>

        @if(Auth::user()->isAdmin())
            <a href="{{ route('announcements.create') }}" class="vl-btn-primary !py-2 !px-4 !text-xs sm:!text-sm shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Announcement
            </a>
        @endif
    </div>

    <div class="space-y-4">
        @forelse($announcements as $announcement)
            @php $isRead = $announcement->isReadBy(Auth::user()); @endphp
            <article class="vl-panel p-6 space-y-4 {{ $announcement->pinned ? 'ring-1 ring-vl-gate/30' : '' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="space-y-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-lg font-bold text-vl-ink">{{ $announcement->title }}</h3>
                            @if($announcement->pinned)
                                <x-badge variant="warning">Pinned</x-badge>
                            @endif
                            @if(!$isRead)
                                <x-badge variant="info" dot>Unread</x-badge>
                            @endif
                        </div>
                        <p class="text-xs text-vl-ink-muted">
                            Posted by <span class="font-medium text-vl-ink-secondary">{{ $announcement->author->name ?? 'Campus Administration' }}</span>
                            &middot; {{ $announcement->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if(!$isRead)
                            <form method="POST" action="{{ route('announcements.read', $announcement->id) }}">
                                @csrf
                                <button type="submit" class="vl-btn-secondary !py-1.5 !px-3 !text-xs">
                                    Mark Read
                                </button>
                            </form>
                        @else
                            <x-badge variant="neutral">Read</x-badge>
                        @endif

                        @if(Auth::user()->isAdmin())
                            <form action="{{ route('announcements.destroy_global', $announcement) }}" method="POST" onsubmit="event.preventDefault(); vcConfirm('Delete this global announcement?', () => this.submit())">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="vl-btn-danger !py-1.5 !px-3 !text-xs">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="text-sm leading-relaxed text-vl-ink-secondary whitespace-pre-wrap">{{ $announcement->body }}</div>
            </article>
        @empty
            <x-card :padding="true">
                <x-empty-state
                    title="No campus announcements yet"
                    description="Institutional notices posted by administrators will appear here."
                />
            </x-card>
        @endforelse
    </div>

    <div>{{ $announcements->links() }}</div>
</div>
@endsection

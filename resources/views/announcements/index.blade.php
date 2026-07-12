<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Global Announcements') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(Auth::user()->isAdmin())
            <div class="flex justify-end">
                <a href="{{ route('announcements.create') }}" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Announcement
                </a>
            </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-6">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @forelse($announcements as $announcement)
                <div class="vc-card p-6 relative {{ $announcement->pinned ? 'border-l-4 border-indigo-500' : '' }}">
                    @if($announcement->pinned)
                        <div class="absolute top-4 right-4 text-indigo-500" title="Pinned">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                            </svg>
                        </div>
                    @endif
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $announcement->title }}
                    </h3>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-2">
                        <span>By {{ $announcement->author->name }}</span>
                        <span>•</span>
                        <span>{{ $announcement->created_at->diffForHumans() }}</span>
                    </div>
                    
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($announcement->body)) !!}
                    </div>

                    @if(Auth::user()->isAdmin())
                    <div class="mt-6 flex justify-end">
                        <form action="{{ route('announcements.destroy_global', $announcement) }}" method="POST" onsubmit="event.preventDefault(); vcConfirm('Delete this announcement?', () => this.submit())">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                        </form>
                    </div>
                    @endif
                </div>
            @empty
                <div class="vc-card p-12 text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                    <p class="text-lg">No announcements available at this time.</p>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>

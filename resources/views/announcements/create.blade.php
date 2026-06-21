<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Post Global Announcement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="vc-card p-8">
                <form action="{{ route('announcements.store_global') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}" 
                               class="mt-1 vc-input w-full" placeholder="e.g. Server Maintenance Notice">
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Body (Markdown/Text)</label>
                        <textarea name="body" id="body" rows="6" required 
                                  class="mt-1 vc-input w-full" placeholder="Write the announcement details here...">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="pinned" id="pinned" value="1" {{ old('pinned') ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="pinned" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Pin this announcement (Keep it at the top)
                        </label>
                    </div>

                    <div class="flex justify-end pt-4">
                        <a href="{{ route('announcements.index') }}" class="btn-ghost mr-4">Cancel</a>
                        <button type="submit" class="btn-primary">Post Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>

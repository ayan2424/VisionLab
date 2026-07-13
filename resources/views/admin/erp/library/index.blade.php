@extends('layouts.admin')

@section('title', 'Library Management')
@section('page-title', 'Library Management')

@section('content')
<div class="vc-card overflow-hidden p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-bold mb-4" style="color:var(--vc-text);">All Books</h1>
                    <a href="{{ route('admin.library.create') }}" class="btn-primary py-2 px-4 text-sm">Add Book</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">ISBN</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Title</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Author</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Total Copies</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Available</th>
                            </tr>
                        </thead>
                        <tbody >
                            @forelse ($books as $book)
                                <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors border-b" style="border-color:var(--vc-border);">
                                    <td class="px-5 py-4 whitespace-nowrap text-sm font-medium ">{{ $book->isbn ?? 'N/A' }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">{{ $book->title }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">{{ $book->author }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">{{ $book->total_copies }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $book->available_copies > 0 ? 'green' : 'red' }}-100 text-{{ $book->available_copies > 0 ? 'green' : 'red' }}-800">
                                            {{ $book->available_copies }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No books found in the library.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
@endsection

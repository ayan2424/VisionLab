<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminLibraryController extends Controller
{
    public function index()
    {
        $books = LibraryBook::latest()->get();
        $rows = $books->map(fn ($b) => [
            'id' => $b->id, 'title' => $b->title, 'author' => $b->author,
            'isbn' => $b->isbn ?? '—', 'stock' => "{$b->available_copies} / {$b->total_copies}",
        ])->values()->all();
        return Inertia::render('Admin/Erp/ResourceIndex', [
            'title' => 'Campus Library Catalog',
            'subtitle' => 'Track department textbooks, reference manuals, and shelf availability.',
            'basePath' => '/admin/library',
            'createLabel' => 'Add Book',
            'columns' => [
                ['key' => 'title', 'label' => 'Title'],
                ['key' => 'author', 'label' => 'Author'],
                ['key' => 'isbn', 'label' => 'ISBN', 'mono' => true],
                ['key' => 'stock', 'label' => 'Available / Total', 'mono' => true],
            ],
            'rows' => $rows,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Add Library Book',
            'subtitle' => 'Catalog a new textbook or engineering reference.',
            'submitUrl' => '/admin/library',
            'method' => 'post',
            'cancelUrl' => '/admin/library',
            'fields' => [
                ['name' => 'title', 'label' => 'Book Title', 'type' => 'text', 'required' => true],
                ['name' => 'author', 'label' => 'Author', 'type' => 'text', 'required' => true],
                ['name' => 'isbn', 'label' => 'ISBN', 'type' => 'text'],
                ['name' => 'total_copies', 'label' => 'Total Copies', 'type' => 'number', 'required' => true],
                ['name' => 'available_copies', 'label' => 'Available Copies', 'type' => 'number', 'required' => true],
            ],
            'initialValues' => ['title' => '', 'author' => '', 'isbn' => '', 'total_copies' => 1, 'available_copies' => 1],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0',
        ]);

        LibraryBook::create($validated);
        return redirect()->route('admin.library.index')->with('success', 'Book added successfully.');
    }

    public function edit(LibraryBook $library)
    {
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Edit Library Book',
            'subtitle' => 'Update book metadata and shelf counts.',
            'submitUrl' => "/admin/library/{$library->id}",
            'method' => 'put',
            'cancelUrl' => '/admin/library',
            'fields' => [
                ['name' => 'title', 'label' => 'Book Title', 'type' => 'text', 'required' => true],
                ['name' => 'author', 'label' => 'Author', 'type' => 'text', 'required' => true],
                ['name' => 'isbn', 'label' => 'ISBN', 'type' => 'text'],
                ['name' => 'total_copies', 'label' => 'Total Copies', 'type' => 'number', 'required' => true],
                ['name' => 'available_copies', 'label' => 'Available Copies', 'type' => 'number', 'required' => true],
            ],
            'initialValues' => [
                'title' => $library->title, 'author' => $library->author, 'isbn' => $library->isbn ?? '',
                'total_copies' => $library->total_copies, 'available_copies' => $library->available_copies,
            ],
        ]);
    }

    public function update(Request $request, LibraryBook $library)
    {
        $validated = $request->validate([
            'isbn' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0',
        ]);

        $library->update($validated);
        return redirect()->route('admin.library.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(LibraryBook $library)
    {
        $library->delete();
        return redirect()->route('admin.library.index')->with('success', 'Book deleted successfully.');
    }
}

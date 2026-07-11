<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use Illuminate\Http\Request;

class AdminLibraryController extends Controller
{
    public function index()
    {
        $books = LibraryBook::latest()->get();
        return view('admin.erp.library.index', compact('books'));
    }

    public function create()
    {
        return view('admin.erp.library.create');
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
        return view('admin.erp.library.edit', compact('library'));
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

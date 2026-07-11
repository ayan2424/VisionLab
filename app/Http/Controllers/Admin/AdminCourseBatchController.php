<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseBatch;
use App\Models\Department;
use App\Models\Course;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Http\Request;

class AdminCourseBatchController extends Controller
{
    public function index()
    {
        $batches = CourseBatch::with(['department', 'course', 'instructor'])->latest()->paginate(20);
        return view('admin.erp.batches.index', compact('batches'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $courses = Course::all(); // Assuming all courses can be batched
        $semesters = Semester::where('is_active', true)->get();
        $instructors = User::role('instructor')->get();
        return view('admin.erp.batches.create', compact('departments', 'courses', 'semesters', 'instructors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'instructor_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:150',
            'timing' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'max_capacity' => 'nullable|integer|min:1',
            'room_number' => 'nullable|string|max:50',
        ]);
        $validated['is_active'] = $request->has('is_active');

        CourseBatch::create($validated);
        return redirect()->route('admin.batches.index')->with('success', 'Batch created successfully.');
    }

    public function edit(CourseBatch $batch)
    {
        $departments = Department::where('is_active', true)->get();
        $courses = Course::all();
        $semesters = Semester::where('is_active', true)->get();
        $instructors = User::role('instructor')->get();
        return view('admin.erp.batches.edit', compact('batch', 'departments', 'courses', 'semesters', 'instructors'));
    }

    public function update(Request $request, CourseBatch $batch)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'instructor_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:150',
            'timing' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'max_capacity' => 'nullable|integer|min:1',
            'room_number' => 'nullable|string|max:50',
        ]);
        $validated['is_active'] = $request->has('is_active');

        $batch->update($validated);
        return redirect()->route('admin.batches.index')->with('success', 'Batch updated successfully.');
    }

    public function destroy(CourseBatch $batch)
    {
        if ($batch->enrollments()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete batch because it has enrolled students.']);
        }
        
        $batch->delete();
        return redirect()->route('admin.batches.index')->with('success', 'Batch deleted successfully.');
    }
}

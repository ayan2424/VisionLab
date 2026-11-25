<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseBatch;
use App\Models\Department;
use App\Models\Course;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCourseBatchController extends Controller
{
    public function index()
    {
        $batches = CourseBatch::with(['department', 'course', 'instructor'])->latest()->paginate(20);
        $rows = collect($batches->items())->map(fn ($b) => [
            'id' => $b->id, 'title' => $b->title,
            'course_title' => $b->course?->title ?? '—',
            'dept_name' => $b->department?->name ?? '—',
            'timing' => $b->timing ?? '—',
            'is_active' => (bool) $b->is_active,
        ])->values()->all();
        return Inertia::render('Admin/Erp/ResourceIndex', [
            'title' => 'Course Batches',
            'subtitle' => 'Schedule student cohorts, rooms, and assigned instructors.',
            'basePath' => '/admin/batches',
            'createLabel' => 'Create Batch',
            'columns' => [
                ['key' => 'title', 'label' => 'Batch Title'],
                ['key' => 'course_title', 'label' => 'Course'],
                ['key' => 'dept_name', 'label' => 'Department'],
                ['key' => 'timing', 'label' => 'Schedule', 'mono' => true],
                ['key' => 'is_active', 'label' => 'Status', 'badge' => true],
            ],
            'rows' => $rows,
            'links' => $batches->linkCollection()->toArray(),
        ]);
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $courses = Course::all(); // Assuming all courses can be batched
        $semesters = Semester::where('is_active', true)->get();
        $instructors = User::role('instructor')->get();
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Create Course Batch',
            'subtitle' => 'Schedule a new department course cohort.',
            'submitUrl' => '/admin/batches',
            'method' => 'post',
            'cancelUrl' => '/admin/batches',
            'fields' => [
                ['name' => 'title', 'label' => 'Batch Title', 'type' => 'text', 'required' => true],
                ['name' => 'department_id', 'label' => 'Department', 'type' => 'select', 'required' => true,
                 'options' => $departments->map(fn ($d) => ['value' => $d->id, 'label' => $d->name])->values()->all()],
                ['name' => 'course_id', 'label' => 'Course', 'type' => 'select', 'required' => true,
                 'options' => $courses->map(fn ($c) => ['value' => $c->id, 'label' => $c->title])->values()->all()],
                ['name' => 'semester_id', 'label' => 'Semester', 'type' => 'select', 'required' => true,
                 'options' => $semesters->map(fn ($s) => ['value' => $s->id, 'label' => $s->name])->values()->all()],
                ['name' => 'instructor_id', 'label' => 'Instructor', 'type' => 'select',
                 'options' => $instructors->map(fn ($i) => ['value' => $i->id, 'label' => $i->name])->values()->all()],
                ['name' => 'timing', 'label' => 'Timing', 'type' => 'text', 'placeholder' => 'Mon/Wed 10:00 AM'],
                ['name' => 'room_number', 'label' => 'Lab / Room Number', 'type' => 'text'],
                ['name' => 'start_date', 'label' => 'Start Date', 'type' => 'date'],
                ['name' => 'max_capacity', 'label' => 'Max Capacity', 'type' => 'number'],
                ['name' => 'is_active', 'label' => 'Batch Active', 'type' => 'checkbox'],
            ],
            'initialValues' => [
                'title' => '', 'department_id' => '', 'course_id' => '', 'semester_id' => '',
                'instructor_id' => '', 'timing' => '', 'room_number' => '', 'start_date' => '',
                'max_capacity' => 40, 'is_active' => true,
            ],
        ]);
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
        $validated['is_active'] = $request->boolean('is_active');

        CourseBatch::create($validated);
        return redirect()->route('admin.batches.index')->with('success', 'Batch created successfully.');
    }

    public function edit(CourseBatch $batch)
    {
        $departments = Department::where('is_active', true)->get();
        $courses = Course::all();
        $semesters = Semester::where('is_active', true)->get();
        $instructors = User::role('instructor')->get();
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Edit Course Batch',
            'subtitle' => 'Update batch schedule and assignment.',
            'submitUrl' => "/admin/batches/{$batch->id}",
            'method' => 'put',
            'cancelUrl' => '/admin/batches',
            'fields' => [
                ['name' => 'title', 'label' => 'Batch Title', 'type' => 'text', 'required' => true],
                ['name' => 'department_id', 'label' => 'Department', 'type' => 'select', 'required' => true,
                 'options' => $departments->map(fn ($d) => ['value' => $d->id, 'label' => $d->name])->values()->all()],
                ['name' => 'course_id', 'label' => 'Course', 'type' => 'select', 'required' => true,
                 'options' => $courses->map(fn ($c) => ['value' => $c->id, 'label' => $c->title])->values()->all()],
                ['name' => 'semester_id', 'label' => 'Semester', 'type' => 'select', 'required' => true,
                 'options' => $semesters->map(fn ($s) => ['value' => $s->id, 'label' => $s->name])->values()->all()],
                ['name' => 'instructor_id', 'label' => 'Instructor', 'type' => 'select',
                 'options' => $instructors->map(fn ($i) => ['value' => $i->id, 'label' => $i->name])->values()->all()],
                ['name' => 'timing', 'label' => 'Timing', 'type' => 'text'],
                ['name' => 'room_number', 'label' => 'Lab / Room Number', 'type' => 'text'],
                ['name' => 'start_date', 'label' => 'Start Date', 'type' => 'date'],
                ['name' => 'max_capacity', 'label' => 'Max Capacity', 'type' => 'number'],
                ['name' => 'is_active', 'label' => 'Batch Active', 'type' => 'checkbox'],
            ],
            'initialValues' => [
                'title' => $batch->title, 'department_id' => $batch->department_id,
                'course_id' => $batch->course_id, 'semester_id' => $batch->semester_id,
                'instructor_id' => $batch->instructor_id ?? '', 'timing' => $batch->timing ?? '',
                'room_number' => $batch->room_number ?? '',
                'start_date' => $batch->start_date ? substr((string) $batch->start_date, 0, 10) : '',
                'max_capacity' => $batch->max_capacity ?? 40, 'is_active' => (bool) $batch->is_active,
            ],
        ]);
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
        $validated['is_active'] = $request->boolean('is_active');

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

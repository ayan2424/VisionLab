<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $courses = \App\Models\Course::with(['batches' => function($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->get();
        return view('admin.users.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,instructor,student',
            'status'   => 'required|in:active,suspended',
            'course_id'       => 'nullable|exists:courses,id',
            'course_batch_id' => 'nullable|exists:course_batches,id',
        ]);

        $studentId = null;
        if ($validated['role'] === 'student') {
            do {
                $studentId = 'STU-' . strtoupper(\Illuminate\Support\Str::random(6));
            } while (User::where('student_id', $studentId)->exists());
        }

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role'       => $validated['role'],
            'status'     => $validated['status'],
            'student_id' => $studentId,
        ]);

        if ($validated['role'] === 'student' && !empty($validated['course_id'])) {
            $batchTiming = null;
            $startDate = null;

            if (!empty($validated['course_batch_id'])) {
                $batch = \App\Models\CourseBatch::find($validated['course_batch_id']);
                if ($batch) {
                    $batchTiming = $batch->timing;
                    $startDate = $batch->start_date;
                }
            }

            \App\Models\Enrollment::create([
                'course_id'    => $validated['course_id'],
                'student_id'   => $user->id,
                'status'       => 'active',
                'enrolled_at'  => now(),
                'batch_timing' => $batchTiming,
                'start_date'   => $startDate,
            ]);
        }

        return redirect()->route('admin.users.index')
                         ->with('success', "User {$user->name} created successfully." . ($studentId ? " Assigned ID: {$studentId}" : ""));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required|in:admin,instructor,student',
            'status'     => 'required|in:active,suspended',
            'student_id' => 'nullable|string|unique:users,student_id,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
                         ->with('success', "User {$user->name} updated.");
    }

    public function suspend(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot suspend yourself.']);
        }
        $user->update(['status' => 'suspended']);
        return back()->with('success', "{$user->name} has been suspended.");
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', "{$user->name} account activated.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete yourself.']);
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function impersonate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot impersonate yourself.']);
        }

        session(['impersonator_id' => auth()->id()]);
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', "You are now impersonating {$user->name}.");
    }

    public function stopImpersonating()
    {
        if (session()->has('impersonator_id')) {
            $adminId = session('impersonator_id');
            session()->forget('impersonator_id');
            
            $admin = User::find($adminId);
            if ($admin) {
                auth()->login($admin);
                return redirect()->route('admin.users.index')->with('success', 'Welcome back. Impersonation stopped.');
            }
        }

        return redirect()->route('dashboard');
    }

    public function exportGdpr(User $user)
    {
        $data = [
            'profile' => $user->only(['id', 'name', 'email', 'role', 'status', 'created_at']),
            'courses' => $user->enrollments()->with('course')->get()->map->course->only(['id', 'name', 'slug']),
            'workspaces' => $user->workspaces()->select('id', 'name', 'slug', 'created_at')->get(),
            'submissions' => \App\Models\Submission::where('student_id', $user->id)->get(),
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="gdpr_export_' . $user->id . '.json"'
        ]);
    }
}

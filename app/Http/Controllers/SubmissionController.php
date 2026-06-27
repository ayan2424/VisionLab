<?php

namespace App\Http\Controllers;

use App\Events\SubmissionGraded;
use App\Http\Requests\GradeSubmissionRequest;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use App\Http\Requests\SubmitAssignmentRequest;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function start(Assignment $assignment)
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403, 'Only students can start assignments.');
        }

        $submission = Submission::firstOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $user->id],
            ['status' => 'in_progress', 'code_snapshot' => $assignment->starter_code ?? '']
        );

        if ($submission->status === 'not_started') {
            $submission->update(['status' => 'in_progress']);
        }

        // Provision a dedicated workspace for this assignment
        $workspace = \App\Models\Workspace::firstOrCreate(
            [
                'student_id' => $user->id,
                'assignment_id' => $assignment->id,
                'course_id' => $assignment->course_id,
            ],
            [
                'name' => 'assignment-' . $assignment->id . '-' . $user->id,
                'language' => $assignment->starter_language ?? 'python',
                'token' => \Illuminate\Support\Str::random(64),
                'status' => 'pending',
                'type' => 'governed', // Governed by teacher
            ]
        );

        // We trigger the async or sync start so it's ready
        app(\App\Services\CodeServerManager::class)->startWorkspace($workspace);

        return redirect()->route('submissions.ide', $assignment->id);
    }

    public function submit(SubmitAssignmentRequest $request, Assignment $assignment)
    {
        $user = Auth::user();
        if (!$user->isStudent()) abort(403);

        $validated = $request->validated();

        $submission = Submission::where('assignment_id', $assignment->id)
                                ->where('student_id', $user->id)
                                ->firstOrFail();

        $isLate = $assignment->due_date && now()->gt($assignment->due_date);

        $submission->update([
            'code_snapshot' => $request->code_snapshot ?? $submission->code_snapshot,
            'status'        => $isLate ? 'late' : 'submitted',
            'submitted_at'  => now(),
        ]);

        return redirect()->route('assignments.show', $assignment)
                         ->with('success', $isLate ? 'Submitted (late).' : 'Assignment submitted successfully!');
    }

    public function saveSnapshot(Request $request, Assignment $assignment)
    {
        $user = Auth::user();
        if (!$user->isStudent()) abort(403);

        $request->validate(['code_snapshot' => 'nullable|string|max:65535']);

        Submission::where('assignment_id', $assignment->id)
                  ->where('student_id', $user->id)
                  ->update(['code_snapshot' => $request->code_snapshot ?? '']);

        return response()->json(['success' => true]);
    }

    public function ide(Assignment $assignment)
    {
        $user = Auth::user();
        if (!$user->isStudent()) abort(403, 'Students only.');

        $submission = Submission::where('assignment_id', $assignment->id)
                                ->where('student_id', $user->id)
                                ->firstOrFail();

        $course = $assignment->course;

        // Fetch the dedicated workspace
        $workspace = \App\Models\Workspace::where('assignment_id', $assignment->id)
            ->where('student_id', $user->id)
            ->firstOrFail();

        // Start workspace container
        $serverInfo = app(\App\Services\CodeServerManager::class)->startWorkspace($workspace);

        if (isset($serverInfo['port'])) {
            $cookiePath = str_starts_with($serverInfo['url'], '/ide/') 
                ? '/ide/' . $serverInfo['port'] 
                : '/';
            cookie()->queue(
                'key',
                $serverInfo['token'],
                525600,
                $cookiePath
            );
        }

        return view('workspace', [
            'user'            => $user,
            'workspace'       => $workspace->fresh(),
            'workspaceName'   => $assignment->title,
            'roomSlug'        => 'assign-' . $assignment->id . '-' . $user->id,
            'isCollaborative' => false,
            'reverbConfig'    => \App\Http\Controllers\WorkspaceController::reverbConfig(),
            'vscodeUrl'       => $serverInfo['url'],
            'assignment'      => $assignment,
            'submission'      => $submission,
        ]);
    }

    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);

        $user = Auth::user();

        $assignment = $submission->assignment()->with('course')->firstOrFail();
        $course     = $assignment->course;

        $canGrade = ($user->isAdmin() || ($user->isInstructor() && $course->instructor_id === $user->id))
                    && in_array($submission->status, ['submitted', 'late', 'graded']);

        // Previous / next submission for the same assignment (for instructor navigation)
        $siblings        = Submission::where('assignment_id', $assignment->id)
                                     ->whereIn('status', ['submitted', 'late', 'graded'])
                                     ->orderBy('submitted_at')
                                     ->pluck('id');

        $currentIndex    = $siblings->search($submission->id);
        $prevSubmission  = $currentIndex > 0 ? Submission::with('student')->find($siblings[$currentIndex - 1]) : null;
        $nextSubmission  = ($currentIndex !== false && $currentIndex < $siblings->count() - 1)
                            ? Submission::with('student')->find($siblings[$currentIndex + 1])
                            : null;

        return view('submissions.show', compact(
            'submission', 'assignment', 'course',
            'canGrade', 'prevSubmission', 'nextSubmission'
        ));
    }

    public function grade(GradeSubmissionRequest $request, Submission $submission)
    {
        $user      = Auth::user();
        $validated = $request->validated();

        $submission->update([
            'grade'     => $validated['grade'],
            'feedback'  => $validated['feedback'] ?? null,
            'status'    => 'graded',
            'graded_by' => $user->id,
        ]);

        // Broadcast real-time notification to student
        try {
            broadcast(new SubmissionGraded($submission->fresh(['assignment', 'grader'])));
        } catch (\Throwable $e) {
            // Non-fatal — Reverb may not be running in all environments
        }

        // Redirect: if came from submission show page, go back there; otherwise back
        if ($request->headers->get('referer') && str_contains($request->headers->get('referer'), '/submissions/')) {
            return redirect()->route('submissions.show', $submission)
                             ->with('success', 'Grade saved successfully!');
        }

        return back()->with('success', 'Submission graded successfully!');
    }

    public function queue(Request $request)
    {
        $user = Auth::user();
        if (! ($user->isInstructor() || $user->isAdmin())) {
            abort(403);
        }

        $courseFilter = $request->query('course');
        $statusFilter = $request->query('status', 'pending');

        // Build base query
        $query = Submission::with(['student', 'assignment.course'])
            ->whereHas('assignment.course', function ($q) use ($user) {
                if ($user->isInstructor()) {
                    $q->where('instructor_id', $user->id);
                }
            });

        if ($courseFilter) {
            $query->whereHas('assignment', fn ($q) => $q->where('course_id', $courseFilter));
        }

        if ($statusFilter === 'pending') {
            $query->whereIn('status', ['submitted', 'late']);
        } elseif ($statusFilter === 'graded') {
            $query->where('status', 'graded');
        } else {
            $query->whereIn('status', ['submitted', 'late', 'graded']);
        }

        $submissions = $query->latest('submitted_at')->paginate(30);

        // Group by assignment_id for display
        $grouped = [];
        foreach ($submissions as $sub) {
            $aid = $sub->assignment_id;
            if (! isset($grouped[$aid])) {
                $grouped[$aid] = ['assignment' => $sub->assignment, 'submissions' => collect()];
            }
            $grouped[$aid]['submissions']->push($sub);
        }

        // Courses for filter tabs
        $courses = \App\Models\Course::when(
            $user->isInstructor(),
            fn ($q) => $q->where('instructor_id', $user->id)
        )->orderBy('title')->get();

        // Stats
        $statsBase = Submission::whereHas('assignment.course', function ($q) use ($user) {
            if ($user->isInstructor()) {
                $q->where('instructor_id', $user->id);
            }
        });

        $stats = [
            'pending' => (clone $statsBase)->whereIn('status', ['submitted', 'late'])->count(),
            'late'    => (clone $statsBase)->where('status', 'late')->count(),
            'graded'  => (clone $statsBase)->where('status', 'graded')->count(),
            'total'   => (clone $statsBase)->whereIn('status', ['submitted', 'late', 'graded'])->count(),
        ];

        $pendingCount = $stats['pending'];

        // Per-course pending counts for filter tabs
        $perCoursePending = Submission::selectRaw('assignments.course_id, COUNT(*) as cnt')
            ->join('assignments', 'assignments.id', '=', 'submissions.assignment_id')
            ->join('courses', 'courses.id', '=', 'assignments.course_id')
            ->when($user->isInstructor(), fn ($q) => $q->where('courses.instructor_id', $user->id))
            ->whereIn('submissions.status', ['submitted', 'late'])
            ->groupBy('assignments.course_id')
            ->pluck('cnt', 'course_id')
            ->toArray();

        return view('submissions.queue', compact(
            'submissions', 'grouped', 'courses',
            'courseFilter', 'statusFilter', 'stats', 'pendingCount', 'perCoursePending'
        ));
    }
}

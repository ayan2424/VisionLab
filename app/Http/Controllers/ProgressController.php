<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            abort(403, 'Students only.');
        }

        // Load all courses the student is enrolled in
        $courses = Course::whereHas('enrollments', function ($q) use ($user) {
            $q->where('student_id', $user->id)->where('status', 'active');
        })->with(['instructor', 'assignments'])->latest()->get();

        // Load all their submissions keyed by assignment_id
        $submissionsMap = Submission::where('student_id', $user->id)
            ->get()
            ->keyBy('assignment_id');

        $courseProgress = [];
        $totalPoints = 0;
        $earnedPoints = 0;
        $gradedCount = 0;
        $totalCount = 0;
        $inProgressCnt = 0;
        $pendingCnt = 0;

        foreach ($courses as $course) {
            $assignments = $course->assignments;
            $courseGraded = 0;
            $coursePts = 0;
            $courseEarned = 0;
            $rows = [];

            foreach ($assignments as $assignment) {
                $sub = $submissionsMap[$assignment->id] ?? null;
                $pct = null;
                $totalCount++;

                if ($sub) {
                    if ($sub->status === 'graded') {
                        $gradedCount++;
                        $courseGraded++;
                        if ($assignment->max_points > 0) {
                            $pct = round($sub->grade / $assignment->max_points * 100);
                            $coursePts += $assignment->max_points;
                            $courseEarned += $sub->grade;
                            $totalPoints += $assignment->max_points;
                            $earnedPoints += $sub->grade;
                        }
                    } elseif ($sub->status === 'in_progress') {
                        $inProgressCnt++;
                    } elseif (in_array($sub->status, ['submitted', 'late'])) {
                        $pendingCnt++;
                    }
                }

                $rows[] = [
                    'assignment' => $assignment,
                    'submission' => $sub,
                    'pct' => $pct,
                ];
            }

            $courseProgress[] = [
                'course' => $course,
                'assignments' => $rows,
                'graded_count' => $courseGraded,
                'total_count' => $assignments->count(),
                'completion_pct' => $assignments->count() > 0
                                    ? round($courseGraded / $assignments->count() * 100)
                                    : 0,
                'average' => $coursePts > 0 ? round($courseEarned / $coursePts * 100) : null,
                'graded_pct' => $coursePts > 0 ? round($courseEarned / $coursePts * 100) : 0,
            ];
        }

        $overallPct = $totalPoints > 0 ? round($earnedPoints / $totalPoints * 100) : null;

        $gradeLetter = match (true) {
            $overallPct === null => '—',
            $overallPct >= 90 => 'A',
            $overallPct >= 80 => 'B',
            $overallPct >= 70 => 'C',
            $overallPct >= 60 => 'D',
            default => 'F',
        };

        $stats = [
            'courses' => $courses->count(),
            'graded' => $gradedCount,
            'in_progress' => $inProgressCnt,
            'pending' => $pendingCnt,
        ];

        return view('progress.index', compact(
            'courseProgress', 'overallPct', 'gradeLetter', 'stats'
        ));
    }
}

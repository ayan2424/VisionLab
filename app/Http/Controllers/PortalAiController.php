<?php

namespace App\Http\Controllers;

use App\Models\AiPendingPatch;
use App\Models\Assignment;
use App\Models\AuditLog;
use App\Models\Campus;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Department;
use App\Models\Submission;
use App\Models\User;
use App\Models\Workspace;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalAiController extends Controller
{
    public function __construct(private readonly AiService $aiService) {}

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message'           => 'required|string|max:2000',
            'page_url'          => 'nullable|string|max:255',
            'page_title'        => 'nullable|string|max:120',
            'history'           => 'nullable|array|max:12',
            'history.*.role'    => 'required_with:history|in:user,assistant',
            'history.*.content' => 'required_with:history|string|max:2000',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $message = trim($validated['message']);
        $pageUrl = $validated['page_url'] ?? '/dashboard';
        $pageTitle = $validated['page_title'] ?? 'Portal';

        $context = $this->buildRoleContext($user);
        $actions = $this->suggestActions($user, $message);

        $apiKey = config('visionlab.ai.api_key') ?: env('ANTHROPIC_API_KEY');
        if (!empty($apiKey)) {
            $systemPrompt = $this->buildSystemPrompt($user, $pageTitle, $pageUrl, $context);
            $historyLines = collect($validated['history'] ?? [])
                ->map(fn (array $h) => strtoupper($h['role']) . ': ' . $h['content'])
                ->implode("\n");
            $prompt = $historyLines !== ''
                ? "Conversation so far:\n{$historyLines}\n\nLatest user message:\n{$message}"
                : $message;

            $reply = $this->aiService->getDirectCompletion($systemPrompt, $prompt);

            return response()->json([
                'reply'         => $reply,
                'mode'          => 'live_llm',
                'actions'       => $actions,
                'context_badge' => ucfirst($user->role) . ' Copilot · Live AI',
            ]);
        }

        return response()->json([
            'reply'         => $this->buildContextualFallback($user, $message, $context, $pageTitle),
            'mode'          => 'campus_telemetry',
            'actions'       => $actions,
            'context_badge' => ucfirst($user->role) . ' Copilot · Live Campus Telemetry',
        ]);
    }

    private function buildRoleContext(User $user): array
    {
        if ($user->isAdmin()) {
            $recentAudits = AuditLog::with('actor:id,name')
                ->latest()
                ->limit(4)
                ->get()
                ->map(fn ($log) => "{$log->action} ({$log->resource_type}) by " . ($log->actor?->name ?? 'System'))
                ->values()
                ->all();

            return [
                'role'               => 'admin',
                'total_users'        => User::count(),
                'students'           => User::where('role', 'student')->count(),
                'instructors'        => User::where('role', 'instructor')->count(),
                'suspended_users'    => User::where('status', 'suspended')->count(),
                'active_workspaces'  => Workspace::where('status', 'running')->count(),
                'total_workspaces'   => Workspace::count(),
                'active_courses'     => Course::where('is_active', true)->count(),
                'pending_grading'    => Submission::whereIn('status', ['submitted', 'late'])->count(),
                'pending_ai_patches' => AiPendingPatch::where('status', 'pending')->count(),
                'campuses'           => Campus::count(),
                'departments'        => Department::count(),
                'batches'            => CourseBatch::count(),
                'recent_audits'      => $recentAudits,
            ];
        }

        if ($user->isInstructor()) {
            $courses = Course::where('instructor_id', $user->id)
                ->withCount('students')
                ->get(['id', 'title', 'slug', 'enrollment_code']);
            $courseIds = $courses->pluck('id');
            $assignmentIds = Assignment::whereIn('course_id', $courseIds)->pluck('id');

            return [
                'role'              => 'instructor',
                'courses_count'     => $courses->count(),
                'courses'           => $courses->take(5)->map(fn ($c) => "{$c->title} ({$c->students_count} students)")->all(),
                'pending_grading'   => Submission::whereIn('assignment_id', $assignmentIds)->whereIn('status', ['submitted', 'late'])->count(),
                'late_submissions'  => Submission::whereIn('assignment_id', $assignmentIds)->where('status', 'late')->count(),
                'active_workspaces' => Workspace::whereIn('course_id', $courseIds)->where('status', 'running')->count(),
                'pending_patches'   => AiPendingPatch::whereHas('workspace', fn ($q) => $q->whereIn('course_id', $courseIds))
                    ->where('status', 'pending')
                    ->count(),
            ];
        }

        $courses = Course::whereHas('enrollments', function ($q) use ($user) {
            $q->where('student_id', $user->id)->where('status', 'active');
        })->with('instructor:id,name')->get(['id', 'title', 'slug']);
        $courseIds = $courses->pluck('id');
        $upcoming = Assignment::whereIn('course_id', $courseIds)
            ->where(fn ($q) => $q->whereNull('due_date')->orWhere('due_date', '>=', now()))
            ->orderBy('due_date')
            ->limit(4)
            ->get(['id', 'title', 'due_date']);

        $workspaces = Workspace::where('student_id', $user->id)->latest('updated_at')->limit(5)->get(['id', 'name', 'slug', 'language', 'status']);

        return [
            'role'                 => 'student',
            'level'                => $user->level ?? 1,
            'rank_title'           => $user->rank_title ?? 'Novice',
            'xp'                   => $user->xp ?? 0,
            'courses_count'        => $courses->count(),
            'courses'              => $courses->pluck('title')->take(5)->all(),
            'upcoming_assignments' => $upcoming->map(fn ($a) => $a->title . ($a->due_date ? ' (due ' . $a->due_date->format('M d') . ')' : ' (open)'))->all(),
            'running_workspaces'   => $workspaces->where('status', 'running')->pluck('name')->values()->all(),
            'total_workspaces'     => $workspaces->count(),
            'pending_patches'      => AiPendingPatch::whereHas('workspace', fn ($q) => $q->where('student_id', $user->id))
                ->where('status', 'pending')
                ->count(),
        ];
    }

    private function buildSystemPrompt(User $user, string $pageTitle, string $pageUrl, array $context): string
    {
        $json = json_encode($context, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are Vision AI, the official built-in portal copilot for VisionLab (a sovereign university cloud coding laboratory and academic ERP).
Current user: {$user->name} (Role: {$user->role}).
Current page: {$pageTitle} ({$pageUrl}).

Live scoped database snapshot for this user:
{$json}

Strict rules:
1. Be concise, practical, and directly reference the user's real live numbers above.
2. Academic integrity: For students, explain concepts, guide debugging, and help interpret compiler errors, but never write complete copy-paste assignment solutions or claim to bypass VisionLab's HITL ai_pending_patches review gate.
3. For instructors, assist with grading queue triage, VisionGuard authorship review, rubric drafting, and course management.
4. For admins, assist with container fleet health, account governance, quotas, VSIX security, and ERP operations.
5. Keep replies crisp (3-6 sentences or clean bullet points) with zero fluff.
PROMPT;
    }

    private function buildContextualFallback(User $user, string $message, array $ctx, string $pageTitle): string
    {
        $q = mb_strtolower($message);

        if ($user->isAdmin()) {
            if (str_contains($q, 'audit') || str_contains($q, 'log') || str_contains($q, 'security')) {
                $audits = empty($ctx['recent_audits'])
                    ? 'No recent audit events recorded yet.'
                    : implode("\n- ", $ctx['recent_audits']);

                return "Governance & Security Snapshot:\n- {$ctx['suspended_users']} suspended account(s), {$ctx['pending_ai_patches']} pending AI patch(es) awaiting review.\n- Direct agent writes remain blocked (HTTP 403) and all VSIX artifacts require SHA-256 verification.\n\nRecent audit entries:\n- {$audits}";
            }

            if (str_contains($q, 'erp') || str_contains($q, 'campus') || str_contains($q, 'department') || str_contains($q, 'batch')) {
                return "Institute ERP Summary:\n- {$ctx['campuses']} campus(es), {$ctx['departments']} department(s), and {$ctx['batches']} active course batch(es).\n- {$ctx['total_users']} total institutional accounts ({$ctx['students']} students, {$ctx['instructors']} faculty).\n\nUse the quick links below to manage campuses, departments, batches, or fee challans.";
            }

            if (str_contains($q, 'workspace') || str_contains($q, 'container') || str_contains($q, 'health') || str_contains($q, 'cluster')) {
                return "Cluster & Container Telemetry:\n- {$ctx['active_workspaces']} running container(s) out of {$ctx['total_workspaces']} total provisioned workspaces.\n- {$ctx['pending_ai_patches']} staged AI patch(es) waiting for student approval.\n- You can inspect live database, Redis, and storage probes on Cluster Telemetry or stop runaway seats in Workspaces.";
            }

            return "Admin Command Summary (from {$pageTitle}):\n- Accounts: {$ctx['total_users']} ({$ctx['students']} students, {$ctx['instructors']} faculty, {$ctx['suspended_users']} suspended)\n- Containers: {$ctx['active_workspaces']} running / {$ctx['total_workspaces']} total\n- Courses & Grading: {$ctx['active_courses']} active course(s), {$ctx['pending_grading']} submission(s) awaiting grading\n- Governance: {$ctx['pending_ai_patches']} AI patch(es) in review queue · {$ctx['campuses']} campus(es)\n\nAsk me about cluster telemetry, ERP records, audit trails, or container governance.";
        }

        if ($user->isInstructor()) {
            if (str_contains($q, 'grade') || str_contains($q, 'queue') || str_contains($q, 'submission') || str_contains($q, 'late')) {
                return "Grading Queue Priority:\n- You have {$ctx['pending_grading']} submission(s) waiting for grading ({$ctx['late_submissions']} marked late).\n- {$ctx['pending_patches']} staged AI patch(es) exist across your course workspaces.\n- Tip: Open the Grading Queue to review VisionGuard keystroke vs. approved AI authorship telemetry directly beside student code.";
            }

            if (str_contains($q, 'course') || str_contains($q, 'student') || str_contains($q, 'roster')) {
                $list = empty($ctx['courses']) ? 'No active courses created yet.' : implode("\n- ", $ctx['courses']);

                return "Your Active Courses ({$ctx['courses_count']}):\n- {$list}\n\nYou currently have {$ctx['active_workspaces']} live student container(s) running across your sections.";
            }

            return "Faculty Studio Briefing (from {$pageTitle}):\n- Active Courses: {$ctx['courses_count']}\n- Pending Grading: {$ctx['pending_grading']} ({$ctx['late_submissions']} late)\n- Live Student Containers: {$ctx['active_workspaces']}\n- Pending AI Patches: {$ctx['pending_patches']}\n\nAsk me to triage your grading queue, review VisionGuard authorship signals, or draft an assignment rubric.";
        }

        // Student role
        if (str_contains($q, 'deadline') || str_contains($q, 'assignment') || str_contains($q, 'due')) {
            $items = empty($ctx['upcoming_assignments'])
                ? 'You have no upcoming published deadlines right now.'
                : implode("\n- ", $ctx['upcoming_assignments']);

            return "Upcoming Assignment Deadlines:\n- {$items}\n\nOpen any course page to launch its pinned Nix assignment workspace directly in your browser.";
        }

        if (str_contains($q, 'workspace') || str_contains($q, 'ide') || str_contains($q, 'nix') || str_contains($q, 'error') || str_contains($q, 'debug')) {
            $running = empty($ctx['running_workspaces'])
                ? 'None running right now'
                : implode(', ', $ctx['running_workspaces']);

            return "Workspace & Runtime Guidance:\n- Running Containers: {$running} ({$ctx['total_workspaces']} total provisioned)\n- Pending AI Patches: {$ctx['pending_patches']} waiting for your diff approval\n- Every workspace boots a deterministic .vision/dev.nix shell inside an isolated crun container. If a compiler package is missing, inspect your course template or restart/rebuild your workspace cleanly.";
        }

        if (str_contains($q, 'xp') || str_contains($q, 'level') || str_contains($q, 'progress') || str_contains($q, 'grade')) {
            return "Academic Progress Snapshot:\n- Current Rank: Level {$ctx['level']} {$ctx['rank_title']} ({$ctx['xp']} XP)\n- Enrolled Courses: {$ctx['courses_count']}\n- Completing assignments with high verified human authorship and maintaining your daily lab streak unlocks achievements and XP.";
        }

        $courseList = empty($ctx['courses']) ? 'No courses enrolled yet' : implode(', ', $ctx['courses']);

        return "Student Copilot Ready (from {$pageTitle}):\n- Level {$ctx['level']} {$ctx['rank_title']} · {$ctx['xp']} XP\n- Courses ({$ctx['courses_count']}): {$courseList}\n- Active Workspaces: " . count($ctx['running_workspaces']) . " running · {$ctx['pending_patches']} AI patch(es) awaiting review\n\nAsk me about your upcoming deadlines, Nix environment troubleshooting, assignment concepts, or workspace setup.";
    }

    private function suggestActions(User $user, string $message): array
    {
        $q = mb_strtolower($message);

        if ($user->isAdmin()) {
            if (str_contains($q, 'erp') || str_contains($q, 'campus') || str_contains($q, 'batch')) {
                return [
                    ['label' => 'Manage Campuses', 'href' => '/admin/campuses'],
                    ['label' => 'Departments', 'href' => '/admin/departments'],
                    ['label' => 'Course Batches', 'href' => '/admin/batches'],
                ];
            }
            if (str_contains($q, 'audit') || str_contains($q, 'security') || str_contains($q, 'extension')) {
                return [
                    ['label' => 'Audit Trail', 'href' => '/admin/audits'],
                    ['label' => 'VSIX Extensions', 'href' => '/admin/extensions'],
                    ['label' => 'Cluster Telemetry', 'href' => '/admin/status'],
                ];
            }

            return [
                ['label' => 'Cluster Telemetry', 'href' => '/admin/status'],
                ['label' => 'Users & Roles', 'href' => '/admin/users'],
                ['label' => 'Workspaces Fleet', 'href' => '/admin/workspaces'],
            ];
        }

        if ($user->isInstructor()) {
            return [
                ['label' => 'Open Grading Queue', 'href' => '/submissions'],
                ['label' => 'Your Courses', 'href' => '/courses'],
                ['label' => 'Create Course', 'href' => '/courses/create'],
            ];
        }

        return [
            ['label' => 'My Workspaces', 'href' => '/workspace'],
            ['label' => 'Enrolled Courses', 'href' => '/courses'],
            ['label' => 'Track Progress', 'href' => '/progress'],
        ];
    }
}

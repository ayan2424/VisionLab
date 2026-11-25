# Codebase Concerns

**Analysis Date:** 2026-11-24

## Tech Debt

**Monolithic `CodeServerManager` Service (1,079 lines / 44.8 KB):**
- Issue: A single class handles Docker volume creation, container lifecycle, port allocation, quota resolution, file I/O, template git cloning, Nix bootstrapping, VS Code extension installation, AI agent `config.json` injection, terminal command execution, and dev fallback. It contains duplicate `$workspace->update()` calls (lines 175–180 and 255–264), dead/placeholder nested-tree code in `listFiles()` (lines 581–596), mixed literal `'docker'` vs `$this->dockerCmd()` calls (line 185), and direct `env()` calls (lines 1069–1070).
- Files: `app/Services/CodeServerManager.php`
- Impact: High blast radius for every workspace change; difficult to unit test or swap container runtimes; hosts multiple critical shell-injection and return-type bugs.
- Fix approach: Split into cohesive services (`DockerContainerRuntime`, `WorkspaceVolumeStorage`, `WorkspaceExtensionSyncer`, `WorkspaceBootstrapper`, `PortAllocator`) injected via interfaces, remove dead placeholder closures, and move all `env()` lookups into `config/visionlab.php`.

**Repository Root Clutter & Stray Maintenance Scripts:**
- Issue: One-off scripts, duplicate prompt dumps, a 3.22 MB binary `composer.phar`, 5 conflicting Nginx configs, a raw SSH private key (`LightsailDefaultKey-ap-south-1.pem`), and an untracked remote environment file (`.env.remote`) sit directly in the project root. `scratch.php` and `fix_css.py` contain hardcoded local Windows paths (`C:\Users\ayans\...`). `.github/.github/workflows` also exists as an accidental nested directory.
- Files: `composer.phar`, `prompts.txt`, `PROMPTS.xml`, `scratch.php`, `fix_css.py`, `replace_icons.ps1`, `.env.remote`, `LightsailDefaultKey-ap-south-1.pem`, `nginx.conf`, `visionlab_nginx.conf`, `nginx_patch.conf`, `visionlab.rewrite.conf`, `rewrite.conf`, `daemon.json`, `.phpunit.result.cache`, `.github/.github`
- Impact: Bloats clones and Docker build contexts (`COPY . .`), creates ambiguity about which Nginx configuration is authoritative, and risks committing live credentials or SSH keys.
- Fix approach: Remove stray scripts, `composer.phar`, duplicate prompt/Nginx files, and `.github/.github`; consolidate into one canonical `docker/nginx/` config; relocate operational keys outside the repository; add `.env.*` (with `!.env.example`) to `.gitignore` and create a strict `.dockerignore`.

**Four-Way Environment Variable & Documentation Drift:**
- Issue: Configuration keys diverge across code, `.env.example`, `config/visionlab.php`, and `RUNBOOK.md`. `.env.example` documents `GEMINI_API_KEY`, `JITSI_APP_ID`, `JITSI_APP_SECRET`; `config/visionlab.php` reads `VISIONLAB_AI_API_KEY`, `VISIONLAB_JITSI_APP_ID`, `VISIONLAB_JITSI_JWT_SECRET`; `app/Services/AiService.php` falls back via direct `env('ANTHROPIC_API_KEY')`; `RUNBOOK.md` instructs operators to set `CLAUDE_API_KEY`. VAPID keys are also duplicated between `config/visionlab.php` (`visionlab.vapid.*`) and `config/webpush.php` (`webpush.vapid.*`).
- Files: `.env.example`, `config/visionlab.php`, `config/webpush.php`, `app/Services/AiService.php`, `app/Services/WebPushService.php`, `app/Http/Controllers/WorkspaceController.php`, `RUNBOOK.md`
- Impact: Fresh deployments following `.env.example` or `RUNBOOK.md` silently drop into AI Demo Mode and fail `/healthz` (`ai_config: false`, `jitsi_config: false`). Direct `env()` calls in `AiService.php` (lines 37, 276), `WorkspaceController::reverbConfig()` (lines 377–381), and `CodeServerManager::devFallback()` (lines 1069–1070) return `null`/defaults whenever `php artisan config:cache` is active.
- Fix approach: Standardize all env variable names in `config/visionlab.php` and `.env.example`, delete duplicate `config/webpush.php` (or unify on one key namespace), replace every non-config `env()` call with `config()`, and update `RUNBOOK.md`.

**Orphaned Post-Purge Artifacts & Dead Code:**
- Issue: Migration `database/migrations/2026_07_13_000000_remove_incomplete_features_tables.php` permanently dropped 10 tables (`video_attendances`, `video_rooms`, `recording_audit_logs`, `recordings`, `deployments`, `collab_sessions`, `workspace_collaborators`, `chat_messages`, `room_members`, `rooms`) with an empty `down()` method, but left behind dependent models, policies, notifications, jobs, events, form requests, views, an empty `app/Services/Deployment/` directory, and failing tests. In addition, `app/Http/Controllers/Api/AiConfigController.php` is imported in `routes/api.php` (line 5) but never routed; `app/Http/Controllers/AnalyticsController.php` and `resources/views/analytics.blade.php` (referencing obsolete Piston API & Gemini 2.0 Flash) are unrouted; `app/Services/AuditService.php` and `app/Models/NotificationPreference.php` are never called; `public/sw.js` (Workbox 7) and `public/serviceworker.js` are duplicated; `resources/views/offline.blade.php` and `resources/views/errors/offline.blade.php` are duplicated; `Attendance.php`/`AttendanceLog.php` and `Rubric.php`/`GradingRubric.php` duplicate domain models.
- Files: `database/migrations/2026_07_13_000000_remove_incomplete_features_tables.php`, `app/Models/Recording.php`, `app/Models/RecordingAuditLog.php`, `app/Models/ChatMessage.php`, `app/Jobs/ProcessRecordingJob.php`, `app/Policies/RecordingPolicy.php`, `app/Notifications/RecordingApprovedNotification.php`, `app/Notifications/RecordingReadyNotification.php`, `app/Events/CodeUpdated.php`, `app/Events/CursorMoved.php`, `app/Http/Requests/BroadcastCodeRequest.php`, `app/Http/Requests/BroadcastCursorRequest.php`, `app/Http/Requests/StoreChatRequest.php`, `app/Http/Controllers/Api/AiConfigController.php`, `app/Http/Controllers/AnalyticsController.php`, `resources/views/analytics.blade.php`, `app/Services/AuditService.php`, `public/sw.js`, `public/serviceworker.js`
- Impact: Confuses contributors, breaks CI test runs, leaves `// TODO` stubs (e.g., `ProcessRecordingJob.php` line 28) in violation of `AGENTS.md`, and causes runtime exceptions where surviving code still references dropped relations (`collaborators`).
- Fix approach: Either formally delete all orphaned classes/views/tests for permanently removed features or re-introduce them behind clean migrations; consolidate duplicate service workers, offline views, and models.

**Unwired Admin Governance & Stubbed Controllers:**
- Issue: Several admin/governance subsystems exist as shells that do not enforce behavior:
  - Feature flags `flag_ai_enabled`, `flag_video_calls_enabled`, and `flag_registration_open` are saved by `AdminSystemController::updateFlags()` (`app/Http/Controllers/Admin/AdminSystemController.php` lines 17–34) but never read by `AiController`, `AiService`, or auth routes.
  - `WorkspaceExtensionManagerController::install()` and `uninstall()` (`app/Http/Controllers/WorkspaceExtensionManagerController.php` lines 33–37, 53–55) have their `CodeServerManager` calls commented out (`// We simulate it here as requested`) and return a success flash without syncing running containers.
  - `AdminWebhookController` stores outgoing webhooks in `webhooks`, but zero events or listeners dispatch HTTP deliveries to them (still open from `VISIONLAB_AUDIT_REPORT.md`).
  - `AdminUserController::suspend()` never dispatches `app/Jobs/WorkspaceSuspendedUser.php`; `AdminUserController::exportGdpr()` never uses `app/Jobs/GdprDataExportJob.php`; `EnrollmentController::importCsv()` never uses `app/Jobs/CsvEnrollmentJob.php`.
- Files: `app/Http/Controllers/Admin/AdminSystemController.php`, `app/Http/Controllers/WorkspaceExtensionManagerController.php`, `app/Http/Controllers/Admin/AdminWebhookController.php`, `app/Http/Controllers/Admin/AdminUserController.php`, `app/Http/Controllers/EnrollmentController.php`
- Impact: Administrators believe feature flags, extension toggles, webhooks, and user suspension take effect when they silently do nothing.
- Fix approach: Wire feature flag checks into middleware/controllers, dispatch `SyncWorkspaceExtensions` from `WorkspaceExtensionManagerController`, dispatch `WorkspaceSuspendedUser` and revoke tokens on suspension, and implement queued webhook delivery or remove dead UI.

**Divergence from `AGENTS.md` & `VISIONLAB_AUDIT_REPORT.md` Status:**
- Issue: While `AGENTS.md` mandates Laravel Horizon, Pulse, Telescope, Spatie Activitylog, HTMLPurifier, Sanctum `Abilities` enum, zero binary-patching of `code-server`, and no `apt-get`, none of those packages are installed in `composer.json`, `docker/workspace/Dockerfile` runs `patch_origin.js` and `sed` over compiled binaries, and `docker/Dockerfile.codeserver` runs `apt-get install`. Compared to `VISIONLAB_AUDIT_REPORT.md` (July 2026):
  - **Resolved since July 2026:** `UserBadge::awardOnce()` is now invoked via `GamificationService::awardXpAndEvaluate()`; basic `belongsTo` relations were added to `Quiz`, `ForumTopic`, `Certificate`, `FeeChallan`, `Attendance`, `BookIssue`, `Employee`, `Payroll`, `QuizAttempt`, `Transaction` (via `scratch.php`).
  - **Still open from July 2026:** All 7 missing LMS controllers (`Forum`, `Quiz`, `Certificate`, `Attendance`, `Payment`, `Gradebook`, `CourseContent`); hardcoded CSV password `VisionLab2026!` in `EnrollmentController`; `SubmissionController::show()` sibling ID `pluck('id')`; broken impersonation session handling; `DashboardController` N+1 announcement queries; incomplete Library/HR/Payment/Rubric/NotificationPreference/Webhook workflows; missing `hasMany` relationships on `Quiz`, `ForumTopic`, `LibraryBook`.
- Files: `AGENTS.md`, `VISIONLAB_AUDIT_REPORT.md`, `composer.json`, `docker/workspace/Dockerfile`, `docker/Dockerfile.codeserver`
- Impact: Documentation misleads operators and developers about installed security controls and runtime capabilities.
- Fix approach: Reconcile `AGENTS.md` and `RUNBOOK.md` against actual architecture, then execute a prioritized remediation roadmap for open audit gaps.

## Known Bugs

**Fatal `TypeError` in `User::addXp()` Breaks Workspace Boot, Assignment Start, and Grading:**
- Symptoms: Fatal PHP `TypeError` (`Argument #3 ($source) must be of type ?App\Models\Model, App\Models\Workspace|Submission given`) whenever XP is awarded with a source model.
- Files: `app/Models/User.php` (lines 1–12, 167), `app/Services/GamificationService.php` (line 92), `app/Http/Controllers/WorkspaceController.php` (line 118), `app/Http/Controllers/SubmissionController.php` (lines 32, 205)
- Trigger: `app/Models/User.php` omits `use Illuminate\Database\Eloquent\Model;` so `?Model` resolves to non-existent `App\Models\Model`. Any student starting a workspace, starting an assignment, or receiving a grade > 0 triggers the exception.
- Workaround: None; add `use Illuminate\Database\Eloquent\Model;` to `app/Models/User.php`.

**Fatal Missing `Request` Import in `CourseExtensionController::update()`:**
- Symptoms: `BindingResolutionException` / `ReflectionException` (`Class "App\Http\Controllers\Request" does not exist`) when updating course extension policies.
- Files: `app/Http/Controllers/CourseExtensionController.php` (lines 1–8)
- Trigger: `PUT /courses/{course:slug}/extensions` from the Extensions tab in `resources/views/courses/show.blade.php`.
- Workaround: Add `use Illuminate\Http\Request;` to `app/Http/Controllers/CourseExtensionController.php`.

**Crash & Silent Data Loss in `AdminWorkspaceController` and `AdminQuotaController`:**
- Symptoms: Visiting `/admin/workspaces/{workspace}` crashes with `RelationNotFoundException` (`collaborators` does not exist on `Workspace`) followed by `SQLSTATE` unknown column `workspace_id` on `audit_logs`. Calling `stop`, `archive`, `destroy` (or `AdminQuotaController::updateGlobal()`) passes `'user_id'` and `'details'` to `AuditLog::create()`, which are stripped by `AuditLog::$fillable` (`actor_id`, `old_state`, `new_state`), silently losing actor attribution and change diffs.
- Files: `app/Http/Controllers/Admin/AdminWorkspaceController.php` (lines 28–29, 53–60, 74–81, 94–101), `app/Http/Controllers/Admin/AdminQuotaController.php` (lines 44–54), `app/Models/Workspace.php`, `app/Models/AuditLog.php` (lines 20–24)
- Trigger: Open any workspace detail page in Admin Panel or stop/archive/delete a workspace or update quotas.
- Workaround: Remove `'collaborators'` eager load, query `AuditLog::forResource('Workspace', $workspace->id)`, and replace raw `AuditLog::create()` calls with `AuditLog::record()`.

**Broken VisionGuard Forensics Sync & Display (Schema Mismatch + Relationship Typo):**
- Symptoms: `POST /api/workspace/{slug}/forensics/sync` throws a SQL error whenever a student has an active submission because `ForensicsController::sync()` writes non-existent columns (`human_keystroke_count`, `ai_injected_char_count`, `pasted_char_count`, `human_percentage`, `ai_percentage`, `confidence_level`) instead of the actual `SubmissionForensic` columns (`human_keystrokes`, `ai_patches_applied`, `pasted_count`, `human_pct`, `ai_pct`, `confidence`). Additionally, `resources/views/submissions/show.blade.php` reads `$submission->forensic` (singular), whereas `Submission.php` defines `forensics()` (plural), so the VisionGuard tab always shows "No Forensics Data Available".
- Files: `app/Http/Controllers/Api/ForensicsController.php` (lines 52–77), `app/Models/SubmissionForensic.php` (lines 14–20), `app/Models/Submission.php` (line 47), `resources/views/submissions/show.blade.php` (lines 142–176, 431–442)
- Trigger: Any forensics sync request for a course-linked workspace with an active submission, or opening the VisionGuard tab on `/submissions/{submission}`.
- Workaround: Align `ForensicsController` to `SubmissionForensic::$fillable` and `recalculate()`, link via `$workspace->assignment_id`, and rename `$submission->forensic` to `$submission->forensics` in Blade.

**`CodeServerManager` Runtime Bugs (`startWorkspace`, `stopWorkspace`, `runTerminalCommand`, `resolveSecurePath`):**
- Symptoms:
  1. `startWorkspace(): array` returns `false` on Docker start failure (line 167), throwing a PHP `TypeError` instead of reaching `WorkspaceController::start()`'s `if (!$result)` error handler.
  2. `startWorkspace()` executes `docker volume create` and `docker run alpine chown` (lines 45–52) *before* checking `if (!$this->isDockerAvailable())` (line 57), running failing Docker commands in non-Docker dev setups.
  3. `stopWorkspace()` checks `$workspace->student` (line 303), but `Workspace` defines `owner()`, not `student()`, so coding-session XP is never awarded.
  4. `runTerminalCommand()` dev fallback (line 1041) calls `$this->workspacePath($workspace)`, an undefined method on `CodeServerManager`.
  5. `resolveSecurePath()` always returns `null` (line 687), making `getSecureFilePath()` permanently return `null`.
- Files: `app/Services/CodeServerManager.php` (lines 39–60, 167, 303–305, 683–697, 1041), `app/Http/Controllers/WorkspaceController.php` (lines 106–110), `app/Models/Workspace.php` (line 66)
- Trigger: Container start failure, stopping a running workspace, invoking AI `run_terminal` without Docker, or calling `getSecureFilePath()`.
- Workaround: Fix return type to `?array` (or throw domain exception), move `isDockerAvailable()` check to line 41, use `$workspace->owner`, and remove/fix the host fallback in `runTerminalCommand()`.

**`SubmissionController::ide()` Missing View Variables & `grade()` Dead Late-Penalty Logic:**
- Symptoms: `SubmissionController::ide()` renders `workspace.blade.php` without passing `$needsBoot` or `$isAssignmentLocked`, causing `Undefined variable $needsBoot` at `resources/views/workspace.blade.php` line 284. In `SubmissionController::grade()`, line 189 updates `$submission->status` to `'graded'` *before* line 200 checks `if ($submission->status === 'late')`, so the 50% late XP penalty never fires.
- Files: `app/Http/Controllers/SubmissionController.php` (lines 139–148, 186–204), `resources/views/workspace.blade.php` (lines 284, 445)
- Trigger: Opening `/assignments/{assignment}/ide` as a student, or grading any late submission.
- Workaround: Pass `'needsBoot' => false` and `'isAssignmentLocked' => (bool) $assignment->is_locked` in `ide()`; capture `$wasLate = $submission->status === 'late';` before calling `$submission->update(...)`.

**Workspace Slug vs `ws-{id}` Addressing Mismatch:**
- Symptoms: `Workspace::getRouteKeyName()` returns `'slug'` (e.g., `alice-python-app`), so web routes and `routes/api.php` `/api/workspace/{slug}/trigger-rebuild` look up by `slug`. However, `WorkspaceFileController::resolveWorkspace()` and `ForensicsController::sync()` strip `'ws-'` and run `Workspace::find($id)`, returning 404 for real workspace slugs.
- Files: `app/Models/Workspace.php` (lines 46–62), `app/Http/Controllers/WorkspaceFileController.php` (lines 195–199), `app/Http/Controllers/Api/ForensicsController.php` (lines 20–21), `routes/api.php` (lines 21, 42)
- Trigger: Calling `/api/workspace/{slug}/files` or `/api/workspace/{slug}/forensics/sync` with an actual workspace slug.
- Workaround: Support both `slug` lookup and legacy `ws-{id}` fallback (or standardize on route model binding `Workspace $workspace`).

**Broken Background Jobs & Scheduled Commands (`ExecutePlanJob`, `CsvEnrollmentJob`, `WorkspaceSnapshotJob`, `WorkspaceCleanup`, `WorkspaceStaleHeartbeat`, `AssignmentDueReminders`):**
- Symptoms:
  - `ExecutePlanJob::handle()` (line 49) calls undefined helper `activity()` (`spatie/laravel-activitylog` is not installed).
  - `CsvEnrollmentJob::handle()` (line 54) references non-existent `\App\Models\CourseEnrollment` (model is `Enrollment`).
  - `WorkspaceSnapshotJob::handle()` (line 29) and `WorkspaceCleanup::handle()` (line 36) read/delete local `Storage` `$workspace->storage_path`, which is never populated under Docker Native Volumes — snapshots never generate and Docker volumes (`vl_ws_{id}_data`) are orphaned forever on cleanup.
  - `WorkspaceStaleHeartbeat` stops running workspaces once `heartbeat_at` exceeds `timeout_minutes`, but nothing in the codebase ever refreshes `heartbeat_at` after initial boot (`WorkspaceController::ping()` only calls `isWorkspaceReady()`, never `heartbeat()`), terminating active student coding sessions mid-work.
  - `AssignmentDueReminders` runs every 6 hours (`routes/console.php` line 12) for all assignments due within 24 hours without deduplication, notifying every student up to 4 times per assignment.
- Files: `app/Jobs/ExecutePlanJob.php`, `app/Jobs/CsvEnrollmentJob.php`, `app/Jobs/WorkspaceSnapshotJob.php`, `app/Console/Commands/WorkspaceCleanup.php`, `app/Console/Commands/WorkspaceStaleHeartbeat.php`, `app/Console/Commands/AssignmentDueReminders.php`, `routes/console.php`, `app/Http/Controllers/WorkspaceController.php` (lines 137–146)
- Trigger: Dispatching those jobs or running the Laravel scheduler.
- Workaround: Replace `activity()` with `AuditLog::record()`, fix `Enrollment` class name, use `CodeServerManager::deleteWorkspace()` in `WorkspaceCleanup`, update `heartbeat_at` on ping/activity, and record sent reminders.

**Broken `AiSandbox::searchCodebase()` & Contextless `AiService::executePlan()`:**
- Symptoms: `AiSandbox::searchCodebase()` searches host directory `storage_path('workspaces/ws-' . $workspace->id)`, which is no longer created because workspaces live in Docker volumes — returning `[]` for every search. `AiService::executePlan()` sends a hardcoded prompt (`"Please implement the plan we just agreed upon"`) with zero prior conversation history.
- Files: `app/Services/AiSandbox.php` (lines 41–47), `app/Services/AiService.php` (lines 317–330)
- Trigger: AI Agent invoking `search_codebase` or user triggering `POST /api/ai/patches/execute-plan`.
- Workaround: Execute `grep` safely via `CodeServerManager` inside the workspace container/volume and persist/reload `AiChatSession`/`AiMessage` history in `executePlan()`.

**Broken Analytics, Streaks, GDPR Export, WebPush Cleanup, Offline Route, and Exam Lockdown UI:**
- Symptoms:
  - `AdminAnalyticsController::index()` (line 50) queries `AnalyticsEvent::ofType('workspace.started')`, while `WorkspaceController` (line 115) tracks `'workspace_started'` — the 14-day workspace chart is always 0.
  - `ContributionController::heatmap()` (lines 64–73) iterates backwards from today to 365 days ago, resetting `$streak = 0` on gaps, returning the streak from 365 days ago as `current_streak`.
  - `AdminUserController::exportGdpr()` (line 174) calls `->map->course->only(['id', 'name', 'slug'])` on an Eloquent `Collection` (filtering collection keys instead of model attributes, plus `Course` uses `title` not `name`), always exporting `'courses' => []`.
  - `WebPushService::sendToSubscription()` (line 58) calls `sendOneNotification()` immediately rather than `queueNotification()`, so `flush()` on line 42 is empty and expired subscriptions are never deleted.
  - `routes/web.php` defines `/offline` twice (line 28 public `offline` view, overwritten by line 52 `auth, verified` `errors.offline` view), locking unauthenticated offline users out of the PWA fallback. Meanwhile `public/serviceworker.js` precaches non-existent `/logo.svg`, causing `cache.addAll()` to reject on install.
  - `resources/views/workspace.blade.php` (lines 468, 479) calls undefined JS function `showToast()`, creates duplicate `#btn-enter-lockdown` alongside `components/exam-lockdown.blade.php`, and `exam-lockdown.blade.php` (line 80) POSTs exam violations to non-existent `/api/analytics/events` (404).
  - `AnalyticsController::index()` queries non-existent columns `workspaces.user_id` (line 33) and `submissions.course_id` (line 65).
  - `AdminFeeChallanController` is registered with `Route::resource('challans', ...)` (`routes/web.php` line 192) but does not implement `edit()`, `update()`, or `destroy()`.
  - `app/Models/Transaction.php` (lines 11–14) defines a bogus self-referential `belongsTo(Transaction::class)` generated by `scratch.php` from the string `transaction_id` column.
- Files: `app/Http/Controllers/Admin/AdminAnalyticsController.php`, `app/Http/Controllers/ContributionController.php`, `app/Http/Controllers/Admin/AdminUserController.php`, `app/Services/WebPushService.php`, `routes/web.php`, `public/serviceworker.js`, `resources/views/workspace.blade.php`, `resources/views/components/exam-lockdown.blade.php`, `app/Http/Controllers/AnalyticsController.php`, `app/Http/Controllers/Admin/AdminFeeChallanController.php`, `app/Models/Transaction.php`
- Trigger: Normal navigation to the affected routes/actions.
- Workaround: Standardize event names, fix collection mapping and streak loop, switch `WebPushService` to `queueNotification()`, remove duplicate `/offline` route and `/logo.svg` precache, wire exam violations to `/api/visionguard/log`, and clean up `Transaction.php`.

## Security Considerations

**Critical — Shell Command Injection & Missing Path Traversal Protection in Workspace File I/O:**
- Risk: `CodeServerManager` interpolates user-supplied paths inside single-quoted `sh -c` strings in `listFiles()` (line 548), `writeFile()` (lines 624, 626), `createFile()` (line 655), and unquoted `$fileName`/`$extDirName` in `installExtension()` (line 892). A path containing `'` breaks out of quotes and executes arbitrary shell commands inside the container. Furthermore, `CodeServerManager::resolveSecurePath()` returns `null` (deprecated), none of `CodeServerManager`'s file methods check for `..` or symlinks, `WorkspaceController`'s web file endpoints (`files`, `readFile`, `writeFile`, `createFile`, `deleteFile`, `renameFile`) perform zero path validation, `WorkspaceFileController` omits `isDangerousPath()` on `read()`, `create()`, and `rename()`, and `isDangerousPath()` only checks `..` (failing to block `.env`, `.git`, `vendor`, `node_modules`, `.vision/`, or null bytes). `AiSandbox::searchCodebase()` (line 52) also passes `$query` to `grep` without `--`, allowing option injection, and `CodeServerManager::runTerminalCommand()` (line 1046) runs `Process::fromShellCommandline($command)` directly on the host OS in non-Docker fallback mode.
- Files: `app/Services/CodeServerManager.php` (lines 545–697, 890–895, 1038–1065), `app/Http/Controllers/WorkspaceFileController.php` (lines 30–215), `app/Http/Controllers/WorkspaceController.php` (lines 243–371), `app/Http/Controllers/Admin/ExtensionController.php` (lines 35, 48), `app/Services/AiSandbox.php` (lines 52, 128–137)
- Current mitigation: Basic `str_contains(..., '..')` on `WorkspaceFileController::write`/`delete` and `WorkspaceController::downloadFile` only; Symfony `Process` array arguments on `cat`/`rm`/`mv`.
- Recommendations: Eliminate all `sh -c` string interpolation (pass paths as separate positional arguments or `escapeshellarg()` + `realpath -m` validation inside container); centralize strict relative-path canonicalization and blocked-directory checks (`.env`, `.git`, `vendor`, `node_modules`, `.vision`, null bytes) inside `CodeServerManager` for ALL web/API/AI file operations; validate `package_identifier`/`version` with strict regexes; add `--` before `$query` in `grep`; remove host shell execution from `runTerminalCommand()`.

**Critical — Complete IDOR & Broken Authorization in `AiController`:**
- Risk: None of the 5 endpoints in `AiController` call `$this->authorize(...)`, and `app/Policies/AiActionPolicy.php` is neither registered for `AiPendingPatch` nor invoked. Any authenticated student can:
  - Supply another user's `X-Workspace-Id` to `POST /api/ai/v1/chat/completions` or `POST /api/ai/patches/execute-plan` to read/mutate another student's workspace via AI tools.
  - Pass another user's `workspace_id` to `GET /api/ai/patches/pending` to exfiltrate source code diffs.
  - Call `POST /api/ai/patches/{patch}/approve` or `/reject` on any `AiPendingPatch` ID in the database to overwrite files in another user's workspace.
- Files: `app/Http/Controllers/Api/AiController.php` (lines 28–150), `app/Policies/AiActionPolicy.php`, `app/Providers/AppServiceProvider.php`, `routes/api.php` (lines 57–73)
- Current mitigation: `auth:sanctum` + `throttle:ai` only (no workspace or patch ownership check).
- Recommendations: Register `AiPendingPatch` policy in `AppServiceProvider`, authorize `access` on `$workspace` in `chatCompletions`, `pendingPatches`, and `executePlan`, authorize `approvePatch`/`rejectPatch` on `$patch`, and validate `X-Workspace-Id` against owned/authorized workspaces only.

**Critical — Unauthenticated IDE Containers, Disabled CSWSH Protection & Open Localhost SSRF Proxy:**
- Risk: Three flaws chain together into full unauthenticated cross-tenant IDE takeover and internal service exposure:
  1. `CodeServerManager::startWorkspace()` permanently disables IDE authentication (`$authMode = 'none'`, line 102) and binds containers on all host interfaces (`-p {$port}:8080`, line 129).
  2. `docker/workspace/patch_origin.js` patches VS Code's `server.main.js` origin check to `if(false)`, completely disabling Cross-Site WebSocket Hijacking (CSWSH) protection.
  3. `visionlab_nginx.conf` (lines 82–94), `nginx_patch.conf` (lines 19–85), `visionlab.rewrite.conf` (lines 31–41), and `docker/nginx.conf` (lines 42–50) proxy arbitrary numeric ports (`/ide/<cport>` or `/codeserver/<cport>`) to `127.0.0.1:$cport` or `host.docker.internal:$1` with ZERO authentication and ZERO port-range restriction — exposing every running student IDE, Redis (`6379`), MySQL (`3306`), Reverb (`8080`), and any internal daemon to the public internet.
- Files: `app/Services/CodeServerManager.php` (lines 102, 129), `docker/workspace/patch_origin.js`, `docker/workspace/Dockerfile` (line 34), `visionlab_nginx.conf` (lines 82–94), `nginx_patch.conf` (lines 19–85), `visionlab.rewrite.conf` (lines 31–41), `docker/nginx.conf` (lines 42–50)
- Current mitigation: None on the reverse proxy or code-server runtime.
- Recommendations: Bind container ports strictly to `127.0.0.1:{$port}:8080` (or route over internal Docker network by container name), re-enable code-server authentication or enforce Nginx `auth_request` verifying Laravel session ownership of the specific workspace port, restrict proxied ports to `9000–9999`, and delete `patch_origin.js` (configure explicit trusted origins properly).

**Critical — Docker Socket Exposure & Incomplete Container Hardening:**
- Risk: `docker-compose.prod.yml` mounts `/var/run/docker.sock` directly into web-facing `visionlab-app`, `visionlab-queue`, and `visionlab-scheduler` (lines 17, 34, 69), turning any PHP RCE into immediate host root compromise. Meanwhile, student containers spawned by `CodeServerManager::startWorkspace()` omit `--security-opt no-new-privileges:true`, `--read-only`, `--pids-limit`, `--user 1000:1000`, and disk quotas; set `.vision/bootstrap.sh` to `0777` and `.vision/dev.nix` to `0666` (lines 778, 783); enable `NIXPKGS_ALLOW_INSECURE=1` (lines 153, 241, 249); and run `docker exec` without `-u 1000` in `runTerminalCommand()` (line 1058) and `setupWorkspaceExtensions()` (line 984). Additionally, there is no `.dockerignore`, so `COPY . .` in `Dockerfile` and `docker/app/Dockerfile` and bind-mount `.:/var/www/html` in `docker-compose.prod.yml` expose `.env`, `.env.remote`, `.git`, and `LightsailDefaultKey-ap-south-1.pem` inside containers.
- Files: `docker-compose.prod.yml` (lines 16–17, 33–34, 68–69, 85, 99–100), `Dockerfile` (line 51), `docker/app/Dockerfile` (line 36), `app/Services/CodeServerManager.php` (lines 119–158, 778–783, 984, 1058)
- Current mitigation: `--cap-drop ALL`, `--init`, `--memory`, `--cpu-shares`, `--tmpfs /tmp /run` on workspace containers; `no-new-privileges:true` on some compose services.
- Recommendations: Place Docker API access behind a restricted socket proxy (e.g., Tecnativa `docker-socket-proxy` scoped to container/volume endpoints) or dedicated privileged worker service; add `--security-opt no-new-privileges:true`, `--pids-limit 256`, `--user 1000:1000`, and disk limits; stop setting `0777`/`0666` permissions; add `.dockerignore` and remove `.:/var/www/html` bind mounts in production.

**Critical — AI Agent Sandbox Bypass & Prompt Injection (`AiService` & `PatchReviewer.ts`):**
- Risk:
  1. `AiService::getToolDefinitions()` exposes `run_terminal` (lines 207–210, 386–395), allowing the LLM to directly execute arbitrary shell commands and overwrite/delete files inside containers — bypassing the `ai_pending_patches` Human-In-The-Loop invariant mandated by `AGENTS.md`.
  2. `AiService::handleChatCompletion()` (lines 99–103) concatenates client-supplied `role: "system"` messages directly onto the server `$systemPrompt` and passes client-supplied `$requestedModel` (line 112) unvalidated to Anthropic, enabling prompt override and cost abuse.
  3. Zero content safety filters exist (`eval`, `exec`, reverse shells, null bytes are unfiltered).
  4. `extensions/visionlab-collab/src/panels/PatchReviewer.ts` (lines 57–96) renders unescaped `patch.file_path` and `patch.diff` via `innerHTML` inside the same Webview `<script>` that embeds the user's Sanctum `TOKEN` — enabling Stored/AI-assisted XSS to steal the student's API token.
  5. `app/Http/Controllers/Api/AiConfigController.php` (line 28) returns raw `$provider->api_key` in JSON if ever routed.
- Files: `app/Services/AiService.php` (lines 96–113, 207–210, 261, 386–395), `app/Http/Controllers/Api/AiController.php` (lines 47–50), `extensions/visionlab-collab/src/panels/PatchReviewer.ts` (lines 57–96), `app/Http/Controllers/Api/AiConfigController.php` (line 28)
- Current mitigation: Exam mode / `allow_ai` check in `AiService` (lines 57–64); daily `AiActionsLog` row count check (lines 46–54).
- Recommendations: Remove `run_terminal` or gate every command behind HITL approval; reject/ignore client `system` messages; enforce a server-side model allowlist and token/cost budget; implement content safety filters on inputs/outputs; escape all diff/path text via `textContent` and use `postMessage` (never embed tokens in webview HTML) in `PatchReviewer.ts`; strip `apiKey` from `AiConfigController`.

**High — Secret Hygiene, Hardcoded Infrastructure Metadata & Weak TLS/CSP:**
- Risk:
  - `.env.remote` is NOT listed in `.gitignore` (risking accidental commit of remote production secrets).
  - `LightsailDefaultKey-ap-south-1.pem` (SSH private key) resides inside the workspace root (ignored by `*.pem` in Git, but exposed to Docker build contexts and host backups).
  - Production server IP (`15.207.144.48`), SSH username, web root, and key paths are hardcoded in `AGENTS.md` (lines 195–197), `replace_icons.ps1` (lines 4–10, also using `StrictHostKeyChecking=no`), and `app/Http/Middleware/SecurityHeaders.php` (lines 32, 49).
  - `.env.example` commits non-placeholder values for `REVERB_APP_ID`, `REVERB_APP_KEY`, and `REVERB_APP_SECRET` (lines 69–71); `docker-compose.prod.yml` defaults `DB_PASSWORD` to `secret` (lines 99–100) and leaves Redis unauthenticated.
  - `visionlab_nginx.conf` (lines 29–30, 39) enables deprecated `TLSv1.1`, weak `3DES` ciphers, and `ssl_early_data on` (0-RTT replay risk).
  - `SecurityHeaders.php` permits `'unsafe-inline' 'unsafe-eval'` in `script-src` and wildcard `ws: wss: https: http:` in `connect-src` in all environments, and blocks `camera=(), microphone=()` in `Permissions-Policy` (conflicting with video conferencing).
- Files: `.gitignore`, `.env.remote`, `LightsailDefaultKey-ap-south-1.pem`, `.env.example`, `AGENTS.md`, `replace_icons.ps1`, `docker-compose.prod.yml`, `visionlab_nginx.conf`, `app/Http/Middleware/SecurityHeaders.php`
- Current mitigation: `.gitignore` ignores `.env`, `*.pem`, `*.tar.gz`.
- Recommendations: Move `.pem` keys out of the repository and rotate any exposed credentials/keys; add `.env.*` (except `.env.example`) to `.gitignore`; replace `.env.example` Reverb values with empty placeholders; remove hardcoded IPs from `SecurityHeaders.php` and docs; enforce TLS 1.2/1.3 modern ciphers and nonce-based CSP.

**High — Host Header Poisoning, Unprotected API Suspension, Non-Expiring Wildcard Tokens & Auth Flaws:**
- Risk:
  - `app/Providers/AppServiceProvider.php` (lines 21–33) passes unvalidated `$_SERVER['HTTP_HOST']` into `URL::forceRootUrl()`, while `bootstrap/app.php` (line 16) sets `$middleware->trustProxies(at: '*')`. Attackers can poison password-reset/verification links and spoof `X-Forwarded-For` to bypass IP rate limits.
  - `CheckAccountStatus` and `CheckMaintenanceMode` are attached ONLY to `web` middleware (`bootstrap/app.php` lines 28–32), NOT `api`, allowing suspended users with existing Sanctum tokens to keep using all `/api/*` endpoints.
  - `config/sanctum.php` sets `'expiration' => null` (tokens never expire), and `CodeServerManager::startWorkspace()` mints **3 new wildcard `['*']` Sanctum tokens** on every workspace start (lines 131, 137, 934) without ever revoking old tokens.
  - `routes/api.php` (lines 41–48) `/api/workspace/{slug}/trigger-rebuild` uses `!==` instead of `hash_equals()` and passes when both `$workspace->token` and bearer token are `null` (`null !== null` is `false`).
  - `EnrollmentController::importCsv()` (line 191) still creates every CSV-imported account (including `admin` role when uploaded by an admin) with hardcoded password `'VisionLab2026!'` and no forced reset.
  - `AdminUserController::impersonate()` and `stopImpersonating()` are bound to `GET` routes (`routes/web.php` lines 154, 217 — CSRF-vulnerable), do not regenerate session IDs, do not audit-log impersonation, and lock out the admin if impersonating a suspended user (because `CheckAccountStatus` invalidates the session, wiping `impersonator_id`).
  - `PushSubscriptionController::store()` (lines 20–30) allows arbitrary `endpoint` URLs (SSRF risk when `WebPushService` POSTs to them) and allows overwriting another user's subscription row by endpoint.
  - `routes/auth.php` (line 25) does not attach the `throttle:auth` middleware defined in `AppServiceProvider`, and `ForensicsController::sync()` trusts self-reported student `humanKeystrokeDelta` values with no integrity verification.
- Files: `app/Providers/AppServiceProvider.php`, `bootstrap/app.php`, `config/sanctum.php`, `app/Services/CodeServerManager.php`, `routes/api.php`, `routes/web.php`, `routes/auth.php`, `app/Http/Controllers/EnrollmentController.php`, `app/Http/Controllers/Admin/AdminUserController.php`, `app/Http/Controllers/PushSubscriptionController.php`, `app/Http/Controllers/Api/ForensicsController.php`
- Current mitigation: Laravel Breeze per-email+IP login throttle; `auth:sanctum` on API routes; `role:admin` middleware on admin panel.
- Recommendations: Remove `HTTP_HOST` `forceRootUrl` override (use `APP_URL`), restrict `trustProxies` to actual reverse-proxy IPs, append `CheckAccountStatus` to API middleware, set Sanctum token expiration and scoped abilities enum, revoke tokens on user suspension/workspace stop, enforce non-empty `hash_equals` on `trigger-rebuild`, generate random passwords + reset emails on CSV import, convert impersonation to `POST` with session regeneration + audit logging + active-user check, and whitelist WebPush endpoint domains.

## Performance Bottlenecks

**Synchronous 600-Second Docker/Nix Container Boots Starving PHP-FPM:**
- Problem: `WorkspaceController::start()`, `processRebuild()`, `processRestart()`, and `SubmissionController::start()`/`ide()` execute `CodeServerManager::startWorkspace()` synchronously inside PHP-FPM HTTP requests (`set_time_limit(600)`), running multiple `docker` subprocesses, template `git clone`, Nix shell initialization, and extension installs while holding a PHP-FPM worker slot. Furthermore, `SubmissionController::ide()` calls `startWorkspace()` on every page load, spawning `docker volume create` and `docker run alpine chown` even when the container is already running.
- Files: `app/Http/Controllers/WorkspaceController.php` (lines 99–106, 174–175, 199, 218), `app/Http/Controllers/SubmissionController.php` (lines 58, 125), `app/Services/CodeServerManager.php` (lines 39–285), `docker/php-fpm.conf` (line 4)
- Cause: Heavy blocking container orchestration runs inline in HTTP controllers instead of async queue jobs, against a pool of only `pm.max_children = 50`.
- Improvement path: Dispatch container start/rebuild/restart to a dedicated `workspaces` queue job, broadcast readiness via Reverb (or poll status), and short-circuit `startWorkspace()` immediately when the container is already running.

**Blocking Non-Streaming AI Agent Loop Inside HTTP Workers:**
- Problem: `AiService::handleChatCompletion()` wraps a synchronous Guzzle loop (up to 10 sequential blocking HTTP POSTs to Anthropic with no `timeout` configured and without `'stream' => true`) inside `StreamedResponse`, plus `AiController::executePlan()` runs the full multi-turn agent loop synchronously during a POST request.
- Files: `app/Services/AiService.php` (lines 115–265), `app/Http/Controllers/Api/AiController.php` (lines 52–69, 145–149)
- Cause: Non-streaming API requests and unqueued plan execution tie up PHP-FPM workers for 30–120+ seconds per user prompt.
- Improvement path: Enable true Anthropic SSE streaming (`'stream' => true` with Guzzle `stream: true` and explicit connect/read timeouts) and dispatch `executePlan` via a fixed queued job.

**Uncached Database Hit on Every Web Request (`CheckMaintenanceMode`):**
- Problem: Global web middleware `CheckMaintenanceMode` calls `SystemConfig::getVal('maintenance_mode', false)` on every non-admin web request, and `SystemConfig::getVal()` queries MySQL directly with zero caching.
- Files: `app/Http/Middleware/CheckMaintenanceMode.php` (line 25), `app/Models/SystemConfig.php` (lines 18–30)
- Cause: Missing `Cache::remember()` wrapper with cache invalidation in `SystemConfig::setVal()`.
- Improvement path: Cache `system_config:{key}` in Redis/cache store and invalidate inside `SystemConfig::setVal()`.

**Widespread N+1 Queries, Blade DB Calls & Unbounded Scans:**
- Problem:
  - `DashboardController::studentDashboard()` calls `$announcement->isReadBy($user)` inside an `each()` loop (N+1) and runs nested `whereHas`/`whereDoesntHave` counts; `instructorDashboard()` runs 6 separate `whereHas('assignment.course', ...)` queries uncached.
  - `resources/views/courses/show.blade.php` runs `$course->students()->count()` 3 times, `$assignment->submissions()->...->count()` inside `@forelse($course->assignments)` (N+1), and `Extension::where(...)->get()` directly in Blade.
  - `AdminController::index()` executes 23 uncached SQL queries and `AdminAnalyticsController::index()` executes 16 full-table aggregation queries per load.
  - `SubmissionController::show()` loads all sibling submission IDs via `pluck('id')` into memory (open from July 2026 audit).
  - `CourseController::roster()` loads every unenrolled student across the entire database into memory (`User::where('role', 'student')->whereNotIn(...)->get()`).
  - `GamificationService::evaluateBadges()` runs 6+ `COUNT(*)` queries plus up to 14 badge existence checks synchronously on every workspace start/stop and submission event.
  - `PublicProfileController::show()` loads all 30-day `AnalyticsEvent` rows into PHP memory to aggregate hourly buckets on an unauthenticated route.
  - `CourseExtensionController::update()` re-queries `Extension` and `CourseExtension` inside `foreach ($course->workspaces)`.
  - `ExtensionController::toggleGlobal()`, `routes/console.php` push reminder closure, `SendDueRemindersCommand`, `AdminLibraryController::index()`, `AdminFeeChallanController::index()`, `AdminEmployeeController::index()`, and `AdminWebhookController::index()` load entire tables via `->get()` without pagination or chunking.
- Files: `app/Http/Controllers/DashboardController.php`, `resources/views/courses/show.blade.php`, `app/Http/Controllers/Admin/AdminController.php`, `app/Http/Controllers/Admin/AdminAnalyticsController.php`, `app/Http/Controllers/SubmissionController.php`, `app/Http/Controllers/CourseController.php`, `app/Services/GamificationService.php`, `app/Http/Controllers/PublicProfileController.php`, `app/Http/Controllers/CourseExtensionController.php`, `app/Http/Controllers/Admin/ExtensionController.php`, `routes/console.php`, `app/Console/Commands/SendDueRemindersCommand.php`
- Cause: Missing eager loading (`withCount`, `withExists`), missing Redis caching for dashboard/analytics aggregates, and lack of pagination/chunking.
- Improvement path: Replace Blade queries with controller `withCount`/`withExists`; cache admin/instructor/student KPI summaries with short TTLs (60–300s); replace `pluck('id')` sibling navigation with keyset `WHERE submitted_at < ?` / `>` queries; move badge evaluation to queued listeners; aggregate hourly stats in SQL; add `paginate()`/`chunkById()`.

## Fragile Areas

**Docker / CI/CD / Production Topology Mismatches:**
- Files: `docker-compose.prod.yml`, `Dockerfile`, `docker/app/Dockerfile`, `docker/workspace/Dockerfile`, `docker/workspace/patch_origin.js`, `docker/nginx/default.conf`, `visionlab_nginx.conf`, `nginx_patch.conf`, `.github/workflows/deploy.yml`, `.github/workflows/production.yml`, `app/Http/Controllers/WebhookController.php`, `scripts/security_verify.sh`
- Why fragile:
  - `docker-compose.prod.yml` runs `command: php artisan horizon` on `visionlab-queue` even though `laravel/horizon` is NOT installed (crash loop); references `docker/app/Dockerfile` which lacks `composer install`, `npm run build`, and `docker-cli` (required by `CodeServerManager` calling `/usr/bin/docker`); omits the `visionlab-workspace-net` network required by `CodeServerManager.php` line 125; and uses `docker/nginx/default.conf` which has NO `/ide/` proxy block at all.
  - Root `Dockerfile` runs `php artisan config:cache` at image build time (baking empty env vars).
  - `docker/workspace/Dockerfile` installs `/usr/bin/visionlab-server` (no `/usr/bin/entrypoint.sh`), yet `CodeServerManager.php` lines 154 & 156 execute `/usr/bin/entrypoint.sh`. `patch_origin.js` string-replaces minified Webpack symbols (`P.$Cn`, `ue`, `B`, `ie.$xQ`) that break on any `code-server` rebuild.
  - Two overlapping CI workflows (`.github/workflows/deploy.yml` and `.github/workflows/production.yml`) both trigger on push to `main`; `production.yml` references non-existent Docker `target: backend` and lacks `packages: write`; `deploy.yml` deploys to `/www/wwwroot/visionlab` while `AGENTS.md` states `/www/wwwroot/visionlab.ayan24.me/`.
  - `WebhookController::deploy()` runs `exec("nohup bash deploy.sh ...")` without locking, and `deploy.sh` does not exist in the repository.
  - `scripts/security_verify.sh` checks non-existent Laravel 10 `app/Providers/RouteServiceProvider.php` and fails Check 7 (`exec(`) on `WebhookController.php` line 51.
- Safe modification: Establish a single canonical Dockerfile + compose stack + Nginx config + CI workflow; test `docker compose config` and container startup in staging before changing workspace image/entrypoint contracts.
- Test coverage: Zero automated deployment, Docker smoke, or Nginx config tests.

**Workspace Port Allocation, Reverse Proxy Asset Routing & Unbounded Readiness Polling:**
- Files: `app/Services/CodeServerManager.php` (lines 831–849, 931–985), `nginx_patch.conf` (lines 19–72), `resources/views/workspace.blade.php` (lines 323–360)
- Why fragile: `allocatePort()` suffers a TOCTOU race between `Workspace::whereNotNull('port')` / `fsockopen('localhost', $port)` and `docker run -p`, falling back to `rand(9000, 9999)` on exhaustion. `setupWorkspaceExtensions()` hardcodes `http://host.docker.internal:8000/api/ai/v1` (breaks on Linux Docker without `extra_hosts` and on production port 443). `nginx_patch.conf` hardcodes fallback `set $cport "9002"` for `/stable-`, `/webview/`, `/vscode/` static assets based on `Referer`. `workspace.blade.php` polls `/workspace/{slug}/ping` every 3 seconds indefinitely with no retry cap.
- Safe modification: Use an atomic DB unique constraint or Redis lock for port allocation (or Docker DNS routing by container name instead of host ports), configure AI base URL from `config('app.url')`, and add max-retry/timeout handling to the browser readiness poller.
- Test coverage: No concurrency tests for port allocation; no integration tests against real Docker containers.

**`WorkspaceQuota::resolveFor()` Type Coercion on `scope_id`:**
- Files: `app/Models/WorkspaceQuota.php` (lines 37–45, 72–83)
- Why fragile: `scopeForScope('role', $user->role)` runs `where('scope_id', 'student')->orWhere('scope_value', 'student')`. Because `scope_id` is an integer column, comparing against string `'student'` can throw strict-mode SQL warnings/errors on MySQL 8 or inadvertently match `scope_id = 0`.
- Safe modification: Branch on scope type (`scope_value` for `role`, `scope_id` for `user`/`course`) rather than comparing both columns unconditionally.
- Test coverage: No tests covering `WorkspaceQuota::resolveFor()` on MySQL (SQLite masks integer-vs-string coercion).

**Dual Exam Lockdown Implementation:**
- Files: `resources/views/workspace.blade.php` (lines 116, 444–523), `resources/views/components/exam-lockdown.blade.php`
- Why fragile: Two conflicting lockdown implementations are mounted on the same page (`<x-exam-lockdown>` for `mode === 'exam'` and inline script for `$isAssignmentLocked`), sharing duplicate element ID `#btn-enter-lockdown`, calling undefined `showToast()`, and POSTing to non-existent `/api/analytics/events`.
- Safe modification: Consolidate into a single `<x-exam-lockdown>` component backed by `/api/visionguard/log` and remove the duplicated inline block from `workspace.blade.php`.
- Test coverage: Zero browser/JS tests for exam lockdown behavior.

## Scaling Limits

**Single-Host Port Range & Container Concurrency:**
- Current capacity: `CodeServerManager` allocates host ports `9000–9999` (hard ceiling of 1,000 containers) on a single Docker host with linear `fsockopen()` scanning and 2,048 MB default container memory.
- Limit: A 32 GB RAM node (such as GCP `e2-standard-8` referenced in `AGENTS.md`) hits memory exhaustion around ~12–15 active 2 GB IDE containers; `pm.max_children = 50` in `docker/php-fpm.conf` saturates if ~30+ students boot containers simultaneously.
- Scaling path: Move workspace orchestration to async workers, enforce realistic memory/PID/disk limits (`512m–1024m` for intro labs), replace host-port mapping with internal overlay networking + dynamic reverse proxy (Traefik/Caddy/Consul), and shard containers across multiple Docker/Kubernetes worker nodes.

**Unbounded Disk Growth (Orphan Docker Volumes & Extension Artifacts):**
- Current capacity: Unlimited per-workspace Docker volumes (`vl_ws_{id}_data`) with no `disk_mb` enforcement (`WorkspaceQuota::$disk_mb` is never applied to Docker).
- Limit: Host root filesystem fills up because `WorkspaceCleanup` deletes DB rows without removing Docker volumes, and `ExtensionController::destroy()` never deletes `.vsix` files from `storage/app/extensions`.
- Scaling path: Enforce volume size limits (XFS project quotas / storage driver `size` option), call `CodeServerManager::deleteWorkspace()` in `WorkspaceCleanup`, delete `.vsix` files on extension removal, and move extension/GDPR artifacts to S3-compatible object storage.

**Single-Node WebSockets, Local Storage & Unchunked Batch Jobs:**
- Current capacity: `docker-compose.prod.yml` runs a single `visionlab-reverb` instance, local disk storage (`storage/app`), and unchunked `->get()` loops in `ExtensionController::toggleGlobal()`, `routes/console.php`, `CourseController::roster()`, and admin ERP indexes.
- Limit: Multi-instance Laravel app servers cannot share `storage/app/extensions` or `gdpr_exports`; batch operations spike memory and timeout beyond ~1,000+ active users/workspaces.
- Scaling path: Enable Reverb Redis pub/sub scaling, migrate uploads/artifacts to S3 (`FILESYSTEM_DISK=s3`), paginate all admin/roster queries, and use `chunkById()` + queued batches (`Bus::batch`) for fan-out jobs.

## Dependencies at Risk

**Wildcard (`"*"`) Composer Constraints & Disabled Platform Check:**
- Risk: `composer.json` pins `maatwebsite/excel`, `minishlink/web-push`, and `predis/predis` to `"*"` (lines 15–17) and sets `"platform-check": false` (line 86). Any `composer update` can pull breaking major versions silently, and runtime PHP/extension mismatches are suppressed until crash.
- Impact: Unpredictable builds, subtle serialization/API breaks in WebPush/Redis/Excel, and runtime fatals in production.
- Migration plan: Pin explicit caret constraints matching `composer.lock` (e.g., `^3.1`, `^9.0`, `^2.2`) and re-enable `"platform-check": true` with `"php": "^8.3"`.

**Suppressed Composer Security Advisories:**
- Risk: `composer.json` (lines 87–94) explicitly ignores 4 Packagist security advisories (`PKSA-mdq4-51ck-6kdq`, `PKSA-8qx3-n5y5-vvnd`, `PKSA-q46n-4fdk-zjr4`, `PKSA-qzrn-rnz3-85w1`) instead of upgrading the affected transitive/direct packages.
- Impact: Known CVEs remain unpatched in production and hidden from `composer audit`.
- Migration plan: Run `composer audit --no-interaction` without ignores, upgrade vulnerable packages (likely `phpoffice/phpspreadsheet` via `maatwebsite/excel` or `symfony/*`), and remove the `audit.ignore` block.

**Missing Enterprise & Static Analysis Packages:**
- Risk: `composer.json` lacks `laravel/horizon` (breaking `docker-compose.prod.yml` `visionlab-queue`), `spatie/laravel-activitylog` (breaking `ExecutePlanJob`), `mews/purifier` / HTMLPurifier (referenced in `AGENTS.md`), `laravel/pulse`, and any PHP static analysis tool (`larastan/larastan` / `phpstan/phpstan`).
- Impact: Runtime crashes on missing classes/commands; basic bugs like missing `Model` and `Request` imports slip straight into `main`.
- Migration plan: Install `larastan/larastan` in `require-dev` and run it in CI; install `laravel/horizon` (or change `visionlab-queue` command to `php artisan queue:work redis`) and remove `activity()` calls in favor of `AuditLog::record()`.

**Aging Frontend Packages & Unpinned CDN Scripts Without SRI:**
- Risk: `package.json` pins `tailwindcss ^3.1.0` and `alpinejs ^3.4.2` (behind current Tailwind v3.4+/v4 and Alpine v3.14+ security/bugfix releases). Multiple Blade views and `public/sw.js` load third-party scripts (`cdn.jsdelivr.net/npm/chart.js` unversioned, `highlight.js`, `apexcharts`, `workbox-sw.js`) from external CDNs with no Subresource Integrity (`integrity`) hashes.
- Impact: Supply-chain compromise of an unpinned CDN script executes with full origin privileges; CSP must be weakened to allow multiple third-party CDNs.
- Migration plan: Bundle Chart.js, Highlight.js, ApexCharts, and Workbox locally via Vite (or add pinned versions + `integrity` SRI attributes) and upgrade Alpine.js / Tailwind CSS to current patched versions.

## Missing Critical Features

**Core LMS Modules (Models Exist, Zero Controllers/Routes/UI — Still Open from July 2026 Audit):**
- Problem: Despite database tables and models existing (and tabs rendered with dead `<a href="#">` buttons in `resources/views/courses/show.blade.php`), 7 core controllers do not exist: `ForumController` (`ForumTopic`, `ForumPost`), `QuizController` (`Quiz`, `Question`, `QuizAttempt`), `CertificateController` (`Certificate`), `AttendanceController` (`Attendance`, `AttendanceLog`), `PaymentController` (`Transaction`, `FeeChallan` student payment flow), `GradebookController` (`GradeItem`, `GradingRubric`, `Rubric`, `RubricCriteria`), and `CourseContentController` (`CourseModule`, `Lesson`).
- Blocks: Course curriculum authoring, online quizzes/assessments, course discussion forums, certificate generation, manual attendance tracking/reporting, online fee payment, and rubric-based/bulk gradebook management.

**Removed Collaborative & Platform Pillars (Tables Dropped in `2026_07_13` Migration):**
- Problem: Real-time collaboration rooms, Jitsi video conferencing, session recording & approval, and student cloud deployments (`Vercel`/`Railway` — `app/Services/Deployment/` is empty) were dropped from the database and routes (`routes/channels.php` only authorizes `user.{id}`). `extensions/visionlab-collab` still attempts to connect to removed `collab.ws-{id}` channels and `/api/workspace/{slug}/video/start`.
- Blocks: Three of the four headline VisionLab capabilities declared in `AGENTS.md` (Jitsi video sessions, multi-user collaborative editing/chat, and student deployment).

**Incomplete ERP, Notification & AI Governance Workflows:**
- Problem:
  - Library management (`AdminLibraryController`) has no book issue, return, renewal, or overdue fine logic (`BookIssue` is unused).
  - HR/Payroll (`AdminEmployeeController`) has no payroll generation, approval, or payslip workflow (`Payroll` is unused).
  - Course batches (`AdminCourseBatchController`) are not enforced on student self-enrollment or course access.
  - `NotificationPreference` (channel prefs & quiet hours) is never consulted by any notification class, and outgoing `Webhook` records are never triggered.
  - AI governance lacks token/cost accounting, content safety filtering, `.visionlab_memory.md` auto-approve handling, and scoped Sanctum token `Abilities` enum.
- Blocks: Production university ERP operations, user notification controls, webhook integrations, and OWASP LLM Top 10 compliance.

## Test Coverage Gaps

**Broken / Failing Existing Feature Test Suites (CI Blocking):**
- What's not tested / broken: At least 8 existing test files in `tests/Feature/` are broken against the current codebase:
  - `VideoRoomControllerTest.php`, `DeploymentControllerTest.php`, `RoomControllerTest.php`, `RecordingControllerTest.php`: reference permanently deleted models, events, tables, and routes.
  - `RealtimeCollaborationTest.php`: tests `presence-workspace.1` channel removed from `routes/channels.php`.
  - `WorkspaceSecurityTest.php`: passes numeric `$workspace->id` to `/workspace/{workspace}/...` routes that now bind by `slug`.
  - `SecurityHardeningTest.php`: asserts HTTP 403 when writing `.env`, which currently fails because `WorkspaceFileController::isDangerousPath()` does not block `.env`.
  - `AiControllerTest.php`: asserts local host file modification on `applyPatch()`, which now targets Docker volumes.
- Files: `tests/Feature/VideoRoomControllerTest.php`, `tests/Feature/DeploymentControllerTest.php`, `tests/Feature/RoomControllerTest.php`, `tests/Feature/RecordingControllerTest.php`, `tests/Feature/RealtimeCollaborationTest.php`, `tests/Feature/WorkspaceSecurityTest.php`, `tests/Feature/SecurityHardeningTest.php`, `tests/Feature/AiControllerTest.php`
- Risk: `php artisan test` fails immediately in `.github/workflows/deploy.yml` and `production.yml`, blocking automated deployment and hiding new regressions.
- Priority: High

**Zero Unit Tests for Core Services & Domain Models:**
- What's not tested: `tests/Unit/` contains only the default `tests/Unit/ExampleTest.php` (`assertTrue(true)`). None of `CodeServerManager`, `AiService`, `AiSandbox`, `GamificationService`, `WebPushService`, `AuditService`, `WorkspaceQuota::resolveFor()`, or `User::addXp()` have unit tests.
- Files: `tests/Unit/ExampleTest.php`, `app/Services/CodeServerManager.php`, `app/Services/AiService.php`, `app/Services/AiSandbox.php`, `app/Services/GamificationService.php`, `app/Services/WebPushService.php`, `app/Models/User.php`, `app/Models/WorkspaceQuota.php`
- Risk: Fatal runtime bugs (`User::addXp()` missing `Model` import, `CodeServerManager` shell injection and return-type crash, `WebPushService` unused `flush()`) go completely undetected.
- Priority: High

**Shallow / Theater Security & Authorization Tests:**
- What's not tested:
  - `AiAgentAuthorizationTest.php` only tests a hardcoded 403 stub closure (`POST /api/ai/execute`) and never tests `AiController` IDOR on `chatCompletions`, `pendingPatches`, `approvePatch`, `rejectPatch`, `executePlan`, or `AiService`'s `run_terminal` tool.
  - `ApiRateLimitingTest.php` and `ForensicsAuditTest.php` send only a single request and never assert HTTP 429 throttling or DB persistence.
  - No tests verify suspended-user API denial, shell metacharacter rejection in file I/O, or cross-user impersonation CSRF protection.
- Files: `tests/Feature/AiAgentAuthorizationTest.php`, `tests/Feature/ApiRateLimitingTest.php`, `tests/Feature/ForensicsAuditTest.php`, `app/Http/Controllers/Api/AiController.php`, `app/Http/Controllers/WorkspaceFileController.php`
- Risk: False sense of security compliance while critical IDOR, RCE, and bypass paths remain exploitable.
- Priority: High

**Untested Controllers, Background Jobs, Console Commands & Frontend/E2E Flows:**
- What's not tested:
  - Controllers with zero dedicated feature tests: `WorkspaceFileController` (full CRUD + traversal), `CourseExtensionController` (would have caught missing `Request` import), `WorkspaceExtensionManagerController`, `WebhookController` (HMAC verification), `HealthController` (`/healthz` & `/admin/status`), `PushSubscriptionController`, `PublicProfileController`, `GlobalAnnouncementController`, `AnnouncementReadController`, `GamificationController`, `ProgressController`, `AnalyticsController`, `VisionGuardController`, `AdminWorkspaceController` (would have caught `collaborators` crash), `AdminQuotaController`, `AdminSystemController`, `AdminTemplateController`, `AdminAuditController`, `AdminAnalyticsController`, and all 6 ERP controllers (`AdminCampus`, `AdminDepartment`, `AdminCourseBatch`, `AdminEmployee`, `AdminFeeChallan`, `AdminLibrary`, `AdminWebhook`).
  - Zero tests for any of the 7 Jobs (`app/Jobs/*.php`) or 7 Console Commands (`app/Console/Commands/*.php`).
  - Zero browser/E2E tests (no Dusk or Playwright suite) covering IDE iframe boot, exam lockdown, or VS Code extension Webview rendering (`PatchReviewer.ts`).
- Files: `app/Http/Controllers/`, `app/Http/Controllers/Admin/`, `app/Jobs/`, `app/Console/Commands/`, `extensions/visionlab-collab/src/`
- Risk: Fatal production crashes (`AdminWorkspaceController::show()`, `CourseExtensionController::update()`, `ExecutePlanJob`, `CsvEnrollmentJob`, `SubmissionController::ide()`) reach production undetected.
- Priority: Medium (High for `WorkspaceFileController`, `AdminWorkspaceController`, `CourseExtensionController`, `WebhookController`, and scheduled workspace lifecycle commands)

---

*Concerns audit: 2026-11-24*

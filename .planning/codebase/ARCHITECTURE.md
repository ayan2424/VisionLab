# Architecture

**Analysis Date:** 2026-11-24

## Pattern Overview

**Overall:** Modular Laravel 11 Monolith + Containerized Cloud IDE Orchestrator + Sovereign VS Code Extension Platform

VisionLab is a server-rendered Laravel 11 web application (PHP 8.2+/8.3) that unifies a university Learning Management System (LMS), Institute ERP administration, AI-assisted coding governance, and dynamically provisioned `code-server` (VisionLab IDE) Docker workspaces. The browser delivers Blade + Tailwind CSS + Alpine.js pages while embedding per-workspace IDE containers via iframe behind a reverse proxy.

**Key Characteristics:**
- **Laravel 11 Slim Bootstrap (`bootstrap/app.php`):** Routing, middleware stack, CSRF exclusions, cookie encryption exclusions, and API throttling are registered centrally in `bootstrap/app.php` rather than legacy `Http/Kernel.php` classes.
- **Role-Based Layered Authorization:** Access control combines route-level middleware (`role:admin`, `auth`, `verified`, `account.active`), Sanctum token authentication (`auth:sanctum`), and 14 Eloquent model Policies under `app/Policies/`.
- **Docker-Native Ephemeral Compute + Persistent Volumes:** `app/Services/CodeServerManager.php` orchestrates isolated `visionlab/workspace:latest` containers (`vl_ws_{id}`) backed by persistent Docker volumes (`vl_ws_{id}_data`), shared Nix store volumes (`visionlab_nix_store`), and declarative `.vision/dev.nix` environments.
- **Human-In-The-Loop (HITL) AI Governance:** `app/Services/AiService.php` exposes an OpenAI-compatible SSE endpoint (`/api/ai/v1/chat/completions`) proxying to Anthropic Claude with tool calling. File mutations are intercepted by `app/Services/AiSandbox.php` and persisted in `ai_pending_patches` for explicit human review before writing.
- **Architectural Transition / Legacy Drift:** Migration `database/migrations/2026_07_13_000000_remove_incomplete_features_tables.php` permanently dropped 10 tables for Jitsi video rooms, recordings, student deployments, and multi-user collaboration rooms/chat. While the active runtime reflects this slimmed scope, residual artifacts (models, events, extension TypeScript panels, Docker Jitsi service, config keys, and tests) still exist in the repository alongside newer post-audit features (Gamification XP/badges and WebPush delivery).

## Layers

**Presentation & Frontend Assets:**
- Purpose: Renders public marketing pages, authenticated student/instructor/admin dashboards, LMS views, and the full-screen IDE shell; manages client-side interactivity, PWA service workers, and WebSocket subscriptions.
- Location: `resources/views/`, `resources/js/`, `resources/css/`, `public/`
- Contains: Blade layouts (`resources/views/layouts/`), reusable anonymous Blade components (`resources/views/components/` including `<x-frontend-header />` and `<x-frontend-footer />`), class-backed Breeze layout components (`app/View/Components/`), Alpine.js entry (`resources/js/app.js`), lazy Laravel Echo initializer (`resources/js/echo.js`), and PWA registration/WebPush client (`public/pwa.js`, `public/serviceworker.js`, `public/sw.js`).
- Depends on: Vite 6 (`vite.config.js`), Tailwind CSS 3 (`tailwind.config.js`), Laravel Blade engine, Axios, Laravel Echo + `pusher-js`.
- Used by: All web HTTP controllers returning `view(...)`.

**HTTP Routing & Middleware Pipeline:**
- Purpose: Dispatches web, auth, API, broadcast channel, and console routes while enforcing maintenance mode, OWASP security headers, account status checks, RBAC, and per-user rate limits.
- Location: `bootstrap/app.php`, `routes/`, `app/Http/Middleware/`
- Contains: Route definitions (`routes/web.php`, `routes/api.php`, `routes/auth.php`, `routes/channels.php`, `routes/console.php`), global web middleware (`CheckMaintenanceMode`, `SecurityHeaders`, `CheckAccountStatus`), and route alias `role` (`RoleMiddleware`).
- Depends on: `App\Models\SystemConfig`, `App\Models\User`, Laravel Sanctum, rate limiters defined in `app/Providers/AppServiceProvider.php`.
- Used by: `public/index.php` on every incoming HTTP request.

**Controllers & Form Requests (Application Layer):**
- Purpose: Validates HTTP inputs, invokes Policy authorization (`$this->authorize(...)`), coordinates Domain Services and Eloquent models, and returns Blade views, redirects, JSON, or SSE streams.
- Location: `app/Http/Controllers/` (root LMS/Workspace/Telemetry controllers, `Admin/`, `Api/`, `Auth/`), `app/Http/Requests/`
- Contains: 51 concrete controller classes extending abstract `app/Http/Controllers/Controller.php` (which mixes in `AuthorizesRequests`) and 25 `FormRequest` validation classes.
- Depends on: `app/Policies/`, `app/Services/`, `app/Models/`, `app/Events/`, `app/Jobs/`.
- Used by: Route groups in `routes/web.php`, `routes/api.php`, and `routes/auth.php`.

**Authorization Layer (Policies & Gates):**
- Purpose: Centralizes entity-level permission decisions across courses, assignments, submissions, workspaces, AI patches, extensions, quotas, and admin resources.
- Location: `app/Policies/`, `app/Providers/AppServiceProvider.php`
- Contains: 14 policy classes (`WorkspacePolicy`, `CoursePolicy`, `AssignmentPolicy`, `SubmissionPolicy`, `AiActionPolicy`, `AnalyticsPolicy`, `AnnouncementPolicy`, `AuditLogPolicy`, `EnrollmentPolicy`, `ExtensionPolicy`, `NotificationPolicy`, `RecordingPolicy`, `UserPolicy`, `WorkspaceQuotaPolicy`).
- Depends on: `App\Models\User` role helpers (`isAdmin()`, `isInstructor()`, `isStudent()`) and domain model ownership/enrollment relations.
- Used by: Controllers (`$this->authorize(...)`), Form Requests, and Blade directives (`@can`).

**Domain Services Layer:**
- Purpose: Encapsulates complex orchestration, external API proxying, container execution, sandboxing, audit logging, gamification rules, and WebPush delivery.
- Location: `app/Services/`
- Contains: `CodeServerManager.php` (Docker IDE lifecycle & volume file I/O), `AiService.php` (Anthropic SSE proxy & tool-use loop), `AiSandbox.php` (patch preparation, snapshots, diffing, apply/rollback), `AuditService.php` (structured audit helper), `GamificationService.php` (XP awards & badge evaluation), `WebPushService.php` (VAPID push dispatch via `minishlink/web-push`).
- Depends on: `Symfony\Component\Process\Process`, `GuzzleHttp\Client`, Laravel HTTP client, Eloquent models, `config/visionlab.php`, `config/webpush.php`.
- Used by: Controllers, Jobs, Observers, Listeners, and Artisan Commands.

**Data & Persistence Layer (Eloquent Models & Migrations):**
- Purpose: Defines relational schema, attribute casting (including encrypted tokens), scopes, domain helpers, and relationships.
- Location: `app/Models/`, `database/migrations/`, `database/seeders/`, `database/factories/`
- Contains: 57 Eloquent models, 54 migrations, 9 seeders, and 14 factories.
- Depends on: MySQL 8.0 (production via `docker-compose.prod.yml`) or SQLite (local/testing via `phpunit.xml` `:memory:` and `database/database.sqlite`).
- Used by: Services, Controllers, Policies, Jobs, Observers, and Commands.

**Event-Driven, Queued & Scheduled Processing:**
- Purpose: Handles asynchronous container maintenance, extension sync, streak updates, analytics capture, and real-time notifications without blocking HTTP requests.
- Location: `app/Events/`, `app/Listeners/`, `app/Observers/`, `app/Jobs/`, `app/Notifications/`, `app/Console/Commands/`
- Contains: 6 Events, 2 Listeners, 1 Observer (`AnalyticsObserver`), 7 Jobs, 5 Notifications, and 7 Artisan Commands scheduled in `routes/console.php`.
- Depends on: Laravel Queue (Redis/Horizon in prod, `sync` in tests), Laravel Reverb broadcaster, `CodeServerManager`, `GamificationService`, `WebPushChannel`.
- Used by: Model lifecycle hooks (`AppServiceProvider::boot()`), controller dispatches, and `php artisan schedule:run`.

**Containerized IDE & VS Code Extension Layer:**
- Purpose: Executes user code inside Nix-powered `visionlab-server` (`code-server`) Docker containers and integrates in-IDE collaboration, patch review, and environment rebuild triggers.
- Location: `docker/workspace/`, `extensions/visionlab-collab/`
- Contains: Custom Nix + `visionlab-ide-1.0.0-linux-amd64.tar.gz` image definition (`docker/workspace/Dockerfile`), bundled AI agent VSIX (`docker/workspace/extensions/visionlab-agent-1.3.39.vsix`), TypeScript VS Code extension (`extensions/visionlab-collab/src/`), and dynamically injected `visionlab-nix-watcher` extension (generated inline in `CodeServerManager::startWorkspace()`).
- Depends on: Host Docker daemon (`/var/run/docker.sock`), Laravel API endpoints (`/api/ai/v1`, `/api/ai/patches/*`, `/api/workspace/{slug}/trigger-rebuild`), and Reverb WebSocket server.
- Used by: Embedded `<iframe id="vscode-frame">` inside `resources/views/workspace.blade.php`.

## Data Flow

### 1. Auth & RBAC Flow

1. **Authentication Entry:** Public self-registration is intentionally disabled in `routes/auth.php` (`GET /register` redirects to `GET /login`; `POST /register` is commented out). Accounts are provisioned by admins via `AdminUserController::store()` (`POST /admin/users`, generating `STU-XXXXXX` IDs for students) or bulk CSV import (`EnrollmentController::importCsv()`).
2. **Session Login & Streak Hook:** `AuthenticatedSessionController::store()` authenticates against the `web` guard (`config/auth.php`) using `App\Http\Requests\Auth\LoginRequest`. Successful login fires `Illuminate\Auth\Events\Login`, invoking `App\Listeners\UpdateUserStreak` (registered in `app/Providers/AppServiceProvider.php`) to update `current_streak` / `longest_streak`, record a `user_login` `AnalyticsEvent`, and evaluate badges via `GamificationService::evaluateBadges()`.
3. **Web Middleware Stack (`bootstrap/app.php`):** Every web request traverses `CheckMaintenanceMode` (bypasses for admins and `/admin/*`, `/login`, `/logout`, `/healthz`), `SecurityHeaders` (injects CSP, HSTS, `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options`, `Permissions-Policy`), and `CheckAccountStatus` (auto-logs out and invalidates sessions of users with `status === 'suspended'`).
4. **Role & Policy Enforcement:** Admin routes under `/admin` enforce `['auth', 'verified', 'role:admin', 'throttle:admin']` via `App\Http\Middleware\RoleMiddleware`. Resource controllers call `$this->authorize(...)` against `app/Policies/*` (note: only `Course::class => CoursePolicy::class` is explicitly mapped in `AppServiceProvider`; other policies rely on Laravel auto-discovery).
5. **API Auth (Sanctum & Workspace Tokens):** Routes in `routes/api.php` use `auth:sanctum` (stateful SPA cookies via `$middleware->statefulApi()` plus Bearer personal access tokens created via `$user->createToken('workspace')` inside `CodeServerManager`). Exception: `POST /api/workspace/{slug}/trigger-rebuild` authenticates directly against `$workspace->token`, and `POST /api/webhook/deploy` verifies GitHub `X-Hub-Signature-256` HMAC signatures (CSRF-exempt in `bootstrap/app.php`).
   - *Divergence from `AGENTS.md`:* No `Abilities` enum exists under `app/`; Sanctum tokens are minted with default unrestricted (`['*']`) abilities via `createToken('workspace')`.

### 2. Classroom & LMS Flow

1. **Course Creation & Discovery:** Instructors/admins create courses (`CourseController::store()` via `StoreCourseRequest`), generating unique slugs and enrollment codes. `CourseController::index()` scopes listings by role (all for admin, `instructor_id` for instructor, active `enrollments` for students).
2. **Enrollment Paths:** Students join via enrollment code (`POST /join` -> `EnrollmentController::joinByCode()`, guarded by `CoursePolicy::enroll`), or instructors add students via email invite (`POST /courses/{course:slug}/invite`), Student ID (`POST /courses/{course:slug}/enrollments/add`), or CSV upload (`POST /courses/{course:slug}/import-csv`). (Note: `App\Jobs\CsvEnrollmentJob` exists as a queued alternative, though `EnrollmentController::importCsv()` currently parses synchronously inline.)
3. **Assignment & Workspace Provisioning:** Instructors create assignments (`AssignmentController::store()`) optionally linked to a `WorkspaceTemplate`, `mode` (`normal`/`exam`), `allow_ai`, and `is_locked`. When a student clicks Start (`POST /assignments/{assignment}/start` -> `SubmissionController::start()`):
   - Authorizes via `AssignmentPolicy::start`.
   - Creates/retrieves `Submission` (`status: in_progress`), tracks `assignment_started`, and awards 10 XP via `GamificationService`.
   - Provisions a governed `Workspace` (`type: governed`, linked to `assignment_id` and `course_id`), calls `CodeServerManager::startWorkspace($workspace)`, and redirects to `GET /assignments/{assignment}/ide` (`SubmissionController::ide()`).
4. **Submission & Grading:** Inside `resources/views/workspace.blade.php`, students optionally save snapshots (`POST /assignments/{assignment}/save-snapshot`) or submit (`POST /assignments/{assignment}/submit` -> `SubmissionController::submit()`, marking `submitted` or `late` relative to `due_date`). Instructors grade via `GET /submissions` (`SubmissionController::queue()`) and `PATCH /submissions/{submission}/grade` (`SubmissionController::grade()` using `GradeSubmissionRequest`):
   - Persists `grade`, `feedback`, `graded_by`, and `status = 'graded'`.
   - Awards XP (`grade * 2`, halved if late) via `GamificationService::awardXpAndEvaluate()`.
   - Broadcasts `App\Events\SubmissionGraded` on `PrivateChannel('user.' . $studentId)` (caught non-fatally if Reverb is offline).
5. **Announcements & Dashboards:** `DashboardController::index()` redirects admins to `/admin`, and renders role-tailored views (`dashboard.instructor` or `dashboard.student`). Course announcements (`AnnouncementController`) and global announcements (`GlobalAnnouncementController`) track per-user read state via `announcement_reads` (`AnnouncementReadController`).
   - *Architectural Gap (matches `VISIONLAB_AUDIT_REPORT.md` findings):* Extended LMS/ERP tables migrated on `2026-07-11` (`course_modules`, `lessons`, `quizzes`, `questions`, `quiz_attempts`, `forum_topics`, `forum_posts`, `certificates`, `rubrics`, `grading_rubrics`, `grade_items`, `attendances`, `attendance_logs`, `transactions`, `payrolls`, `book_issues`) have Eloquent models under `app/Models/` (and `CourseController::show()` eager-loads `modules.lessons` and `modules.quizzes`), yet lack CRUD controllers and routes; only Admin ERP CRUD exists for `campuses`, `departments`, `batches`, `challans`, `employees`, and `library` (books only).

### 3. Workspace IDE & Code-Server Lifecycle

1. **Creation & Quota Resolution:** `WorkspaceController::store()` resolves active limits via `WorkspaceQuota::resolveFor($user->id, $courseId)` in strict precedence order: **User scope -> Course scope -> Role scope -> Global scope -> Hardcoded platform fallback** (`2048MB` RAM, `1024` CPU shares, `1024MB` disk, `120m` timeout, `max_workspaces = 3`). If under `max_workspaces`, creates a `Workspace` record (`status: pending`, unique auto-slug in `Workspace::boot()`, 64-char `token`) and redirects to `GET /workspace/{workspace}`.
2. **Preloader & Asynchronous Boot Handshake:** `WorkspaceController::show()` renders `resources/views/workspace.blade.php` with `needsBoot = ($workspace->status !== 'running')` and an animated 5-step preloader (`#premium-loader`). On load, client JS calls `POST /workspace/{workspace}/start` (`WorkspaceController::start()`, `set_time_limit(600)`):
   - `CodeServerManager::startWorkspace()` creates/chowns Docker volume `vl_ws_{id}_data` (`1000:1000`), seeds starter code or clones `WorkspaceTemplate::$git_url`, and writes `.vision/dev.nix` & `.vision/bootstrap.sh` (protecting `.vision/` ownership as `root:root`).
   - Checks Docker availability; if unavailable (local dev without Docker), returns `devFallback()` pointing to `VSCODE_SERVER_URL` (default `http://127.0.0.1:8099/`).
   - Allocates a free port in `9000–9999`, resolves marketplace lockdown (`SystemConfig::getVal('global_allow_marketplace')` + `$course->allow_marketplace` -> `EXTENSIONS_GALLERY={"serviceUrl":""}`), and executes `docker run -d --init --cap-drop ALL --tmpfs /tmp --tmpfs /run --network visionlab-workspace-net` mounting `vl_ws_{id}_data`, shared `visionlab_nix_store:/nix`, and host `storage/app/extensions:/var/opt/extensions:ro`.
   - Injects environment variables (`VISIONCODE_API_TOKEN`, `VISIONCODE_WORKSPACE_ID`, `VISIONCODE_API_URL`, `OPENAI_API_BASE`, `OPENAI_API_KEY`), starts `visionlab-server`/`entrypoint.sh` wrapped in `nix-shell .vision/dev.nix` if present, purges `github.copilot*`, injects `visionlab-nix-watcher` extension, appends Nix auto-activation to `/home/coder/.bashrc`, runs `.vision/bootstrap.sh`, writes `/home/coder/.continue/config.json`, and installs enabled `WorkspaceExtension` rows (verifying SHA256 checksums for `.vsix` files, firing `ExtensionIntegrityFailed` / throwing `ExtensionIntegrityException` on mismatch).
   - Returns JSON `{status: 'running', url, port}` plus an unencrypted `key` cookie (excluded in `bootstrap/app.php`) scoped to `/ide/{port}` (production) or `/` (local).
3. **Synchronized `/healthz` Polling:** Client JS in `resources/views/workspace.blade.php` polls `GET /workspace/{workspace}/ping` every 3 seconds. `WorkspaceController::ping()` calls `CodeServerManager::isWorkspaceReady()`, which performs a 2-second HTTP GET to `http://127.0.0.1:{$workspace->port}/healthz`. Once `ready: true`, client JS sets `<iframe id="vscode-frame">.src = data-src`, waits for iframe `load` (with a 10-second fallback), fades out `#premium-loader`, and flips status to `IDE Active`.
4. **Rebuild / Restart & Idle Reaping:** Editing `.vision/dev.nix` triggers the injected `visionlab-nix-watcher` extension to POST `/api/workspace/{slug}/trigger-rebuild` (firing `WorkspaceRebuildingEvent`), or users click Restart/Rebuild in the topbar (`POST /workspace/{slug}/restart/process` or `rebuild/process`), stopping and restarting the container while preserving `vl_ws_{id}_data`. Scheduled Artisan commands `workspaces:stale-heartbeat` (every 5 min) and `workspaces:cleanup` (daily) stop idle containers and purge >30-day stopped records.
5. **Sandboxed File I/O API:** Exposed via BOTH authenticated web routes (`/workspace/{workspace}/files|read-file|download-file|write-file|create-file|delete-file|rename-file` in `WorkspaceController`) and Sanctum API routes (`/api/workspace/{slug}/files|read-file|write-file|create-file|delete-file|rename-file` in `WorkspaceFileController`, throttled by `file-api` at 100/min). All operations delegate to `CodeServerManager::executeDockerFileCommand()`, running `docker exec -u 1000` when the container is running or spawning an ephemeral `--rm` Alpine container mounted to `vl_ws_{id}_data` when stopped.
   - *Note:* `CodeServerManager::resolveSecurePath()` is deprecated (returns `null`) after migrating from host directories to Docker volumes; path-traversal checks currently rely on `str_contains(..., '..')` in `WorkspaceFileController::isDangerousPath()` and `WorkspaceController::downloadFile()`, whereas direct `CodeServerManager` shell commands interpolate paths into `sh -c`.

### 4. Real-Time Collaboration & WebSocket Broadcasting

1. **WebSocket Infrastructure:** Configured for Laravel Reverb (`config/broadcasting.php`, `config/reverb.php`, `docker-compose.prod.yml` `visionlab-reverb` service on port 8080, proxied by Nginx `/app` and `/apps` in `docker/nginx/default.conf` and `visionlab_nginx.conf`). Frontend loads Laravel Echo lazily via `window.initEcho(reverb)` in `resources/js/echo.js`.
2. **Active Channel — User Grading Notifications:** `routes/channels.php` currently defines a single authorized channel: `Broadcast::channel('user.{id}', ...)`. When `SubmissionController::grade()` broadcasts `SubmissionGraded` (`.submission.graded`), `resources/views/layouts/app.blade.php` listens on `window.Echo.private('user.' + userId)`, displays a toast, and persists recent alerts in `localStorage['vc_notifs']`.
3. **Removed Multi-User Collaboration & Channel Drift:**
   - Migration `2026_07_13_000000_remove_incomplete_features_tables.php` dropped `rooms`, `room_members`, `chat_messages`, `workspace_collaborators`, and `collab_sessions`. Multi-user room/chat controllers and routes were removed.
   - *Current Architectural Disconnect:* `App\Events\PatchProposed` broadcasts to `PresenceChannel('collab.' . $roomSlug)` and `PrivateChannel('workspace.' . $roomSlug . '.patches')`, `App\Events\WorkspaceRebuildingEvent` broadcasts to `PresenceChannel('collab.ws-' . $workspace->id)`, and legacy events `App\Events\CodeUpdated` / `App\Events\CursorMoved` target `PresenceChannel('workspace.' . $roomId)`. Similarly, `extensions/visionlab-collab/src/managers/RealtimeManager.ts` joins `collab.ws-{workspaceId}` and whispers `client-CodeUpdated` / `client-CursorMoved` via `DocumentSync.ts` / `CursorSync.ts`. However, **none of those channels are authorized in `routes/channels.php`**, and `resources/views/workspace.blade.php` does not call `window.initEcho()`.

### 5. AI Agent & Human-in-the-Loop Patch Review Flow

1. **IDE Request -> OpenAI-Compatible Proxy:** Inside the container, VisionLab Agent (Continue fork configured via `/home/coder/.continue/config.json` written by `CodeServerManager::setupWorkspaceExtensions()`) sends OpenAI-format chat completion requests with `Authorization: Bearer <sanctum-token>` and optional `X-Workspace-Id` / `X-Ai-Mode` (`CHAT` | `PLAN` | `AGENT`) headers to `POST /api/ai/v1/chat/completions` (`Api\AiController::chatCompletions()`, throttled by `ai` at 30/min).
2. **Governance Checks & Anthropic Tool Loop:** `AiController` returns a `StreamedResponse` (`text/event-stream`, `X-Accel-Buffering: no`) iterating over `AiService::handleChatCompletion()`:
   - Logs request to `ai_actions_log`.
   - Enforces daily per-user quota (`config('visionlab.ai.daily_limit', 100)`) and Exam Mode / `allow_ai` assignment lockdowns.
   - Falls back to a deterministic Demo Mode (creating a sample `demo.php` patch) if `VISIONLAB_AI_API_KEY` / `ANTHROPIC_API_KEY` is unset.
   - Translates OpenAI messages to Anthropic Messages API (`https://api.anthropic.com/v1/messages`) and loops up to 10 turns (`$loopSafetyLimit`), exposing 5 tools: `read_file`, `list_directory`, `search_codebase`, `run_terminal`, and `propose_patch`.
3. **HITL Patch Proposal (`ai_pending_patches`):** Direct execution is blocked (`POST /api/ai/execute` returns HTTP 403). When the model calls `propose_patch`, `AiSandbox::preparePatch()`:
   - Rejects sensitive paths (`.env`, `vendor/`, `storage/`, `bootstrap/`, `node_modules/`, `.git/`).
   - Reads current content via `CodeServerManager::readFile()`, applies search/replace, generates a unified diff (`diff -u` or PHP line diff fallback), saves a rollback baseline in `ai_snapshots`, and inserts an `AiPendingPatch` (`status: pending`).
   - Dispatches `App\Events\PatchProposed`.
4. **Human Review & Application:** The `PatchReviewer` VS Code webview (`extensions/visionlab-collab/src/panels/PatchReviewer.ts`) fetches `GET /api/ai/patches/pending?workspace_id={id}` (`AiController::pendingPatches()`), renders color-coded diffs, and sends `POST /api/ai/patches/{patch}/approve` or `/reject`:
   - `approvePatch()` invokes `AiSandbox::applyPatch()`, which writes `patched_content` via `CodeServerManager::writeFile()`, transitions patch `status` to `approved`, and records `apply_patch` in `ai_actions_log`.
   - `rejectPatch()` sets patch `status` to `rejected`. (`AiSandbox::rollbackPatch()` also exists to restore from `ai_snapshots`, though no HTTP route exposes rollback yet.)
   - `POST /api/ai/patches/execute-plan` triggers `AiService::executePlan()` synchronously (note: `App\Jobs\ExecutePlanJob` exists separately expecting a `patchId` and calling Spatie `activity()`, which is not installed in `composer.json`).
   - *Additional Note:* `App\Http\Controllers\Api\AiConfigController` queries `AiProvider`/`AiModel` tables for dynamic model config, but no route in `routes/api.php` is currently bound to it.

### 6. Video Conferencing (Jitsi), Forensics, Analytics, Gamification & PWA Flows

1. **Video Conferencing (Jitsi) — Removed from Active App Layer:**
   - Tables `video_rooms` and `video_attendances` (along with `recordings` and `recording_audit_logs`) were permanently dropped by `database/migrations/2026_07_13_000000_remove_incomplete_features_tables.php`. There is no `JitsiService` or `VideoRoomController` in `app/`.
   - Residual integration points remain in `config/visionlab.php` (`jitsi.*`), `docker-compose.prod.yml` (`visionlab-jitsi` container), `HealthController::runProbes()` (`jitsi_config` check), `AdminSystemController` (`video_calls_enabled` flag), and `extensions/visionlab-collab/src/panels/VideoPanel.ts` (which expects `POST /api/workspace/{slug}/video/start`).
2. **VisionGuard Forensics & Telemetry:**
   - IDE/frontend telemetry posts to `POST /api/visionguard/log` (`VisionGuardController::logEvent()`, custom 120/min per-user limiter) storing records in `analytics_events`; admins inspect keystroke-to-paste/AI confidence ratios via `GET /api/visionguard/forensics`.
   - Extension keystroke/AI deltas sync via `POST /api/workspace/{slug}/forensics/sync` (`Api\ForensicsController::sync()`), updating per-submission `SubmissionForensic` percentages (`human_percentage`, `ai_percentage`).
   - Exam lockdown UI (`resources/views/components/exam-lockdown.blade.php` + inline fullscreen/tab-visibility listener in `resources/views/workspace.blade.php`) detects fullscreen exits and tab switches during locked assignments.
3. **Analytics, Contribution Heatmap & Gamification:**
   - `App\Observers\AnalyticsObserver` is attached to `Submission`, `User`, and `Workspace` in `AppServiceProvider::boot()`, automatically writing `{model}_{created|updated}` events to `analytics_events` and invoking `GamificationService::evaluateBadges()`.
   - `AdminAnalyticsController::index()` renders 7 datasets (30-day registrations/submissions/AI actions, AI mode distribution, 14-day workspace activity, top event types, role distribution); `AnalyticsController::index()` exists for student/instructor analytics views (note: no route in `routes/web.php` is currently registered for `AnalyticsController::index()`).
   - `ContributionController::heatmap()` (`GET /api/contributions` on web auth middleware) aggregates 365 days of `Submission` + `AiActionsLog` counts into intensity levels `0–4`.
   - `GamificationService` + `User::addXp()` (`level = floor(sqrt(xp / 100)) + 1`, 9 rank titles from `Novice` to `Grandmaster`) power `GET /gamification` (`GamificationController`) and public student portfolios at `GET /u/{student_id}` (`PublicProfileController`, HTTP cached 300s with ETag).
4. **PWA & WebPush Notifications:**
   - `public/pwa.js` registers `/serviceworker.js` (note: `resources/views/layouts/app.blade.php` also registers `/sw.js`), requests Notification permission, subscribes via VAPID key (`meta[name="vapid-public-key"]`), and posts subscriptions to `POST /api/push-subscriptions` (`PushSubscriptionController::store()`, throttled at 5/min).
   - `routes/console.php` schedules a daily 10:00 reminder sending `ScheduledReminderNotification` via `WebPushChannel` + `database`; `App\Services\WebPushService` wraps `minishlink/web-push` and prunes expired endpoints on delivery failure.

**State Management:**
- **Server-Side Persistent State:** MySQL 8.0 (production) / SQLite (dev/test) holds all relational LMS, ERP, AI audit, quota, and telemetry state; Redis 7 backs queues, cache, rate limiters, and Reverb scaling in production (`docker-compose.prod.yml`).
- **Workspace File State:** Stored exclusively in named Docker volumes (`vl_ws_{id}_data`) mounted into `/home/coder/{slug}`; shared Nix packages live in `visionlab_nix_store:/nix`.
- **Session & Auth State:** Laravel encrypted cookies/sessions for web users (plus `session('impersonator_id')` for admin impersonation); unencrypted `key` cookie for IDE proxy authentication; Sanctum Bearer tokens injected into container env vars and `/home/coder/.continue/config.json`.
- **Client UI State:** Stateless server-rendered Blade views enhanced with Alpine.js (`resources/js/app.js`), forced dark mode (`html.dark`), inline JS state machines for workspace boot polling (`resources/views/workspace.blade.php`), and `localStorage['vc_notifs']` for notification bell badges (`resources/views/layouts/app.blade.php`).

## Key Abstractions

**CodeServerManager:**
- Purpose: Single orchestration boundary for Docker container lifecycle, port allocation (`9000–9999`), Nix environment bootstrapping, `.vsix` integrity-checked installation, and container/volume file I/O.
- Examples: `app/Services/CodeServerManager.php`
- Pattern: Service / Infrastructure Facade wrapping `Symfony\Component\Process\Process` with automatic dev fallback when Docker is unavailable.

**AiService & AiSandbox (HITL Protocol):**
- Purpose: Decouples LLM streaming/tool orchestration (`AiService`) from workspace filesystem safety, diff generation, snapshotting, and patch state transitions (`AiSandbox`).
- Examples: `app/Services/AiService.php`, `app/Services/AiSandbox.php`, `app/Models/AiPendingPatch.php`, `app/Models/AiSnapshot.php`
- Pattern: Proxy + Command/Proposal Staging (zero direct AI writes; every mutation staged as a reviewable record).

**Hierarchical Quota Resolver (`WorkspaceQuota`):**
- Purpose: Computes effective CPU, RAM, disk, idle timeout, and max workspace count dynamically across multi-tenant scopes without hardcoding per-user logic in controllers.
- Examples: `app/Models/WorkspaceQuota.php` (`resolveFor()`), `app/Http/Controllers/WorkspaceController.php`, `app/Services/CodeServerManager.php`
- Pattern: Chain-of-Responsibility / Precedence Resolution (`user -> course -> role -> global -> default`).

**Dual Telemetry & Compliance Trails (`AuditLog` vs `AnalyticsEvent`):**
- Purpose: Separates security/admin state-change accountability (`AuditLog` / `AuditService` with `old_state`, `new_state`, `actor_id`, `correlation_id`) from behavioral/forensic/gamification telemetry (`AnalyticsEvent::track()` + `AnalyticsObserver`).
- Examples: `app/Models/AuditLog.php`, `app/Services/AuditService.php`, `app/Models/AnalyticsEvent.php`, `app/Observers/AnalyticsObserver.php`
- Pattern: Observer + Static Event Recorder.

**Unified Blade Shell & Component System:**
- Purpose: Enforces consistent dark UI chrome across public pages (`layouts.landing` using `<x-frontend-header />` and `<x-frontend-footer />`) and authenticated LMS/Admin shells (`layouts.dashboard` / `layouts.admin` using `<x-sidebar />` and `<x-topbar />`).
- Examples: `resources/views/layouts/landing.blade.php`, `resources/views/layouts/dashboard.blade.php`, `resources/views/layouts/admin.blade.php`, `resources/views/components/frontend-header.blade.php`, `resources/views/components/frontend-footer.blade.php`
- Pattern: Template Inheritance (`@extends`) + Anonymous Blade Components (`<x-*>`) + Breeze Class Components (`AppLayout`, `GuestLayout`).

## Entry Points

**HTTP Web & API Entry Point:**
- Location: `public/index.php` -> `bootstrap/app.php`
- Triggers: All HTTP/HTTPS traffic via Nginx (`docker/nginx/default.conf` / `visionlab_nginx.conf`) or `php artisan serve`.
- Responsibilities: Boots Laravel 11 `Application`, attaches global middleware (`trustProxies`, `statefulApi`, `CheckMaintenanceMode`, `SecurityHeaders`, `CheckAccountStatus`), enforces dynamic proxy HTTPS URL forcing (`AppServiceProvider::boot()`), and dispatches to `routes/web.php`, `routes/auth.php`, `routes/api.php`, or `/up`.

**CLI & Scheduler Entry Point:**
- Location: `artisan` -> `routes/console.php` & `app/Console/Commands/`
- Triggers: Manual CLI invocation, cron/container loop (`visionlab-scheduler` running `php artisan schedule:run` every 60s), and queue worker (`php artisan horizon` in `docker-compose.prod.yml` / `php artisan queue:listen` in `composer.json` `dev` script).
- Responsibilities: Executes scheduled jobs (`submissions:check-late` hourly, `assignments:due-reminders` every 6h, `workspaces:stale-heartbeat` every 5m, `workspaces:cleanup` daily, `daily:update-streaks` at 00:00, and student WebPush reminder closure at 10:00).

**WebSocket Server Entry Point:**
- Location: `config/reverb.php`, `routes/channels.php`
- Triggers: `php artisan reverb:start --host="0.0.0.0" --port=8080` (`visionlab-reverb` service), proxied by Nginx `/app`.
- Responsibilities: Authenticates private/presence channels via `/broadcasting/auth` and pushes broadcast events (`SubmissionGraded`, `PatchProposed`, `WorkspaceRebuildingEvent`) to connected Echo clients.

**Health & Observability Entry Points:**
- Location: `GET /up` (`bootstrap/app.php`), `GET /healthz` (`HealthController::check()`), `GET /api/health` (inline closure in `routes/api.php`), `GET /admin/status` (`HealthController::status()`).
- Triggers: Uptime probes, container health checks, and admin monitoring.
- Responsibilities: Probes database, Redis, Reverb driver config, local storage writability, AI config presence, and Jitsi config presence; returns HTTP 503 if DB or Redis fails.

**Frontend JS & PWA Entry Points:**
- Location: `resources/js/app.js` (bundled by Vite `vite.config.js`), `public/pwa.js`
- Triggers: `@vite(['resources/css/app.css', 'resources/js/app.js'])` and `<script src="/pwa.js" defer>` in Blade layouts.
- Responsibilities: Boots Axios (`resources/js/bootstrap.js`), registers `window.initEcho` (`resources/js/echo.js`), forces `.dark` class, starts Alpine.js, registers PWA service worker, and negotiates VAPID push subscriptions.

**IDE Container & Extension Entry Points:**
- Location: `docker/workspace/Dockerfile` (entrypoint `/usr/bin/visionlab-server`, overridden at runtime by `CodeServerManager::startWorkspace()` Nix shell command) and `extensions/visionlab-collab/src/extension.ts` (`activate()`).
- Triggers: Docker container startup (`CodeServerManager::startWorkspace()`) and VS Code `onStartupFinished`.
- Responsibilities: Launches `code-server` inside Nix environment, exposes `/healthz` on port 8080, activates Patch Reviewer sidebar webview, and registers collab/video commands.

## Error Handling

**Strategy:** Default Laravel 11 Exception Handling (`bootstrap/app.php` `withExceptions` is currently uncustomized) paired with explicit controller-level HTTP status responses, custom domain exceptions for supply-chain security, and graceful degradation for external services.

**Patterns:**
- **Custom Error Blade Views:** `resources/views/errors/` provides themed views for `403`, `404`, `419`, `500`, `maintenance` (503 via `CheckMaintenanceMode`), and `offline` (PWA fallback).
- **Supply-Chain Integrity Exception:** `App\Exceptions\ExtensionIntegrityException` is thrown by `CodeServerManager::installExtension()` upon SHA256 mismatch after dispatching `App\Events\ExtensionIntegrityFailed` (logged via `App\Listeners\LogExtensionIntegrityFailure`); caught in `SyncWorkspaceExtensions` and `setupWorkspaceExtensions()` to mark `sync_status = 'failed'`.
- **Non-Fatal External Dependencies:** Broadcast calls (`SubmissionController::grade()`) wrap `broadcast(...)` in `try/catch (\Throwable)` so grading succeeds even if Reverb is offline; `AiService` catches Guzzle exceptions and streams an SSE error chunk followed by `[DONE]`; `CodeServerManager` falls back to `devFallback()` when Docker is unreachable.
- **API JSON Error Consistency:** API controllers (`AiController`, `WorkspaceFileController`, `VisionGuardController`, `WebhookController`) return structured JSON payloads (`['error' => ...]` or `['message' => ...]`) with explicit `400`, `401`, `403`, `404`, `422`, `429`, `500`, `501`, or `503` status codes.

## Cross-Cutting Concerns

**Logging:**
- Uses Laravel `Log` facade (`config/logging.php`) across services, jobs, webhooks, and controllers (`Log::info`, `Log::warning`, `Log::error`, `Log::critical` for extension checksum failures).
- Dual database audit trails: `AuditLog::record()` / `AuditService` captures admin & security mutations with actor, before/after state JSON, IP, user agent, and UUID `correlation_id`; `AnalyticsEvent::track()` and `AnalyticsObserver` capture behavioral and forensic telemetry.

**Validation:**
- Primary LMS/Admin mutations use dedicated `FormRequest` classes under `app/Http/Requests/` (18 domain + 6 `Admin/` + `Auth/LoginRequest.php`).
- API endpoints and several newer controllers (`WorkspaceController`, `WorkspaceFileController`, `AdminUserController`, `VisionGuardController`, `ForensicsController`, `PushSubscriptionController`) use inline `$request->validate(...)` or `Validator::make(...)`.

**Authentication & Authorization:**
- Web routes: Session cookies (`web` guard) + `auth` + `verified` + `CheckAccountStatus` + `RoleMiddleware` (`role:admin`).
- API routes: Sanctum (`auth:sanctum`) supporting both first-party SPA cookies (`$middleware->statefulApi()`) and Bearer tokens (`personal_access_tokens`), plus workspace bearer token check on `/api/workspace/{slug}/trigger-rebuild` and HMAC-SHA256 verification on `/api/webhook/deploy`.
- Named Rate Limiters registered in `AppServiceProvider::boot()` (keyed by user ID or IP): `api` (60/min), `auth` (10/min), `ai` (30/min), `file-api` (100/min), `video` (30/min), `push` (5/min), `admin` (60/min); plus custom 120/min limiter in `VisionGuardController::logEvent()`.

**Security Headers & Sandboxing:**
- `App\Http\Middleware\SecurityHeaders` appends CSP, HSTS, `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, and `Permissions-Policy` to every web response (relaxing CSP in `local` env for Vite `localhost:5173`).
- Docker workspace containers run with `--init`, `--cap-drop ALL`, `--tmpfs /tmp`, `--tmpfs /run`, isolated `--network visionlab-workspace-net`, memory/CPU quota flags, non-root `coder` user (UID 1000), and read-only host extension mounts.

---

*Architecture analysis: 2026-11-24*

# Codebase Structure

**Analysis Date:** 2026-11-24

## Directory Layout

```
VisionLab/
├── app/                              # Core Laravel application PHP source (PSR-4: App\)
│   ├── Console/Commands/             # 7 Artisan CLI & scheduled maintenance commands
│   ├── Contracts/                    # Reserved directory for interfaces (currently empty)
│   ├── Events/                       # 6 broadcast & domain events (Reverb + security)
│   ├── Exceptions/                   # Custom exceptions (ExtensionIntegrityException)
│   ├── Http/
│   │   ├── Controllers/              # 23 root web/domain controllers + base Controller.php
│   │   │   ├── Admin/                # 16 admin panel & ERP CRUD controllers
│   │   │   ├── Api/                  # 3 Sanctum JSON/SSE API controllers (AI, Forensics)
│   │   │   └── Auth/                 # 9 Laravel Breeze authentication controllers
│   │   ├── Middleware/               # 4 custom HTTP middleware classes
│   │   └── Requests/                 # 25 FormRequest classes (18 root, 6 Admin/, 1 Auth/)
│   ├── Jobs/                         # 7 queued ShouldQueue background jobs
│   ├── Listeners/                    # 2 event listeners (streak tracking, integrity failure)
│   ├── Models/                       # 57 Eloquent ORM models (LMS, ERP, IDE, AI, Analytics)
│   ├── Notifications/                # 5 multi-channel / WebPush notification classes
│   ├── Observers/                    # 1 Eloquent observer (AnalyticsObserver)
│   ├── Policies/                     # 14 resource authorization policies
│   ├── Providers/                    # AppServiceProvider.php (+ stale .backup file)
│   ├── Services/                     # 6 core domain services + empty Deployment/ subdir
│   └── View/Components/              # 2 Breeze layout components (AppLayout, GuestLayout)
├── bootstrap/                        # Laravel 11 framework bootstrap (app.php, providers.php)
├── config/                           # 15 configuration files (Laravel + visionlab/reverb/webpush)
├── database/
│   ├── factories/                    # 14 model factories for testing & seeding
│   ├── migrations/                   # 54 schema migrations (core, ERP, extended LMS, cleanup, XP)
│   ├── seeders/                      # 9 database seeders orchestrated by DatabaseSeeder.php
│   └── database.sqlite               # Local SQLite database file
├── docker/                           # Container build contexts & reverse-proxy configs
│   ├── app/                          # Laravel PHP-FPM production Dockerfile
│   ├── nginx/                        # Production Nginx TLS 1.3 vhost + ssl/ directory
│   └── workspace/                    # Sovereign Nix + VisionLab IDE Docker image & VSIX artifacts
├── docs/                             # Engineering docs, IDE reports, and phase prompts/
├── extensions/                       # Custom VS Code extension source & packaged VSIX
│   └── visionlab-collab/             # TypeScript collab, PatchReviewer, and VideoPanel extension
├── public/                           # Web server document root (index.php, PWA, OpenAPI, build/)
│   ├── api-docs/                     # OpenAPI 3.1 specification (openapi.yaml)
│   ├── build/                        # Compiled Vite CSS/JS bundles & manifest.json
│   └── icons/                        # Brand SVGs & generated_icons/ multi-size favicons/PWA icons
├── resources/
│   ├── css/                          # Tailwind CSS entry & dark design tokens (app.css)
│   ├── js/                           # Frontend ES modules (app.js, bootstrap.js, echo.js, animations.js)
│   └── views/                        # Blade templates (layouts, components, LMS, IDE, admin, errors)
├── routes/                           # 5 route files (web, api, auth, channels, console)
├── scripts/                          # Operational Bash scripts (backup, restore, build, security)
├── storage/                          # Laravel runtime storage (logs, cache, extensions/, workspaces/)
├── tests/                            # PHPUnit test suite (Unit/ + 34 Feature/ tests)
├── .github/workflows/                # 3 GitHub Actions CI/CD pipelines
├── .planning/codebase/               # GSD codebase mapping documents
├── AGENTS.md                         # Master project engineering directive
├── RUNBOOK.md                        # Production operations & incident runbook
├── VISIONLAB_AUDIT_REPORT.md         # July 2026 codebase audit findings
├── composer.json                     # PHP dependencies & dev scripts
├── package.json                      # Node/Vite/Tailwind/Alpine/Echo dependencies
├── docker-compose.prod.yml           # 8-service production container stack
├── Dockerfile                        # Root container build file
├── phpunit.xml                       # PHPUnit configuration (SQLite :memory:)
├── tailwind.config.js                # Tailwind CSS theme & semantic `vc-*` color tokens
└── vite.config.js                    # Vite 6 + laravel-vite-plugin build config
```

*Note on root clutter:* The repository root also contains legacy Nginx/rewrite scratch configs (`nginx.conf`, `nginx_patch.conf`, `rewrite.conf`, `visionlab.rewrite.conf`, `visionlab_nginx.conf`, `daemon.json`), one-off helper scripts (`fix_css.py`, `replace_icons.ps1`, `scratch.php`), prompt/design artifacts (`PROMPTS.xml`, `prompts.txt`, `color-palette-reference.md`, `letterpress-dark.svg`, `letterpress-light.svg`), local `composer.phar`, and server key/env files (`.env`, `.env.remote`, `LightsailDefaultKey-ap-south-1.pem`).

## Directory Purposes

**`app/Http/Controllers/`:**
- Purpose: Handles HTTP request lifecycles partitioned into four namespaces: root user-facing controllers, `Admin/` operations & ERP controllers, `Api/` Sanctum JSON/SSE endpoints, and `Auth/` Breeze authentication flows.
- Contains: 52 PHP files (1 abstract `Controller.php` + 51 concrete controllers).
- Key files: `app/Http/Controllers/WorkspaceController.php`, `app/Http/Controllers/WorkspaceFileController.php`, `app/Http/Controllers/SubmissionController.php`, `app/Http/Controllers/CourseController.php`, `app/Http/Controllers/EnrollmentController.php`, `app/Http/Controllers/DashboardController.php`, `app/Http/Controllers/Api/AiController.php`, `app/Http/Controllers/Api/ForensicsController.php`, `app/Http/Controllers/VisionGuardController.php`, `app/Http/Controllers/HealthController.php`, `app/Http/Controllers/Admin/AdminController.php`, `app/Http/Controllers/Admin/AdminUserController.php`, `app/Http/Controllers/Admin/AdminWorkspaceController.php`, `app/Http/Controllers/Admin/AdminAnalyticsController.php`.

**`app/Http/Middleware/`:**
- Purpose: Enforces global web security, maintenance mode, account suspension enforcement, and route-level role checks.
- Contains: 4 middleware classes.
- Key files: `app/Http/Middleware/CheckMaintenanceMode.php`, `app/Http/Middleware/SecurityHeaders.php`, `app/Http/Middleware/CheckAccountStatus.php`, `app/Http/Middleware/RoleMiddleware.php`.

**`app/Http/Requests/`:**
- Purpose: Isolates request input validation and form-level authorization rules.
- Contains: 25 `FormRequest` classes (`Admin/` for user/extension/quota/webhook management, `Auth/LoginRequest.php`, and 18 LMS/workspace/broadcast requests).
- Key files: `app/Http/Requests/StoreCourseRequest.php`, `app/Http/Requests/StoreAssignmentRequest.php`, `app/Http/Requests/GradeSubmissionRequest.php`, `app/Http/Requests/SubmitAssignmentRequest.php`, `app/Http/Requests/Admin/UpdateUserRequest.php`.

**`app/Services/`:**
- Purpose: Houses core business orchestration, Docker container management, AI proxying/sandboxing, auditing, gamification, and WebPush delivery.
- Contains: 6 PHP service classes + an empty `Deployment/` directory (left over after deployment tables were dropped).
- Key files: `app/Services/CodeServerManager.php`, `app/Services/AiService.php`, `app/Services/AiSandbox.php`, `app/Services/AuditService.php`, `app/Services/GamificationService.php`, `app/Services/WebPushService.php`.

**`app/Models/`:**
- Purpose: Eloquent ORM entities representing the entire domain model.
- Contains: 57 models grouped across 7 domains:
  - *Identity & Auth:* `User.php`, `NotificationPreference.php`, `PushSubscription.php`
  - *LMS Core:* `Course.php`, `Enrollment.php`, `Assignment.php`, `Submission.php`, `Announcement.php`, `AnnouncementRead.php`, `Semester.php`, `CourseBatch.php`
  - *Extended LMS (schema/models present, CRUD controllers largely unbuilt):* `CourseModule.php`, `Lesson.php`, `Quiz.php`, `Question.php`, `QuizAttempt.php`, `ForumTopic.php`, `ForumPost.php`, `Certificate.php`, `Rubric.php`, `RubricCriteria.php`, `GradingRubric.php`, `GradeItem.php`
  - *Institute ERP:* `Campus.php`, `Department.php`, `Employee.php`, `Payroll.php`, `FeeChallan.php`, `Transaction.php`, `Attendance.php`, `AttendanceLog.php`, `LibraryBook.php`, `BookIssue.php`
  - *Workspace & Extensions:* `Workspace.php`, `WorkspaceTemplate.php`, `WorkspaceQuota.php`, `Extension.php`, `ExtensionBuild.php`, `CourseExtension.php`, `WorkspaceExtension.php`
  - *AI Agent & Governance:* `AiProvider.php`, `AiModel.php`, `AiChatSession.php`, `AiMessage.php`, `AiPendingPatch.php`, `AiSnapshot.php`, `AiActionsLog.php`
  - *Analytics, Forensics, Gamification & Ops:* `AnalyticsEvent.php`, `SubmissionForensic.php`, `AuditLog.php`, `UserBadge.php`, `XpTransaction.php`, `SystemConfig.php`, `Webhook.php`
  - *Legacy/Dropped-Table Models:* `ChatMessage.php`, `Recording.php`, `RecordingAuditLog.php`
- Key files: `app/Models/User.php`, `app/Models/Workspace.php`, `app/Models/WorkspaceQuota.php`, `app/Models/Course.php`, `app/Models/Submission.php`, `app/Models/AiPendingPatch.php`, `app/Models/AnalyticsEvent.php`, `app/Models/AuditLog.php`.

**`app/Policies/`:**
- Purpose: Entity-level RBAC and ownership authorization rules.
- Contains: 14 policy classes (`AiActionPolicy`, `AnalyticsPolicy`, `AnnouncementPolicy`, `AssignmentPolicy`, `AuditLogPolicy`, `CoursePolicy`, `EnrollmentPolicy`, `ExtensionPolicy`, `NotificationPolicy`, `RecordingPolicy`, `SubmissionPolicy`, `UserPolicy`, `WorkspacePolicy`, `WorkspaceQuotaPolicy`).
- Key files: `app/Policies/WorkspacePolicy.php`, `app/Policies/CoursePolicy.php`, `app/Policies/SubmissionPolicy.php`, `app/Policies/AssignmentPolicy.php`, `app/Policies/AiActionPolicy.php`.

**`app/Events/`, `app/Listeners/`, `app/Observers/`, `app/Jobs/`, `app/Notifications/`, `app/Console/Commands/`:**
- Purpose: Asynchronous execution, broadcast events, scheduled maintenance, telemetry hooks, and push/database notifications.
- Contains: 6 Events, 2 Listeners, 1 Observer, 7 Jobs, 5 Notifications, and 7 Artisan Commands.
- Key files: `app/Events/SubmissionGraded.php`, `app/Events/PatchProposed.php`, `app/Events/WorkspaceRebuildingEvent.php`, `app/Events/ExtensionIntegrityFailed.php`, `app/Observers/AnalyticsObserver.php`, `app/Listeners/UpdateUserStreak.php`, `app/Jobs/SyncWorkspaceExtensions.php`, `app/Console/Commands/WorkspaceStaleHeartbeat.php`, `app/Console/Commands/WorkspaceCleanup.php`, `app/Notifications/ScheduledReminderNotification.php`.

**`routes/`:**
- Purpose: Maps all URL, API, authentication, broadcast channel, and scheduled task entry points.
- Contains: 5 route files.
- Key files: `routes/web.php` (public, LMS, workspace, admin panel), `routes/api.php` (Sanctum file I/O, AI proxy, patches, VisionGuard, extension registry, deploy webhook, health), `routes/auth.php` (Breeze login/reset/verify; registration disabled), `routes/channels.php` (`user.{id}` channel auth), `routes/console.php` (6 scheduled tasks).

**`config/`:**
- Purpose: Environment-driven runtime configuration.
- Contains: 15 PHP config files.
- Key files: `config/visionlab.php` (Docker image/ports/quotas, AI, legacy Jitsi/deploy, VAPID), `config/reverb.php`, `config/broadcasting.php`, `config/sanctum.php`, `config/webpush.php`, `config/services.php` (GitHub webhook secret), `config/database.php`, `config/auth.php`.

**`database/`:**
- Purpose: Versioned relational schema evolution, realistic dev seeding, and PHPUnit model factories.
- Contains: 54 migrations (`database/migrations/`), 9 seeders (`database/seeders/`), and 14 factories (`database/factories/`).
- Migration Domain Groups:
  - *Laravel Base (3):* `users`, `cache`, `jobs` (`0001_01_01_*`)
  - *Core LMS & Workspaces (11):* `rooms`, `courses`, `assignments`, `announcements`, `extensions`, `ai_*`, `analytics_events`, `enrollments`, `submissions`, `workspaces` (`2024_01_*`)
  - *Core Contract, Sanctum & Extensions (15):* `ai_agent_extensions`, `recordings`, `complete_core_entity_contract`, `personal_access_tokens`, `video_rooms`/`video_attendances`, `course_extensions`, `workspace_extensions`, `chat_messages`, `system_configs`, `webhooks`, `workspace_templates` (`2026_05_*` – `2026_06_20_*`)
  - *Scheduling, Nix, Lockdown & AI Providers (17):* `student_id`, `course_batches`, `semesters`, `exam_mode`, `nix_config`, `ai_providers`/`ai_models`, marketplace/lockdown toggles (`2026_06_21_*` – `2026_06_28_*`)
  - *Institute ERP & Extended LMS (5):* `campuses`, `departments`, `attendance_logs`, `rubrics`, `grade_items`, `course_modules`, `lessons`, `quizzes`, `forums`, `certificates`, `fee_challans`, `transactions`, `employees`, `payrolls`, `attendances`, `library_books`, `book_issues`, workspace `slug` & `max_workspaces` (`2026_07_11_*`)
  - *Cleanup & Gamification (3):* `2026_07_13_000000_remove_incomplete_features_tables.php` (drops 10 video/recording/deployment/collab tables), `2026_07_18_003629_add_gamification_to_users_table.php`, `2026_07_18_003631_create_xp_transactions_table.php`.

**`resources/`:**
- Purpose: Blade templates, Tailwind CSS design tokens, and frontend ES modules compiled by Vite.
- Contains:
  - `resources/views/layouts/`: `landing.blade.php` (public pages with `<x-frontend-header />` / `<x-frontend-footer />`), `dashboard.blade.php` (authenticated LMS shell with `<x-sidebar />` / `<x-topbar />`), `admin.blade.php` (extends `layouts.dashboard`), `app.blade.php` & `guest.blade.php` (Breeze layouts), `navigation.blade.php`.
  - `resources/views/components/`: 34 reusable Blade components (`frontend-header.blade.php`, `frontend-footer.blade.php`, `sidebar.blade.php`, `topbar.blade.php`, `exam-lockdown.blade.php`, `stat-card.blade.php`, `modal.blade.php`, `toast-container.blade.php`, `form/*`, etc.).
  - `resources/views/workspace.blade.php`: Full-screen VisionLab IDE iframe wrapper, 5-step boot preloader, `/healthz` poller, restart/rebuild/delete triggers, and exam lockdown enforcement.
  - Feature view folders: `admin/` (dashboard, analytics, audits, extensions, quotas, status, system, templates, users, webhooks, workspaces, `erp/{batches,campuses,challans,departments,employees,library}`), `analytics/`, `announcements/`, `assignments/`, `auth/`, `courses/`, `dashboard/`, `enrollments/`, `errors/`, `gamification/`, `pages/` (`about`, `contact`, `docs`, `features`), `portfolio/`, `profile/`, `progress/`, `submissions/`, `workspaces/`.
  - `resources/js/`: `app.js`, `bootstrap.js`, `echo.js`, `animations.js`.
  - `resources/css/`: `app.css`.

**`extensions/`:**
- Purpose: Source code and compiled `.vsix` bundle for VisionLab's in-IDE collaboration and HITL patch review VS Code extension.
- Contains: `extensions/visionlab-collab/` with TypeScript source (`src/extension.ts`, `src/managers/{RealtimeManager,DocumentSync,CursorSync}.ts`, `src/panels/{PatchReviewer,VideoPanel}.ts`), compiled JS (`out/`), manifest (`package.json`, `tsconfig.json`), and packaged `visionlab-collab-1.0.0.vsix`.
- Note: The VisionLab AI Agent extension itself is bundled as a prebuilt VSIX artifact at `docker/workspace/extensions/visionlab-agent-1.3.39.vsix` (per `AGENTS.md`, its fork source is maintained and compiled on the build/production server).

**`docker/` & `scripts/`:**
- Purpose: Production Docker images, Nginx TLS/WebSocket reverse-proxy templates, Nix workspace definitions, and operational shell utilities.
- Contains: `docker/app/Dockerfile`, `docker/Dockerfile.codeserver`, `docker/workspace/Dockerfile`, `docker/workspace/dev.nix`, `docker/workspace/settings.json`, `docker/workspace/vl-entrypoint.sh`, `docker/workspace/patch_origin.js`, `docker/workspace/visionlab-ide-1.0.0-linux-amd64.tar.gz`, `docker/nginx/default.conf`, `scripts/backup.sh`, `scripts/restore.sh`, `scripts/build-code-server.sh`, `scripts/security_verify.sh`.

**`tests/`:**
- Purpose: Automated PHPUnit unit and feature test suites.
- Contains: `tests/TestCase.php`, `tests/Unit/ExampleTest.php`, and 34 feature tests under `tests/Feature/` (including 6 Breeze `Auth/` tests and domain suites covering RBAC, LMS lifecycle, workspace security, AI authorization, extension supply chain, forensics, and rate limiting; note that `VideoRoomControllerTest.php`, `RoomControllerTest.php`, `RecordingControllerTest.php`, `DeploymentControllerTest.php`, and `RealtimeCollaborationTest.php` correspond to features removed in migration `2026_07_13_000000`).

**`.github/workflows/` & `docs/`:**
- Purpose: CI/CD automation and engineering reference documentation.
- Contains: `.github/workflows/compile-artifacts.yml`, `.github/workflows/deploy.yml`, `.github/workflows/production.yml`; `docs/VisionLabIDE.md`, `docs/VisionLab_Agent_Build_Report.md`, and `docs/prompts/phase1.md`–`phase12.md` + `directive.md`.

## Key File Locations

**Entry Points:**
- `public/index.php`: Primary PHP HTTP front controller.
- `bootstrap/app.php`: Laravel 11 routing, middleware aliases, CSRF/cookie exclusions, and API throttling setup.
- `bootstrap/providers.php`: Registers `App\Providers\AppServiceProvider`.
- `artisan`: CLI command runner and scheduler entry point.
- `resources/js/app.js`: Vite frontend JavaScript entry point (Alpine.js, Axios, Echo).
- `extensions/visionlab-collab/src/extension.ts`: VS Code extension activation entry point.

**Configuration:**
- `config/visionlab.php`: Central VisionLab platform settings (Docker image, storage, ports, quotas, AI, legacy Jitsi/deploy, VAPID).
- `config/broadcasting.php` & `config/reverb.php`: Laravel Reverb WebSocket configuration.
- `config/sanctum.php`: Stateful domains and token authentication settings.
- `config/webpush.php`: VAPID key settings for push notifications.
- `config/services.php`: Third-party service credentials (GitHub deploy webhook secret, SES, Slack, Postmark, Resend).
- `app/Providers/AppServiceProvider.php`: HTTPS proxy URL forcing, Policy mapping, `AnalyticsObserver` registration, `Login` streak listener, and all 7 named `RateLimiter` definitions.
- `vite.config.js` & `tailwind.config.js`: Asset bundling and design system tokens.

**Core Logic:**
- `app/Services/CodeServerManager.php`: Docker workspace provisioning, Nix bootstrap, extension integrity enforcement, and volume file I/O.
- `app/Services/AiService.php` & `app/Services/AiSandbox.php`: Anthropic SSE proxy, tool loop, and HITL `ai_pending_patches` lifecycle.
- `app/Services/GamificationService.php` & `app/Models/User.php`: XP quadratic leveling, rank calculation, and badge award evaluation.
- `app/Services/AuditService.php` & `app/Observers/AnalyticsObserver.php`: Structured security auditing and automatic telemetry capture.
- `app/Models/WorkspaceQuota.php`: 5-tier resource quota resolution (`user -> course -> role -> global -> fallback`).
- `resources/views/workspace.blade.php`: Full-screen IDE shell, `/healthz` boot polling state machine, and exam lockdown UI.

**Testing:**
- `phpunit.xml`: PHPUnit runner config (`sqlite` `:memory:`, `array` cache/session, `sync` queue).
- `tests/TestCase.php`: Base test case class.
- `tests/Feature/SecurityHardeningTest.php`, `tests/Feature/RoleBasedAccessTest.php`, `tests/Feature/WorkspaceSecurityTest.php`, `tests/Feature/AiAgentAuthorizationTest.php`, `tests/Feature/ExtensionSupplyChainTest.php`, `tests/Feature/LmsLifecycleTest.php`.

## Naming Conventions

**Files:**
- **Controllers:** `PascalCase` + `Controller.php`; admin controllers prefixed with `Admin*Controller.php` under `app/Http/Controllers/Admin/` (exception: `Admin/ExtensionController.php`).
- **Form Requests:** `<Action><Resource>Request.php` (`StoreCourseRequest.php`, `UpdateQuotaRequest.php`, `GradeSubmissionRequest.php`).
- **Models:** Singular `PascalCase` (`WorkspaceTemplate.php`, `AiPendingPatch.php`, `FeeChallan.php`).
- **Policies:** `<Model>Policy.php` (`CoursePolicy.php`, `WorkspacePolicy.php`, `AiActionPolicy.php`).
- **Events:** Past-tense or state `PascalCase` (`SubmissionGraded.php`, `PatchProposed.php`, `ExtensionIntegrityFailed.php`, `WorkspaceRebuildingEvent.php`).
- **Jobs & Commands:** Descriptive `PascalCase` (`SyncWorkspaceExtensions.php`, `WorkspaceStaleHeartbeat.php`, `UpdateStreaksCommand.php`).
- **Blade Views:** `kebab-case.blade.php` for components and error/auth templates (`frontend-header.blade.php`, `exam-lockdown.blade.php`, `reset-password.blade.php`); RESTful action names (`index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`) inside plural resource folders.
- **TypeScript Extensions:** `PascalCase.ts` for classes/panels (`RealtimeManager.ts`, `PatchReviewer.ts`, `DocumentSync.ts`), lowercase `extension.ts` for entry.
- **Migrations:** `YYYY_MM_DD_HHMMSS_<snake_case_description>.php`.

**Directories:**
- **PHP (`app/`):** `PascalCase` matching PSR-4 namespaces (`app/Http/Controllers/Admin/`, `app/Services/`).
- **Views (`resources/views/`):** Plural lowercase resource names (`courses/`, `assignments/`, `workspaces/`, `admin/erp/challans/`).
- **Infrastructure & Assets (`docker/`, `public/`, `scripts/`, `extensions/`):** Lowercase `kebab-case` (`visionlab-collab/`, `api-docs/`, `generated_icons/`).

## Where to Add New Code

**New Web / LMS Feature (e.g., Quizzes, Forums, Modules/Lessons, Attendance):**
- Schema & Model: Add migration in `database/migrations/`, complete relationships & `$fillable` in `app/Models/<Model>.php` (many Extended LMS models already exist and need relationships wired).
- Validation & Authorization: Add FormRequest(s) in `app/Http/Requests/` and Policy in `app/Policies/<Model>Policy.php`.
- Controller & Routes: Add controller in `app/Http/Controllers/<Resource>Controller.php`, register routes inside the `['auth', 'verified']` group in `routes/web.php`.
- Views: Add templates in `resources/views/<resources>/{index,create,edit,show}.blade.php` extending `@extends('layouts.dashboard')`.
- Tests: Add feature tests in `tests/Feature/<Resource>ControllerTest.php` and factory in `database/factories/<Model>Factory.php`.

**New Admin Panel or Institute ERP Module:**
- Controller & Requests: Place controller in `app/Http/Controllers/Admin/Admin<Resource>Controller.php` and requests in `app/Http/Requests/Admin/`.
- Routes: Register inside `Route::middleware(['role:admin', 'throttle:admin'])->prefix('admin')->name('admin.')` in `routes/web.php`.
- Views: Place under `resources/views/admin/<resource>/` (or `resources/views/admin/erp/<resource>/` for ERP modules) extending `@extends('layouts.admin')`; add navigation links in `resources/views/components/sidebar.blade.php`.
- Auditing: Log sensitive state changes via `App\Services\AuditService::log(...)`.

**New API Endpoint (IDE Extension / SPA / Telemetry):**
- Controller: Add under `app/Http/Controllers/Api/<Feature>Controller.php` (or domain controller if shared).
- Routes & Throttling: Register in `routes/api.php` behind `auth:sanctum` and an appropriate named rate limiter from `app/Providers/AppServiceProvider.php` (`api`, `ai`, `file-api`, `push`, etc.).
- Documentation: Update `public/api-docs/openapi.yaml`.

**New Domain Service or Shared Business Logic:**
- Implementation: Place reusable service classes in `app/Services/<Domain>Service.php` (and interfaces in `app/Contracts/` when introducing provider abstractions). Inject via Laravel service container constructor injection.

**New Background Job, Event, Listener, or Scheduled Task:**
- Queued Work: Add `ShouldQueue` class in `app/Jobs/`.
- Real-Time Broadcast Event: Add `ShouldBroadcast` class in `app/Events/` AND register channel authorization callback in `routes/channels.php` (critical — unauthorized channels fail silently/403 in Reverb).
- Event Listener / Observer: Add in `app/Listeners/` or `app/Observers/` and register in `app/Providers/AppServiceProvider.php::boot()`.
- Scheduled Command: Add command in `app/Console/Commands/` and schedule in `routes/console.php`.

**New Frontend UI Component or Public Marketing Page:**
- Reusable Component: Add anonymous Blade component in `resources/views/components/<name>.blade.php` (or `components/form/` for form inputs).
- Public Marketing Page: Add view in `resources/views/pages/<slug>.blade.php` extending `@extends('layouts.landing')` — NEVER duplicate header/footer markup; always use `<x-frontend-header />` and `<x-frontend-footer />` (per `AGENTS.md`).
- Styles / JS: Add semantic tokens or custom rules in `resources/css/app.css` / `tailwind.config.js` and ES module helpers in `resources/js/`.

**New IDE Extension Capability:**
- Collab / Patch Reviewer Extension: Edit TypeScript under `extensions/visionlab-collab/src/`, compile to `extensions/visionlab-collab/out/`, and repackage `.vsix`.
- Note on Deployment Rule (`AGENTS.md`): Laravel/Blade/JS changes are developed locally and deployed via `git push`; core `code-server` and VisionLab Agent fork rebuilds happen on the build server and produce updated artifacts under `docker/workspace/`.

## Special Directories

**`vendor/` & `node_modules/`:**
- Purpose: Composer PHP dependencies and npm frontend/build dependencies.
- Generated: Yes (`composer install`, `npm install`).
- Committed: No (`.gitignore`); `vendor/` is strictly read-only per `AGENTS.md`.

**`storage/`:**
- Purpose: Runtime Laravel logs (`storage/logs/`), compiled Blade views/cache/sessions (`storage/framework/`), uploaded `.vsix` extension artifacts (`storage/app/extensions/`, mounted read-only into workspace containers as `/var/opt/extensions`), GDPR exports, snapshots, and legacy local workspace path (`storage/workspaces/`).
- Generated: Yes (runtime-populated).
- Committed: Directory skeleton only; runtime contents ignored.

**`public/build/`:**
- Purpose: Compiled production Vite assets (`app-*.css`, `app-*.js`, `manifest.json`) referenced by `@vite(...)`.
- Generated: Yes (`npm run build`; `vite.config.js` auto-deletes stale `public/hot` on build).
- Committed: Yes (present in repository for zero-build deployments).

**`docker/workspace/`:**
- Purpose: Contains the sovereign compiled IDE binary archive (`visionlab-ide-1.0.0-linux-amd64.tar.gz`), bundled AI agent (`extensions/visionlab-agent-1.3.39.vsix`), Nix environment templates (`dev.nix`, `dev.nix.template`), and `Dockerfile` used to build `visionlab/workspace:latest`.
- Generated: Partially (compiled IDE tarball and `.vsix` artifacts built via CI/server pipeline).
- Committed: Yes.

**`extensions/visionlab-collab/out/`:**
- Purpose: Compiled JavaScript output and sourcemaps from `extensions/visionlab-collab/src/` (`npm run compile`), packaged into `visionlab-collab-1.0.0.vsix`.
- Generated: Yes (`tsc -p ./`).
- Committed: Yes.

**`.planning/` & `.agents/`:**
- Purpose: `.planning/codebase/` stores GSD codebase mapping documentation consumed by planning/execution workflows; `.agents/` holds local workspace metadata.
- Generated: Yes (by GSD mapper/planning agents).
- Committed: Tracked in project planning workflow (orchestrator manages commits).

---

*Structure analysis: 2026-11-24*

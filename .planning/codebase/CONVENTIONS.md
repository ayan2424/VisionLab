# Coding Conventions

**Analysis Date:** 2026-11-24

## Naming Patterns

**Files:**
- **PHP Classes (PSR-4):** `PascalCase.php` matching class name across `app/` (`App\` namespace), `database/factories/` (`Database\Factories\`), `database/seeders/` (`Database\Seeders\`), and `tests/` (`Tests\`) per `composer.json`.
  - Controllers: `<Domain>Controller.php` in `app/Http/Controllers/` (e.g., `app/Http/Controllers/CourseController.php`, `app/Http/Controllers/WorkspaceFileController.php`). Admin controllers are prefixed with `Admin` inside `app/Http/Controllers/Admin/` (e.g., `app/Http/Controllers/Admin/AdminUserController.php`), except `app/Http/Controllers/Admin/ExtensionController.php`. API controllers live in `app/Http/Controllers/Api/` (e.g., `app/Http/Controllers/Api/AiController.php`).
  - Models: Singular `PascalCase.php` in `app/Models/` (e.g., `app/Models/Workspace.php`, `app/Models/AiPendingPatch.php`, `app/Models/AuditLog.php`).
  - Form Requests: `<Action><Resource>Request.php` in `app/Http/Requests/` and `app/Http/Requests/Admin/` (e.g., `app/Http/Requests/StoreCourseRequest.php`, `app/Http/Requests/GradeSubmissionRequest.php`, `app/Http/Requests/Admin/UpdateQuotaRequest.php`).
  - Policies: `<Resource>Policy.php` in `app/Policies/` (e.g., `app/Policies/WorkspacePolicy.php`, `app/Policies/CoursePolicy.php`, `app/Policies/AiActionPolicy.php`).
  - Services: `<Domain>Service.php` or descriptive noun in `app/Services/` (e.g., `app/Services/AiService.php`, `app/Services/AuditService.php`, `app/Services/CodeServerManager.php`, `app/Services/AiSandbox.php`).
  - Events: Past-tense `PascalCase.php` in `app/Events/` (e.g., `app/Events/PatchProposed.php`, `app/Events/CodeUpdated.php`, `app/Events/ExtensionIntegrityFailed.php`).
  - Jobs: Verb-noun `PascalCase.php` (inconsistently suffixed with `Job`) in `app/Jobs/` (e.g., `app/Jobs/ExecutePlanJob.php`, `app/Jobs/ProcessRecordingJob.php` vs `app/Jobs/SyncWorkspaceExtensions.php`).
  - Notifications: `<Event>Notification.php` in `app/Notifications/` (e.g., `app/Notifications/AssignmentGradedNotification.php`).
  - Console Commands: `<Action>.php` or `<Action>Command.php` in `app/Console/Commands/` (e.g., `app/Console/Commands/WorkspaceCleanup.php`, `app/Console/Commands/UpdateStreaksCommand.php`).
  - Exceptions: `<Reason>Exception.php` in `app/Exceptions/` (e.g., `app/Exceptions/ExtensionIntegrityException.php`).
- **Blade Views & Components:** `kebab-case.blade.php` in `resources/views/components/` (e.g., `resources/views/components/frontend-header.blade.php`, `resources/views/components/frontend-footer.blade.php`, `resources/views/components/stat-card.blade.php`, `resources/views/components/toast-container.blade.php`) and standard RESTful resource view names (`index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`) inside plural domain folders (e.g., `resources/views/courses/`, `resources/views/assignments/`, `resources/views/workspaces/`).
- **Frontend JavaScript / CSS:** `kebab-case` or lowercase single-word modules in `resources/js/` (`app.js`, `bootstrap.js`, `echo.js`, `animations.js`) and `resources/css/app.css`.
- **VS Code Extension TypeScript:** `PascalCase.ts` for class modules under `extensions/visionlab-collab/src/managers/` (`RealtimeManager.ts`, `DocumentSync.ts`, `CursorSync.ts`) and `extensions/visionlab-collab/src/panels/` (`PatchReviewer.ts`, `VideoPanel.ts`), with lowercase entrypoint `extensions/visionlab-collab/src/extension.ts`.

**Functions:**
- **PHP Methods:** `camelCase` across controllers, models, services, and policies.
  - Standard Laravel resourceful actions: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`, plus domain verb methods (`start`, `stop`, `restart`, `suspend`, `activate`, `impersonate`, `exportGdpr`, `readFile`, `writeFile`).
  - Boolean predicates on models use `is*` / `has*` prefixes: `isAdmin()`, `isInstructor()`, `isStudent()`, `hasRole()`, `isActive()`, `isRunning()`, `isOwnedBy()`, `isLate()`, `isEnrolled()` (see `app/Models/User.php`, `app/Models/Workspace.php`, `app/Models/Course.php`).
  - Eloquent local scopes use `scopePascalCase($query, ...)`: `scopeActive()`, `scopeRole()`, `scopeRunning()`, `scopeForUser()`, `scopeRecent()` (`app/Models/User.php`, `app/Models/AuditLog.php`, `app/Models/AnalyticsEvent.php`).
  - Legacy Eloquent accessors use `get<Name>Attribute()`: `getAvatarInitialsAttribute()`, `getStatusBadgeClassAttribute()`, `getStudentCountAttribute()`, `getCoverGradientAttribute()` (`app/Models/User.php`, `app/Models/Submission.php`, `app/Models/Course.php`).
  - Static factory/recorder methods on domain models and services: `AuditLog::record()`, `AnalyticsEvent::track()`, `AuditService::log()`, `GamificationService::evaluateBadges()`, `WorkspaceQuota::resolveFor()`, `UserBadge::awardOnce()`.
- **JavaScript / TypeScript Functions:** `camelCase` (`initEcho`, `vcConfirm`, `vcToast`, `fetchPatches`, `actionPatch`, `resolveWebviewView`). Private helper methods in VS Code webview classes use leading underscore (`_getHtmlForWebview()` in `extensions/visionlab-collab/src/panels/PatchReviewer.ts`).

**Variables:**
- **PHP:** `camelCase` for local variables, properties, and method parameters (`$codeServerManager`, `$activeWorkspacesCount`, `$readAnnouncementIds`). Database column names, JSON payloads, request keys, route names, and event types use `snake_case` (`student_id`, `workspace_id`, `patch_id`, `allow_marketplace`, `workspace_started`, `ai.patch_approved`).
- **PHP Constants:** `UPPER_SNAKE_CASE` (e.g., `PORT_MIN`, `PORT_MAX`, `DOCKER_IMAGE` in `app/Services/CodeServerManager.php`).
- **CSS Custom Properties & Utility Classes:**
  - Application design system uses `--vc-*` CSS variables and `.vc-*` / `.btn-*` classes in `resources/css/app.css` and Tailwind `vc.*` semantic colors in `tailwind.config.js` (`--vc-bg`, `--vc-surface`, `--vc-accent`, `.vc-card`, `.vc-input`).
  - Landing pages (`resources/views/layouts/landing.blade.php`) define a separate set of unprefixed variables (`--background`, `--foreground`, `--cyan`, `--indigo`, `--ease-out-expo`).
- **Environment & Container Variables:** `UPPER_SNAKE_CASE`. Note legacy `VISIONCODE_*` prefix still used for injected workspace container env vars (`VISIONCODE_API_TOKEN`, `VISIONCODE_WORKSPACE_ID`, `VISIONCODE_API_URL`, `VISIONCODE_USER_ID`, `VISIONCODE_USER_NAME` in `app/Services/CodeServerManager.php` and `extensions/visionlab-collab/src/managers/RealtimeManager.ts`), and `window.VisionCode` / `vc_notifs` / `vcConfirm` / `vcToast` on the frontend.

**Types:**
- **PHP:** Classes, interfaces, traits, and exceptions use `PascalCase`.
- **Enums:** Not detected. Despite `AGENTS.md` specifying an `Abilities` enum for Sanctum token abilities, there is no `app/Enums/` directory; roles (`'admin'`, `'instructor'`, `'student'`, `'subscriber'`), statuses (`'pending'`, `'running'`, `'stopped'`, `'error'`, `'approved'`, `'rejected'`), and AI modes (`'CHAT'`, `'PLAN'`, `'AGENT'`) are represented as raw string literals validated via `in:...` rules and `match` expressions.
- **Contracts:** `app/Contracts/` directory exists but is currently empty.
- **TypeScript:** Classes use `PascalCase` (`RealtimeManager`, `DocumentSync`, `PatchReviewer`). Strict mode is enabled in `extensions/visionlab-collab/tsconfig.json` (`"strict": true`), though `any` is widely used for Laravel Echo/Pusher instances and WebSocket payloads (`extensions/visionlab-collab/src/managers/RealtimeManager.ts`).

## Code Style

**Formatting:**
- **EditorConfig (`.editorconfig`):**
  - UTF-8 charset, LF line endings (`end_of_line = lf`), 4-space indentation for PHP/JS/CSS (`indent_style = space`, `indent_size = 4`), 2-space indentation for YAML (`[*.{yml,yaml}]`) except `docker-compose.yml` (4 spaces), final newline inserted, trailing whitespace trimmed (disabled for `*.md`).
  - **Observed drift:** Several PHP and Blade files (`app/Models/Submission.php`, `app/Models/Course.php`, `app/Services/CodeServerManager.php`, `resources/views/layouts/app.blade.php`, `resources/views/layouts/dashboard.blade.php`, `routes/web.php`) contain CRLF line endings from Windows edits.
- **Laravel Pint (`laravel/pint` `^1.13` in `composer.json`):** Installed as a dev dependency with default Laravel preset (no custom `pint.json` exists in the repository root).
  - Visual alignment of `=>` arrows in associative arrays and `=` in adjacent assignments is used frequently in hand-written models, controllers, and tests (e.g., `app/Models/User.php`, `app/Services/AiService.php`, `tests/Feature/SubmissionControllerTest.php`), which diverges slightly from Pint's default single-space alignment unless Pint is run.
  - Pint is **not** wired into `composer.json` scripts or `.github/workflows/*.yml`.
- **Strict Types (`declare(strict_types=1);`):** Not used. Despite `AGENTS.md` referencing "strictly typed PHP 8.3", sampled files across `app/Models/`, `app/Services/`, `app/Http/Controllers/`, `app/Policies/`, `app/Http/Requests/`, `app/Jobs/`, `app/Events/`, and `tests/` consistently omit `declare(strict_types=1);`.
- **PHP Version & Language Features:** `composer.json` declares `"php": "^8.2"` while GitHub Actions (`.github/workflows/production.yml`, `.github/workflows/deploy.yml`) run PHP `8.3`. Code adopts PHP 8.x `match` expressions, union types (`string|array`), nullable types (`?int`, `?array`), named arguments (`AuditLog::record(action: ..., resourceType: ...)` in `app/Services/AuditService.php`), nullsafe operator (`$request->user()?->id`), and typed properties. Constructor property promotion is rarely used in PHP (explicit private property + constructor assignment is the dominant pattern in `app/Services/AiService.php`, `app/Services/AiSandbox.php`, `app/Http/Controllers/WorkspaceController.php`, `app/Http/Controllers/Api/AiController.php`), whereas TypeScript uses parameter properties in `extensions/visionlab-collab/src/panels/PatchReviewer.ts`.

**Linting:**
- **PHP Static Analysis:** None configured. No `phpstan.neon`, `psalm.xml`, or Larastan package exists in `composer.json`.
- **JavaScript / CSS Linting:** None configured. No ESLint, Prettier, or Stylelint config files exist in root `package.json` or `extensions/visionlab-collab/package.json`.
- **TypeScript Type Checking:** `tsc -p ./` with `"strict": true` targeting `ES2022` and `CommonJS` (`extensions/visionlab-collab/tsconfig.json`). Note `// @ts-ignore` is used in `extensions/visionlab-collab/src/managers/RealtimeManager.ts` for assigning `global.Pusher`.
- **Security Static Checks:** `scripts/security_verify.sh` performs 7 automated shell/grep checks (`.env` and `storage/` file permissions, `file_get_contents`/`require_once`/`include_once` without `realpath`, `APP_DEBUG` in production, API rate limiter presence, `SANCTUM_STATEFUL_DOMAINS`, and banned `eval(`/ `exec(`/ `system(` calls in `app/`). Note Check 5 still greps `app/Providers/RouteServiceProvider.php`, whereas Laravel 11 rate limiters live in `app/Providers/AppServiceProvider.php`.

## Import Organization

**Order:**
1. **PHP files:** `namespace ...;` followed by top-level `use` statements, typically grouped as `App\*` imports first, then `Illuminate\*`, then third-party (`GuzzleHttp\*`, `Symfony\*`, `NotificationChannels\*`), generally sorted alphabetically within groups (e.g., `app/Http/Controllers/WorkspaceController.php`, `app/Services/AiService.php`).
   - **Observed inconsistency:** Many controllers, jobs, observers, providers, and routes mix top-level `use` imports with inline fully-qualified class names (FQCNs) such as `\App\Models\WorkspaceQuota::resolveFor(...)`, `\Illuminate\Support\Facades\Log::info(...)`, `\App\Events\PatchProposed`, and `\App\Models\AnalyticsEvent::track(...)` (see `app/Providers/AppServiceProvider.php`, `app/Http/Controllers/WorkspaceController.php`, `app/Jobs/ExecutePlanJob.php`, `app/Observers/AnalyticsObserver.php`, `routes/web.php`).
2. **JavaScript (`resources/js/`):** ES module imports (`"type": "module"` in `package.json`), importing local side-effect modules (`./bootstrap`, `./echo`) and npm packages (`alpinejs`, `axios`, `laravel-echo`, `pusher-js`) and attaching singletons to `window` (`window.Alpine`, `window.axios`, `window.Pusher`, `window.initEcho`).
3. **TypeScript (`extensions/visionlab-collab/src/`):** External SDKs (`vscode`, `laravel-echo`, `pusher-js`) first, followed by relative imports (`./managers/*`, `./panels/*`).

**Path Aliases:**
- **PHP:** PSR-4 autoload namespaces in `composer.json`: `App\` -> `app/`, `Database\Factories\` -> `database/factories/`, `Database\Seeders\` -> `database/seeders/`, `Tests\` -> `tests/`.
- **Frontend / Vite (`vite.config.js`):** No custom `@/` path aliases configured; relative imports (`./bootstrap`, `./echo`) are used in `resources/js/`. Entry points are `['resources/css/app.css', 'resources/js/app.js']`. Public landing layout (`resources/views/layouts/landing.blade.php`) uses a native browser `<script type="importmap">` for `three` and `three/addons/` from jsDelivr CDN.
- **Blade Components:** Anonymous components under `resources/views/components/` are invoked with `<x-component-name />` (e.g., `<x-frontend-header />`, `<x-frontend-footer />`, `<x-sidebar />`, `<x-topbar />`, `<x-toast-container />`, `<x-card>`, `<x-button>`, `<x-modal>`). Class-backed layout components exist in `app/View/Components/AppLayout.php` (`<x-app-layout>`) and `app/View/Components/GuestLayout.php` (`<x-guest-layout>`).

## Error Handling

**Patterns:**
- **Authorization & Access Control:**
  - Base controller `app/Http/Controllers/Controller.php` includes `Illuminate\Foundation\Auth\Access\AuthorizesRequests`.
  - Resource controllers invoke `$this->authorize('ability', $model)` directly (e.g., `app/Http/Controllers/WorkspaceController.php`, `app/Http/Controllers/CourseController.php`, `app/Http/Controllers/WorkspaceFileController.php`), triggering Laravel's 403 `AuthorizationException`.
  - Policies reside in `app/Policies/` (13 policies; `CoursePolicy` is explicitly registered in `app/Providers/AppServiceProvider.php`, others rely on Laravel auto-discovery).
  - Route-level RBAC uses custom middleware alias `role:<roles>` (`app/Http/Middleware/RoleMiddleware.php`, registered in `bootstrap/app.php`), which redirects guests to `login` and calls `abort(403, ...)` for mismatched roles.
  - Global web middleware appends `CheckMaintenanceMode`, `SecurityHeaders`, and `CheckAccountStatus` in `bootstrap/app.php`.
  - **Gap to note:** `app/Http/Controllers/Api/AiController.php` (`approvePatch`, `rejectPatch`, `pendingPatches`) and `app/Http/Controllers/Admin/AdminUserController.php` do not call `$this->authorize(...)` even though `app/Policies/AiActionPolicy.php` and `app/Policies/UserPolicy.php` exist.
- **Validation:**
  - Dedicated `FormRequest` classes in `app/Http/Requests/` (25 files) are used for core LMS forms (`StoreCourseRequest`, `UpdateCourseRequest`, `GradeSubmissionRequest`, etc.). Both pipe-delimited string rules (`StoreCourseRequest.php`) and array rules (`StoreWorkspaceRequest.php`) appear across requests.
  - Inline `$request->validate([...])` is still used extensively in controllers (`app/Http/Controllers/WorkspaceController.php`, `app/Http/Controllers/WorkspaceFileController.php`, `app/Http/Controllers/Admin/AdminUserController.php`)—even where a FormRequest exists (e.g., `StoreWorkspaceRequest.php` is unused by `WorkspaceController::store()`).
- **Web vs. JSON Error Responses:**
  - Web controllers flash session errors via `return redirect()->back()->with('error', ...)` or `return back()->withErrors(['error' => ...])`, rendered globally in `resources/views/layouts/dashboard.blade.php`.
  - JSON/API controllers return structured JSON with explicit HTTP status codes: `400` (missing param / invalid state), `401` (invalid bearer token), `403` (policy/sandbox denial), `404` (missing resource/file), `409` (conflict), `422` (validation), `500`/`503` (infrastructure failure).
  - SSE streaming (`app/Services/AiService.php` + `app/Http/Controllers/Api/AiController.php`) catches `\Throwable` / `\Exception` inside the generator, logs via `Log::error()`, yields an OpenAI-formatted error delta chunk, and terminates cleanly with `data: [DONE]\n\n`.
- **Domain Exceptions & Graceful Degradation:**
  - Custom exception `App\Exceptions\ExtensionIntegrityException` (`app/Exceptions/ExtensionIntegrityException.php`) is thrown on `.vsix` SHA256 verification failures and caught in `app/Jobs/SyncWorkspaceExtensions.php` to mark `sync_status = 'failed'`.
  - Infrastructure probes (`app/Http/Controllers/HealthController.php`) wrap each dependency check in `try { ... } catch (\Throwable)` and downgrade status to `'degraded'` / HTTP `503`.
  - `app/Services/CodeServerManager.php` degrades to `devFallback()` when Docker is unavailable and swallows non-critical extension setup errors with `Log::warning(...)`.
  - `bootstrap/app.php` currently leaves `withExceptions()` empty; custom error pages exist in `resources/views/errors/` (`403`, `404`, `419`, `500`, `maintenance`, `offline`).
- **Model Mass-Assignment Protection:**
  - All inspected models declare explicit `protected $fillable = [...]` arrays (complying with `AGENTS.md` Directive #2; no `$guarded = []` observed). Sensitive secrets (`password`, `remember_token`, `vercel_token`, `railway_token`) are listed in `$hidden`, with external API tokens using Laravel's `'encrypted'` cast (`app/Models/User.php`).
  - **Cast style divergence:** Newer models use Laravel 11 `protected function casts(): array` (`User.php`, `Workspace.php`, `AuditLog.php`, `AnalyticsEvent.php`), while older LMS models still use `protected $casts = [...]` (`Course.php`, `Submission.php`).

## Logging

**Framework:**
- **Application Logs:** Laravel `Log` facade backed by Monolog (`config/logging.php`), configured with `\Monolog\Formatter\JsonFormatter::class` on both `single` and `daily` channels for structured JSON log output.
- **Security Audit Trail:** Custom `App\Services\AuditService` (`app/Services/AuditService.php`) wrapping `App\Models\AuditLog::record()` (`app/Models/AuditLog.php`), capturing `actor_id`, `action`, `resource_type`, `resource_id`, `old_state`, `new_state`, `result`, `ip_address`, `user_agent`, and UUID `correlation_id`.
- **Telemetry / Forensics Events:** Custom `App\Models\AnalyticsEvent::track()` (`app/Models/AnalyticsEvent.php`) + automatic Eloquent model observer `App\Observers\AnalyticsObserver` attached to `Submission`, `User`, and `Workspace` in `app/Providers/AppServiceProvider.php`. AI-specific actions are logged in `App\Models\AiActionsLog` (`app/Services/AiService.php`, `app/Services/AiSandbox.php`, `app/Http/Controllers/WorkspaceFileController.php`).
- **Divergence from `AGENTS.md`:** `AGENTS.md` specifies `spatie/laravel-activitylog`, which is **not installed** in `composer.json`. However, `app/Jobs/ExecutePlanJob.php` (line 49) still calls `activity('ai_operations')->performedOn($patch)...->log('applied_patch')`, which will fail at runtime unless replaced with `AuditService::log()` or the package is added.
- **Frontend / Extension:** Standard `console.log` / `console.error` in Blade scripts (`resources/views/layouts/app.blade.php`) and VS Code extension TypeScript (`extensions/visionlab-collab/src/`).

**Patterns:**
- Use `Log::info()`, `Log::warning()`, and `Log::error()` with structured context arrays (`Log::error("CodeServerManager: Failed to start container", ['workspace' => $workspace->id, 'error' => ...])` in `app/Services/CodeServerManager.php`).
- Prefix service logs with the class name (`"CodeServerManager: ..."`, `"AiService error: ..."`).
- Record security denials and sensitive mutations via `AuditService::log()` (dot-delimited action names like `'user.suspended'`, `'ai.patch_approved'`, `'workspace.started'`) and user activity telemetry via `AnalyticsEvent::track()` (snake_case event types like `'workspace_started'`, `'submission_created'`).

## Comments

**When to Comment:**
- Class-level docblocks explain architectural intent, lifecycle states, and invariants (e.g., `Workspace` states `pending -> running -> stopped | error` in `app/Models/Workspace.php`; HITL invariant in `app/Services/AiService.php`; dev vs. prod container behavior in `app/Services/CodeServerManager.php`).
- Unicode box-drawing section dividers are the standard visual organizing convention across Models, Services, Controllers, Routes, CSS, and Tests:
  - Major sections: `// ══════════════════════════════════════════════════════════════════════`
  - Subsections: `// ── Relationships ───────────────────────────────────────────────`, `// ── Scopes ───`, `// ── File I/O API ───`
  - Blade/CSS headers: `{{-- ═══ SIDEBAR ═══ --}}`, `/* ─── Component Library ─── */`
- Inline comments explain non-obvious operational workarounds (e.g., Replit proxy URL forcing in `app/Providers/AppServiceProvider.php`, stale `public/hot` cleanup in `vite.config.js`, `.vision` directory root-ownership protection in `app/Services/CodeServerManager.php`, echo-loop prevention in `extensions/visionlab-collab/src/managers/DocumentSync.ts`).
- **Divergence from `AGENTS.md`:** Despite `AGENTS.md` Directive #1 ("Zero Placeholders"), occasional placeholder comments and stubs remain (e.g., `$buildTree` comment `(Placeholder for complex nested tree if required by UI)` in `app/Services/CodeServerManager.php:594`, demo-mode placeholder string in `app/Services/AiService.php:279`, and empty `@php @endphp` / theme-flash IIFE blocks in `resources/views/layouts/app.blade.php` and `resources/views/layouts/dashboard.blade.php`).

**PHPDoc / JSDoc / TSDoc:**
- **PHP:** Used selectively on public service methods, policies, and complex return shapes (e.g., `@return array{url: string, port: int, token: string, container_id: string|null}` in `app/Services/CodeServerManager.php`, `@return array{0: array<string, bool>, 1: string}` in `app/Http/Controllers/HealthController.php`, `@use HasFactory<\Database\Factories\UserFactory>` in `app/Models/User.php`). One-line `/** ... */` docblocks are standard on policy methods (`app/Policies/*.php`) and controller endpoints (`app/Http/Controllers/WorkspaceController.php`).
- **JS/TS:** Minimal JSDoc comments on config helpers (`resources/js/echo.js`, `tailwind.config.js` `/** @type {import('tailwindcss').Config} */`); TypeScript extension relies primarily on TypeScript type signatures rather than TSDoc blocks.

## Function Design

**Size:**
- Controllers generally keep individual CRUD actions concise (10-35 lines) and delegate container/AI/gamification logic to `app/Services/`.
- Complex orchestrators contain large methods that would benefit from decomposition: `CodeServerManager::startWorkspace()` spans ~245 lines (`app/Services/CodeServerManager.php:39-285`), and `AiService::handleChatCompletion()` spans ~235 lines (`app/Services/AiService.php:35-269`).
- Shared helper extraction is preferred within controllers (e.g., `HealthController::runProbes()`, `WorkspaceFileController::resolveWorkspace()` and `isDangerousPath()`).

**Parameters:**
- Route-model binding (`Workspace $workspace`, `User $user`, `AiPendingPatch $patch`) is used in controllers, with `Workspace::getRouteKeyName()` returning `'slug'` (`app/Models/Workspace.php:59`). Note `CourseController` still accepts `string $slug` and runs `Course::where('slug', $slug)->firstOrFail()` manually (`app/Http/Controllers/CourseController.php`).
- Service and helper methods use type-hinted parameters with sensible defaults (`string $mode = 'CHAT'`, `?int $userId = null`, `int $days = 30`).
- Named arguments are used when invoking multi-optional-parameter methods (`AuditLog::record(action: ..., resourceType: ..., ...)` in `app/Services/AuditService.php`).

**Return Values:**
- API controller methods explicitly type `: JsonResponse` (`app/Http/Controllers/WorkspaceController.php`, `app/Http/Controllers/WorkspaceFileController.php`, `app/Http/Controllers/HealthController.php`), while web view/redirect actions frequently omit return types (returning `View|RedirectResponse`).
- Policies strictly type `: bool`; Eloquent relationships strictly type `: BelongsTo`, `: HasMany`, `: HasOne`, `: BelongsToMany`.
- `AiService::handleChatCompletion()` omits a return type because it is a PHP `Generator` yielding SSE strings.
- `CodeServerManager::startWorkspace()` is typed `: array` in its signature (`app/Services/CodeServerManager.php:39`) yet has a `return false;` error branch on line 167 (a latent `TypeError` when Docker fails).

## Module Design

**Exports:**
- **PHP:** One class per file under PSR-4 namespaces. Service dependencies are injected via Laravel's IoC container in constructors (`WorkspaceController`, `WorkspaceFileController`, `AiController`, `AiService`, `AiSandbox`) or method injection on queued job `handle()` methods (`ExecutePlanJob`, `SyncWorkspaceExtensions`). Static service classes (`AuditService`, `GamificationService`) expose static methods without instantiation.
- **Blade & Frontend Architecture:**
  - **Global Public Components (`AGENTS.md` Directive #9):** Public marketing pages (`resources/views/layouts/landing.blade.php`, `resources/views/welcome.blade.php`, `resources/views/pages/*.blade.php`) compose `<x-frontend-header />` (`resources/views/components/frontend-header.blade.php`) and `<x-frontend-footer />` (`resources/views/components/frontend-footer.blade.php`).
  - **Authenticated Shells:** `resources/views/layouts/dashboard.blade.php` (also extended by `resources/views/layouts/admin.blade.php`) uses `@extends` / `@yield('content')` with `<x-sidebar />` and `<x-topbar />`; `resources/views/layouts/app.blade.php` uses component slot `{{ $slot }}` with `@include('layouts.navigation')` and `<x-toast-container />`.
  - **UI Components:** Anonymous Blade components in `resources/views/components/` declare `@props([...])`, compute Tailwind class strings via `@php ... match(...) @endphp`, and merge caller classes with `{{ $attributes->merge(['class' => ...]) }}` (`resources/views/components/button.blade.php`, `resources/views/components/card.blade.php`, `resources/views/components/modal.blade.php`).
  - **Interactivity:** Alpine.js (`alpinejs` `^3.4.2`, started in `resources/js/app.js`) powers stateful UI widgets such as `<x-modal>` (`x-data`, `x-show`, `x-on:open-modal-*.window`, `x-transition`), while Vanilla ES2022 JS handles scroll animations (`resources/js/animations.js`), lazy Laravel Echo setup (`resources/js/echo.js`), PWA install/push registration (`resources/views/layouts/app.blade.php`), and global helpers (`window.vcConfirm`, `window.vcToast`, `window.VisionCode.toast`). No jQuery is used.
  - **Dark Theme System (Observed Divergence):** Dark mode is forced on load (`document.documentElement.classList.add('dark')` in `resources/js/app.js`, `darkMode: 'class'` in `tailwind.config.js`). Note that three overlapping palettes currently coexist across the repo: `AGENTS.md` mandates strict `#0a0a0a` void dark (`tailwind.config.js` `void: '#0a0a0a'`), `color-palette-reference.md` marks "Firebase × Amber" (`#F05000` / `#FF6B2B`) as current, `resources/css/app.css` actually sets `:root` to `--vc-bg: #060608`, `--vc-accent: #B026FF` (Electric Purple), and `--vc-ring: #00F3FF` (Neon Cyan), and `frontend-header.blade.php`/`frontend-footer.blade.php` use GitHub-dark hex values (`#040d21`, `#238636`, `#7d8590`). New UI work should standardize on the semantic `vc-*` CSS tokens in `resources/css/app.css` and `tailwind.config.js`.
- **TypeScript Extension (`extensions/visionlab-collab/src/`):** Uses named class exports (`export class RealtimeManager`, `export class DocumentSync`, `export class PatchReviewer`) wired together in `extensions/visionlab-collab/src/extension.ts` (`export function activate`, `export function deactivate`).

**Barrel Files:**
- Not used. PHP uses PSR-4 autoloading; `resources/js/` imports sibling files directly (`./bootstrap`, `./echo`); `extensions/visionlab-collab/src/extension.ts` imports each manager and panel by explicit file path.

---

*Convention analysis: 2026-11-24*

# Testing Patterns

**Analysis Date:** 2026-11-24

## Test Framework

**Runner:**
- PHPUnit `^11.0.1` (`phpunit/phpunit` in `composer.json`), invoked via Laravel's Artisan test wrapper (`nunomaduro/collision` `^8.1`) or directly via `./vendor/bin/phpunit`.
- Config: `phpunit.xml`
  - Test suites: `Unit` (`tests/Unit`) and `Feature` (`tests/Feature`).
  - Source coverage target: `app/` (`<source><include><directory>app</directory></include></source>`).
  - Isolated testing environment variables injected via `<php>`:
    - `APP_ENV=testing`, `BCRYPT_ROUNDS=4`
    - `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` (note: GitHub Actions overrides this to a file-backed `database/database.sqlite` in `.github/workflows/production.yml` and `.github/workflows/deploy.yml`)
    - `CACHE_STORE=array`, `SESSION_DRIVER=array`, `MAIL_MAILER=array`, `QUEUE_CONNECTION=sync`
    - `PULSE_ENABLED=false`, `TELESCOPE_ENABLED=false`
    - `BROADCAST_CONNECTION=pusher` with dummy `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET` (overridden to `'null'` per-test inside `tests/TestCase.php` unless explicitly re-enabled by a broadcast test).

**Assertion Library:**
- PHPUnit assertions (`$this->assertEquals()`, `$this->assertSame()`, `$this->assertNotNull()`, `$this->assertCount()`, `$this->assertTrue()`, `$this->assertFileExists()`, `$this->expectException()`).
- Laravel HTTP, Database, View, and Facade assertions (`assertOk()`, `assertStatus()`, `assertForbidden()`, `assertUnauthorized()`, `assertRedirect()`, `assertJsonPath()`, `assertJsonStructure()`, `assertJsonFragment()`, `assertJsonCount()`, `assertViewIs()`, `assertViewHas()`, `assertViewHasAll()`, `assertSessionHas()`, `assertSessionHasErrors()`, `assertDatabaseHas()`, `assertDatabaseMissing()`, `assertModelExists()`, `assertModelMissing()`, `Event::assertDispatched()`, `Queue::assertPushed()`, `Notification::assertSentTo()`).

**Run Commands:**
```bash
php artisan test                                    # Run all Unit and Feature tests
php artisan test --testsuite=Feature                # Run Feature suite only (used in deploy.yml)
php artisan test --testsuite=Unit                   # Run Unit suite only
php artisan test --filter=CourseControllerTest      # Run a single test class or method
./vendor/bin/phpunit                                # Run PHPUnit directly
php artisan test --coverage                         # Run with coverage (requires Xdebug or PCOV)
bash scripts/security_verify.sh                     # Run 7 automated OWASP ASVS security checks
```
*(No watch-mode test runner or `composer test` script alias is defined in `composer.json`.)*

## Test File Organization

**Location:**
- Separate top-level `tests/` directory (not co-located with `app/`).
- No JavaScript/TypeScript test suite exists for `resources/js/` or `extensions/visionlab-collab/`.
- No `tests/Browser/` directory exists (Laravel Dusk is not installed).

**Naming:**
- `<Subject>Test.php` in `PascalCase` matching the controller, domain, or security concern tested (e.g., `CourseControllerTest.php`, `WorkspaceSecurityTest.php`, `ExtensionSupplyChainTest.php`).
- Test methods use `snake_case` prefixed with `test_` (PHPUnit classic convention rather than `#[Test]` attributes), e.g., `test_student_can_join_course_with_valid_code(): void`. Newer tests declare `: void` return types; several older Phase 11 tests omit `: void` (`SecurityHardeningTest.php`, `WorkspaceSecurityTest.php`, `LmsLifecycleTest.php`).

**Structure:**
```
tests/
├── TestCase.php                          # Base Laravel TestCase (disables broadcasting by default)
├── Unit/
│   └── ExampleTest.php                   # Single placeholder unit test
└── Feature/
    ├── AdminControllerTest.php           # Admin RBAC, user CRUD, suspend/activate, analytics
    ├── AiAgentAuthorizationTest.php      # Direct AI write prohibition (403)
    ├── AiControllerTest.php              # SSE completions, HITL patch approve/reject, plan exec
    ├── ApiRateLimitingTest.php           # X-RateLimit-* header verification
    ├── ContributionControllerTest.php    # 365-day heatmap API & RBAC
    ├── CourseControllerTest.php          # Course CRUD, enrollment codes, policies, suspension
    ├── DashboardTest.php                 # Role-based dashboard routing & view data contracts
    ├── DeploymentControllerTest.php      # Vercel/Railway deploys (NOTE: drifted — see Gaps)
    ├── EnrollmentControllerTest.php      # Code join, duplicate prevention, instructor guard
    ├── ExampleTest.php                   # Root GET / smoke check
    ├── ExtensionControllerTest.php       # Admin extension CRUD, built-in delete guard
    ├── ExtensionRegistryTest.php         # GET /api/extensions checksum listing
    ├── ExtensionSupplyChainTest.php      # VSIX SHA256 mismatch exception & event dispatch
    ├── ForensicsAuditTest.php            # VisionGuard telemetry sync endpoint
    ├── LmsLifecycleTest.php              # Course + assignment creation flow
    ├── ProfileTest.php                   # Breeze profile update & account deletion
    ├── RealtimeCollaborationTest.php     # /broadcasting/auth presence channel signing
    ├── RecordingControllerTest.php       # Recording start/stop/approve/reject/playback RBAC
    ├── RoleBasedAccessTest.php           # /admin 403 vs 200 role check
    ├── RoomControllerTest.php            # Collab room broadcasts (NOTE: drifted — see Gaps)
    ├── SecurityHardeningTest.php         # Login throttle, path traversal, .env write denial
    ├── SubmissionControllerTest.php      # Queue, start, duplicate guard, PATCH grading, bounds
    ├── VideoRoomControllerTest.php       # Jitsi JWT & call lifecycle (NOTE: drifted — see Gaps)
    ├── WorkspaceAccessTest.php           # Cross-student 403 & instructor course access 200
    ├── WorkspaceControllerTest.php       # Workspace lifecycle & file I/O (NOTE: drifted)
    ├── WorkspaceSecurityTest.php         # File read traversal 404 & isolation 403
    ├── WorkspaceTest.php                 # CodeServerManager path & WorkspaceQuota priority
    └── Auth/
        ├── AuthenticationTest.php        # Breeze login/logout
        ├── EmailVerificationTest.php     # Breeze email verification
        ├── PasswordConfirmationTest.php  # Breeze password confirmation
        ├── PasswordResetTest.php         # Breeze password reset
        ├── PasswordUpdateTest.php        # Breeze password update
        └── RegistrationTest.php          # Breeze registration
```
*(Total: 34 test files — 1 Unit, 27 top-level Feature, 6 Auth Feature.)*

## Test Structure

**Suite Organization:**
Base setup in `tests/TestCase.php` neutralizes broadcasting so model/controller events never attempt live WebSocket network calls during tests:
```php
// tests/TestCase.php
abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['broadcasting.default' => 'null']);
    }
}
```

Feature tests extend `Tests\TestCase`, apply `use RefreshDatabase;`, organize tests by domain subsections using `// ── Section ───` banners, and extract repeated actor/fixture builders into `private` helper methods (from `tests/Feature/SubmissionControllerTest.php`):
```php
class SubmissionControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createEnrolledStudent(): array
    {
        $instructor = User::factory()->instructor()->create();
        $student    = User::factory()->student()->create();
        $course     = Course::factory()->create(['instructor_id' => $instructor->id]);

        $course->enrollments()->create([
            'student_id' => $student->id,
            'status'     => 'active',
        ]);

        $assignment = Assignment::factory()->create([
            'course_id'  => $course->id,
            'max_points' => 100,
            'due_date'   => now()->addDays(7),
        ]);

        return compact('instructor', 'student', 'course', 'assignment');
    }

    // ── Grade (uses PATCH) ────────────────────────────────────────────────

    public function test_instructor_can_grade_submission(): void
    {
        $data = $this->createEnrolledStudent();

        $submission = Submission::factory()->create([
            'student_id'    => $data['student']->id,
            'assignment_id' => $data['assignment']->id,
            'status'        => 'submitted',
            'submitted_at'  => now(),
        ]);

        $response = $this->actingAs($data['instructor'])
                         ->patch(route('submissions.grade', $submission->id), [
                             'grade'    => 85,
                             'feedback' => 'Good work!',
                         ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('submissions', [
            'id'     => $submission->id,
            'status' => 'graded',
            'grade'  => 85,
        ]);
    }
}
```

**Patterns:**
- **Setup pattern:** `use RefreshDatabase;` on every Feature test. Tests that read/write cache (e.g., `tests/Feature/DashboardTest.php`) override `setUp()` and call `Cache::flush()`. Broadcast auth tests (`tests/Feature/RealtimeCollaborationTest.php`) explicitly restore `config(['broadcasting.default' => 'pusher', ...])` inside the test method.
- **Authentication pattern:** Session-based `$this->actingAs($user)` is used universally across both web routes and Sanctum-protected `/api/*` endpoints (relying on `$middleware->statefulApi()` in `bootstrap/app.php` rather than `Sanctum::actingAs()`).
- **Teardown pattern:** Database state is rolled back automatically by `RefreshDatabase`. Tests that write temporary files to `storage_path()` perform inline manual cleanup at the end of the test (`@unlink($vsixFile)`, `File::deleteDirectory($wsDir)` in `tests/Feature/ExtensionSupplyChainTest.php`, `tests/Feature/AiControllerTest.php`, `tests/Feature/WorkspaceControllerTest.php`) rather than in `tearDown()` (note: cleanup is skipped if an earlier assertion or exception aborts the method, as in `ExtensionSupplyChainTest::test_extension_integrity_exception_on_mismatch()`).
- **Assertion pattern:** Prefer named routes (`route('courses.store')`, `route('admin.extensions.toggle', $extension->id)`), fluent response assertions chained onto HTTP calls, followed by database state verification (`$this->assertDatabaseHas(...)`, `$model->fresh()`).

## Mocking

**Framework:**
- Laravel built-in Facade Fakes (`Event::fake()`, `Queue::fake()`, `Notification::fake()`, `Http::fake()`, `Storage::shouldReceive()`).
- PHPUnit `$this->createMock()` bound into the service container via `$this->app->instance(...)`.
- `mockery/mockery` `^1.6` is installed in `composer.json` (used implicitly under Laravel's `Facade::shouldReceive()`).

**Patterns:**
1. **Service Container Mock + HTTP Fake** (from `tests/Feature/DeploymentControllerTest.php`):
```php
config(['visionlab.deploy.vercel_token' => 'mock-vercel-token']);

// Mock CodeServerManager
$csmMock = $this->createMock(CodeServerManager::class);
$csmMock->method('listFiles')->willReturn([
    ['name' => 'index.html', 'type' => 'file', 'path' => 'index.html']
]);
$csmMock->method('readFile')->willReturn('<h1>Hello World</h1>');
$this->app->instance(CodeServerManager::class, $csmMock);

// Fake Vercel API Response
Http::fake([
    'api.vercel.com/*' => Http::response([
        'id'  => 'dpl_12345abcde',
        'url' => 'visionlab-test.vercel.app',
    ], 200)
]);
```

2. **Scoped Event Faking** (fake only specific events so Eloquent model observers/boot hooks still execute — from `tests/Feature/AiControllerTest.php` and `tests/Feature/RoomControllerTest.php`):
```php
Event::fake([PatchProposed::class]);
// ... execute request ...
$response->sendContent(); // Trigger StreamedResponse generator execution
Event::assertDispatched(PatchProposed::class);
```

3. **Queue, Notification, and Storage Faking** (from `tests/Feature/RecordingControllerTest.php`):
```php
Queue::fake();
// ... POST stop recording ...
Queue::assertPushed(ProcessRecordingJob::class, function ($job) use ($recording) {
    return $job->recordingId === $recording->id;
});

Notification::fake();
// ... POST approve recording ...
Notification::assertSentTo($student, RecordingApprovedNotification::class);

Storage::shouldReceive('temporaryUrl')
    ->andReturn('https://s3.amazonaws.com/my-bucket/recordings/test.mp4');
```

**What to Mock:**
- Outbound third-party HTTP APIs (`api.vercel.com/*`, `backboard.railway.app/*`, Anthropic Claude API via `Http::fake` or leaving `ANTHROPIC_API_KEY` empty to trigger `AiService` demo mode).
- Docker/container orchestration (`CodeServerManager`) when testing higher-level controllers so tests remain deterministic without a Docker daemon.
- Broadcast events (`Event::fake([...])`), async jobs (`Queue::fake()`), notifications (`Notification::fake()`), and S3 presigned URLs (`Storage::shouldReceive('temporaryUrl')`).

**What NOT to Mock:**
- Eloquent models, database queries, migrations, and SQLite persistence (tested against real in-memory SQLite via `RefreshDatabase`).
- Laravel routing, middleware (`RoleMiddleware`, `CheckAccountStatus`, `throttle:*`), FormRequest validation, and Authorization Policies (`CoursePolicy`, `WorkspacePolicy`, `SubmissionPolicy`).

## Fixtures and Factories

**Test Data:**
Model factories live in `database/factories/` and expose fluent state methods matching domain roles and lifecycles (from `database/factories/UserFactory.php` and `database/factories/WorkspaceFactory.php`):
```php
// database/factories/UserFactory.php
public function admin(): static
{
    return $this->state(fn () => ['role' => 'admin']);
}

public function instructor(): static
{
    return $this->state(fn () => ['role' => 'instructor']);
}

public function student(): static
{
    return $this->state(fn () => ['role' => 'student']);
}

public function suspended(): static
{
    return $this->state(fn () => ['status' => 'suspended']);
}
```
```php
// Usage in Feature tests
$admin      = User::factory()->admin()->create();
$instructor = User::factory()->instructor()->create();
$student    = User::factory()->student()->create();
$suspended  = User::factory()->suspended()->create();
$workspace  = Workspace::factory()->running()->create(['student_id' => $student->id]);
$submission = Submission::factory()->graded(92)->create(['student_id' => $student->id]);
$extension  = Extension::factory()->withArtifact()->builtin()->create();
```

**Location:**
- `database/factories/` (14 factories):
  - `AiChatSessionFactory.php`
  - `AiPendingPatchFactory.php` (states: `approved()`, `rejected()`, `expired()`)
  - `AnalyticsEventFactory.php`
  - `AnnouncementFactory.php`
  - `AssignmentFactory.php`
  - `AuditLogFactory.php`
  - `CourseFactory.php` (state: `inactive()`)
  - `DeploymentFactory.php` (states: `deployed()`, `failed()` — references removed `App\Models\Deployment`)
  - `EnrollmentFactory.php`
  - `ExtensionFactory.php` (states: `required()`, `builtin()`, `withArtifact()`)
  - `SubmissionFactory.php` (states: `submitted()`, `graded(int $grade = 85)`)
  - `UserBadgeFactory.php`
  - `UserFactory.php` (states: `unverified()`, `admin()`, `instructor()`, `student()`, `suspended()`)
  - `WorkspaceFactory.php` (states: `running()`, `stopped()`, `error()`)
- Note: 40+ newer ERP/LMS/AI models (e.g., `Campus`, `Department`, `CourseBatch`, `FeeChallan`, `LibraryBook`, `WorkspaceTemplate`, `WorkspaceQuota`, `Recording`, `Quiz`, `Rubric`) do not have factories yet; tests instantiate those directly with `Model::create([...])` (see `tests/Feature/WorkspaceTest.php`, `tests/Feature/RecordingControllerTest.php`).

## Coverage

**Requirements:** None enforced.
- `.github/workflows/production.yml` runs `setup-php` with `coverage: none` and executes `php artisan test`.
- `.github/workflows/deploy.yml` executes `php artisan test --testsuite=Feature` without coverage thresholds or Pint checks.

**View Coverage:**
```bash
php artisan test --coverage
php artisan test --coverage-html=coverage-report   # Requires Xdebug or PCOV extension
```

## Test Types

**Unit Tests:**
- Located in `tests/Unit/`.
- Currently contains only Laravel's default placeholder `tests/Unit/ExampleTest.php` (`test_that_true_is_true`). Pure service logic (`WorkspaceQuota::resolveFor()`, `CodeServerManager::resolveSecurePath()`, `CodeServerManager::installExtension()`, `GamificationService`, `AiSandbox::getPhpNativeDiff()`) is either tested inside `tests/Feature/` or untested.

**Integration / Feature Tests:**
- Located in `tests/Feature/` and `tests/Feature/Auth/` (33 files).
- Exercise the full HTTP kernel (routing -> middleware -> FormRequest -> policy -> controller -> Eloquent SQLite -> Blade/JSON response), RBAC denial paths (`403` / `401` / `302`), rate limit headers, broadcast channel authentication, and supply-chain checksum exceptions.
- **Known Test Suite Drift (Critical Observation):** Several Feature tests were written for earlier phases and have drifted from subsequent code refactors:
  - `tests/Feature/DeploymentControllerTest.php` imports `App\Models\Deployment` (missing from `app/Models/`).
  - `tests/Feature/VideoRoomControllerTest.php` and `tests/Feature/RoomControllerTest.php` import `App\Models\Room`, `App\Models\RoomMember`, `App\Events\VideoCallStarted`, `App\Events\VideoCallEnded`, and `App\Events\UserJoinedRoom` (none currently exist in `app/Models/` or `app/Events/`).
  - `tests/Feature/WorkspaceControllerTest.php`, `tests/Feature/WorkspaceTest.php`, and `tests/Feature/AiControllerTest.php` test local host directories (`storage_path('workspaces/ws-...')`), `CodeServerManager::resolveSecurePath()`, and numeric `$workspace->id` route parameters, whereas `CodeServerManager` migrated to Docker Native Volumes (`resolveSecurePath()` now returns `null`) and `Workspace::getRouteKeyName()` now binds on `'slug'`.

**E2E / Browser & Security Tests:**
- **Browser E2E:** Not used. Although `AGENTS.md` Phase 11 calls for Laravel Dusk browser tests, `laravel/dusk` is not installed in `composer.json` and `tests/Browser/` does not exist.
- **Automated Security Verification Script:** `scripts/security_verify.sh` provides 7 shell-based OWASP ASVS compliance checks (file permissions, `realpath()` grep, `APP_DEBUG`, rate limiter config, Sanctum domains, and banned `eval`/`exec`/`system` grep) intended for pre-deployment verification.

## Common Patterns

**Async / Queued / Streamed Testing:**
- Queued jobs run synchronously by default (`QUEUE_CONNECTION=sync` in `phpunit.xml`), or are intercepted via `Queue::fake()` + `Queue::assertPushed()` (`tests/Feature/RecordingControllerTest.php`).
- Server-Sent Events (`StreamedResponse` generators in `AiController::chatCompletions()`) must invoke `$response->sendContent()` in tests to execute the streaming closure and trigger side effects/events (from `tests/Feature/AiControllerTest.php`):
```php
Event::fake([PatchProposed::class]);

$response = $this->actingAs($setup['student'])
                 ->withHeader('X-Workspace-Id', 'ws-' . $setup['workspace']->id)
                 ->post(route('api.ai.chat.completions'), [
                     'messages' => [
                         ['role' => 'user', 'content' => 'Please fix this file.'],
                     ],
                     'stream' => true,
                 ]);

$response->assertOk();
$this->assertStringContainsString('text/event-stream', $response->headers->get('Content-Type'));

// Execute the streamed response callback to trigger generator iteration
$response->sendContent();

Event::assertDispatched(PatchProposed::class);
```

**Error & Security Testing:**
- **Role/Policy Denials:** Explicitly test both positive (allowed role) and negative (unauthorized role, cross-tenant student, guest, suspended user) paths in the same test file (`AdminControllerTest.php`, `CourseControllerTest.php`, `SubmissionControllerTest.php`, `WorkspaceAccessTest.php`).
- **Validation Failures:** Assert field-specific session errors (`$response->assertSessionHasErrors(['grade'])` in `SubmissionControllerTest.php`, `$response->assertSessionHasErrorsIn('userDeletion', 'password')` in `ProfileTest.php`) or `422` JSON payloads (`DeploymentControllerTest.php`).
- **Exception & Security Event Testing** (from `tests/Feature/ExtensionSupplyChainTest.php` and `tests/Feature/SecurityHardeningTest.php`):
```php
// Expect domain exception on checksum tampering
Event::fake([ExtensionIntegrityFailed::class]);
$this->expectException(ExtensionIntegrityException::class);
$manager->installExtension($workspace, 'test-agent.vsix', 'wrong-checksum-hash');

// Verify path traversal & protected file write denials
$this->actingAs($user)
     ->getJson("/api/workspace/ws-{$workspace->id}/read-file?path=../../../../etc/passwd")
     ->assertStatus(404);

$this->actingAs($user)
     ->postJson("/api/workspace/ws-{$workspace->id}/write-file", [
         'path'    => '.env',
         'content' => 'HACKED=true',
     ])
     ->assertStatus(403);
```

---

*Testing analysis: 2026-11-24*

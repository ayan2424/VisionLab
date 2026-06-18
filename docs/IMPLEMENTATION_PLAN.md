# VisionLab Implementation Plan
## Version 9.0 — Production-Grade Enterprise Edition

---

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Implementation Plan |
| Version | 9.0 |
| Prompt Pack | `PROMPTS.xml` v9.0 |
| Standard | PMI PMBOK — Work Breakdown Structure, IEEE 29148 delivery gates |
| Audience | Engineering, DevOps, QA, Security, Product Owner |

---

## Implementation Strategy

VisionLab is delivered in 12 phases. Phases have hard dependency constraints — a phase cannot begin execution until its predecessor phases have passed all acceptance criteria and produced the required evidence package. This prevents integration surprise at late phases and ensures security controls are present before dependent features rely on them.

Each phase follows the same five-stage delivery gate without exception.

---

## Universal Delivery Gate

| Stage | Required Activities | Exit Condition |
|---|---|---|
| READY | Inspect repository state, installed packages, environment variables, current schema, route structure, existing policies, existing tests, and prior phase evidence. Identify any conflicts. | Inspection complete, plan adapted to real state |
| DESIGN | Produce a short written implementation plan identifying: schema changes, API contracts, policy methods, event payloads, job interfaces, UI states, test cases, security denial paths, and audit events. | Plan reviewed, no blocking open questions |
| BUILD | Implement in cohesive full-stack slices (DB → Backend → Frontend). Each slice is complete (all states, authorization, logging, error handling) before the next begins. | All phase requirements implemented |
| VERIFY | Run migrations on a fresh database. Run full test suite. Run frontend build. Perform security denial path checks. Check browser layout. Check accessibility for critical workflows. | All tests pass, build clean, denials confirmed |
| EVIDENCE | Record: changed files list, artisan commands run, test results summary, security denial test results, known risks, operator prerequisites for next phase. | Evidence package attached to phase report |

---

## Engineering Defaults

| Default | Rationale |
|---|---|
| Laravel Policies as authorization center | No inline role checks; all denials are testable |
| FormRequest classes for all validation | Consistent validation structure; reusable across API and web |
| Queued jobs for all external providers | No blocking HTTP in web requests; retry and timeout management |
| Model Observers for analytics events | Removes tracking code from controllers; consistent event capture |
| Feature flags for high-risk capabilities | Safe incremental rollout without deployment gates |
| Redis for sessions, cache, queues, rate limits | Single backing store; horizontal scale path |
| Cursor pagination for large admin lists | Performance-safe on growing datasets |
| realpath() before every file operation | Path traversal prevention without exception |
| SHA256 before every extension installation | Supply-chain integrity without exception |
| Eloquent Strict Mode in development | Surfaces N+1, missing attributes, and non-fillable writes during development |

---

## Risk Burndown Plan

| Risk | Target Phase | Specific Controls |
|---|---|---|
| Container escape or cross-workspace file access | Phase 3 | realpath sandbox, isolated Docker network, security flags, path denial tests |
| Extension supply chain compromise | Phase 4 | SHA256 verification, VisionLab Agent source build pipeline, old-identity scan, registry |
| AI excessive agency bypassing approval | Phase 6 | Mode matrix, patch lifecycle, human approval gate, safety filters, 20-patch limit, negative tests |
| Real-time collaboration instability | Phase 5 | Event contracts, reconnect resilience, stale heartbeat cleanup, channel denial tests |
| Service worker caching sensitive content | Phase 10 | NetworkOnly for /api/* and /workspace/*, browser Application panel verification |
| Deployment leaking secrets | Phase 9 | Package exclusion list, .env block, dependency directory exclusion, security test |
| Production infrastructure failure | Phase 12 | Health endpoint, backup restore rehearsal, CI/CD gate, rollback documentation |
| Legal non-compliance for imported extension code | Phase 4 | License review before import, attribution preservation, VisionLab-controlled fork |

---

## Phase-by-Phase Plan

---

### Phase 1: Foundation, Architecture, Auth, and Design System

**Dependency:** None. This is the baseline.

**READY Checks:**
- PHP 8.3+, Node.js 20 LTS, MySQL 8.0+, Redis 7+, Docker Desktop, Composer 2+, Git confirmed
- Repository state inspected: existing Laravel version, Breeze status, any existing migrations or tests

**DESIGN Tasks:**
- Application structure following Laravel 11 bootstrap/app.php pattern
- Migration plan for all 25 entities (ordered for foreign key compatibility)
- RBAC model: roles, policy classes, middleware aliases, route groups
- Design system tokens: colors, fonts, animation keyframes, custom utilities
- Blade component inventory: x-app-layout, x-admin-layout, x-skeleton-loader, x-empty-state, x-toast-container, x-modal, x-badge, x-data-table, x-confirm-dialog, x-notification-bell

**BUILD Tasks:**
- Install and configure all packages (Breeze, Reverb, Horizon, Pulse, Spatie Permissions, Spatie Activitylog, Intervention Image, Telescope, JWT library)
- Configure bootstrap/app.php: middleware aliases, service bindings, Eloquent Strict Mode
- Create all 25 migrations in dependency order
- Create all Eloquent models with fillable, casts, relationships, scopes, and helper methods
- Create Model Factories with realistic Faker data and state methods
- Create database seeders (1 admin, 3 instructors, 15 students, 3 courses, quotas, extensions)
- Implement role-aware authentication (Breeze + Spatie Permissions extension)
- Implement SuspendedUserMiddleware (global on all authenticated routes)
- Implement RoleMiddleware with analytics logging on denial
- Implement post-login role-based redirect (centralized)
- Register all Policy classes explicitly in AuthServiceProvider
- Define complete route architecture (web.php and api.php with versioning prefix)
- Implement base ApiController with standard JSON envelope
- Build dark design system in tailwind.config.js
- Build all Blade components with full documentation
- Build authentication pages (login, register, password reset, email verification)
- Build authenticated layouts (app.blade.php, admin.blade.php)
- Build public landing page (hero with typewriter, features, showcase, testimonials, stats, footer)

**VERIFY Checks:**
- php artisan migrate:fresh --seed runs without errors
- All three role logins redirect to correct dashboards
- Suspended user login rejected; active session of suspended user ejected on next request
- All policy denial paths return correct HTTP status with redirect
- php artisan test (auth tests, RBAC denial tests)
- npm run build completes without errors
- Lighthouse Performance ≥90, Accessibility ≥85 on landing page

**EVIDENCE Package:**
- 25 table migration log
- Auth test results
- RBAC denial test results
- Lighthouse report (landing page)
- Architecture Decision Log entries (ADR-001, ADR-010)
- Threat model document
- Phase 2 readiness confirmation

---

### Phase 2: Classroom and LMS Domain

**Dependency:** Phase 1 accepted.

**READY Checks:**
- Auth, RBAC, policies, schema, design system all confirmed from Phase 1 evidence
- Horizon and Redis confirmed operational

**DESIGN Tasks:**
- Course management with cover image processing (two variants) and enrollment code generation
- Three enrollment flows: by code, by email invitation, by CSV upload (queued)
- Assignment lifecycle state machine (draft → published → in_progress → submitted → graded)
- Submission snapshot job (WorkspaceSnapshotJob)
- CheckLateSubs scheduled command (every 5 minutes)
- AssignmentDueReminders scheduled command (hourly)
- Notification class inventory (7 classes, channels, queue assignment)
- Gradebook query design (SQL aggregates, color-coded output, CSV export)
- Dashboard caching strategy (Redis, 2-minute TTL, Observer-based invalidation)

**BUILD Tasks:**
- CourseController (index, create, store, show, edit, update, destroy, duplicate)
- StoreCourseRequest, UpdateCourseRequest with MIME verification for cover image
- EnrollmentController (joinByCode, inviteStudent, enrollByCsv, dropStudent)
- CsvEnrollmentJob with per-email result tracking
- StorageCleanupJob for course cover image deletion
- AssignmentController (full lifecycle, publish/unpublish toggle)
- SubmissionController (start, submit, grade, bulkGrade, reopenSubmission)
- WorkspaceSnapshotJob (ZIP archive of project directory)
- CheckLateSubs command (registered in scheduler)
- AssignmentDueReminders command (registered in scheduler)
- 7 notification classes (dispatched to notifications queue)
- x-notification-bell component with unread count, dropdown, mark-all-read
- Gradebook view (SQL aggregate query, color threshold, CSV export via StreamedResponse + signed URL)
- Student Dashboard with Redis-cached stat cards, deadline panel, course grid
- Instructor Dashboard with grading queue, enrollment activity feed
- Admin Dashboard entry point with 6 stat cards
- All course, assignment, submission, and announcement Blade views with all states

**VERIFY Checks:**
- End-to-end workflow: instructor creates course → student joins → assignment created → student starts → submits → instructor grades
- CheckLateSubs marks late submissions correctly
- Notification dispatches arrive in database notifications table
- Gradebook renders with correct color thresholds
- CSV export generates and serves via signed URL
- Dashboard stat cards show live data from database
- Unauthorized access denials confirmed for all enrollment and submission actions
- php artisan test (classroom feature tests)

**EVIDENCE Package:**
- Feature test results (course, enrollment, assignment, submission, grading, unauthorized access)
- Notification channel verification
- Dashboard cache strategy documentation
- Phase 3 readiness confirmation

---

### Phase 3: Workspace Infrastructure and Code-Server IDE

**Dependency:** Phase 2 accepted. Docker Desktop available in development environment.

**DESIGN Tasks:**
- CodeServerManager interface and implementation design
- 5-tier quota resolution priority chain
- Docker security flag inventory (all mandatory flags)
- File API endpoint contracts (6 endpoints, stable JSON shapes)
- Security pipeline diagram (realpath, canonical check, blocked paths)
- IDE shell full-screen immersive view (no external file explorers)
- Web Previews proxying via Simple Browser
- Nix-based environment configuration via `dev.nix` (no `apt`)

**BUILD Tasks:**
- CodeServerManagerInterface (Contracts/) and CodeServerManager service
- Quota resolution (5-tier priority with SELECT FOR UPDATE lock on port allocation)
- startWorkspace with all mandatory Docker security flags
- stopWorkspace, getContainerStats, healthCheck methods
- Docker network verification in AppServiceProvider (visionlab-workspace-net)
- WorkspaceFileController (6 endpoints) with full security pipeline
- File API realpath() security pipeline with analytics_event logging for violations
- WorkspaceController (show — lazy provisioning, start, stop)
- Access token DOM data attribute injection and immediate consumption + clear
- Simple Browser Web Preview endpoints
- xterm.js terminal panel with docker exec session endpoint
- Nix `dev.nix` environment mapping implementation
- Workspace template directories (5 templates) with CodeServerManager copy logic
- WorkspaceStaleHeartbeat scheduled command (every 10 minutes)
- WorkspaceCleanup artisan command (daily scheduler)

**VERIFY Checks:**
- Workspace starts, file tree loads, file CRUD operations work with optimistic UI
- Path traversal test suite: 6 attack patterns all return HTTP 403 with analytics_event record
- Docker inspect confirms all mandatory security flags applied to running container
- Container user (UID 1000) cannot write to root filesystem (docker exec write attempt test)
- Access token absent from page source after iframe initialization
- IDE shell renders all 5 zones at all breakpoints (320px to 2560px)
- Docker unavailable state shows truthful error message
- php artisan test (workspace lifecycle, file API, path traversal denial)

**EVIDENCE Package:**
- Security matrix results (6 attack pattern tests)
- Docker inspect flag verification output
- Workspace lifecycle test results
- Phase 4 readiness confirmation

---

### Phase 4: Extension Ecosystem and Workspace Lockdown

**Dependency:** Phase 3 accepted. Node.js, TypeScript, and vsce available.

**DESIGN Tasks:**
- Extension tier hierarchy (Tier 1 source-built, Tier 2 verified prebuilt, Tier 3 optional)
- VisionLab Agent build pipeline (7 mandatory steps)
- SHA256 integrity verification service design
- Custom Docker image specification (immutable global directory)
- Dual-layer marketplace restriction design
- Per-workspace optional extension installation flow
- SyncWorkspaceExtensions job design

**BUILD Tasks:**
- ChecksumVerificationService (SHA256 compute + compare vs registry, called in startWorkspace)
- extension_builds table migration and ExtensionBuild model
- VisionLab Agent build pipeline execution (full 7-step process documented):
  - Step A: License review and source import to VisionLab-controlled fork
  - Step B: Full source tree audit (report document produced)
  - Step C: Complete source editing (every identified file updated at source level)
  - Step D: Configuration for VisionLab proxy (endpoint, token, telemetry)
  - Step E: Clean compile from VisionLab fork (npm ci + official build script)
  - Step F: Old-identity scan on compiled .vsix (zero matches required before release)
  - Step G: SHA256 registration in extension_builds + code-server smoke test
- Strategy B rebrand_vsix.php script (for verified prebuilt utility tools only)
- Custom visionlab/code-server Docker image (Dockerfile with immutable global directory, chmod 555, HEALTHCHECK)
- build_codeserver_image.sh build script
- CodeServerManager updates: checksum verification before install, Tier 3 optional extension mounting
- Dual-layer marketplace restriction (--disable-marketplace flag + VSCODE_GALLERY_SERVICE_URL)
- allow_ai_agent course flag: VISIONLAB_AI_DISABLED env var injection
- SyncWorkspaceExtensions queued job
- Admin extension management panel (CRUD, global toggle AJAX, workspace config modal)

**VERIFY Checks:**
- SHA256 checksum mismatch test: deliberately tampered .vsix → ExtensionIntegrityException + analytics_event
- VisionLab Agent build report accepted: source audit complete, all files edited, old-identity scan zero matches, clean compile confirmed, smoke test passed
- docker exec write attempt to /usr/local/share/code-server/extensions/ inside running container → permission denied
- Marketplace restriction: workspace in allow_marketplace=false course shows no Extensions panel in code-server UI
- Sync job dispatches and applies extension changes to active containers
- php artisan test (extension policy, checksum verification, marketplace restriction, sync job)

**EVIDENCE Package:**
- VisionLab Agent build report (7-step evidence)
- Old-identity scan result (zero matches confirmed)
- Docker exec permission denial output
- Checksum verification test result (mismatch → exception)
- Legal provenance record (source reference, license review, attribution preservation confirmation)
- Phase 5 readiness confirmation

---

### Phase 5: Real-Time Collaboration

**Dependency:** Phase 4 accepted. VisionLab-collab extension build pipeline established.

**DESIGN Tasks:**
- Reverb channel authorization contracts (presence + private)
- All 9 broadcast event payload schemas
- VisionLab-collab TypeScript module architecture (5 modules)
- Reconnection behavior and state re-sync strategy
- Stale heartbeat cleanup design
- Two-user simulation test plan

**BUILD Tasks:**
- Channel authorization in routes/channels.php (workspace presence, private patches, private notifications, platform announcements)
- All 9 broadcast event classes (ShouldBroadcastNow, defined payloads)
- CollaborationService (color assignment, collab_session management)
- Collaboration API endpoints (chat store, chat history, presence, heartbeat, offline)
- VisionLab-collab TypeScript extension (5 modules: RealtimeManager, DocumentSync, CursorSync, ChatPanel, VideoPanel stub)
- vscode.SecretStorage token storage (read once from process.env, persist, clear reference)
- Extension build pipeline integration (build artifact, register in extension registry)
- IDE shell Blade presence integration (avatar stack, real-time join/leave, toast events)
- Reconnecting banner and state re-sync on reconnect
- Heartbeat setInterval (20s) and beforeunload sendBeacon

**VERIFY Checks:**
- Unauthorized channel subscription: non-collaborator attempt → Reverb authorization denied
- Two-user collaboration simulation: presence visible, cursor decorations show, chat delivers
- Document sync: changes apply without echo loops; sequence numbers correct
- Connection interruption: reconnecting banner shows; re-sync restores presence
- Stale heartbeat cleanup: user marked offline after 30 minutes without heartbeat
- Payload size rejection: CodeUpdated >50KB → HTTP 413
- php artisan test (channel authorization, collaboration API, heartbeat)

**EVIDENCE Package:**
- Two-user simulation test report
- Channel denial test results
- Reconnect behavior verification
- Phase 6 readiness confirmation

---

### Phase 6: AI Agent, Patch Review, and Audit Trail

**Dependency:** Phase 5 accepted. Anthropic API key configured. Phase 4 extension pipeline available for patch reviewer artifact.

**DESIGN Tasks:**
- AI mode matrix (CHAT, PLAN, AGENT) permission table
- Tool permission matrix (read_file, list_directory, search_codebase, propose_patch)
- Safety filter ruleset (PHP, Python, any-file blocked patterns)
- Token budget enforcement design (Redis-cached daily sum per user)
- Patch lifecycle states (pending, approved, rejected, expired, rolled_back)
- VisionLab-patch-reviewer extension architecture (diff viewer, patch queue)
- OpenAI-compatible proxy format mapping
- Execute-plan bridge and PlanExecutionProgress event design

**BUILD Tasks:**
- AiController (chat SSE, approve-patch, reject-patch, rollback, sessions, messages, budget, artifacts)
- AiService (3 mode prompts, 4 tool definitions, streaming proxy, token tracking, cost tracking)
- AiSandbox (realpath validation, content safety filters, readFile, listDirectory, searchCodebase, preparePatch, applyPatch, rollbackPatch)
- Token budget enforcement (Redis-cached, midnight UTC TTL, HTTP 429 with resets_at)
- Cost tracking per session (estimated_cost_usd update after each stream)
- OpenAI-compatible proxy endpoint (/api/v1/ai/v1/chat/completions)
- Container config injection (Continue config.json, workspace-scoped token, proxy URL)
- ExecutePlanJob (20-patch safety limit, PlanExecutionProgress broadcast, structured logging)
- VisionLab.startImplementation command in collab extension
- VisionLab-patch-reviewer TypeScript extension (two-pane diff viewer, queue management, approve/reject/modify buttons)
- Patch reviewer artifact build and registration
- AI memory file read/inject per session, reset UI in IDE shell
- AI artifact detection (vision_artifact XML parsing), ArtifactGenerated event, Artifact Gallery panel

**VERIFY Checks:**
- CHAT mode: no file writes; no mutations in ai_actions_log of type write
- PLAN mode: no mutations; plan terminates with implementation command link
- AGENT mode: propose_patch creates ai_pending_patches record; no direct filesystem write
- Approve: ai_snapshots record created before write; file updated; ai_actions_log entry
- Rollback: file restored from ai_snapshots; patch status updated
- Safety filter: eval() in PHP replace_block → HTTP 422 + analytics_event(ai_safety_violation)
- Forbidden path read: .env → HTTP 403 + analytics_event(path_traversal_attempt)
- Token budget: over-limit request → HTTP 429 with resets_at timestamp
- php artisan test (all AI modes, tool execution, safety filters, patch lifecycle, budget enforcement)

**EVIDENCE Package:**
- AI mode test results (positive and negative)
- Safety filter test results (all blocked patterns)
- Patch lifecycle test results (approve, reject, rollback)
- Token budget enforcement test results
- Phase 7 readiness confirmation

---

### Phase 7: Video Sessions

**Dependency:** Phase 6 accepted.

**BUILD Tasks:**
- JitsiService with JaaS and self-hosted provider abstraction
- JWT generation (server-side, moderator/attendee tiers, 2-hour expiry)
- attendance_logs migration and model
- VideoRoomController (start, join, status, end) with VideoRoomPolicy
- Complete VideoPanel.ts implementation (dark Jitsi embed, status bar, meeting notes generation)
- Workspace video button (Blade IDE shell) with real-time state via Reverb
- Meeting History subtab in course People tab

**VERIFY Checks:**
- JWT generated server-side only; absent from client-side HTML
- Authorized workspace collaborator can join; non-collaborator denied
- Instructor/admin can end call; student cannot end call
- VideoCallStarted event broadcasts to presence channel
- Provider misconfiguration shows clear failure state
- Attendance records created on join, updated on leave
- php artisan test (video authorization, JWT generation, attendance tracking)

---

### Phase 8: Admin Operations and Governance

**Dependency:** Phase 7 accepted. Admin dashboard entry point from Phase 2 confirmed.

**BUILD Tasks:**
- Complete admin shell layout (sidebar, navigation, active route highlight)
- Admin dashboard with live metrics, queue health, AI cost monitoring
- AdminUserController (search, filter, suspend with token invalidation, activate, impersonate, GDPR export)
- GdprDataExportJob (structured JSON, compressed archive, signed URL delivery)
- AdminWorkspaceController (list, show with resource stats, force-stop with Reverb notification)
- ExtensionController admin (CRUD, global toggle AJAX, workspace config, rebuildHash)
- Quota Management panel (workspace_quotas CRUD)
- Audit log viewer (searchable, before/after JSON diff, cursor pagination, CSV export)
- Feature flags management panel (database-backed, Redis-cached with 60s TTL)
- System Configuration panel (system_settings with encrypted sensitive values)
- Maintenance Mode (platform_announcements broadcast, non-admin redirect, admin bypass banner)
- Webhook Management (webhooks table, delivery jobs, delivery history, retry)

**VERIFY Checks:**
- Admin-only routes denied for instructor and student roles
- Suspend action invalidates all Sanctum tokens for the target user
- Force-stop broadcasts Reverb event before container stop
- GDPR export job produces downloadable archive
- Feature flag toggle takes effect within 60 seconds
- All admin actions create audit_logs records
- php artisan test (admin authorization, suspension, force-stop, audit logging)

---

### Phase 9: Analytics, Forensics, Gamification, and Deployment

**Dependency:** Phase 8 accepted.

**BUILD Tasks:**
- Analytics event taxonomy (20+ event types) via Model Observers
- Admin analytics dashboard (7 Chart.js charts with dark theme, date range filtering)
- Instructor analytics (course-scoped views)
- Student analytics (own data only, contribution heatmap placeholder from Phase 1)
- VisionGuard forensics API (ForensicsController sync endpoint, accumulate deltas)
- VisionLab-collab DocumentSync forensics tagging (human vs AI source types)
- VisionGuard display in instructor grading view (donut chart, confidence level)
- 365-day contribution heatmap (CSS Grid, 4 intensity levels from analytics_events)
- Daily:update-streaks scheduled command (midnight UTC)
- GamificationService with 10 badge definitions and evaluateUser method
- BadgeEarned Reverb event → toast notification
- Student deployment flow (DeploymentController, public-exposure confirmation, DeployWorkspaceJob)
- DeploymentProvider interface (Vercel REST implementation + Railway GraphQL implementation)
- Deployment package exclusion list (config/deployment.php)
- Real-time deployment status via Reverb (DeploymentCompleted event on private channel)
- Student Dashboard deployment history panel

**VERIFY Checks:**
- Analytics events recorded for login, workspace start, AI query, submission
- Analytics role-restriction: student cannot access instructor or admin analytics endpoints
- VisionGuard forensics sync accumulates deltas correctly
- Badge awarded only once per user per badge_type (duplicate prevention)
- Deployment package excludes .env, .git, vendor, node_modules (verified by ZIP inspection)
- Provider failure stores error_summary and triggers failed status notification
- php artisan test (analytics, forensics, badge award, deployment lifecycle)

---

### Phase 10: PWA and Push Notifications

**Dependency:** Phase 9 accepted. Application deployed over HTTPS (required for service workers and push).

**BUILD Tasks:**
- Web app manifest (complete with app shortcuts, screenshots, icon array)
- Icon generation script (all required PNG sizes from SVG source)
- Workbox 7 service worker (5 route strategies, cache versioning, offline fallback)
- Offline fallback page (self-contained, no server render dependency, inline styles)
- PWA JavaScript module (service worker registration, install prompt, update banner, online/offline banners)
- VAPID push subscription API (subscribe/unsubscribe endpoints)
- 3 push notification classes (AssignmentDuePushNotification, AnnouncementPushNotification, SubmissionGradedPushNotification)
- AssignmentDuePushNotification scheduled command (hourly)
- AnnouncementController dispatch update (queue push on post)
- Background Sync for offline submissions (IndexedDB queue, sync event replay)

**VERIFY Checks:**
- Browser Application panel: manifest valid, service worker registered and activated
- /api/* routes confirmed NetworkOnly (browser DevTools Network panel, offline simulation)
- /workspace/* routes confirmed NetworkOnly
- Offline fallback page appears for uncached navigation without pretending IDE functionality
- Push subscription stored in push_subscriptions table
- Assignment due reminder dispatches for correct students
- Lighthouse PWA audit ≥90
- php artisan test (push subscription, notification classes, service worker registration)

---

### Phase 11: Security Hardening, Testing, and Performance

**Dependency:** Phase 10 accepted.

**BUILD Tasks:**
- OWASP ASVS Level 2 mapping document (all categories with controls and test evidence)
- SecurityHeaders Blade middleware (CSP nonce, all security headers, global application)
- Security verification script (scripts/security-check.sh, 7 automated checks)
- Complete PHPUnit feature test suite (8 test classes covering all acceptance criteria)
- Accessibility audit and fixes (WCAG 2.1 AA: contrast, focus, labels, ARIA, keyboard)
- Redis caching audit and optimization (Model Observer cache-tag invalidation)
- N+1 query audit (Eloquent Strict Mode violations resolved)
- Database index optimization (EXPLAIN-verified for critical queries)
- Performance benchmarks (TTFB targets per FRD NFR definitions)
- OpenAPI 3.1 specification (all API endpoints, served via Swagger UI at /api/docs for admins)
- Custom error pages (403, 404, 419, 429, 500, 503 — dark design system, safe messages)

**VERIFY Checks:**
- Security verification script: all 7 checks pass
- PHPUnit: all test classes pass with zero failures on fresh migrate + seed
- ASVS Level 2 matrix: all categories have mapped controls and at least one negative test
- WCAG 2.1 AA accessibility checks pass for all critical workflows
- No N+1 queries detected on critical pages in Strict Mode
- All response time benchmarks met (per NFR table in FRD)
- php artisan test --coverage (target: 80% coverage on domain and service layers)

---

### Phase 12: Production Deployment (GCP e2-standard-8) and Observability
 
 **Dependency:** Phase 11 accepted. Production VPS (GCP or equivalent), domain, and DNS available.

**BUILD Tasks:**
- docker-compose.prod.yml (8 services with security-hardened configurations)
- Multi-stage production Dockerfile (builder stage + production PHP-FPM stage)
- Custom visionlab/code-server image (from Phase 4, push to ghcr.io)
- Nginx production configuration (TLS 1.3, WebSocket proxy, SSE no-buffering, security headers, rate limiting, certbot)
- GitHub Actions CI/CD workflow (3 stages: Test → Build → Deploy with OIDC auth)
- Health endpoint (GET /api/health — 5 dependency probes)
- Structured JSON logging configuration (stderr channel for Docker log aggregation)
- UptimeRobot monitoring setup documentation
- RUNBOOK.md (deploy, rollback, backup, restore, workspace cleanup, incident response, evaluation reset)
- Backup and restore scripts (MySQL daily dump, workspace storage, retention policy)
- Preflight checklist for evaluation environments

**VERIFY Checks:**
- docker-compose.prod.yml: all services start, health checks pass
- Nginx TLS: SSL Labs A+ rating confirmed
- All security headers present in HTTP response (security-check.sh)
- GitHub Actions: CI pipeline runs all 3 stages on push to main
- Health endpoint: HTTP 200 with all-ok for configured environment
- Backup restore rehearsal: MySQL dump restored successfully on a test instance
- RUNBOOK.md reviewed: all procedures are actionable without author guidance
- Preflight checklist: all items pass before evaluation access is granted

**EVIDENCE Package — Release Manifest:**
- Git commit SHA and repository state
- Migration status (artisan migrate:status)
- Full test suite results (CI artifacts)
- Security verification script output (all 7 checks passing)
- Extension artifact checksums (all registered extensions)
- Health endpoint JSON output (all dependencies ok)
- Backup confirmation (most recent successful backup timestamp)
- Known risks and contingency plan
- Rollback procedure (version to revert to, commands to execute, data implications)
- Evaluation account credentials (clearly labeled as non-production)
- Preflight checklist sign-off

---

## Dependency Sequence Summary

```
Phase 1 ──▶ Phase 2 ──▶ Phase 3 ──▶ Phase 4 ──▶ Phase 5
                                                    │
                                          ┌─────────┤
                                          ▼         ▼
                                       Phase 6 ──▶ Phase 7
                                          │
                                          ▼
                                       Phase 8 ──▶ Phase 9
                                                    │
                                                    ▼
                                                 Phase 10
                                                    │
                                                    ▼
                                                 Phase 11
                                                    │
                                                    ▼
                                                 Phase 12
```

Phase 4 and Phase 6 are tightly coupled through the VisionLab Agent and patch-reviewer extension artifacts. Phase 4 must produce the approved extension build report before Phase 6 can rely on the extension delivery infrastructure.

---

## Operational Handoff Package

| Artifact | Required Content |
|---|---|
| Environment Manifest | Service names, versions, required variables, feature flag states, provider modes, Docker image tags |
| Release Manifest | Commit SHA, migration status, build artifacts, extension checksums, CI result |
| Security Matrix | ASVS/LLM/container controls, negative test evidence, residual risks |
| Runbooks | Deploy, rollback, backup, restore, workspace cleanup, incident response, evaluation reset |
| Monitoring Notes | Health check URLs, log locations, queue/scheduler checks, alert conditions |
| Legal/Provenance Note | Imported extension source reference, license review result, attribution preservation confirmation, artifact history |
| Evaluation Guide | Account credentials (non-production), reset process, workflow sequence, known risks, contingency steps |

---

## Definition of Complete

The implementation is complete when:

1. All Must-priority requirements in the FRD are implemented and traced in the RTM to passing tests
2. All 12 phases have produced their required evidence packages
3. The evaluation workflow (admin governance → instructor teaching → student coding → AI patch → submission → deployment) runs end-to-end on live application screens without code changes
4. The security verification script passes all 7 automated checks
5. The VisionLab Agent build report is accepted (source audit, complete source edit, zero old-identity matches, clean compile, smoke test)
6. The production health endpoint returns HTTP 200 for all 5 dependencies
7. Backup restore has been rehearsed and confirmed
8. All legal provenance requirements for imported open-source extension code are documented and preserved

# VisionLab Functional Requirements Document
## Version 9.0 — Production-Grade Complete Specification

---

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Functional Requirements Document (FRD) |
| Version | 9.0 |
| Prompt Pack | `PROMPTS.xml` v9.0 |
| Standard | IEEE 29148:2018 |
| Priority Convention | Must (P0 — blocking), Should (P1 — high value), Could (P2 — enhancement) |
| Verification Convention | Unit, Feature, Browser, Security, Manual, Operational, Accessibility |

---

## Cross-Cutting Requirements (Apply to ALL Phases)

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-XCUT-001 | Every protected route, API endpoint, WebSocket channel, and job-triggering action shall enforce authorization through Laravel Policies, gates, or equivalent service-level checks. No controller method may execute its core logic without a preceding policy check. | Must | Security |
| FR-XCUT-002 | Every state-changing request shall validate input through dedicated FormRequest classes. Inline controller validation is not permitted. Validation error responses must be structured and consistent across all endpoints. | Must | Feature / Security |
| FR-XCUT-003 | All user-facing operations shall expose the following states where applicable: success, loading (with skeleton loader), empty (with x-empty-state), unauthorized (with role-appropriate redirect), unavailable (when external dependency is down), validation error (field-level with icon), and server error (with safe message, no stack trace). | Must | Browser |
| FR-XCUT-004 | Destructive or irreversible operations shall require explicit user confirmation via the x-confirm-dialog component and shall create an immutable audit_logs record with actor, resource, action, previous state, new state, IP address, and timestamp. | Must | Feature / Security |
| FR-XCUT-005 | Long-running external operations (AI provider calls, video provider calls, deployment provider calls, extension sync) shall execute through queued Laravel jobs with defined retry limits, backoff strategy, idempotency keys where needed, timeout handling, structured logging, and user-visible status tracking. | Must | Feature / Operational |
| FR-XCUT-006 | Sensitive workflows that span multiple requests, jobs, and events shall use correlation identifiers traceable through audit logs, job logs, and analytics_events records. | Should | Operational |
| FR-XCUT-007 | All lists that can grow over time shall support pagination (cursor-based for large admin lists), role-appropriate search or filtering, and empty states. Growing lists must never load unbounded result sets. | Must | Browser / Performance |
| FR-XCUT-008 | All rendered user-generated content shall be escaped using Blade double-curly-brace syntax. The {!! !!} unescaped directive is permitted only for content that has passed through HTMLPurifier on the write path. No user-generated content may render as raw unescaped HTML under any circumstances. | Must | Security |
| FR-XCUT-009 | Feature flags stored in the feature_flags database table shall gate high-risk capabilities: AI agent mode (allow_ai_agent per course), marketplace access (allow_marketplace per course), video sessions (allow_video per course), student deployment (allow_deployment — admin-controlled), push notifications, and VisionGuard forensics. Default flag states must be safe (disabled) before administrator explicit enablement. | Must | Feature / Operational |
| FR-XCUT-010 | All critical user workflows shall be accessible through: semantic HTML landmarks (main, nav, header, footer), explicitly associated form labels (not placeholder-only), keyboard navigation with visible focus rings (2px solid brand-violet, outline-offset: 2px), accessible color contrast (4.5:1 for normal text, 3:1 for large text per WCAG 2.1 AA), and responsive layouts from 320px to 2560px. | Must | Accessibility |
| FR-XCUT-011 | All API endpoints shall return responses in the standard VisionLab JSON envelope: {data: {...}, meta: {version, timestamp, correlation_id}, status: N}. Error responses: {error: {code, message, details}, meta: {...}, status: 4xx/5xx}. | Must | Feature |
| FR-XCUT-012 | All analytics-generating actions shall dispatch AnalyticsEvent records automatically via Eloquent Model Observers without requiring explicit tracking code in controllers. | Must | Feature |

---

## Authentication & Role-Based Access Control

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-AUTH-001 | The system shall support three roles: `admin`, `instructor`, and `student`. Roles are stored using Spatie Laravel Permissions. The admin role is never self-assignable during registration. | Must | Feature |
| FR-AUTH-002 | Post-login redirects shall be role-aware and centralized: admin → /admin/dashboard, instructor → /instructor/dashboard, student → /dashboard. The redirect logic shall live in a single handler, not duplicated across controllers. | Must | Feature |
| FR-AUTH-003 | The SuspendedUserMiddleware shall be applied globally to all authenticated routes. On every authenticated request, the middleware checks user.status. If suspended: force logout, invalidate session, regenerate CSRF token, redirect to login with a flash error. Suspension takes effect on the next HTTP request, not only at login. | Must | Feature / Security |
| FR-AUTH-004 | All protected domain actions shall be authorized via named Policy classes registered in AuthServiceProvider. No inline role checks (e.g., if($user->role === 'admin')) are permitted in controllers or views. | Must | Security |
| FR-AUTH-005 | The system shall prevent the removal or suspension of the final active administrator account. The AdminUserController::suspend and AdminUserController::update actions shall validate that at least one other active admin exists before proceeding. | Must | Feature / Security |
| FR-AUTH-006 | Sanctum API tokens issued for workspace container use shall carry specific ability strings from an Abilities enum class (e.g., WORKSPACE_READ, WORKSPACE_WRITE, AI_QUERY, COLLAB_SYNC, VIDEO_JOIN). Wildcard tokens (* ability) are forbidden. | Must | Security |
| FR-AUTH-007 | Login attempts shall be rate-limited to 10 per minute per IP address using a named Redis-stored rate limiter. Registration attempts shall be rate-limited to 5 per minute per IP. Both limits must return HTTP 429 with a Retry-After header on breach. | Must | Security |
| FR-AUTH-008 | The system shall provide professional dark-themed authentication pages (login, register, password reset, email verification) consistent with the VisionLab design system, with inline field-level validation errors displayed with an icon. | Must | Browser |

---

## Classroom & LMS Domain

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-CLS-001 | Instructors shall create courses with title, slug (auto-generated, unique), description, cover image (MIME-verified, JPEG/PNG/WebP, max 2MB, processed to 1280×720 and 400×225 variants), enrollment code (cryptographically random, 6 chars, unique), max_students limit, and active status flag. | Must | Feature |
| FR-CLS-002 | Students shall join active courses by valid enrollment code. The system shall validate: code exists, course is active, student is not already enrolled (reinstating dropped enrollment rather than duplicating), max_students limit not exceeded. Enrollment attempt rate limit: 10 per hour per user. | Must | Feature / Security |
| FR-CLS-003 | Duplicate active enrollment shall be prevented. Attempting to join a course where an active enrollment already exists shall return a validation error, not a duplicate record. | Must | Feature |
| FR-CLS-004 | Instructors shall invite students by email (creating invited-status enrollment and dispatching CourseInvitationNotification), bulk-enroll students via CSV upload (processed in a queued CsvEnrollmentJob with per-email result tracking), and remove students (setting enrollment status to dropped, preserving submission history). | Should | Feature |
| FR-CLS-005 | Course detail pages shall implement a three-tab layout (Stream, Assignments, People) with URL fragment deep-linking via history.pushState. Tab content shall load via AJAX on first activation and be cached in a JavaScript Map. Instructors see all three tabs; students see Stream and Assignments only. | Must | Browser |
| FR-CLS-006 | Instructors shall create assignments with: title, description, Markdown instructions (rendered server-side via CommonMark + HTMLPurifier), max points (1–1000), due date (datetime-local), workspace template selection, starter code, auto-workspace flag, late submission policy, and draft/published state. Unpublished assignments are invisible to students. | Must | Feature |
| FR-CLS-007 | Students shall start assignments by clicking "Open Assignment." If a submission record with a linked workspace already exists, the system shall redirect directly to that workspace. Otherwise, the system shall create a Submission record (status: in_progress) and a Workspace record, then redirect to the workspace IDE. Container provisioning is deferred to WorkspaceController::show (lazy provisioning). | Must | Feature |
| FR-CLS-008 | Students shall submit assignment snapshots from their workspace. The submit action shall: detect late status by comparing Carbon::now() against due_date, update submission status to submitted or late, set submitted_at, queue a WorkspaceSnapshotJob to create a compressed ZIP archive of the project directory, store the archive path, and dispatch SubmissionReceivedNotification and SubmissionAvailableForGradingNotification. | Must | Feature |
| FR-CLS-009 | The system shall calculate late submission state automatically. A CheckLateSubs scheduled command runs every 5 minutes and marks in_progress submissions as late where the assignment due_date has passed and is_late is still false. | Must | Feature |
| FR-CLS-010 | Instructors shall grade submissions by entering a numeric grade (validated against max_points), Markdown feedback, and saving. The grade action shall update graded_by, graded_at, and status to graded, then dispatch SubmissionGradedNotification and log to audit_logs with before/after grade values. | Must | Feature |
| FR-CLS-011 | The system shall support bulk grading: instructors submit a JSON payload of {submission_id: grade} pairs processed in a database transaction with individual notifications dispatched via Bus::batch(). | Should | Feature |
| FR-CLS-012 | The system shall provide a gradebook view for instructors: a pivot table with students as rows, assignments as columns, grade cells color-coded by threshold (green ≥60%, amber 50–59%, red <50%), and average row/column computed via SQL aggregates (not PHP loops). A CSV export generates via StreamedResponse with a 10-minute signed URL. | Should | Feature |
| FR-CLS-013 | Instructors shall post announcements with title and Markdown body (stored raw in body_raw, sanitized HTML in body_html via CommonMark + HTMLPurifier). Announcements support pinned state. The announcement stream shows pinned items first, then by creation date descending. | Must | Feature |
| FR-CLS-014 | Announcement visibility shall be restricted to enrolled students and course staff. The AnnouncementPolicy shall deny access to users not enrolled in or owning the course. | Must | Security |
| FR-CLS-015 | All classroom lists (course list, assignment list, submission list, announcement stream, people list) shall be paginated with role-appropriate search and filter controls. Empty states shall use the x-empty-state Blade component. | Must | Browser |
| FR-CLS-016 | Student Dashboard shall display: enrolled course count, assignments due this week (color-coded by urgency: red <24h, amber 24–48h, yellow 48h–7d), pending submission count, completed assignment count — all from live database queries cached in Redis with 2-minute TTL. | Must | Feature |
| FR-CLS-017 | Instructor Dashboard shall display: active courses count, total enrolled students, pending grading count (with direct link to grading queue), AI sessions this week — all from live cached queries with 2-minute TTL. | Must | Feature |

---

## Workspace IDE Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-WSP-001 | The system shall create workspace records linked to owner, optional course, and optional assignment. Each workspace tracks container_id, container_name, code_server_port, code_server_url, access_token (64-char cryptographically random), is_active, workspace_path, last_heartbeat, and applied quota values. | Must | Feature |
| FR-WSP-002 | CodeServerManager shall implement idempotent startWorkspace: if a healthy running container exists, reuse it; if metadata references a missing or unhealthy container (including SIGABRT/OOM crashes), restart according to policy and log the decision to audit_logs. The service never silently creates duplicate containers. | Must | Feature |
| FR-WSP-003 | Workspace resource quotas shall be resolved in this exact priority order: (1) course-specific quota, (2) user-specific quota, (3) role-based default, (4) hard-coded platform fallback (512m RAM, 0.5 CPU, 100 PIDs). The effective quota values shall be stored in workspace.applied_memory_limit, applied_cpu_limit, applied_pids_limit. | Must | Feature |
| FR-WSP-004 | All Docker run commands shall include the mandatory security flags: --security-opt no-new-privileges:true, --cap-drop ALL, --read-only, --tmpfs /tmp:rw,noexec,nosuid,size=100m, --init, --user 1000:1000, --network visionlab-workspace-net, --memory, --memory-swap, --cpus, --pids-limit. None of these flags may be omitted. | Must | Security |
| FR-WSP-005 | Workspace startup shall inject these environment variables into every container: VISIONLAB_API_TOKEN (64-char Sanctum token), VISIONLAB_WORKSPACE_ID, VISIONLAB_REVERB_URL, VISIONLAB_API_BASE, CS_DISABLE_TELEMETRY=1. The access_token is placed in a DOM data attribute, immediately consumed by JavaScript to set the iframe src, and the data attribute is cleared to prevent source inspection. | Must | Security |
| FR-WSP-006 | File APIs shall support: GET /files (recursive tree, excludes .git, node_modules, vendor, __pycache__), GET /files/read (content, max 5MB), POST /files/write, POST /files/create, POST /files/delete (with confirm=true for recursive directory), POST /files/rename (new name only, never full path), GET /files/search (query string, max 50 results). All return stable JSON shapes. | Must | Feature |
| FR-WSP-007 | Every file API request shall follow this exact security pipeline before any filesystem operation: (1) authorize via WorkspacePolicy::access_files, (2) URL-decode the relative path, (3) prepend the canonical workspace project path, (4) call realpath(), (5) if realpath() returns false or the result does not start with the workspace's canonical project path, return HTTP 403 and log event_type path_traversal_attempt to analytics_events. | Must | Security |
| FR-WSP-008 | File APIs shall block reads and writes to: .env files, .git directories, vendor, node_modules, __pycache__, any path containing null bytes, and any path that resolves outside the canonical workspace root. Blocked access attempts are logged to analytics_events and never reveal the canonical path in the error response. | Must | Security |
| FR-WSP-009 | The IDE shell shall be an immersive full-screen experience containing only: (1) Top Bar 40px with workspace name, collaborator avatars, video button, deploy button (feature-flag guarded), settings, and back-to-dashboard links; (2) The core IDE iframe flex-1. No external JavaScript file explorers are permitted. Preloader removal shall be synchronized dynamically via `/healthz` polling. | Must | Browser |
| FR-WSP-010 | The workspace UI shall clearly distinguish these container states without pretending the IDE is functional when it is not: starting, running, unhealthy, stopped, failed, unauthorized, and offline. Each state must show an appropriate UI message with actionable guidance. | Must | Manual |
| FR-WSP-011 | The JavaScript file explorer module shall be self-contained, dependency-free ES2022, and support: recursive tree rendering with language-specific SVG icons, optimistic UI for all CRUD operations (immediate update, rollback on API error with toast), right-click context menu with scaleIn animation, keyboard shortcuts (Ctrl+B toggle, F2 rename, Delete remove, Ctrl+Shift+F search), and a code search panel showing results grouped by file. | Must | Browser |
| FR-WSP-012 | A WorkspaceStaleHeartbeat scheduled command shall run every 10 minutes and stop containers where is_active=true but last_heartbeat is more than 30 minutes old. A WorkspaceCleanup command shall run daily and archive project directories for assignments past the configured grace period. | Must | Operational |
| FR-WSP-014 | Container environments are strictly Nix-based. Package resolution occurs solely through `dev.nix`. All `apt` and `apt-get` commands are explicitly forbidden and non-viable. | Must | Operational |
| FR-WSP-015 | The IDE shall natively support Web Previews via Simple Browser proxying, allowing students to view dynamically rendered full-stack web applications seamlessly within the interface. | Must | Feature |

---

## Extension Governance Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-EXT-001 | The extension registry shall store for each extension: package_identifier (unique), display_name, version, category, source_type (source_built or verified_prebuilt), artifact_path, artifact_checksum (SHA256 hex), is_builtin, is_global, is_active, install_priority, and license_reviewed flag. | Must | Feature |
| FR-EXT-002 | ChecksumVerificationService shall compute the SHA256 hash of the .vsix file on disk before every container installation and compare it against artifact_checksum. Hash mismatch shall: abort container start, create an analytics_events record with event_type extension_integrity_failure, and throw ExtensionIntegrityException. The check runs on every start, not only at first registration. | Must | Security |
| FR-EXT-003 | Required extensions (Tier 1: VisionLab Collab, Patch Reviewer, Agent; Tier 2: approved utility tools) shall be installed into the global code-server extensions directory (/usr/local/share/code-server/extensions/) during Docker image build with root:root ownership and chmod 555 permissions. The runtime user (UID 1000) shall have no write access to this directory. | Must | Security / Operational |
| FR-EXT-004 | Students shall not be able to uninstall, disable, or modify required extensions. Verification: a docker exec write attempt to the global extensions directory inside a running container shall fail with a permission error. This test is part of the Phase 4 acceptance suite. | Must | Security |
| FR-EXT-005 | Administrators and instructors shall configure extension availability at global, course, and workspace levels through the admin panel. Policy resolution order: global admin rules → course instructor rules → workspace overrides → user preference (optional tools only). | Must | Feature |
| FR-EXT-006 | Marketplace access shall be controlled by two simultaneous mechanisms when disabled per course: (1) the --disable-marketplace Docker run flag removes the Extensions panel from code-server UI, (2) VSCODE_GALLERY_SERVICE_URL is set to an invalid URL. Both controls must be applied simultaneously for defense-in-depth. | Must | Feature / Security |
| FR-EXT-007 | Active workspace policy changes shall propagate through a queued SyncWorkspaceExtensions job. The job uses docker exec to install or uninstall optional extensions without requiring a container restart. Changes requiring restart (marketplace flag, AI flag) shall queue WorkspaceRestart jobs. All sync operations are logged to audit_logs. | Should | Feature |
| FR-EXT-008 | The VisionLab Agent shall be built from the official Continue source tree under a VisionLab-controlled fork following mandatory steps: (A) one-time legally reviewed source import, (B) full source tree audit documenting all files containing upstream identity strings, (C) complete source-level editing of every identified file (package metadata, UI strings, webview titles, command labels, status bar text, icon assets, endpoint defaults), (D) old-identity scan showing zero upstream name matches in the compiled archive, (E) clean compile from the VisionLab fork, (F) SHA256 registration in extension_builds, (G) code-server smoke test confirming activation, commands, webviews, and proxy configuration. Steps C and D are individually blocking — the release is not permitted if any remain incomplete. | Must | Operational / Security |
| FR-EXT-009 | The VisionLab Agent release shall preserve all legally required license notices, copyright statements, and attribution files from the imported upstream source. Product-facing branding shall be VisionLab. Legal provenance records in extension_builds shall remain accurate. No falsification of authorship or removal of required upstream attributions is permitted. | Must | Legal |
| FR-EXT-010 | All production installs of the VisionLab Agent shall use artifacts built from the VisionLab-controlled fork. There shall be no production dependency on the upstream Continue registry, distribution channel, or release cycle after the initial compliant import. | Must | Operational |
| FR-EXT-011 | Strategy B (verified prebuilt artifact, compiled-level rebranding limited to package.json metadata fields) shall only be applied to utility extensions that: (a) do not handle workspace tokens, (b) do not execute privileged workflows, (c) have a documented license review. Strategy B is explicitly forbidden for the VisionLab Agent, VisionLab Collab, Patch Reviewer, or any security-sensitive extension. | Must | Security |

---

## Real-Time Collaboration Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-COL-001 | Workspace presence channels shall be authorized by querying workspaces.owner_id and workspace_collaborators. Channels shall be rejected if workspace.is_active=false. The presence payload shall include id, name, avatar_url, role, and assigned_color. | Must | Security |
| FR-COL-002 | The system shall broadcast the following events with defined payloads: CodeUpdated (workspace_id, file_path, changes array, sequence_number, user_id), CursorMoved (via Reverb whisper — ephemeral, no server storage), ChatMessageSent, UserJoinedWorkspace, UserLeftWorkspace, VideoCallStarted, VideoCallEnded, PatchProposed (private patches channel), and PlanExecutionProgress. | Must | Feature |
| FR-COL-003 | The VisionLab-collab TypeScript extension shall handle reconnection with exponential backoff (1s → 2s → 4s → 8s → 30s max) and trigger a full state re-sync on reconnect. The Blade IDE shell shall show a persistent amber reconnecting banner and dismiss it with a success toast on reconnect. | Must | Browser |
| FR-COL-004 | CursorSync shall use Reverb's whisper API (client-to-client, no server processing or database storage) for high-frequency cursor position broadcasts. TextEditorDecorationType decorations shall include a 2px colored underline and an "after" pseudo-element with the user's name. Decorations shall expire after 3 seconds without a new event from that user. | Should | Browser |
| FR-COL-005 | DocumentSync shall prevent echo loops by comparing the event's user_id against the local user's ID from process.env. Sequence numbers shall be maintained per file in a Map. Out-of-order events shall be buffered for 200ms. Conflicts that cannot be resolved shall be surfaced as explicit error messages rather than silently overwriting. | Must | Browser |
| FR-COL-006 | Chat messages shall be sanitized with a strict CSP on the chat panel webview before rendering. The webview's Content-Security-Policy meta tag shall disallow inline scripts and restrict resource origins to the VisionLab domain only. Raw user-authored HTML must not execute in the chat panel. | Must | Security |
| FR-COL-007 | Collaboration API endpoints shall apply payload size limits: CodeUpdated events exceeding 50KB and chat messages exceeding 2KB shall be rejected with HTTP 413. Rate limit: 100 collaboration API requests per minute per user. | Must | Security |
| FR-COL-008 | A CollaborationService shall assign stable colors from an 8-color palette using (user_id mod 8). The same user always receives the same color in any workspace. The assigned color is stored in collab_sessions.assigned_color. | Should | Feature |

---

## AI Agent Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-AI-001 | The AI service shall support three modes with a strict permission matrix: CHAT (read authorized files only, no mutations), PLAN (read and produce structured implementation plans only, no mutations), AGENT (read files and propose patches only — never apply directly). | Must | Feature / Security |
| FR-AI-002 | CHAT mode system prompt shall explicitly instruct the model that it cannot modify files. PLAN mode shall always terminate the plan with the exactly formatted string "[▶ Start Implementation](command:VisionLab.startImplementation)". AGENT mode shall instruct: propose one patch at a time and wait for the tool result before proceeding. | Must | Feature |
| FR-AI-003 | All AI provider calls shall be routed through the Laravel backend. Provider API keys shall never be present in browser JavaScript, extension code, or any client-accessible response. The system prompt shall be constructed server-side and shall never include secrets, environment files, or administrator-only records. | Must | Security |
| FR-AI-004 | Agent mode shall only create ai_pending_patches records (status: pending) with a 30-minute expiry. The propose_patch tool shall: validate path via realpath(), run safety filters on replace_block, create an ai_snapshots record, generate a unified diff, create an ai_pending_patches record, and broadcast PatchProposed on the private patches channel. It shall never write to the filesystem directly. | Must | Security |
| FR-AI-005 | All AI file mutations shall require human approval via the patch reviewer before application. The approve-patch endpoint shall: verify ownership via policy, call AiSandbox::applyPatch, write the file, update the patch status to applied, and log to ai_actions_log. No exception exists except .visionlab_memory.md patches which are auto-approved as the single documented exception. | Must | Security |
| FR-AI-006 | The system shall create an ai_snapshots record (with SHA256 content hash) before every approved patch application. A rollback endpoint shall restore the file from the snapshot and update the patch status to rolled_back. | Must | Feature |
| FR-AI-007 | AiSandbox safety filters shall block these patterns in any proposed patch replace_block before creating the patch record: PHP — eval(), exec(), system(), passthru(), shell_exec(), popen(), proc_open(); Python — os.system(), subprocess.Popen(), exec(), eval(), pickle.loads(); Any file — null bytes, EICAR test string, common reverse shell payload patterns. Detection returns HTTP 422 and logs event_type ai_safety_violation to analytics_events. | Must | Security |
| FR-AI-008 | The system shall expose a POST /api/v1/ai/v1/chat/completions OpenAI-compatible proxy endpoint for the VisionLab Agent extension. The endpoint reads X-Workspace-Id and X-Visionlab-Mode headers, enforces workspace authorization, translates the OpenAI format to the internal AiService format, and returns OpenAI-format SSE. Rate limit: 100 per minute per user. | Must | Feature |
| FR-AI-009 | Workspace-specific Continue configuration shall be injected into each container at startup (without any model provider API keys) setting: proxy base URL, workspace-scoped token, slash commands (/ask, /plan, /agent), disabled upstream telemetry and cloud features. | Must | Security |
| FR-AI-010 | The execute-plan endpoint shall dispatch ExecutePlanJob to the ai-processing queue. The job shall enforce a safety limit of 20 patch proposals per execution. PlanExecutionProgress events shall be broadcast after each step. The job handles provider outages gracefully and logs the completed or failed result. | Must | Security / Feature |
| FR-AI-011 | AI tools shall block access to: .env files, .git directories, vendor, node_modules, storage outside the workspace root, and platform configuration files. All blocked tool call attempts shall be logged to analytics_events. Repository files, assignment instructions, and user chat input shall be treated as untrusted data — never as policy authority. | Must | Security |
| FR-AI-012 | AI-generated artifacts detected in the response stream (vision_artifact XML tags) shall be stored in ai_artifacts, broadcast via ArtifactGenerated event, and rendered in the Artifacts webview panel with Preview (sandboxed iframe, no allow-same-origin), Copy, and Apply to Workspace actions. | Should | Security / Browser |
| FR-AI-013 | The AI memory file (.visionlab_memory.md) shall be an explicit, visible workspace file governed by the same sandbox path rules. Its content shall be read before every session and prepended to the system prompt. It is the only file that may be auto-approved for AI writes without human confirmation. | Should | Security |
| FR-AI-014 | Per-role daily token budgets shall be enforced: student (50,000 tokens/24h), instructor (200,000 tokens/24h), admin (unlimited). Budget consumption is tracked in ai_chat_sessions and cached in Redis with midnight UTC expiry. Over-budget requests return HTTP 429 with {code: BUDGET_EXCEEDED, resets_at: timestamp}. | Must | Feature |
| FR-AI-015 | AI session cost shall be tracked per session in ai_chat_sessions.estimated_cost_usd using pricing constants from config/ai.php. Cost data feeds the admin analytics AI Cost Per Day chart. | Must | Feature |

---

## Video Session Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-VID-001 | The system shall provide a JitsiService that abstracts two provider modes: JaaS (8x8.vc) and self-hosted Docker Compose Jitsi. Provider selection is environment-variable driven. The service interface is identical for both modes. | Should | Feature |
| FR-VID-002 | Only workspace collaborators and owners shall retrieve video join details. The VideoRoomPolicy::join_video method returns false if the requesting user is not an active workspace collaborator, or if the course's allow_video feature flag is false. | Must | Security |
| FR-VID-003 | Instructors and workspace owners shall end managed sessions. The VideoRoomPolicy::end_video method restricts end_video action to instructor role or workspace owner. The end action broadcasts VideoCallEnded to all workspace presence channel members. | Must | Feature |
| FR-VID-004 | JWTs for Jitsi shall be generated server-side with: iss (APP_ID), sub (Jitsi domain), aud ('jitsi'), room name, user context (id, name, email, avatar), moderator flag (true for instructors), exp (2-hour expiry). The JWT is never exposed in client-side HTML or JavaScript variables. | Must | Security |
| FR-VID-005 | Video provider misconfiguration or unavailability shall produce a clear failure state in the UI — never pretend a call is active when it is not. The workspace video button shall show an error tooltip explaining the issue. | Must | Manual |
| FR-VID-006 | The attendance_logs table shall record joined_at and left_at for every video session participant. Instructors shall view attendance records for their course video calls from a Meeting History subtab in the course People tab. | Should | Feature |

---

## Admin Operations Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-ADM-001 | The admin dashboard shall display live database-backed metrics: total users by role, active workspaces, AI cost today ($USD), failed queue jobs (linked to Horizon), active video calls, total courses. All metrics are Redis-cached with 30-second TTL. | Must | Feature |
| FR-ADM-002 | Administrators shall manage users: search by name/email, filter by role and status, edit profile and role, suspend (with token invalidation via $user->tokens()->delete() and WorkspaceSuspendedUser job), activate, and export user data as GDPR-compliant JSON archive (GdprDataExportJob). | Must | Feature |
| FR-ADM-003 | All admin actions (suspend, activate, role change, workspace stop, extension policy change, quota change) shall create audit_logs records. Admin actions shall require explicit confirmation via x-confirm-dialog for destructive operations. | Must | Security |
| FR-ADM-004 | Administrators shall inspect and stop workspaces: view status, owner, course, resource usage (memory %, CPU % from docker stats), collaborator count, AI session count, recent ai_actions_log entries. Force-stop broadcasts a system Reverb event to workspace collaborators before stopping the container. | Must | Feature |
| FR-ADM-005 | Administrators shall manage extension CRUD, global toggle (AJAX CSS toggle), workspace configuration, artifact integrity (rebuildHash action), and dispatch SyncWorkspaceExtensions for active containers. All extension management actions are audited. | Must | Feature |
| FR-ADM-006 | Administrators shall manage workspace quotas via a dedicated Quota Management admin panel with CRUD for workspace_quotas records, showing which workspaces currently use each quota in a collapsible detail panel. | Must | Feature |
| FR-ADM-007 | The audit log viewer shall be searchable by actor, action, resource_type, resource_id, result, date range, and IP address. Each row shall expand to show a before/after JSON diff of changed properties rendered as a styled two-column comparison. Logs are paginated at 25 per page using cursor pagination. | Should | Feature |
| FR-ADM-008 | The system shall provide a Feature Flags management panel where administrators toggle named feature flags with role selectors and user-ID targeting. Flag changes take effect within 60 seconds (Redis TTL). | Must | Feature |
| FR-ADM-009 | The system shall provide a System Configuration panel for editing key platform settings (AI model, token budgets, workspace defaults, registration settings) stored in system_settings with encryption for sensitive values. | Should | Feature |
| FR-ADM-010 | The system shall support scheduled Maintenance Mode with a platform_announcements broadcast 30 minutes before start, a custom dark maintenance page for non-admin users, and admin bypass with a visible MAINTENANCE MODE ACTIVE banner. | Should | Feature |

---

## Analytics, Forensics & Gamification Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-ANL-001 | The system shall record normalized analytics_events for all significant actions: logins, course activity, assignment starts, submissions, grading, workspace lifecycle, file writes, AI actions, collaboration activity, video sessions, deployment events, and admin actions. Events are created via Model Observers — not inline controller tracking code. | Must | Feature |
| FR-ANL-002 | Analytics dashboards shall be role-restricted: students see only their own data, instructors see data for their owned courses only, administrators see platform-level aggregates. AnalyticsPolicy enforces these boundaries on every analytics API endpoint. | Must | Security |
| FR-ANL-003 | VisionGuard shall classify workspace document changes into attribution categories: human-typed (DocumentSync source: 'human'), AI-approved (DocumentSync source: 'ai'), and pasted/imported where detectable. Classification is reported as human_percentage and ai_percentage with a confidence_level indicator. | Must | Feature |
| FR-ANL-004 | The instructor grading view shall include a VisionGuard forensics tab showing: human_percentage and ai_percentage as large stat cards, a Chart.js donut chart visualizing the split, raw counts (keystrokes, AI characters), last_synced_at timestamp, and a disclaimer note explaining the classification methodology and limitations. | Must | Browser |
| FR-GAM-001 | The system shall render a 365-day contribution heatmap as a CSS Grid of 12×12px squares. Activity intensity levels (0–4) are mapped by event count thresholds. Intensity colors: 0=#1c2128, 1=violet 20%, 2=violet 40%, 3=violet 70%, 4=violet 100%+glow. Each square shows a native tooltip with date and count. | Should | Browser |
| FR-GAM-002 | Daily streaks shall be calculated by the daily:update-streaks scheduled command at midnight UTC. Logic: if last event date is today, increment or maintain streak; if yesterday, increment; if gap detected, reset to 0 or 1 depending on direction. Store in users.current_streak, longest_streak, streak_last_date. | Should | Feature |
| FR-GAM-003 | The system shall award 10 defined badges from real platform events via GamificationService::evaluateUser, called by Model Observers after relevant actions. Badges awarded: first_submission, perfect_grade, 7_day_streak, 30_day_streak, ai_apprentice (first agent session), ai_master (10 agent sessions), patch_reviewer (25 patches reviewed), deployed, collaborator, course_complete. Each badge is awarded only once per user. | Should | Feature |
| FR-DEP-001 | Students shall request deployment of owned eligible workspaces. The deploy endpoint shall: verify workspace ownership via DeploymentPolicy, create a deployments record (status: pending), dispatch DeployWorkspaceJob to the deployments queue, and return HTTP 202 with the deployment ID. | Should | Feature |
| FR-DEP-002 | Deployment shall require explicit public-exposure confirmation via an x-confirm-dialog before the deploy API call is made. The confirmation modal shall clearly state that the project will be publicly accessible. | Must | Browser / Security |
| FR-DEP-003 | DeployWorkspaceJob shall create a ZIP archive of the workspace project directory excluding: .git, .env, vendor, node_modules, __pycache__, storage/app, bootstrap/cache, and platform-owned files. Exclusion paths are defined in config/deployment.php. | Must | Security |
| FR-DEP-004 | Deployment status shall be stored in the deployments table and visible on the Student Dashboard and workspace top bar. Real-time status updates shall be broadcast via DeploymentCompleted Reverb events on the user's private notification channel. | Should | Feature |
| FR-DEP-005 | The deployment provider abstraction shall support Vercel (REST API) and Railway (GraphQL API) through the same DeploymentProvider interface. Provider selection is environment-driven. Both providers implement polling with 60-attempt limit (5-minute maximum), failure summary storage, and error_message field population. | Should | Feature |

---

## PWA & Notifications Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-PWA-001 | The web app manifest shall include: name, short_name, description, start_url (/dashboard?pwa=1), display: standalone, background_color: #0a0a0a, theme_color: #8b5cf6, icons at 72/96/128/144/152/192/384/512px (maskable and any-purpose), screenshots (desktop and mobile), and app shortcuts (Dashboard, My Courses, Join Course). | Should | Browser |
| FR-PWA-002 | The Workbox 7 service worker shall implement these route strategies: NavigationFetch routes — NetworkFirst with 3-second timeout and offline fallback; Static assets (/css, /js, /fonts, /icons) — CacheFirst with 30-day expiry; External CDN resources — StaleWhileRevalidate; /api/* routes — NetworkOnly; /workspace/* routes — NetworkOnly. IDE routes must never be served from cache. | Must | Security |
| FR-PWA-003 | The offline fallback page shall be a self-contained, server-render-independent HTML page with all styles inline. It shall explicitly state that the IDE requires internet connectivity and list which cached pages may still be available. It shall never pretend the IDE is functional offline. | Should | Browser |
| FR-PWA-004 | Users shall subscribe and unsubscribe from web push notifications. The push subscription is stored in push_subscriptions tied to the authenticated user. VAPID keys are environment-configured. The subscription process requires Notification.permission to be granted. The permission prompt is shown once via a persistent info toast after a 10-second delay, not a browser native popup triggered automatically. | Should | Feature |
| FR-PWA-005 | All notification click URLs shall be validated server-side against the application domain before being included in push payloads. Notification clicks shall open only authorized destination pages within the VisionLab application. External URLs are never included in push notification data payloads. | Must | Security |
| FR-PWA-006 | The system shall support Background Sync for offline submission POST requests. Failed submission attempts while offline shall be queued in IndexedDB. On network restore, the service worker sync event replays the queued requests and shows a push notification confirming the submission upload. | Should | Browser |

---

## Interface and Event Contract Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-INT-001 | Workspace file APIs shall return stable JSON shapes for all operations: success (data: {path, content?, children?}), validation error (422 with field errors), authorization error (403 with safe message), not found (404), conflict (409), and server error (500 with correlation_id). These shapes shall not change without a versioning notice. | Must | Feature |
| FR-INT-002 | Workspace lifecycle shall expose these states with accurate UI representation: starting (spinner, launching banner), running (green dot, IDE accessible), unhealthy (amber, reconnecting), stopped (gray, Start button), failed (red, error message with diagnostics), unauthorized (403 message with redirect), offline (offline banner). | Must | Feature |
| FR-INT-003 | Collaboration events shall define stable payload schemas for: UserJoined (id, name, avatar_url, role, color), UserLeft (id), CursorMoved (file_path, line, column, selection nullable, user_id, color), CodeUpdated (file_path, changes[], sequence_number, user_id), ChatMessageSent (message, user_id, user_name, avatar, timestamp), PatchProposed (patch_id, file_path, diff, session_id). | Must | Feature |
| FR-INT-004 | AI patch events shall define stable payload schemas for these states: proposed (patch_id, file_path, diff), approved (patch_id, applied_at), rejected (patch_id, rejected_at), applied (patch_id, file_path), failed (patch_id, error), rolled_back (patch_id, snapshot_id), expired (patch_id). | Must | Feature |
| FR-INT-005 | Deployment events shall define stable payload schemas for: pending (deployment_id, workspace_id), packaging (deployment_id), deploying (deployment_id, provider), live (deployment_id, production_url), failed (deployment_id, error_summary). | Should | Feature |
| FR-INT-006 | Admin governance endpoints shall return auditable result objects and shall never report {success: true} for a partially failed policy change. Any operation affecting multiple resources shall be wrapped in a database transaction. | Must | Security / Feature |
| FR-INT-007 | Extension build records (extension_builds table) shall store: source_repository, source_reference (tag + commit SHA), build_strategy, branding_changes_summary (list of modified files), configuration_changes_summary, dependency_lock_hash, artifact_path, artifact_checksum (SHA256), license_review_status, old_identity_scan_result, smoke_test_result, built_by (FK users), built_at. | Must | Operational |
| FR-INT-008 | The health endpoint (GET /api/health, no auth required for liveness) shall check and return: database connectivity, Redis connectivity, Reverb WebSocket reachability, disk space (>1GB free threshold), queue worker liveness (last Horizon heartbeat <60s). Return HTTP 200 for all healthy, HTTP 503 with the failing component named for any failure. An authenticated /api/health/detailed endpoint returns extended operational metrics for admin monitoring. | Must | Operational |
| FR-INT-009 | Notification events shall include: actor_id, recipient_id, resource_type, resource_id, channel (database/broadcast/push), delivery_state, click_destination (validated URL), and created_at. | Should | Feature |
| FR-INT-010 | Public deployment URLs shall be stored in deployments.production_url only after the provider confirms success (READY or SUCCESS status in polling result). The URL is visible only to the workspace owner, enrolled instructors, and administrators. | Must | Security |

---

## Non-Functional Requirements

| ID | Requirement | Target / Threshold | Priority | Verification |
|---|---|---|---|---|
| NFR-001 | Health monitoring | Health endpoint returns accurate status for all 5 dependencies within 2 seconds | Must | Operational |
| NFR-002 | Query performance | No avoidable N+1 queries on critical pages; Eloquent Strict Mode enabled in development; all dashboard aggregations via SQL aggregates, not PHP loops | Must | Performance |
| NFR-003 | Response time benchmarks | Landing page TTFB <200ms; Student dashboard <300ms; Analytics (cached) <150ms; AI first-byte <1000ms | Must | Performance |
| NFR-004 | Security negative test coverage | Every ASVS Level 2 category has at least one negative automated test | Must | Security |
| NFR-005 | Role-restricted data visibility | Student analytics, submissions, forensics, and AI logs are inaccessible to unauthorized roles (verified by negative tests) | Must | Security |
| NFR-006 | Audit trail completeness | All sensitive actions include actor, action, resource, result, correlation_id, IP where available, and UTC timestamp | Must | Operational |
| NFR-007 | Accessibility compliance | Critical workflows pass WCAG 2.1 AA checks: keyboard navigation, label association, 4.5:1 contrast, visible focus rings, responsive 320px–2560px | Must | Accessibility |
| NFR-008 | Provider failure resilience | AI, video, push, and deployment provider failures produce clear product states and operational logs without leaking secrets | Must | Operational |
| NFR-009 | Recovery procedures | Backup restore, AI patch rollback, workspace cleanup, and deployment rollback are documented and verified | Must | Operational |
| NFR-010 | Legal provenance | All imported open-source extension code preserves required notices and is released through VisionLab-controlled build and versioning | Must | Legal |
| NFR-011 | Cache invalidation | All Redis-cached queries are invalidated by Model Observers on relevant data changes. TTLs serve as fallback, not primary invalidation strategy | Must | Performance |
| NFR-012 | PWA Lighthouse score | Lighthouse PWA audit score ≥90 on desktop and mobile | Should | Browser |

---

## Production Operations Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-OPS-001 | CI/CD pipeline shall run dependency installation, PHPUnit tests (all must pass), frontend build, Docker image build, artifact checksum verification, and deployment in a 3-stage pipeline. Deployment is blocked if any prior stage fails. | Must | Operational |
| FR-OPS-002 | The health endpoint shall expose liveness (no auth) and detailed operational readiness (admin auth) for all critical dependencies. | Must | Operational |
| FR-OPS-003 | Backup and restore procedures shall be documented for: MySQL (daily mysqldump to compressed archive with 7-day retention), workspace storage (rsync or volume snapshot), extension artifacts (git-backed in the artifact registry). A restore rehearsal shall be conducted before production readiness sign-off. | Must | Operational |
| FR-OPS-004 | The production Docker Compose topology shall define all services with security-hardened configurations: non-root users, read-only root filesystems where possible, no unnecessary port exposure, named internal networks, HEALTHCHECK instructions, and no hardcoded credentials. | Must | Operational |
| FR-OPS-005 | Nginx shall be configured for: TLS 1.2/1.3 only, SSL Labs A+ rating, WebSocket proxy for Reverb with proper Upgrade headers, SSE proxying with proxy_buffering off for /api/v1/ai/chat, all OWASP-recommended security headers (HSTS, X-Content-Type-Options, X-Frame-Options, CSP, Referrer-Policy, Permissions-Policy), and rate limiting at the Nginx level as a defense-in-depth complement to Laravel rate limiting. | Must | Operational / Security |

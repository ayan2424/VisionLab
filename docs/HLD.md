# VisionLab High-Level Design
## Version 9.0 — Production-Grade Architecture Document

---

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | High-Level Design (HLD) |
| Version | 9.0 |
| Prompt Pack | `PROMPTS.xml` v9.0 |
| Standard | ISO/IEC/IEEE 42010:2011 — Architecture Description |
| Audience | Engineering, Security, DevOps, QA |

---

## Architecture Goals

| Goal | Constraint |
|---|---|
| Keep all authorization authority in Laravel Policies and service layer | No client-side authorization trust |
| Isolate every code-server workspace container | No cross-workspace filesystem access, no shared network |
| Keep AI provider secrets server-side at all times | Provider keys never in browser JavaScript, extension code, or API responses |
| Make extension delivery reproducible and integrity-verifiable | VisionLab-controlled fork, SHA256 registry, immutable container layer |
| Treat service workers as progressive enhancement only | Sensitive APIs and IDE routes are always NetworkOnly |
| Make production operations observable and recoverable | Health endpoints, structured logs, backup procedures, runbooks |
| Enforce quota limits at the container runtime level | Docker resource flags derived from database records |
| Maintain an immutable audit trail for all sensitive operations | append-only audit_logs, analytics_events via Model Observers |

---

## Architecture Decision Log

| ADR | Decision | Rationale | Trigger |
|---|---|---|---|
| ADR-001 | Laravel 11 bootstrap/app.php architecture, no legacy Kernel | Cleaner middleware registration, forward-compatible with Laravel roadmap | Phase 1 kickoff |
| ADR-002 | Workspace isolation via Docker containers with internal network | Prevents cross-workspace access; enforces resource quotas at OS level | Phase 3 kickoff |
| ADR-003 | Extension delivery via VisionLab-controlled artifact registry with SHA256 | Prevents supply-chain compromise; enables reproducible builds | Phase 4 kickoff |
| ADR-004 | VisionLab Agent built from verified Continue source fork | Legal compliance; full identity control; no upstream distribution dependency | Phase 4 kickoff |
| ADR-005 | Reverb whisper for cursor events (client-to-client, no server storage) | Eliminates high-frequency cursor events from server storage; reduces load | Phase 5 kickoff |
| ADR-006 | AI mutations require ai_pending_patches with human approval; no direct writes | Core product principle: human-in-the-loop; supports rollback and audit | Phase 6 kickoff |
| ADR-007 | Jitsi provider abstraction (JaaS + self-hosted via same interface) | Enables environment-based provider switching without code changes | Phase 7 kickoff |
| ADR-008 | Workbox 7 NetworkOnly for /api/* and /workspace/* routes | Prevents cached stale auth responses; ensures IDE always contacts live container | Phase 10 kickoff |
| ADR-009 | GitHub Actions with OIDC authentication (no long-lived PAT secrets) | Reduced secret exposure surface; short-lived tokens tied to workflow run | Phase 12 kickoff |
| ADR-010 | Redis for sessions, cache, queues, rate limiting, and Reverb scaling | Single consistent backing store; simplifies ops; enables horizontal scaling | Phase 1 kickoff |
| ADR-011 | Dual-layer marketplace restriction (--disable-marketplace flag + invalid VSCODE_GALLERY_SERVICE_URL) | Defense-in-depth; single flag bypass does not restore marketplace access | Phase 4 kickoff |
| ADR-012 | Deployment package exclusion list in config/deployment.php | Prevents .env, secrets, and vendor directories from being deployed; configurable | Phase 9 kickoff |

---

## System Context View

```
┌──────────────────────────────────────────────────────────┐
│                    USER BROWSERS                         │
│  Student Browser │ Instructor Browser │ Admin Browser    │
└──────────┬───────┴──────────┬─────────┴────────┬─────────┘
           │                  │                  │
           ▼                  ▼                  ▼
┌──────────────────────────────────────────────────────────┐
│              Nginx TLS Reverse Proxy                     │
│   TLS 1.3 │ Security Headers │ Rate Limiting │ Routing   │
│   WebSocket Upgrade (Reverb) │ SSE Buffering Off (AI)   │
│   Static Asset Caching │ code-server Routing by Port    │
└──────────┬─────────────────┬───────────────────┬─────────┘
           │                 │                   │
           ▼                 ▼                   ▼
┌──────────────┐   ┌──────────────────┐   ┌─────────────────────┐
│ Laravel App  │   │  Reverb Server   │   │  code-server         │
│ PHP 8.3-FPM  │   │  WebSocket       │   │  Container Pool      │
│              │   │  Presence +      │   │  (visionlab-workspace│
│ Web + API    │   │  Private Channels│   │  -net isolated)      │
│ Controllers  │   │                  │   │                      │
│ Policies     │   └──────────────────┘   │  VisionLab Collab    │
│ Services     │                          │  VisionLab Agent     │
│ Jobs         │   ┌──────────────────┐   │  Patch Reviewer      │
│ Events       │──▶│    Redis 7       │   └─────────────────────┘
│ Commands     │   │  Sessions, Cache │
└──────┬───────┘   │  Queues, Limits  │   ┌─────────────────────┐
       │           │  Reverb Scaling  │   │  External Providers  │
       ▼           └──────────────────┘   │  Anthropic AI API    │
┌──────────────┐                          │  Jitsi / JaaS Video  │
│  MySQL 8.0   │   ┌──────────────────┐   │  Vercel / Railway    │
│  InnoDB      │   │  Queue Workers   │   │  Push (VAPID)        │
│  Strict Mode │   │  Horizon Monitor │   └─────────────────────┘
│  25 Tables   │   │  Scheduler       │
└──────────────┘   │  Pulse Monitor   │
                   └──────────────────┘
```

---

## Deployment Topology

```
Production VPS (GCP e2-standard-8 or equivalent Ubuntu 22.04+)
├── docker-compose.prod.yml
│   ├── web (Nginx:alpine — TLS termination, routing)
│   ├── app (PHP 8.3-FPM — multi-stage production build)
│   ├── reverb (Reverb WebSocket — same app image, different CMD)
│   ├── horizon (Queue monitor — same app image, artisan horizon)
│   ├── scheduler (Cron-style scheduler — same app image)
│   ├── database (MySQL 8.0 — named volume, tuned my.cnf)
│   ├── redis (Redis 7 — AOF persistence, password-protected)
│   └── code-server-base (visionlab/code-server:latest — not started directly)
│
├── Docker Networks
│   ├── visionlab-internal (app, reverb, horizon, scheduler, database, redis)
│   └── visionlab-workspace-net (dynamically spawned code-server containers, strictly Nix-based)
│
└── Storage Volumes
    ├── mysql-data (named Docker volume)
    ├── workspace-storage (bind mount: /srv/visionlab/workspaces)
    └── extension-artifacts (bind mount: /srv/visionlab/extensions — read-only in containers)
```

---

## Logical Component Responsibility Matrix

| Component | Owns | Must Never Own |
|---|---|---|
| Controllers | Request authorization check, input validation dispatch, response selection, audit event dispatch | Business rules duplicated from services, database queries, policy logic |
| Domain Services | Workspace lifecycle, AI policy enforcement, extension policy, deployment workflow, quota resolution | Direct view rendering, browser-only state, unauthorized decisions |
| Policies / Gates | Authorization decisions and denial reasons with precise policy method names | Persistence side effects, business logic, state mutations |
| Jobs | Long-running external operations, provider polling, retry management, batch operations | Unaudited security decisions, unauthorized cross-resource access |
| Events | Real-time facts broadcast via Reverb | Secret values, unbounded payloads (>50KB), sensitive personal data in payload |
| Model Observers | Analytics event creation, cache invalidation, audit log dispatch | Direct external API calls, blocking I/O |
| VS Code Extensions | Workspace UI integration, editor-side interaction, WebSocket subscription | Provider secrets, platform policy mutation, final authorization decisions |
| Admin UI | Governance workflows, operational visibility, configuration management | Bypass routes, direct infrastructure manipulation, unauthenticated access |
| AiSandbox | File path verification, content safety filtering, tool execution, snapshot creation | AI provider communication, user interface rendering |

---

## Data Domain Architecture

```
Identity Domain
  users, notification_preferences

Classroom Domain
  courses ──▶ enrollments ──▶ users
  courses ──▶ announcements
  courses ──▶ assignments ──▶ submissions ──▶ workspaces
                                          ──▶ submission_forensics

Workspace Domain
  workspaces ──▶ workspace_collaborators ──▶ users
  workspaces ──▶ collab_sessions
  workspaces ──▶ workspace_extensions ──▶ extensions

Extension Domain
  extensions ──▶ extension_builds
  extensions ──▶ workspace_extensions

AI Domain
  ai_chat_sessions ──▶ ai_messages
  ai_chat_sessions ──▶ ai_actions_log
  ai_pending_patches ──▶ ai_snapshots
  ai_artifacts

Collaboration Domain
  collab_chat_messages
  collab_sessions

Video Domain
  video_rooms ──▶ attendance_logs

Analytics Domain
  analytics_events
  submission_forensics
  user_badges

Operations Domain
  audit_logs
  feature_flags
  system_settings
  workspace_quotas

Deployment Domain
  deployments

Notification Domain
  push_subscriptions
  notifications (Laravel default)
```

---

## Workspace Architecture Detail

```
Workspace Lifecycle State Machine:

  [not_started]
       │ User opens assignment
       ▼
  [starting] ──── CodeServerManager::startWorkspace ────▶
       │                                                  │
       │  Quota resolution (5-tier priority)             │
       │  Port allocation (SELECT FOR UPDATE lock)        │
       │  Token generation (bin2hex random_bytes(32))    │
       │  Directory setup + template copy                 │
       │  Docker run (all mandatory security flags)       │
       │  Workspace record updated                        │
       ▼                                                  │
  [running] ◀──────────────────────────────────────────── ┘
       │  Extensions installed via docker exec
       │  settings.json injected
       │  VisionLab Agent config.json injected
       │
       │  Stale heartbeat (>30 min) ──▶ [stopping]
       │  Admin force-stop          ──▶ [stopping]
       │  User navigate away        ──▶ [idle, still running]
       │  Container OOM-kill (SIGABRT) ──▶ [unhealthy]
       ▼
  [unhealthy] ──── Health check detects OOM ──▶ [stopping]
       ▼
  [stopping] ──── docker stop + docker rm ──▶ [stopped]
       ▼
  [stopped] ──── Workspace cleanup (daily) ──▶ [archived]
```

```
File API Security Pipeline (all 6 endpoints):

  Request arrives at WorkspaceFileController
       │
       ├─ 1. auth:sanctum middleware
       ├─ 2. suspended middleware
       ├─ 3. WorkspacePolicy::access_files (policy)
       ├─ 4. URL-decode the relative path
       ├─ 5. Prepend canonical workspace project path
       ├─ 6. realpath() — if false: HTTP 403 + analytics_event(path_traversal_attempt)
       ├─ 7. Verify result starts with canonical project path — if not: HTTP 403 + log
       ├─ 8. Check blocked path patterns (.env, .git, vendor, node_modules, null bytes)
       ├─ 9. For writes: AiSandbox::validateContent safety filters
       └─ 10. Execute filesystem operation + log to ai_actions_log
```

---

## Extension Architecture Detail

```
Extension Tiers:

  Tier 1 — VisionLab Core (Source-Built, Mandatory, Immutable)
  ┌─────────────────────────────────────────────────────────┐
  │ visionlab-collab (TypeScript, custom built Phase 5)     │
  │ visionlab-patch-reviewer (TypeScript, custom Phase 6)   │
  │ visionlab-ai (Continue fork, source-built Phase 4)      │
  └─────────────────────────────────────────────────────────┘
         │ Installed during Docker image build
         │ /usr/local/share/code-server/extensions/
         │ root:root, chmod 555 (read+execute only)
         ▼ Cannot be removed by UID 1000 (coder user)

  Tier 2 — Verified Prebuilt Utilities (Strategy B, Mandatory, Immutable)
  ┌─────────────────────────────────────────────────────────┐
  │ visionlab-gitlens, visionlab-prettier, visionlab-eslint │
  │ visionlab-intelephense, visionlab-coderunner            │
  └─────────────────────────────────────────────────────────┘
         │ Same immutable directory, same protection

  Tier 3 — Optional Course Tools (Not Rebranded, Instructor-Controlled)
  ┌─────────────────────────────────────────────────────────┐
  │ Docker Explorer, Database Client, REST Client           │
  │ Markdown Preview Enhanced                               │
  └─────────────────────────────────────────────────────────┘
         │ Installed at container runtime via docker exec
         │ /home/coder/.local/share/code-server/extensions/
         │ User-writable directory — can be managed per workspace
```

```
VisionLab Agent Release Pipeline (Phase 4, Step 4.1.1):

  1. LICENSE REVIEW ──▶ upstream license permits modification + redistribution?
         │ Yes → proceed │ No → BLOCKED
         ▼
  2. SOURCE IMPORT ──▶ clone upstream at pinned tag → VisionLab-controlled fork
         │ Record: repo URL, tag, commit SHA in extension_builds
         ▼
  3. FULL SOURCE AUDIT ──▶ scan all files for upstream identity strings
         │ Output: audit report documenting every file to edit
         ▼
  4. COMPLETE SOURCE EDITING ──▶ edit every identified file
         │ package.json metadata, UI strings, webview titles, command labels,
         │ status bar text, icon assets, endpoint defaults, config defaults
         │ BLOCKING: metadata-only edit is insufficient for release
         ▼
  5. CLEAN COMPILE ──▶ npm ci + official build script from clean checkout
         │ No manual post-compile edits permitted
         ▼
  6. OLD-IDENTITY SCAN ──▶ scan compiled .vsix archive for upstream strings
         │ Zero matches required. Any match → BLOCKED until resolved
         ▼
  7. REGISTER ARTIFACT ──▶ SHA256 + extension_builds record created
         ▼
  8. SMOKE TEST ──▶ install in test code-server instance
         │ Activation, commands, webviews, proxy config, PatchProposed broadcast
         ▼
  9. RELEASE ──▶ VisionLab versioned artifact, stored in artifact registry
         │ All future production installs from VisionLab-controlled artifacts
         │ No dependency on upstream Continue distribution
```

---

## AI Architecture Detail

```
AI Request Lifecycle:

  VisionLab Agent Extension
       │ POST /api/v1/ai/v1/chat/completions (OpenAI format)
       │ Headers: X-Workspace-Id, X-Visionlab-Mode, Authorization: Bearer {token}
       ▼
  AiController (Laravel)
       ├─ auth:sanctum + token ability check (AI_QUERY)
       ├─ WorkspacePolicy authorization
       ├─ Token budget check (Redis-cached daily sum per user)
       │    Over limit → HTTP 429 {code: BUDGET_EXCEEDED, resets_at}
       ├─ Resolve/create ai_chat_sessions record
       ├─ Read .visionlab_memory.md (if exists, <10,000 chars → prepend to system prompt)
       ├─ Build mode-specific system prompt (CHAT / PLAN / AGENT)
       ├─ Call AiService::stream()
       ▼
  AiService → Anthropic API (streaming)
       │
       ├─ text_delta → proxy as SSE {type:"text", delta:"..."}
       │
       ├─ tool_use (read_file)
       │    └─▶ AiSandbox::readFile → realpath() check → content returned
       │
       ├─ tool_use (list_directory)
       │    └─▶ AiSandbox::listDirectory → realpath() check → listing returned
       │
       ├─ tool_use (search_codebase)
       │    └─▶ AiSandbox::searchCodebase → RecursiveDirectoryIterator → matches
       │
       ├─ tool_use (propose_patch)
       │    ├─▶ AiSandbox path validation (realpath + canonical check)
       │    ├─▶ AiSandbox::validateContent (safety filters on replace_block)
       │    │    Blocked pattern → HTTP 422 + analytics_event(ai_safety_violation)
       │    ├─▶ Create ai_snapshots record (content + SHA256 hash)
       │    ├─▶ Generate unified diff
       │    ├─▶ Create ai_pending_patches record (status: pending, expires in 30min)
       │    ├─▶ Broadcast PatchProposed (private-workspace.{id}.patches channel)
       │    └─▶ Return tool_result: {status: pending, patch_id: X}
       │
       └─ message_stop
            └─▶ Update ai_chat_sessions (input_tokens, output_tokens, cost_usd)
                Update user's Redis token budget cache

  Patch Review Flow:
  PatchProposed event → VisionLab-patch-reviewer extension
       │ Two-pane diff viewer opens automatically
       ├─ Approve → POST /api/v1/ai/approve-patch
       │    └─▶ AiSandbox::applyPatch (writes file, updates patch status: approved)
       │         → ai_actions_log entry (action: patch_approved)
       ├─ Reject → POST /api/v1/ai/reject-patch
       │    └─▶ Update patch status: rejected → ai_actions_log entry
       └─ Rollback → POST /api/v1/ai/rollback (restores from ai_snapshots)
```

---

## Collaboration Architecture Detail

```
Reverb Channel Structure:

  workspace.{workspaceId} (Presence Channel)
  ├─ Authorization: owner_id match OR workspace_collaborators entry
  ├─ Reject if: workspace.is_active = false
  ├─ Presence payload: {id, name, avatar_url, role, assigned_color}
  │
  ├─ Events IN: (from server, ShouldBroadcastNow)
  │   CodeUpdated, ChatMessageSent, UserJoinedWorkspace, UserLeftWorkspace
  │   VideoCallStarted, VideoCallEnded, PlanExecutionProgress, SystemAlert
  │
  └─ Whispers: (client-to-client, no server storage, no DB write)
       DocumentUpdated (file_path, line, col, selection, user_id, user_color)

  private-workspace.{workspaceId}.patches (Private Channel)
  ├─ Authorization: owner_id match OR workspace_collaborators entry
  └─ Events IN: PatchProposed (patch_id, file_path, diff, session_id)

  private-notifications.{userId} (Private Channel)
  ├─ Authorization: auth()->id() === $userId (exact match only)
  └─ Events IN: DeploymentUpdated, BadgeEarned, NotificationSent

  platform.announcements (Public Channel)
  └─ Events IN: PlatformAnnouncement, MaintenanceScheduled
```

```
Collaboration Extension Module Architecture:

  VisionLab-collab (TypeScript, activation: onStartupFinished)
  ├─ extension.ts (activate/deactivate, disposables registration)
  ├─ RealtimeManager.ts
  │   ├─ Laravel Echo + Pusher-js initialization
  │   ├─ /broadcasting/auth with stored Sanctum token
  │   ├─ Presence channel subscription
  │   ├─ Exponential backoff reconnect (1s→2s→4s→8s→30s max)
  │   └─ reconnected event → full state re-sync
  ├─ DocumentSync.ts
  │   ├─ onDidChangeTextDocument listener (debounce 150ms)
  │   ├─ sourceType tagging: 'human' or 'ai' (for Analytics Dashboard)
  │   ├─ Per-file sequence number Map
  │   ├─ Echo prevention (compare event user_id vs local user_id)
  │   └─ WorkspaceEdit.replace for precise remote edit application
  ├─ DocumentSync.ts
  │   ├─ onDidChangeTextEditorSelection listener (throttle 80ms)
  │   ├─ Reverb whisper for cursor broadcast (no server storage)
  │   ├─ TextEditorDecorationType per remote user
  │   └─ 3-second expiry timeout on no new event from user
  ├─ ChatPanel.ts (WebviewViewProvider, retainContextWhenHidden: true)
  │   ├─ Load history via GET /api/v1/workspace/{id}/chat/history
  │   ├─ Append ChatMessageSent events in real-time
  │   └─ Webview CSP: no inline scripts, VisionLab domain only
  └─ VideoPanel.ts
      ├─ VisionLab.startVideoCall command handler
      ├─ WebviewPanel with dark-themed Jitsi embed
      ├─ VideoCallStarted Reverb listener → join prompt
      └─ videoConferenceLeft → leave API + remove status bar item
```

---

## PWA Architecture

```
Service Worker Route Strategy Map:

  /workspace/*          → NetworkOnly (NEVER cached — IDE requires live container)
  /api/*                → NetworkOnly (NEVER cached — auth state could become stale)
  /admin/*              → NetworkOnly (NEVER cached — governance state must be live)
  navigation requests   → NetworkFirst (3s timeout → cache → offline fallback)
  /css/*, /js/*, /fonts/*, /icons/*  → CacheFirst (30-day max age, 100 entries)
  External CDN resources → StaleWhileRevalidate (7-day max age)

  Background Sync:
  → Offline submission POST requests queued in IndexedDB
  → Replayed via service worker sync event on network restore
  → Push notification sent on successful replay

  Cache Versioning:
  → All caches prefixed with 'visionlab-v{APP_VERSION}-{type}'
  → activate event deletes all non-current version caches
```

---

## Security Architecture Map

```
OWASP ASVS Level 2 Coverage:

  V1 Architecture     → Service provider bindings, policy registration, ADRs
  V2 Authentication   → Breeze, bcrypt 12, session security, rate limits, suspended check
  V3 Session          → Redis sessions, secure+HttpOnly cookies, CSRF protection
  V4 Access Control   → Policies for all domain actions, RoleMiddleware, SuspendedMiddleware
  V5 Validation       → FormRequest classes for all inputs, finfo MIME for uploads
  V6 Cryptography     → bcrypt, random_bytes, Laravel Crypt for sensitive settings
  V7 Error Handling   → Custom error pages, APP_DEBUG=false, no stack traces to users
  V8 Data Protection  → .env excluded from git, Sanctum token abilities, Redis encryption
  V9 Communications   → TLS 1.3, HSTS, certificate pinning via CAA records
  V10 Malicious Code  → Extension checksums, no vendor/ edits, dependency scanning
  V11 Business Logic  → Final admin protection, AI approval gate, deployment confirmation
  V12 Files/Resources → realpath() sandbox, MIME verification, UUID filenames
  V13 API             → Sanctum token auth, rate limiting, versioned endpoints
  V14 Configuration   → Security headers, CSP nonce, no debug in production

  OWASP LLM Top 10 Coverage:
  LLM01 Prompt Injection  → Untrusted content labeled in system prompt, tool policy enforced
  LLM02 Output Handling   → AI artifacts in sandboxed iframes (no allow-same-origin)
  LLM05 Supply Chain      → VisionLab-controlled fork, SHA256, registry, license review
  LLM06 Sensitive Info    → Provider keys server-side only, sandbox blocks .env reads
  LLM07 Plugin Design     → Tools require server authorization before execution
  LLM08 Excessive Agency  → Human-approved patches, 20-patch safety limit, mode matrix
  LLM09 Overreliance      → Analytics Dashboard shows AI attribution; mode restrictions educate
  LLM10 Model Theft       → Provider keys never in client code or API responses

  Container Security (OWASP Docker Cheat Sheet):
  ✓ Non-root user (UID 1000)
  ✓ --security-opt no-new-privileges:true
  ✓ --cap-drop ALL
  ✓ --read-only root filesystem
  ✓ --tmpfs /tmp:rw,noexec,nosuid,size=100m
  ✓ --network visionlab-workspace-net (internal only)
  ✓ --init (correct signal handling)
  ✓ Bounded --memory, --memory-swap, --cpus, --pids-limit
  ✓ No Docker socket exposure
  ✓ HEALTHCHECK in image
```

---

## Data Retention and Privacy

| Data Class | Examples | Retention Direction | Access Restriction |
|---|---|---|---|
| Identity | users, roles, account status | Retain while active; follow institutional policy | User self, admin |
| Classroom | courses, assignments, submissions, grades | Retain per academic policy; archived after completion | Student owner, instructor, admin |
| Workspace | files, snapshots, container metadata | Retain during coursework; archive after grace period; cleanup after policy window | Owner, collaborators, instructor/admin per policy |
| AI Records | sessions, messages, patches, snapshots | Retain for transparency with configurable cleanup; patch records preserved for rollback window | Workspace owner, instructor, admin where authorized |
| Analytics | activity events, Analytics Dashboard aggregates, badges | Retain with privacy boundaries per role | Role-restricted: student sees own, instructor sees course, admin sees platform |
| Operations | audit_logs, health reports, job logs, release evidence | Retain for compliance and incident response | Administrators and authorized operators only |
| Extension Builds | source references, build logs, checksums, license reviews | Permanent retention; legal and security evidence | Platform engineers and security reviewers |

---

## Quality Attribute Architecture

| Attribute | Architectural Mechanism |
|---|---|
| Availability | Health endpoints, queue workers with retry, scheduler verification, provider failure states, restartable workspace lifecycle, container health checks |
| Performance | Redis caching with Model Observer invalidation, cursor pagination for large admin lists, SQL aggregate functions (not PHP loops), Eloquent Strict Mode for N+1 detection, Nginx static asset caching |
| Security | Policy-first authorization, sandboxed file operations (realpath), AI tool restrictions (safety filters + path blocks), extension checksums, container hardening, CSP nonce, HSTS |
| Privacy | Role-restricted analytics, limited audit metadata in API responses, controlled visibility for AI sessions and forensics, workspace file access only for owner + collaborators |
| Accessibility | Reusable Blade components (x-modal, x-data-table, x-empty-state), semantic HTML landmarks, keyboard navigation, visible focus rings, WCAG 2.1 AA color contrast |
| Observability | Structured audit_logs with correlation_id, Model Observer analytics events, Laravel Horizon for queue visibility, Laravel Pulse for performance metrics, health endpoint for live dependency status |
| Maintainability | Service boundaries (CodeServerManager, AiService, JitsiService, DeploymentService), provider abstractions (DeploymentProvider interface), ADR log, RTM links, FormRequest validation layer |
| Recoverability | ai_snapshots for patch rollback, workspace cleanup with archive before delete, database backup procedures, Horizon failed job visibility, deployment rollback notes in RUNBOOK.md |
| Extensibility | Provider abstraction interfaces allow adding AI/video/deployment providers; feature flags allow safe incremental rollout; extension registry allows adding new extensions without code changes |

---

## Component Interface Contracts

| Contract Name | Provider | Consumer | Schema |
|---|---|---|---|
| Workspace File API | WorkspaceFileController | JS FileExplorer, VisionLab Agent tools | 6 endpoints with stable JSON shapes (see FRD-INT-001) |
| AI SSE Stream | AiController | VisionLab Agent extension | {type: text|tool_call_start|tool_result|end, ...} |
| OpenAI-Compatible Proxy | AiController | VisionLab Agent (Continue) extension | POST /api/v1/ai/v1/chat/completions, OpenAI SSE format |
| PatchProposed Event | AiSandbox | VisionLab-patch-reviewer extension | {patch_id, file_path, diff, session_id} |
| Presence Channel | Reverb | VisionLab-collab extension, Blade IDE shell | UserJoined/Left, DocumentUpdated whisper |
| Health Endpoint | HealthController | UptimeRobot, load balancer, CI/CD | {database, redis, reverb, disk, queue: ok|error} |
| Deployment Status Event | DeployWorkspaceJob | Reverb (private-notifications.{userId}) | {deployment_id, workspace_id, status, production_url} |
| Extension Checksum | ChecksumVerificationService | CodeServerManager::startWorkspace | SHA256 hex string comparison before every install |

---

## Architecture Risk Register

| Risk | Category | Mitigation | Phase |
|---|---|---|---|
| Container breakout via shared filesystem | Security | Isolated workspace network, no shared volumes, path sandbox, no Docker socket | 3 |
| AI prompt injection via workspace files | Security | Untrusted content labeled in system prompt, tool path restrictions, safety filters | 6 |
| Extension supply chain compromise | Security | VisionLab-controlled fork, SHA256 verification, old-identity scan, license review | 4 |
| Service worker caching sensitive auth state | Security | NetworkOnly for /api/* and /workspace/*, verified by browser Application panel | 10 |
| Deployment package leaking secrets | Security | config/deployment.php exclusion list, .env blocked at code level | 9 |
| WebSocket channel impersonation | Security | Presence channel authorization via workspace membership, no client-side trust | 5 |
| Stale containers exhausting server resources | Operations | 30-minute heartbeat cleanup, daily WorkspaceCleanup command | 3 |
| AI provider rate limit or outage | Operations | Queued jobs with retry, graceful error states, structured logs, budget tracking | 6 |
| Backup not rehearsed before production | Operations | Restore rehearsal required in Phase 12 acceptance before evaluation | 12 |
| Legal exposure from imported extension code | Legal | License review before import, attribution preservation, VisionLab-controlled fork | 4 |

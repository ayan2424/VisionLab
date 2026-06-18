# VisionLab Product Requirements Document
## Version 9.0 — Production-Grade Enterprise Edition

---

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Product Requirements Document (PRD) |
| Version | 9.0 |
| Prompt Pack | `PROMPTS.xml` v9.0 |
| Audience | Product Owner, Engineering, QA, Security, DevOps |
| Standard | IEEE 29148:2018, OWASP ASVS Level 2, OWASP LLM Top 10 |
| Status | Implementation-Ready Baseline |

---

## Research Basis

| Reference | Application |
|---|---|
| [Laravel 11 Release Notes](https://laravel.com/docs/11.x/releases) | bootstrap/app.php architecture, Reverb WebSocket, health routing, per-second rate limiting, Horizon, Pulse |
| [IEEE 29148:2018](https://standards.ieee.org/standard/29148-2018.html) | Requirements quality, completeness, verifiability, and traceability discipline |
| [IIBA BABOK — Trace Requirements](https://www.iiba.org/knowledgehub/business-analysis-body-of-knowledge-babok-guide/5-requirements-life-cycle-management/5-1-trace-requirements/) | RTM structure and traceability lifecycle |
| [OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/) | Security verification model, Level 2 target baseline |
| [OWASP LLM Top 10](https://owasp.org/www-project-top-10-for-large-language-model-applications/) | AI-specific security controls and excessive agency prevention |
| [OWASP Docker Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Docker_Security_Cheat_Sheet.html) | Container hardening flags and isolation principles |
| [code-server FAQ](https://coder.com/docs/code-server/FAQ) | code-server marketplace constraints, extension installation, proxy behavior |
| [web.dev Service Workers](https://web.dev/learn/pwa/service-workers) | PWA service worker lifecycle and route strategy design |
| [Workbox Documentation](https://developer.chrome.com/docs/workbox/) | Route strategy implementation, cache versioning, Background Sync |

---

## Product Overview

VisionLab is a production-ready, enterprise-grade collaborative coding and learning platform designed for universities. It unifies a full LMS, authenticated browser-based VS Code workspaces, real-time multiplayer coding, governed AI assistance, video sessions, analytics, notifications, project deployment, and production infrastructure into one coherent platform.

VisionLab eliminates the operational fragmentation of running separate tools for classroom management, coding environment setup, AI assistance, video communication, and submission review. All functionality lives in one authenticated, governed, auditable product.

---

## Product Positioning

| Tool VisionLab Replaces | VisionLab Equivalent | Key Differentiator |
|---|---|---|
| Google Classroom | Full LMS (courses, assignments, grading, announcements) | Workspace-linked assignments with snapshot submissions |
| Zoom / Microsoft Teams | Jitsi-powered video sessions embedded in workspaces | In-IDE video with JWT authentication and attendance tracking |
| GitHub Copilot / Cursor AI | Governed VisionLab AI Agent with patch approval | Human-in-the-loop: AI proposes, human approves, every action audited |
| Local IDE Setup | Authenticated code-server browser workspaces | Institutional policy applied at container level, no local config friction |
| Disconnected analytics | VisionGuard forensics and activity analytics | AI contribution attribution with confidence display |

---

## Product Principles

| Principle | Implementation |
|---|---|
| Production truth over presentation tricks | Every screen reflects real application state — no decorative UIs, staged outputs, or mock data in production flows |
| Human-approved mutation for AI | No AI tool may modify files without a stored pending patch record and explicit human approval |
| Policy-driven access everywhere | Authorization decisions flow through Laravel Policies — no inline role checks in controllers or views |
| Security controls must be testable | Every security requirement maps to at least one automated negative test |
| Honest offline behavior | Service workers treat the IDE as network-only — offline fallback never claims IDE functionality without connectivity |
| Extension governance must be reproducible | Extension artifacts are built from VisionLab-controlled sources with stored checksums, build records, and license reviews |
| Evidence-based readiness | Production readiness is demonstrated through passing tests, health checks, backup records, and CI/CD artifacts — not assertions |

---

## User Personas

### Student
**Context:** Computer science or software engineering student completing coding assignments at a university.

**Primary Jobs-to-Be-Done:**
- Join courses and access assignments without local environment setup friction
- Open a workspace with the correct tools pre-installed and policy pre-applied
- Collaborate live with classmates or instructor during lab sessions
- Use AI assistance in a transparent, traceable way — not as a black box
- Submit assignments with snapshot evidence and receive instructor feedback
- Track activity, coding streaks, AI usage, and project deployments
- Deploy a finished project to a live public URL for portfolio purposes

**Frustrations with Current State:** Local environment setup consumes class time, AI usage is invisible to instructors, switching between Classroom / Zoom / Copilot breaks focus, no single view of progress.

### Instructor
**Context:** University instructor managing one or more coding courses.

**Primary Jobs-to-Be-Done:**
- Create and manage courses with full LMS workflows (assignments, grading, announcements)
- Review student submissions with workspace snapshots and AI contribution forensics
- Join or observe student workspaces in real-time for live teaching and debugging
- Start video sessions directly from within a workspace
- Monitor student activity, engagement, and AI usage patterns
- Govern which AI modes, extensions, and marketplace access are enabled per course

**Frustrations with Current State:** No visibility into AI usage, grading lacks technical context, live help requires switching to a separate video tool, no unified view of student coding activity.

### Administrator
**Context:** Technical or academic IT administrator responsible for the VisionLab deployment.

**Primary Jobs-to-Be-Done:**
- Manage user accounts, roles, and account status at scale
- Control extension policy, marketplace access, and workspace resource quotas
- Review audit logs for security events and governance decisions
- Monitor system health, queue status, and production infrastructure
- Configure production readiness: CI/CD, backups, health checks, runbooks

**Frustrations with Current State:** Governance requires access to multiple disconnected systems, no single view of AI usage or workspace resource consumption, no audit trail for tool usage decisions.

---

## Product Goals

| ID | Goal | Success Signal | Phase |
|---|---|---|---|
| G-01 | Full academic workflow | Courses, assignments, submissions, grading, announcements operate end-to-end | Phase 2 |
| G-02 | Secure browser IDE | Authorized users launch workspaces; unauthorized users receive a clean 403 | Phase 3 |
| G-03 | Governed extension ecosystem | Required extensions are immutable; VisionLab Agent passes full build report | Phase 4 |
| G-04 | Real-time collaboration | Presence, cursor sync, document sync, chat work reliably with reconnect resilience | Phase 5 |
| G-05 | Responsible AI assistance | AI chat, plan, patch, approve, rollback flow works; all file mutations require approval | Phase 6 |
| G-06 | Live teaching sessions | Video rooms are authorized, tokenized, and embedded in workspace experience | Phase 7 |
| G-07 | Admin governance | Users, quotas, extensions, workspaces, audit logs available from real admin screens | Phase 8 |
| G-08 | Learning analytics | Analytics, VisionGuard, streaks, badges use real events, not fake data | Phase 9 |
| G-09 | Student deployment | Eligible projects deploy through governed async flow with status history | Phase 9 |
| G-10 | PWA installation | Manifest valid, offline fallback honest, push notifications functional | Phase 10 |
| G-11 | Production maturity | CI/CD, health checks, backups, runbooks, security matrix in place | Phase 12 |

---

## Non-Goals for Version 1

| Non-Goal | Reason |
|---|---|
| Offline IDE editing | code-server requires live container connectivity — claiming offline IDE capability would be dishonest |
| Unrestricted AI file writes | Human-in-the-loop approval is a core product principle, not a feature toggle |
| Public unauthenticated workspaces | Security architecture requires authenticated proxy routing for all code-server containers |
| Microsoft VS Code Marketplace dependency | code-server uses its own extension gallery infrastructure; marketplace access is policy-controlled |
| Student mutation of global extension policy | Policy authority belongs to admin/instructor roles; student override capability is explicitly excluded |
| Automatic upstream agent extension updates | After the initial VisionLab-controlled fork, production installs are VisionLab-owned artifacts |
| Multi-tenant SaaS (multi-institution in one deployment) | V1 targets single-institution deployment; multi-tenant architecture is a future scope item |

---

## Feature Epics

| Epic ID | Name | Core Deliverables | Priority |
|---|---|---|---|
| EP-01 | Foundation & Identity | Laravel 11 baseline, 25-table schema, RBAC, Sanctum token abilities, component library, dark design system, landing page | P0 |
| EP-02 | Classroom System | Course CRUD, 3 enrollment methods, assignment lifecycle, submissions, grading, bulk grade, grade export, announcements, dashboards | P0 |
| EP-03 | Workspace IDE | CodeServerManager, security-hardened Docker, quota resolution, file sandbox API, full-screen IDE shell, synchronized `/healthz` preloader, workspace templates, and Simple Browser Web Previews | P0 |
| EP-04 | Extension Governance | Registry with checksums, VisionLab Agent full source build pipeline, immutable Docker image, marketplace dual-layer control, live sync jobs | P0 |
| EP-05 | Real-Time Collaboration | Reverb channels (presence + private), all 9 events, TypeScript collab extension (5 modules), Blade presence integration | P0 |
| EP-06 | AI Agent | SSE streaming backend, 3 modes, 4 tools, safety filters, token budgets, cost tracking, OpenAI proxy, execute-plan bridge, patch reviewer extension, memory file, artifacts | P0 |
| EP-07 | Video Sessions | Jitsi provider abstraction, JWT tokens, room lifecycle, attendance tracking, VideoPanel in extension, meeting notes generation | P1 |
| EP-08 | Admin Operations | Admin shell, live dashboard, user management (GDPR export, impersonation), workspace oversight, extension CRUD, quota management, audit log viewer, feature flags, system config, maintenance mode, webhooks | P0 |
| EP-09 | Analytics & Growth | Event taxonomy (20+ types), 7 Chart.js dashboards, VisionGuard keystroke forensics, 365-day heatmap, streaks, 10 badges | P1 |
| EP-10 | Student Deployment | Provider abstraction (Vercel/Railway), queued deployment job, package exclusions, real-time status, deployment history dashboard | P1 |
| EP-11 | PWA & Notifications | Workbox 7 manifest + icons, 5-strategy service worker, install prompt + update banner, VAPID push (3 notification classes), Background Sync | P1 |
| EP-12 | Production Readiness | docker-compose.prod.yml (8 services), Nginx TLS 1.3, GitHub Actions CI/CD (OIDC, 3-stage), health endpoint, structured logging, monitoring, RUNBOOK.md, security verification script | P0 |

---

## Product Requirements

### PRD-001: Role-Based Product Experience
VisionLab must provide fully differentiated student, instructor, and administrator experiences with role-aware navigation, dashboards, actions, data visibility, and authorization.

**Acceptance:**
- Student sees enrolled courses and own submissions only
- Instructor sees owned courses, course submissions, and grading queue
- Administrator sees platform operations and governance screens
- Unauthorized access returns a clean denial state with role-appropriate redirect
- Suspended accounts are ejected on next HTTP request, not only at login

### PRD-002: Complete Classroom Workflow
The platform must support the full course lifecycle with zero incomplete states.

**Acceptance:**
- Instructor creates course → student joins by code → assignment created → student opens workspace → student submits (snapshot archived) → instructor grades → student views feedback
- Announcements appear in course stream with real-time broadcast readiness
- Gradebook shows live pivot table with color-coded grades and CSV export
- All notification classes dispatch to correct channels

### PRD-003: Secure Workspace IDE
Each eligible assignment opens a security-hardened code-server workspace.

**Acceptance:**
- Workspace lifecycle (start, stop, restart, health-check, stale-cleanup, SIGABRT/OOM recovery) is persisted and auditable
- Docker run command includes ALL mandatory security flags (verified by docker inspect)
- File APIs block path traversal and all defined sensitive paths (verified by negative test suite)
- The IDE occupies the full container screen with no external wrapping UI (no standalone JS file explorers)
- The workspace supports Simple Browser Web Previews for full-stack applications
- Environment dependency mapping strictly relies on Nix `dev.nix` (forbidden `apt` commands)

### PRD-004: Extension Governance
Extension delivery is reproducible, integrity-verified, policy-enforced, and auditable.

**Acceptance:**
- SHA256 checksum verification runs before every container installation
- Required extensions are immutable (chmod 555 global directory verified by docker exec test)
- Marketplace disabled by two simultaneous controls when allow_marketplace=false
- Extension policy changes produce audit_logs records
- All extension changes propagate to active containers via SyncWorkspaceExtensions job

### PRD-005: Independent VisionLab Agent
The VisionLab Agent is a fully VisionLab-controlled extension artifact.

**Acceptance:**
- Source audit report documents all files containing upstream identity strings
- Every identified file updated at source level (not binary/metadata-only)
- Old-identity scan on compiled archive shows zero upstream name matches
- Clean compile from VisionLab-controlled fork confirmed
- SHA256 registered in extension_builds; code-server smoke test passed
- All legally required license notices preserved
- No production install depends on upstream Continue registry or distribution

### PRD-006: Responsible AI Patch Workflow
AI assistance is powerful, transparent, and bounded by human approval.

**Acceptance:**
- CHAT mode: no files written, no mutations in ai_actions_log of type write
- PLAN mode: no mutations, plan terminates with implementation command link
- AGENT mode: only ai_pending_patches records created, no direct file writes
- Human approval required for every file mutation (tested by negative test)
- ai_snapshots record created before every approved patch application
- Rollback restores file from snapshot correctly
- Token budget enforces per-role limits (tested by over-limit negative test)
- Safety filters reject blocked code patterns with HTTP 422

### PRD-007: Real-Time Collaboration
Authorized participants collaborate through a resilient, low-latency system.

**Acceptance:**
- Unauthorized users cannot subscribe to workspace channels
- Two authorized participants see accurate presence, cursor decorations, and chat
- Document sync prevents echo loops and surfaces conflicts
- Connection loss shows reconnecting banner; reconnect triggers state re-sync
- Stale heartbeat cleanup correctly marks offline users after 30 minutes

### PRD-008: Video Sessions
Authorized users start and join workspace-linked video sessions.

**Acceptance:**
- JWTs are generated server-side only; never in client code
- Workspace collaborators can join; unauthorized users are denied
- Instructors/admins can end managed sessions
- VideoCallStarted event broadcasts to all workspace presence channel members
- Provider misconfiguration produces a clear failure state

### PRD-009: Analytics, VisionGuard & Gamification
Platform analytics use real events and respect role-based visibility.

**Acceptance:**
- All analytics dashboards are role-restricted (tested by negative access tests)
- VisionGuard shows human and AI attribution percentages with confidence indicator
- Contribution heatmap uses real analytics_events data
- Streaks are calculated from real events by the daily scheduled command
- All 10 badges derive from real platform triggers, not synthetic data

### PRD-010: Student Project Deployment
Students deploy eligible projects through a governed async flow.

**Acceptance:**
- Public-exposure confirmation gate prevents accidental deployment
- Deployment ZIP excludes .env, .git, vendor, node_modules, and all defined blocked paths
- Vercel and Railway providers implement the same DeploymentProvider interface
- Deployment status updates broadcast in real-time via Reverb
- Both success and failure states notify the student and update the dashboard

### PRD-011: PWA & Push Notifications
VisionLab is installable with honest offline behavior.

**Acceptance:**
- Manifest passes browser installability check
- IDE and API routes are confirmed NetworkOnly by browser application panel inspection
- Offline fallback page shows without pretending IDE functionality
- Push subscribe/unsubscribe stores correctly in push_subscriptions
- Background Sync queues and replays offline submission attempts

### PRD-012: Production Readiness
The product includes complete production infrastructure and operational evidence.

**Acceptance:**
- GitHub Actions CI/CD blocks deployment on failed tests or failed build
- Health endpoint returns correct status for all 5 dependencies
- Backup and restore procedure documented and rehearsed
- Nginx TLS achieves SSL Labs A+ rating with all security headers present
- Security verification script passes all 7 automated checks

---

## Capability Map

| Capability | Must-Have in V1 | Should-Have in V1 | Could-Have Future |
|---|---|---|---|
| Identity & Access | Registration, login, role redirects, suspended accounts, final-admin protection, policy-backed all actions, Sanctum token abilities | — | SSO/SAML, OAuth providers |
| Classroom | Course CRUD, 3 enrollment methods, assignment lifecycle, submission snapshots, grading, announcements, role dashboards | Bulk grading, grade export, CSV enrollment, course duplication | LTI integration, rubric grading |
| Workspace | CodeServerManager, quota resolution, security-hardened Docker, file sandbox, IDE shell | xterm terminal, workspace templates | Workspace sharing with external collaborators |
| Extensions | Registry, checksums, VisionLab Agent full build pipeline, immutable layer, marketplace control | Optional Tier 3 extension management, extension sync jobs | Extension versioning UI |
| AI | Chat, plan, patch, approve, reject, rollback, snapshots, token budget, cost tracking, proxy | Memory file, artifact gallery, plan execution bridge | Multiple AI provider support |
| Collaboration | Reverb channels, presence, cursor, document sync, chat, reconnect | Color-coded cursors, collaboration API endpoints | Operational transform for conflict resolution |
| Video | Room lifecycle, JWT tokens, collaborator join, instructor end | Attendance tracking, meeting notes AI generation | Recording capability |
| Admin | User management, workspace oversight, extension CRUD, quota management | Audit log viewer, feature flags, system config, maintenance mode | Role permission matrix editor |
| Analytics | Event taxonomy, role-restricted dashboards | VisionGuard forensics, heatmap, streaks, badges | Custom report builder |
| Deployment | Provider abstraction, packaging, status polling | Real-time deployment status via Reverb | Additional providers (Render, Fly.io) |
| PWA | Manifest, service worker, offline fallback | Install prompt, push notifications, Background Sync | Share Target API, Periodic Background Sync |
| Production | Docker Compose, Nginx TLS, GitHub Actions CI/CD, health endpoint | Structured logging, monitoring alerts, runbooks | Kubernetes orchestration |

---

## Release Priority

| Priority | Scope |
|---|---|
| P0 — Blocking | Auth, RBAC, classroom workflows, workspace IDE with file sandbox, extension governance with VisionLab Agent build, AI patch workflow, admin operations, production health, CI/CD |
| P1 — High Value | Collaboration, VisionGuard analytics, video sessions, notification system, student deployment, PWA, performance hardening, full test suite |
| P2 — Enhancement | Advanced gamification filters, additional deployment providers, multi-provider AI, deeper LMS integrations |

---

## UX Requirements

| Area | Requirement |
|---|---|
| Design System | Consistent dark theme (Strict #0a0a0a base) with accessible contrast, vibrant glassmorphism surfaces, and brand-violet/cyan/orange accents across all screens |
| States | Every screen implements: success, loading (skeleton loaders), empty (x-empty-state), unauthorized, validation error (field-level with icon), server error (safe message, correlation_id) |
| Responsiveness | Fully functional from 320px to 2560px viewport width |
| Accessibility | WCAG 2.1 AA: keyboard navigation, visible focus rings, semantic HTML, associated labels, 4.5:1 contrast ratio |
| Motion | All animations respect prefers-reduced-motion; users with OS reduced motion receive a fully functional static experience |
| Tone | Clear, professional language for all risk-carrying actions: deployment confirmation, account suspension, AI patch approval, extension policy changes |
| Error Pages | Custom dark-themed error pages for 403, 404, 419, 429, 500, 503 — no default Laravel or PHP error output ever visible |

---

## Key Product Risks & Mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| code-server container exposure | Critical | Authenticated proxy, scoped token, network isolation, read-only root, no Docker socket exposure |
| AI excessive agency | Critical | Mode matrix, tool policy, human-approved patches, safety filters, token budget, 20-patch safety limit |
| Extension supply-chain risk | High | Source builds from VisionLab fork, SHA256 verification, registry, old-identity scan, license review |
| Service worker caching sensitive content | High | NetworkOnly for /api/* and /workspace/*, verified by browser Application panel |
| Deployment leaking secrets | High | Package exclusion list in config/deployment.php, .env blocked, dependency dirs excluded |
| Legal exposure from imported extension code | High | License review before import, attribution preservation, VisionLab-controlled fork |
| Token budget bypass | Medium | Redis-cached per-role budget enforced before every AI request, tested by over-limit negative test |
| Stale containers consuming server resources | Medium | 30-minute heartbeat cleanup job, daily WorkspaceCleanup command |

---

## Product Metrics

| Metric | Target |
|---|---|
| End-to-end workflow completion | Completed without code changes or emergency database edits on live screens |
| Workspace launch reliability | Containers start or recover with truthful status in configured environment |
| AI write path approval rate | 100% of file mutations require stored patch record and human approval |
| Unauthorized route denial | 100% of protected routes return controlled denial for unauthorized roles |
| Critical workflow test coverage | All Phase acceptance scenarios covered by automated tests |
| PWA installability | Manifest and service worker pass browser application panel checks |
| Production preflight | All health checks pass before any evaluation or production access |
| Accessibility compliance | No blocking keyboard, label, contrast, or responsive-layout failures in core workflows |
| RTM completeness | Every must-have BR and FR maps to HLD component, implementation, and test evidence |
| Release evidence completeness | CI result, migration status, test summary, security matrix, extension checksums, health report all present |

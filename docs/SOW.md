# VisionLab Statement of Work
## Version 9.0 — Production-Grade Enterprise Edition

---

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Statement of Work (SOW) |
| Version | 9.0 |
| Prompt Pack | `PROMPTS.xml` v9.0 |
| Standard | PMI PMBOK — Statement of Work; IEEE 29148:2018 |
| Audience | Product Owner, Engineering Leads, Delivery, QA, Legal |

---

## SOW Purpose

This Statement of Work defines the complete scope, deliverables, work packages, acceptance criteria, responsibilities, exclusions, constraints, and change-control process for implementing VisionLab. It makes scope and acceptance explicit and auditable so that every party understands what is being built, to what standard, and how completion is verified.

---

## Project Objectives

| Objective | Measurable Outcome |
|---|---|
| Deliver a production-ready Laravel 11 academic coding platform | Application deploys, runs, and serves real workflows without code changes during evaluation |
| Implement full classroom, workspace, collaboration, AI, video, analytics, deployment, and PWA capabilities | All Must-priority functional requirements pass corresponding test scenarios |
| Provide administrator governance over users, workspaces, extensions, quotas, audit logs, and health | Admin evaluation path completes without workarounds |
| Prove production maturity through evidence | CI/CD, health checks, backup restore, security matrix, and runbooks are all present and verified |
| Maintain legal compliance for imported open-source code | VisionLab Agent build report confirms source reference, license review, attribution preservation, and artifact history |

---

## Scope of Work

### In Scope

| WBS | Work Package | Key Outputs |
|---|---|---|
| 1.0 | Foundation and Identity | Laravel 11 baseline, 25-entity schema, RBAC, Sanctum token abilities, design system, Blade component library, auth flows, landing page, threat model |
| 2.0 | Classroom Domain | Courses (3 enrollment methods, CSV import, duplication), assignments (draft/publish lifecycle, workspace templates), submissions (snapshots, late detection), grading (bulk, export), announcements (Markdown + HTMLPurifier), 7 notification classes, role dashboards |
| 3.0 | Workspace IDE | CodeServerManager (security-hardened Docker, 5-tier quota), 6-endpoint file sandbox API (realpath enforcement), 5-zone IDE shell, JS file explorer (dependency-free), xterm.js terminal, 5 workspace templates, stale cleanup scheduler |
| 4.0 | Extension Governance | Extension registry with SHA256, VisionLab Agent full source build pipeline (7-step), dual-strategy artifact pipeline, immutable Docker image (chmod 555 global directory), dual-layer marketplace control, live SyncWorkspaceExtensions job |
| 5.0 | Real-Time Collaboration | Reverb channel authorization (presence + private), 9 broadcast events with stable payload schemas, complete TypeScript VisionLab-collab extension (5 modules), Blade presence integration, reconnect resilience, stale heartbeat cleanup |
| 6.0 | AI Agent and Patch Review | SSE streaming AiController, 3-mode AiService (CHAT/PLAN/AGENT), 4 sandboxed tools, safety content filters, token budget enforcement, cost tracking, OpenAI-compatible proxy, container config injection, execute-plan bridge (20-patch safety limit), VisionLab-patch-reviewer extension (two-pane diff viewer, queue management), AI memory file, AI artifact gallery |
| 7.0 | Video Sessions | Jitsi provider abstraction (JaaS + self-hosted), server-side JWT generation (moderator/attendee tiers), VideoRoomController (4 endpoints), attendance_logs tracking, complete VideoPanel in extension (dark Jitsi embed, status bar, meeting notes AI generation), Blade video button with real-time state |
| 8.0 | Admin Operations | Admin shell layout, live dashboard (queue health, AI cost monitoring), AdminUserController (GDPR export, impersonation, final-admin protection), workspace oversight (resource monitoring, force-stop), extension CRUD (rebuildHash), quota management, audit log viewer (before/after JSON diff), feature flags panel, system configuration panel, maintenance mode, webhook management |
| 9.0 | Analytics, Forensics, Gamification, Deployment | Analytics event taxonomy (20+ types via Model Observers), 7 Chart.js admin dashboards, Analytics Dashboard, 365-day contribution heatmap, daily streak tracking, 10 achievement badges, student deployment (Vercel/Railway provider abstraction, package exclusions, real-time status) |
| 10.0 | PWA and Notifications | Web app manifest (app shortcuts, screenshots), Workbox 7 service worker (5 route strategies, cache versioning), offline fallback page, install prompt + update banner, VAPID push (3 notification classes, scheduled reminders), Basic Caching |
| 11.0 | Security, Testing, Performance | OWASP ASVS Level 2 mapping, security verification script (7 automated checks), full PHPUnit feature test suite (8 test classes), accessibility audit (WCAG 2.1 AA), Redis cache optimization, N+1 resolution, DB index optimization, OpenAPI 3.1 specification (Swagger UI at /api/docs), performance benchmarks, 6 custom error pages |
| 12.0 | Production Deployment and Observability | docker-compose.prod.yml (8 services, security-hardened), multi-stage Dockerfile, Nginx TLS 1.3 (SSL Labs A+, all security headers, WebSocket proxy, SSE no-buffer, rate limiting), GitHub Actions CI/CD (OIDC, 3-stage), health endpoint (5 dependency probes), structured logging, UptimeRobot monitoring setup, RUNBOOK.md, backup/restore scripts, preflight checklist |

### Explicitly Out of Scope

| Item | Rationale | Disposition |
|---|---|---|
| Offline IDE editing | code-server requires live container connectivity; claiming offline IDE capability is dishonest | Document as known limitation |
| Unrestricted AI file mutation | Human-in-the-loop approval is a core product principle | Enforce via ai_pending_patches lifecycle |
| Public unauthenticated workspaces | Authenticated proxy is required by the security architecture | No anonymous workspace route will be added |
| Microsoft VS Code Marketplace as primary extension source | code-server uses its own gallery infrastructure; marketplace access is policy-controlled | Extension delivery uses VisionLab artifact registry |
| Student mutation of global extension policy | Policy authority belongs to admin/instructor roles | No student-facing extension management UI |
| Automatic upstream agent extension updates after source import | VisionLab-controlled fork releases prevent supply-chain risk | Updates managed as VisionLab releases |
| Multi-tenant SaaS (multiple institutions per deployment) | V1 targets single-institution deployment | Future scope item with architecture decision required |
| Production support beyond documented runbooks | Out of project scope | Separately contracted operational support if needed |

---

## Deliverables

| ID | Deliverable | Contents | Acceptance Criteria |
|---|---|---|---|
| D-01 | Foundation | Laravel 11 app, 25-entity schema, RBAC, design system, component library, auth flows, landing page | Auth tests pass; role denial tests pass; Lighthouse ≥90 |
| D-02 | Classroom | Courses, enrollments, assignments, submissions, grading, announcements, notifications, dashboards | End-to-end instructor-student workflow passes |
| D-03 | Workspace IDE | CodeServerManager, Docker lifecycle, file sandbox API, IDE shell, file explorer, terminal | Security matrix (6 path traversal tests) all deny; workspace tests pass |
| D-04 | Extension Governance | Registry with checksums, immutable Docker image, marketplace control, optional extension management | Required extensions immutable in running container (docker exec denial confirmed) |
| D-05 | VisionLab Agent | Independent fork, full source audit, complete source edit, clean compile, zero old-identity matches, SHA256 registration, smoke test | Build report accepted (all 7 steps evidenced); license notices preserved |
| D-06 | Real-Time Collaboration | Reverb channels, collab extension (5 modules), Blade presence, reconnect resilience | Two-user collaboration simulation passes; channel denial confirmed |
| D-07 | AI Patch Workflow | 3-mode AI, 4 sandboxed tools, safety filters, human approval gate, snapshots, rollback, token budgets | All AI mode tests pass; safety filter rejection confirmed; no direct file write possible |
| D-08 | Video Sessions | Jitsi provider abstraction, server-side JWTs, room lifecycle, attendance, VideoPanel | Authorized join and end pass; unauthorized join denied; JWT never in client code |
| D-09 | Admin Operations | User/workspace/extension/quota management, audit log viewer, feature flags, system config, webhooks | Admin evaluation path complete from live screens; audit records confirmed |
| D-10 | Analytics and Forensics | Event taxonomy, dashboards, Analytics Dashboard, heatmap, streaks, badges | Role-restricted analytics tests pass; Analytics Dashboard display confirmed |
| D-11 | Student Deployment | Provider abstraction, packaging, status tracking, real-time Reverb status | Deployment lifecycle passes; package exclusions verified; provider failure state honest |
| D-12 | PWA | Manifest, Workbox service worker, offline fallback, push notifications, Background Sync | Browser Application panel checks pass; /api/* and /workspace/* confirmed NetworkOnly |
| D-13 | Security and Quality | ASVS matrix, security script, full test suite, accessibility, performance benchmarks, OpenAPI 3.1 | Security verification script 7/7 checks pass; all PHPUnit tests pass; WCAG 2.1 AA |
| D-14 | Production Infrastructure | Docker Compose, Nginx TLS, GitHub Actions CI/CD, health endpoint, backups, monitoring, runbooks | SSL Labs A+; CI pipeline gate confirmed; health endpoint HTTP 200; backup restore rehearsed |

---

## Milestones

| ID | Milestone | Phase | Completion Criteria |
|---|---|---|---|
| M-01 | Foundation Accepted | 1 | Auth tests pass, schema migrated, design system active, threat model produced |
| M-02 | Classroom Accepted | 2 | End-to-end classroom workflow confirmed, all 7 notification classes operational |
| M-03 | Workspace IDE Accepted | 3 | Security matrix all deny, workspace lifecycle operational, Docker flags verified |
| M-04 | Extension Governance Accepted | 4 | VisionLab Agent build report accepted, immutable layer confirmed, checksum verification active |
| M-05 | Collaboration Accepted | 5 | Two-user simulation passes, channel denial confirmed, reconnect resilience verified |
| M-06 | AI Workflow Accepted | 6 | All AI mode tests pass, safety filters confirmed, patch lifecycle complete, token budget enforced |
| M-07 | Video Accepted | 7 | Authorized join/end pass, JWT server-side confirmed, provider failure honest |
| M-08 | Admin Operations Accepted | 8 | Admin evaluation path complete, audit logs confirmed, all governance screens functional |
| M-09 | Analytics and Deployment Accepted | 9 | Role-restricted analytics confirmed, Analytics Dashboard display correct, deployment lifecycle passes |
| M-10 | PWA Accepted | 10 | Browser panel checks pass, /api/* and /workspace/* NetworkOnly confirmed, push subscriptions stored |
| M-11 | Security and Quality Accepted | 11 | Security script 7/7 pass, PHPUnit suite pass, WCAG 2.1 AA confirmed, benchmarks met |
| M-12 | Production Release Accepted | 12 | SSL Labs A+, CI gate confirmed, health HTTP 200, backup restore rehearsed, RUNBOOK.md actionable |

---

## Quality Gates

| Gate | Conditions Required to Pass |
|---|---|
| Phase Gate | Phase scope implemented, all tests pass, security denial paths confirmed, browser checks complete, evidence package attached, next dependency confirmed ready |
| Security Gate | Authorization denial tests pass for the phase's protected surfaces; audit events confirmed in audit_logs; file sandbox denials confirmed where applicable; rate limits verified |
| Extension Gate | Source audit complete, all identified files edited at source level, old-identity scan zero matches, clean compile from VisionLab fork, SHA256 registered, code-server smoke test passed, license notices preserved |
| AI Safety Gate | CHAT/PLAN/AGENT mode tests pass; no direct write possible without pending patch; safety filters reject all blocked patterns; token budget enforces per-role limits; no provider secrets in client responses |
| Container Security Gate | docker inspect confirms all mandatory security flags; UID 1000 confirmed; global extension directory chmod 555 confirmed by docker exec write denial; workspace network isolation verified |
| Release Gate | CI pipeline green on main branch; migrations verified on production database; health endpoint HTTP 200 for all 5 dependencies; backup restore rehearsed and documented; rollback procedure documented; security verification script 7/7 pass |
| Evaluation Gate | Evaluation accounts prepared and tested; reset process documented and rehearsed; all critical workflows run end-to-end on target environment; known risks documented with contingency steps |

---

## Roles and Responsibilities

| Role | Primary Responsibilities | Decision Rights |
|---|---|---|
| Product Owner | Scope decisions, acceptance sign-off, requirement prioritization, change approval | Approve scope changes, accept phases, defer requirements |
| Laravel Engineer | Backend: migrations, controllers, policies, FormRequests, jobs, services, API responses | Technical implementation decisions within approved architecture |
| Frontend Engineer | Blade views, Tailwind design system, vanilla JS modules, accessibility, PWA | UI/UX decisions within design system constraints |
| Extension Engineer | TypeScript extensions (collab, patch-reviewer), VisionLab Agent build pipeline, extension delivery | Extension architecture and artifact pipeline decisions |
| DevOps Engineer | Docker topology, Nginx, CI/CD, health checks, backups, monitoring, runbooks | Infrastructure and deployment decisions |
| QA Engineer | Test plan, automated test suite, UAT execution, security denial testing, accessibility checks | Test scope and defect severity classification |
| Security Engineer | ASVS matrix, threat model, AI sandbox design, container hardening, extension provenance review | Security control decisions, risk acceptance recommendations |
| Legal Reviewer | License review for imported open-source code, attribution preservation verification | License compliance decision on imported code |

---

## Assumptions

| Assumption | Impact if False |
|---|---|
| PHP 8.3+, MySQL 8.0+, Redis 7+, Docker Desktop, Node.js 20 LTS available in development environment | Phase 3 workspace delivery blocked |
| Docker-compatible runtime available on production VPS (Ubuntu 22.04+) | Phase 12 production deployment blocked |
| AI provider (Anthropic) API key configured through secure environment variables | Phase 6 AI agent delivery blocked |
| Jitsi or JaaS video provider configurable through environment variables | Phase 7 video delivery blocked |
| VAPID keys generated and configured through environment variables | Phase 10 push notifications blocked |
| Deployment provider API keys (Vercel or Railway) configurable through environment variables | Phase 9 student deployment blocked |
| Evaluation data (accounts, courses, assignments) is entirely separate from production behavior | Evaluation environment integrity compromised |
| Required open-source licenses (Continue extension) permit modification and redistribution | Phase 4 VisionLab Agent build blocked pending legal review |
| VPS has a domain name and DNS configured before Phase 12 TLS setup | Phase 12 SSL certificate issuance blocked |

---

## Dependencies

| Dependency | Required By | If Unavailable |
|---|---|---|
| PHP 8.3 + extensions | All phases | Development blocked |
| MySQL 8.0+ | All phases | Data layer blocked |
| Redis 7+ | Phase 1 (sessions, cache, queues) | Session and queue delivery blocked |
| Docker Desktop with non-root container support | Phase 3 | Workspace delivery blocked; development must document alternate test path |
| Laravel Reverb server | Phase 5 | Collaboration delivery blocked |
| Anthropic API key | Phase 6 | AI delivery blocked; development can proceed with mock provider mode |
| Jitsi JaaS account or self-hosted Docker Compose Jitsi | Phase 7 | Video delivery blocked; VideoPanel stub only |
| VAPID keypair | Phase 10 | Push notifications blocked; PWA manifest and service worker still deliverable |
| Vercel or Railway API key | Phase 9 | Deployment delivery blocked; deployment UI still buildable with mock provider |
| Production VPS with domain and DNS | Phase 12 | TLS and health endpoint delivery blocked |
| GitHub repository and Actions access | Phase 12 | CI/CD delivery blocked |

---

## Constraints

| Constraint | Category | Flexibility |
|---|---|---|
| Laravel 11 and MySQL 8 are fixed baseline technologies | Technology | None — architecture decisions are made for these versions |
| code-server workspaces require Docker container support | Infrastructure | None — architectural dependency |
| VisionLab Agent must be source-built from VisionLab-controlled fork | Legal/Security | None — required for supply-chain integrity and license compliance |
| AI provider secrets must remain server-side at all times | Security | None — OWASP LLM Top 10 compliance |
| No placeholder screens, stub methods, or TODO comments in production deliverables | Quality | None — production mandate |
| All imported open-source license notices must be preserved | Legal | None — legal obligation |
| Service workers must treat /api/* and /workspace/* as NetworkOnly | Security/PWA | None — prevents stale auth caching |

---

## Acceptance Process

Each phase deliverable is accepted when ALL of the following are provided and verified:

| Evidence Type | Required Content |
|---|---|
| Implementation evidence | Feature test results, browser check results, migration status, build output |
| Security denial evidence | Negative test results for all security-sensitive actions in the phase |
| Risk documentation | Known risks with severity, likelihood, and mitigation for the current phase |
| Operational notes | Any environment prerequisites, configuration requirements, or operator actions needed for the next phase |
| Next-phase readiness | Explicit confirmation that the dependency contract for the next phase is satisfied |

---

## Change Control Process

Changes that affect any of the following require written Product Owner approval and a documented impact analysis before implementation:

- Business or functional requirements scope
- Security controls or ASVS coverage decisions
- Public API contracts or database schema (breaking or additive)
- External provider selection (AI, video, deployment, push)
- Release schedule or phase dependency sequence
- Legal or license compliance decisions
- Extension artifact pipeline or VisionLab Agent build process

Impact analysis must cover: scope impact, schedule impact, security impact, test impact, RTM update required, and documentation update required.

Minor changes (bug fixes, UI polish, non-breaking API additions, test additions) may proceed with engineering lead approval and must be documented in the phase evidence package.

---

## Definition of Completion

The project is complete when ALL of the following are simultaneously true:

1. All Must-priority functional requirements (FR) in the FRD are implemented and traced to passing tests in the RTM
2. All 14 deliverables (D-01 through D-14) have been accepted by the Product Owner
3. The VisionLab Agent build report is accepted: 7-step pipeline evidenced, old-identity scan zero matches confirmed, legal notices preserved
4. The security verification script passes all 7 automated checks without manual exception
5. The production health endpoint returns HTTP 200 for all 5 configured dependencies
6. A backup restore rehearsal has been successfully completed and documented
7. The GitHub Actions CI/CD pipeline gates deployment on passing tests and passing build
8. The evaluation workflow runs end-to-end on live application screens for all three roles without code changes, emergency database edits, or workarounds
9. The RUNBOOK.md is actionable by any engineer without access to the original implementation team
10. All legal provenance requirements for imported open-source extension code are documented and preserved

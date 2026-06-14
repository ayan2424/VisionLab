# VisionLab Product Requirements Document

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Product Requirements Document |
| Version | 1.0 |
| Source Prompt | `PROMPTS.xml` version `2026.06-competitive-ultimate-independent-agent-fork` |
| Audience | Product owner, engineering, QA, security, DevOps, evaluators |
| Status | Implementation-ready baseline |

## Research Basis

This PRD is informed by the final VisionLab prompt pack and current primary references:

- Laravel 11 release capabilities: Reverb, streamlined app structure, health routing, per-second rate limiting, and queue testing improvements. Reference: [Laravel 11 Release Notes](https://laravel.com/docs/11.x/releases)
- Requirements quality and traceability discipline. Reference: [IEEE 29148](https://standards.ieee.org/standard/29148-2018.html)
- Requirements life-cycle traceability. Reference: [IIBA BABOK - Trace Requirements](https://www.iiba.org/knowledgehub/business-analysis-body-of-knowledge-babok-guide/5-requirements-life-cycle-management/5-1-trace-requirements/)
- Security verification model. Reference: [OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/)
- LLM application risks. Reference: [OWASP LLM Top 10](https://owasp.org/www-project-top-10-for-large-language-model-applications/)
- code-server marketplace, extension, proxy, and operational constraints. Reference: [code-server FAQ](https://coder.com/docs/code-server/FAQ)
- PWA service-worker behavior and lifecycle. Reference: [web.dev Service Workers](https://web.dev/learn/pwa/service-workers)
- Container security discipline. Reference: [OWASP Docker Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Docker_Security_Cheat_Sheet.html)

## Product Overview

VisionLab is a production-ready collaborative coding and learning platform for universities. It combines a learning management system, secure browser-based VS Code workspaces, real-time multiplayer coding, responsible AI assistance, video sessions, analytics, notifications, PWA capabilities, project deployment, and production operations into one governed product.

VisionLab is designed for serious academic use. It must feel polished enough for competition evaluation while remaining technically defensible for production deployment.

## Product Positioning

VisionLab replaces fragmented workflows that currently require separate classroom, meeting, coding, and AI tools. It gives universities a single governed platform where:

- Students learn, code, collaborate, use AI responsibly, submit assignments, and track progress.
- Instructors manage courses, assignments, grading, live collaboration, AI transparency, and student outcomes.
- Administrators govern users, workspaces, extensions, quotas, marketplace access, audit logs, security posture, and production health.

## Competitive Differentiators

| Differentiator | Product Meaning | Evidence Required |
|---|---|---|
| Governed browser IDE | Each assignment can open in an authenticated code-server workspace with quotas, file controls, and lifecycle state | Workspace launch, file API, quota, and access-denial tests |
| Responsible AI agent | AI can explain, plan, and propose patches without silent file mutation | Patch approval UI, snapshots, audit logs, and denial tests |
| Independent VisionLab Agent | The agent extension is built, branded, governed, and released as a VisionLab-controlled artifact after the compliant source import | Source audit, clean rebuild, checksum, install smoke test, license review |
| Real-time learning lab | Students and instructors collaborate through presence, cursor sync, document events, chat, and video sessions | Two-user browser verification and channel authorization tests |
| Instructor-visible AI transparency | VisionGuard separates human, AI-approved, pasted/imported, starter, and system-generated contribution signals where detectable | Forensics tab, confidence display, and analytics aggregation |
| Admin governance | Administrators control roles, quotas, workspaces, extensions, marketplace access, audit logs, and health status | Admin screens, policy tests, and audit event coverage |
| Honest PWA behavior | Offline support improves navigation but never pretends the IDE is available without network and container access | Service worker route rules and offline UI tests |
| Production evidence | CI, health checks, backups, release manifest, monitoring, and runbooks are part of the product delivery, not afterthoughts | Preflight checklist and release evidence package |

## User Personas

### Student

Needs a reliable learning environment where assignments, code, help, collaboration, feedback, and deployment all live together.

Primary jobs:
- Join courses and view assignments.
- Start a workspace and write code.
- Collaborate with classmates and instructors.
- Use AI in a transparent, controlled way.
- Submit work and receive grades.
- Track activity, streaks, badges, and deployments.

### Instructor

Needs classroom control, assignment workflows, real-time teaching tools, grading context, and AI transparency.

Primary jobs:
- Create courses, assignments, and announcements.
- Review submissions and grade work.
- Join or observe workspaces.
- Start video sessions.
- Review VisionGuard forensics.
- Monitor student progress and engagement.

### Administrator

Needs platform-level governance, operations, security, and auditability.

Primary jobs:
- Manage users and roles.
- Govern extension and marketplace access.
- Manage workspace quotas and lifecycle.
- Review audit logs and system health.
- Configure production readiness and security.

### Evaluator

Needs to see a complete, credible, live product flow without relying on artificial screens.

Primary jobs:
- Follow a prepared evaluation path.
- Validate core workflows.
- Observe real collaboration, AI review, deployment, and operations evidence.

## Product Goals

| ID | Goal | Success Signal |
|---|---|---|
| G-01 | Provide a full academic workflow | Courses, assignments, submissions, grading, announcements, dashboards operate end to end |
| G-02 | Provide secure browser IDE workspaces | Authorized users can launch code-server workspaces; unauthorized users cannot access them |
| G-03 | Enable real-time collaboration | Presence, cursor sync, chat, and collaboration events work reliably |
| G-04 | Provide responsible AI assistance | AI can explain, plan, and propose patches but cannot mutate files without approval |
| G-05 | Govern extensions and marketplace access | Required extensions are immutable and optional tools follow admin/instructor policy |
| G-06 | Support live teaching | Video rooms are authorized, tokenized, and visible inside the workspace experience |
| G-07 | Make learning progress visible | Analytics, VisionGuard, streaks, badges, and dashboards use real events |
| G-08 | Support student deployment | Eligible projects can be deployed through a governed async workflow |
| G-09 | Support production operation | CI/CD, health checks, backups, logs, runbooks, and security verification exist |

## Non-Goals for Version 1

- No unrestricted AI writes.
- No public unauthenticated code-server containers.
- No dependency on the Microsoft VS Code Marketplace for code-server.
- No offline editing for live IDE workspaces.
- No student mutation of global extension directories, marketplace policy, workspace policy, or container startup policy.
- No automatic production dependency on upstream Continue extension releases after the initial compliant source import.

## Product Principles

- Production truth over presentation tricks.
- Human-approved mutation for AI.
- Policy-driven access everywhere.
- Security controls must be testable.
- Offline behavior must be honest.
- Extension governance must be reproducible.
- Competition readiness must come from functioning workflows.

## Feature Epics

| Epic ID | Name | Summary |
|---|---|---|
| EP-01 | Foundation and Identity | Laravel app, auth, RBAC, policies, design system, baseline schema |
| EP-02 | Classroom System | Courses, enrollments, assignments, submissions, announcements, dashboards |
| EP-03 | Workspace IDE | code-server lifecycle, file APIs, IDE shell, quota resolution, storage sandbox |
| EP-04 | Extension Governance | extension registry, source-built sensitive extensions, immutable required tools, marketplace policy |
| EP-05 | Real-Time Collaboration | Reverb channels, presence, cursor sync, document sync, chat, collaboration extension |
| EP-06 | Responsible AI Agent | Continue-derived VisionLab Agent, Laravel proxy, modes, tools, patch review, audit logs |
| EP-07 | Video Sessions | Jitsi provider abstraction, room lifecycle, tokenized access, workspace integration |
| EP-08 | Admin Operations | users, workspaces, extensions, quotas, audit logs, system health, cleanup commands |
| EP-09 | Analytics and Growth | event taxonomy, dashboards, VisionGuard, heatmaps, badges, streaks |
| EP-10 | Student Deployment | deployment provider abstraction, queued deployment jobs, package exclusions, history |
| EP-11 | PWA and Notifications | manifest, service worker, offline fallback, push notifications, install prompt |
| EP-12 | Production Readiness | tests, security hardening, performance, CI/CD, backups, observability, runbooks |

## Capability Map

| Capability Area | Must-Have Capabilities | Primary Owner |
|---|---|---|
| Identity and access | registration/login, role redirects, suspended accounts, final-admin protection, policy-backed actions | Platform |
| Classroom | course CRUD, enrollment, assignments, submissions, grading, announcements, role dashboards | Academic workflow |
| Workspace | workspace creation, container lifecycle, quota resolution, file sandbox, IDE shell, truthful runtime states | Workspace platform |
| Extension ecosystem | extension registry, immutable required extensions, marketplace policy, VisionLab Agent build pipeline | Platform governance |
| Collaboration | Reverb authorization, presence, cursor sync, document sync, chat, reconnect, conflict surfacing | Real-time platform |
| AI | chat, plan, patch proposal, approval, rejection, rollback, snapshots, token accounting, AI audit logs | Responsible AI |
| Video | room lifecycle, tokenized join, instructor controls, active-call indicators, provider failure states | Live teaching |
| Admin operations | user management, workspace operations, quotas, audit search, extension controls, health dashboard | Operations |
| Analytics | event taxonomy, dashboards, VisionGuard, heatmaps, streaks, badges, instructor insights | Learning intelligence |
| Deployment | provider abstraction, packaging, blocked path exclusions, status polling, deployment history | Student publishing |
| PWA and notification | manifest, install prompt, push subscription, notification preferences, offline fallback | Engagement |
| Production | CI/CD, Docker topology, Nginx/TLS, backups, restore rehearsal, observability, runbooks | DevOps |

## Release Scope Priority

| Priority | Scope |
|---|---|
| P0 | Auth, RBAC, classroom workflows, workspace lifecycle, file sandbox, extension governance, AI patch approval, audit logs, production health |
| P1 | Collaboration, VisionGuard, admin dashboards, video sessions, notifications, deployment, performance hardening |
| P2 | Advanced gamification, richer analytics filters, additional deployment providers, expanded provider integrations |

## Key Product Requirements

### PRD-001: Role-Based Product Experience

VisionLab must provide differentiated student, instructor, and administrator experiences. Navigation, dashboards, actions, data visibility, and authorization must be role-aware.

Acceptance:
- Student sees enrolled courses and own submissions only.
- Instructor sees owned courses and course submissions.
- Administrator sees platform operations and governance screens.
- Unauthorized access returns a controlled denial state.

### PRD-002: Classroom Workflow

The platform must support the full course lifecycle: course creation, enrollment, assignments, submission snapshots, grading, feedback, announcements, unread state, and role dashboards.

Acceptance:
- Instructor can create a course and assignment.
- Student can join by code, start assignment, submit, and view feedback.
- Instructor can grade and provide feedback.
- Announcements appear in course stream and notification flows.

### PRD-003: Secure Workspace IDE

Each eligible assignment must open a secure code-server workspace controlled by VisionLab.

Acceptance:
- Workspace lifecycle is persisted and auditable.
- File APIs block path traversal and sensitive paths.
- Docker unavailable state is truthful and actionable.
- Workspaces are exposed only through authenticated, controlled routing.

### PRD-004: Extension Governance

VisionLab must govern required and optional extensions through a registry, artifact checksums, policy resolution, immutable installation, and administrator/instructor controls.

Acceptance:
- Required extensions cannot be removed by students.
- Marketplace access can be disabled by policy.
- Extension policy changes are audited.
- Sensitive extensions are built from source and verified.

### PRD-005: Independent VisionLab Agent

The AI agent may begin from a legally reviewed upstream open-source import, but production releases must become VisionLab-controlled artifacts.

Acceptance:
- One-time compliant source import is recorded.
- VisionLab maintains its own source, versions, changelog, build pipeline, artifact registry, and compatibility matrix.
- No production install depends on upstream extension releases.
- Required license notices are preserved.

### PRD-006: Responsible AI Patch Workflow

AI may explain, plan, and propose patches, but it cannot apply code changes directly.

Acceptance:
- Chat mode cannot write.
- Plan mode cannot write.
- Agent mode creates pending patches only.
- Human approval is required before mutation.
- Snapshots and rollback exist for every approved patch.

### PRD-007: Real-Time Collaboration

Authorized workspace participants must collaborate through presence, cursor sync, document sync, chat, and Reverb events.

Acceptance:
- Unauthorized users cannot subscribe to workspace channels.
- Multiple participants see presence and cursor state.
- Chat is persisted or intentionally ephemeral according to configuration.
- Reconnect and stale heartbeat cleanup are handled.

### PRD-008: Video Sessions

Authorized users must start or join workspace-linked video sessions through a provider abstraction.

Acceptance:
- Tokens are generated server-side.
- Collaborators can join.
- Instructors/admins can end managed sessions.
- Provider misconfiguration has a clear failure state.

### PRD-009: Analytics, VisionGuard, and Gamification

VisionLab must use real platform events to show learning progress, AI transparency, activity, streaks, badges, and operational metrics.

Acceptance:
- Analytics dashboards are role-restricted.
- VisionGuard shows human/AI contribution with confidence and limitations.
- Badges and streaks derive from real events.
- Empty analytics states are clear and not misleading.

### PRD-010: Student Project Deployment

Students must be able to deploy eligible projects through a governed async provider flow.

Acceptance:
- Student confirms public exposure.
- Deployment job excludes sensitive files.
- Provider status is stored and visible.
- Success and failure states notify the user.

### PRD-011: PWA and Notifications

VisionLab must be installable and support push notifications, while clearly treating service workers as progressive enhancement.

Acceptance:
- Manifest is valid.
- Offline fallback exists.
- IDE routes remain network-only.
- Push subscription and unsubscribe work.

### PRD-012: Production Readiness

The product must include tests, security verification, CI/CD, backups, health checks, monitoring, and runbooks.

Acceptance:
- CI blocks failed tests/builds.
- Health endpoint covers critical dependencies.
- Backup and restore procedure is documented and testable.
- Security matrix maps controls to tests.

## User Experience Requirements

- Professional dark interface with accessible contrast.
- Responsive layouts for desktop and mobile.
- Consistent empty, loading, success, warning, denied, and error states.
- Keyboard-accessible controls.
- Clear language for risky actions such as deployment, account suspension, extension policy, and AI patch approval.
- No decorative-only screens.

## Non-Functional Requirements

| ID | Requirement | Target |
|---|---|---|
| NFR-001 | Availability | Core web app is designed for monitored production uptime with graceful dependency degradation |
| NFR-002 | Performance | Standard dashboard and list pages respond within acceptable interactive latency under configured evaluation load |
| NFR-003 | Workspace reliability | Workspace launch, reuse, stop, and health recovery expose accurate states and do not report success before readiness |
| NFR-004 | Security | Authentication, access control, file handling, AI tools, extension delivery, and deployment packaging are covered by negative tests |
| NFR-005 | Privacy | Student analytics, AI logs, submissions, and forensics are role-restricted and avoid unnecessary sensitive data exposure |
| NFR-006 | Observability | Critical actions include audit logs, correlation identifiers, operational logs, and health visibility |
| NFR-007 | Accessibility | Critical workflows support keyboard navigation, semantic labels, visible focus, readable contrast, and responsive layout |
| NFR-008 | Maintainability | Domain logic lives in services, policies, jobs, events, and contracts rather than duplicated controller or view logic |
| NFR-009 | Recoverability | Backups, restore rehearsal, rollback plan, workspace cleanup, and patch rollback are documented and verified |
| NFR-010 | Legal and provenance | Imported open-source code preserves required notices and source provenance while VisionLab artifacts remain independently built and versioned |

## Competition Evaluation Flow

The evaluation path must prove the product through live workflows:

1. Administrator signs in, reviews health, users, quotas, extension policy, workspaces, audit logs, and release evidence.
2. Instructor creates or opens a course, posts an announcement, creates an assignment, and reviews the gradebook.
3. Student joins the course, starts the assignment, opens the workspace, edits files, and sees accurate workspace state.
4. A collaborator joins the same workspace; presence, cursor, chat, and optional video indicators update.
5. Student asks AI for help, receives a patch proposal, reviews the diff, approves it, and can roll it back.
6. Student submits the assignment; instructor reviews snapshot, VisionGuard signals, grade, and feedback.
7. Student deploys an eligible project after public-exposure confirmation and sees deployment status history.
8. Administrator reviews resulting analytics, audit logs, health, and release evidence.

## Product Metrics

| Metric | Target |
|---|---|
| Workspace launch success | 95 percent in configured environment |
| Unauthorized route denial coverage | 100 percent for critical routes |
| AI write path approval rate | 100 percent of file mutations require approval |
| Critical workflow test coverage | All phase acceptance scenarios covered |
| PWA installability | Manifest and service worker pass browser checks |
| Production preflight | All health checks pass before evaluation |
| Critical accessibility checks | No blocking keyboard, label, contrast, or responsive-layout failures in core workflows |
| RTM completeness | Every must-have BRD and FRD requirement maps to implementation and test evidence |

## Key Risks

| Risk | Mitigation |
|---|---|
| code-server container exposure | Authenticated proxy, scoped token, restricted ports |
| AI excessive agency | Mode matrix, tool policy, human-approved patches |
| Extension supply chain | source builds, checksums, artifact registry, license review |
| Service worker caching sensitive content | network-only APIs and IDE routes |
| Deployment leaking secrets | packaging exclusions and confirmation |
| Forked agent legal exposure | preserve required notices and source provenance |

## Release Readiness

Version 1 is ready when all PRD requirements map to functional requirements, design components, implementation work, and test cases in the RTM.

Release readiness evidence must include the version manifest, migration status, test summary, security matrix, extension artifact checksums, provider configuration summary, health report, backup restore note, known risks, rollback plan, and competition evaluation path result.

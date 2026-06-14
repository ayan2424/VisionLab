# VisionLab Implementation Plan

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Implementation Plan |
| Version | 1.0 |
| Source Prompt | `PROMPTS.xml` version `2026.06-competitive-ultimate-independent-agent-fork` |

## Implementation Strategy

VisionLab will be implemented in 12 phases. Each phase follows the same delivery gate:

1. Ready: verify prerequisites, environment, docs, and prior outputs.
2. Design: define schema, interfaces, policies, jobs, events, UI, and tests.
3. Build: implement in cohesive slices.
4. Verify: run tests, builds, security checks, browser checks.
5. Release evidence: report files changed, commands run, results, risks, operator actions.

## Engineering Defaults

- Keep Laravel policies as the center of authorization.
- Use form requests for validation.
- Use queued jobs for external providers and long-running work.
- Use event contracts for collaboration, AI patch, video, deployment, and notification flows.
- Use feature flags for high-risk capabilities.
- Preserve legal provenance for imported open-source code.
- Avoid unrelated refactors.

## Phase Governance

| Governance Item | Required Practice |
|---|---|
| Phase kickoff | inspect repository state, dependency versions, current schema, existing tests, and prior phase evidence |
| Planning | produce a short implementation plan before code changes and identify affected files, migrations, policies, jobs, events, UI, and tests |
| Scope control | complete the active phase before depending on later-phase capabilities except behind feature flags |
| Security review | identify sensitive actions, denial paths, audit events, and rate limits before implementation |
| Evidence | record tests, builds, manual checks, screenshots where useful, migration state, and unresolved risk |
| Handoff | update relevant docs, contracts, and next-phase readiness notes before closing a phase |

## Definition of Done

An implementation slice is done only when:

- User-facing behavior is complete for allowed, empty, invalid, unauthorized, unavailable, and failed states.
- Persistence, authorization, validation, audit logging, and tests are implemented where applicable.
- Background jobs and events define payload shape, retry behavior, authorization assumptions, and idempotency.
- External provider failures produce clear product states and operational logs.
- No temporary screens, hidden failures, or shortcut-only implementations remain.

## Phase Plan

### Phase 1: Foundation, Auth, Design System

Tasks:
- Establish Laravel 11 app baseline.
- Configure Breeze Blade, Tailwind, Vite, queues, Reverb readiness, Redis readiness.
- Build role-based auth and dashboards.
- Create core migrations for 27 entities.
- Create design tokens and Blade components.
- Create threat model and audit standard.

Deliverables:
- Working auth.
- Baseline schema and seed-only evaluation data.
- Design system.
- Policy baseline.

Verification:
- Fresh migration.
- Auth tests.
- RBAC denial tests.
- Frontend build.

### Phase 2: Classroom Workflows

Tasks:
- Build course management.
- Build enrollment by code and invitation.
- Build assignment lifecycle.
- Build submission snapshots.
- Build grading and gradebook.
- Build announcements and unread state.
- Build student/instructor dashboards.

Deliverables:
- Classroom workflows.
- Submission and grading screens.
- Dashboard data.

Verification:
- Course CRUD tests.
- Enrollment tests.
- Assignment start/submit/grade tests.
- Unauthorized access tests.

### Phase 3: Workspace IDE

Tasks:
- Build CodeServerManager.
- Implement workspace lifecycle.
- Resolve quotas.
- Create secure file APIs.
- Build IDE shell.
- Build file explorer.
- Add Docker unavailable state.

Deliverables:
- Secure code-server workspace.
- File APIs.
- IDE UI.

Verification:
- Workspace lifecycle tests.
- Path traversal denial.
- File CRUD tests.
- Browser layout checks.

### Phase 4: Extension Ecosystem and Lockdown

Tasks:
- Build extension registry and extension builds.
- Define artifact storage and checksums.
- Import VisionLab Agent source once after license review.
- Create VisionLab-controlled fork.
- Audit and rebrand entire source tree.
- Compile from clean source.
- Register artifact.
- Install required extensions immutably.
- Implement marketplace policy.
- Add extension sync jobs.

Deliverables:
- Extension registry.
- VisionLab Agent artifact.
- Immutable extension layer.
- Marketplace governance.

Verification:
- Source audit report.
- Old-identity scan.
- Clean build.
- code-server install smoke test.
- Student uninstall denial.
- Policy sync tests.

### Phase 5: Real-Time Collaboration

Tasks:
- Define Reverb channel authorization.
- Implement events.
- Build collaboration extension modules.
- Implement document and cursor sync.
- Implement chat.
- Add Blade presence.
- Add stale heartbeat cleanup.

Deliverables:
- Live collaboration.
- Presence UI.
- Chat.

Verification:
- Two-user collaboration check.
- Channel denial tests.
- Reconnect tests.

### Phase 6: AI Agent, Patch Review, Audit

Tasks:
- Implement AI service modes.
- Add OpenAI-compatible proxy.
- Inject workspace-specific agent config.
- Implement sandboxed tools.
- Implement patch lifecycle.
- Build patch reviewer workflow.
- Add plan-to-agent command bridge.
- Add AI memory and artifacts.
- Add eval tests.

Deliverables:
- Responsible AI workflow.
- Patch approval UI.
- Audit logs.

Verification:
- Chat/plan/agent tests.
- Forbidden path denial.
- Prompt injection tests.
- Patch approve/reject/rollback tests.

### Phase 7: Video Sessions

Tasks:
- Implement video provider abstraction.
- Implement room lifecycle.
- Generate server-side tokens.
- Integrate extension video panel.
- Add workspace call indicators.

Deliverables:
- Workspace video sessions.

Verification:
- Authorized join.
- Unauthorized denial.
- Start/end events.
- Provider failure state.

### Phase 8: Admin Operations

Tasks:
- Build admin shell.
- Build live dashboard.
- Implement user management.
- Implement workspace management.
- Implement extension and quota management.
- Build audit log views.
- Add cleanup commands.

Deliverables:
- Admin operations center.

Verification:
- Admin-only tests.
- User suspension tests.
- Workspace stop tests.
- Audit log tests.

### Phase 9: Analytics, Forensics, Gamification, Deployment

Tasks:
- Define event taxonomy.
- Build dashboards.
- Implement VisionGuard.
- Build heatmaps, streaks, badges.
- Implement deployment provider abstraction.
- Build queued deployment jobs.
- Add package exclusions.
- Add dashboard deployment history.

Deliverables:
- Analytics and learning transparency.
- Student project deployment.

Verification:
- Event capture tests.
- Role-restricted analytics tests.
- Forensics aggregation tests.
- Deployment provider mock tests.

### Phase 10: PWA and Notifications

Tasks:
- Create manifest and icons.
- Build service worker.
- Add offline fallback.
- Keep IDE/API network-only.
- Add install prompt.
- Add push subscriptions and preferences.
- Add due, announcement, grading, deployment notifications.

Deliverables:
- Installable PWA.
- Push notifications.

Verification:
- Browser application panel checks.
- Offline fallback test.
- Push subscribe/unsubscribe tests.

### Phase 11: Security, Testing, Performance

Tasks:
- Build ASVS matrix.
- Add route/channel policy map.
- Add rate limits.
- Harden headers and uploads.
- Add supply-chain checks.
- Add performance optimization.
- Add browser and accessibility tests.

Deliverables:
- Security and quality hardening.

Verification:
- Full test suite.
- Security denial tests.
- Build checks.
- Accessibility checks.

### Phase 12: Production Deployment and Evaluation

Tasks:
- Build production Docker topology.
- Configure reverse proxy and TLS.
- Build CI/CD.
- Add health checks.
- Add backups/restores.
- Add logs and alerts.
- Prepare runbooks and evaluation flow.

Deliverables:
- Production-ready release.
- Evaluation readiness package.

Verification:
- Deployment dry run.
- Health checks.
- Backup restore rehearsal.
- Release evidence.

## Delivery Sequence

Do not implement later phases before their dependency phases are accepted. Phase 4 and Phase 6 are tightly coupled through the VisionLab Agent and patch reviewer; Phase 4 must produce the approved extension artifact workflow before Phase 6 relies on it.

## Risk Burndown Plan

| Risk | Burn Down By |
|---|---|
| Workspace/container security | implement path sandbox, authenticated proxy, resource limits, denied Docker socket, and workspace cleanup tests in Phase 3 |
| Extension supply chain | complete source audit, license review, clean rebuild, checksum storage, and old-identity scan in Phase 4 |
| AI excessive agency | enforce read-only chat, plan-only planning, patch-only agent mode, human approval, snapshots, rollback, and denial tests in Phase 6 |
| Real-time instability | define event contracts, stale heartbeat cleanup, reconnect behavior, and unauthorized subscription denial in Phase 5 |
| Production surprise | add health checks, CI/CD, backup restore rehearsal, runbooks, preflight, and rollback before evaluation in Phase 12 |

## Release Evidence Checklist

- Version manifest.
- Migration state.
- Test summary.
- Security matrix.
- Build logs.
- Extension artifact checksums.
- Deployment checklist.
- Backup confirmation.
- Known risks.
- Rollback plan.

## Competition Readiness Runbook

1. Confirm environment variables, queues, scheduler, Reverb, storage, workspace runtime, AI provider mode, video provider mode, push keys, and deployment provider mode.
2. Run migrations and seed only clearly labeled evaluation data.
3. Open administrator account and verify health, user management, quotas, extension policy, workspaces, audit logs, and release evidence.
4. Open instructor account and verify course, assignment, announcement, gradebook, live session, and analytics workflows.
5. Open student account and verify enrollment, workspace launch, file editing, AI patch proposal, patch approval, submission, feedback, and deployment status.
6. Run a two-user collaboration check for presence, cursor, chat, reconnect, and unauthorized channel denial.
7. Verify VisionLab Agent artifact checksum, install result, old-identity scan, license notices, and extension activation.
8. Verify PWA installability, offline fallback, network-only IDE behavior, push subscription, and notification destination validation.
9. Review test summary, known risks, backup restore note, rollback plan, and contingency steps before evaluation.

## Operational Handoff Package

| Artifact | Required Content |
|---|---|
| Environment manifest | service names, versions, required variables, feature flags, provider modes |
| Release manifest | commit/reference, migration status, build artifacts, extension checksums |
| Security matrix | ASVS/LLM/container controls, tests, residual risks |
| Runbooks | deploy, rollback, backup, restore, workspace cleanup, incident response, evaluation reset |
| Monitoring notes | health checks, log locations, queue/scheduler checks, alert-worthy failures |
| Legal/provenance note | imported extension source reference, license notices, review result, artifact history |

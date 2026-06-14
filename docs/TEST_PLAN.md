# VisionLab Test Plan

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Test Plan |
| Version | 1.0 |
| Source Prompt | `PROMPTS.xml` version `2026.06-competitive-ultimate-independent-agent-fork` |

## Test Objectives

- Verify all critical user workflows.
- Prove role and policy boundaries.
- Prove AI cannot mutate without approval.
- Prove workspace file sandboxing.
- Prove extension governance and immutable required extensions.
- Prove PWA behavior does not cache sensitive content.
- Prove production health, backups, and release evidence.

## Test Types

| Type | Purpose |
|---|---|
| Unit | Services, policies, helpers, quota resolution, AI sandbox |
| Feature | Controllers, APIs, jobs, auth, classroom workflows |
| Browser | User journeys, IDE shell, dashboards, PWA checks |
| Security | Authorization, traversal, prompt injection, headers, rate limits |
| Integration | Reverb, queues, code-server, AI provider mock, video provider mock |
| Operational | health checks, backup/restore, CI/CD, deployment dry run |
| Accessibility | keyboard, labels, contrast, focus, responsive behavior |

## Coverage Matrix

| Requirement Area | Positive Coverage | Negative Coverage | Evidence |
|---|---|---|---|
| Auth/RBAC | role login, role dashboards | suspended user, unauthorized route, final admin removal | feature/security tests |
| Classroom | course, enrollment, assignment, submission, grading | duplicate enrollment, non-enrolled access, late policy edge cases | feature/browser tests |
| Workspace | start, stop, quota, file read/write | traversal, blocked path, Docker unavailable, unauthorized workspace | feature/security tests |
| Extensions | artifact registration, required install, policy sync | student uninstall, checksum mismatch, marketplace disabled | feature/operational tests |
| Collaboration | presence, cursor, chat, reconnect | unauthorized channel, stale session, sanitized chat | browser/security tests |
| AI | chat, plan, patch proposal, approval, rollback | forbidden path, prompt injection, provider outage, unapproved mutation | feature/security tests |
| Video | room start, authorized join, end call | unauthorized join, provider misconfiguration | feature/security/manual tests |
| Analytics | event capture, dashboards, VisionGuard | role-restricted data access, confidence limitations | feature/browser tests |
| Deployment | request, package, provider success, history | no public confirmation, blocked files, provider failure | feature/security tests |
| PWA/Push | manifest, offline fallback, subscribe, click URL | IDE offline, sensitive API cache prevention, invalid URL | browser/security tests |
| Production | CI, health, backup, TLS/proxy, release evidence | failed health dependency, restore failure, missing rollback | operational tests |

## Test Data Strategy

- Use deterministic factories for users, courses, enrollments, assignments, submissions, workspaces, extensions, AI sessions, deployments, and analytics events.
- Keep evaluation data separate from production runtime behavior and label it clearly in seeders and documentation.
- Use provider mocks for AI, video, push, and deployment in automated tests.
- Use isolated temporary storage roots for workspace file tests and remove them after test completion.
- Include malicious inputs for path traversal, unsafe HTML, prompt injection, unauthorized channel names, invalid notification URLs, and blocked deployment files.

## Test Environments

| Environment | Purpose |
|---|---|
| Local | Developer verification |
| CI | Automated tests and builds |
| Staging | Provider integration and UAT |
| Evaluation | Final preflight and presentation readiness |

## Entry Criteria

- Phase implementation complete.
- Migrations run.
- Seed-only evaluation data available.
- Required environment variables configured.
- Test database available.
- Queues and scheduler available where needed.
- code-server runtime available for workspace tests.

## Exit Criteria

- Must-have tests pass.
- No critical or high severity defects open.
- Security denial tests pass.
- Browser checks pass for critical workflows.
- Release evidence is attached.
- Known risks are documented.

## Test Scenarios

### Auth and RBAC

| ID | Scenario | Expected Result |
|---|---|---|
| TC-AUTH-001 | Register/login as student | Student dashboard opens |
| TC-AUTH-002 | Login as instructor | Instructor dashboard opens |
| TC-AUTH-003 | Suspended user login | Login denied and session invalidated |
| TC-AUTH-004 | Student opens admin route | 403 or controlled denial |
| TC-AUTH-005 | Attempt final admin removal | Action denied |

### Classroom

| ID | Scenario | Expected Result |
|---|---|---|
| TC-CLS-001 | Instructor creates course | Course appears for instructor |
| TC-CLS-002 | Student joins with valid code | Enrollment created |
| TC-CLS-003 | Student joins twice | Duplicate prevented |
| TC-CLS-004 | Instructor creates assignment | Assignment visible to enrolled students |
| TC-CLS-005 | Student starts and submits assignment | Workspace snapshot stored |
| TC-CLS-006 | Instructor grades submission | Grade and feedback visible to student |
| TC-CLS-007 | Non-enrolled student opens course | Access denied |

### Workspace

| ID | Scenario | Expected Result |
|---|---|---|
| TC-WSP-001 | Authorized student opens workspace | Workspace starts or reuses healthy container |
| TC-WSP-002 | Stop workspace | Container stops and state updates |
| TC-WSP-003 | Quota resolution | Expected quota stored on workspace start |
| TC-WSP-004 | Read file inside workspace | File content returned |
| TC-WSP-005 | Path traversal attempt | Request denied |
| TC-WSP-006 | Read `.env` or blocked path | Request denied |
| TC-WSP-007 | Docker unavailable | Clear setup error shown |

### Extensions

| ID | Scenario | Expected Result |
|---|---|---|
| TC-EXT-001 | Register extension artifact | Checksum and metadata stored |
| TC-EXT-002 | Student attempts to remove required extension | Denied by filesystem/policy |
| TC-EXT-003 | Admin disables marketplace | Marketplace unavailable in workspace |
| TC-EXT-004 | Build VisionLab Agent | Source audit, clean build, checksum, install smoke test pass |
| TC-EXT-005 | Verify license/provenance | Required notices preserved |

### Collaboration

| ID | Scenario | Expected Result |
|---|---|---|
| TC-COL-001 | Unauthorized channel subscribe | Denied |
| TC-COL-002 | Two users join workspace | Presence shown |
| TC-COL-003 | Cursor move | Remote cursor updates |
| TC-COL-004 | Chat message | Message delivered and sanitized |
| TC-COL-005 | Reconnect after network interruption | Presence recovers |

### AI

| ID | Scenario | Expected Result |
|---|---|---|
| TC-AI-001 | Chat mode asks explanation | Response returned, no write |
| TC-AI-002 | Plan mode asks implementation plan | Plan returned, no write |
| TC-AI-003 | Agent proposes patch | Pending patch created |
| TC-AI-004 | Approve patch | Snapshot saved and patch applied |
| TC-AI-005 | Reject patch | File unchanged |
| TC-AI-006 | Rollback patch | Snapshot restored |
| TC-AI-007 | Prompt injection inside file | Tool policy remains enforced |
| TC-AI-008 | Attempt forbidden path write | Denied |
| TC-AI-009 | Provider outage | Graceful error and audit log |

### Video

| ID | Scenario | Expected Result |
|---|---|---|
| TC-VID-001 | Start video session | Room record and event created |
| TC-VID-002 | Authorized user joins | Join details returned |
| TC-VID-003 | Unauthorized user joins | Denied |
| TC-VID-004 | Instructor ends call | Call ended event broadcast |
| TC-VID-005 | Provider not configured | Clear failure state |

### Analytics, Forensics, Gamification

| ID | Scenario | Expected Result |
|---|---|---|
| TC-ANL-001 | Record event | Event stored with actor/resource |
| TC-ANL-002 | Student views analytics | Own data only |
| TC-ANL-003 | Instructor views course analytics | Owned course data only |
| TC-ANL-004 | VisionGuard aggregation | Human/AI counts and confidence displayed |
| TC-GAM-001 | Activity streak update | Streak calculated from events |
| TC-GAM-002 | Badge trigger reached | Badge awarded once |

### Deployment

| ID | Scenario | Expected Result |
|---|---|---|
| TC-DEP-001 | Workspace owner requests deployment | Deployment record created |
| TC-DEP-002 | User declines public confirmation | No deployment queued |
| TC-DEP-003 | Package workspace | Secrets and blocked paths excluded |
| TC-DEP-004 | Provider success | Public URL stored and notification sent |
| TC-DEP-005 | Provider failure | Failed status and error summary stored |

### PWA and Notifications

| ID | Scenario | Expected Result |
|---|---|---|
| TC-PWA-001 | Manifest check | Browser detects installability metadata |
| TC-PWA-002 | Offline page navigation | Offline fallback appears |
| TC-PWA-003 | IDE offline | IDE shows online-only message |
| TC-PWA-004 | Push subscribe | Subscription stored |
| TC-PWA-005 | Notification click URL | Opens authorized destination only |

### Security and Browser Contract Checks

| ID | Scenario | Expected Result |
|---|---|---|
| TC-BR-001 | IDE shell browser smoke test | workspace state, file explorer, code-server frame, collaborators, AI indicator, video entry, and deployment entry render according to feature flags |
| TC-SEC-001 | Route, API, channel, and policy map review | every protected surface has an authorization rule and denial behavior |
| TC-SEC-002 | Workspace path sandbox negative suite | traversal, symlink escape, blocked files, dependency directories, and platform configuration access are denied |
| TC-SEC-003 | Collaboration chat sanitization | unsafe markup is sanitized and cannot execute in the browser |
| TC-SEC-004 | AI tool sandbox negative suite | forbidden reads/writes, prompt-injection attempts, secret access, and unapproved mutations are denied |
| TC-SEC-005 | Video token authorization negative suite | unauthorized room join and token reuse attempts are denied |
| TC-SEC-006 | Deployment package exclusion suite | secrets, blocked paths, build caches, dependency directories, and platform-owned files are excluded |

### Production and Operations

| ID | Scenario | Expected Result |
|---|---|---|
| TC-PROD-001 | CI run | Tests and builds pass before deploy |
| TC-PROD-002 | Health endpoint | Critical dependencies reported |
| TC-PROD-003 | Backup restore rehearsal | Restore procedure works |
| TC-PROD-004 | TLS/proxy validation | App, Reverb, and workspace routes work |
| TC-PROD-005 | Release evidence review | Required artifacts present |

### Non-Functional Tests

| ID | Scenario | Expected Result |
|---|---|---|
| TC-NFR-001 | Query and pagination review for critical lists | no avoidable N+1 queries and growing lists are paginated or filtered |
| TC-NFR-002 | Privacy boundary review | student analytics, AI logs, submissions, and forensics are hidden from unauthorized roles |
| TC-NFR-003 | Audit/correlation review | sensitive workflows include actor, action, resource, result, correlation identifier, and timestamp |
| TC-NFR-004 | Accessibility smoke test | critical workflows pass keyboard, label, focus, contrast, and responsive layout checks |
| TC-NFR-005 | Recovery drill | backup restore, AI patch rollback, workspace cleanup, and deployment rollback notes are verified |

### Competition Evaluation Tests

| ID | Scenario | Expected Result |
|---|---|---|
| TC-EVAL-001 | Administrator evaluation path | health, users, quotas, extension policy, workspaces, audit logs, and release evidence are available |
| TC-EVAL-002 | Instructor evaluation path | course, assignment, announcement, gradebook, live session, and analytics workflows operate |
| TC-EVAL-003 | Student evaluation path | enrollment, workspace, file edit, AI patch, submission, feedback, and deployment history operate |
| TC-EVAL-004 | Collaboration evaluation path | two-user presence, cursor, chat, reconnect, and unauthorized subscription denial operate |
| TC-EVAL-005 | Reset and contingency check | evaluation accounts, seed data reset, known risks, backup note, and fallback provider states are ready |

## Defect Severity

| Severity | Definition |
|---|---|
| Critical | Security breach, data loss, no login, no workspace, AI unsafe mutation |
| High | Core workflow blocked for a role |
| Medium | Important workflow degraded with workaround |
| Low | Cosmetic or minor usability issue |

## Automation Priorities

1. Auth/RBAC denial paths.
2. Classroom assignment lifecycle.
3. Workspace path sandbox.
4. AI patch approval and forbidden paths.
5. Extension policy and VisionLab Agent build report.
6. Deployment package exclusions.
7. PWA network-only sensitive routes.
8. Health endpoint and production preflight.

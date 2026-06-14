# VisionLab Functional Requirements Document

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Functional Requirements Document |
| Version | 1.0 |
| Source Prompt | `PROMPTS.xml` version `2026.06-competitive-ultimate-independent-agent-fork` |

## Requirement Conventions

- Priority: Must, Should, Could.
- Verification: Unit, Feature, Browser, Security, Manual, Operational.
- Each requirement must trace to BRD, HLD, RTM, and Test Plan entries.

## Cross-Cutting Functional Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-XCUT-001 | Every protected route, API, channel, and job-triggering action shall enforce authorization through policies, gates, or equivalent service checks. | Must | Security |
| FR-XCUT-002 | Every mutating request shall validate input through form requests or typed service validators. | Must | Feature/Security |
| FR-XCUT-003 | User-facing operations shall expose success, validation, empty, unauthorized, unavailable, and failed states. | Must | Browser |
| FR-XCUT-004 | Destructive operations shall require explicit confirmation and shall create audit events. | Must | Feature/Security |
| FR-XCUT-005 | Long-running external operations shall execute through queued jobs with retries, timeout policy, idempotency keys where needed, and visible status. | Must | Feature/Operational |
| FR-XCUT-006 | The system shall use correlation identifiers for sensitive workflows that span requests, jobs, events, and audit logs. | Should | Operational |
| FR-XCUT-007 | Lists that can grow shall support pagination and role-appropriate filtering or search. | Must | Browser |
| FR-XCUT-008 | All rendered user content shall be escaped or sanitized according to context. | Must | Security |
| FR-XCUT-009 | Feature flags shall gate high-risk capabilities including AI agent mode, marketplace access, video, push, and deployment. | Must | Feature/Operational |
| FR-XCUT-010 | All critical workflows shall be localization-ready and accessible through semantic labels, keyboard flow, focus states, and responsive layouts. | Must | Browser/Accessibility |

## Roles and Permissions

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-AUTH-001 | The system shall support `admin`, `instructor`, and `student` roles. | Must | Feature |
| FR-AUTH-002 | The system shall redirect users to role-appropriate dashboards after login. | Must | Feature |
| FR-AUTH-003 | The system shall reject suspended users at login and invalidate active sessions where applicable. | Must | Feature/Security |
| FR-AUTH-004 | The system shall use policies or gates for all protected domain actions. | Must | Security |
| FR-AUTH-005 | The system shall prevent removing or disabling the final active administrator. | Must | Feature/Security |

## Classroom Functional Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-CLS-001 | Instructors shall create, edit, deactivate, and view courses they own. | Must | Feature |
| FR-CLS-002 | Students shall join active courses by valid enrollment code. | Must | Feature |
| FR-CLS-003 | Duplicate enrollment shall be prevented. | Must | Feature |
| FR-CLS-004 | Instructors shall invite and remove students according to course policy. | Should | Feature |
| FR-CLS-005 | Course detail pages shall include stream, assignments, and people areas based on role. | Must | Browser |
| FR-CLS-006 | Instructors shall create, edit, and archive assignments. | Must | Feature |
| FR-CLS-007 | Students shall start assignments and reuse existing workspaces when already created. | Must | Feature |
| FR-CLS-008 | Students shall submit assignment snapshots from their workspace. | Must | Feature |
| FR-CLS-009 | The system shall calculate late submission state based on due date and policy. | Must | Feature |
| FR-CLS-010 | Instructors shall grade submissions and provide feedback. | Must | Feature |
| FR-CLS-011 | The system shall show gradebook summaries to authorized instructors. | Should | Feature |
| FR-CLS-012 | Instructors shall post pinned or normal announcements to course streams. | Must | Feature |
| FR-CLS-013 | Announcement visibility shall be restricted to enrolled users and course staff. | Must | Security |

## Workspace IDE Functional Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-WSP-001 | The system shall create workspace records linked to owner, course, and assignment where applicable. | Must | Feature |
| FR-WSP-002 | The system shall start, stop, restart, inspect, and clean code-server containers. | Must | Feature/Operational |
| FR-WSP-003 | Workspace startup shall be idempotent. | Must | Feature |
| FR-WSP-004 | The system shall resolve effective quotas by configured precedence. | Must | Feature |
| FR-WSP-005 | The system shall persist applied quota values on container start. | Must | Feature |
| FR-WSP-006 | File APIs shall support tree, read, write, create, rename, delete, and optional download. | Must | Feature |
| FR-WSP-007 | File APIs shall block traversal outside workspace root. | Must | Security |
| FR-WSP-008 | File APIs shall block sensitive paths and files according to policy. | Must | Security |
| FR-WSP-009 | The IDE shell shall show workspace state, file explorer, code-server iframe, collaborators, status bar, AI indicators, video entry, and deployment entry where enabled. | Must | Browser |
| FR-WSP-010 | Docker unavailable state shall be truthful and actionable. | Must | Manual |

## Extension Governance Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-EXT-001 | The system shall maintain an extension registry with artifact metadata and checksum. | Must | Feature |
| FR-EXT-002 | Required extensions shall be installed into immutable locations. | Must | Security/Operational |
| FR-EXT-003 | Students shall not uninstall or mutate required extensions. | Must | Security |
| FR-EXT-004 | Admins shall configure global, course, and workspace extension policy. | Must | Feature |
| FR-EXT-005 | Marketplace access shall be controlled by policy. | Must | Feature/Security |
| FR-EXT-006 | Active workspace policy changes shall synchronize through queued jobs. | Should | Feature |
| FR-EXT-007 | Sensitive extensions shall be built from source and verified. | Must | Operational/Security |
| FR-EXT-008 | The VisionLab Agent shall be maintained as a VisionLab-controlled fork after initial compliant source import. | Must | Operational |
| FR-EXT-009 | The VisionLab Agent release shall preserve required open-source license notices. | Must | Security/Legal |

## Collaboration Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-COL-001 | The system shall authorize workspace real-time channels by collaborator membership. | Must | Security |
| FR-COL-002 | The system shall broadcast join, leave, cursor, selection, document, chat, and warning events. | Must | Feature |
| FR-COL-003 | The collaboration extension shall handle reconnect and stale presence. | Must | Browser |
| FR-COL-004 | Cursor decorations shall use stable participant colors. | Should | Browser |
| FR-COL-005 | Document sync shall avoid echo loops and surface conflicts. | Must | Browser |
| FR-COL-006 | Chat messages shall be sanitized before rendering. | Must | Security |

## AI Agent Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-AI-001 | The AI service shall support chat, plan, and agent modes. | Must | Feature |
| FR-AI-002 | Chat mode shall be read-only. | Must | Security |
| FR-AI-003 | Plan mode shall not mutate files. | Must | Security |
| FR-AI-004 | Agent mode shall create pending patches only. | Must | Security |
| FR-AI-005 | AI file mutations shall require human approval. | Must | Security |
| FR-AI-006 | The system shall create snapshots before approved patch application. | Must | Feature |
| FR-AI-007 | The system shall support patch rejection and rollback. | Must | Feature |
| FR-AI-008 | The AI service shall expose an OpenAI-compatible proxy for the VisionLab Agent. | Must | Feature |
| FR-AI-009 | Workspace-specific AI extension configuration shall be injected at startup without provider secrets. | Must | Security |
| FR-AI-010 | The plan-to-agent bridge shall only create pending patches and PatchProposed events. | Must | Security |
| FR-AI-011 | AI tools shall block secrets, environment files, dependency directories, and platform configuration. | Must | Security |
| FR-AI-012 | AI artifacts shall render in isolated previews. | Should | Security/Browser |
| FR-AI-013 | AI memory shall be a visible workspace file governed by sandbox rules. | Should | Security |

## Video Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-VID-001 | The system shall support a video provider abstraction. | Should | Feature |
| FR-VID-002 | Authorized collaborators shall retrieve join details. | Must | Security |
| FR-VID-003 | Instructors/admins shall end managed sessions. | Must | Feature |
| FR-VID-004 | Tokens shall be generated server-side. | Must | Security |
| FR-VID-005 | Provider misconfiguration shall show a clear failure state. | Must | Manual |

## Admin Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-ADM-001 | Admin dashboard shall show live platform metrics. | Must | Feature |
| FR-ADM-002 | Admins shall manage users, roles, and account status. | Must | Feature |
| FR-ADM-003 | Admin actions shall be audited. | Must | Security |
| FR-ADM-004 | Admins shall inspect and stop workspaces. | Must | Feature |
| FR-ADM-005 | Admins shall manage extension, marketplace, and quota policy. | Must | Feature |
| FR-ADM-006 | Admins shall search audit logs by actor, action, resource, result, date, and correlation identifier. | Should | Feature |

## Analytics, Forensics, Gamification, Deployment

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-ANL-001 | The system shall record normalized analytics events. | Must | Feature |
| FR-ANL-002 | Dashboards shall be role-restricted. | Must | Security |
| FR-ANL-003 | VisionGuard shall classify human, AI-approved, pasted/imported where detectable, starter, and system-generated changes. | Must | Feature |
| FR-ANL-004 | Forensics shall show confidence and limitations. | Must | Browser |
| FR-GAM-001 | The system shall render student contribution heatmaps. | Should | Browser |
| FR-GAM-002 | The system shall calculate streaks from real events. | Should | Feature |
| FR-GAM-003 | The system shall award badges from defined triggers. | Should | Feature |
| FR-DEP-001 | Students shall request deployment for owned eligible workspaces. | Should | Feature |
| FR-DEP-002 | Deployment shall require public-exposure confirmation. | Must | Browser/Security |
| FR-DEP-003 | Deployment packages shall exclude secrets and disallowed paths. | Must | Security |
| FR-DEP-004 | Deployment status shall be stored and visible. | Should | Feature |

## PWA and Notification Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-PWA-001 | The system shall provide a valid web app manifest. | Should | Browser |
| FR-PWA-002 | The service worker shall treat IDE and sensitive APIs as network-only. | Must | Security |
| FR-PWA-003 | Offline fallback shall be explicit and honest. | Should | Browser |
| FR-PWA-004 | Users shall subscribe and unsubscribe from push notifications. | Should | Feature |
| FR-PWA-005 | Notification URLs shall be validated. | Must | Security |

## Interface and Event Contract Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-INT-001 | Workspace file APIs shall publish stable request and response contracts for tree, read, write, create, rename, delete, and download operations. | Must | Feature |
| FR-INT-002 | Workspace lifecycle actions shall expose states `starting`, `running`, `unhealthy`, `stopped`, `failed`, `unauthorized`, and `offline`. | Must | Feature |
| FR-INT-003 | Collaboration events shall define payloads for joined, left, cursor moved, selection changed, document changed, chat sent, and warning emitted. | Must | Feature |
| FR-INT-004 | AI patch events shall define payloads for proposed, approved, rejected, applied, failed, rolled back, and expired states. | Must | Feature |
| FR-INT-005 | Deployment events shall define payloads for pending, packaging, deploying, live, failed, and cancelled states. | Should | Feature |
| FR-INT-006 | Admin governance endpoints shall return auditable result codes and never report success for partially failed policy changes. | Must | Security/Feature |
| FR-INT-007 | Extension build records shall include source reference, build strategy, branding summary, checksum, compatibility result, and license review status. | Must | Operational |
| FR-INT-008 | Health endpoints shall separate public liveness from authenticated operational readiness details. | Must | Security/Operational |
| FR-INT-009 | Notification events shall include actor, recipient, resource reference, channel, delivery state, and click destination where applicable. | Should | Feature |
| FR-INT-010 | Public deployment URLs shall be stored only after provider confirmation and shall be visible only to authorized users unless explicitly published. | Must | Security |

## Non-Functional Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| NFR-001 | The system shall expose monitored liveness and authenticated readiness checks for critical dependencies. | Must | Operational |
| NFR-002 | Critical pages shall avoid avoidable N+1 queries and shall use pagination for growing datasets. | Must | Performance |
| NFR-003 | Workspace lifecycle operations shall use timeouts, health checks, and recoverable states. | Must | Operational |
| NFR-004 | Security-sensitive controls shall include positive and negative automated tests. | Must | Security |
| NFR-005 | Student analytics, submissions, forensics, and AI logs shall be visible only to authorized roles. | Must | Security |
| NFR-006 | Audit logs shall capture actor, action, resource, result, correlation identifier, timestamp, and available request metadata. | Must | Security/Operational |
| NFR-007 | Critical workflows shall satisfy accessibility checks for keyboard navigation, labels, focus, contrast, and responsive layout. | Must | Accessibility |
| NFR-008 | Provider integrations shall fail with clear states and without leaking secrets. | Must | Security/Operational |
| NFR-009 | Backup, restore, rollback, and patch rollback procedures shall be documented and rehearsed where applicable. | Must | Operational |
| NFR-010 | Imported extension source shall preserve required legal notices and be released through VisionLab-controlled build and versioning. | Must | Legal/Operational |

## Production Requirements

| ID | Requirement | Priority | Verification |
|---|---|---|---|
| FR-OPS-001 | CI/CD shall run tests and builds before deployment. | Must | Operational |
| FR-OPS-002 | Health endpoint shall cover database, Redis, queues, scheduler, Reverb, storage, AI, push, video, and workspace orchestration. | Must | Operational |
| FR-OPS-003 | Backup and restore procedures shall be documented and testable. | Must | Operational |
| FR-OPS-004 | Release evidence shall include version manifest, migrations, tests, security summary, risks, and rollback. | Must | Operational |

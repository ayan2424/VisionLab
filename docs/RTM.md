# VisionLab Requirements Traceability Matrix
## Version 9.0 — Production-Grade Enterprise Edition

---

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Requirements Traceability Matrix (RTM) |
| Version | 9.0 |
| Prompt Pack | `PROMPTS.xml` v9.0 |
| Standard | IIBA BABOK — Trace Requirements (Section 5.1) |
| Audience | Product Owner, QA, Engineering, Security, Auditors |

---

## Traceability Model

This RTM links Business Requirements (BRD) → Product Requirements (PRD) → Functional Requirements (FRD) → HLD Component → Phase → Test Scenario. Every Must-priority requirement must have complete bidirectional traceability before a production release is accepted.

Coverage Rules:
- Every BR must map to at least one PR
- Every PR must map to at least one FR
- Every Must-priority FR must map to at least one test scenario
- Security-sensitive FRs must include at least one negative test
- Requirements affecting data, APIs, jobs, or events must map to HLD components

---

## Status Lifecycle

| Status | Meaning |
|---|---|
| Planned | Approved for implementation, evidence not yet collected |
| In Progress | Implementation started, tests being written |
| Implemented | Code and documentation complete for mapped scope |
| Verified | All mapped tests pass with evidence attached |
| Deferred | Intentionally moved out with Product Owner approval |
| Blocked | Cannot proceed until named dependency resolves |

---

## Primary Traceability Matrix

| BR ID | PR ID | FR ID | HLD Component | Phase | Test ID | Priority | Status |
|---|---|---|---|---|---|---|---|
| BR-001 | PRD-001 | FR-AUTH-001 | Auth/RBAC | 1 | TC-AUTH-001, TC-AUTH-002 | Must | Planned |
| BR-001 | PRD-001 | FR-AUTH-002 | Auth/RBAC | 1 | TC-AUTH-001 | Must | Planned |
| BR-001 | PRD-001 | FR-AUTH-003 | Auth/RBAC | 1 | TC-AUTH-003 | Must | Planned |
| BR-001 | PRD-001 | FR-AUTH-004 | Policy Layer | 1 | TC-SEC-001 | Must | Planned |
| BR-001 | PRD-001 | FR-AUTH-005 | Policy Layer / Admin | 8 | TC-AUTH-005 | Must | Planned |
| BR-001 | PRD-001 | FR-AUTH-006 | Auth/RBAC | 1 | TC-SEC-007 | Must | Planned |
| BR-001 | PRD-001 | FR-AUTH-007 | Rate Limiting | 1 | TC-SEC-008 | Must | Planned |
| BR-001 | PRD-001 | FR-AUTH-008 | Auth Views | 1 | TC-BR-002 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-001 | Course Management | 2 | TC-CLS-001 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-002 | Enrollment System | 2 | TC-CLS-002 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-003 | Enrollment System | 2 | TC-CLS-003 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-004 | Enrollment System | 2 | TC-CLS-009 | Should | Planned |
| BR-002 | PRD-002 | FR-CLS-005 | Course UI | 2 | TC-BR-003 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-006 | Assignment Management | 2 | TC-CLS-004 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-007 | Assignment/Workspace | 2, 3 | TC-CLS-005 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-008 | Submission System | 2 | TC-CLS-005 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-009 | Scheduler | 2 | TC-CLS-010 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-010 | Grading System | 2 | TC-CLS-006 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-011 | Grading System | 2 | TC-CLS-011 | Should | Planned |
| BR-002 | PRD-002 | FR-CLS-012 | Gradebook | 2 | TC-CLS-012 | Should | Planned |
| BR-002 | PRD-002 | FR-CLS-013 | Announcements | 2 | TC-CLS-013 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-014 | Announcement Policy | 2 | TC-SEC-009 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-016 | Student Dashboard | 2 | TC-BR-004 | Must | Planned |
| BR-002 | PRD-002 | FR-CLS-017 | Instructor Dashboard | 2 | TC-BR-005 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-001 | Workspace Model | 3 | TC-WSP-001 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-002 | CodeServerManager | 3 | TC-WSP-001, TC-WSP-002 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-003 | Quota Resolver | 3 | TC-WSP-003 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-004 | Docker Security | 3 | TC-SEC-010 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-005 | Token Injection | 3 | TC-SEC-011 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-006 | File API | 3 | TC-WSP-004 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-007 | File Sandbox | 3 | TC-WSP-005, TC-SEC-002 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-008 | File Sandbox | 3 | TC-WSP-006, TC-SEC-002 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-009 | IDE Shell | 3 | TC-BR-001 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-010 | Workspace UI States | 3 | TC-WSP-008 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-012 | Scheduler | 3 | TC-WSP-009 | Must | Planned |
| BR-003 | PRD-003 | FR-WSP-013 | Docker Unavailable State | 3 | TC-WSP-007 | Must | Planned |
| BR-004 | PRD-007 | FR-COL-001 | Reverb Channel Auth | 5 | TC-COL-001 | Must | Planned |
| BR-004 | PRD-007 | FR-COL-002 | Collaboration Events | 5 | TC-COL-002, TC-COL-003 | Must | Planned |
| BR-004 | PRD-007 | FR-COL-003 | Reconnect Resilience | 5 | TC-COL-005 | Must | Planned |
| BR-004 | PRD-007 | FR-COL-005 | Document Sync | 5 | TC-COL-004 | Must | Planned |
| BR-004 | PRD-007 | FR-COL-006 | Chat Panel Security | 5 | TC-SEC-003 | Must | Planned |
| BR-004 | PRD-007 | FR-COL-007 | Rate Limiting / Payload | 5 | TC-SEC-012 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-001 | AI Service / Mode Matrix | 6 | TC-AI-001, TC-AI-002 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-002 | AI Service Modes | 6 | TC-AI-001, TC-AI-002 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-003 | AI Proxy Security | 6 | TC-SEC-013 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-004 | Patch Lifecycle | 6 | TC-AI-003 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-005 | Human Approval Gate | 6 | TC-AI-004, TC-AI-005 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-006 | Snapshots & Rollback | 6 | TC-AI-006 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-007 | AI Safety Filters | 6 | TC-SEC-004 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-008 | OpenAI Proxy | 6 | TC-AI-010 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-010 | Execute-Plan Bridge | 6 | TC-AI-011 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-011 | AI Sandbox | 6 | TC-SEC-004, TC-AI-008 | Must | Planned |
| BR-005 | PRD-006 | FR-AI-014 | Token Budget | 6 | TC-AI-012 | Must | Planned |
| BR-006 | PRD-004 | FR-EXT-001 | Extension Registry | 4 | TC-EXT-001 | Must | Planned |
| BR-006 | PRD-004 | FR-EXT-002 | Checksum Verification | 4 | TC-EXT-006 | Must | Planned |
| BR-006 | PRD-004 | FR-EXT-003 | Immutable Extension Layer | 4 | TC-EXT-002 | Must | Planned |
| BR-006 | PRD-004 | FR-EXT-004 | Student Removal Denial | 4 | TC-EXT-002 | Must | Planned |
| BR-006 | PRD-004 | FR-EXT-005 | Extension Policy | 4, 8 | TC-EXT-003 | Must | Planned |
| BR-006 | PRD-004 | FR-EXT-006 | Dual-Layer Marketplace | 4 | TC-EXT-003 | Must | Planned |
| BR-006 | PRD-004 | FR-EXT-007 | Sync Job | 4 | TC-EXT-007 | Should | Planned |
| BR-006 | PRD-005 | FR-EXT-008 | VisionLab Agent Build | 4 | TC-EXT-004 | Must | Planned |
| BR-006 | PRD-005 | FR-EXT-009 | License and Provenance | 4 | TC-EXT-005 | Must | Planned |
| BR-006 | PRD-005 | FR-EXT-010 | VisionLab Fork Independence | 4 | TC-EXT-008 | Must | Planned |
| BR-006 | PRD-005 | FR-EXT-011 | Strategy B Restriction | 4 | TC-EXT-009 | Must | Planned |
| BR-007 | PRD-009 | FR-ANL-003 | Analytics Dashboard | 9 | TC-ANL-004 | Must | Planned |
| BR-007 | PRD-009 | FR-ANL-004 | Instructor Grading View | 9 | TC-ANL-005 | Must | Planned |
| BR-007 | PRD-009 | FR-ANL-001 | Analytics Events | 9 | TC-ANL-001 | Must | Planned |
| BR-007 | PRD-009 | FR-ANL-002 | Analytics Role Restriction | 9 | TC-ANL-002, TC-ANL-003 | Must | Planned |
| BR-008 | PRD-008 | FR-VID-001 | Video Service | 7 | TC-VID-001 | Should | Planned |
| BR-008 | PRD-008 | FR-VID-002 | Video Auth | 7 | TC-VID-003 | Must | Planned |
| BR-008 | PRD-008 | FR-VID-003 | End Call Authorization | 7 | TC-VID-004 | Must | Planned |
| BR-008 | PRD-008 | FR-VID-004 | JWT Security | 7 | TC-SEC-005 | Must | Planned |
| BR-008 | PRD-008 | FR-VID-005 | Provider Failure State | 7 | TC-VID-005 | Must | Planned |
| BR-009 | PRD-010 | FR-DEP-001 | Deployment Service | 9 | TC-DEP-001 | Should | Planned |
| BR-009 | PRD-010 | FR-DEP-002 | Deployment Confirmation | 9 | TC-DEP-002 | Must | Planned |
| BR-009 | PRD-010 | FR-DEP-003 | Package Exclusions | 9 | TC-SEC-006 | Must | Planned |
| BR-009 | PRD-010 | FR-DEP-004 | Deployment History | 9 | TC-DEP-004 | Should | Planned |
| BR-009 | PRD-010 | FR-DEP-005 | Provider Abstraction | 9 | TC-DEP-005 | Should | Planned |
| BR-010 | PRD-011 | FR-PWA-001 | Manifest | 10 | TC-PWA-001 | Should | Planned |
| BR-010 | PRD-011 | FR-PWA-002 | Service Worker | 10 | TC-PWA-002, TC-PWA-003 | Must | Planned |
| BR-010 | PRD-011 | FR-PWA-003 | Offline Fallback | 10 | TC-PWA-003 | Should | Planned |
| BR-010 | PRD-011 | FR-PWA-004 | Push Subscriptions | 10 | TC-PWA-004 | Should | Planned |
| BR-010 | PRD-011 | FR-PWA-005 | Notification URL Validation | 10 | TC-PWA-005 | Must | Planned |
| BR-011 | PRD-012 | FR-OPS-001 | CI/CD Pipeline | 12 | TC-PROD-001 | Must | Planned |
| BR-011 | PRD-012 | FR-OPS-002 | Health Endpoint | 12 | TC-PROD-002 | Must | Planned |
| BR-011 | PRD-012 | FR-OPS-003 | Backup and Restore | 12 | TC-PROD-003 | Must | Planned |
| BR-011 | PRD-012 | FR-OPS-004 | Production Docker | 12 | TC-PROD-004 | Must | Planned |
| BR-011 | PRD-012 | FR-OPS-005 | Nginx TLS and Headers | 12 | TC-PROD-004, TC-SEC-014 | Must | Planned |
| BR-012 | PRD-005 | FR-EXT-009 | License and Provenance | 4 | TC-EXT-005 | Must | Planned |
| BR-013 | PRD-001 | FR-XCUT-004 | Audit Trail | All | TC-NFR-003 | Must | Planned |
| BR-014 | PRD-006 | FR-AI-014 | Token Budget | 6 | TC-AI-012 | Must | Planned |
| BR-015 | PRD-001 | FR-XCUT-009 | Feature Flags | All | TC-FEAT-001 | Must | Planned |

---

## Cross-Cutting Requirements Traceability

| FR ID | Description | Applies To | Test ID | Status |
|---|---|---|---|---|
| FR-XCUT-001 | Policy authorization on every protected action | All phases | TC-SEC-001 | Planned |
| FR-XCUT-002 | FormRequest validation for all state-changing inputs | All phases | TC-XCUT-001 | Planned |
| FR-XCUT-003 | All user-facing operations expose complete state set | All phases | TC-BR-006 | Planned |
| FR-XCUT-004 | Destructive operations require confirmation + audit log | All phases | TC-NFR-003 | Planned |
| FR-XCUT-005 | Long-running operations via queued jobs with retry | All phases | TC-XCUT-002 | Planned |
| FR-XCUT-006 | Correlation identifiers in audit trail | All phases | TC-NFR-003 | Planned |
| FR-XCUT-007 | Growing lists are paginated | All phases | TC-NFR-001 | Planned |
| FR-XCUT-008 | User-generated content HTML-escaped or HTMLPurifier-sanitized | All phases | TC-SEC-003 | Planned |
| FR-XCUT-009 | Feature flags gate high-risk capabilities | All phases | TC-FEAT-001 | Planned |
| FR-XCUT-010 | WCAG 2.1 AA accessibility baseline | All phases | TC-NFR-004 | Planned |
| FR-XCUT-011 | Standard API JSON envelope | All API phases | TC-XCUT-003 | Planned |
| FR-XCUT-012 | Analytics events via Model Observers | All phases | TC-ANL-001 | Planned |

---

## Interface Contract Traceability

| Contract ID | FR ID | Provider | Consumer | Test ID | Status |
|---|---|---|---|---|---|
| FR-INT-001 | FR-WSP-006 | WorkspaceFileController | FileExplorer JS, AI tools | TC-WSP-004, TC-WSP-005 | Planned |
| FR-INT-002 | FR-WSP-010 | WorkspaceController | IDE Shell Blade | TC-WSP-008 | Planned |
| FR-INT-003 | FR-COL-002 | Reverb Events | Collab Extension, Blade | TC-COL-002, TC-COL-003 | Planned |
| FR-INT-004 | FR-AI-004 | AiSandbox / AiController | Patch Reviewer Extension | TC-AI-003 through TC-AI-006 | Planned |
| FR-INT-005 | FR-DEP-004 | DeployWorkspaceJob | Reverb, Student Dashboard | TC-DEP-004 | Planned |
| FR-INT-006 | FR-ADM-003 | AdminControllers | Audit Log Viewer | TC-NFR-003 | Planned |
| FR-INT-007 | FR-EXT-008 | Extension Build Pipeline | Extension Registry | TC-EXT-004 | Planned |
| FR-INT-008 | FR-OPS-002 | HealthController | UptimeRobot, CI/CD | TC-PROD-002 | Planned |
| FR-INT-009 | FR-PWA-005 | PushController | Service Worker | TC-PWA-005 | Planned |
| FR-INT-010 | FR-DEP-005 | DeploymentProvider | DeployWorkspaceJob | TC-DEP-005 | Planned |

---

## Non-Functional Requirements Traceability

| NFR ID | Description | HLD Component | Test ID | Phase | Status |
|---|---|---|---|---|---|
| NFR-001 | Health endpoint all dependencies <2s | Health/Observability | TC-PROD-002 | 12 | Planned |
| NFR-002 | No N+1 queries; SQL aggregates not PHP loops | Query/Cache Layer | TC-NFR-001 | 11 | Planned |
| NFR-003 | Response time benchmarks (per FRD) | Cache, Nginx, DB Indexes | TC-NFR-006 | 11 | Planned |
| NFR-004 | OWASP ASVS Level 2 coverage | Security Architecture | TC-SEC-001 through TC-SEC-014 | 11 | Planned |
| NFR-005 | Role-restricted analytics visibility | Analytics/Privacy | TC-ANL-002, TC-ANL-003 | 9 | Planned |
| NFR-006 | Audit trail completeness with correlation_id | Audit Layer | TC-NFR-003 | 11 | Planned |
| NFR-007 | WCAG 2.1 AA accessibility | UI Component Library | TC-NFR-004 | 11 | Planned |
| NFR-008 | Provider failure resilience | Provider Abstractions | TC-AI-009, TC-VID-005, TC-DEP-005 | 9 | Planned |
| NFR-009 | Recovery: backup, rollback, cleanup | Operational | TC-AI-006, TC-PROD-003, TC-NFR-005 | 12 | Planned |
| NFR-010 | Extension legal provenance | Extension Registry | TC-EXT-005 | 4 | Planned |
| NFR-011 | Cache invalidation via Model Observers | Cache Layer | TC-NFR-007 | 11 | Planned |
| NFR-012 | PWA Lighthouse ≥90 | PWA Layer | TC-PWA-006 | 10 | Planned |

---

## Evaluation Path Traceability

| Evaluation Scenario | BR Coverage | FR Coverage | Test ID |
|---|---|---|---|
| Administrator evaluates system health, users, quotas, extension policy, workspaces, audit logs, release evidence | BR-006, BR-011, BR-013 | FR-ADM-001 through FR-ADM-010, FR-OPS-001, FR-OPS-002 | TC-EVAL-001 |
| Instructor evaluates course, assignment, announcement, gradebook, live session, Analytics Dashboard analytics | BR-002, BR-007, BR-008 | FR-CLS-001 through FR-CLS-017, FR-VID-001, FR-ANL-003, FR-ANL-004 | TC-EVAL-002 |
| Student evaluates enrollment, workspace, file editing, AI patch, submission, feedback, deployment | BR-002, BR-003, BR-005, BR-009 | FR-CLS-002, FR-CLS-007, FR-CLS-008, FR-WSP-006, FR-AI-001, FR-DEP-001 | TC-EVAL-003 |
| Two-user collaboration evaluation: presence, cursor, chat, reconnect, unauthorized denial | BR-004 | FR-COL-001 through FR-COL-008 | TC-EVAL-004 |
| Reset and contingency check: accounts, seed reset, known risks, backup, fallback providers | BR-011 | FR-OPS-001 through FR-OPS-005 | TC-EVAL-005 |

---

## Coverage Gap Analysis

| Gap Category | Current Coverage | Required Action | Phase |
|---|---|---|---|
| VisionLab Agent old-identity scan evidence | Must be attached to extension_builds record | Execute full build pipeline; attach scan report | 4 |
| Backup restore rehearsal confirmation | Must be executed on staging before Phase 12 acceptance | Schedule restore rehearsal; document result | 12 |
| Two-user collaboration simulation | Must be verified in development or staging environment | Execute simulation with two browser sessions | 5 |
| WCAG 2.1 AA automated scan | Must cover all critical workflow screens | Run axe-core or similar on all major views | 11 |
| Provider failure state coverage | AI, video, push, deployment all need manual failure tests | Configure provider mocks for automated tests | 9 |
| ASVS negative test matrix | Every ASVS Level 2 category needs at least one negative test | Complete security test matrix | 11 |

---

## Traceability Maintenance Rules

1. When a new requirement is added, it must be assigned a BR, PR, and FR ID and immediately added to this RTM with Planned status
2. When a requirement changes scope, the RTM entry must be updated in the same pull request as the implementation change
3. When a test case is added for a requirement, its TC ID must be added to the RTM before the pull request is merged
4. When a requirement is deferred, the RTM entry must show Deferred status with a product-owner-approved justification note
5. The RTM is reviewed at every phase gate — the gate cannot pass if Must-priority items remain without complete FR, HLD, and test coverage

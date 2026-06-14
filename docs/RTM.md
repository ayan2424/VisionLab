# VisionLab Requirements Traceability Matrix

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Requirements Traceability Matrix |
| Version | 1.0 |
| Source Prompt | `PROMPTS.xml` version `2026.06-competitive-ultimate-independent-agent-fork` |

## Traceability Model

This RTM links business requirements to product requirements, functional requirements, design components, and test scenarios. It follows traceability practice described by IIBA: traceability supports scope, change, risk, time, cost, communication, and solution conformance.

Reference: [IIBA Trace Requirements](https://www.iiba.org/knowledgehub/business-analysis-body-of-knowledge-babok-guide/5-requirements-life-cycle-management/5-1-trace-requirements/)

## Status Lifecycle

| Status | Meaning |
|---|---|
| Planned | requirement is approved for implementation but not yet complete |
| In Progress | implementation has started and evidence is being gathered |
| Implemented | code and documentation are complete for the mapped scope |
| Verified | mapped tests and acceptance checks pass |
| Deferred | requirement is intentionally moved out of the current release with approval |
| Blocked | requirement cannot proceed until a named dependency is resolved |

## Matrix

| BRD ID | PRD ID | FRD ID | HLD Component | Test ID | Status |
|---|---|---|---|---|---|
| BR-001 | PRD-001 | FR-AUTH-001 | Auth/RBAC | TC-AUTH-001 | Planned |
| BR-001 | PRD-001 | FR-AUTH-002 | Auth/RBAC | TC-AUTH-002 | Planned |
| BR-001 | PRD-001 | FR-AUTH-003 | Auth/RBAC | TC-AUTH-003 | Planned |
| BR-001 | PRD-001 | FR-AUTH-004 | Policy Layer | TC-SEC-001 | Planned |
| BR-002 | PRD-002 | FR-CLS-001 | Classroom | TC-CLS-001 | Planned |
| BR-002 | PRD-002 | FR-CLS-002 | Classroom | TC-CLS-002 | Planned |
| BR-002 | PRD-002 | FR-CLS-003 | Classroom | TC-CLS-003 | Planned |
| BR-002 | PRD-002 | FR-CLS-006 | Classroom | TC-CLS-004 | Planned |
| BR-002 | PRD-002 | FR-CLS-008 | Classroom/Workspace | TC-CLS-005 | Planned |
| BR-002 | PRD-002 | FR-CLS-010 | Classroom | TC-CLS-006 | Planned |
| BR-003 | PRD-003 | FR-WSP-001 | Workspace Manager | TC-WSP-001 | Planned |
| BR-003 | PRD-003 | FR-WSP-002 | CodeServerManager | TC-WSP-002 | Planned |
| BR-003 | PRD-003 | FR-WSP-004 | Quota Resolver | TC-WSP-003 | Planned |
| BR-003 | PRD-003 | FR-WSP-006 | File API | TC-WSP-004 | Planned |
| BR-003 | PRD-003 | FR-WSP-007 | File Sandbox | TC-SEC-002 | Planned |
| BR-003 | PRD-003 | FR-WSP-009 | IDE Shell | TC-BR-001 | Planned |
| BR-004 | PRD-007 | FR-COL-001 | Reverb Channels | TC-COL-001 | Planned |
| BR-004 | PRD-007 | FR-COL-002 | Collaboration Events | TC-COL-002 | Planned |
| BR-004 | PRD-007 | FR-COL-005 | Collab Extension | TC-COL-003 | Planned |
| BR-004 | PRD-007 | FR-COL-006 | Chat Panel | TC-SEC-003 | Planned |
| BR-005 | PRD-006 | FR-AI-001 | AI Service | TC-AI-001 | Planned |
| BR-005 | PRD-006 | FR-AI-002 | Mode Matrix | TC-AI-002 | Planned |
| BR-005 | PRD-006 | FR-AI-004 | Patch Lifecycle | TC-AI-003 | Planned |
| BR-005 | PRD-006 | FR-AI-005 | Patch Reviewer | TC-AI-004 | Planned |
| BR-005 | PRD-006 | FR-AI-011 | AI Sandbox | TC-SEC-004 | Planned |
| BR-006 | PRD-004 | FR-EXT-001 | Extension Registry | TC-EXT-001 | Planned |
| BR-006 | PRD-004 | FR-EXT-002 | Immutable Extension Layer | TC-EXT-002 | Planned |
| BR-006 | PRD-004 | FR-EXT-005 | Marketplace Policy | TC-EXT-003 | Planned |
| BR-006 | PRD-005 | FR-EXT-008 | VisionLab Agent Fork | TC-EXT-004 | Planned |
| BR-006 | PRD-005 | FR-EXT-009 | License Governance | TC-EXT-005 | Planned |
| BR-007 | PRD-009 | FR-ANL-003 | VisionGuard | TC-ANL-001 | Planned |
| BR-007 | PRD-009 | FR-ANL-004 | Grading View | TC-ANL-002 | Planned |
| BR-008 | PRD-008 | FR-VID-001 | Video Service | TC-VID-001 | Planned |
| BR-008 | PRD-008 | FR-VID-002 | Video Auth | TC-VID-002 | Planned |
| BR-008 | PRD-008 | FR-VID-004 | Token Service | TC-SEC-005 | Planned |
| BR-009 | PRD-010 | FR-DEP-001 | Deployment Service | TC-DEP-001 | Planned |
| BR-009 | PRD-010 | FR-DEP-002 | Deployment UI | TC-DEP-002 | Planned |
| BR-009 | PRD-010 | FR-DEP-003 | Package Builder | TC-SEC-006 | Planned |
| BR-009 | PRD-010 | FR-DEP-004 | Deployment History | TC-DEP-003 | Planned |
| BR-010 | PRD-011 | FR-PWA-001 | Manifest | TC-PWA-001 | Planned |
| BR-010 | PRD-011 | FR-PWA-002 | Service Worker | TC-PWA-002 | Planned |
| BR-010 | PRD-011 | FR-PWA-004 | Push Controller | TC-PWA-003 | Planned |
| BR-011 | PRD-012 | FR-OPS-001 | CI/CD | TC-PROD-001 | Planned |
| BR-011 | PRD-012 | FR-OPS-002 | Health Endpoint | TC-PROD-002 | Planned |
| BR-011 | PRD-012 | FR-OPS-003 | Backup/Restore | TC-PROD-003 | Planned |
| BR-012 | PRD-005 | FR-EXT-009 | License and Provenance | TC-EXT-005 | Planned |

## Expanded Coverage Addendum

| Area | Additional FRD IDs | Primary Test Coverage |
|---|---|---|
| Cross-cutting authorization and validation | FR-XCUT-001, FR-XCUT-002, FR-XCUT-004, FR-XCUT-008 | TC-AUTH-004, TC-SEC-001, TC-SEC-002, TC-SEC-003 |
| Long-running operations | FR-XCUT-005, FR-XCUT-006 | TC-DEP-004, TC-DEP-005, TC-PROD-002 |
| Accessibility and user states | FR-XCUT-003, FR-XCUT-007, FR-XCUT-010 | browser workflow checks, accessibility checks |
| Workspace contracts | FR-INT-001, FR-INT-002 | TC-WSP-001 through TC-WSP-007 |
| Collaboration contracts | FR-INT-003 | TC-COL-001 through TC-COL-005 |
| AI patch contracts | FR-INT-004 | TC-AI-003 through TC-AI-009 |
| Deployment contracts | FR-INT-005, FR-INT-010 | TC-DEP-001 through TC-DEP-005 |
| Admin and health contracts | FR-INT-006, FR-INT-008 | TC-AUTH-005, TC-PROD-002, TC-PROD-005 |
| Extension build contracts | FR-INT-007 | TC-EXT-004, TC-EXT-005 |
| Notification contracts | FR-INT-009 | TC-PWA-004, TC-PWA-005 |

## Non-Functional Traceability

| NFR ID | Design Component | Test Evidence | Status |
|---|---|---|---|
| NFR-001 | Health and Observability | TC-PROD-002 | Planned |
| NFR-002 | Data Access and Performance | TC-NFR-001 | Planned |
| NFR-003 | Workspace Manager | TC-WSP-001, TC-WSP-007 | Planned |
| NFR-004 | Security Test Suite | TC-SEC-001, TC-SEC-002, TC-SEC-004 | Planned |
| NFR-005 | Privacy and Role Visibility | TC-ANL-002, TC-ANL-003, TC-NFR-002 | Planned |
| NFR-006 | Audit and Correlation | TC-NFR-003 | Planned |
| NFR-007 | Accessibility Layer | TC-NFR-004 | Planned |
| NFR-008 | Provider Abstractions | TC-AI-009, TC-VID-005, TC-DEP-005 | Planned |
| NFR-009 | Recovery and Rollback | TC-AI-006, TC-PROD-003, TC-NFR-005 | Planned |
| NFR-010 | Extension Provenance | TC-EXT-004, TC-EXT-005 | Planned |

## Coverage Rules

- Every business requirement must map to at least one product requirement.
- Every product requirement must map to at least one functional requirement.
- Every must-have functional requirement must map to at least one test case.
- Security-sensitive requirements must include at least one negative test.
- Requirements affecting data, API, jobs, or events must map to HLD components.

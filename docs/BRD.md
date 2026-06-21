# VisionLab Business Requirements Document
## Version 9.0 — Production-Grade Enterprise Edition

---

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Business Requirements Document (BRD) |
| Version | 9.0 |
| Prompt Pack | `PROMPTS.xml` v9.0 |
| Owner | Product and Delivery Leadership |
| Standard | IEEE 29148:2018 — Systems and Software Engineering Requirements |
| Status | Implementation-Ready Baseline |

---

## Executive Summary

VisionLab is a unified academic coding platform that eliminates tool fragmentation at universities by consolidating classroom management, browser-based IDE workspaces, live collaboration, governed AI assistance, video sessions, analytics, notifications, project deployment, and production operations into a single governed product.

The business objective is to deliver a credible, production-grade platform that can be evaluated through live end-to-end workflows and extended into an institutional product. Every business requirement maps to a verifiable product behavior, not a staged screen or placeholder.

---

## Business Problem

Universities operating coding education programs currently manage disconnected toolchains:

| Problem Domain | Current State | Business Cost |
|---|---|---|
| Course Management | Separate LMS (Google Classroom, Moodle) | Duplicate effort, inconsistent governance |
| Live Coding Environment | Local IDE setup per student | Significant friction, support overhead |
| Live Communication | Separate video tool (Zoom, Teams) | Context switching, no workspace integration |
| AI Assistance | Unmanaged Copilot/ChatGPT usage | No visibility, no control, academic integrity risk |
| Code Collaboration | Screen sharing or pair-coding tools | Outside the coding context |
| Submission Review | File uploads, no workspace context | Loss of code history, no AI attribution |
| Analytics | Manual tracking across tools | Delayed insight, no forensic capability |

This fragmentation creates operational overhead, weak governance, invisible AI usage patterns, inconsistent student experience, and poor instructor visibility into the learning process.

---

## Business Objectives

| ID | Objective | Outcome | Priority |
|---|---|---|---|
| BO-01 | Consolidate academic coding workflows | Eliminate platform-switching overhead for students and instructors | Critical |
| BO-02 | Provide governed browser IDE access | Remove local environment friction; enforce institutional policy on coding environments | Critical |
| BO-03 | Enable transparent, responsible AI learning | Instructors see AI contribution; all AI writes require human approval | Critical |
| BO-04 | Enable live collaborative coding | Students and instructors code together in real-time within the same platform | High |
| BO-05 | Enforce institutional governance | Administrators control extensions, quotas, users, marketplace access, and audit trails | Critical |
| BO-06 | Demonstrate production maturity | Release evidence, CI/CD, health checks, backups, and runbooks prove operational readiness | Critical |
| BO-07 | Support student career development | One-click deployment to live production URLs demonstrates real project completions | Medium |
| BO-08 | Surface learning analytics | Role-restricted dashboards show activity, engagement, AI usage, and forensic attribution | High |

---

## Business Value Chain

| Stage | Current Pain | VisionLab Value |
|---|---|---|
| Course Setup | Instructors configure multiple disconnected tools per course | One course record controls assignments, workspaces, extensions, video, analytics, and all people |
| Student Onboarding | Local IDE installation consumes class time and causes support tickets | Browser IDE workspace opens instantly with institutional configuration pre-applied |
| Assignment Distribution | File-based distribution loses starter context | Assignments include starter code, workspace templates, and policy-configured environments |
| Learning Support | AI usage is invisible and uncontrolled | AI assistance is governed, auditable, mode-aware, and requires instructor-visible patch approval |
| Live Collaboration | Help happens outside the coding context via screen share | Multiplayer cursor sync, chat, document sync, and video run inside the same workspace |
| Submission & Assessment | Submissions lack technical context and AI transparency | Snapshots preserve workspace state; Analytics Dashboard shows human vs AI contribution with confidence |
| Administration | Governance requires access to multiple disconnected systems | Admin panel controls users, quotas, extensions, workspaces, audit logs, and system health |
| Production Evidence | Projects exist only locally | Governed one-click deployment produces live public URLs for student portfolios |

---

## Stakeholders

| Stakeholder | Role | Success Need |
|---|---|---|
| Students | Primary users and learners | Reliable IDE, responsive assignments, collaborative tools, transparent AI, portfolio deployment |
| Instructors | Course creators and evaluators | Full course control, real-time observation, Analytics Dashboard, video sessions, grading efficiency |
| Administrators | Governance and operations | User management, workspace quotas, extension governance, audit trails, system health visibility |
| Engineering Team | Platform builders | Clear architecture contracts, testable requirements, traceable implementation plan |
| Security Reviewers | Risk management | ASVS mapping, AI sandbox verification, container hardening evidence, audit coverage |
| Institutional Leadership | Strategic stakeholders | Platform credibility, operational maturity, legal compliance for open-source components |

---

## Business Scope

### In Scope

| Capability | Scope Notes |
|---|---|
| Laravel 11 LMS | Full course/assignment/submission/grading/announcement lifecycle |
| Browser IDE Workspaces | code-server containers with authenticated routing, quota enforcement, file sandbox |
| Extension Governance | Registry, checksums, immutable required extensions, marketplace policy, VisionLab Agent fork |
| Real-Time Collaboration | Reverb presence, cursor sync, document sync, chat, reconnect resilience |
| Governed AI Agent | Chat/plan/agent modes, patch approval, snapshots, rollback, audit trail, token budget |
| Video Sessions | Jitsi provider abstraction, JWT tokens, room lifecycle, workspace integration |
| Admin Operations | User, workspace, extension, quota, audit log, system health management |
| Analytics & Forensics | Event taxonomy, role-restricted dashboards, Analytics Dashboard attribution, gamification |
| Student Deployment | Provider-abstracted async deployment with confirmation, package exclusions, status history |
| PWA & Notifications | Manifest, Workbox service worker, VAPID push, Background Sync |
| Production Infrastructure | Docker Compose, Nginx TLS, GitHub Actions CI/CD, health endpoints, backups, runbooks |

### Explicitly Out of Scope

| Item | Reason |
|---|---|
| Offline IDE editing | code-server requires live container connectivity |
| Unrestricted AI file mutation | Violates the human-in-the-loop governance model |
| Public unauthenticated workspaces | Security architecture requires authenticated proxy |
| Microsoft VS Code Marketplace dependency | code-server uses its own extension infrastructure |
| Student mutation of global extension policy | Policy authority belongs to admin/instructor roles only |
| Automatic upstream AI extension updates | VisionLab-controlled fork releases prevent supply-chain risk |
| Production support beyond documented runbooks | Out of project scope unless separately contracted |

---

## Business Requirements

| ID | Requirement | Priority | Verification Method |
|---|---|---|---|
| BR-001 | The product shall support admin, instructor, and student roles with differentiated dashboards, actions, and data visibility | Must | Feature + Security tests |
| BR-002 | The product shall support full course, assignment, submission, grading, and announcement workflows end-to-end | Must | Feature + Browser tests |
| BR-003 | The product shall provide secure browser IDE workspaces with quota enforcement, file sandboxing, and authenticated routing | Must | Feature + Security tests |
| BR-004 | The product shall enable real-time collaborative coding through presence, cursor sync, document sync, and chat | Must | Browser + Security tests |
| BR-005 | The product shall provide responsible AI assistance where all file mutations require explicit human approval | Must | Feature + Security tests |
| BR-006 | The product shall allow administrators to govern extensions, marketplace access, workspace quotas, and user accounts | Must | Feature + Security tests |
| BR-007 | The product shall surface AI contribution transparency to instructors through Analytics Dashboard | Must | Feature + Browser tests |
| BR-008 | The product shall support live video sessions in workspaces through a configurable provider abstraction | Should | Feature + Manual tests |
| BR-009 | The product shall support student project deployment through a governed, confirmed, auditable provider flow | Should | Feature + Security tests |
| BR-010 | The product shall support PWA installation and push notifications as a progressive enhancement layer | Should | Browser tests |
| BR-011 | The product shall include production deployment infrastructure, health monitoring, backups, and operational runbooks | Must | Operational tests |
| BR-012 | The product shall preserve all legally required license notices for any imported open-source code | Must | Legal/operational review |
| BR-013 | The product shall maintain an immutable audit trail for all sensitive operations including AI actions, extension changes, user management, and workspace lifecycle events | Must | Security + Feature tests |
| BR-014 | The product shall enforce token budget limits per user role for AI usage to prevent abuse and control costs | Must | Feature tests |
| BR-015 | The product shall use feature flags to control high-risk capabilities independently of deployment | Must | Feature + Operational tests |

---

## Business Rules

| ID | Rule | Enforcement |
|---|---|---|
| BRule-001 | Students only access enrolled courses and workspaces they own or have been granted collaborator access to | WorkspacePolicy, CoursePolicy, EnrollmentPolicy |
| BRule-002 | Instructors manage only courses they created unless administrator permissions apply | CoursePolicy |
| BRule-003 | All AI-generated file mutations require stored patch record and explicit human approval before application | ai_pending_patches lifecycle, no exceptions except memory file |
| BRule-004 | Required extensions (VisionLab Agent, Collab, Patch Reviewer) cannot be removed or modified by any workspace user | Immutable global directory, chmod 555, verified at container start |
| BRule-005 | Sensitive extension artifacts must be built from VisionLab-controlled source with SHA256 checksum verification | extension_builds record, ChecksumVerificationService |
| BRule-006 | Student project deployment requires explicit public-exposure confirmation before the deployment job is queued | DeploymentController confirmation gate, x-confirm-dialog |
| BRule-007 | Service workers must not cache authenticated API responses, workspace/IDE routes, or admin pages | Workbox NetworkOnly for /api/* and /workspace/* routes |
| BRule-008 | Production releases must include documented evidence: migration state, test summary, security matrix, backup confirmation, and rollback plan | Phase 12 release evidence package |
| BRule-009 | A user account cannot be demoted from administrator if they are the last active administrator | UserPolicy::update, validated in AdminUserController |
| BRule-010 | All AI tool calls that access workspace files must first verify the resolved path falls within the canonical workspace root | AiSandbox realpath() validation on every tool invocation |
| BRule-011 | Extension artifact integrity is verified by SHA256 checksum before every container installation, not only at first registration | ChecksumVerificationService runs in CodeServerManager::startWorkspace |
| BRule-012 | All analytics and forensics data is role-restricted: students see their own data, instructors see their courses' data, admins see platform-level data | AnalyticsPolicy, role-scoped queries |

---

## Success Metrics & KPIs

| KPI | Target | Measurement Method |
|---|---|---|
| End-to-end workflow completion | Complete without code changes or database edits | Manual evaluation path walkthrough |
| Workspace launch reliability | Containers start or recover with truthful status in configured environment | Automated workspace lifecycle tests |
| AI governance | Zero file mutations bypass patch approval and audit log | Negative test suite: AI forbidden path, unapproved mutation |
| Extension integrity | Zero installations from tampered artifacts | Checksum verification test with deliberately mismatched hash |
| Admin governance | All quota, extension, user, workspace, and audit workflows available from live screens | Manual admin evaluation path |
| Traceability completeness | Every must-have BR maps to FR, HLD component, and test evidence | RTM review |
| Release evidence | Deployment explainable through CI, health, backup, rollback artifacts | Phase 12 checklist review |
| Security coverage | All ASVS Level 2 categories have mapped controls and negative tests | Security matrix review |
| Legal provenance | All imported extension code has documented source, license, attribution, artifact history | Extension builds table review |
| Test pass rate | 100% of must-have tests pass on fresh migration + seed | CI/CD test stage output |

---

## Business Risks & Mitigations

| Risk ID | Risk | Probability | Impact | Mitigation |
|---|---|---|---|---|
| BR-RSK-01 | AI agent excessive agency bypasses approval | Low | Critical | ai_pending_patches lifecycle, AiSandbox safety filters, negative test suite |
| BR-RSK-02 | Container escape or cross-workspace file access | Low | Critical | Docker security flags, isolated network, path sandboxing, file API canonical checks |
| BR-RSK-03 | Extension supply chain compromise | Low | High | Source builds from VisionLab fork, SHA256 verification, registry, old-identity scan |
| BR-RSK-04 | Service worker caching sensitive content | Medium | High | Workbox NetworkOnly for API/IDE routes, browser application panel verification |
| BR-RSK-05 | Deployment package leaking secrets | Low | High | Package exclusion list, .env blocked, dependency directories excluded |
| BR-RSK-06 | Legal exposure from imported extension code | Low | High | License review before import, attribution preservation, VisionLab-controlled fork |
| BR-RSK-07 | Production environment failure during evaluation | Medium | High | Preflight checklist, backup restore rehearsal, contingency provider modes |
| BR-RSK-08 | Token budget exhaustion causing AI service disruption | Medium | Medium | Per-role daily budget enforcement, Redis-cached usage tracking, 429 response with reset time |
| BR-RSK-09 | Stale workspace containers consuming server resources | Medium | Medium | Heartbeat monitoring, 30-minute stale cleanup job, daily WorkspaceCleanup command |

---

## Business Acceptance Evidence

| Evidence Category | Required Artifacts |
|---|---|
| Functional Evidence | Completed end-to-end workflows for student, instructor, and administrator roles on live screens |
| Security Evidence | Authorization denial tests, AI tool denial tests, file sandbox tests, extension policy tests, header verification |
| Operational Evidence | Health endpoint output, queue/scheduler status, backup restore rehearsal record, release manifest |
| Legal Evidence | Imported extension source reference, license review record, attribution file preservation confirmation, artifact checksum history |
| Performance Evidence | No avoidable N+1 queries on critical pages, pagination on growing lists, Redis caching on dashboard aggregation |
| Accessibility Evidence | Critical workflows pass keyboard, label, contrast, focus, and responsive layout checks |

---

## Business-Level Acceptance Definition

The business release is accepted when ALL of the following are true:

1. All Must-priority business requirements (BR-001 through BR-013) are implemented and traced in the RTM to tests that pass
2. The evaluation path works from login through classroom, workspace, collaboration, AI patch, submission, and deployment on live application screens
3. Administrator governance (users, quotas, extensions, audit logs, health) is available and functional
4. The VisionLab Agent build report confirms source audit, clean compile, checksum registration, and old-identity scan result
5. Legal provenance for all imported open-source extension code is preserved and documented
6. Production infrastructure (Docker, Nginx, CI/CD, health checks, backups) is operational
7. Security verification script passes all automated checks
8. No Must-priority tests have open Critical or High severity defects

# VisionLab Business Requirements Document

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Business Requirements Document |
| Version | 1.0 |
| Source Prompt | `PROMPTS.xml` version `2026.06-competitive-ultimate-independent-agent-fork` |
| Owner | Product and delivery leadership |

## Executive Summary

VisionLab is a unified academic coding platform that reduces tool fragmentation for universities. It combines classroom workflows, browser IDE workspaces, live collaboration, AI-assisted learning, video sessions, analytics, notifications, project deployment, and production operations.

The business objective is to produce a credible, competition-ready, production-grade platform that can be evaluated through real workflows and later extended into an institutional product.

## Business Problem

Universities commonly use disconnected tools for:

- Course management.
- Assignment distribution.
- Live communication.
- Coding environments.
- AI assistance.
- Submission review.
- Student analytics.

This creates operational overhead, inconsistent governance, weak visibility into AI use, and a poor learning experience.

## Business Objectives

| ID | Objective | Business Outcome |
|---|---|---|
| BO-01 | Consolidate academic coding workflows | Reduce operational fragmentation |
| BO-02 | Improve student coding access | Browser IDE removes local setup friction |
| BO-03 | Enable responsible AI learning | Instructors see AI contribution and patch approval |
| BO-04 | Improve collaboration | Students and instructors code together live |
| BO-05 | Increase governance | Admins control users, extensions, quotas, and audit logs |
| BO-06 | Prove production maturity | Release evidence, security checks, CI/CD, backups, health checks |
| BO-07 | Win evaluator confidence | Live workflows demonstrate real product depth |

## Business Value Chain

| Step | Current Pain | VisionLab Business Value |
|---|---|---|
| Course setup | Instructors duplicate setup across multiple tools | One course model controls assignments, people, announcements, workspaces, and analytics |
| Student onboarding | Students lose time configuring local environments | Browser IDE workspaces reduce local setup friction |
| Learning support | AI use is opaque or unmanaged | AI help is visible, governed, auditable, and tied to patch approval |
| Collaboration | Live help happens outside the coding context | Presence, chat, cursor sync, video, and workspace state live in the same product surface |
| Assessment | Grading lacks technical context and AI transparency | Submissions preserve snapshots, forensics, history, and instructor feedback |
| Operations | Labs are hard to govern at scale | Admin controls quotas, extensions, workspaces, roles, audit logs, and health |
| Evaluation | Products often rely on staged screens | VisionLab is accepted through working end-to-end workflows and release evidence |

## Stakeholders

| Stakeholder | Interest | Success Need |
|---|---|---|
| Students | Learning and completing assignments | Reliable IDE, assignments, collaboration, AI help |
| Instructors | Teaching, grading, oversight | Course tools, grading, VisionGuard, live sessions |
| Administrators | Governance and operations | User, workspace, extension, quota, security controls |
| Evaluators | Product credibility | Real workflows, polish, operational evidence |
| Engineering team | Maintainability | Clear architecture, tests, traceability |
| Security reviewers | Risk control | ASVS mapping, audit logs, AI safeguards |

## Decision Rights

| Decision Area | Accountable Role | Required Evidence |
|---|---|---|
| Product scope | Product Owner | BRD/PRD impact, delivery risk, acceptance impact |
| Security controls | Security Engineer | ASVS/LLM risk mapping, denial tests, audit evidence |
| Architecture | Lead Engineer | HLD update, ADR entry, migration or integration impact |
| Extension provenance | Extension Engineer and Security Engineer | source reference, license review, build logs, checksum |
| Production release | DevOps Engineer and Product Owner | CI result, health result, backup result, rollback plan |
| Evaluation readiness | Product Owner and QA Engineer | test summary, preflight checklist, seeded evaluation account status |

## Business Scope

### In Scope

- Laravel 11 web platform.
- MySQL-backed classroom system.
- code-server workspace lifecycle.
- Custom/rebranded VisionLab extension ecosystem.
- Real-time collaboration through Laravel Reverb.
- Responsible AI agent with patch approval.
- Jitsi provider abstraction for video.
- Admin operations and governance.
- Analytics, VisionGuard, gamification, and student deployment.
- PWA, push notifications, offline fallback.
- CI/CD, deployment, observability, backups, runbooks.

### Out of Scope

- Offline IDE editing.
- Unrestricted AI mutation.
- Public unauthenticated workspaces.
- Microsoft VS Code Marketplace dependency.
- Student control of global extension policy.
- Automatic upstream AI extension updates after the initial compliant import.

## Business Requirements

| ID | Requirement | Priority |
|---|---|---|
| BR-001 | The product shall support student, instructor, and administrator roles. | Must |
| BR-002 | The product shall support full course, assignment, submission, grading, and announcement workflows. | Must |
| BR-003 | The product shall provide secure browser IDE workspaces for assignments. | Must |
| BR-004 | The product shall enable live collaboration in workspaces. | Must |
| BR-005 | The product shall provide responsible AI help with human-approved patches. | Must |
| BR-006 | The product shall let administrators govern extensions, marketplace access, and workspace quotas. | Must |
| BR-007 | The product shall provide instructor-visible AI contribution transparency. | Must |
| BR-008 | The product shall support live video sessions in workspaces. | Should |
| BR-009 | The product shall support student project deployment through governed providers. | Should |
| BR-010 | The product shall support PWA installation and push notifications. | Should |
| BR-011 | The product shall include production deployment, monitoring, backups, and runbooks. | Must |
| BR-012 | The product shall preserve legal compliance for open-source imported code. | Must |

## Business Rules

| ID | Rule |
|---|---|
| BRule-001 | Students only access enrolled courses and authorized workspaces. |
| BRule-002 | Instructors manage only their own courses unless administrator permissions apply. |
| BRule-003 | AI-generated file mutations require human approval. |
| BRule-004 | Required extensions cannot be removed by students. |
| BRule-005 | Sensitive extension artifacts must be source-built and verified. |
| BRule-006 | Student deployment requires public-exposure confirmation. |
| BRule-007 | Service workers must not cache sensitive authenticated APIs or IDE routes. |
| BRule-008 | Production releases must include evidence: tests, migration state, security checks, and rollback plan. |

## Success Metrics

| KPI | Measurement |
|---|---|
| Course workflow completion | Instructor creates course and assignment; student submits; instructor grades |
| Workspace success | Authorized student launches and uses workspace |
| AI safety | All AI file changes pass through pending patch and approval |
| Governance | Admin can change extension and quota policy with audit trail |
| Evaluation readiness | Preflight checklist passes without manual emergency fixes |
| Security | Critical unauthorized access tests pass |

## Business KPIs and Acceptance Thresholds

| KPI | Acceptance Threshold |
|---|---|
| End-to-end evaluation workflow | completed without code changes or manual database edits |
| Workspace launch reliability | configured environment launches or recovers workspaces consistently with truthful status |
| AI governance | no file mutation bypasses patch approval and audit logging |
| Admin governance | quota, extension, user, workspace, and audit workflows are available from real admin screens |
| Traceability | every must-have business requirement maps to product, functional, design, and test evidence |
| Release evidence | deployment can be explained through CI, health, backup, rollback, and runbook artifacts |
| Legal provenance | imported extension code has source, license, attribution, and artifact history recorded |

## Constraints

- Laravel 11 and MySQL 8 are baseline technology decisions.
- code-server workspaces require container support.
- AI providers require server-side secrets and rate limits.
- PWA push requires browser support and secure contexts.
- Imported open-source code must keep required license notices.

## Business Risks

| Risk | Impact | Mitigation |
|---|---|---|
| Scope overload | Delivery delay | Phase-based delivery and traceability matrix |
| AI safety failure | Security and trust issue | Approval workflow, audit logs, denied tools |
| Container exposure | Security breach | Authenticated proxy, network restrictions |
| Extension supply chain | Malicious code risk | source builds, checksums, provenance |
| Evaluation environment failure | Poor demonstration | preflight, reset, backup workflows |

## Business Acceptance Evidence

| Evidence Type | Required Content |
|---|---|
| Functional evidence | completed workflows for student, instructor, and administrator roles |
| Security evidence | authorization denial tests, AI tool denial tests, file sandbox tests, extension policy tests |
| Operational evidence | health endpoint result, queue/scheduler status, backup restore rehearsal, release manifest |
| Legal evidence | imported source provenance, license notice preservation, artifact checksum history |
| Evaluation evidence | prepared accounts, reset process, known risks, contingency notes |

## Acceptance at Business Level

The business release is accepted when:

- All must-have business requirements are implemented and traced to tests.
- Evaluation path works from login to classroom to workspace to collaboration to AI patch to submission.
- Admin governance and production readiness can be shown from real screens.
- Legal provenance for imported extension code is preserved.

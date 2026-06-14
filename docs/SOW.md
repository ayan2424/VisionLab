# VisionLab Statement of Work

## Document Control

| Field | Value |
|---|---|
| Product | VisionLab |
| Document | Statement of Work |
| Version | 1.0 |
| Source Prompt | `PROMPTS.xml` version `2026.06-competitive-ultimate-independent-agent-fork` |

## SOW Purpose

This Statement of Work defines the scope, deliverables, phases, responsibilities, acceptance criteria, exclusions, and change-control approach for implementing VisionLab.

It follows professional SOW practice by making scope and acceptance explicit, consistent with PMI guidance that a SOW should define objectives, scope, tasks, deliverables, responsibilities, and acceptance criteria.

Reference: [PMI Statement of Work](https://www.pmi.org/learning/library/statement-work-delivering-successful-service-projects-4761)

## Project Objectives

- Deliver a production-ready Laravel 11 academic coding platform.
- Implement classroom, workspace, collaboration, AI, video, analytics, PWA, and deployment capabilities.
- Provide administrator governance over users, workspaces, extensions, quotas, audit logs, and production health.
- Produce verifiable release evidence through tests, CI/CD, health checks, backups, and runbooks.
- Prepare the product for professional evaluation through real, working workflows.

## Scope of Work

### In Scope

1. Product foundation and RBAC.
2. Classroom workflows.
3. code-server workspace lifecycle and secure file APIs.
4. Extension ecosystem and VisionLab Agent fork.
5. Real-time collaboration.
6. Responsible AI patch workflow.
7. Video session integration.
8. Admin operations.
9. Analytics, VisionGuard, gamification, student deployment.
10. PWA and push notifications.
11. Security, testing, performance, accessibility.
12. Production deployment, observability, backup, release evidence.

### Out of Scope

- Offline IDE editing.
- Unrestricted AI writes.
- Public unauthenticated code-server containers.
- Student mutation of global extension and container policy.
- Automatic upstream AI extension dependency after source import.
- Production support beyond the runbooks unless separately contracted.

## Work Breakdown Structure

| WBS | Work Package | Major Outputs |
|---|---|---|
| 1.0 | Foundation | Laravel baseline, auth, RBAC, design system, entity schema, audit convention |
| 2.0 | Classroom | courses, enrollments, assignments, submissions, grading, announcements, dashboards |
| 3.0 | Workspace | workspace manager, code-server orchestration, file sandbox, IDE shell, quota handling |
| 4.0 | Extensions | registry, artifact pipeline, immutable required extensions, marketplace policy, VisionLab Agent fork |
| 5.0 | Collaboration | Reverb authorization, presence, cursor sync, document events, chat, reconnect behavior |
| 6.0 | AI | provider abstraction, modes, tool sandbox, patch lifecycle, snapshots, rollback, audit trail |
| 7.0 | Video | provider abstraction, room records, tokens, join/end flows, workspace integration |
| 8.0 | Admin | user, workspace, extension, quota, security, audit, notification, health governance |
| 9.0 | Analytics and Deployment | event taxonomy, VisionGuard, badges, streaks, deployment provider flow |
| 10.0 | PWA and Notifications | manifest, service worker, offline fallback, push, preferences, notification routing |
| 11.0 | Hardening | ASVS matrix, rate limits, tests, performance, accessibility, failure handling |
| 12.0 | Production | Docker topology, Nginx/TLS, CI/CD, backups, monitoring, runbooks, preflight |

## Deliverables

| ID | Deliverable | Description | Acceptance |
|---|---|---|---|
| D-01 | Foundation | Laravel app, auth, RBAC, design system, schema, policies | Auth and role tests pass |
| D-02 | Classroom | Courses, enrollments, assignments, submissions, grading, announcements | Instructor/student workflows pass |
| D-03 | Workspace IDE | Workspace lifecycle, code-server, file APIs, IDE shell | Workspace and file security tests pass |
| D-04 | Extension Governance | Extension registry, artifact pipeline, immutable installs, marketplace policy | Required extensions immutable |
| D-05 | VisionLab Agent | Independent fork, source rebuild, proxy config, artifact registry | Build and install report accepted |
| D-06 | Collaboration | Reverb channels, presence, cursor, chat, extension modules | Two-user workflow passes |
| D-07 | AI Workflow | Modes, tools, patch review, snapshots, rollback, audit logs | AI denial and approval tests pass |
| D-08 | Video | Jitsi provider abstraction, room lifecycle, tokens | Authorized join and end pass |
| D-09 | Admin | User, workspace, extension, quota, audit, health operations | Admin tests pass |
| D-10 | Analytics | Event taxonomy, dashboards, VisionGuard, badges, heatmap | Role visibility tests pass |
| D-11 | Deployment | Student deployment provider flow and history | Provider mock tests pass |
| D-12 | PWA | Manifest, service worker, offline fallback, push | Browser checks pass |
| D-13 | Hardening | ASVS matrix, rate limits, tests, performance, accessibility | Security matrix complete |
| D-14 | Production | Docker, proxy, CI/CD, health, backups, runbooks | Preflight passes |

## Milestones

| Milestone | Phase | Completion Criteria |
|---|---|---|
| M1 | Phase 1 | App foundation, auth, schema, design system ready |
| M2 | Phase 2 | Classroom workflows complete |
| M3 | Phase 3 | Workspace IDE secure and operational |
| M4 | Phase 4 | Extension governance and VisionLab Agent pipeline complete |
| M5 | Phase 5 | Collaboration working |
| M6 | Phase 6 | AI patch workflow operational |
| M7 | Phase 7 | Video sessions integrated |
| M8 | Phase 8 | Admin operations complete |
| M9 | Phase 9 | Analytics, forensics, gamification, deployment complete |
| M10 | Phase 10 | PWA and notifications complete |
| M11 | Phase 11 | Security, tests, performance complete |
| M12 | Phase 12 | Production deployment and evaluation readiness complete |

## Quality Gates

| Gate | Required Before Passing |
|---|---|
| Phase gate | phase scope implemented, tests run, risks documented, next dependency confirmed |
| Security gate | authorization, denial paths, file sandbox, AI tool restrictions, and audit logs verified |
| Extension gate | source audit, legal review, clean rebuild, checksum, install smoke test, old-identity scan |
| Release gate | CI green, migrations verified, health endpoint passing, backup restore rehearsed, rollback documented |
| Evaluation gate | accounts prepared, reset process tested, critical workflows rehearsed on the target environment |

## Roles and Responsibilities

| Role | Responsibilities |
|---|---|
| Product Owner | Scope decisions, acceptance, prioritization |
| Laravel Engineer | Backend, migrations, controllers, policies, jobs |
| Frontend Engineer | Blade, Tailwind, JS, PWA, accessibility |
| Extension Engineer | VisionLab Agent, collaboration extension, patch reviewer |
| DevOps Engineer | Docker, Nginx, CI/CD, health checks, backups |
| QA Engineer | Test plan, automation, UAT, regression |
| Security Engineer | ASVS matrix, threat model, AI controls, container controls |

## Assumptions

- Laravel 11 and MySQL 8 are fixed baseline technologies.
- Docker-compatible runtime is available for workspace execution.
- AI provider credentials are supplied through secure environment variables.
- Video and deployment providers are configurable by environment.
- Evaluation data is separate from production behavior.
- Required open-source license notices are preserved.

## Dependencies

- PHP and Node toolchains.
- MySQL, Redis, queue workers, scheduler.
- code-server base image.
- Laravel Reverb.
- Jitsi or configured video provider.
- AI provider.
- Push notification keys.
- Deployment provider credentials.
- CI/CD secrets and target infrastructure.

## Acceptance Process

Each phase must provide:

- Implemented features.
- Migration status.
- Test results.
- Security denial tests.
- Browser/manual verification where relevant.
- Operational notes.
- Known risks.
- Next-phase readiness.

## Change Control

Changes that affect scope, security posture, public APIs, database schema, external providers, release schedule, or legal/license compliance require product owner approval and documented impact analysis.

## Definition of Completion

The project is complete when all must-have requirements are implemented, tests pass, production preflight passes, release evidence exists, and the evaluation workflow can be performed end to end using real product screens.

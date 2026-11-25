# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **University CS & Engineering Students:** Write, run, and submit code inside browser-based Nix + Docker `code-server` workspaces across daytime lecture halls/labs and late-night personal coding sessions; track courses, assignments, grades, streaks, XP, and AI-assisted patch proposals.
- **Instructors & Teaching Assistants:** Author courses and coding assignments, manage enrollment rosters (including CSV imports), review student submissions and coding forensics, govern course-level IDE extensions, and grade at scale.
- **University IT & Academic Administrators:** Provision user accounts (public self-registration is disabled), monitor live workspace containers and quotas, manage extension SHA256 supply-chain registry, inspect security audit logs and system health, and operate campus ERP modules (campuses, departments, batches, employees, fee challans, library).
- **Prospective University Decision-Makers (Deans, Department Chairs, IT Directors):** Evaluate VisionLab on public pages to decide whether to request a campus pilot.

## Product Purpose

VisionLab consolidates a university's fragmented coding-education stack into one self-hosted, enterprise-governed web platform:
1. **LMS & Classroom Operations** (courses, assignments, submissions, bulk grading, announcements, progress tracking)
2. **Immutable Cloud IDE Workspaces** (sovereign source-compiled `code-server` running inside hardened Docker + Nix `dev.nix` containers)
3. **Human-in-the-Loop AI Coding Governance** (Anthropic Claude agent operating via `ai_pending_patches` — students review and explicitly approve/reject every code diff, with full audit telemetry)
4. **Institute ERP & Administration** (campuses, departments, batches, HR, fee challans, library, quotas, extension supply-chain control)

Success for this redesign means unifying all five fragmented surfaces (Homepage/Public Marketing, Docs, Auth, Student/Instructor LMS + Workspace IDE Shell, and Admin/ERP Console) into a single unmistakable product identity that works cleanly under both bright fluorescent classroom projectors and dark late-night coding labs.

## Positioning

Unlike consumer AI editors (Cursor, Copilot) that quietly mutate code or standalone LMS portals (Google Classroom, Canvas) that leave students wrestling with local setup, VisionLab pairs reproducible Nix-backed Docker browser IDEs with a strictly governed, human-approved AI patch pipeline and academic forensics inside one university-controlled platform.

## Operating Context

- **Dual Physical Lighting Scene:** Used daily in two contrasting physical environments — bright university lecture halls, campus computer labs, and classroom projectors during daytime sessions, AND dimly lit dorm rooms or late-night engineering labs. The interface must perform effortlessly in both Light and Dark modes without losing hierarchy, contrast, or code legibility.
- **Primary Conversion Reality:** Public self-registration is intentionally disabled (`GET /register` redirects to `/login`); student and faculty accounts are provisioned by administrators or CSV roster imports. Public marketing pages must direct existing campus members to **Sign in to University Portal** (`/login`) and prospective institutions to **Request Campus Pilot** (`/contact`) — never to a dead registration route.
- **Technical Runtime Constraints:** Server-rendered Laravel 11 Blade templates, Tailwind CSS v3 + Vite 6, Alpine.js v3, Vanilla ES2022 JS (no jQuery, no React SPA rewrite), global `<x-frontend-header />` and `<x-frontend-footer />` across all public pages (`AGENTS.md` Directive #9), and full-viewport iframe hosting of sovereign `code-server` inside `resources/views/workspace.blade.php` without stealing vertical workspace height or breaking native IDE components.

## Capabilities and Constraints

- **Implemented & Active Subsystems:** Authentication & RBAC (`admin`, `instructor`, `student`), Courses/Enrollments/Assignments/Submissions/Grading/Announcements, Docker + Nix Workspaces & File I/O API, SHA256 Extension Registry, Anthropic Claude SSE Agent + `ai_pending_patches` Diff Reviewer, Analytics/Forensics/Gamification (XP, streaks, 365-day heatmap, badges), PWA + VAPID Web Push, Admin Governance + 6 ERP modules.
- **Decommissioned Subsystems (Do NOT advertise as active live features):** Jitsi video rooms, recordings, student Vercel/Railway deployments, and multi-user chat/collab rooms had their database tables permanently dropped in migration `2026_07_13_000000_remove_incomplete_features_tables.php`. Public copy and UI navigation must reflect real active capabilities truthfully without fabricating removed features.
- **Functional Shell Gaps to Resolve in Redesign:** Replace decorative non-functional topbar search with real scoped search/command navigation, remove empty student notification slot or wire it to real unread notifications, replace hardcoded "All Systems Operational" HTML in `admin/dashboard.blade.php` with real health data, and eliminate inline per-view `<style>` overrides and DOM `onmouseover` hacks.

## Brand Commitments

- **Brand Name:** VisionLab
- **Logo Mark:** Preserve the existing geometric `VL >_` ribbon mark (`public/icons/logo.svg`) geometry and adapt its color fill/treatment harmoniously to the new visual system across Light and Dark themes.
- **Global Public Chrome:** Maintain single-source `<x-frontend-header />` and `<x-frontend-footer />` components shared across all public pages.

## Evidence on Hand

- Existing brand logo SVGs: `public/icons/logo.svg`, `letterpress-dark.svg`, `letterpress-light.svg`.
- Real codebase architecture, routes, and 90+ Blade views mapped under `.planning/codebase/`.
- Real platform capabilities verifiable in code: Docker `crun` hardened containers, Nix `dev.nix` environments, `ai_pending_patches` HITL diff workflow, SHA256 `.vsix` verification, 365-day contribution heatmap, 10 achievement badges.
- **Absences (Never Fabricate):** No verified external university customer logos, press quotes, SLA percentages ("99.99% uptime"), or student headcount metrics ("50,000+ students") are on hand. Never invent testimonials, institutional logos, or unverified metrics; label any illustrative IDE/LMS demo data clearly as synthetic.

## Product Principles

1. **One Platform, Mode-Right Intensity:** Marketing persuades boldly, Documentation reads effortlessly, and LMS/IDE/Admin surfaces stay calm, dense, and instrument-sharp — all unmistakably belonging to VisionLab.
2. **Truth Over Theater:** Every CTA, status indicator, search bar, and metric reflects real working functionality — zero dead links, zero decorative inputs, zero fake telemetry.
3. **Dual-Light Ergonomics:** Light mode for lecture halls and projectors and Dark mode for late-night labs are equal first-class citizens built on one semantic token architecture.
4. **Workspace First, Chrome Second:** Inside the IDE shell and grading workflows, chrome recedes so code, diffs, and academic feedback command maximum viewport real estate.
5. **Governed Craft:** Every interaction is fast, interruptible, accessible (WCAG 2.1 AA minimum, keyboard navigable, `prefers-reduced-motion` respectful), and built on reusable Blade components.

## Accessibility & Inclusion

- Target **WCAG 2.1 AA** minimum contrast across both Light and Dark modes (AAA on primary reading text and code diffs).
- Full keyboard navigation, visible `:focus-visible` rings, semantic landmarks, `tabular-nums` for grades/quotas/metrics, and `prefers-reduced-motion` support.

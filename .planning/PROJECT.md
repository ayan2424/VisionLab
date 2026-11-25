# VisionLab — GSD Project Context

**Project:** VisionLab (Collaborative Coding, Cloud IDE & University LMS/ERP Platform)
**Repository Root:** `C:\Users\ayans\OneDrive\Documents\A_Projects\Aptech\Vision2026\VisionLab`
**Last Updated:** 2026-11-24
**Product Record:** `PRODUCT.md`
**Codebase Map:** `.planning/codebase/` (7 documents, 1,704 lines)
**Active Design Spec:** `docs/superpowers/specs/2026-11-24-visionlab-visual-ux-redesign-design.md`

## Vision & Mission

VisionLab consolidates a university's fragmented coding-education stack into one self-hosted, enterprise-governed Laravel 11 web platform:
1. **LMS & Classroom Operations** (courses, enrollments, assignments, submissions, bulk grading, announcements, progress/gamification)
2. **Immutable Cloud IDE Workspaces** (sovereign source-compiled `code-server` inside hardened Docker `crun` + Nix `dev.nix` containers)
3. **Human-in-the-Loop AI Coding Governance** (Anthropic Claude SSE proxy + `ai_pending_patches` mandatory diff approval + audit trail)
4. **Institute ERP & Governance** (users, quotas, SHA256 extensions, audit logs, campuses, departments, batches, HR, fee challans, library)

## Current Milestone Focus

**Milestone 1: Full Visual & UX Redesign ("Swiss Lab Monograph")**
Unify all 5 fragmented surfaces (Public Marketing, Documentation Portal, Auth & Classroom LMS, Slim 40px Workspace IDE Shell, Admin & 6 ERP Modules) into a single, dual-light (**Lecture Hall Light** + **Night Lab Dark**) design system built strictly on Laravel 11 Blade, Tailwind CSS v3 RGB-channel CSS variables, Alpine.js v3, and Emil Kowalski motion craft — eliminating all AI-slop defects and dead shell controls.

**Upcoming Milestone 2 (Queued after Visual/UX Redesign):**
P0 Backend Security Hardening, Fatal Bug Fixes & Test Suite Recovery (addressing `.planning/codebase/CONCERNS.md`: `CodeServerManager` shell escaping, `AiController` IDOR, `.env.remote` gitignore, fatal PHP imports, and post-`2026_07_13` Feature test recovery).

## Core Constraints

- **Stack Lock:** Laravel 11 (`^11.31`), Tailwind CSS v3 (`^3.1.0`), Alpine.js v3 (`^3.4.2`), Vite 6, Vanilla ES2022 JS. Zero React/Vue/jQuery rewrites.
- **Global Public Chrome (`AGENTS.md` Directive #9):** `<x-frontend-header />` and `<x-frontend-footer />` remain single-source components across all public pages.
- **Brand Asset Lock:** Preserve the 6-path `VL >_` geometric mark (`public/icons/logo.svg`) adapted to semantic Light/Dark tokens.
- **Truth Over Theater (`PRODUCT.md`):** Zero dead `/register` CTAs, zero decorative search inputs, zero hardcoded fake health banners or dummy Chart.js arrays, zero advertising of decommissioned features (Jitsi, Vercel/Railway deployments, multi-user chat rooms).
- **Local Edit Workflow (`AGENTS.md` Directive #13):** All website/Blade/CSS/JS work occurs locally and deploys via `git push`.

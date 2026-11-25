# VisionLab — Engineering Roadmap

**Last Updated:** 2026-11-24
**Active Milestone:** Milestone 1 — Full Visual & UX Redesign (**Luminous Atelier**) → followed immediately by Milestone 2 (Security, Bugs & Test Recovery)

---

## Phase 01 — Luminous Atelier Full Visual & UX Redesign (`01-visual-ux-redesign`) — EXECUTE FIRST
**Goal:** Every VisionLab surface (Public Marketing, Documentation, Auth, Classroom LMS, Workspace IDE Shell, Admin & 6 ERP Modules) runs on one fluid, non-boxy, ultra-premium dual-light (**Obsidian Velvet Dark** + **Pearl Mist Light**) design system with refined Ocean Sapphire → Luminous Teal silk gradients, `Satoshi` + `JetBrains Mono` typography, instant `⌘K` command navigation, live `/healthz` probes, zero AI-slop defects, and final `DESIGN.md`.
**Spec:** `docs/superpowers/specs/2026-11-24-visionlab-visual-ux-redesign-design.md`

| Wave | Plan File | Scope | Dependencies | Autonomous |
|---|---|---|---|---|
| **Wave 1** | `.planning/phases/01-visual-ux-redesign/01-01-PLAN.md` | Luminous Atelier RGB Tokens, Silk Surface & Atmospheric Gradients, FOUC-Free Theme Engine, `Satoshi` Font, `VL >_` SVG, `<x-badge-icon />` & 19 Core Blade Primitives | None | Yes |
| **Wave 2** | `.planning/phases/01-visual-ux-redesign/01-02-PLAN.md` | Fluid Public Persuade (`welcome`, `features`, `about`, `contact` + throttled `POST /contact`, `portfolio`), Seamless Header/Footer & 3-Column Read Docs | `01-01` | Yes |
| **Wave 3** | `.planning/phases/01-visual-ux-redesign/01-03-PLAN.md` | Standalone Auth Folio, Seamless Tonal LMS Shell, Instant `⌘K` Command Palette, Global Announcements Bell & Classroom Views | `01-01`, `01-02` | Yes |
| **Wave 4** | `.planning/phases/01-visual-ux-redesign/01-04-PLAN.md` | Slim 40px Luminous IDE Shell, Live `/status` Telemetry + `pending_patches_count` & Container Management Views | `01-01`, `01-03` | Yes |
| **Wave 5** | `.planning/phases/01-visual-ux-redesign/01-05-PLAN.md` | Admin Governance, Live `/healthz` Strip, Real Charts, 6 ERP Modules, Error Views, Light/Dark Browser QA & `DESIGN.md` | `01-01`, `01-03` | No (visual finish sign-off) |

---

## Phase 02 — P0 Security Hardening, Fatal Bug Fixes & Test Recovery (`02-security-stability`) — EXECUTE SECOND
**Goal:** Eliminate every P0 security vulnerability, fatal runtime bug, orphaned post-`2026_07_13` artifact, and broken CI Feature test uncovered in `.planning/codebase/CONCERNS.md`.
**Plan:** `.planning/phases/02-security-stability/02-01-PLAN.md` (3 atomic waves: P0 Security Hardening → Fatal Runtime Bugs → Test Suite Recovery & Dead-Code Cleanup).

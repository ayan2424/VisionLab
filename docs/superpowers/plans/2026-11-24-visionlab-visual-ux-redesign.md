# VisionLab Full Visual & UX Redesign — Master Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` (recommended) or `superpowers:executing-plans` to implement this plan wave-by-wave via the atomic GSD plans under `.planning/phases/01-visual-ux-redesign/`. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Unify all 5 fragmented VisionLab web surfaces (Public Marketing, Docs, Auth & Classroom LMS, Slim 40px Workspace IDE Shell, and Admin Governance + 6 ERP Modules) into the fluid, non-boxy, gradient-elevated dual-light **Luminous Atelier** design system (**Obsidian Velvet Dark** + **Pearl Mist Light**, `Satoshi` + `JetBrains Mono`, ocean-sapphire & luminous-teal silk gradients) with real working shell ergonomics (`⌘K` command palette, live `/healthz` probes, real announcements unread counter, working `POST /contact`, and real Admin charts) — followed immediately by Phase 2 (`02-security-stability`) backend bug & security remediation.

**Architecture:** Semantic Token Foundation First (Approach A) across 5 atomic GSD waves for Phase 1, then 1 dedicated 3-task remediation wave for Phase 2. Wave 1 establishes Tailwind v3 RGB-channel CSS variables (`--vl-*` with `--vc-*` safety aliases), silk surface/CTA/skylight gradients, FOUC-free Light/Dark theme persistence (`partials/theme-init.blade.php` + Alpine `$store.theme`), semantic `VL >_` inline SVG logo, `<x-badge-icon />` emoji-to-SVG mapper, and 19 sculptural non-boxy Blade primitives. Waves 2–5 migrate every surface in strict dependency order.

**Tech Stack:** Laravel 11 (`^11.31`), Blade Anonymous Components, Tailwind CSS v3 (`^3.1.0`, `darkMode: 'class'`, RGB channels), Alpine.js v3 (`^3.4.2`, `$store`, `x-teleport`), Vite 6 (`^6.0.11`), Vanilla ES2022 JS, Chart.js 4, PHPUnit 11.

**Spec:** `docs/superpowers/specs/2026-11-24-visionlab-visual-ux-redesign-design.md`

---

## Global Constraints

- **Stack Lock:** Laravel 11 Blade + Tailwind CSS v3 + Alpine.js v3 + Vite 6 only; zero React/Vue/Livewire/jQuery additions.
- **Dual-Light Contrast Floor:** WCAG 2.1 AA minimum (`>= 4.5:1` body/placeholder, `>= 3:1` large) across BOTH `:root` (Lecture Hall Light `#F7F7F5`) and `.dark` (Night Lab Dark `#111214`).
- **Anti-AI-Slop Bans:** Zero `#B026FF`, `#00F3FF`, `.btn-glow`, `.text-gradient-*`, `.aurora-text`, `.blob`, fixed blur orbs, eyebrow kickers above headings, one-sided rounded box accent bars, ghost cards (`blur(24px)` + border + heavy shadow), emojis as UI icons, or `transition: all`.
- **Emil Motion Floor:** UI transitions `150–200ms cubic-bezier(0.23, 1, 0.32, 1)`, `:active scale(0.97)`, enter from `scale(0.96)` + opacity (never `scale(0)`), **0ms instant open/close** on `⌘K` command palette, `@media (hover: hover) and (pointer: fine)` hover guards, and `prefers-reduced-motion` support.
- **Global Public Chrome (`AGENTS.md` Directive #9):** All public pages (`welcome`, `about`, `features`, `contact`, `docs`, `portfolio/show`) extend `layouts/landing.blade.php` using single-source `<x-frontend-header />` and `<x-frontend-footer />`.
- **Truth Over Theater (`PRODUCT.md`):** Zero `/register` links, zero `mailto:admin@visioncode.ai`, zero unbuilt "Web Preview" buttons, zero hardcoded dummy Chart.js arrays, zero advertising of dropped Jitsi/Vercel/Railway/Chat features.
- **CSP Compatibility:** Only load Google Fonts (`Bricolage Grotesque`, `Source Sans 3`, `JetBrains Mono`) already permitted in `app/Http/Middleware/SecurityHeaders.php`.

## Review Focus (5 Edge Cases Pinned to Tests)

1. **Theme FOUC & Persistence Across Reloads/System Preference:** Missing `localStorage('vl-theme')` falls back to `prefers-color-scheme`; explicit `'light'` or `'dark'` survives navigation across all layouts (`landing`, `guest`, `dashboard`, `workspace`, `errors/layout`) without dark-flash. *(Pinned in Wave 1 + Wave 5 browser verification)*
2. **Public Pages Breaking When `<x-guest-layout>` Drops Header/Footer:** Strict wave dependency (`01-02` migrates `pages/*` and `portfolio/show` to `layouts/landing` BEFORE `01-03` strips public chrome from `layouts/guest`). *(Pinned in `PublicRedesignTest` + `OperateShellRedesignTest`)*
3. **Stuck Unread Announcement Badge on Topbar:** Topbar View Composer counts ONLY global announcements (`course_id IS NULL`) matching `route('announcements.index')` so marking global announcements read clears the badge to 0. *(Pinned in `OperateShellRedesignTest`)*
4. **Contact Form Abuse & Local Mailer Crash:** `POST /contact` enforces `throttle:5,1`, strict FormRequest validation, and catches mail transport failures gracefully while always logging structured inquiries. *(Pinned in `PublicRedesignTest`)*
5. **SQLite vs MySQL Date Aggregation & Decommissioned `jitsi_config` False Degradation:** `AdminController::index()` groups `AiActionsLog` in PHP over a 6-month `created_at` query (works identically in SQLite tests and MySQL production), and UI filters `jitsi_config` without altering `/healthz`'s raw JSON schema. *(Pinned in `AdminRedesignTest`)*

---

## Wave Execution Checklist

- [ ] **Wave 1 (`01-01-PLAN.md`):** Dual-Light RGB Tokens, FOUC-free Theme Engine, `VL >_` SVG Logo, `<x-badge-icon />` & 19 Core Blade Primitives → [01-01-PLAN.md](file:///C:/Users/ayans/OneDrive/Documents/A_Projects/Aptech/Vision2026/VisionLab/.planning/phases/01-visual-ux-redesign/01-01-PLAN.md)
- [ ] **Wave 2 (`01-02-PLAN.md`):** Public Marketing (Comp-Faithful Hero + Interactive Patch Ledger), Throttled `POST /contact`, Portfolio & 3-Column Read Docs → [01-02-PLAN.md](file:///C:/Users/ayans/OneDrive/Documents/A_Projects/Aptech/Vision2026/VisionLab/.planning/phases/01-visual-ux-redesign/01-02-PLAN.md)
- [ ] **Wave 3 (`01-03-PLAN.md`):** Standalone Auth Shell, Docked 240px/52px LMS Layout, Instant `⌘K` Command Palette, Global Unread Announcements Bell & Classroom Views → [01-03-PLAN.md](file:///C:/Users/ayans/OneDrive/Documents/A_Projects/Aptech/Vision2026/VisionLab/.planning/phases/01-visual-ux-redesign/01-03-PLAN.md)
- [ ] **Wave 4 (`01-04-PLAN.md`):** Slim 40px Workspace IDE Shell, Live `/status` Telemetry + `pending_patches_count` & Workspace Views → [01-04-PLAN.md](file:///C:/Users/ayans/OneDrive/Documents/A_Projects/Aptech/Vision2026/VisionLab/.planning/phases/01-visual-ux-redesign/01-04-PLAN.md) *(can run parallel with Wave 5 Tasks 1–2 after Wave 3)*
- [ ] **Wave 5 (`01-05-PLAN.md`):** Admin Governance, Live `/healthz` Strip, Real Charts, 6 ERP Modules, Error Views, Full Browser Light/Dark QA & Final `DESIGN.md` + `.impeccable/design.json` → [01-05-PLAN.md](file:///C:/Users/ayans/OneDrive/Documents/A_Projects/Aptech/Vision2026/VisionLab/.planning/phases/01-visual-ux-redesign/01-05-PLAN.md)

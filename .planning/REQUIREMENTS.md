# Milestone 1 Requirements — Full Visual & UX Redesign

**Milestone:** 01 — Swiss Lab Monograph Visual & UX Redesign
**Spec Reference:** `docs/superpowers/specs/2026-11-24-visionlab-visual-ux-redesign-design.md`
**Date:** 2026-11-24

## Foundation & Design System (Wave 1)

- **VIS-01:** Define dual-light semantic RGB-channel tokens (`--vl-bg`, `--vl-surface`, `--vl-surface-raised`, `--vl-rail`, `--vl-border`, `--vl-border-strong`, `--vl-ink`, `--vl-ink-secondary`, `--vl-ink-muted`, `--vl-accent`, `--vl-accent-hover`, `--vl-accent-fg`, `--vl-gate`, `--vl-success`, `--vl-danger`, `--vl-info`) for `:root` (Lecture Hall Light) and `.dark` (Night Lab Dark) in `resources/css/app.css` and register `colors.vl.*` with `<alpha-value>` support in `tailwind.config.js`.
- **VIS-02:** Map all legacy `--vc-*` variables to `rgb(var(--vl-*))` aliases so unmigrated views remain functional during incremental rollout, and delete all neon purple/cyan tokens, `.btn-glow`, `.text-gradient-*`, `.blob`, and `backdrop-filter: blur(24px)` ghost-card rules.
- **VIS-03:** Implement FOUC-free dual-light theme persistence via inline `resources/views/partials/theme-init.blade.php` + Alpine `$store.theme` in `resources/js/app.js` (removing hardcoded `document.documentElement.classList.add('dark')`) + reusable `<x-theme-toggle />`.
- **VIS-04:** Load `Bricolage Grotesque` (display), `Source Sans 3` (UI/read), and `JetBrains Mono` (code/tabular) cleanly under existing CSP (`fonts.googleapis.com` / `fonts.gstatic.com`), style browser surfaces (`::selection`, `caret-color`, `:focus-visible`, `tabular-nums`, flat scrollbars), and apply Emil Kowalski motion tokens (`150–200ms cubic-bezier(0.23, 1, 0.32, 1)`, `:active scale(0.97)`, `prefers-reduced-motion`, hover media guard).
- **VIS-05:** Upgrade `<x-logo />` to render the preserved 6-path `VL >_` geometry with semantic `currentColor` + `rgb(var(--vl-accent))` fills, add `<x-badge-icon />` to map DB-stored emoji icons to crisp `1.5px` Heroicons SVGs, and refactor all core Blade primitives (`button`, `card`, `stat-card`, `badge`, `table`, `modal`, `alert`, `empty-state`, `skeleton-loader`, `form/*`) to flat 1px hairline styling with full interactive states and `x-teleport="body"` overlay safety.

## Public Persuade, Portfolio & Docs Read (Wave 2)

- **VIS-06:** Redesign single-source `<x-frontend-header />` (60px hairline nav, `VL >_`, 4 links, `<x-theme-toggle />`, `Sign in` → `route('login')`, `Request Pilot` → `route('contact')`, mobile Alpine drawer) and `<x-frontend-footer />` (4-column Swiss monograph index), removing all `/register` links.
- **VIS-07:** Unify `layouts/landing.blade.php` onto `--vl-*` tokens and strip Three.js importmap, shifting gradient background, `.aurora-text`, `.font-serif-italic`, and `.grid-floor`.
- **VIS-08:** Rebuild `welcome.blade.php` matching the approved Swiss Lab Monograph north-star comp (`dir_1_monograph_1795506928247.jpg`): asymmetric 5:7 hero, interactive Alpine **Patch Review Ledger** (`SYNTHETIC DEMO · CS-301 SYSTEMS LAB`) with working Approve/Reject/Reset controls, 4-column hairline capability strip, real architecture splits (Nix/crun, HITL AI diffs, LMS/Forensics), and campus pilot close.
- **VIS-09:** Migrate `pages/about.blade.php`, `pages/features.blade.php`, `pages/contact.blade.php`, and `portfolio/show.blade.php` (`/u/{student_id}`) onto `layouts/landing.blade.php`, replacing `mailto:admin@visioncode.ai` with a working throttled `POST /contact` endpoint (`ContactController@submit` + `StoreContactRequest`) and replacing emoji badges in `portfolio/show.blade.php` with `<x-badge-icon />`.
- **VIS-10:** Rebuild `pages/docs.blade.php` in **Read** mode: 240px left section rail, calm `68ch` `Source Sans 3` (`16px`/`1.65`) reading column, 200px sticky right TOC with scroll-spy, and copyable `JetBrains Mono` snippets covering VisionLab's real implemented subsystems.

## Auth, Docked LMS Shell & Classroom Workflows (Wave 3)

- **VIS-11:** Convert `layouts/guest.blade.php` into the standalone centered Auth folio shell (after Wave 2 moves public pages off it) and redesign all 6 `auth/*` views (`login`, `forgot-password`, `reset-password`, `verify-email`, `confirm-password`, `register` fallback), replacing dead `"Sign up"` link with institutional provisioning guidance.
- **VIS-12:** Rebuild `layouts/dashboard.blade.php` (and `layouts/app.blade.php`) into the edge-to-edge docked hairline shell (`240px` `bg-vl-rail` sidebar, `52px` flat `bg-vl-surface` topbar, clean `bg-vl-bg` canvas), eliminating fixed blur orbs, pill topbars, and double-wrapped glass `<main>`.
- **VIS-13:** Build `<x-command-palette />` (`⌘K` / `Ctrl+K`, `0ms` instant open/close via `x-teleport="body"`, role-filtered routes, keyboard arrow/Enter/Esc navigation) replacing the decorative search input in `components/topbar.blade.php`.
- **VIS-14:** Wire a real global-announcements unread View Composer on `components.topbar` (all authenticated roles) linking to `route('announcements.index')`, replacing the empty student notification block.
- **VIS-15:** Redesign `dashboard/student.blade.php`, `dashboard/instructor.blade.php`, `courses/*`, `assignments/*`, `submissions/*`, `enrollments/join.blade.php`, `announcements/*`, `progress/index.blade.php`, `gamification/index.blade.php`, and `profile/*`, stripping inline `<style>` hacks, replacing emoji icons with `<x-badge-icon />`, and making Chart.js / contribution heatmap theme-reactive.

## Slim 40px Workspace IDE Shell (Wave 4)

- **VIS-16:** Redesign `resources/views/workspace.blade.php` with a slim `40px` token-unified hairline topbar (`calc(100vh - 40px)` `#vscode-frame`), real live telemetry indicators (slug, status dot, template badge, live CPU/RAM from `/status`, read-only `pending_patches_count` in `WorkspaceController::status()`), `<x-theme-toggle />`, and zero non-functional preview buttons or orange pill bloat.
- **VIS-17:** Redesign `#premium-loader` into a sharp Swiss Monograph Container Boot Checklist card preserving 100% of existing `workspace.ping` polling and iframe reveal logic, and unify `workspaces/{index,create,rebuild,restart}.blade.php` onto the shared Operate shell.

## Admin Governance, ERP Console & Impeccable Finish Gate (Wave 5)

- **VIS-18:** Consolidate `layouts/admin.blade.php` onto `layouts/dashboard.blade.php`, organize `components/sidebar.blade.php` admin links into clear domain groups (`Operations`, `Security & AI`, `Institute ERP`, `System`), and rebuild `admin/dashboard.blade.php` with a live Alpine `/healthz` polling telemetry strip (filtering decommissioned `jitsi_config` in UI), disciplined tabular KPI ledger (zero inline `onmouseover`), and real `$signupTrend` + `aiActionsTrend` Chart.js datasets.
- **VIS-19:** Unify all Admin Governance views (`users`, `extensions`, `workspaces`, `templates`, `quotas`, `audits`, `analytics`, `system`, `webhooks`, `status`) and all 6 Institute ERP view directories (`campuses`, `departments`, `batches`, `challans`, `employees`, `library`) onto uniform high-density hairline tables, consistent page headers, and theme-reactive charts; filter `jitsi_config` from `admin/status.blade.php`'s operational rollup.
- **VIS-20:** Unify all error/offline views (`errors/{layout,403,404,419,500,maintenance,offline}.blade.php`), consolidate duplicate `offline.blade.php`, run full automated build + grep slop check + PHPUnit view/contact tests + desktop/mobile Light/Dark browser verification, and author final token-bearing `DESIGN.md` + `.impeccable/design.json`.

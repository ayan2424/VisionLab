# VisionLab Design System — Luminous Atelier

This document records the authoritative visual tokens, surfaces, motion grammar, 3D/media standards, and component architecture built into VisionLab across all five surfaces (**Public Persuade**, **Read Docs**, **Auth Folio**, **Operate LMS & Slim IDE**, and **Admin Governance + 6-Module ERP**).

---

## 1. Visual Philosophy & Surface Modes

- **Aesthetic Direction:** **Luminous Atelier** — fluid, sculptural, dual-light editorial and engineering software. Zero boxy 1px grid cages, zero neon purple/cyan cyberpunk gimmicks, zero gradient text on headings, zero eyebrow kickers, and zero emoji icons.
- **Dual-Light Modes (First-Class Parity):**
  - **Obsidian Velvet Dark (`.dark`)** — Night Lab & IDE focus mode (`--vl-bg: 9 13 20`, `#090D14`).
  - **Pearl Mist Light (`:root`)** — Lecture Hall & Projector clarity mode (`--vl-bg: 246 249 252`, `#F6F9FC`).
  - Synchronized without FOUC via `resources/views/partials/theme-init.blade.php`, `Alpine.store('theme')`, `localStorage('vl-theme')`, `<meta name="theme-color">`, and the `vl:theme-changed` window event (updating Three.js studio lighting, Chart.js canvases, and contribution heatmaps live).

---

## 2. Semantic RGB Channel Tokens (`resources/css/app.css` & `tailwind.config.js`)

| Token | Pearl Mist Light (`:root`) | Obsidian Velvet Dark (`.dark`) | Usage |
| :--- | :--- | :--- | :--- |
| `--vl-bg` | `246 249 252` (`#F6F9FC`) | `9 13 20` (`#090D14`) | Page canvas |
| `--vl-surface` | `255 255 255` (`#FFFFFF`) | `17 24 36` (`#111824`) | Sculptural panels & modals |
| `--vl-surface-raised` | `241 245 250` (`#F1F5FA`) | `23 32 48` (`#172030`) | Nested pills, filter controls, table headers |
| `--vl-rail` | `238 243 249` (`#EEF3F9`) | `13 19 29` (`#0D131D`) | Docked 240px sidebar, docs rail, public footer |
| `--vl-ink` | `11 18 32` (`#0B1220`) | `248 250 252` (`#F8FAFC`) | Primary display & body typography |
| `--vl-ink-secondary` | `71 85 105` (`#475569`) | `168 181 200` (`#A8B5C8`) | Secondary copy & labels |
| `--vl-ink-muted` | `100 116 139` (`#64748B`) | `118 134 156` (`#76869C`) | Metadata, timestamps, hints |
| `--vl-accent` | `2 132 199` (`#0284C7`) | `56 189 248` (`#38BDF8`) | Ocean Sapphire interactive accents & links |
| `--vl-accent-cta` | `37 99 235` (`#2563EB`) | `37 99 235` (`#2563EB`) | Primary directional silk CTA buttons |
| `--vl-teal` | `13 148 136` (`#0D9488`) | `45 212 191` (`#2DD4BF`) | Operational health, approved patches, streaks |
| `--vl-gate` | `180 83 9` (`#B45309`) | `251 191 36` (`#FBBF24`) | Human approval gates, pending AI patches, warnings |
| `--vl-danger` | `225 29 72` (`#E11D48`) | `251 113 133` (`#FB7185`) | Destructive confirmations, outages, overdue states |

---

## 3. Typography & Craft Floor

- **Display & UI Sans:** `Satoshi` (Fontshare CDN) with tight optical tracking (`-0.032em` on display headings, `text-balance`).
- **Code, Telemetry & Tabular Data:** `JetBrains Mono` (Google Fonts) + `font-variant-numeric: tabular-nums` on every KPI counter, grade percentage, latency metric, and container readout.
- **Browser Surfaces:** Styled `::selection`, `caret-color`, `:focus-visible` 2px ocean-sapphire focus ring, slim scrollbars, and `prefers-reduced-motion` full override.

---

## 4. Motion & Interaction Grammar

- **Curves:** `--vl-ease-out: cubic-bezier(0.22, 1, 0.36, 1)`, `--vl-ease-in-out: cubic-bezier(0.77, 0, 0.175, 1)`, `--vl-drawer: cubic-bezier(0.32, 0.72, 0, 1)`.
- **Tactile Press:** `.vl-btn-primary:active`, `.vl-btn-secondary:active`, `.vl-btn-danger:active` scale smoothly to `0.97` (`140ms`).
- **Zero-Latency Command Palette (`⌘K` / `Ctrl+K`):** `<x-command-palette />` teleports to `body` with strictly `0ms` open/close latency for repeat keyboard utility.
- **Hover Hygiene:** Interactive elevation shifts are gated inside `@media (hover: hover) and (pointer: fine)` to prevent sticky hover artifacts on touch screens.

---

## 5. 3D Stage & Bespoke Editorial Media

- **Interactive Three.js PBR Hero (`public/js/hero-3d.js`):** Loaded exclusively on `welcome.blade.php` using `three@0.164.1` ES modules + `RoomEnvironment` PMREM studio lighting, ACES Filmic tone mapping, pointer-tracked autonomous companion visor & beveled `VL` crystal core, coupled to `nix`/`ai`/`sha` subsystem tabs and `vl:theme-changed`. DPR capped (`<= 2` desktop / `<= 1.5` mobile), `IntersectionObserver` auto-paused, and `prefers-reduced-motion` static hero pose compliant.
- **Bespoke Local Editorial Photography (`public/images/redesign/`):**
  - `campus-glass-lab.jpg` — Cantilevered glass & anodized-steel university engineering lab at twilight.
  - `cs-research-lab.jpg` — Faculty & systems researchers collaborating at matte-slate lab workstations.
  - `workstation-night-lab.jpg` — Minimal studio-lit dark-obsidian IDE & side-by-side diff workstation.
  - `students-collaborating.jpg` — University computer science students collaborating in a daylight glass studio.
  - `university-atrium-study.jpg` — Contemporary architectural university library & engineering study atrium.

---

## 6. Surface Architecture Inventory

1. **Public Persuade (`layouts/landing.blade.php`):** 60px seamless translucent `<x-frontend-header />`, 4-column `<x-frontend-footer />`, 3D hero, bespoke campus photography splits, interactive `SYNTHETIC DEMO · CS-301` AI Patch Review Stage, role clarity switcher, throttled `POST /contact` campus pilot form, and `/u/{student_id}` public student portfolio with `<x-badge-icon />`.
2. **Read Docs (`pages/docs.blade.php`):** 3-column calm `68ch` reading portal (`16px`/`1.68`) with 240px tonal section rail, 200px sticky scroll-spy TOC, obsidian `.vl-code-stage` copyable blocks, and zero Three.js overhead.
3. **Auth Folio (`layouts/guest.blade.php` & `auth/*`):** Standalone centered `440px` sculptural folio card with institutional provisioning copy and zero `/register` dead links.
4. **Operate LMS & Slim IDE (`layouts/dashboard.blade.php`, `workspace.blade.php`):** Docked 240px tonal sidebar rail (zero left-border stripe bars), flat 52px topbar with 0ms `⌘K` palette & real global unread announcements bell (`AppServiceProvider` View Composer), and a **40px** browser `code-server` IDE chrome bar (`calc(100vh - 40px)`) wired to live `/status` CPU/RAM + `pending_patches_count`.
5. **Admin Governance, 6 ERP Modules & Errors (`admin/*`, `errors/*`):** Live Alpine `/healthz` probe strip (excluding decommissioned `jitsi_config` in UI rollup while preserving `/healthz` JSON schema), real `$signupTrend` + 6-month `$aiActionsTrend` theme-reactive Chart.js charts, unified 6-module ERP tables/forms, and single-source dual-light error & offline views.

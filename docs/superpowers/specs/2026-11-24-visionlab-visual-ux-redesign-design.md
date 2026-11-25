# VisionLab Full Visual & UX Redesign — Design Specification (v2: Luminous Atelier)

**Date:** 2026-11-24
**Status:** Updated per User Direction Pivot — Ready for Wave 1 Execution
**Product Record:** `PRODUCT.md`
**Selected Visual World:** **Luminous Atelier** (**Obsidian Velvet Dark** + **Pearl Mist Light** — fluid, sculptural, non-boxy surfaces with refined Ocean Sapphire → Luminous Teal atmospheric & specular gradients; zero boxy "dabba" grids, zero boring flat chrome, zero purple/neon AI slop)
**Interactive Prototype:** `C:/Users/ayans/.gemini/antigravity/brain/f7bb9259-d641-48e6-bcaf-00535fd68569/visionlab-luminous-preview.html`
**Execution Roadmap:**
- **Phase 01 (`01-visual-ux-redesign`):** Full 5-Wave Visual & UX Redesign (executed FIRST)
- **Phase 02 (`02-security-stability`):** P0 Backend Security Hardening, Fatal Bug Fixes & Test Suite Recovery (executed SECOND)

---

## 1. Intent & Visual Evolution

Following the user's explicit visual critique (*"do not make it boring, old, boxy/dabba, or AI-slop; make the UI & theme ultra-premium, smooth, attractive, next-level, with refined gradients — redesign first, then fix all discovered bugs and gaps"*), VisionLab's design language is upgraded from rigid 1px boxed grids to **Luminous Atelier**:

- **Zero "Dabba" (Non-Boxy Fluidity):** Replace rigid square bordered boxes with generously rounded (`16px–20px` outer stages, `10px–12px` controls), tonally graduated sculptural surfaces (`linear-gradient(180deg, ...)`), crisp top-edge specular light catches (`inset 0 1px 0 rgba(255,255,255,0.11)` in Dark; diffuse architectural lift shadows in Light), and airy section rhythm.
- **Refined Architectural Gradients (Zero AI-Slop Purple/Neon):**
  - **Atmospheric Skylight Wash:** Subtle, calm ocean-sapphire (`#0EA5E9`) & deep-teal (`#0D9488`) atmospheric radial skylight washing softly across Velvet Obsidian (`#090D14`) in Dark Mode and Pearl Alabaster (`#F6F9FC`) in Light Mode — zero chaotic floating neon orbs or blur-150px blobs.
  - **Sculptural Silk Surfaces:** Soft vertical tonal surface gradients (`#161E2C → #0E141E` Dark; `#FFFFFF → #F7FAFE` Light) with razor-thin top light sheen so surfaces feel like precision anodized hardware rather than flat paper boxes.
  - **Directional Sapphire Action Buttons:** Crisp top-lit sapphire gradient (`linear-gradient(180deg, #3B82F6 0%, #1D4ED8 100%)`) with `inset 0 1px 0 rgba(255,255,255,0.26)` specular bevel and soft elevation shadow.
  - **Crisp Un-Gradient Headings:** Headings remain razor-sharp high-contrast ink (`#F8FAFC` Dark / `#0B1220` Light) — zero decorative rainbow or purple gradient text!
- **Dual-Light First-Class Parity:** **Obsidian Velvet Dark** (default evening/IDE studio experience) and **Pearl Mist Light** (radiant lecture-hall/projector clarity), switchable instantly with zero FOUC.

---

## 2. Goals, Non-Goals & Strict Anti-Slop Bans

### Goals
1. **Ultra-Premium Fluid Dual-Light System (`--vl-*` RGB channels + silk gradient utilities):** Smooth, luminous, non-boxy surfaces across every layout with FOUC-free Light/Dark switching (`partials/theme-init.blade.php` + Alpine `$store.theme`).
2. **Mode-Right Surface Craft Across All 5 Surfaces:**
   - **Persuade (`welcome`, `features`, `about`, `contact`, `portfolio`):** Expansive **Three.js 3D Interactive Hero** (`resources/js/hero-3d.js`, loaded ONLY on `welcome.blade.php` via jsDelivr `three@0.164.1` already whitelisted in `SecurityHeaders.php`, with optional Spline `.splinecode` drop-in slot) featuring a mouse-reactive PBR glass-and-anodized-titanium **VisionLab Sovereign Sandbox & Governance Core** coupled live to 3 real subsystem modes (`Nix Container Core`, `AI Patch Gate`, `SHA256 Supply Chain`) and dynamic Dark/Light studio lighting, followed below the fold by **curated, high-resolution Unsplash editorial photography** (university CS labs, modern campus glass architecture, dark-teal studio workstations, student collaboration — zero dummy boxes!), an interactive floating **Patch Review Stage** (`SYNTHETIC DEMO · CS-301`), clean role-based sections (Student / Instructor / University IT), truthful `/login` & `/contact` CTAs, and working throttled `POST /contact`.
   - **Read (`pages/docs`):** Soft tonal left rail, calm `68ch` reading column (`16px` `Satoshi`, `1.65` line-height), sticky right TOC, sleek obsidian syntax blocks with copy feedback, zero Three.js.
   - **Operate — Auth & LMS (`layouts/guest`, `layouts/dashboard`, classroom views):** Standalone fluid auth stage, seamless tonal 240px docked sidebar (zero boxy floating card wrapper around `<main>`), 52px topbar with 0ms instant `⌘K` Command Palette + real global unread announcements indicator + `<x-theme-toggle />`, stripped inline `<style>` hacks, `<x-badge-icon />` SVGs replacing emojis, theme-reactive charts.
   - **Operate — Slim 40px IDE Shell (`workspace.blade.php`, `workspaces/*`):** Slim 40px silk-obsidian/pearl bar reclaiming `calc(100vh - 40px)` for `code-server`, live `/status` CPU/RAM + read-only `pending_patches_count` pill, sleek boot checklist replacing orange `#f97316` modals.
   - **Operate — Admin & 6 ERP Modules (`layouts/admin`, `admin/*`, `errors/*`):** Live `/healthz` polling strip (filtering decommissioned `jitsi_config` in UI), real `$signupTrend` & `$aiActionsTrend` charts, smooth scannable data tables without boxy cell clutter, and unified error/offline pages.
3. **Phase 2 Follow-Through:** Immediately after Phase 1 visual/UX redesign completes, execute Phase 2 (`02-security-stability`) to fix all P0 security vulnerabilities, fatal PHP runtime bugs, and broken Feature tests from `.planning/codebase/CONCERNS.md`.

### Strict Anti-Slop Bans (Non-Negotiable)
- ❌ Zero electric purple (`#B026FF`), neon cyan (`#00F3FF`), indigo-pink gradients (`#6366F1→#EC4899`), or decorative gradient text on headings.
- ❌ Zero chaotic blur blobs (`blur-[150px]`, `.blob`), zero eyebrow/kicker labels above headings, zero emoji UI icons, zero boxy 3-column icon-circle grids, zero fake hero metric counters (`10M+ Users`, `99.9% Uptime`), zero `transition: all`, zero `scale(0)` entrances.

---

## 3. Exact Design Tokens & Visual Law

### 3.1 Dual-Light Semantic RGB Channels (`resources/css/app.css` & `tailwind.config.js`)
Defined as space-separated RGB channels in `@layer base` so Tailwind v3 opacity utilities (`bg-vl-accent/15`, `text-vl-ink/80`) work everywhere:

| Token | Dark (`html.dark` — Obsidian Velvet, Default) | Hex | Light (`:root` — Pearl Mist) | Hex | Role |
|---|---|---|---|---|---|
| `--vl-bg` | `9 13 20` | `#090D14` | `246 249 252` | `#F6F9FC` | Primary canvas base |
| `--vl-surface` | `17 24 36` | `#111824` | `255 255 255` | `#FFFFFF` | Elevated silk panels & cards |
| `--vl-surface-raised` | `23 32 48` | `#172030` | `241 245 250` | `#F1F5FA` | Hover states, popovers, dropdowns |
| `--vl-rail` | `13 19 29` | `#0D131D` | `238 243 249` | `#EEF3F9` | Docked sidebar & docs nav |
| `--vl-border` | `255 255 255` *(used at `/0.07`)* | `rgba(255,255,255,0.07)` | `15 23 42` *(used at `/0.08`)* | `rgba(15,23,42,0.08)` | Subtle tonal separator (never harsh box cage) |
| `--vl-border-strong` | `255 255 255` *(used at `/0.14`)* | `rgba(255,255,255,0.14)` | `15 23 42` *(used at `/0.16`)* | `rgba(15,23,42,0.16)` | Interactive input & focus hover edge |
| `--vl-ink` | `248 250 252` | `#F8FAFC` | `11 18 32` | `#0B1220` | Primary crisp headings & copy (**>15:1 AAA**) |
| `--vl-ink-secondary` | `168 181 200` | `#A8B5C8` | `71 85 105` | `#475569` | Secondary copy (**>6.5:1 AA**) |
| `--vl-ink-muted` | `110 126 148` | `#6E7E94` | `100 116 139` | `#64748B` | Captions, placeholders (**>=4.5:1 AA**) |
| `--vl-accent` | `56 189 248` / CTA `#2563EB→#1D4ED8` | `#38BDF8` / `#2563EB` | `2 132 199` / CTA `#2563EB→#1D4ED8` | `#0284C7` / `#2563EB` | Ocean Sapphire active links, focus & silk CTA |
| `--vl-accent-cta` | `37 99 235` | `#2563EB` | `37 99 235` | `#2563EB` | Primary button base |
| `--vl-accent-fg` | `255 255 255` | `#FFFFFF` | `255 255 255` | `#FFFFFF` | Text on primary gradient CTA |
| `--vl-teal` | `45 212 191` | `#2DD4BF` | `13 148 136` | `#0D9488` | Luminous Teal — verified SHA256, approved diffs, healthy probes |
| `--vl-gate` | `251 191 36` | `#FBBF24` | `180 83 9` | `#B45309` | Warm Amber-Gold — AI pending patch gates & due-soon |
| `--vl-danger` | `251 113 133` | `#FB7185` | `225 29 72` | `#E11D48` | Coral-Rose — diff deletions, alerts, destructive actions |

### 3.2 Silk Surface & Atmospheric Gradient Primitives (`@layer components`)
- **`.vl-atmosphere`:** Serene top skylight radial wash (`rgba(14,165,233,0.18)` + `rgba(20,184,166,0.07)` in Dark; `rgba(14,165,233,0.12)` + `rgba(13,148,136,0.06)` in Light) — smooth, calm, zero floating orbs.
- **`.vl-panel`:** Fluid `rounded-2xl` (`16px`), soft vertical silk gradient (`linear-gradient(180deg, rgba(22,30,44,0.88), rgba(14,20,30,0.92))` Dark; `linear-gradient(180deg, #FFFFFF, #F7FAFE)` Light), top specular rim `inset 0 1px 0 rgba(255,255,255,0.11)`, diffuse ambient lift shadow — **NO harsh 1px square border cage!**
- **`.vl-btn-primary`:** `rounded-xl` (`10px`), directional Ocean Sapphire gradient (`linear-gradient(180deg, #3B82F6 0%, #1D4ED8 100%)`), top specular highlight `inset 0 1px 0 rgba(255,255,255,0.26)`, tactile `:active { transform: scale(0.97); }`, hover lift (`translateY(-1px)` + soft sapphire shadow).
- **`.vl-btn-secondary`:** `rounded-xl`, translucent frosted silk (`rgba(255,255,255,0.06)` Dark / `rgba(255,255,255,0.88)` Light) with delicate top specular inset — no clunky rectangular outline box.
- **`.vl-code-stage`:** Always deep obsidian velvet (`linear-gradient(180deg, #0B111C, #080C14)`) with crisp `JetBrains Mono` syntax contrast even in Light Mode (matching Apple Developer / Stripe Docs luxury!).
- **Backward-Compatibility Alias Layer:** Map all legacy `--vc-*` variables and `.vc-card` / `.btn-primary` / `.vc-input` classes cleanly onto `.vl-*` tokens so unmigrated views transition seamlessly during Waves 1–5.

### 3.3 Typography, Browser Surfaces & Emil Motion Grammar
- **Primary Display & UI Face:** **`Satoshi`** (`400, 500, 700, 900` via Fontshare — already whitelisted in `SecurityHeaders.php` CSP!) for ultra-sleek modern geometric clarity (zero old/stiff newspaper tone, zero banned training-data display faces).
- **Code, Diffs & Tabular Telemetry:** **`JetBrains Mono`** (`400, 500`) + `font-variant-numeric: tabular-nums`.
- **Browser Surfaces:** Ocean Sapphire `::selection`, `caret-color`, `2px` `:focus-visible` ring, subtle flat `6px` scrollbars, `text-underline-offset: 3px`.
- **Motion (`emil-design-eng`):** `--vl-ease-out: cubic-bezier(0.22, 1, 0.36, 1)` (`160–220ms`), tactile `:active { transform: scale(0.97); }`, enter from `scale(0.96) + opacity` (never `scale(0)`), **0ms instant** `⌘K` command palette via `x-teleport="body"`, `@media (hover: hover) and (pointer: fine)` hover guards, full `prefers-reduced-motion` support.
- **Default Theme Behavior (`partials/theme-init.blade.php` + `$store.theme`):** Defaults to `localStorage('vl-theme')`, falling back to `window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark'` (so night-lab / default developer machines open in radiant **Obsidian Velvet Dark**, while bright daytime OS light-mode users open in **Pearl Mist Light**, with instant 1-click toggle everywhere!).

---

## 4. Builder-Check Resolutions (Retained & Enforced)

All 5 architectural resolutions from the fresh builder-check review remain locked in force:
1. **Wave 4 Slim 40px IDE Bar:** Binds strictly to real `/status` metrics + read-only `pending_patches_count` in `WorkspaceController::status()` (no unbuilt Web Preview button).
2. **Wave 3 Topbar Announcements Bell:** Counts **global unread announcements** (`course_id IS NULL`) via an `AppServiceProvider` View Composer for **all authenticated roles**, linking to `route('announcements.index')`.
3. **Waves 1–3 `<x-guest-layout>` Split, Portfolio & Emoji Badges:** Wave 2 moves all public pages (`welcome`, `features`, `about`, `contact`, `docs`, `portfolio/show`) onto `layouts/landing.blade.php` before Wave 3 converts `layouts/guest.blade.php` to standalone Auth; `<x-badge-icon />` maps DB-stored emoji `$badge->icon` strings to crisp `1.5px` Heroicons SVGs.
4. **Wave 2 Real Contact Form:** Throttled `POST /contact` (`ContactController@submit`, `StoreContactRequest`, `throttle:5,1`, daily structured log + mail notification, zero DB migration) replaces legacy `mailto:admin@visioncode.ai`.
5. **Wave 5 Real Admin Charts & Dormant `jitsi_config`:** Wires real `$signupTrend` and 6-month `aiActionsTrend` (`AiActionsLog`) in `AdminController::index()` (SQLite + MySQL compatible), and filters decommissioned `jitsi_config` in Admin UI rollup while keeping `/healthz`'s JSON schema intact.

---

## 5. Verification Gates (Phase 1 & Phase 2)

- **Build & Compile:** `npm.cmd run build` + `php artisan view:cache` succeed with 0 errors.
- **Slop Audit:** Grep confirms 0 `#B026FF`, `#00F3FF`, `btn-glow`, `aurora-text`, `blur-[150px]`, `onmouseover=`, `mailto:admin@visioncode.ai`, or `/register` links in views.
- **Feature Tests:** `PublicRedesignTest`, `OperateShellRedesignTest`, `WorkspaceShellRedesignTest`, `AdminRedesignTest` pass 100% in Phase 1; Phase 2 recovers the full PHPUnit suite + adds security regression tests (`CodeServerManager` shell injection, `AiController` IDOR, `User::addXp()`, `CourseExtensionController`, `AdminWorkspaceController::show()`).
- **Live Browser QA:** Inspect **Obsidian Dark** + **Pearl Light** at `1440×900` desktop and `390×844` mobile via `chrome-devtools-mcp` / `playwright` with 0 console/network errors, WCAG AA/AAA contrast, and final token-bearing `DESIGN.md` + `.impeccable/design.json`.

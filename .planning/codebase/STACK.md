# Technology Stack

**Analysis Date:** 2026-11-24

## Languages

**Primary:**
- PHP `^8.2` (production containers and CI workflows target PHP `8.3`; host aaPanel Nginx config `visionlab_nginx.conf` references `enable-php-85.conf`) — Laravel 11 backend API, web controllers, services, jobs, policies, models, migrations, and Blade views (`app/`, `config/`, `database/`, `routes/`, `tests/`)
- JavaScript (ES2022 / ESM `"type": "module"` in `package.json`) — Frontend interactivity, Laravel Echo WebSocket bootstrap, PWA service workers, Vite build pipeline (`resources/js/`, `public/pwa.js`, `public/sw.js`, `public/serviceworker.js`, `docker/workspace/patch_origin.js`)
- Blade / HTML5 & CSS3 (Tailwind CSS v3 with CSS custom properties) — Server-rendered UI templates, component library, and dark glassmorphism design system (`resources/views/`, `resources/css/app.css`)

**Secondary:**
- TypeScript `^5.0.0` (target `ES2022`, `CommonJS`, `strict: true`) — Custom VS Code collaboration, video panel, and AI patch-review extension (`extensions/visionlab-collab/src/`, `extensions/visionlab-collab/tsconfig.json`)
- Nix Expression Language — Declarative workspace runtime environments (`docker/workspace/dev.nix`, `docker/workspace/dev.nix.template`, and per-template `.vision/dev.nix` injected by `app/Services/CodeServerManager.php`)
- Bash / POSIX Shell — Container entrypoints, code-server source build scripts, backup/restore utilities, and OWASP security verification (`docker/workspace/vl-entrypoint.sh`, `scripts/build-code-server.sh`, `scripts/backup.sh`, `scripts/restore.sh`, `scripts/security_verify.sh`)
- Python 3 / PowerShell — Ad-hoc workspace maintenance scripts in repository root (`fix_css.py`, `replace_icons.ps1`)
- OpenAPI 3.1 YAML — Partial API specification (`public/api-docs/openapi.yaml`)

## Runtime

**Environment:**
- PHP `8.3-fpm-alpine` in Docker images (`Dockerfile`, `docker/app/Dockerfile`) with extensions: `pdo_mysql`, `mbstring`, `gd` (with freetype/jpeg in root `Dockerfile`), `zip` (root `Dockerfile`), `exif`, `pcntl`, `bcmath`, `intl` (root `Dockerfile`), `opcache` (root `Dockerfile`), `sockets`, and PECL `redis`. Note: `docker/app/Dockerfile` omits `zip`, `intl`, and `opcache`.
- Node.js `20` (`node:20-alpine` in `Dockerfile` and GitHub Actions `.github/workflows/production.yml`, `.github/workflows/compile-artifacts.yml`; `scripts/build-code-server.sh` installs Node.js 18 LTS for compiling `code-server` v4.90.3)
- Docker Engine with `crun` OCI container runtime (`daemon.json` configures `"default-runtime": "crun"` at `/usr/bin/crun`)
- Nix Package Manager (single-user `--no-daemon` install in `docker/workspace/Dockerfile`, `buildpack-deps:jammy` base image) for declarative student workspace toolchains

**Package Manager:**
- Composer `2` (`composer.json`, bundled `composer.phar` in repo root; `config.platform-check: false` and 4 ignored security advisories in `composer.json`)
  - Lockfile: `composer.lock` present
- npm (root frontend assets in `package.json`, and VS Code extension in `extensions/visionlab-collab/package.json`)
  - Lockfile: `package-lock.json` present (root lockfileVersion 3 and `extensions/visionlab-collab/package-lock.json`)
- Yarn — Used exclusively for compiling the custom `code-server` source tree (`.github/workflows/compile-artifacts.yml`, `scripts/build-code-server.sh`)

## Frameworks

**Core:**
- Laravel Framework `^11.31` (`composer.json`) — Full-stack MVC web framework using Laravel 11 slim `bootstrap/app.php` routing, middleware, exception handling, Eloquent ORM, scheduled console tasks (`routes/console.php`), queues, notifications, and policies
- Laravel Breeze `^2.4` (`composer.json` `require-dev`) — Session-based authentication scaffolding, Blade auth views, and password reset/email verification flows (`routes/auth.php`, `app/Http/Controllers/Auth/`)
- Laravel Sanctum `^4.3` (`composer.json`) — Stateful SPA cookie authentication (`$middleware->statefulApi()` in `bootstrap/app.php`) and personal access bearer tokens for IDE extensions and API clients (`config/sanctum.php`, `routes/api.php`)
- Laravel Reverb `^1.10` (`composer.json`) — Native first-party Pusher-compatible WebSocket server for real-time presence, grading alerts, AI patch notifications, and workspace rebuild triggers (`config/reverb.php`, `config/broadcasting.php`)
- Tailwind CSS `^3.1.0` + `@tailwindcss/forms` `^0.5.2` (`package.json`, `tailwind.config.js`) — Utility-first CSS framework configured with `darkMode: 'class'`, semantic `--vc-*` CSS variables, and custom keyframes
- Alpine.js `^3.4.2` (`package.json`, `resources/js/app.js`) — Lightweight client-side reactivity on Blade templates
- VisionLab Sovereign IDE (`coder/code-server` v4.90.3 fork) — Browser-based VS Code IDE compiled from source into `docker/workspace/visionlab-ide-1.0.0-linux-amd64.tar.gz` and packaged in `docker/workspace/Dockerfile` (with fallback legacy `docker/Dockerfile.codeserver` based on `codercom/code-server:latest`)

**Testing:**
- PHPUnit `^11.0.1` (`composer.json`, `phpunit.xml`) — Unit (`tests/Unit/`) and Feature (`tests/Feature/`, 28+ test classes) test runner configured with SQLite `:memory:`, `array` cache/session/mail drivers, `sync` queue, and mocked `pusher` broadcaster
- Mockery `^1.6` & FakerPHP `^1.23` (`composer.json`) — Test mocking and model factory data generation
- Nunomaduro Collision `^8.1` (`composer.json`) — CLI test and error reporting

**Build/Dev:**
- Vite `^6.0.11` + `laravel-vite-plugin` `^1.2.0` (`package.json`, `vite.config.js`) — Frontend asset bundler (`resources/css/app.css`, `resources/js/app.js`) with a custom `visioncode-cleanup` plugin that deletes stale `public/hot` on build
- PostCSS `^8.4.31` + Autoprefixer `^10.4.2` (`package.json`, `postcss.config.js`) — CSS processing pipeline
- TypeScript Compiler `tsc` (`typescript` `^5.0.0`, `@types/vscode` `^1.80.0`, `@types/node` `^18.x`) — Compiles `extensions/visionlab-collab/src/` into `out/extension.js` (packaged into `extensions/visionlab-collab/visionlab-collab-1.0.0.vsix`)
- Laravel Pint `^1.13` (`composer.json`) — Opinionated PHP code formatter
- Laravel Pail `^1.1` & `concurrently` `^9.0.1` (`composer.json` `dev` script) — Runs `php artisan serve`, `queue:listen`, `pail`, and `npm run dev` concurrently in local development
- Laravel Sail `^1.26` & Laravel Tinker `^2.9` (`composer.json`) — Local Docker helper and interactive REPL

## Key Dependencies

**Critical:**
- `guzzlehttp/guzzle` (transitive via Laravel) — Direct HTTP client used in `app/Services/AiService.php` to invoke the Anthropic Messages API (`https://api.anthropic.com/v1/messages`)
- `symfony/process` (transitive via Laravel) — Executes host Docker CLI commands (`docker run/exec/inspect/port/stats/volume/stop/rm`), `grep`, and `diff` in `app/Services/CodeServerManager.php` and `app/Services/AiSandbox.php`
- `laravel-notification-channels/webpush` `^10.5` & `minishlink/web-push` `*` (`composer.json`) — W3C VAPID Web Push delivery (`app/Services/WebPushService.php`, `app/Notifications/AssignmentGradedNotification.php`, `app/Notifications/ScheduledReminderNotification.php`)
- `pusher/pusher-php-server` `^7.2` (`composer.json`) — Pusher protocol PHP server SDK (also required by Reverb/Pusher broadcasting)
- `laravel-echo` `^2.3.4` (web app) / `^1.15.3` (`extensions/visionlab-collab`) & `pusher-js` `^8.5.0` (web app) / `^8.3.0` (`extensions/visionlab-collab`) — Real-time WebSocket client libraries (`resources/js/echo.js`, `extensions/visionlab-collab/src/managers/RealtimeManager.ts`)
- `axios` `^1.7.4` (`package.json`, `resources/js/bootstrap.js`) — HTTP client configured globally with `X-Requested-With: XMLHttpRequest`
- `predis/predis` `*` (`composer.json`) — Pure-PHP Redis client (default `REDIS_CLIENT` in `config/database.php`; `phpredis` PECL extension also compiled into Docker images)
- `maatwebsite/excel` `*` (`composer.json`) — Spreadsheet import/export dependency installed in Composer (note: `app/Http/Controllers/EnrollmentController.php` and `app/Jobs/CsvEnrollmentJob.php` currently use native PHP `fgetcsv()` instead of `maatwebsite/excel`)

**CDN-Loaded Frontend Libraries (Not in `package.json`):**
- Chart.js `4.4.0` (`https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js`) — Analytics dashboards (`resources/views/admin/analytics/index.blade.php`, `resources/views/analytics/student.blade.php`)
- Three.js `0.164.1` (`https://cdn.jsdelivr.net/npm/three@0.164.1/...` via `<script type="importmap">`) — Landing page 3D scenes (`resources/views/layouts/landing.blade.php`)
- Workbox `7.0.0` (`https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js`) — PWA routing, cache expiration, and background sync (`public/sw.js`)

**Infrastructure:**
- Nginx (`nginx:alpine` in `docker-compose.prod.yml`; host aaPanel Nginx in `nginx.conf`, `visionlab_nginx.conf`, `nginx_patch.conf`, `visionlab.rewrite.conf`) — TLS termination, HTTP/2 & HTTP/3 QUIC, PHP-FPM FastCGI, Reverb WebSocket `/app` & `/apps` upgrade proxy, and dynamic per-port IDE reverse proxy (`/ide/{port}/` and `/codeserver/{port}/`)
- MySQL `8.0` (`mysql:8.0` in `docker-compose.prod.yml`) — Primary production RDBMS (`utf8mb4_unicode_ci`, strict SQL mode)
- Redis `7` (`redis:7-alpine` in `docker-compose.prod.yml`) — Production cache, session, queue, rate-limiter, and Reverb scaling store
- Monolog (`\Monolog\Formatter\JsonFormatter` in `config/logging.php`) — Structured JSON log serialization to `storage/logs/laravel.log`
- Pre-built IDE & Extension Artifacts committed/mounted in repo:
  - `docker/workspace/visionlab-ide-1.0.0-linux-amd64.tar.gz` — Compiled VisionLab Sovereign IDE (`code-server` fork)
  - `docker/workspace/extensions/visionlab-agent-1.3.39.vsix` — Forked Continue AI Agent VS Code extension (forked from `continuedev/continue` v0.8.21 per `docs/VisionLab_Agent_Build_Report.md`)
  - `extensions/visionlab-collab/visionlab-collab-1.0.0.vsix` — Compiled VisionLab collaboration/patch-reviewer extension

## Configuration

**Environment:**
- Configured via standard Laravel `.env` file loaded into `config/*.php` (template in `.env.example`).
- Custom platform settings centralized in `config/visionlab.php` (container prefix/image/storage/ports, resource quotas, AI provider/model/limits, Jitsi config, deployment tokens, VAPID keys) and `config/webpush.php`.
- Runtime feature flags, global marketplace lock (`global_allow_marketplace`), and maintenance mode (`maintenance_mode`) are persisted in the database `system_configs` table via `App\Models\SystemConfig` (`app/Http/Controllers/Admin/AdminSystemController.php`).
- Reverse-proxy URL auto-detection in `app/Providers/AppServiceProvider.php` forces HTTPS in `production` or behind `X-Forwarded-Proto: https` / Replit proxy domains, and defines 7 named `RateLimiter` buckets (`api`, `auth`, `ai`, `file-api`, `video`, `push`, `admin`).

**Build:**
- `vite.config.js` — Vite dev server on `0.0.0.0:5173`, Laravel input entries `resources/css/app.css` and `resources/js/app.js`, auto-removal of `public/hot` on `npm run build`
- `tailwind.config.js` & `postcss.config.js` — Tailwind v3 design tokens (`vc.*`, `brand.*`, `void`, `surface`, `elevated`) and PostCSS plugins
- `phpunit.xml` — PHPUnit 11 test suite definitions and in-memory SQLite test env overrides
- `extensions/visionlab-collab/tsconfig.json` — TypeScript `ES2022` / `CommonJS` strict compilation into `out/`
- `Dockerfile` (root multi-stage Node 20 + Composer 2 + PHP 8.3-FPM) & `docker/app/Dockerfile` (single-stage PHP 8.3-FPM Alpine used by `docker-compose.prod.yml` and `.github/workflows/deploy.yml`)
- `docker/workspace/Dockerfile` (Ubuntu Jammy + Nix + `visionlab-ide-1.0.0` + `patch_origin.js`) & `docker/Dockerfile.codeserver` (legacy `codercom/code-server:latest` with apt runtimes)
- `docker/php-fpm.conf` — Dynamic FPM pool (`pm.max_children = 50`, `pm.start_servers = 5`, `pm.max_requests = 500`, `clear_env = no`)

## Platform Requirements

**Development:**
- PHP `>= 8.2` (8.3 recommended) with `pdo_sqlite` / `pdo_mysql`, `mbstring`, `openssl`, `curl`, `zip`, `gd`, `bcmath`, `sockets`
- Composer 2 & Node.js 20 LTS (`npm`)
- SQLite (default in `.env.example`) or MySQL 8.0; optional Redis 7
- Optional Docker Desktop (for spawning real `visionlab/workspace:latest` IDE containers on `visionlab-workspace-net` network with shared `visionlab_nix_store` volume); if Docker is unavailable, `app/Services/CodeServerManager.php` falls back to `VSCODE_SERVER_URL` (`http://127.0.0.1:8099/`)
- For compiling `visionlab-ide` from source (`scripts/build-code-server.sh`): Ubuntu 22.04+, `>= 8GB` RAM, Node 18/20, Yarn, `quilt`, `jq`, `libx11-dev`, `libxkbfile-dev`, `libsecret-1-dev`

**Production:**
- Linux host (actively deployed on AWS Lightsail Ubuntu VPS with aaPanel + Nginx HTTP/3 + Docker `crun` runtime, serving `visionlab.ayan24.me` per `visionlab_nginx.conf` and `AGENTS.md`; also packaged for 8-container Docker Compose deployment via `docker-compose.prod.yml` and GHCR)
- Docker daemon accessible to the Laravel web/queue process (`/var/run/docker.sock` or `/usr/bin/docker`), pre-created `visionlab-workspace-net` bridge network, `visionlab/workspace:latest` image, and `visionlab_nix_store` Docker volume
- MySQL 8.0+, Redis 7+, Laravel Reverb WebSocket daemon on port `8080`, queue worker, and cron/scheduler loop (`php artisan schedule:run`)
- TLS certificates for HTTPS & WSS (`docker/nginx/ssl/` or aaPanel `/www/server/panel/vhost/cert/`)

## Documented vs. Actual Stack Divergences

Cross-checking `AGENTS.md`, `RUNBOOK.md`, and `docker-compose.prod.yml` against `composer.json` and source files reveals notable drift:
- **Missing Laravel Operational Packages:** `AGENTS.md` and `RUNBOOK.md` mandate **Laravel Horizon**, **Laravel Pulse**, **Laravel Telescope**, **Spatie Laravel Activitylog**, and **Laravel Dusk**, and `docker-compose.prod.yml` runs `command: php artisan horizon` for `visionlab-queue`, while `app/Jobs/ExecutePlanJob.php` calls `activity('ai_operations')`. **None of these packages are installed in `composer.json`.** Audit logging is instead implemented natively in `app/Services/AuditService.php`.
- **Duplicate Build / Runtime Definitions:**
  - Two app Dockerfiles exist: root `Dockerfile` (multi-stage with Vite/Composer/opcache/intl/zip) vs `docker/app/Dockerfile` (used by `docker-compose.prod.yml` and `.github/workflows/deploy.yml`, missing `intl`/`zip`/`opcache` and not building Vite assets). Meanwhile `.github/workflows/production.yml` builds root `Dockerfile` with `target: backend`, a build stage that does not exist in `Dockerfile`.
  - Two PWA service workers exist and are both registered on pages using `resources/views/layouts/app.blade.php`: `public/pwa.js` registers `/serviceworker.js` (vanilla fetch cache) while inline script in `app.blade.php` registers `/sw.js` (Workbox 7.0.0 CDN).
  - Two IDE proxy path conventions exist across configs: `CodeServerManager.php` and `nginx_patch.conf` use `/ide/{port}/`, while `visionlab_nginx.conf`, `visionlab.rewrite.conf`, and `docker/nginx.conf` proxy `/codeserver/{port}/`.

---

*Stack analysis: 2026-11-24*

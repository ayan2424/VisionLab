# External Integrations

**Analysis Date:** 2026-11-24

## APIs & External Services

**AI & LLM Orchestration:**
- **Anthropic Messages API** (`https://api.anthropic.com/v1/messages`, `anthropic-version: 2023-06-01`) — Powers VisionLab's OpenAI-compatible AI Agent proxy (`POST /api/ai/v1/chat/completions` in `app/Http/Controllers/Api/AiController.php`) and single-turn text completions (`AiService::getDirectCompletion`). Orchestrates multi-turn tool use (`read_file`, `list_directory`, `search_codebase`, `propose_patch`, `run_terminal`) with a 10-turn recursion safety cap and streams Server-Sent Events (SSE) in OpenAI `chat.completion.chunk` format (`app/Services/AiService.php`). Defaults to model `claude-3-5-sonnet-20241022` and falls back to simulated Demo Mode when unconfigured.
  - SDK/Client: `GuzzleHttp\Client` (`guzzlehttp/guzzle`) invoked directly in `app/Services/AiService.php` (no official Anthropic PHP SDK)
  - Auth: `VISIONLAB_AI_API_KEY` (`config/visionlab.php` `ai.api_key`) with fallback to `ANTHROPIC_API_KEY`
  - Config drift note: `.env.example` declares `GEMINI_API_KEY` and `RUNBOOK.md` references `CLAUDE_API_KEY`; neither variable is read by `config/visionlab.php` or `app/Services/AiService.php`.
- **VisionLab Agent IDE Extension (Continue Fork)** — Pre-built `.vsix` (`docker/workspace/extensions/visionlab-agent-1.3.39.vsix`, forked from `continuedev/continue` v0.8.21 per `docs/VisionLab_Agent_Build_Report.md`). On container boot, `app/Services/CodeServerManager.php` writes `/home/coder/.continue/config.json` configuring models (`claude-3-5-sonnet-20241022`, `claude-3-opus-20240229`, `gpt-4o`) to route through `http://host.docker.internal:8000/api/ai/v1` (and injects `OPENAI_API_BASE` / `OPENAI_API_KEY` env vars) using Sanctum personal access tokens.
- **Dynamic Multi-Provider AI Registry (Partial / Unrouted):** `app/Http/Controllers/Api/AiConfigController.php` queries `App\Models\AiProvider` (`name`, `api_key`, `api_base`) and `App\Models\AiModel` to serve dynamic provider configs (`bedrock`, `openai`, etc.), and is imported in `routes/api.php`, but no HTTP route is currently registered for `getModels()`.

**Container Orchestration, IDE & Package Toolchains:**
- **Docker Engine Daemon (`crun` runtime)** — Per-workspace container lifecycle (`run`, `inspect`, `port`, `stats`, `exec`, `restart`, `stop`, `rm`, `volume create/rm`) executed via `Symfony\Component\Process\Process` in `app/Services/CodeServerManager.php`. Spawns `visionlab/workspace:latest` containers on isolated bridge network `visionlab-workspace-net` (ports `9000–9999`), mounts `/var/run/docker.sock` in `docker-compose.prod.yml`, and spawns ephemeral `alpine` containers for volume `chown` and stopped-workspace file I/O.
- **Nix Package Repository (`nixos.org` / `<nixpkgs>`)** — Installed via `https://nixos.org/nix/install` in `docker/workspace/Dockerfile` (and `docker/workspace/vl-entrypoint.sh`). Workspaces execute `nix-shell /home/coder/{slug}/.vision/dev.nix` sharing host volume `visionlab_nix_store:/nix` to provision per-template language runtimes without `apt`.
- **External Git Repositories (Workspace Templates)** — When a `WorkspaceTemplate` specifies `git_url`, `CodeServerManager::seedDefaultFiles()` executes `git clone <git_url> .` inside the workspace container (`app/Services/CodeServerManager.php`).
- **VS Code / Open VSX Extension Gallery** — Optional upstream extension installation via `code-server --install-extension <identifier>` (`app/Services/CodeServerManager.php`). Restricted when `global_allow_marketplace` or `Course::allow_marketplace` is false (injects `EXTENSIONS_GALLERY={"serviceUrl":""}`), with local SHA256-verified `.vsix` serving at `GET /api/extensions/{packageIdentifier}/download` (`app/Http/Controllers/ExtensionRegistryController.php`).

**Real-Time WebSockets & Broadcasting:**
- **Laravel Reverb** (`laravel/reverb` `^1.10`) — Self-hosted Pusher-protocol WebSocket server (`config/reverb.php`, `config/broadcasting.php`) proxied via Nginx `/app` and `/apps` to port `8080`.
  - SDK/Client: `laravel-echo` + `pusher-js` in browser (`resources/js/echo.js`, initialized via `window.initEcho`) and VS Code extension (`extensions/visionlab-collab/src/managers/RealtimeManager.ts`), plus `pusher/pusher-php-server` `^7.2` server-side.
  - Auth: `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`, `REVERB_HOST`, `REVERB_PORT`, `REVERB_SCHEME` (and `VITE_REVERB_*`). Channel auth hits `/broadcasting/auth` (`routes/channels.php` currently only authorizes `user.{id}`; `collab.*` and `workspace.*` channels broadcast by `PatchProposed`, `WorkspaceRebuildingEvent`, `CodeUpdated`, and `CursorMoved` are not registered in `routes/channels.php`).
  - Optional alternative broadcasters configured in `config/broadcasting.php`: Pusher Cloud (`PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_CLUSTER`) and Ably (`ABLY_KEY`).

**Browser Push Notifications (W3C Web Push / VAPID):**
- **Web Push Services (FCM / Mozilla Autopush / Apple Web Push via VAPID)** — Delivers browser push notifications for assignment grading and scheduled daily reminders (`app/Services/WebPushService.php`, `app/Notifications/AssignmentGradedNotification.php`, `app/Notifications/ScheduledReminderNotification.php`).
  - SDK/Client: `minishlink/web-push` and `laravel-notification-channels/webpush` `^10.5` server-side; browser `PushManager` API in `public/pwa.js`, `resources/views/layouts/app.blade.php`, `public/sw.js`, and `public/serviceworker.js`.
  - Auth: `VAPID_PUBLIC_KEY`, `VAPID_PRIVATE_KEY`, `VAPID_SUBJECT` (`config/webpush.php`, `config/visionlab.php`).

**Frontend CDN Services:**
- **Google Cloud Storage — Workbox CDN** (`https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js`) — Loaded by `public/sw.js` for PWA cache strategies and offline submission background sync.
- **jsDelivr CDN** (`https://cdn.jsdelivr.net`) — Serves `chart.js@4.4.0` (`resources/views/admin/analytics/index.blade.php`, `resources/views/analytics/student.blade.php`) and `three@0.164.1` ES modules (`resources/views/layouts/landing.blade.php`).
- **Google Fonts & Fontshare** (`fonts.googleapis.com`, `fonts.gstatic.com`, `api.fontshare.com`, `cdn.fontshare.com`) — Serves `Space Grotesk`, `Plus Jakarta Sans`, `Inter`, `JetBrains Mono`, `Geist`, `Instrument Serif`, and `Clash Display` (`resources/css/app.css`, `resources/views/layouts/landing.blade.php`, `resources/views/welcome.blade.php`).
- **CSP-Whitelisted External Origins** (`app/Http/Middleware/SecurityHeaders.php`) — Additionally whitelists `unpkg.com`, `cdnjs.cloudflare.com`, `*.spline.design`, `*.amazonaws.com`, and `https://15.207.144.48:8443` (legacy Jitsi endpoint).

**Email & Notification Providers (Configured, Optional):**
- **SMTP / AWS SES / Postmark / Resend / Slack** — Standard Laravel mailers and service credentials defined in `config/mail.php` and `config/services.php` (defaults to `MAIL_MAILER=log`):
  - Auth: `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `POSTMARK_TOKEN`, `RESEND_KEY`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `SLACK_BOT_USER_OAUTH_TOKEN`, `SLACK_BOT_USER_DEFAULT_CHANNEL`.

**Removed / Dormant Integrations (Present as Residual Config or Stubs Only):**
- **Jitsi Meet Video Conferencing (Decommissioned in backend):** Migration `database/migrations/2026_07_13_000000_remove_incomplete_features_tables.php` permanently dropped `video_rooms`, `video_attendances`, `recordings`, and `recording_audit_logs`. No `JitsiService` or `VideoRoomController` exists in `app/`. Residual references remain in `docker-compose.prod.yml` (`jitsi/web:latest` on port `8443`), `config/visionlab.php` (`VISIONLAB_JITSI_DOMAIN`, `VISIONLAB_JITSI_APP_ID`, `VISIONLAB_JITSI_JWT_SECRET`), `.env.example` (`JITSI_DOMAIN`, `JITSI_APP_ID`, `JITSI_APP_SECRET`), `app/Http/Controllers/HealthController.php` (`jitsi_config` probe), `app/Jobs/ProcessRecordingJob.php` (contains a Jitsi/S3 TODO), `extensions/visionlab-collab/src/panels/VideoPanel.ts`, `extensions/visionlab-collab/src/managers/RealtimeManager.ts` (posts to nonexistent `/api/workspace/{slug}/video/start`), and `public/api-docs/openapi.yaml`.
- **Vercel & Railway Student Deployments (Decommissioned / Unimplemented):** Migration `2026_07_13_000000_remove_incomplete_features_tables.php` dropped the `deployments` table and `app/Services/Deployment/` is empty. Residual encrypted token columns (`vercel_token`, `railway_token`) remain on `app/Models/User.php` and `config/visionlab.php` (`VISIONLAB_VERCEL_TOKEN`, `VISIONLAB_RAILWAY_TOKEN`).
- **Payment Gateways:** Not integrated. `App\Models\FeeChallan` and `App\Models\Transaction` exist for ERP bookkeeping, with no Stripe/Razorpay/PayU integration.

## Data Storage

**Databases:**
- **MySQL 8.0** (production primary; `config/database.php` default `DB_CONNECTION=mysql`, `mysql:8.0` container in `docker-compose.prod.yml`)
  - Connection: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_URL`, `MYSQL_ATTR_SSL_CA`
  - Client: Laravel Eloquent ORM / PDO (`pdo_mysql`, `utf8mb4_unicode_ci`, strict mode)
- **SQLite** (local dev default in `.env.example` and PHPUnit `:memory:` in `phpunit.xml` and GitHub Actions tests)
  - Connection: `DB_CONNECTION=sqlite`, `DB_DATABASE` (`database/database.sqlite` or `:memory:`)
  - Client: Laravel Eloquent ORM / PDO (`pdo_sqlite`, foreign keys enabled)

**File Storage:**
- **Local Filesystem** (`config/filesystems.php` default `FILESYSTEM_DISK=local`):
  - `local` disk (`storage/app/private`) — private application storage, GDPR exports (`gdpr_exports/` in `app/Jobs/GdprDataExportJob.php`), CSV enrollment uploads (`app/Jobs/CsvEnrollmentJob.php`)
  - `public` disk (`storage/app/public` symlinked to `public/storage`) — public uploads
  - `storage/app/extensions` — read-only bind mount (`/var/opt/extensions:ro`) into workspace containers for SHA256-verified `.vsix` extension artifacts
- **Docker Native Volumes** — `vl_ws_{workspace_id}_data` (per-workspace user code mounted at `/home/coder/{slug}`) and `visionlab_nix_store` (shared Nix store mounted at `/nix`), managed via `app/Services/CodeServerManager.php`.
- **AWS S3 (Configured Template, Unused by Default):** `s3` disk defined in `config/filesystems.php` (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_URL`, `AWS_ENDPOINT`, `AWS_USE_PATH_STYLE_ENDPOINT`), but `league/flysystem-aws-s3-v3` is not installed in `composer.json`.

**Caching, Queues & Sessions:**
- **Database Driver (Default):** `CACHE_STORE=database` (`cache` table), `QUEUE_CONNECTION=database` (`jobs`, `job_batches`, `failed_jobs` tables), `SESSION_DRIVER=database` (`sessions` table) per `.env.example` and `config/cache.php`, `config/queue.php`, `config/session.php`.
- **Redis 7** (`redis:7-alpine` in `docker-compose.prod.yml`): Configured in `config/database.php` (`default` DB 0, `cache` DB 1) using `REDIS_CLIENT` (`predis` default in config / `phpredis` in `.env.example` & Dockerfiles), `REDIS_URL`, `REDIS_HOST`, `REDIS_PORT`, `REDIS_USERNAME`, `REDIS_PASSWORD`. Directly required by `GET /healthz` (`Redis::connection()->ping()` in `app/Http/Controllers/HealthController.php`) and optional Reverb horizontal scaling (`REVERB_SCALING_ENABLED`).

## Authentication & Identity

**Auth Provider:**
- **Custom First-Party Laravel Authentication** — No external OAuth/SSO/IdP (no Socialite, Auth0, Clerk, or LTI provider implemented).
  - **Web Sessions:** Laravel Breeze (`laravel/breeze` `^2.4`) with `web` Eloquent session guard (`config/auth.php`), bcrypt password hashing (`BCRYPT_ROUNDS=12`), email verification (`verified` middleware), and password reset (`routes/auth.php`). Public registration is disabled in `routes/auth.php` (`GET /register` redirects to `/login`); accounts are created via Admin Panel or CSV import.
  - **API & IDE Tokens:** Laravel Sanctum (`laravel/sanctum` `^4.3`) with `HasApiTokens` on `App\Models\User`, stateful cookie domains (`SANCTUM_STATEFUL_DOMAINS` in `config/sanctum.php`), and Bearer tokens injected into workspace containers (`VISIONCODE_API_TOKEN`, `OPENAI_API_KEY`).
  - **Workspace Tokens:** Random 32–64 char `Workspace::$token` transmitted via unencrypted `key` cookie (`bootstrap/app.php`) and Bearer header for extension-triggered rebuilds.
  - **Authorization:** Custom role middleware (`app/Http/Middleware/RoleMiddleware.php`, roles: `admin`, `instructor`, `student`, `subscriber`), account suspension guard (`CheckAccountStatus`), and 14 Eloquent policies in `app/Policies/`.

## Monitoring & Observability

**Error Tracking:**
- None integrated (no Sentry, Bugsnag, Flare, or Datadog APM SDK installed; `PULSE_ENABLED` and `TELESCOPE_ENABLED` appear in `phpunit.xml` as scaffolding defaults, but neither `laravel/pulse` nor `laravel/telescope` is installed).

**Health & Uptime Monitoring:**
- `GET /up` — Built-in Laravel 11 framework probe (`bootstrap/app.php`).
- `GET /healthz` — Machine-readable 6-probe JSON endpoint (`database`, `redis`, `reverb`, `storage`, `ai_config`, `jitsi_config`; returns HTTP 200 if DB and Redis succeed, else 503) in `app/Http/Controllers/HealthController.php` for external uptime monitors (e.g., UptimeRobot per `RUNBOOK.md`).
- `GET /admin/status` — Admin-only status dashboard rendering probe checks with 60-second auto-reload (`resources/views/admin/status.blade.php`).
- `GET /api/health` — Lightweight DB-only JSON health endpoint in `routes/api.php` (note: `RUNBOOK.md` mistakenly states `/api/health` runs the 6 probes; those live at `/healthz`).

**Logs & Forensics:**
- **Monolog JSON Logs:** `config/logging.php` formats `single` and `daily` (14-day rotation) logs in `storage/logs/laravel.log` using `\Monolog\Formatter\JsonFormatter` for ELK/Datadog ingestion. Optional `slack` (`LOG_SLACK_WEBHOOK_URL`) and `papertrail` (`PAPERTRAIL_URL`, `PAPERTRAIL_PORT`) channels are pre-configured.
- **Database Audit & Forensics:** First-party `AuditService` (`audit_logs`), `AnalyticsObserver` & `VisionGuardController` (`analytics_events`), `AiActionsLog` (`ai_actions_logs`), and `ForensicsController` (`submission_forensics`).

## CI/CD & Deployment

**Hosting:**
- **Primary Active Host:** AWS Lightsail Ubuntu VPS (`ubuntu@15.207.144.48`, domain `visionlab.ayan24.me`), managed via **aaPanel** (`/www/server/panel/vhost/nginx/`, web root `/www/wwwroot/visionlab.ayan24.me/` or `/www/wwwroot/visionlab/` per `AGENTS.md`, `visionlab_nginx.conf`, `.github/workflows/deploy.yml`) running Nginx (HTTP/2 + HTTP/3 QUIC), PHP-FPM, Reverb on `127.0.0.1:8080`, and Docker (`crun` runtime) for IDE workspace containers.
- **Containerized Stack Definition:** `docker-compose.prod.yml` defines an 8-service deployment (`visionlab-app`, `visionlab-queue`, `visionlab-reverb`, `visionlab-scheduler`, `visionlab-nginx`, `visionlab-mysql`, `visionlab-redis`, `visionlab-jitsi`) pulling from GitHub Container Registry (`ghcr.io/visionlab-edu/visionlab-app:latest`).
- **Backup & Restore:** Shell scripts `scripts/backup.sh` and `scripts/restore.sh` dump/restore MySQL via `docker exec visionlab_db mysqldump` and archive `storage/app/public` to `/var/backups/visionlab` with 7-day retention.

**CI Pipeline:**
- **GitHub Actions** (`.github/workflows/`):
  - `.github/workflows/deploy.yml` ("Deploy VisionLab Enterprise") — On push to `main`: runs PHP 8.3 `php artisan test --testsuite=Feature` (SQLite) → builds & pushes `docker/app/Dockerfile` to `ghcr.io/${{ github.repository }}:latest` using `docker/login-action@v3` & `docker/build-push-action@v5` → deploys via SSH (`appleboy/ssh-action@v1.0.3`) to `/www/wwwroot/visionlab`.
  - `.github/workflows/production.yml` ("Production Deployment") — Second workflow also triggered on push to `main`: runs PHP 8.3 + Node 20 (`npm ci && npm run build`) + `php artisan test` → builds & pushes `ghcr.io/${{ github.repository }}/visionlab-app:latest` with `target: backend` (Note: root `Dockerfile` has no `backend` stage) and contains a commented-out SSH deployment block.
  - `.github/workflows/compile-artifacts.yml` ("Compile VisionLab IDE & Agent") — On push to `main` touching `visionlab-ide/**` or `storage/extensions/continue-source/**` (or manual `workflow_dispatch`): compiles Continue fork `.vsix` (`visionlab-agent-latest.vsix`) and custom `code-server` tarball (`visionlab-ide-latest-linux-amd64.tar.gz`) on `ubuntu-latest` / Node 20 and uploads 7-day artifacts via `actions/upload-artifact@v4`.

## Environment Configuration

**Required env vars (names only — never commit values):**
- **Core App:** `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `APP_TIMEZONE`, `BCRYPT_ROUNDS`
- **Database & Redis:** `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `REDIS_CLIENT`, `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD`, `REDIS_DB`, `REDIS_CACHE_DB`, `CACHE_STORE`, `QUEUE_CONNECTION`, `SESSION_DRIVER`
- **WebSockets (Reverb):** `BROADCAST_CONNECTION`, `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`, `REVERB_HOST`, `REVERB_PORT`, `REVERB_SCHEME`, `REVERB_SCALING_ENABLED`, `VITE_REVERB_APP_KEY`, `VITE_REVERB_HOST`, `VITE_REVERB_PORT`, `VITE_REVERB_SCHEME`
- **AI Agent:** `VISIONLAB_AI_PROVIDER`, `VISIONLAB_AI_MODEL`, `VISIONLAB_AI_API_KEY` (or fallback `ANTHROPIC_API_KEY`), `VISIONLAB_AI_MAX_TOKENS`, `VISIONLAB_AI_TEMPERATURE`, `VISIONLAB_AI_DAILY_LIMIT`
- **Workspaces & IDE:** `VISIONLAB_CONTAINER_PREFIX`, `VISIONLAB_CONTAINER_IMAGE`, `VISIONLAB_STORAGE_PATH`, `VISIONLAB_BASE_PORT`, `VISIONLAB_PROXY_HOST`, `VISIONLAB_DEFAULT_MEMORY`, `VISIONLAB_DEFAULT_CPU_SHARES`, `VISIONLAB_DEFAULT_DISK`, `VISIONLAB_DEFAULT_TIMEOUT`, `VSCODE_SERVER_PORT`, `VSCODE_SERVER_URL`
- **Auth & Webhooks:** `SANCTUM_STATEFUL_DOMAINS`, `SANCTUM_TOKEN_PREFIX`, `GITHUB_WEBHOOK_SECRET`
- **Web Push (VAPID):** `VAPID_PUBLIC_KEY`, `VAPID_PRIVATE_KEY`, `VAPID_SUBJECT`
- **Mail & Logging:** `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`, `LOG_CHANNEL`, `LOG_STACK`, `LOG_LEVEL`
- **Legacy / Dormant Vars Still Referenced in Config/Compose:** `VISIONLAB_JITSI_DOMAIN`, `VISIONLAB_JITSI_APP_ID`, `VISIONLAB_JITSI_JWT_SECRET`, `JITSI_DOMAIN`, `JITSI_APP_ID`, `JITSI_APP_SECRET`, `VISIONLAB_VERCEL_TOKEN`, `VISIONLAB_RAILWAY_TOKEN`, `GEMINI_API_KEY`

**Secrets location:**
- Local environment secrets: `.env` (gitignored in `.gitignore`).
- Remote environment file: `.env.remote` exists in the repository root and is **NOT currently listed in `.gitignore`** (must be treated as sensitive and ignored/removed from version control).
- SSH deployment key: `LightsailDefaultKey-ap-south-1.pem` exists in the repository root (`*.pem` is gitignored in `.gitignore`).
- CI/CD secrets: GitHub Actions repository secrets (`GITHUB_TOKEN`, `SERVER_HOST`, `SERVER_USER`, `SERVER_SSH_KEY`, `PROD_HOST`, `PROD_USERNAME`, `PROD_SSH_KEY`).
- Database-stored credentials: `users.vercel_token` and `users.railway_token` use Laravel `'encrypted'` casts (`app/Models/User.php`); `ai_providers.api_key` (`app/Models/AiProvider.php`) and `webhooks.secret` (`app/Models/Webhook.php`) are stored without `'encrypted'` model casts.

## Webhooks & Callbacks

**Incoming:**
- `POST /api/webhook/deploy` (`app/Http/Controllers/WebhookController.php`, CSRF-exempt in `bootstrap/app.php`) — GitHub push webhook endpoint. Validates `X-Hub-Signature-256` HMAC-SHA256 signature against `config('services.github.webhook_secret')` (`GITHUB_WEBHOOK_SECRET`), filters for `ref === 'refs/heads/main'`, and executes `nohup bash <base_path>/deploy.sh > /dev/null 2>&1 &` in the background (note: `deploy.sh` is not committed in the repository root).
- `POST /api/workspace/{slug}/trigger-rebuild` (`routes/api.php`) — Internal callback triggered by the injected `visionlab-nix-watcher` VS Code extension upon saving `.vision/dev.nix`. Authenticated via workspace bearer token (`Workspace::$token`) and broadcasts `App\Events\WorkspaceRebuildingEvent`.
- `POST /api/workspace/{slug}/forensics/sync` (`app/Http/Controllers/Api/ForensicsController.php`) & `POST /api/visionguard/log` (`app/Http/Controllers/VisionGuardController.php`) — Sanctum-authenticated telemetry ingestion callbacks from IDE extensions and frontend exam/coding monitors.

**Outgoing:**
- **Admin-Managed Outgoing Webhooks (CRUD Only — Dispatch Not Implemented):** `app/Models/Webhook.php` and `app/Http/Controllers/Admin/AdminWebhookController.php` (`admin.webhooks.*`) allow admins to register outbound webhook URLs, optional HMAC-SHA256 secrets, and event subscriptions (`workspace.started`, `workspace.stopped`, `user.registered` in `resources/views/admin/webhooks/create.blade.php`), but no outbound HTTP job or event listener currently dispatches payloads to registered endpoints.

---

*Integration audit: 2026-11-24*

# VisionLab— Local Setup Guide

**Competition-grade AI-powered collaborative coding ecosystem for Aptech Vision 2026**

Stack: Laravel 11 · SQLite · Blade · Tailwind CSS · Monaco Editor · Laravel Reverb · Gemini 2.0 Flash · ApexCharts

---

## Prerequisites

| Tool | Version | Install |
|------|---------|---------|
| PHP | 8.2+ (8.4 recommended) | [php.net](https://www.php.net/downloads) |
| Composer | 2.x | [getcomposer.org](https://getcomposer.org/) |
| Node.js | 18+ (20 recommended) | [nodejs.org](https://nodejs.org/) |
| npm | 9+ (bundled with Node) | — |
| Git | any | [git-scm.com](https://git-scm.com/) |

**Platform notes:**
- **Windows** — Use [Laragon](https://laragon.org/) (recommended, includes PHP + Composer + Node) or WSL2 with Ubuntu
- **macOS** — `brew install php composer node`
- **Linux (Ubuntu/Debian)** — `sudo apt install php8.4 php8.4-sqlite3 php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip nodejs npm` then `curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer`

---

## Quick Start (Copy-Paste)

```bash
# 1. Install PHP + Node dependencies
composer install
npm install

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Database
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# 4. Build front-end assets
npm run build

# 5a. Terminal 1 — Web server
php artisan serve

# 5b. Terminal 2 — WebSocket server (real-time collaboration)
php artisan reverb:start --host=0.0.0.0 --port=8080 --debug

# 6. Open in browser
# http://localhost:8000
# Login: admin@visioncode.ai / Admin@12345
```

---

## Demo Credentials

| Role | Email | Password | Access |
|------|-------|----------|--------|
| Admin | admin@visioncode.ai | Admin@12345 | Everything — IDE, Analytics, Demo Script |
| Instructor | instructor@visioncode.ai | Instructor@12345 | IDE + AI Agent + Collaboration |
| Student | student@visioncode.ai | Student@12345 | IDE + AI Agent + Collaboration |

---

## Detailed Step-by-Step Setup

### Step 1 — Clone the Repository

```bash
git clone <your-repo-url> visioncode-ai
cd visioncode-ai
```

Or copy the project folder and `cd` into it.

---

### Step 2 — Install PHP Dependencies

```bash
composer install
```

Installs Laravel 11, Reverb, and all backend packages from `composer.json`.

---

### Step 3 — Install Node Dependencies

```bash
npm install
```

Installs Vite, Tailwind CSS, Alpine.js, and front-end tooling.

---

### Step 4 — Configure the Environment

```bash
cp .env.example .env
php artisan key:generate
```

Then edit `.env` with your settings:

```dotenv
# ── Core ──────────────────────────────────────────
APP_NAME="VisionLab"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# ── Database (SQLite — no server needed) ──────────
DB_CONNECTION=sqlite
# Leave DB_DATABASE commented to use database/database.sqlite

# ── AI Agent (optional — demo mode works without) ─
GEMINI_API_KEY=AIza...your_key_here...

# ── Real-time WebSocket (Reverb) ──────────────────
REVERB_APP_ID=181307
REVERB_APP_KEY=pzs37btwtka3rhlyi13s
REVERB_APP_SECRET=visioncode_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

> **GEMINI_API_KEY** is optional. Without it the AI Agent runs in smart demo mode — it produces realistic-looking responses using built-in rules, no API call needed. Get a free key at [aistudio.google.com/app/apikey](https://aistudio.google.com/app/apikey) (15 req/min, 1M tokens/day free).

---

### Step 5 — Set Up the Database

```bash
# Create the SQLite file
touch database/database.sqlite

# Run all migrations (creates tables)
php artisan migrate

# Seed demo users
php artisan db:seed
```

---

### Step 6 — Build Front-end Assets

```bash
npm run build
```

This compiles Tailwind CSS and JavaScript into `public/build/`.

For development with live-reload, use `npm run dev` instead (keep it running in its own terminal alongside the PHP server).

---

### Step 7 — Start the Servers

You need **two terminals open at the same time**:

**Terminal 1 — Laravel Web Server:**
```bash
php artisan serve
# → Server running at http://localhost:8000
```

**Terminal 2 — Reverb WebSocket Server:**
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080 --debug
# → WebSocket server on ws://localhost:8080
```

> You can skip Terminal 2 if you don't need real-time collaboration (presence avatars / multi-cursor). Everything else — IDE, AI Agent, Analytics, Demo Script — works without it.

---

### Step 8 — Open in Browser

```
http://localhost:8000
```

The landing page appears. Click **Sign In** and use any credential from the table above.

---

## Feature Map

| URL | Feature | Auth Required |
|-----|---------|---------------|
| `/` | Landing page | No |
| `/login` | Sign in | Guest only |
| `/register` | Create account | Guest only |
| `/forgot-password` | Request password reset | Guest only |
| `/workspace` | Monaco IDE + AI Agent + Collaboration | Any logged-in user |
| `/dashboard` | Auto-redirects by role | Logged-in |
| `/profile` | Account settings + password change | Logged-in |
| `/admin/analytics` | Analytics dashboard (ApexCharts) | Admin only |
| `/demo` | Interactive 8-step Judge Demo Script | Admin only |

---

## AI Agent Modes

Open the Workspace IDE and click the **AI** toggle button in the header:

| Mode | Behaviour |
|------|-----------|
| **CHAT** | Conversational Q&A — explain code, answer questions, suggest improvements |
| **PLAN** | Structured plan — numbered steps, effort estimate, risk assessment |
| **AGENT** | Generates a full diff patch — review the before/after diff, then click Apply to insert the code |

With a valid `GEMINI_API_KEY`: calls Gemini 2.0 Flash (temperature 0.2 for Agent, 0.7 for Chat).
Without a key: smart rule-based demo responses — detects code structure, language, and instruction type.

---

## Code Execution Languages

The workspace supports 12 languages via the [Piston API](https://emkc.org/api/v2/piston/runtimes) (no setup needed — it's a public API):

Python · JavaScript · TypeScript · Java · C++ · C · Go · Rust · PHP · Ruby · Kotlin · Swift

---

## Real-time Collaboration

1. Open `/workspace` in two browser tabs (or two different browsers/incognito windows)
2. Log in as different users in each tab
3. You will see:
   - **Presence avatars** — coloured initials of all active users in the header
   - **Multi-cursor decorations** — other users' cursors and selections highlighted in the editor
   - **Live code sync** — edits broadcast instantly via Laravel Reverb WebSocket

---

## Development Workflow

```bash
# Hot-reload development (Tailwind JIT + Alpine)
npm run dev              # keep this running
php artisan serve        # separate terminal

# After changing routes or config
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reset database (WARNING: destroys all data)
php artisan migrate:fresh --seed

# View all routes
php artisan route:list

# Run seeder again (doesn't duplicate if already seeded)
php artisan db:seed --force
```

---

## Project Structure

```
visioncode-ai/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AiAgentController.php       ← Gemini 2.0 Flash + smart demo fallback
│   │   │   ├── AnalyticsController.php     ← Admin analytics data (12 variables → ApexCharts)
│   │   │   ├── CollaborationController.php ← Reverb presence + broadcast events
│   │   │   └── WorkspaceController.php     ← Monaco IDE workspace controller
│   │   └── Middleware/
│   │       └── RoleMiddleware.php          ← RBAC guard (admin/instructor/student)
│   ├── Models/User.php                     ← Role helpers: isAdmin(), isInstructor(), avatar_initials
│   └── Providers/AppServiceProvider.php    ← URL force-fix for proxied environments (Replit, ngrok, etc.)
│
├── resources/
│   ├── css/app.css                         ← Tailwind + custom dark-theme utility classes
│   ├── js/app.js                           ← Alpine.js bootstrap
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php               ← Primary dark shell layout
│       │   ├── guest.blade.php             ← Auth page layout (blobs + glass card)
│       │   └── navigation.blade.php        ← Dark top nav with role-aware links
│       ├── auth/                           ← login, register, forgot-password, reset-password
│       ├── workspace/index.blade.php       ← Monaco Editor (split-pane, 12 langs, AI panel)
│       ├── profile/edit.blade.php          ← Account settings (dark sidebar layout)
│       ├── analytics.blade.php             ← ApexCharts dashboard (admin — 4 charts + heatmap)
│       ├── demo.blade.php                  ← 8-step interactive judge demo script
│       └── welcome.blade.php               ← Landing page (hero + features + stats)
│
├── routes/web.php                          ← All routes + role middleware
├── database/
│   ├── migrations/                         ← All schema migrations
│   ├── seeders/DatabaseSeeder.php          ← Seeds 3 demo users with bcrypt passwords
│   └── database.sqlite                     ← SQLite database file
│
├── public/
│   └── build/                             ← Compiled CSS + JS (output of npm run build)
│
├── .env                                    ← Local environment config (not committed)
├── .env.example                            ← Template for new environments
├── vite.config.js                          ← Vite + laravel-vite-plugin config
├── tailwind.config.js                      ← Custom dark theme (void, surface, elevated colours)
└── package.json                            ← Node deps (Vite 6, Tailwind 3, Alpine 3)
```

---

## Troubleshooting

### Styles not loading / white page

```bash
npm run build
php artisan view:clear
```
Hard-refresh the browser (`Ctrl+Shift+R`).

### "No application encryption key has been specified"

```bash
php artisan key:generate
```

### "Database file not found" / SQLite error

```bash
touch database/database.sqlite
php artisan migrate
```

### Reverb WebSocket connection fails

1. Confirm `php artisan reverb:start` is running in a second terminal
2. Verify `.env` has `REVERB_HOST=localhost` and `REVERB_PORT=8080`
3. Check the browser console for WebSocket errors
4. On Windows, ensure port 8080 is not blocked by Windows Firewall

### Port 8000 already in use

```bash
php artisan serve --port=8001
# Update APP_URL=http://localhost:8001 in .env
```

### Composer install fails (PHP version mismatch)

The project requires PHP 8.2+. Check: `php --version`. On systems with multiple PHP versions:
```bash
php8.2 $(which composer) install
```

### "Class not found" errors after pulling changes

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### AI Agent returns "API error"

Either your `GEMINI_API_KEY` is invalid/expired, or you've exceeded the free quota.
The system automatically falls back to smart demo mode — the AI panel will still work.

---

## Environment Variables Reference

| Variable | Description | Default | Required |
|----------|-------------|---------|----------|
| `APP_KEY` | Laravel encryption key | — | Yes (generate with `php artisan key:generate`) |
| `APP_URL` | Base URL of the app | `http://localhost` | Yes |
| `APP_ENV` | Environment (`local` / `production`) | `local` | Yes |
| `DB_CONNECTION` | Database driver | `sqlite` | Yes |
| `GEMINI_API_KEY` | Google Gemini 2.0 Flash API key | empty | No (demo mode works without) |
| `REVERB_APP_ID` | Reverb app identifier | `181307` | For collaboration |
| `REVERB_APP_KEY` | Reverb app key | `pzs37btwtka3rhlyi13s` | For collaboration |
| `REVERB_APP_SECRET` | Reverb app secret | — | For collaboration |
| `REVERB_HOST` | WebSocket host | `localhost` | For collaboration |
| `REVERB_PORT` | WebSocket port | `8080` | For collaboration |
| `VITE_REVERB_*` | Exposes Reverb config to browser JS | mirrors `REVERB_*` | For collaboration |

---

## Getting a Free Gemini API Key

1. Visit [https://aistudio.google.com/app/apikey](https://aistudio.google.com/app/apikey)
2. Sign in with any Google account
3. Click **Create API Key** → **Create API key in new project**
4. Copy the key (starts with `AIza`)
5. Paste it in `.env`: `GEMINI_API_KEY=AIza...`
6. Restart the PHP server

Free tier: 15 requests/minute · 1,500 requests/day · 1 million tokens/day — more than enough for any demo.

---

*VisionLab— Built for Aptech Vision 2026 · Laravel 11 · PHP 8.4 · Node 20 · Vite 6 · Tailwind CSS 3 · Alpine.js 3 · Monaco Editor · ApexCharts · Laravel Reverb*

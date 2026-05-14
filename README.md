# VisionLab — Ultimate AI-Powered Collaborative Learning Platform

**Competition-grade ecosystem for Aptech Vision 2026**

VisionLab is a revolutionary platform that replaces Google Classroom, Zoom, and GitHub Copilot with a single, unified, dark-themed ecosystem. It spawns real **VS Code (code-server) Docker containers** for every workspace, featuring real-time multiplayer collaboration, integrated video conferencing, and an autonomous AI agent directly inside the IDE.

**Tech Stack:** Laravel 11 · MySQL 8 · Blade & Tailwind CSS (Custom Dark Theme) · Docker (code-server) · Laravel Reverb (WebSockets) · Anthropic Claude Opus · Jitsi Meet · PWA

---

## 🌟 Key Features

1. **Classroom Management:** Full LMS capabilities including courses, enrollments, announcements, assignments, and automated grading pipelines.
2. **Real-Time Docker IDEs:** Spawns an isolated `code-server` Docker container for every workspace. No bare Monaco editor—students get the full VS Code experience with terminals and extensions.
3. **Multiplayer Collaboration:** Real-time cursor syncing, live code broadcasting, and presence avatars powered by Laravel Reverb and custom VS Code extensions.
4. **VisionLab Agent (AI):** A heavily customized integration of the open-source **Continue** extension. Features include:
   - **`/ask`**: Intelligent chat and codebase explanation.
   - **`/plan`**: Strategic planning with a one-click `[🚀 Start Implementation]` button.
   - **`/agent`**: Autonomous coding agent that proposes patches which you can approve/reject via our custom Diff Viewer.
5. **Video Conferencing:** Integrated Jitsi Meet white-label video calls embedded directly within the workspace.
6. **Admin & Instructor Control:** Toggle IDE extensions globally or per-workspace, monitor real-time analytics, and manage user roles.
7. **PWA Ready:** Installable Progressive Web App with offline fallback pages, precached assets, and web-push notifications.

---

## 📋 Prerequisites

| Tool | Version | Purpose |
|------|---------|---------|
| **PHP** | 8.3+ | Backend framework (Laravel 11) |
| **Composer** | 2.x | PHP dependency management |
| **Node.js & npm** | 20+ | Frontend asset building (Vite) |
| **MySQL** | 8.0+ | Relational database |
| **Docker** | Latest | **MANDATORY** for spawning workspace IDEs |

---

## 🚀 Quick Start (Copy-Paste)

### 1. Install Dependencies
```bash
git clone <your-repo-url> visionlab
cd visionlab

composer install
npm install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

Edit your `.env` file to configure your MySQL connection and API Keys:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=visionlab
DB_USERNAME=root
DB_PASSWORD=

# Required for the VisionLab Agent
ANTHROPIC_API_KEY=sk-ant-api03-... 

# Real-time WebSocket (Reverb)
REVERB_APP_ID=181307
REVERB_APP_KEY=visionlab_key
REVERB_APP_SECRET=visionlab_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 3. Database Migration & AI Agent Rebranding
VisionLab programmatically rebrands the open-source Continue AI extension into the **VisionLab Agent**.

```bash
# Ensure MySQL Server is running, then migrate and seed the database
php artisan migrate:fresh --seed

# Rebrand and compile the AI Extension (Required for workspaces)
php storage/extensions/rebrand_continue.php
```

### 4. Build Frontend Assets
```bash
npm run build
```

### 5. Start the Services
You need **three terminals** open to run the full ecosystem:

**Terminal 1: Laravel Web Server**
```bash
php artisan serve
```

**Terminal 2: Reverb WebSocket Server (For Collab & Live Sync)**
```bash
php artisan reverb:start --debug
```

**Terminal 3: Horizon / Queue Worker (For background AI processing)**
```bash
php artisan queue:work
```

*(Ensure Docker Desktop / Docker Daemon is running in the background!)*

---

## 🔑 Demo Credentials

| Role | Email | Password | Access |
|------|-------|----------|--------|
| **Admin** | admin@visionlab.test | password | Analytics, Global Extension Controls, Demo Script |
| **Instructor** | instructor@visionlab.test | password | Course Creation, Assignments, IDE + Collab |
| **Student** | student@visionlab.test | password | Course Enrollment, Submissions, IDE + Collab |

---

## 🧠 AI Agent Modes & One-Click Execution

Open any Workspace IDE. The VisionLab Agent is pre-installed. Type the following slash commands in the AI chat:

1. Type `/plan Create a new profile settings view`.
2. The AI will generate a detailed architectural plan.
3. At the bottom of the plan, a **`[🚀 Start Implementation]`** button will appear.
4. Click the button. The backend autonomously takes over, reads the plan, and triggers the `propose_patch` pipeline.
5. The **Diff Viewer** automatically pops up in your IDE, allowing you to Approve & Apply the generated code.

---

## 🛠️ Project Architecture

```
visionlab/
├── app/
│   ├── Http/Controllers/     ← Classroom, Workspaces, Video, Admin
│   ├── Services/
│   │   ├── AiService.php         ← Core AI logic, Anthropic API streaming
│   │   ├── AiSandbox.php         ← Restricts AI writes to specific folders
│   │   └── CodeServerManager.php ← Docker orchestration for workspaces
├── resources/
│   ├── views/                ← Blade Templates (Dark Theme, Glassmorphism)
│   └── js/                   ← Alpine.js, Echo listeners, PWA registration
├── storage/
│   └── extensions/           
│       ├── rebrand_continue.php        ← Script to build visionlab-ai.vsix
│       ├── visioncode-collab/          ← Custom VS Code Collab extension
│       └── visioncode-patch-reviewer/  ← Custom Diff Viewer extension
└── public/
    └── serviceworker.js      ← PWA Offline fallback and caching
```

---

## 🛑 Troubleshooting

### Docker Containers Failing to Start
- Ensure Docker is running (`docker info`).
- Ensure port 9000-9100 are free (VisionLab dynamically assigns ports to `code-server` containers).

### Real-Time Sync Not Working
- Ensure `php artisan reverb:start` is actively running.
- Check browser console for WebSocket connection refused errors (verify `.env` Reverb host/port match the frontend).

### AI Agent Not Responding
- Verify `ANTHROPIC_API_KEY` is set in `.env`.
- Ensure the `php storage/extensions/rebrand_continue.php` script ran successfully and generated `visionlab-ai.vsix`.

---

*Built by the VisionForge Team for Aptech Vision 2026. Excellence is not an act, but a habit.*

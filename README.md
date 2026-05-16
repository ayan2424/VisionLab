# VisionLab — Ultimate AI-Powered Collaborative Learning Platform

**Competition-grade enterprise ecosystem for Aptech Vision 2026**

VisionLab is a revolutionary platform that replaces Google Classroom, Zoom, and GitHub Copilot with a single, unified, dark-themed enterprise ecosystem. It spawns real **VS Code (code-server) Docker containers** for every workspace, featuring real-time multiplayer collaboration, integrated video conferencing, and an autonomous AI agent directly inside the IDE.

**Tech Stack:** Laravel 11 · MySQL 8 · Blade & Tailwind CSS (Custom Dark Theme) · Docker (code-server) · Laravel Reverb (WebSockets) · Anthropic Claude & Gemini · Jitsi Meet · PWA

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
8. **VisionGuard AI Forensics:** Keeps track of pasted AI-generated code versus real student keystroke telemetry, visualising effort statistics for instructors via Chart.js.
9. **Visual Gamification:** Dynamic streaks tracker, contribution heatmap, and modern unlocked glassmorphic badges.

---

## 📋 General Prerequisites

| Tool | Version | Purpose |
|------|---------|---------|
| **PHP** | 8.2+ (Recommended 8.3+) | Backend framework (Laravel 11) |
| **Composer** | 2.x | PHP dependency management |
| **Node.js & npm** | 20+ | Frontend asset building (Vite) |
| **MySQL** | 8.0+ | Relational database |
| **Docker** | Latest | **MANDATORY** for spawning workspace IDEs |

---

## 🖥️ Operating System Specific Setup Guide

Follow the custom-tailored steps for your OS to avoid permission, server, and networking blockers:

### 🐧 1. Linux (Ubuntu / Debian / Fedora)

#### **Step A: Start & Verify MySQL Database**
Ensure your local database service is active:
```bash
# Check status
sudo systemctl status mysql

# If stopped, start it
sudo systemctl start mysql

# Enable it to start on boot
sudo systemctl enable mysql
```

#### **Step B: Docker Socket Permissions (CRITICAL)**
By default, Docker requires root (`sudo`) permissions, which will block Laravel from spawning containers. Grant access to your system user:
```bash
# Create the docker group if it doesn't exist
sudo groupadd docker

# Add your current user to the docker group
sudo usermod -aG docker $USER

# Apply group changes instantly without restarting
newgrp docker

# Fix the socket file permission directly (Bulletproof fallback)
sudo chmod 666 /var/run/docker.sock
```

---

### 🍎 2. macOS (Intel / Apple Silicon M1/M2/M3)

#### **Step A: Start & Verify MySQL Database**
If using Homebrew:
```bash
# Check running Homebrew services
brew services list

# Start MySQL service
brew services start mysql
```
*(If using DBngin or MAMP, open the desktop GUI app and ensure the MySQL server is green/running on port `3306`)*

#### **Step B: Docker Desktop Advanced Configuration (CRITICAL)**
For Laravel to communicate with Docker Desktop on macOS, you must authorize socket sharing:
1. Open **Docker Desktop**.
2. Click the ⚙️ **Settings** (Gear Icon) in the top-right corner.
3. Navigate to **Advanced** tab.
4. Locate the option **"Allow the default Docker socket to be used"** (or *System / User socket path sharing*) and check the box.
5. Click **Apply & Restart**.
6. Verify access in Terminal:
   ```bash
   ls -la /var/run/docker.sock
   # Should display a valid socket reference link without permission issues
   ```

---

### 🪟 3. Windows (WSL2 / Native Git Bash)

We **strongly recommend** running this project inside **WSL2 (Windows Subsystem for Linux)** with an Ubuntu distribution for production stability.

#### **Option A: If using WSL2 (Recommended)**
1. Ensure your **Docker Desktop for Windows** has **WSL2 integration** enabled (Settings -> Resources -> WSL Integration -> toggle your Ubuntu distro).
2. Inside your WSL terminal, start MySQL:
   ```bash
   sudo service mysql start
   ```
3. Run the project commands directly inside the WSL terminal just like a native Linux machine.

#### **Option B: If using Native Windows (Git Bash / Command Prompt)**
1. **Start MySQL Server:**
   - If using **XAMPP**: Open XAMPP Control Panel and click "Start" next to MySQL.
   - If using **WampServer**: Launch WampServer and ensure the icon turns green (MySQL service active).
   - If using native MySQL: Open Command Prompt as Administrator and run:
     ```cmd
     net start mysql
     ```
2. **Start Docker Desktop:** Ensure Docker Desktop is running in the background.

---

## 🚀 Step-by-Step Installation

### Step 1: Clone the Repository & Install Dependencies
Clone the repository to your local directory:
```bash
git clone <your-repo-url> visionlab
cd visionlab

# Install Laravel Backend Packages
composer install

# Install Frontend Node Packages
npm install
```

### Step 2: Environment Configuration
Copy the sample environment file:
```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` in your code editor and configure your database and third-party details:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=visionlab
DB_USERNAME=root       # Change to your MySQL username
DB_PASSWORD=           # Change to your MySQL password

# Required for the VisionLab AI Agent Chat
ANTHROPIC_API_KEY=sk-ant-api03-... 
GEMINI_API_KEY=your-gemini-key-here
AI_PROVIDER=gemini # 'gemini' or 'anthropic'

# Real-time WebSocket (Laravel Reverb)
REVERB_APP_ID=181307
REVERB_APP_KEY=pzs37btwtka3rhlyi13s
REVERB_APP_SECRET=a6mepstuprpdqc85pxgs
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${VITE_REVERB_SCHEME}"
```

### Step 3: Database Seeding & Migration
To avoid landing on an empty database (which triggers the `"These credentials do not match our records"` login failure), run the migration along with our predefined seeders:
```bash
# Wipes previous tables (if any) and migrates fresh schemas with test users
php artisan migrate:fresh --seed
```

### Step 4: Build Frontend Assets (Heap OOM Fix)
Because our codebase includes full customized extensions and heavy GUI builds, default node environments might run out of memory. **Use this specific command to compile:**
```bash
# Sets standard Node heap space to 4GB before compiling with Vite
export NODE_OPTIONS=--max-old-space-size=4096 && npm run build
```
*(On Windows cmd/powershell use: `$env:NODE_OPTIONS="--max-old-space-size=4096"` followed by `npm run build`)*

### Step 5: Start the Platform Services
Open **three separate terminal tabs** to run the three engines in parallel:

*   **Terminal 1: Laravel Web Server**
    ```bash
    php artisan serve
    ```
*   **Terminal 2: WebSocket Server (For Collaborative Coding & Cursors)**
    ```bash
    php artisan reverb:start --debug
    ```
*   **Terminal 3: Horizon Queue Worker (For Async AI processing & forensics)**
    ```bash
    php artisan queue:work
    ```

*(Make sure your Docker engine is fully active in the background!)*

---

## 🔑 Demo Login Credentials

Use the following logins to test different perspectives of the platform:

| Role | Email | Password | Access Level |
|------|-------|----------|--------------|
| **Admin** | `admin@VisionLab.ai` | `Admin@12345` | Global extension toggles, System statistics, user suspend/activate controls |
| **Instructor** | `instructor@VisionLab.ai` | `Instructor@12345` | Create courses & classroom assignments, View student keystroke AI Forensics |
| **Student** | `student@VisionLab.ai` | `Student@12345` | Solve coding assignments inside code-server, View Streaks & Badge progress |

---

## 🛑 Essential Troubleshooting & Fixes

### 1. ❌ Empty DB / Login Error: "These credentials do not match our records"
If you get this error on trying to log in using the demo credentials, it means your database was successfully migrated but **never seeded**. 
- **The Quick Fix:** Run the seeder directly in your host terminal:
  ```bash
  php artisan db:seed --class=RolesAndUsersSeeder
  ```
- **Verify MySQL Connection:** If this fails with `Connection refused`, your local MySQL server is stopped. Refer to the **OS Specific Setup Guide** above to start your MySQL database service.

### 2. 🐳 Docker Spawning / Socket Access Permission Denied
If workspaces fail to load, check `storage/logs/laravel.log`. If you see errors related to `Permission Denied` when executing Docker commands:
- **Solution:** Laravel is running under your local user account, but your user doesn't have privileges to read/write to `/var/run/docker.sock`.
- **The Linux Command:**
  ```bash
  sudo chmod 666 /var/run/docker.sock
  ```
  *(Note: You will need to rerun this command if you restart your Linux host, or configure the docker user group permanently as detailed in the OS Specific section).*

### 3. 🧠 Frontend Fails to Build / Memory Limit Out of Memory
If running `npm run build` crashes during asset bundling with `FATAL ERROR: Reached heap limit Allocation failed - JavaScript heap out of memory`:
- **Solution:** Vite is bundling the React and extension source files, which requires more memory than the default node limit allows.
- **The Fix:**
  ```bash
  NODE_OPTIONS=--max-old-space-size=4096 npm run build
  ```

---

*Built by the VisionForge Team for Aptech Vision 2026. Excellence is not an act, but a habit.*

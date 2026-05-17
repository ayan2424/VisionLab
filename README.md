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

## 📋 General Prerequisites & Platform Matrix

To run the complete multiplayer IDE environment, the host machine must run a robust stack of development tools.

| Tool | Version | Purpose |
|------|---------|---------|
| **PHP** | 8.2+ (Recommended 8.3+) | Core backend engine (Laravel 11) |
| **Composer** | 2.x | PHP backend dependency manager |
| **Node.js & NPM** | 20+ | Frontend compiler & asset bundling (Vite) |
| **MySQL** | 8.0+ | Core relational database |
| **Docker** | Latest | **MANDATORY** for spawning isolated student workspaces |
| **Docker Image** | `codercom/code-server:4.19.1` | **MANDATORY** VS Code in browser image |

---

## 🐳 0. Core Docker Workspace Configuration (EVERY OS)

VisionLab dynamically orchestrates VS Code instances using Docker containers. The backend spawns workspaces based on the official, stable **`codercom/code-server:4.19.1`** image.

### **Mandatory Pre-Pull Action:**
To ensure zero delays when a student opens their first workspace (as container initialization will timeout if the host has to download the 1GB image on the fly), **you MUST pre-pull the image on your host machine before starting the platform:**
```bash
# Pull the exact VS Code workspace image to your host
docker pull codercom/code-server:4.19.1
```

---

## 🖥️ OS-Specific Complete Installation Handbook

Choose your target development platform below and follow the complete, copypasta-ready instructions to install the entire stack:

---

### 🐧 1. Linux Setup (Ubuntu / Debian / Mint)

Use the terminal to install native dependencies. Run these command groups sequentially:

#### **Step A: Update System & Install PHP 8.3 & Extensions**
Laravel 11 requires a set of PHP extensions for database, curl, and zip management:
```bash
# Update repository index
sudo apt update && sudo apt upgrade -y

# Add Ondrej PHP repository for PHP 8.3 compatibility
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 CLI and essential extensions
sudo apt install -y php8.3-cli php8.3-common php8.3-mysql php8.3-xml php8.3-curl php8.3-mbstring php8.3-zip php8.3-sqlite3 php8.3-bcmath php8.3-intl
```

#### **Step B: Install Composer**
Composer manages PHP packages:
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Verify installation
composer --version
```

#### **Step C: Install Node.js 20 & NPM**
Installs Node.js via the official NodeSource binaries:
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verify installation
node -v
npm -v
```

#### **Step D: Install & Configure MySQL Database**
Install the database server and start the service:
```bash
sudo apt install -y mysql-server

# Enable and start the system service
sudo systemctl enable mysql
sudo systemctl start mysql

# Run secure installation (optional, set root password here if needed)
# sudo mysql_secure_installation
```

#### **Step E: Install Docker Engine & Fix Socket Permissions (CRITICAL)**
Install Docker and add your user to the docker group so Laravel can communicate with `/var/run/docker.sock` without password prompts:
```bash
# Install Docker
sudo apt install -y docker.io

# Start and enable Docker service
sudo systemctl enable docker
sudo systemctl start docker

# Add your user to the docker group
sudo usermod -aG docker $USER

# Apply group changes instantly
newgrp docker

# Grant direct permission to the socket file (Ensures zero permission denied errors)
sudo chmod 666 /var/run/docker.sock
```

---

### 🍎 2. macOS Setup (Intel & Apple Silicon M1/M2/M3)

Use **Homebrew** (the macOS package manager) for a clean, stable development setup.

#### **Step A: Install Homebrew (If not already installed)**
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

#### **Step B: Install PHP 8.3, Composer, Node.js & MySQL**
Install the entire development stack with Homebrew in one go:
```bash
# Install PHP 8.3
brew install php@8.3

# Link PHP 8.3 to your system path
brew link php@8.3 --force --overwrite

# Install Composer
brew install composer

# Install Node.js (version 20+)
brew install node

# Install MySQL Server
brew install mysql
```

#### **Step C: Start Services**
Start your background database server:
```bash
# Start MySQL background service
brew services start mysql
```

#### **Step D: Install Docker Desktop & Share Socket (CRITICAL)**
1. **Install Docker:**
   Download the installer dmg directly from [Docker Desktop for Mac](https://www.docker.com/products/docker-desktop/) (Choose Intel or Apple Silicon chip version) OR install via brew cask:
   ```bash
   brew install --cask docker
   ```
2. **Authorize Socket Sharing (Crucial macOS step):**
   - Open the **Docker Desktop** application from your Applications folder.
   - Click the ⚙️ **Settings** (Gear Icon) in the top-right toolbar.
   - Go to **Advanced** (or *System/Resources* depending on version).
   - Check the box **"Allow the default Docker socket to be used"** (this symlinks `/var/run/docker.sock` into the Mac user space).
   - Click **Apply & Restart**.

---

### 🪟 3. Windows Setup (WSL2 / Linux distro)

For running VisionLab on Windows, **WSL2 (Windows Subsystem for Linux)** is **strictly mandatory** for reliable Docker mount paths and permissions. Attempting native Windows development for Docker containerization leads to file mount path collisions.

#### **Step A: Install WSL2 and Ubuntu**
1. Open PowerShell or Command Prompt as **Administrator** and run:
   ```cmd
   wsl --install -d Ubuntu
   ```
2. Restart your computer when prompted.
3. Once restarted, a console window will pop up to initialize your Ubuntu environment. Set your username and password.

#### **Step B: Install Docker Desktop on Windows**
1. Download and run the installer: [Docker Desktop for Windows](https://www.docker.com/products/docker-desktop/).
2. **Ensure "Use the WSL 2 based engine"** is checked during installation.
3. Open Docker Desktop, go to **Settings (Gear Icon) -> Resources -> WSL Integration**.
4. Check the box next to **"Enable integration with my default WSL distro (Ubuntu)"**.
5. Click **Apply & Restart**.

#### **Step C: Set Up Stack inside WSL2 Ubuntu Terminal**
Open your **WSL Ubuntu terminal** and install the development stack exactly as you would on a native Linux machine:
```bash
# Update Ubuntu package index
sudo apt update && sudo apt upgrade -y

# Add PHP 8.3 Repo
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 & Extensions
sudo apt install -y php8.3-cli php8.3-common php8.3-mysql php8.3-xml php8.3-curl php8.3-mbstring php8.3-zip php8.3-sqlite3 php8.3-bcmath php8.3-intl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20 & NPM
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install & Start MySQL inside WSL
sudo apt install -y mysql-server
sudo service mysql start
```

---

## 🚀 Step-by-Step Project Initialization

Once your operating system stack is completely installed and Docker is running, set up VisionLab:

### 1. Clone & Fetch dependencies
```bash
git clone <your-repo-url> visionlab
cd visionlab

# Install Laravel libraries
composer install

# Install Frontend compilers
npm install
```

### 2. Configure Environment `.env`
```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and fill out your local database username/password and API keys:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=visionlab
DB_USERNAME=root       # Your MySQL username (e.g. 'root')
DB_PASSWORD=           # Your MySQL password

# LLM Providers Configuration
GEMINI_API_KEY=your-gemini-api-key-here
ANTHROPIC_API_KEY=your-anthropic-api-key-here
AI_PROVIDER=gemini # Toggle 'gemini' or 'anthropic'

# Real-Time WebSocket (Laravel Reverb)
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

### 3. Setup Tables & Seed Demo Accounts
**CRITICAL:** To prevent login screens from rejecting your login with `"These credentials do not match our records"`, you MUST populate the database with our preset roles and users:
```bash
php artisan migrate:fresh --seed
```

### 4. Compile Frontend Assets (High-Memory Build)
Because the project compiles heavy customized extensions and advanced React assets inside Vite, increase the default Node memory limit:
```bash
# On Linux / macOS / WSL2
export NODE_OPTIONS=--max-old-space-size=4096 && npm run build

# On Native Windows Powershell (if not using WSL2)
$env:NODE_OPTIONS="--max-old-space-size=4096" ; npm run build
```

### 5. Running the Platforms
Open **three separate terminal screens** to run the services in parallel:

*   **Terminal 1: Laravel Web Server**
    ```bash
    php artisan serve
    ```
*   **Terminal 2: Reverb WebSockets (Real-Time Multiplayer Collab & Cursors)**
    ```bash
    php artisan reverb:start --debug
    ```
*   **Terminal 3: Horizon Queue Worker (Background AI agent tools processing)**
    ```bash
    php artisan queue:work
    ```

---

## 🔑 Demo Access Passwords

| Role | Email | Password | Access Level |
|------|-------|----------|--------------|
| **Admin** | `admin@VisionLab.ai` | `Admin@12345` | Global extension toggles, System statistics, user suspend/activate controls |
| **Instructor** | `instructor@VisionLab.ai` | `Instructor@12345` | Create courses & classroom assignments, View student keystroke AI Forensics |
| **Student** | `student@VisionLab.ai` | `Student@12345` | Solve coding assignments inside code-server, View Streaks & Badge progress |

---

## 🛑 Critical Troubleshooting & Bulletproof Solutions

### **A. "These credentials do not match our records" Login Error**
- **Cause:** Your `users` table is empty.
- **Solution:** Run the seeder directly:
  ```bash
  php artisan db:seed --class=RolesAndUsersSeeder
  ```
  *(If it fails with `Connection Refused`, verify your local MySQL server is actually running via the OS-Specific commands above).*

### **B. Workspace fails to load / Docker Permission Denied**
- **Cause:** Laravel web server is running under your local account but doesn't have system privileges to execute commands on `/var/run/docker.sock`.
- **Solution:** Grant read-write permissions to the Docker socket:
  ```bash
  sudo chmod 666 /var/run/docker.sock
  ```
  *(You must rerun this if you restart your Linux host, or set up the permanent docker user group configuration detailed in Linux step).*

### **C. Docker Image Pull issues**
- **Cause:** Starting a workspace IDE triggers a dynamic container spawn. If `codercom/code-server:4.19.1` isn't pulled, it downloads in the background, timing out your Laravel HTTP request.
- **Solution:** Always run `docker pull codercom/code-server:4.19.1` before starting the server.

---

*Built by the VisionForge Team for Aptech Vision 2026. Excellence is not an act, but a habit.*

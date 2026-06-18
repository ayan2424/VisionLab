#!/bin/bash
set -e

echo "=========================================================="
echo "    VISIONLAB NATIVE IDE (CODE-SERVER) BUILD SCRIPT       "
echo "=========================================================="
echo "This script will compile a sovereign native version of"
echo "code-server with VisionLab branding and strict dark mode."
echo "It requires at least 8GB of RAM and Ubuntu 22.04+."
echo "=========================================================="

sleep 2

# 1. System Dependencies
echo "[1/6] Installing build dependencies..."
sudo apt-get update
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y curl git python3 build-essential pkg-config quilt rsync jq libx11-dev libxkbfile-dev libsecret-1-dev

# Node.js 18 LTS
if ! command -v node >/dev/null 2>&1; then
    echo "Installing Node.js 18..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs
fi

# Yarn
if ! command -v yarn >/dev/null 2>&1; then
    echo "Installing Yarn..."
    sudo npm install -g yarn
fi

# 2. Clone Repository
echo "[2/6] Cloning code-server repository (v4.90.3)..."
if [ ! -d "code-server" ]; then
    git clone https://github.com/coder/code-server.git
fi
cd code-server
git checkout v4.90.3

# Initialize Submodules (VS Code)
echo "Initializing VS Code submodules..."
git submodule update --init

# Apply code-server patches to the VS Code source
echo "Applying code-server patches via quilt..."
quilt push -a || echo "Patches already applied or minor conflicts."

# 3. VisionLab Branding Injection
echo "[3/6] Injecting VisionLab Branding..."

# Update product.json
if [ -f "product.json" ]; then
    jq '.nameShort="VisionLab" | .nameLong="VisionLab IDE" | .applicationName="visionlab-ide" | .dataFolderName=".visionlab"' product.json > product.tmp && mv product.tmp product.json
fi

# Update package.json
if [ -f "package.json" ]; then
    jq '.name="visionlab-ide" | .displayName="VisionLab IDE" | .description="VisionLab Collaborative Code Editor"' package.json > package.tmp && mv package.tmp package.json
fi

# Aesthetic Injection: Force #0a0a0a Dark Mode by replacing the default dark theme colors in the VS Code source
# The VS Code source is in lib/vscode/
echo "Enforcing Strict Dark Mode (#0a0a0a)..."
VSCODE_THEME_FILE="lib/vscode/extensions/theme-defaults/themes/dark_plus.json"
if [ -f "$VSCODE_THEME_FILE" ]; then
    # Modify the base background color
    sed -i 's/"editor.background": ".*"/"editor.background": "#0a0a0a"/' "$VSCODE_THEME_FILE"
    sed -i 's/"sideBar.background": ".*"/"sideBar.background": "#0a0a0a"/' "$VSCODE_THEME_FILE"
    sed -i 's/"activityBar.background": ".*"/"activityBar.background": "#0a0a0a"/' "$VSCODE_THEME_FILE"
    sed -i 's/"terminal.background": ".*"/"terminal.background": "#0a0a0a"/' "$VSCODE_THEME_FILE"
fi

# 4. Install Dependencies
echo "[4/6] Installing dependencies (this will take a while)..."
yarn

# 5. Build Process
echo "[5/6] Building the application (this can take 15-30 minutes)..."
yarn build

# 6. Generate Release Artifact
echo "[6/6] Generating Release Artifact..."
yarn release

echo "=========================================================="
echo "                      BUILD COMPLETE                      "
echo "=========================================================="
echo "Your VisionLab IDE artifact has been generated in the"
echo "code-server/release-packages/ directory."
echo ""
echo "Please transfer the generated .tar.gz file back to the"
echo "Docker build environment to be included in our custom"
echo "visionlab/workspace image."
echo "=========================================================="

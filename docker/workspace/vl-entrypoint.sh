#!/bin/bash
# ─────────────────────────────────────────────────────────────────────────────
# VisionLab Workspace Entrypoint - Project IDX Compatibility Parser
# ─────────────────────────────────────────────────────────────────────────────

# Source Nix profile
if [ -f "$HOME/.nix-profile/etc/profile.d/nix.sh" ]; then
    . "$HOME/.nix-profile/etc/profile.d/nix.sh"
fi

PROJECT_DIR="/home/coder/project"
TEMPLATE_DIR="/home/coder/templates"
DEV_NIX="$PROJECT_DIR/dev.nix"

# 1. Provide default dev.nix if none exists
if [ -d "$PROJECT_DIR" ] && [ ! -f "$DEV_NIX" ]; then
    echo "[VisionLab] Providing default Project IDX template..."
    cp "$TEMPLATE_DIR/dev.nix" "$DEV_NIX"
fi

if [ -f "$DEV_NIX" ]; then
    echo "[VisionLab] Parsing dev.nix..."

    # 2. Extract and Install Packages
    cat << 'EOF' > /tmp/installer.nix
let
  pkgs = import <nixpkgs> {};
  dev = import /home/coder/project/dev.nix { inherit pkgs; };
in
  pkgs.buildEnv {
    name = "visionlab-env";
    paths = dev.packages or [];
  }
EOF
    echo "[VisionLab] Installing Nix packages..."
    nix-env -i -f /tmp/installer.nix

    # 3. Extract and Install Extensions
    echo "[VisionLab] Installing VS Code extensions..."
    EXTENSIONS=$(nix-instantiate --eval --strict --json -E 'let pkgs = import <nixpkgs> {}; dev = import /home/coder/project/dev.nix { inherit pkgs; }; in dev.idx.extensions or []' 2>/dev/null | jq -r '.[]')
    
    for EXT in $EXTENSIONS; do
        echo "[VisionLab] Installing extension: $EXT"
        code-server --install-extension "$EXT" --force || true
    done
fi

# 4. Clean up Nix test container (not needed, but good practice)
rm -f /tmp/installer.nix

# 5. Execute Code-Server
exec /usr/bin/entrypoint.sh "$@"

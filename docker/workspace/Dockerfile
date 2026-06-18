FROM codercom/code-server:4.123.0

USER root

# ─── System Dependencies ──────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    curl xz-utils git direnv jq \
    && rm -rf /var/lib/apt/lists/*

# ─── Nix Package Manager (Single-user) ───────────────────────────────────────
RUN mkdir -m 0755 /nix && chown coder /nix

USER coder

# Install Nix
RUN curl -L https://nixos.org/nix/install | sh -s -- --no-daemon

# Source Nix in all shell sessions (.bashrc for interactive, .profile for login)
RUN echo '. /home/coder/.nix-profile/etc/profile.d/nix.sh' >> /home/coder/.bashrc && \
    echo '. /home/coder/.nix-profile/etc/profile.d/nix.sh' >> /home/coder/.profile

# Setup direnv + nix-direnv for declarative workspace environments (like Firebase Studio)
RUN echo 'eval "$(direnv hook bash)"' >> /home/coder/.bashrc && \
    /home/coder/.nix-profile/bin/nix-env -iA nixpkgs.nix-direnv 2>/dev/null || true && \
    echo 'source $HOME/.nix-profile/share/nix-direnv/direnvrc' >> /home/coder/.direnvrc || \
    git clone https://github.com/nix-community/nix-direnv.git /home/coder/.nix-direnv && \
    echo 'source /home/coder/.nix-direnv/direnvrc' >> /home/coder/.direnvrc

# ─── VisionLab Extension Installation ────────────────────────────────────────
USER root
COPY storage/extensions/continue-source/extensions/vscode/build/visionlab-agent-1.3.39.vsix /tmp/visionlab-agent.vsix

USER coder
RUN code-server --install-extension /tmp/visionlab-agent.vsix --force

# ─── Critical Fix: Patch hardcoded extension ID in installed extension.js ─────
# The compiled bundle still references "Continue.continue" — we patch it to
# use our correct publisher ID "visionlab.visionlab-agent" so the WebView loads.
RUN EXTDIR=$(find /home/coder/.local/share/code-server/extensions -name "extension.js" -path "*/visionlab*" 2>/dev/null | head -1) && \
    if [ -n "$EXTDIR" ]; then \
        sed -i 's/"Continue\.continue"/"visionlab.visionlab-agent"/g' "$EXTDIR" && \
        sed -i 's/"continue\.continue"/"visionlab.visionlab-agent"/g' "$EXTDIR" && \
        sed -i 's/dev\.continue\.continue/dev.visionlab.visionlab-agent/g' "$EXTDIR" && \
        echo "[VisionLab] Extension ID patched successfully in: $EXTDIR"; \
    else \
        echo "[VisionLab] WARNING: extension.js not found!"; \
    fi

# ─── VS Code / code-server Settings (Locked Down) ────────────────────────────
COPY --chown=coder:coder docker/workspace/settings.json /home/coder/.local/share/code-server/Machine/settings.json

# ─── Nix PATH for code-server process ────────────────────────────────────────
# Ensure the code-server process itself can find nix binaries
ENV PATH="/home/coder/.nix-profile/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

# ─── Default dev.nix template for new workspaces ─────────────────────────────
RUN mkdir -p /home/coder/templates
COPY --chown=coder:coder docker/workspace/dev.nix.template /home/coder/templates/dev.nix

# ─── Entrypoint ──────────────────────────────────────────────────────────────
USER root
COPY docker/workspace/vl-entrypoint.sh /usr/bin/vl-entrypoint.sh
RUN chmod +x /usr/bin/vl-entrypoint.sh

USER coder
ENTRYPOINT ["/usr/bin/vl-entrypoint.sh"]
EXPOSE 8080

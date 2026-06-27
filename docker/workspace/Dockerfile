FROM buildpack-deps:jammy

USER root

# Create the coder user
RUN useradd -m -s /bin/bash coder && \
    mkdir -m 0755 /nix && chown coder /nix

USER coder

# Install Nix (no apt-get required because curl and xz are present in base image)
RUN curl -L https://nixos.org/nix/install | sh -s -- --no-daemon

# Source Nix in shell profiles
RUN echo '. /home/coder/.nix-profile/etc/profile.d/nix.sh' >> /home/coder/.bashrc && \
    echo '. /home/coder/.nix-profile/etc/profile.d/nix.sh' >> /home/coder/.profile

ENV PATH="/home/coder/.nix-profile/bin:/nix/var/nix/profiles/default/bin:${PATH}"
SHELL ["/bin/bash", "-c"]

# Install dev.nix packages
COPY --chown=coder:coder dev.nix /home/coder/dev.nix
RUN source /home/coder/.nix-profile/etc/profile.d/nix.sh && \
    nix-env -f /home/coder/dev.nix -i

USER root

# Extract VisionLab IDE
COPY visionlab-ide-1.0.0-linux-amd64.tar.gz /tmp/visionlab-ide.tar.gz
RUN mkdir -p /usr/lib/visionlab-ide && \
    tar -xzf /tmp/visionlab-ide.tar.gz -C /usr/lib/visionlab-ide --strip-components=1 && \
    rm /tmp/visionlab-ide.tar.gz && \
    ln -s /usr/lib/visionlab-ide/bin/code-server /usr/bin/visionlab-server && \
    chown -R coder:coder /usr/lib/visionlab-ide

USER coder

# Ensure local data directory exists
RUN mkdir -p /home/coder/.local/share/visionlab-server/Machine /home/coder/.visionlab-server/Machine

# Copy settings
COPY --chown=coder:coder settings.json /home/coder/.local/share/visionlab-server/Machine/settings.json
COPY --chown=coder:coder settings.json /home/coder/.visionlab-server/Machine/settings.json

ENTRYPOINT ["/usr/bin/visionlab-server"]

# Workspace Specific Rules

- Git Auto-Commit & Push: Whenever you make any changes to the codebase, you must always automatically commit those changes with a detailed, descriptive commit message and push them to the repository yourself before concluding the task. Do not wait for the user to commit and push unless explicitly told otherwise.

- Nginx Code-Server Proxy Fix (Crucial Memory): If code-server returns 404 for static files or fails WebSockets (1006 error) when hosted behind Nginx, ALWAYS ensure the Nginx config has priority prefix matching (`^~ /stable-`, `^~ /webview/`, etc.) with `proxy_set_header Upgrade $http_upgrade;` and `Connection "Upgrade";`. The referer regex must extract the port (`$cport`). NEVER use standard regex locations (`~`) for these paths as aaPanel's global CSS/JS blocks will override them. This has been permanently resolved for `visionlab.ayan24.me` and must not be reverted or forgotten.

- Docker Image 403 Forbidden Fix (Crucial Memory): When natively compiling and building the custom `visionlab-ide` Docker image, ALWAYS ensure that the compiled output folder (e.g., `/usr/lib/code-server`) is fully owned by the non-root `coder` user (`chown -R 1000:1000`). If this is forgotten, the internal `code-server` web server will throw 403 Forbidden errors when trying to serve static assets or extensions because the `coder` user won't have read permissions on root-owned files inside the container.

# Critical Rule for VisionLab Workspace

**NEVER** edit the live Laravel website files directly via the remote Z: drive (RaiDrive SSHFS mount). Editing live files via RaiDrive causes 'couldn't upload' errors, silent sync failures, and permission crashes.

- **Website Edits:** Always edit the Laravel application files on the LOCAL Windows codebase (C:\Users\ayans\OneDrive\Documents\A_Projects\Aptech\Vision2026\VisionLab) and use Git commit/push to deploy changes.
- **IDE Edits (Z: Drive):** The Z: drive is strictly and ONLY for editing the isionlab-ide folder, building the IDE natively, and modifying the code-server extension natively. Never touch the public Laravel app through Z:.

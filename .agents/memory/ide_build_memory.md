# IDE Build Memory & Lessons Learned

This file contains critical historical context regarding the VisionLab IDE (code-server) build process. DO NOT repeat the same mistakes.

## 1. Minification Deadlock (OOM / Hangs)
**The Problem:** When running `yarn build:vscode`, the build would hang indefinitely at `Starting compilation...`.
**The Cause:** The instance lacked enough memory (RAM) to handle `vscode-reh-web-linux-x64-min`. The Linux OOM killer would silently terminate worker threads during minification (Terser), causing `gulp` to deadlock while waiting for workers to return.
**Permanent Fix:** We must bypass minification by setting `export MINIFY=false` before running the build. This speeds up the build exponentially and completely prevents the deadlock.

## 2. File Explorer UI Crash (`TypeError: Cannot read properties of undefined (reading 'extensionId')`)
**The Problem:** The IDE would load, but the Activity Bar would be missing the File Explorer icon, and the console would spam `Cannot read properties of undefined (reading 'extensionId')` at `workbench.web.main.js` (`paneCompositeBar.ts`).
**The Cause:** Previous agents blindly modified `lib/vscode/src/vs/workbench/contrib/files/browser/explorerViewlet.ts` (uncommenting `VIEW_CONTAINER` registration). This caused duplicate or malformed ViewContainer registrations natively, making `viewContainer` undefined when VS Code tried to read its `extensionId`.
**Permanent Fix:** We scrapped the corrupted codebase and cloned a 100% fresh repo directly from GitHub. **Rule:** Do NOT manually uncomment or overwrite core VS Code ViewContainer registrations (like `explorerViewlet.ts`) without understanding how `viewsRegistry` manages them in newer VS Code versions (e.g., v1.93+).

## 3. Security & Embedding Policies (CSP/Iframes)
**The Problem:** The new code-server version has strict Content Security Policies (CSP) and frame-ancestors rules that block the IDE from being embedded in iframes or accessed seamlessly across different origins.
**Permanent Fix:** We must ensure that during the native modification, we patch the CSP headers (e.g., `frame-ancestors`) in the backend HTTP server code (`http.ts` or related files) so that VisionLab can securely embed or proxy the IDE without browser blocks.

## 4. Server Compilation Resource Management (CPU/RAM)
**The Problem:** Unrestricted `yarn` or `gulp` builds can aggressively consume all available CPU and RAM on the host server, causing the server to freeze or the OOM killer to terminate critical processes.
**Permanent Fix:** 
- Allocate specific RAM constraints using Node arguments (e.g., `NODE_OPTIONS="--max-old-space-size=4096"`).
- Use `nice` or limit concurrent workers in gulp/yarn if necessary, ensuring the server remains stable and responsive during the heavy build process.

## 5. SSH Disconnects & Background Processes (`nohup`)
**The Problem:** Because the compilation process takes 30-45 minutes (or more), any dropped SSH connection or terminal timeout would kill the active build process, forcing us to restart from zero.
**Permanent Fix:** Every heavy script (like `yarn build:vscode` or docker builds) MUST be run using `nohup` (e.g., `nohup ./run_build.sh > build.log 2>&1 &`). This ensures compilation runs independently of the SSH session.

## 6. Branding & Customization (`product.json`)
**The Problem:** Default `code-server` references OpenVSX, uses default VS Code branding, and doesn't load our custom extensions natively.
**Permanent Fix:** We must carefully modify `product.json` (and related configuration files) during the build to rename the application to "VisionLab IDE", enforce the default dark theme, and inject any pre-packaged VSIX extensions natively so they are available out-of-the-box.

## 7. Authentication & Nginx Proxying
**The Problem:** `code-server` has its own password authentication system which clashes with VisionLab's Laravel-based authentication when embedded via iframes or proxy.
**Permanent Fix:** 
- The IDE must be launched with `--auth none` flag.
- Access control and authentication must be handled solely by Nginx and the main VisionLab backend before traffic reaches the Docker container.
- Nginx configuration must perfectly handle WebSocket proxying for the terminal and language servers to work correctly over HTTPS.

## 8. GitHub Copilot & Native Feature Hiding
**The Problem:** Newer versions of VS Code integrate GitHub Copilot natively into the UI, which clashes with our custom "VisionLab AI" / "Continue Source" integration.
**Permanent Fix:** We must ensure that GitHub Copilot (and any related Microsoft AI integrations) is disabled, hidden, or completely stripped from the built-in extensions and UI during compilation, so only VisionLab's custom AI tools are visible.

## 9. General Workflow Rule
Always rely on clean git commits to track changes instead of blindly patching files. If something goes wrong, revert using git instead of trying to patch the patch.

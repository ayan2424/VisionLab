# Native Code-Server Rebranding & Nix Integration Plan

This plan outlines the deep source-level modifications required for VisionLab IDE v1.0, ensuring absolute native governance, custom dark orange aesthetics, and restrictive, template-driven Nix environments.

## User Review Required
> [!IMPORTANT]
> Compiling `code-server` natively from source takes approximately 20-30 minutes and requires significant server resources (CPU/RAM). Please review the plan below and confirm if you want me to initiate the source fork and build process now.

## Proposed Architecture

1. **Source Code Forking & Branding Injection (Phase 4 Directive)**
    - Clone the official `coder/code-server` repository directly onto the server/local workspace.
    - Traverse the source (specifically `lib/vscode`) to replace all instances of "Visual Studio Code" and "Code-Server" with **VisionLab IDE**.
    - Overwrite default SVGs, favicons, and manifest files with VisionLab native assets.
    - **Security & Origin Policy Relaxation:** Modify the hardcoded Content Security Policy (CSP) and Origin policies in the latest VS Code source. This will natively allow seamless `<iframe>` embedding within VisionLab's dashboard and fix strict ServiceWorker / CORS blocking without relying on fragile reverse-proxy hacks.

2. **Native Dark Orange Theme Injection**
    - Modify the default VS Code theme declarations natively within the `src/vs/workbench/browser/parts/` directories.
    - Inject the VisionLab `#f97316` (Dark Orange) aesthetic into the base UI tokens so that it becomes the immutable default theme.

3. **Welcome Page & Content Revamp**
    - Overwrite `welcomePage.ts` and associated markdown resources to show VisionLab-specific onboarding content.
    - Remove native VS Code telemetry and upstream sync popups.

4. **Nix Integration & Workspace Templates (Phase 3 Directive)**
    - **Workspace Templates Definition:** As per `FRD-CLS-006` and `feature_matrix.md`, Workspace Templates are pre-configured environments (e.g., Python, Laravel, Node.js) that instructors select when creating assignments. 
    - **Nix-Powered Environments:** Instead of building dozens of heavy Docker images, we will use Nix (`dev.nix`) templates. When a student spawns an IDE for an assignment, the system will inject the instructor's chosen Nix template (e.g., `laravel.nix`), which natively resolves the exact packages (PHP, Composer) required.
    - **Sandbox Enforcement:** The IDE terminal will strictly drop users into this `nix-shell` environment. `apt` and `apt-get` commands will be explicitly forbidden (aliased/blocked) to ensure students cannot break out of their assigned templates or install unauthorized tools.

5. **Deep Backend Controls (Admin & Teacher Panel)**
    - Inject an internal REST/WebSocket bridge natively into the IDE's Extension Host.
    - This will allow the Laravel Reverb backend to send signals directly to the IDE (e.g., forcefully installing a required class extension, blocking a process, or broadcasting teacher announcements directly inside the IDE shell).

6. **Compilation & Docker Image Generation**
    - Run the `yarn build` and `yarn build:vscode` pipelines.
    - Package the compiled artifacts into a new, immutable Docker image (`visionlab-ide-1.0.0`) which will be used by all spawned workspaces.

## Verification Plan

### Automated Tests
- Build verification tests: `yarn test:unit` inside the compiled source.
- Container security check: Verify `apt` is blocked and `nix` is the sole package manager.

### Manual Verification
- Deploy a test workspace using the new image.
- Verify the "VisionLab IDE" text appears in the "About" dialog.
- Verify the Dark Orange theme is active by default.
- Attempt to install a package via `apt` (should fail) and then via `dev.nix` (should succeed).

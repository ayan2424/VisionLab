# Phase 3B Enhancements: Workspace Governance, Isolated Storage & Exam Lockdown

This document outlines the architectural enhancements to the VisionLab LMS platform related to workspace control, storage isolation, and anti-cheating mechanisms.

## 1. Goal Description
1. **Marketplace Control**: Provide teachers and global admins with granular control over whether students can install external VS Code marketplace extensions.
2. **Native Extensions Governance**: Lock down native Admin-provided extensions (like VisionLab Agent) so they can only be toggled (enabled/disabled) but never uninstalled by students.
3. **Isolated Storage Architecture**: Refactor the backend file system architecture to use **Isolated User Storage** (e.g., `storage/users/user_{id}/workspaces/`), ensuring each user has their own dedicated drive where all their data lives.
4. **Universal Exam Lockdown Mode**: Implement an **Absolute Lockdown (Kiosk) Mode** for exams (Workspaces, MCQs, etc.). This enforces maximum browser security limits (Fullscreen, keyboard trapping, tab-switch prevention) to make cheating virtually impossible until the exam is submitted.

## 2. Proposed Architectural Changes

### 2.1 Database & Models
- **Course Level Control**: Utilize the existing `allow_marketplace` boolean on the `courses` table. Add to `Course` model's `$fillable`.
- **Global Control**: Utilize the `system_configs` table to store a `global_allow_marketplace` key.
- **Assignment Exam Mode**: Utilize the existing `mode` column (`'learning'` vs `'exam'`) in the `assignments` table.

### 2.2 Core Infrastructure (`CodeServerManager.php`)
- **Isolated User Storage Hierarchy**:
  - Update `workspacePath()` to route storage directly into the user's isolated drive: `storage_path('users/user_' . $workspace->student_id . '/workspaces/ws-' . $workspace->id);`
  - This natively creates a multi-tenant file structure, ensuring strict data boundaries and quota feasibility per user.
- **Marketplace Lockdown Engine**:
  - In `start()`, check both the global `SystemConfig` and `$workspace->course->allow_marketplace`.
  - If either restricts it, dynamically inject the environment variable `-e EXTENSIONS_GALLERY='{"serviceUrl":""}'` into the container. This natively breaks the marketplace connection.
- **Unyielding Native Extensions (Built-In)**:
  - In `installExtension()`, instead of using `visionlab-ide --install-extension`, the system will use a shell command to seamlessly unpack the `.vsix` archive directly into `/usr/lib/code-server/lib/vscode/extensions/` inside the container.
  - Code-Server inherently treats anything in this directory as a **Built-In System Extension**, which means the VS Code UI will permanently hide the "Uninstall" button.

### 2.3 Universal Exam Lockdown Engine (Frontend)
- **Component (`<x-exam-lockdown>`)**: Create a universal Blade component that wraps *any* exam view.
- **Browser Security Features**:
  - **Fullscreen API Enforcement**: The exam cannot start until the user clicks to enter native browser fullscreen.
  - **Visibility & Focus Trapping**: Listens to `visibilitychange` (tab switching) and `blur` (clicking outside the window). If triggered, the exam content is instantly hidden/blurred, and a severe blocking overlay demands they return.
  - **Keyboard Trapping**: Intercepts `keydown` events to `preventDefault()` on common escape sequences (`Alt`, `F11`, `Windows/Meta` key, `Ctrl+T`) within the bounds of standard browser sandboxing.
  - **Exit Prevention**: Implements `window.onbeforeunload` to trigger the browser's native "Leave Site?" warning if they attempt to close the tab or press the Back button.

## 3. Verification Plan
- **Storage Isolation**: Verify new workspaces are created physically under `storage/users/user_{id}/workspaces/` securely.
- **Marketplace Block**: Test global and course-level marketplace disabling; verify the Extension panel fails to load public extensions.
- **Built-In Native Extension**: Verify VisionLab Agent cannot be uninstalled.
- **Lockdown Trapping**: Start an assignment in `exam` mode. Attempt to press `Alt+Tab` or switch to another window; verify the cheating violation overlay triggers instantly. Attempt to refresh the page; verify the browser's native leave warning appears.

# VisionLab Phase 4.5: Storage, Governance, and Lockdown Architecture

This plan addresses the critical workspace permissions, storage segregation, extension governance, and examination lockdown mode requested by the user.

## Proposed Changes

### 1. Storage Segregation & Permissions Fix
**Issue**: Files created in PHP are owned by `www-data`, causing permission denied errors in Code-Server (User 1000), and vice versa.
**Solution**:
- Update `CodeServerManager::workspacePath()` to segregate storage: `storage/app/workspaces/{role}/{user_id}/{workspace_id}`.
- Update `writeFile`, `createFile`, `deleteFile`, `renameFile` in `CodeServerManager` to forcefully correct permissions (`chmod 0777` or execute the creation inside the container context as `coder` via `docker exec -u 1000`).
- Best approach: Always run a `chown -R 1000:1000` on the workspace directory when booting up the container in `CodeServerManager::startWorkspace()`.

### 2. Built-in Extension Fix (Uninstallation Prevented)
**Issue**: Extensions disappeared because the script attempts to extract them to `/usr/lib/code-server/lib/vscode/extensions/` as user `1000` (which lacks write access to `/usr/lib`), causing silent failures.
**Solution**:
- Update `CodeServerManager::installExtension()` to run the VSIX extraction command with `docker exec -u root` instead of `-u 1000`. This will successfully place them in the system extensions folder, inherently making them "built-in" so students cannot uninstall them (only disable/enable).

### 3. Extension Marketplace Control
**Issue**: Teachers and Admins need granular control over the Extensions Marketplace per course and assignment.
**Solution**:
- Add a `marketplace_enabled` boolean column to the `courses` and `assignments` tables.
- When creating a workspace, inherit the `marketplace_enabled` flag.
- Pass `EXTENSIONS_GALLERY={"serviceUrl":""}` to the Code-Server Docker container if the flag is false.

### 4. Exam Lockdown Mode (Full Screen Enforced)
**Issue**: Students must be locked into full-screen mode during assignments/exams to prevent cheating (tab switching).
**Solution**:
- Add `is_exam_mode` boolean to `assignments`.
- Create a global `exam-lockdown.js` frontend script.
- When a student opens a workspace or exam with `is_exam_mode = true`:
  1. The page forces `element.requestFullscreen()`.
  2. If the user exits full screen (`fullscreenchange` event) or loses focus (`window.onblur`), a blinding overlay covers the screen with a "Return to Fullscreen" button and logs an `ExamViolationEvent` to the database (which teachers can view).

## Verification Plan
### Automated & Manual Verification
- Manually create a file via the IDE (web interface) and attempt to delete it from the VisionLab Admin Dashboard to confirm permissions work both ways.
- Login as a student and confirm the VisionLab Agent is installed but cannot be uninstalled.
- Start an assignment with Exam Mode enabled and verify the browser aggressively locks down the viewport and flags blur events.

## User Review Required
> [!IMPORTANT]
> The Exam Lockdown mode relies on Browser APIs (`requestFullscreen`, `visibilitychange`). Modern browsers will require the user to explicitly click "Start Exam" to trigger fullscreen. Is it acceptable to have a "Start Exam" gateway screen that forces the click?

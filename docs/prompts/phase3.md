<MASTER_PROMPT Phase="3" Name="Workspace_Infrastructure_And_CodeServer_IDE" Version="6.0">
    <SYSTEM_ROLE>
      You are a senior Laravel, Docker, and secure development-environment engineer. Your responsibility is to deliver a real browser-based IDE experience backed by isolated code-server workspaces.
    </SYSTEM_ROLE>
    <PRODUCT_CONTEXT>
      VisionLab workspaces are where students complete assignments, collaborate, and use AI tools. The workspace layer must be secure, observable, recoverable, and integrated with the classroom domain.
    </PRODUCT_CONTEXT>
    <PHASE_GOAL>
      Implement workspace lifecycle management, Docker/code-server orchestration, workspace file APIs, IDE layout, storage boundaries, and workspace access control.
    </PHASE_GOAL>
    <DEPENDENCIES>
      Requires Phase 1 roles and users plus Phase 2 courses, assignments, and submissions. Workspaces must connect to assignments and ownership without breaking classroom policies.
    </DEPENDENCIES>
    <IMPLEMENTATION_REQUIREMENTS>
      <REQUIREMENT>Build a workspace service that creates, starts, stops, restarts, and inspects code-server containers with unique names, isolated storage, dynamic routing data, generated access tokens, health status, and persistent metadata.</REQUIREMENT>
      <REQUIREMENT>Ensure workspace directories are created under controlled storage roots, with assignment starter material copied or mounted according to policy.</REQUIREMENT>
      <REQUIREMENT>Implement workspace controllers for show, stop, status, file tree, read file, write file, create file, create folder, rename, delete, and download where appropriate.</REQUIREMENT>
      <REQUIREMENT>Build a production IDE shell with top bar, file explorer, code-server iframe, activity indicators, collaborator area, status bar, terminal panel entry point, responsive behavior, and clear error states when a workspace is unavailable.</REQUIREMENT>
      <REQUIREMENT>Implement the file explorer with recursive loading, refresh, context actions, optimistic updates only where reversible, toasts, keyboard shortcuts, and consistent loading states.</REQUIREMENT>
      <REQUIREMENT>Record file writes, deletions, renames, container lifecycle events, and failed operations in auditable logs.</REQUIREMENT>
      <REQUIREMENT>Provide local development behavior that fails clearly when Docker or code-server is not available and documents the required setup without pretending the IDE is running.</REQUIREMENT>
      <REQUIREMENT>Configure workspace routing so code-server is reached through the application-controlled reverse proxy, with health checks, heartbeat awareness, secure-context support for webviews, and clear operator diagnostics.</REQUIREMENT>
      <REQUIREMENT>Define workspace resource policy inputs for CPU, memory, disk, idle timeout, maximum lifetime, download restrictions, port forwarding restrictions, and cleanup behavior.</REQUIREMENT>
      <REQUIREMENT>Resolve effective workspace quotas in a deterministic order: administrator global default, course override, user override, role default, then platform fallback. Store and display the effective quota applied to each container start.</REQUIREMENT>
      <REQUIREMENT>Monitor active workspace resource usage and warn users before memory, CPU, disk, or timeout limits disrupt their session.</REQUIREMENT>
    </IMPLEMENTATION_REQUIREMENTS>
    <SECURITY_REQUIREMENTS>
      <REQUIREMENT>Use WorkspacePolicy for every workspace route and API endpoint, including collaborator membership and assignment ownership checks.</REQUIREMENT>
      <REQUIREMENT>Validate every relative path with canonical path checks and block traversal, hidden sensitive files, environment files, dependency directories, executable secrets, and writes outside the workspace root.</REQUIREMENT>
      <REQUIREMENT>Protect code-server with workspace-scoped tokens, controlled proxying, secure headers, and no public unauthenticated container access.</REQUIREMENT>
      <REQUIREMENT>Ensure container operations cannot be influenced by unsanitized user input, shell interpolation, or untrusted path values.</REQUIREMENT>
      <REQUIREMENT>Run workspace containers with least practical privilege, bounded resources, restricted network exposure, controlled writable mounts, and no broad host filesystem access.</REQUIREMENT>
      <REQUIREMENT>Disable or restrict code-server file downloads, forwarded ports, and server-side proxying where policy requires it.</REQUIREMENT>
    </SECURITY_REQUIREMENTS>
    <ACCEPTANCE_CRITERIA>
      <CRITERION>Authorized users can open a workspace, load code-server, browse files, open files, and perform permitted file operations.</CRITERION>
      <CRITERION>Unauthorized users cannot access workspace pages, file APIs, container controls, or stored files.</CRITERION>
      <CRITERION>Container lifecycle state is accurately persisted and visible in the UI.</CRITERION>
      <CRITERION>Tests cover workspace authorization, path traversal prevention, file operations, and lifecycle service behavior.</CRITERION>
    </ACCEPTANCE_CRITERIA>
    <EXECUTION_PROTOCOL>
      <STEP>Inspect existing workspace schema, storage disks, routes, policies, and Docker assumptions.</STEP>
      <STEP>Plan container orchestration, file API contracts, UI states, and test strategy.</STEP>
      <STEP>Implement backend services, routes, controllers, policies, views, JavaScript, and logging.</STEP>
      <STEP>Verify with tests, local container checks where available, path security checks, and UI build checks.</STEP>
      <STEP>Report IDE readiness, environment prerequisites, and readiness for Phase 4.</STEP>
    </EXECUTION_PROTOCOL>
  </MASTER_PROMPT>
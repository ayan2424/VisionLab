<MASTER_PROMPT Phase="5" Name="Real_Time_Collaboration_Core" Version="6.0">
    <SYSTEM_ROLE>
      You are a senior Laravel Reverb, WebSocket, and VS Code extension engineer. Your responsibility is to add reliable real-time collaboration to VisionLab workspaces.
    </SYSTEM_ROLE>
    <PRODUCT_CONTEXT>
      Collaboration is a core differentiator. Students and instructors must be able to share presence, cursor locations, document changes, chat, and workspace activity in a controlled environment.
    </PRODUCT_CONTEXT>
    <PHASE_GOAL>
      Add Reverb channels, collaborator presence, cursor sync, document change sync, live chat, activity state, and secure workspace membership checks.
    </PHASE_GOAL>
    <DEPENDENCIES>
      Requires Phase 3 workspace APIs and Phase 4 extension delivery. Reuse workspace membership, collaborator records, and extension authentication tokens.
    </DEPENDENCIES>
    <IMPLEMENTATION_REQUIREMENTS>
      <REQUIREMENT>Define private or presence channels for each workspace with authorization based on workspace collaborator membership and role.</REQUIREMENT>
      <REQUIREMENT>Implement collaboration events for user joined, user left, cursor moved, document changed, chat message sent, typing state, and workspace activity updates.</REQUIREMENT>
      <REQUIREMENT>Build a collaboration service that assigns stable user colors, tracks online users, updates heartbeats, and cleans stale sessions.</REQUIREMENT>
      <REQUIREMENT>Implement the VisionLab collaboration extension in TypeScript with connection management, authentication, reconnection, document sync, cursor decorations, status bar presence, chat panel, and toast notifications.</REQUIREMENT>
      <REQUIREMENT>Use conflict-aware document handling that avoids echo loops, preserves local edits, and clearly reports sync conflicts or unsupported operations.</REQUIREMENT>
      <REQUIREMENT>Persist chat messages and important collaboration activity where product value or auditability requires it.</REQUIREMENT>
      <REQUIREMENT>Update the Blade workspace shell to show active collaborators, connection health, chat entry points, and real-time status changes.</REQUIREMENT>
    </IMPLEMENTATION_REQUIREMENTS>
    <SECURITY_REQUIREMENTS>
      <REQUIREMENT>Authenticate extension API calls with workspace-scoped tokens and rotate or revoke tokens when access changes.</REQUIREMENT>
      <REQUIREMENT>Prevent users from subscribing to channels for workspaces where they are not collaborators.</REQUIREMENT>
      <REQUIREMENT>Validate event payload sizes, file paths, message lengths, and rate limits to prevent abuse.</REQUIREMENT>
      <REQUIREMENT>Sanitize chat content before rendering in Blade or extension webviews.</REQUIREMENT>
    </SECURITY_REQUIREMENTS>
    <ACCEPTANCE_CRITERIA>
      <CRITERION>Multiple authorized users can join a workspace and see accurate online status.</CRITERION>
      <CRITERION>Cursor positions, document updates, and chat messages sync reliably without leaking to unauthorized users.</CRITERION>
      <CRITERION>Connection loss and reconnection are visible and recover without corrupting files.</CRITERION>
      <CRITERION>Tests cover channel authorization, API validation, event broadcasting, and stale presence cleanup.</CRITERION>
    </ACCEPTANCE_CRITERIA>
    <EXECUTION_PROTOCOL>
      <STEP>Inspect Reverb configuration, workspace routes, extension build pipeline, and existing event patterns.</STEP>
      <STEP>Plan channel contracts, event payloads, extension modules, conflict handling, and tests.</STEP>
      <STEP>Implement backend channels, events, services, APIs, extension files, Blade updates, and logs.</STEP>
      <STEP>Verify with automated tests and a two-user manual collaboration check where environment supports it.</STEP>
      <STEP>Report collaboration readiness and readiness for Phase 6.</STEP>
    </EXECUTION_PROTOCOL>
  </MASTER_PROMPT>
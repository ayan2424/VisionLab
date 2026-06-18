<MASTER_PROMPT Phase="7" Name="Video_Conferencing_And_Live_Sessions" Version="6.0">
    <SYSTEM_ROLE>
      You are a senior WebRTC, Jitsi, Laravel, and workspace-experience engineer. Your responsibility is to add secure live sessions that feel native to VisionLab.
    </SYSTEM_ROLE>
    <PRODUCT_CONTEXT>
      Live instruction and pair programming require video sessions that are tied to courses and workspaces, authorized through VisionLab, and visible in the same collaboration context as coding.
    </PRODUCT_CONTEXT>
    <PHASE_GOAL>
      Add Jitsi room lifecycle, secure tokens, session controls, workspace integration, live indicators, and leave/end behavior.
    </PHASE_GOAL>
    <DEPENDENCIES>
      Requires Phase 3 workspaces, Phase 5 real-time presence, and Phase 2 course roles. Video access must follow workspace collaboration and instructor authority.
    </DEPENDENCIES>
    <IMPLEMENTATION_REQUIREMENTS>
      <REQUIREMENT>Implement video room records with workspace, course or assignment context, room identifier, provider mode, start and end metadata, creator, and active status.</REQUIREMENT>
      <REQUIREMENT>Build a video service that generates unique room names, signed participant tokens, moderator privileges, attendee privileges, and provider-specific join details.</REQUIREMENT>
      <REQUIREMENT>Support self-hosted Jitsi configuration as the production default, while keeping provider settings configurable through environment and application configuration.</REQUIREMENT>
      <REQUIREMENT>Implement start, status, join details, and end endpoints with clear JSON responses and policy enforcement.</REQUIREMENT>
      <REQUIREMENT>Update the collaboration extension to open a secure video panel, show active call status, react to call started or ended events, and clean up on leave.</REQUIREMENT>
      <REQUIREMENT>Update the workspace Blade shell with a video button, active-call indicator, join status, and clear failure messages when video service configuration is incomplete.</REQUIREMENT>
      <REQUIREMENT>Persist and display session metadata useful to instructors, administrators, and operations teams.</REQUIREMENT>
    </IMPLEMENTATION_REQUIREMENTS>
    <SECURITY_REQUIREMENTS>
      <REQUIREMENT>Only workspace collaborators can obtain join details, and only instructors or administrators can end managed instructional calls unless policy allows otherwise.</REQUIREMENT>
      <REQUIREMENT>Use short-lived signed tokens and never expose provider secrets to the browser or extension.</REQUIREMENT>
      <REQUIREMENT>Validate video provider configuration at startup or first use and fail closed when required settings are missing.</REQUIREMENT>
      <REQUIREMENT>Log start, join detail requests, end actions, token generation failures, and provider errors.</REQUIREMENT>
    </SECURITY_REQUIREMENTS>
    <ACCEPTANCE_CRITERIA>
      <CRITERION>Authorized users can start or join a workspace video session and see active status in Blade and the extension.</CRITERION>
      <CRITERION>Unauthorized users cannot retrieve room details or tokens.</CRITERION>
      <CRITERION>Instructors and administrators can end active sessions and all clients receive status updates.</CRITERION>
      <CRITERION>Tests cover video authorization, active room reuse, token generation boundaries, and end-session behavior.</CRITERION>
    </ACCEPTANCE_CRITERIA>
    <EXECUTION_PROTOCOL>
      <STEP>Inspect video room schema, Jitsi configuration needs, workspace policies, and extension command structure.</STEP>
      <STEP>Plan provider configuration, API contracts, UI integration, and test coverage.</STEP>
      <STEP>Implement services, routes, controllers, events, extension updates, Blade updates, and documentation.</STEP>
      <STEP>Verify with tests and a configured provider check where environment supports it.</STEP>
      <STEP>Report video readiness, deployment prerequisites, and readiness for Phase 8.</STEP>
    </EXECUTION_PROTOCOL>
  </MASTER_PROMPT>
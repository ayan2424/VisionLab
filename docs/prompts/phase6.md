<MASTER_PROMPT Phase="6" Name="AI_Agent_Patch_Review_And_Audit_Trail" Version="6.0">
    <SYSTEM_ROLE>
      You are a senior AI integration architect, Laravel backend engineer, and secure coding-agent workflow designer. Your responsibility is to integrate responsible AI assistance without allowing uncontrolled file mutation.
    </SYSTEM_ROLE>
    <PRODUCT_CONTEXT>
      VisionLab uses AI to help students understand, plan, and improve code inside governed workspaces. AI must be useful, auditable, mode-aware, and constrained by instructor and platform policy.
    </PRODUCT_CONTEXT>
    <PHASE_GOAL>
      Integrate the AI backend, Continue extension configuration, controlled AI modes, patch proposal workflow, approval UI, snapshots, logs, and rollback safety.
    </PHASE_GOAL>
    <DEPENDENCIES>
      Requires Phase 3 file APIs, Phase 4 extension delivery, and Phase 5 real-time channels. AI must use workspace-scoped authorization and audit storage created in earlier phases.
    </DEPENDENCIES>
    <IMPLEMENTATION_REQUIREMENTS>
      <REQUIREMENT>Build an AI service that supports chat, planning, and agent modes with explicit tool permissions per mode.</REQUIREMENT>
      <REQUIREMENT>Route all model calls through the Laravel backend so secrets, policy checks, tool use, logging, and rate limits remain server-side.</REQUIREMENT>
      <REQUIREMENT>Configure the Continue extension or approved AI extension to use the Laravel AI endpoint, workspace context, user token, and mode controls.</REQUIREMENT>
      <REQUIREMENT>Expose an OpenAI-compatible Laravel proxy for the rebranded Continue extension while preserving VisionLab-specific mode enforcement, workspace authorization, provider abstraction, token accounting, and tool permission checks.</REQUIREMENT>
      <REQUIREMENT>Inject workspace-specific Continue configuration at container startup, including proxy base URL, workspace token, slash commands, mode defaults, and no direct model-provider secrets.</REQUIREMENT>
      <REQUIREMENT>Implement sandboxed tools for reading files, listing directories, searching code, preparing patches, and returning structured tool results.</REQUIREMENT>
      <REQUIREMENT>Store AI chat sessions, messages, tool calls, proposed patches, snapshots, approvals, rejections, rollbacks, and file changes with actor and workspace context.</REQUIREMENT>
      <REQUIREMENT>Build a VisionLab patch reviewer extension or panel that displays pending patches with file metadata, readable diff, approve, reject, and request-change actions.</REQUIREMENT>
      <REQUIREMENT>Implement a safe plan-to-agent bridge where a plan can trigger a server-side implementation run, but the run can only create pending patches and PatchProposed events until a human approves them.</REQUIREMENT>
      <REQUIREMENT>Broadcast patch proposal and patch status events to authorized workspace users.</REQUIREMENT>
      <REQUIREMENT>Provide AI memory only as an explicit, visible workspace file governed by the same sandbox rules, with concise content and audit logs for updates.</REQUIREMENT>
      <REQUIREMENT>Implement an AI risk control matrix covering prompt injection, indirect prompt injection through files, sensitive information disclosure, insecure tool design, excessive agency, model denial of service, supply-chain risk, and overreliance.</REQUIREMENT>
      <REQUIREMENT>Separate trusted system instructions, developer policies, user requests, repository content, retrieved file content, and tool outputs so untrusted content is never treated as authority.</REQUIREMENT>
      <REQUIREMENT>Add prompt and tool-behavior evaluations for each AI mode, including positive cases, denial cases, malformed tool input, hostile file content, large input, provider outage, and rollback verification.</REQUIREMENT>
    </IMPLEMENTATION_REQUIREMENTS>
    <SECURITY_REQUIREMENTS>
      <REQUIREMENT>Never allow AI to write directly to files without a stored patch, human approval, snapshot, and audit log.</REQUIREMENT>
      <REQUIREMENT>Block reads or writes to secrets, environment files, dependency directories, storage outside the workspace, generated vendor content, and platform configuration unless explicitly allowed by administrator policy.</REQUIREMENT>
      <REQUIREMENT>Apply rate limits, token budgets, request validation, model error handling, and provider timeout handling.</REQUIREMENT>
      <REQUIREMENT>Sanitize AI-rendered content and isolate previews so generated HTML or scripts cannot access platform credentials.</REQUIREMENT>
      <REQUIREMENT>Require human confirmation for every AI-generated change that can affect code, files, grades, extension policy, user permissions, or platform configuration.</REQUIREMENT>
      <REQUIREMENT>Never send secrets, private tokens, full environment files, administrator-only records, or unrelated users' data to an external model provider.</REQUIREMENT>
    </SECURITY_REQUIREMENTS>
    <ACCEPTANCE_CRITERIA>
      <CRITERION>Chat mode can explain code without write access.</CRITERION>
      <CRITERION>Planning mode produces implementation plans without mutating files.</CRITERION>
      <CRITERION>Agent mode can propose patches, but files change only after approval and can be rolled back from snapshots.</CRITERION>
      <CRITERION>AI actions are visible in audit logs and tied to user, workspace, file path, and session.</CRITERION>
      <CRITERION>Tests cover sandbox denial, patch approval, patch rejection, rollback, rate limits, and unauthorized access.</CRITERION>
    </ACCEPTANCE_CRITERIA>
    <EXECUTION_PROTOCOL>
      <STEP>Inspect current AI tables, file APIs, extension interfaces, and policy boundaries.</STEP>
      <STEP>Plan AI endpoint contracts, tool permission matrix, patch lifecycle, extension UI, and tests.</STEP>
      <STEP>Implement backend services, controllers, events, extension integration, UI surfaces, and documentation.</STEP>
      <STEP>Verify with tests and controlled local AI workflow checks using safe files.</STEP>
      <STEP>Report AI readiness, provider configuration needs, and readiness for Phase 7.</STEP>
    </EXECUTION_PROTOCOL>
  </MASTER_PROMPT>
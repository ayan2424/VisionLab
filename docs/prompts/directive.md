<GLOBAL_DIRECTIVE>
    <PRODUCT_STANDARD>
      VisionLab is a production-ready and competition-ready collaborative coding and learning platform for universities. It combines a Laravel learning management system, secure code-server workspaces, real-time collaboration, instructor governance, responsible AI assistance, video sessions, analytics, notifications, and production operations.
    </PRODUCT_STANDARD>
    <IMPLEMENTATION_MODEL>
      Every phase must begin with repository inspection, dependency discovery, and a concise implementation plan. After planning, implement the work fully, verify it with tests or executable checks, document operational steps, and report remaining risks. Do not ship temporary content, incomplete views, unsafe shortcuts, overstatement, or nonfunctional screens.
    </IMPLEMENTATION_MODEL>
    <RESEARCH_BASIS>
      <BASIS>Prompt quality must be treated as an engineering system: instructions must be explicit, scoped, testable, and tied to evaluations so behavior can be measured when prompts or model versions change.</BASIS>
      <BASIS>Agent workflows must control eagerness, tool use, context gathering, write permissions, and completion criteria so an implementation agent does not overreach, stall, or mutate sensitive state without evidence.</BASIS>
      <BASIS>Security requirements should align with OWASP ASVS-style verification: authentication, session management, access control, validation, output encoding, logging, data protection, HTTP security, files, and business logic must all be testable.</BASIS>
      <BASIS>AI features must account for prompt injection, sensitive information disclosure, insecure tool design, excessive agency, model denial of service, supply-chain risk, and overreliance.</BASIS>
      <BASIS>Containerized IDEs must be exposed only through authenticated, TLS-protected, policy-controlled routes, with extension storage, user data, download behavior, proxy behavior, and webview secure-context requirements handled deliberately.</BASIS>
      <BASIS>PWA behavior must improve reliability without misleading users: service workers are optional enhancement layers, offline fallbacks must be explicit, and online-only capabilities such as live IDE workspaces must fail honestly.</BASIS>
      <BASIS>Extension & IDE Modifications: AI must strictly use source-level compilation (e.g., Docker) for both IDE extensions AND the core `code-server` engine. Shortcuts like regex-patching compiled JS bundles to hide UI elements or bypass proper telemetry removal are strictly forbidden. Unwanted components must be eradicated natively at the source level.</BASIS>
      <BASIS>Container environments are fundamentally Nix-based. Using `apt` or `apt-get` to install packages is strictly non-viable and forbidden. All package resolution must occur via `dev.nix`.</BASIS>
    </RESEARCH_BASIS>
    <SOURCE_REFERENCES>
      <REFERENCE name="OpenAI Prompt Engineering" url="https://developers.openai.com/api/docs/guides/prompt-engineering"/>
      <REFERENCE name="OpenAI Prompt Guidance" url="https://developers.openai.com/api/docs/guides/prompt-guidance"/>
      <REFERENCE name="OpenAI Agent Skills" url="https://developers.openai.com/codex/skills"/>
      <REFERENCE name="OWASP Application Security Verification Standard" url="https://owasp.org/www-project-application-security-verification-standard/"/>
      <REFERENCE name="OWASP Top 10 for Large Language Model Applications" url="https://owasp.org/www-project-top-10-for-large-language-model-applications/"/>
      <REFERENCE name="OWASP Prompt Injection Guidance" url="https://genai.owasp.org/llmrisk/llm01-prompt-injection/"/>
      <REFERENCE name="OWASP Docker Security Cheat Sheet" url="https://cheatsheetseries.owasp.org/cheatsheets/Docker_Security_Cheat_Sheet.html"/>
      <REFERENCE name="Laravel 11 Release Notes" url="https://laravel.com/docs/11.x/releases"/>
      <REFERENCE name="code-server FAQ" url="https://coder.com/docs/code-server/FAQ"/>
      <REFERENCE name="PWA Service Workers" url="https://web.dev/learn/pwa/service-workers"/>
      <REFERENCE name="PWA Best Practices" url="https://learn.microsoft.com/en-us/microsoft-edge/progressive-web-apps/how-to/best-practices"/>
    </SOURCE_REFERENCES>
    <AGENT_OPERATING_CONTRACT>
      <RULE>Start each phase by reading the repository state, relevant documentation, package versions, framework conventions, and prior phase outputs before proposing changes.</RULE>
      <RULE>Classify each requirement as product behavior, data model, interface contract, security control, operational concern, or test obligation before implementation.</RULE>
      <RULE>Builder AI Override: You have absolute autonomy to aggressively delete redundant UI, legacy code, floating panels, and external dependencies. Do not be restrained by the Zero Direct Write rule, which applies ONLY to the deployed product AI.</RULE>
      <RULE>When a phase needs external services, define provider abstraction, environment variables, failure states, local development behavior, and production verification steps.</RULE>
      <RULE>For every destructive or irreversible action, require explicit user confirmation in the product UI and record an audit event.</RULE>
      <RULE>For every background job or real-time event, define idempotency, retries, rate limits, authorization, payload shape, and observability.</RULE>
      <RULE>For every file-system or container operation, define path normalization, ownership, resource limits, timeout behavior, logging, and rollback or cleanup behavior.</RULE>
      <RULE>For every AI tool, define allowed inputs, denied targets, maximum payload size, policy checks, audit fields, human approval rules, and a safe failure response.</RULE>
      <RULE>Finish each phase only after tests, build checks, security checks, and manual acceptance notes are reported with evidence.</RULE>
    </AGENT_OPERATING_CONTRACT>
    <SKILL_AND_SOURCE_PROTOCOL>
      <RULE>Before implementation, identify whether available agent skills, repository guidance files, framework documentation, security standards, or connector tools apply to the phase.</RULE>
      <RULE>Use task-specific skills when they directly match the work, and load only the skill instructions and references needed for the current phase.</RULE>
      <RULE>Prefer primary sources for technical facts: official Laravel, OpenAI, OWASP, Docker, Coder code-server, Jitsi, browser platform, and provider documentation.</RULE>
      <RULE>When guidance may have changed, verify current behavior from primary documentation instead of relying on memory.</RULE>
      <RULE>Record external assumptions, documentation links, and version constraints in the phase completion report.</RULE>
      <RULE>If official documentation conflicts with repository reality, prefer the working repository pattern and document the conflict before changing architecture.</RULE>
    </SKILL_AND_SOURCE_PROTOCOL>
    <UNIVERSAL_REQUIREMENTS>
      <REQUIREMENT>Use Laravel 11, MySQL 8, Blade, Tailwind CSS, Laravel Reverb, Redis where needed, Docker-managed code-server workspaces, and TypeScript for VS Code extensions.</REQUIREMENT>
      <REQUIREMENT>Preserve an ultra-premium dark design system (Strict #0a0a0a) with accessible contrast, responsive layouts, and highly interactive navigation. The UI must feature 3D-style interactions, fully rounded/pilled geometry, and floating elements. Use fluid gradients and vibrant glassmorphism for frontend pages, and strictly use an Orange accent color exclusively for the full-screen IDE Workspace container environment.</REQUIREMENT>
      <REQUIREMENT>Use complete data flows backed by persistence, authorization, validation, audit logs, error states, and recovery paths.</REQUIREMENT>
      <REQUIREMENT>Use realistic seed data only where it supports local development, automated tests, or competition evaluation. Seeded data must be clearly separated from production runtime behavior.</REQUIREMENT>
      <REQUIREMENT>Never expose secrets, write outside allowed workspace paths, bypass policies, disable authorization for convenience, or hide failed operations behind success messages.</REQUIREMENT>
      <REQUIREMENT>Keep generated code maintainable, idiomatic, and aligned with the existing repository conventions discovered during inspection.</REQUIREMENT>
      <REQUIREMENT>Maintain a living architecture decision log for major choices such as AI provider, workspace isolation, extension governance, video provider, deployment topology, and analytics privacy.</REQUIREMENT>
      <REQUIREMENT>Define explicit contracts for APIs, events, jobs, policies, services, and extension messages before depending on them across phases.</REQUIREMENT>
      <REQUIREMENT>Use feature flags or configuration gates for high-risk capabilities such as AI write access, marketplace access, video, push notifications, and one-click deployment.</REQUIREMENT>
      <REQUIREMENT>Use localization-ready strings, accessible form labels, keyboard navigation, semantic HTML, and responsive design as baseline product requirements.</REQUIREMENT>
    </UNIVERSAL_REQUIREMENTS>
    <EVALUATION_RUBRIC>
      <DIMENSION name="Functional Completeness">Every advertised workflow must be implemented end to end with persistence, validation, authorization, UI states, and recovery behavior.</DIMENSION>
      <DIMENSION name="Security">Every sensitive workflow must include policy checks, input validation, output escaping, rate limiting where appropriate, audit logging, and tests for denial paths.</DIMENSION>
      <DIMENSION name="Reliability">Services must define timeout behavior, retries, idempotency, health checks, background job handling, and clear failure messages.</DIMENSION>
      <DIMENSION name="Maintainability">Code must follow local conventions, keep boundaries clear, avoid duplicated business logic, and document non-obvious operational decisions.</DIMENSION>
      <DIMENSION name="User Experience">Interfaces must be accessible, responsive, fast to scan, consistent, and free of dead ends or decorative-only screens.</DIMENSION>
      <DIMENSION name="Evidence">Each phase must provide commands run, tests passed, screenshots or manual checks where useful, migration status, and unresolved risks.</DIMENSION>
    </EVALUATION_RUBRIC>
    <MERGED_DETAIL_BLUEPRINTS source="PROMPTS_ENHANCED.xml useful detail merged into production-safe structure">
      <PHASE_BLUEPRINT Phase="1" Name="Foundation Specifics">
        <DETAIL>Establish baseline package capabilities for authentication scaffolding, permissions, real-time broadcasting, image handling, development diagnostics, token-based API access, video tokens, queued jobs, and production health checks.</DETAIL>
        <DETAIL>Define a design system with modern premium gradients (e.g. Google's latest gradient themes), ultra-rounded pill shapes, and floating 3D-interactive elements for frontend pages. The Hero section must be a superb, highly interactive 3D centerpiece. Ensure surfaces and borders reflect a premium glassmorphism aesthetic. Use an Orange primary accent specifically for the workspace container environment.</DETAIL>
        <DETAIL>Define typography with a primary UI font and a monospace code font, loaded consistently through the main layout without layout shift.</DETAIL>
        <DETAIL>Define reusable motion tokens for fade-in, glow pulse, shimmer loading, gradient movement, modal entrance, sidebar entrance, and toast entrance, while respecting reduced-motion preferences.</DETAIL>
        <DETAIL>Define reusable utilities for glass surfaces, focused controls, thin dark scrollbars, accessible focus rings, code text, responsive grids, and status indicators.</DETAIL>
        <DETAIL>Use a complete initial data model covering users, courses, enrollments, announcements, assignments, submissions, workspaces, workspace collaborators, collaboration sessions, video rooms, extensions, workspace extensions, AI chat sessions, AI messages, AI action logs, AI snapshots, pending patches, analytics events, collaboration chat messages, push subscriptions, workspace quotas, user badges, submission forensics, deployment history, audit logs, and notification preferences.</DETAIL>
        <DETAIL>Seed only realistic development and evaluation records: administrator, instructors, students, courses, enrollments, assignments, announcements, approved extensions, and role permissions. Mark seeded credentials and seed-only data as non-production artifacts.</DETAIL>
        <DETAIL>Define core model helpers for role checks, account status checks, active scopes, ownership checks, and policy-friendly relationship access.</DETAIL>
        <DETAIL>Build the landing page from product truth: classroom management, full IDE workspaces, collaboration, responsible AI, governance, and deployment readiness. Avoid decorative claims that are not backed by implemented flows.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="2" Name="Classroom Specifics">
        <DETAIL>Course screens must include list, create, edit, detail, stream, assignments, people, enrollment controls, course status, cover image handling, instructor ownership, and administrator oversight.</DETAIL>
        <DETAIL>Enrollment must support join code validation, invitation state, active state, removal, duplicate prevention, capacity checks if configured, and enrollment audit timestamps.</DETAIL>
        <DETAIL>Assignment workflows must support rich instructions, due date, maximum points, starter material, start assignment, workspace creation or reuse, submit, late calculation, resubmit policy where configured, grade, feedback, and grade book summaries.</DETAIL>
        <DETAIL>Submission snapshots must preserve the evaluated workspace state in a controlled storage location with clear retention and access rules.</DETAIL>
        <DETAIL>Announcements must support authored content, pinned state, course stream placement, unread state, real-time event readiness, and notification integration.</DETAIL>
        <DETAIL>Dashboards must show real data: enrolled courses, upcoming deadlines, recent announcements, pending submissions, grading queue, recent activity, and administrative overview metrics.</DETAIL>
        <DETAIL>Every classroom list must include pagination, filters or search where the list can grow, empty states, and role-appropriate actions.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="3" Name="Workspace IDE Specifics">
        <DETAIL>The workspace manager must resolve workspace root, project root, container name, dynamic port or proxy route, access token, container image (which must support Nix-based declarative environments via `dev.nix` for reproducible language toolchains), resource policy, environment variables, startup timeout, health check, and cleanup behavior.</DETAIL>
        <DETAIL>Workspace startup must be idempotent and resilient to `SIGABRT` / JavaScript heap OOM crashes. If a container crashes, repair or restart it gracefully according to policy and log the decision.</DETAIL>
        <DETAIL>Workspace resource resolution must be deterministic: administrator global defaults, course-specific quota, user-specific quota, role default, then hard platform fallback. The effective quota must be visible to administrators and logged on container start.</DETAIL>
        <DETAIL>The IDE shell must be completely full-screen within the container workspace, with no external JavaScript file explorer wrapping it. It must natively sync preloader removal with `code-server`'s internal `/healthz` dynamic polling.</DETAIL>
        <DETAIL>The IDE must support integrated **Web Previews** (via Simple Browser proxying) so that students can view dynamically rendered web applications, not just static HTML files.</DETAIL>
        <DETAIL>File APIs must return stable JSON shapes with success, validation error, authorization error, conflict, not found, and server error cases.</DETAIL>
        <DETAIL>The workspace UI must clearly distinguish container starting, running, unhealthy, stopped, failed, unauthorized, and offline states.</DETAIL>
        <DETAIL>Do not use external public IDE instances as a substitute for a workspace. If the local environment cannot run containers, show a truthful setup error and document prerequisites.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="4" Name="Extension and Lockdown Specifics">
        <DETAIL>Maintain an extension manifest with package identifier, display name, version, category, source, checksum, built-in status, required status, global availability, course availability, workspace overrides, and rollout state.</DETAIL>
        <DETAIL>Build custom VisionLab extensions from source with versioned artifacts and checksums. Approved third-party extensions must have documented source, license review, version pinning, and integrity verification.</DETAIL>
        <DETAIL>Use a two-lane artifact strategy: source-built artifacts for custom or sensitive extensions, and verified official prebuilt artifacts for standard utilities only when license, provenance, checksum, and compatibility are documented.</DETAIL>
        <DETAIL>The VisionLab Agent must be created through a full Continue source rebuild pipeline: obtain the official source or maintained fork, audit the entire extension source tree, update every relevant source, configuration, asset, localization, command, menu, webview, package metadata, default endpoint, and visible product reference, compile the extension, record checksums, and smoke-test it in code-server.</DETAIL>
        <DETAIL>The rebrand is incomplete if only package metadata is changed. Any remaining old product identity, default provider endpoint, unreviewed command label, stale UI copy, or untracked runtime override must block release until it is intentionally updated or documented as a legally required upstream reference.</DETAIL>
        <DETAIL>After the initial compliant source import, VisionLab must treat the agent as its own maintained fork: own version numbers, changelog, release notes, CI checks, artifact registry, compatibility matrix, and update process. Upstream updates may be reviewed manually for security or useful fixes, but must never be pulled automatically into production artifacts.</DETAIL>
        <DETAIL>Preserve upstream license notices, copyright statements, and required attribution files according to the reviewed open-source license. Public product branding should be VisionLab, but internal records must remain legally accurate about source provenance.</DETAIL>
        <DETAIL>Install required extensions into immutable locations controlled by the container image or root-owned filesystem, while user-level optional extensions are governed by policy.</DETAIL>
        <DETAIL>Marketplace controls must support enabled, disabled, restricted to approved gallery, and instructor-managed modes.</DETAIL>
        <DETAIL>Policy resolution order must be explicit: administrator global rules, course rules, instructor rules where allowed, workspace overrides, and user preference only for optional tools.</DETAIL>
        <DETAIL>Active workspace synchronization must use queued jobs with retries, idempotency, status reporting, and audit events.</DETAIL>
        <DETAIL>Students must never be able to override required extensions, remove audit extensions, bypass marketplace policy, or alter global code-server configuration.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="5" Name="Collaboration Specifics">
        <DETAIL>Define event contracts for presence joined, presence left, cursor moved, selection changed, document changed, chat message sent, typing state changed, user idle, workspace warning, and collaboration error.</DETAIL>
        <DETAIL>The collaboration extension must be modular: realtime connection manager, document sync, cursor sync, chat panel, presence manager, video bridge, patch event listener, notification bridge, and shared configuration loader.</DETAIL>
        <DETAIL>Document sync must prevent echo loops, debounce local changes, identify source, handle remote updates safely, and surface conflicts instead of overwriting silently.</DETAIL>
        <DETAIL>Cursor sync must assign stable colors, show remote user labels, expire stale decorations, and avoid distracting motion.</DETAIL>
        <DETAIL>Chat must persist useful messages, sanitize rendered content, support timestamps, show delivery status, and recover after reconnect.</DETAIL>
        <DETAIL>Blade and extension presence must remain consistent through Reverb reconnects, heartbeat cleanup, and workspace close events.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="6" Name="AI Agent Specifics">
        <DETAIL>Define a mode permission matrix: chat mode can read authorized context only, planning mode can read and propose steps only, agent mode can prepare patches but cannot apply them without human approval.</DETAIL>
        <DETAIL>Define AI tool contracts for reading files, listing directories, searching code, preparing patches, retrieving workspace metadata, retrieving allowed snippets, and reporting inability to act safely.</DETAIL>
        <DETAIL>Expose a Laravel OpenAI-compatible chat-completions style proxy for the rebranded Continue extension, while still enforcing VisionLab workspace context, user identity, AI mode, token limits, provider policy, and tool restrictions on the server.</DETAIL>
        <DETAIL>Patch lifecycle must include pending, approved, rejected, applied, failed, rolled back, and expired states.</DETAIL>
        <DETAIL>The patch reviewer must show file path, summary, original content, proposed content, readable diff, risk notes, approve, reject, request change, and rollback access where permitted.</DETAIL>
        <DETAIL>Support a safe plan-to-implementation command bridge: planning output may offer a command that starts an agent run, but that run must only generate pending patches and broadcast PatchProposed events for human review.</DETAIL>
        <DETAIL>AI actions must be linked to session, message, tool call, user, workspace, file path, old content hash, new content hash, approval actor, and timestamp.</DETAIL>
        <DETAIL>AI provider integration must support streaming, timeout, retry boundaries, token accounting, cost accounting where available, provider error messages, and graceful degradation when unavailable.</DETAIL>
        <DETAIL>Prompt-injection defense must treat repository files, chat text, assignment instructions, and external content as untrusted data, not policy authority.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="7" Name="Video Specifics">
        <DETAIL>Video service configuration must support self-hosted production mode and external provider mode through the same application-level service interface.</DETAIL>
        <DETAIL>Room records must track workspace, creator, provider, room name, active state, started time, ended time, moderator role, participant access requests, and provider errors.</DETAIL>
        <DETAIL>The extension video panel must open a secure embedded meeting experience when configured, handle leave events, close cleanly, and show join status.</DETAIL>
        <DETAIL>The workspace top bar must show call status, join action, start action where authorized, end action where authorized, and error states when provider setup is incomplete.</DETAIL>
        <DETAIL>Video events must update Blade and extension clients consistently when calls start, end, or become unavailable.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="8" Name="Admin Specifics">
        <DETAIL>Admin navigation must include dashboard, users, courses, workspaces, extensions, quotas, analytics, audit logs, security settings, notification settings, and system health.</DETAIL>
        <DETAIL>User management must include search, role filter, status filter, profile edit, role change, suspension, activation, last activity, and final-administrator protection.</DETAIL>
        <DETAIL>Workspace management must include owner, linked course, linked assignment, collaborators, status, container identifier, resource usage, storage usage, recent activity, stop, archive, and cleanup actions.</DETAIL>
        <DETAIL>Extension management must include registry, global policy, course policy, workspace policy, rollout status, artifact integrity, and synchronization job status.</DETAIL>
        <DETAIL>Quota management must include defaults, course overrides, user overrides, effective quota preview, active workspace impact, and audit history.</DETAIL>
        <DETAIL>Audit log views must be searchable by actor, resource, action, result, date, IP address where available, and correlation identifier.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="9" Name="Analytics and Growth Specifics">
        <DETAIL>Analytics must use real platform events and include definitions for each metric so dashboards are explainable.</DETAIL>
        <DETAIL>Administrator analytics must include user activity, course activity, submissions, AI usage, patch approval rate, active workspaces, video usage, extension usage, failed jobs, and system health signals.</DETAIL>
        <DETAIL>Instructor analytics must include engagement by course, assignment completion, late work, grading backlog, AI assistance patterns, collaboration activity, and at-risk student indicators.</DETAIL>
        <DETAIL>Student analytics must include personal progress, activity heatmap, streak, badges, submissions, grades, AI usage transparency, and deployment history where enabled.</DETAIL>
        <DETAIL>VisionGuard must distinguish human typing, AI-approved patches, pasted text where detectable, imported files, and system-generated starter code. It must present confidence and limitations clearly.</DETAIL>
        <DETAIL>Gamification must award badges from real events and avoid encouraging insecure or low-quality behavior.</DETAIL>
        <DETAIL>Student project deployment must be an asynchronous, provider-abstracted product feature with confirmation, public exposure warning, deployment records, status polling, dashboard history, notifications, and provider failure recovery.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="10" Name="PWA and Notifications Specifics">
        <DETAIL>The manifest must define product name, short name, start URL, display mode, theme color, background color, icons, maskable icons, orientation, and app description.</DETAIL>
        <DETAIL>The service worker must define static asset caching, navigation fallback, sensitive API network-only behavior, workspace network-only behavior, cache version cleanup, update handling, and browser support fallback.</DETAIL>
        <DETAIL>The install prompt must be user-controlled, dismissible, respectful of browser rules, and remembered per user or device.</DETAIL>
        <DETAIL>Push subscriptions must validate endpoint, public key, auth token, content encoding, user ownership, and unsubscribe behavior.</DETAIL>
        <DETAIL>Notifications must support assignment due reminders, announcements, grading feedback, deployment completion, video session start where allowed, and account alerts.</DETAIL>
        <DETAIL>Offline indicators must be shared across layouts, with special IDE messaging that makes online-only requirements clear.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="11" Name="Hardening Specifics">
        <DETAIL>Security verification must cover OWASP ASVS-style categories and include both implementation controls and tests proving those controls.</DETAIL>
        <DETAIL>Automated tests must cover authentication, RBAC, classroom workflows, workspace path boundaries, file APIs, collaboration channels, AI patch lifecycle, extension policy, video access, push subscriptions, analytics visibility, admin actions, and deployment health.</DETAIL>
        <DETAIL>Performance work must include query inspection, eager loading, indexes, cache strategy, queue separation, job retries, rate limits, and slow dashboard mitigation.</DETAIL>
        <DETAIL>Container hardening must include resource limits, least privilege, restricted mounts, network boundaries, image provenance, update strategy, and operator diagnostics.</DETAIL>
        <DETAIL>Quality verification must include responsive UI checks, accessibility checks, browser console checks, service worker checks, and critical workflow browser automation where available.</DETAIL>
      </PHASE_BLUEPRINT>
      <PHASE_BLUEPRINT Phase="12" Name="Deployment and Evaluation Specifics">
        <DETAIL>Production topology must define application, web server, database, Redis, queue workers, scheduler, Reverb, workspace containers, storage, video service, AI provider, push service, monitoring, and backup boundaries.</DETAIL>
        <DETAIL>CI/CD must run dependency installation, static checks where available, backend tests, frontend build, browser tests where available, image build, artifact verification, and deployment only after successful checks.</DETAIL>
        <DETAIL>Deployment documentation must include 24/7 Cloud Server orchestration (e2-standard-8 GCP nodes) for autonomous agents, first deploy, environment variables, migrations, seed data, storage links, workspace image builds, and incident handling.</DETAIL>
        <DETAIL>Health checks must cover database, Redis, queue, scheduler, Reverb, storage, workspace orchestration, AI provider configuration, push configuration, and video provider configuration.</DETAIL>
        <DETAIL>Competition readiness must rely on functioning product workflows, prepared evaluation accounts, reset procedures, preflight checklist, service contingency plan, and operator runbook.</DETAIL>
        <DETAIL>Release evidence must include version manifest, migration state, test summary, security verification summary, deployment checklist, backup confirmation, known risks, and rollback plan.</DETAIL>
      </PHASE_BLUEPRINT>
    </MERGED_DETAIL_BLUEPRINTS>
    <ADOPTED_SOURCE_STRATEGIES>
      <STRATEGY name="Concrete Phase Readiness">Each phase uses explicit dependencies, implementation requirements, security requirements, acceptance criteria, and execution protocol so an agent can determine whether it may start and when it is finished.</STRATEGY>
      <STRATEGY name="Single Foundation Schema">The data model is defined early enough to avoid table drift, while later phases may add migrations only when the repository proves a new capability needs them.</STRATEGY>
      <STRATEGY name="Design System First">Visual tokens, typography, motion, form controls, tables, cards, status indicators, toasts, empty states, and error pages are foundation work, not finishing polish.</STRATEGY>
      <STRATEGY name="Policy Everywhere">Controllers, API endpoints, broadcasts, file APIs, extension controls, video rooms, admin actions, deployments, and analytics views must all flow through policies or gates.</STRATEGY>
      <STRATEGY name="Container Lifecycle Discipline">Workspace start, stop, health, quota, token, routing, storage, cleanup, and error states are treated as a managed lifecycle rather than scattered Docker calls.</STRATEGY>
      <STRATEGY name="Human Approved AI Mutation">AI may assist deeply, but code and operational changes require sandboxing, patch preview, audit trail, and human confirmation before mutation.</STRATEGY>
      <STRATEGY name="Extension Governance">Extension delivery combines artifact provenance, integrity checks, immutable required tools, role-aware optional tools, and runtime policy enforcement.</STRATEGY>
      <STRATEGY name="Continue Source Ownership">The VisionLab AI agent must be produced from the official Continue source tree or a maintained fork, with deliberate full-source audit, source-level branding, configuration, compilation, smoke testing, and artifact recording. The core IDE `code-server` must similarly be natively recompiled from source to implement branding and hide UI components. Binary package editing, wrapper-only branding, package-name-only edits, container-runtime overlays, or post-install disguises are not acceptable for the AI agent, any sensitive extension, or the code-server binary.</STRATEGY>
      <STRATEGY name="Smart Declarative Environments">Workspace containers use Nix (`dev.nix`) for package resolution (e.g., PHP, Composer, Node). Do not restrict the AI Agent with hardcoded `dev.nix` code snippets. Instead, present the conceptual requirement and empower the AI to use its robust thinking power to dynamically construct and resolve the optimal Nix environment logic for any given workspace.</STRATEGY>
      <STRATEGY name="Independent Agent Fork">The upstream AI extension source may be imported once as a legally reviewed starting point, then VisionLab must own its source repository, build pipeline, release cadence, artifact registry, roadmap, and maintenance. Future workspace installs must use VisionLab-built artifacts only, with no automatic dependency on upstream extension releases, registries, branding, endpoints, or runtime services.</STRATEGY>
      <STRATEGY name="Real Product Evaluation">Competition readiness is built from functioning workflows, evaluation accounts, reliable preflight checks, and operator runbooks, not staged-only screens.</STRATEGY>
      <STRATEGY name="Async External Integrations">AI providers, video providers, push, and project deployment providers require queued work, retries, status records, timeout handling, and user-visible failure states.</STRATEGY>
      <STRATEGY name="Evidence-Based Completion">A phase is complete only when tests, build checks, manual verification, security checks, migration status, and operational notes are attached to the report.</STRATEGY>
    </ADOPTED_SOURCE_STRATEGIES>
    <PHASE_DELIVERY_GATE>
      <GATE name="Ready">The agent confirms prerequisites, installed packages, environment variables, database state, previous phase outputs, and relevant documentation before implementation.</GATE>
      <GATE name="Design">The agent writes a concise plan covering data changes, API contracts, UI surfaces, policies, jobs, events, tests, and rollback concerns.</GATE>
      <GATE name="Build">The agent implements in cohesive slices, keeps feature flags for risky capabilities, and avoids unrelated refactors.</GATE>
      <GATE name="Verify">The agent runs migrations, tests, build checks, static checks where available, negative authorization tests, and browser checks where the feature is user-facing.</GATE>
      <GATE name="Release Evidence">The agent reports changed files, commands run, test results, screenshots or manual checks when useful, unresolved risks, operator actions, and the next phase readiness state.</GATE>
    </PHASE_DELIVERY_GATE>
    <CORE_ENTITY_CONTRACT>
      <ENTITY name="users">Must include identity, role, avatar or initials fallback, theme preference, status, last activity, streak counters where gamification is enabled, timestamps, and helper methods for role and status checks.</ENTITY>
      <ENTITY name="courses">Must include instructor ownership, slug, description, cover image, enrollment code, active status, optional capacity, and indexes for instructor, slug, active status, and enrollment code.</ENTITY>
      <ENTITY name="enrollments">Must include course, student, status, enrolled timestamp, completed or dropped state where needed, and unique course-student constraint.</ENTITY>
      <ENTITY name="announcements">Must include course, author, title, rich body, pinned state, read tracking, notification state, and stream ordering.</ENTITY>
      <ENTITY name="assignments">Must include course, title, instructions, maximum points, due date, starter material reference, workspace policy, late submission policy, and ordering indexes.</ENTITY>
      <ENTITY name="submissions">Must include assignment, student, workspace, snapshot path, status, grade, feedback, graded by, submitted timestamp, graded timestamp, late state, and uniqueness policy.</ENTITY>
      <ENTITY name="workspaces">Must include owner, course, assignment, container identifiers, proxy route or URL, token, active state, storage path, heartbeat, quota data, and lifecycle state.</ENTITY>
      <ENTITY name="workspace_collaborators">Must include workspace, user, collaborator role, joined timestamp, and unique workspace-user constraint.</ENTITY>
      <ENTITY name="collab_sessions">Must include workspace, user, cursor or selection state, online status, heartbeat, assigned color, and stale cleanup metadata.</ENTITY>
      <ENTITY name="video_rooms">Must include workspace, provider, room identifier, creator, active state, start and end timestamps, token generation metadata, and provider error state.</ENTITY>
      <ENTITY name="extensions">Must include package identifier, display name, version, category, artifact path, checksum, source, built-in status, required status, active status, and rollout metadata.</ENTITY>
      <ENTITY name="extension_builds">Must include extension, source repository, source reference, build strategy, branding changes summary, configuration changes summary, artifact checksum, license review status, compatibility status, build logs location, built by, and built timestamp.</ENTITY>
      <ENTITY name="workspace_extensions">Must include workspace, extension, enabled state, effective policy source, sync status, and unique workspace-extension constraint.</ENTITY>
      <ENTITY name="ai_chat_sessions">Must include workspace, user, title, mode, token totals, context metadata, provider metadata, and timestamps.</ENTITY>
      <ENTITY name="ai_messages">Must include session, role, content, tool name, tool input, tool output, token count, safety flags, and timestamps.</ENTITY>
      <ENTITY name="ai_actions_log">Must include workspace, session, user, action type, path, diff summary, content hashes or snapshots, trigger source, result, and timestamps.</ENTITY>
      <ENTITY name="ai_snapshots">Must include workspace, session, file path, content or storage reference, content hash, creator, and timestamps.</ENTITY>
      <ENTITY name="ai_pending_patches">Must include workspace, session, file path, original content or hash, patched content or hash, diff, status, creator, reviewer, reviewed timestamp, and expiry.</ENTITY>
      <ENTITY name="analytics_events">Must include actor, event type, event data, resource reference, IP address where available, user agent where available, correlation identifier, and timestamp.</ENTITY>
      <ENTITY name="collab_chat_messages">Must include workspace, user, sanitized message, delivery metadata, and timestamps.</ENTITY>
      <ENTITY name="push_subscriptions">Must include user, endpoint, public key, auth token, content encoding, browser metadata where useful, and revocation timestamp where supported.</ENTITY>
      <ENTITY name="workspace_quotas">Must include quota name, memory, CPU, disk, timeout, applicable scope, reference identifier, active state, and audit trail.</ENTITY>
      <ENTITY name="user_badges">Must include user, badge type, name, description, icon, earned timestamp, and source event.</ENTITY>
      <ENTITY name="submission_forensics">Must include submission, workspace, human counters, AI counters, pasted or imported counters where detectable, percentages, confidence level, and last synced timestamp.</ENTITY>
      <ENTITY name="deployments">Must include workspace, user, provider, deployment identifier, public URL, status, queued job metadata, error summary, deployed timestamp, and notification state.</ENTITY>
      <ENTITY name="audit_logs">Must include actor, action, resource type, resource ID, previous state, new state, result, IP address where available, user agent where available, correlation identifier, and timestamp.</ENTITY>
      <ENTITY name="notification_preferences">Must include user, channel preferences, event preferences, quiet hours where supported, and timestamps.</ENTITY>
    </CORE_ENTITY_CONTRACT>
    <PHASE_STEP_MATRIX source="Expanded from valid PROMPTS_ENHANCED.xml strategies and normalized into production-safe XML">
      <PHASE_STEPS Phase="1" Name="Foundation, Auth, and Design System">
        <IMPLEMENTATION_STEP id="1.1" name="Project Scaffolding and Package Baseline">
          <DETAIL>Align the Laravel application, PHP version, Node runtime, database, queue driver, Reverb readiness, image handling, role-permission package, API token strategy, and development diagnostics with the repository reality.</DETAIL>
          <DETAIL>Publish and configure only the packages required by the current and future phases. Document every dependency, why it exists, and whether it is production, development, or optional.</DETAIL>
          <DETAIL>Verify the app starts cleanly, assets compile, storage links are configured, and no framework default page remains as the primary product experience.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="1.2" name="Design System Tokens and UI Primitives">
          <DETAIL>Define the dark product palette, semantic status colors, typography, spacing, focus rings, scrollbars, surfaces, panels, cards, tables, forms, buttons, badges, modals, and navigation primitives.</DETAIL>
          <DETAIL>Define restrained motion tokens for entrances, loading shimmer, modal appearance, sidebar appearance, status pulse, and toast transitions, with reduced-motion handling.</DETAIL>
          <DETAIL>Create reusable Blade components so later phases do not rebuild basic UI patterns inconsistently.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="1.3" name="Complete Relational Data Baseline">
          <DETAIL>Create the initial schema for the core entities listed in CORE_ENTITY_CONTRACT, including foreign keys, uniqueness rules, indexes, casts, timestamps, and soft-delete or retention policy where appropriate.</DETAIL>
          <DETAIL>Keep schema names and relationships consistent with future phases so classroom, workspaces, collaboration, AI, video, PWA, analytics, deployment, and operations can build without table drift.</DETAIL>
          <DETAIL>Provide factories and realistic seed data for local development and evaluation only, clearly separated from production behavior.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="1.4" name="Authentication, RBAC, Policies, and Account Status">
          <DETAIL>Implement role-aware registration, login, dashboard redirects, middleware, policies, gates, suspended account rejection, token invalidation, and administrator recovery safety.</DETAIL>
          <DETAIL>Centralize role routing and account status checks so controllers do not duplicate permission logic.</DETAIL>
          <DETAIL>Define baseline policies for users, courses, enrollments, assignments, submissions, workspaces, extensions, AI actions, video rooms, analytics, notifications, and administration.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="1.5" name="Landing Page and Product Shell">
          <DETAIL>Build a professional landing page based on implemented product truth: courses, assignments, secure workspaces, collaboration, AI governance, video sessions, analytics, and operations.</DETAIL>
          <DETAIL>Build authenticated layouts for student, instructor, and administrator navigation with responsive behavior, active route state, notification entry points, and accessible controls.</DETAIL>
          <DETAIL>Ensure all copy, sections, and calls to action match real platform capabilities or clearly planned gated capabilities.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="1.6" name="Foundation Verification">
          <DETAIL>Verify fresh migration, seeding, authentication, role redirects, policy denials, design system rendering, frontend build, and responsive layout checks.</DETAIL>
          <DETAIL>Produce a phase report with architecture decisions, dependency list, threat model, audit standard, commands run, test results, and Phase 2 readiness.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="2" Name="Classroom and Learning Workflows">
        <IMPLEMENTATION_STEP id="2.1" name="Course Management and Enrollment">
          <DETAIL>Implement course list, create, update, archive or deactivate, detail, stream, assignments tab, people tab, cover image, slug, enrollment code, capacity if configured, and instructor ownership.</DETAIL>
          <DETAIL>Implement enrollment by code, instructor invitation, active or invited state, duplicate prevention, removal, completion, and role-specific visibility.</DETAIL>
          <DETAIL>Enforce CoursePolicy and EnrollmentPolicy on controllers, views, routes, and any API endpoints.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="2.2" name="Assignments, Submissions, and Gradebook">
          <DETAIL>Implement assignment creation, edit, delete or archive, rich instructions, starter material, due date, maximum points, late policy, workspace start, submission snapshot, grading, feedback, and status tracking.</DETAIL>
          <DETAIL>Build student and instructor views for assignment lifecycle, including not started, in progress, submitted, late, graded, resubmitted where configured, and denied access states.</DETAIL>
          <DETAIL>Build gradebook summaries that remain efficient through eager loading, indexes, pagination, and role-restricted queries.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="2.3" name="Announcements and Course Notifications">
          <DETAIL>Implement announcement authoring, pinned state, rich body rendering, stream ordering, unread state, deletion or archive policy, and future notification hooks.</DETAIL>
          <DETAIL>Prepare course-level real-time channel contracts without exposing announcements to users outside the course.</DETAIL>
          <DETAIL>Ensure announcement body content is sanitized or safely rendered according to the chosen editor strategy.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="2.4" name="Role-Specific Dashboards">
          <DETAIL>Build student dashboard with enrolled courses, upcoming deadlines, recent announcements, current streak entry point, deployment history entry point where enabled, and join course action.</DETAIL>
          <DETAIL>Build instructor dashboard with owned courses, pending submissions, recent enrollments, grading queue, course activity, and quick create actions.</DETAIL>
          <DETAIL>Build administrator entry dashboard with real counts and links into full operations screens added later.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="2.5" name="Classroom Verification">
          <DETAIL>Test instructor course creation, student enrollment, duplicate enrollment denial, assignment start, submission snapshot, grading, announcement visibility, and unauthorized access.</DETAIL>
          <DETAIL>Verify all classroom screens have loading, empty, validation, success, denied, and error states.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="3" Name="Workspace Infrastructure and IDE">
        <IMPLEMENTATION_STEP id="3.1" name="Workspace Container Lifecycle">
          <DETAIL>Implement a workspace manager that starts, stops, restarts, inspects, repairs, and cleans containers using idempotent lifecycle rules.</DETAIL>
          <DETAIL>Resolve workspace paths, project paths, container names, image names, proxy routes, workspace tokens, health checks, resource quotas, environment variables, and startup timeouts.</DETAIL>
          <DETAIL>Persist lifecycle state and log container creation, reuse, restart, stop, failure, cleanup, and quota application.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="3.2" name="Secure Workspace File APIs">
          <DETAIL>Implement file tree, read, write, create file, create folder, rename, delete, download where allowed, and conflict handling with stable JSON response contracts.</DETAIL>
          <DETAIL>Normalize paths, verify canonical location inside the workspace root, block sensitive files, block traversal, block dependency and platform directories where policy requires it, and log writes.</DETAIL>
          <DETAIL>Apply WorkspacePolicy and collaborator permissions to every file operation.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="3.3" name="IDE Shell Layout">
          <DETAIL>Build the workspace shell with top bar, file explorer, code-server iframe, collaborator region, AI and patch indicators, video entry point, deploy entry point where enabled, status bar, terminal entry point, and responsive sidebar behavior.</DETAIL>
          <DETAIL>Represent starting, running, unhealthy, stopped, unauthorized, offline, and provider-misconfigured states without pretending the IDE is usable when it is not.</DETAIL>
          <DETAIL>Ensure iframe sandboxing, proxy behavior, secure headers, and token handling match the code-server security model.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="3.4" name="File Explorer Frontend">
          <DETAIL>Implement recursive tree rendering, file and folder icons, active file state, refresh, context actions, keyboard shortcuts, toasts, optimistic updates only where reversible, and rollback on failure.</DETAIL>
          <DETAIL>Support mobile and narrow screens without losing core file operations.</DETAIL>
          <DETAIL>Do not rely on external UI libraries unless the repository already standardizes them.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="3.5" name="Workspace Security and Visual Verification">
          <DETAIL>Verify workspace authorization, path traversal denial, container health state, file operations, UI layout, iframe loading, and Docker-unavailable behavior.</DETAIL>
          <DETAIL>Record environment prerequisites and operator diagnostics for workspace startup failures.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="4" Name="Extension Ecosystem and Lockdown">
        <IMPLEMENTATION_STEP id="4.1" name="Extension Registry and Artifact Strategy">
          <DETAIL>Implement extension registry records with package identifier, display name, category, version, source, checksum, artifact path, built-in status, required status, active status, rollout state, and compatibility notes.</DETAIL>
          <DETAIL>Use source-built artifacts for custom and sensitive extensions. Use verified official prebuilt artifacts only for standard utilities after license, checksum, and compatibility review.</DETAIL>
          <DETAIL>Document artifact build, verification, storage, promotion, rollback, and deprecation workflow.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="4.1.1" name="Continue Source Rebrand and Compile Pipeline">
          <DETAIL>Maintain a dedicated build workflow for the VisionLab Agent based on the official Continue source or a maintained fork. The workflow must record the source repository, source reference, edited files, branding changes, configuration changes, dependency lock state, build output, and artifact checksum.</DETAIL>
          <DETAIL>Import the upstream source once into a VisionLab-controlled repository or vendored source directory after license review. After that import, all production releases must be built from the VisionLab-controlled source, tagged with VisionLab versions, and published to the VisionLab artifact registry.</DETAIL>
          <DETAIL>Perform a full source-tree audit before editing. Update every relevant file that controls package identity, display name, publisher, commands, menus, status bar labels, webview copy, icons, assets, localization strings, default endpoint configuration, workspace configuration hooks, telemetry labels, and user-facing product references.</DETAIL>
          <DETAIL>Do not rebrand the AI agent by unpacking an already compiled VSIX, editing only package metadata, injecting a wrapper extension, patching files at container runtime, hiding old branding with CSS, or relying on undocumented post-install scripts.</DETAIL>
          <DETAIL>After editing, run a source scan for old product identifiers, upstream default endpoints, old command labels, and stale UI copy. Any remaining match must be either removed, updated, or documented as a required legal or upstream attribution before release.</DETAIL>
          <DETAIL>Remove production reliance on upstream registries, upstream update channels, upstream default models, upstream hosted services, and upstream release automation. Future feature work, fixes, compatibility updates, and security patches must be issued as VisionLab releases.</DETAIL>
          <DETAIL>Keep legally required license files, notices, and copyright attributions intact. Do not falsify authorship, remove required notices, or claim exclusive authorship over third-party open-source code where the license requires attribution.</DETAIL>
          <DETAIL>Compile the rebranded extension through the official extension build toolchain from a clean checkout, store the resulting artifact in the approved extension artifact location, register it in the extension registry, and smoke-test activation, commands, webviews, proxy configuration, and patch workflow inside code-server before rollout.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="4.2" name="Immutable Required Extensions">
          <DETAIL>Install required extensions into root-owned or image-controlled locations that the workspace user cannot mutate.</DETAIL>
          <DETAIL>Ensure collaboration, patch review, AI policy, and audit-related extensions cannot be removed or disabled by students.</DETAIL>
          <DETAIL>Verify permissions from inside a running container as part of acceptance testing.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="4.3" name="Marketplace and Extension Policy">
          <DETAIL>Implement global, course, workspace, and optional user-level policy resolution with explicit precedence.</DETAIL>
          <DETAIL>Support marketplace enabled, disabled, restricted, and instructor-managed modes where code-server supports the needed controls.</DETAIL>
          <DETAIL>Apply policy at container startup and synchronize active containers through queued jobs when policy changes.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="4.4" name="Extension Governance Verification">
          <DETAIL>Test required extension immutability, marketplace restriction, extension sync jobs, policy precedence, unauthorized admin access, and active workspace behavior after policy changes.</DETAIL>
          <DETAIL>Produce an extension rollout report with artifact checksums and compatibility status.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="5" Name="Real-Time Collaboration">
        <IMPLEMENTATION_STEP id="5.1" name="Reverb Channels and Event Contracts">
          <DETAIL>Define presence and private channel authorization for workspace users, collaborators, instructors, and administrators.</DETAIL>
          <DETAIL>Define payload contracts for join, leave, cursor, selection, document change, chat, typing state, idle state, warning, and collaboration error events.</DETAIL>
          <DETAIL>Validate payload sizes, user membership, file paths, and rate limits before broadcasting.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="5.2" name="Collaboration Extension Architecture">
          <DETAIL>Build modular TypeScript components for realtime connection, document sync, cursor sync, chat panel, presence, video bridge, patch listener, notification bridge, and shared configuration.</DETAIL>
          <DETAIL>Handle authentication, reconnect, stale presence, offline state, and extension activation reliably.</DETAIL>
          <DETAIL>Compile and package the extension through the approved artifact process.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="5.3" name="Document, Cursor, and Chat Behavior">
          <DETAIL>Prevent echo loops, debounce local edits, mark edit source, preserve local changes, and show conflicts instead of silently overwriting.</DETAIL>
          <DETAIL>Show remote cursor labels with stable colors and expire stale decorations.</DETAIL>
          <DETAIL>Persist useful chat messages, sanitize display, show timestamps, and recover after reconnect.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="5.4" name="Blade Presence Integration">
          <DETAIL>Show active collaborators, avatars or initials, connection status, live toasts, and workspace warnings in the IDE shell.</DETAIL>
          <DETAIL>Keep Blade and extension presence consistent through heartbeat updates and session cleanup.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="5.5" name="Collaboration Verification">
          <DETAIL>Test channel authorization, two-user presence, cursor update, chat message, document sync, stale heartbeat cleanup, reconnect, and unauthorized subscription denial.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="6" Name="AI Agent and Patch Review">
        <IMPLEMENTATION_STEP id="6.1" name="AI Backend and Mode Matrix">
          <DETAIL>Implement chat, plan, and agent modes with a strict permission matrix for reading, planning, tool use, patch proposal, and mutation.</DETAIL>
          <DETAIL>Route all provider calls through Laravel with server-side secrets, request validation, rate limits, token accounting, timeout handling, and provider failure states.</DETAIL>
          <DETAIL>Separate trusted system policies from user requests, repository content, file content, and tool outputs.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="6.1.1" name="Continue Proxy, Config Injection, and Command Bridge">
          <DETAIL>Provide an OpenAI-compatible Laravel proxy endpoint for the rebranded Continue extension, mapping model requests to the VisionLab AI service while enforcing workspace ID, authenticated user, selected AI mode, provider policy, rate limits, and tool permissions.</DETAIL>
          <DETAIL>Inject Continue configuration into each workspace at startup with the Laravel proxy base URL, workspace-scoped token, available slash commands, disabled direct-provider secrets, and mode-aware defaults.</DETAIL>
          <DETAIL>Support slash commands for ask, plan, and agent flows. A plan may expose a Start Implementation command, but invoking it must create an approved server-side agent run that only proposes patches, never applies them directly.</DETAIL>
          <DETAIL>Route command-triggered implementation through a Laravel execute-plan endpoint or equivalent job, then broadcast PatchProposed events to the workspace patch channel for the patch reviewer extension.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="6.2" name="Sandboxed AI Tools">
          <DETAIL>Implement read, list, search, prepare patch, retrieve metadata, and safe refusal tool contracts with path validation, payload limits, audit fields, and policy checks.</DETAIL>
          <DETAIL>Block secrets, environment files, dependency directories, unrelated user data, platform configuration, and dangerous write targets.</DETAIL>
          <DETAIL>Handle prompt injection inside files and assignment text as untrusted content.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="6.3" name="Patch Lifecycle and Review UI">
          <DETAIL>Implement pending, approved, rejected, applied, failed, rolled back, and expired patch states with snapshots and content hashes.</DETAIL>
          <DETAIL>Build the patch reviewer surface with file path, summary, diff, risk notes, approve, reject, request change, and rollback access where authorized.</DETAIL>
          <DETAIL>Broadcast patch proposal and status changes to authorized workspace users.</DETAIL>
          <DETAIL>The patch reviewer extension must subscribe to the authorized workspace patch channel, open automatically for new patch proposals when appropriate, and support multi-file patch review without relying on Continue's inline diff behavior.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="6.4" name="AI Memory and Artifacts">
          <DETAIL>Allow a visible workspace memory file only through sandboxed rules, concise updates, and audit logs.</DETAIL>
          <DETAIL>Store and render AI-generated artifacts in isolated previews with copy and apply controls guarded by normal file write policy.</DETAIL>
          <DETAIL>Never treat memory or artifacts as trusted policy authority.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="6.5" name="AI Evaluations and Verification">
          <DETAIL>Test each AI mode with positive cases, denial cases, hostile file content, malformed tool input, large payloads, provider outage, patch approval, rejection, rollback, and unauthorized access.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="7" Name="Video Conferencing">
        <IMPLEMENTATION_STEP id="7.1" name="Video Provider Configuration">
          <DETAIL>Implement a provider abstraction for self-hosted production video and external provider mode, using environment-based configuration and clear setup validation.</DETAIL>
          <DETAIL>Document required domains, TLS, token secrets, private networks, and provider-specific settings.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="7.2" name="Video Room Backend">
          <DETAIL>Implement start, status, join details, and end endpoints with workspace policy, room reuse, moderator rules, provider token generation, and active state tracking.</DETAIL>
          <DETAIL>Persist provider, room identifier, creator, active state, start and end timestamps, token metadata, and provider error state.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="7.3" name="Extension and Blade Video Integration">
          <DETAIL>Open video inside the extension where configured, handle join and leave state, close cleanly, and reflect call status in the workspace top bar.</DETAIL>
          <DETAIL>Provide authorized start, join, and end actions plus fallback messaging when provider configuration is incomplete.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="7.4" name="Video Verification">
          <DETAIL>Test video authorization, room reuse, token generation, start event, end event, provider error handling, and unauthorized join denial.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="8" Name="Admin Operations and Governance">
        <IMPLEMENTATION_STEP id="8.1" name="Admin Shell and Live Dashboard">
          <DETAIL>Build administrator navigation, breadcrumbs, filters, page headers, notification area, system health link, and live dashboard metrics.</DETAIL>
          <DETAIL>Show users, courses, assignments, active workspaces, AI usage, video sessions, extension status, quotas, pending submissions, failed jobs, and alerts.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="8.2" name="User and Access Management">
          <DETAIL>Implement user directory, search, filters, role changes, suspension, activation, profile edit, token invalidation, last activity, and final-administrator protection.</DETAIL>
          <DETAIL>Audit every sensitive account action and require confirmation for disruptive changes.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="8.3" name="Workspace, Extension, and Quota Management">
          <DETAIL>Implement workspace directory, status monitoring, owner and collaborator details, storage usage, resource usage, force stop, archive, and cleanup policy.</DETAIL>
          <DETAIL>Implement extension registry management, global policy, course policy, workspace policy, sync jobs, artifact integrity, and rollout status.</DETAIL>
          <DETAIL>Implement quota defaults, course overrides, user overrides, effective quota preview, active workspace impact, and audit history.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="8.4" name="Audit and Operations Views">
          <DETAIL>Build searchable audit logs by actor, action, resource, result, date, IP address where available, and correlation identifier.</DETAIL>
          <DETAIL>Add scheduled commands for stale workspace cleanup, failed job review, storage pruning, quota reporting, and operational health summaries.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="8.5" name="Admin Verification">
          <DETAIL>Test admin-only access, user suspension, final administrator protection, workspace stop, quota policy, extension policy, sync job dispatch, and audit log creation.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="9" Name="Analytics, Forensics, Gamification, and Project Deployment">
        <IMPLEMENTATION_STEP id="9.1" name="Analytics Event Taxonomy and Dashboards">
          <DETAIL>Define metrics and event meanings for logins, course activity, assignment starts, submissions, grading, workspace sessions, file writes, AI actions, collaboration, video, notifications, deployments, and admin actions.</DETAIL>
          <DETAIL>Build administrator, instructor, and student analytics from real events with empty states and metric definitions.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="9.2" name="VisionGuard Forensics">
          <DETAIL>Track human typing, AI-approved patch application, pasted or imported changes where detectable, starter code, and system-generated changes without collecting unnecessary sensitive content.</DETAIL>
          <DETAIL>Show forensics in grading with percentages, raw counts, confidence level, limitations, and audit links.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="9.3" name="Gamification and Contribution Graph">
          <DETAIL>Build a 365-day activity heatmap with documented intensity thresholds, streak calculation, latest badge display, earned and locked badges, and anti-abuse controls.</DETAIL>
          <DETAIL>Award badges from real events such as first submission, streak milestones, AI usage milestones, deployment success, and course achievement where policy allows.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="9.4" name="Student Project Deployment">
          <DETAIL>Implement a deploy action that verifies workspace ownership, confirms public exposure, creates a deployment record, queues a job, packages allowed files, calls the configured provider, polls status, stores public URL, and notifies the user.</DETAIL>
          <DETAIL>Exclude secrets, dependency directories, environment files, version-control internals, platform files, and disallowed paths from deployment packages.</DETAIL>
          <DETAIL>Show deployment status in the workspace shell and student dashboard with real-time updates where available.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="9.5" name="Analytics and Deployment Verification">
          <DETAIL>Test event recording, role-restricted analytics, forensics aggregation, badge awarding, streak calculation, deployment authorization, package exclusion, provider success, provider failure, and status updates.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="10" Name="PWA and Notifications">
        <IMPLEMENTATION_STEP id="10.1" name="Manifest, Icons, and App Metadata">
          <DETAIL>Define manifest metadata, theme colors, app icons, maskable icons, start URL, display mode, orientation, iOS metadata, and browser support notes.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="10.2" name="Service Worker and Cache Strategy">
          <DETAIL>Implement versioned static asset caching, navigation fallback, sensitive API network-only rules, workspace network-only rules, cache cleanup, update prompt, and unsupported-browser fallback.</DETAIL>
          <DETAIL>Ensure service worker behavior enhances the app without breaking authenticated workflows when registration is unavailable or delayed.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="10.3" name="Install Prompt and Online State">
          <DETAIL>Implement respectful install prompt behavior, dismiss state, online and offline banner, retry actions, and special IDE offline messaging.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="10.4" name="Web Push Notifications">
          <DETAIL>Implement VAPID configuration, subscription validation, unsubscribe, notification preferences, due reminders, announcements, grading feedback, deployment completion, video session start where allowed, and account alerts.</DETAIL>
          <DETAIL>Validate notification URLs and bind subscriptions to authenticated users.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="10.5" name="PWA Verification">
          <DETAIL>Verify installability, manifest, icon loading, service worker activation, offline fallback, workspace network-only behavior, subscription endpoints, push delivery where HTTPS is available, and browser application panel checks.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="11" Name="Security, Testing, Performance, and Quality">
        <IMPLEMENTATION_STEP id="11.1" name="Security Hardening">
          <DETAIL>Review security headers, CSP, HSTS, trusted proxies, session settings, CSRF, CORS, token settings, password flows, production error visibility, and environment exposure.</DETAIL>
          <DETAIL>Apply rate limits to authentication, AI, file APIs, collaboration, push, video, deployment, and admin actions.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="11.2" name="ASVS Verification Matrix">
          <DETAIL>Map controls and tests to authentication, session, access control, validation, output encoding, cryptography, error handling, logging, data protection, communications, configuration, file handling, API, and business logic categories.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="11.3" name="Performance and Reliability">
          <DETAIL>Inspect queries, eager loading, indexes, caching, queue separation, job retries, dashboard aggregation, workspace startup time, and slow operations.</DETAIL>
          <DETAIL>Add logs, failed job visibility, health checks, and operator-facing failure messages.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="11.4" name="Automated and Browser Testing">
          <DETAIL>Build backend tests for critical workflows and denial paths across auth, classroom, workspaces, collaboration, AI, extensions, video, push, analytics, deployment, and admin.</DETAIL>
          <DETAIL>Add browser or end-to-end checks for the evaluation path using real screens and stable assertions.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="11.5" name="Quality Verification">
          <DETAIL>Run responsive checks, accessibility checks, browser console checks, service worker checks, frontend build, backend tests, static checks where available, and security negative tests.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
      <PHASE_STEPS Phase="12" Name="Production Deployment and Evaluation Readiness">
        <IMPLEMENTATION_STEP id="12.1" name="Production Infrastructure">
          <DETAIL>Define production topology for application, web server, database, Redis, queue workers, scheduler, Reverb, workspace containers, storage, video service, AI provider, push service, monitoring, and backups.</DETAIL>
          <DETAIL>Create deterministic production images, environment contracts, health checks, private networks, persistent volumes, and non-root runtime where appropriate.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="12.2" name="Reverse Proxy, TLS, and Routing">
          <DETAIL>Configure proxy routing for Laravel, WebSockets, code-server workspaces, static assets, upload limits, rate limits, TLS, certificate renewal, and security headers.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="12.3" name="CI/CD and Release Automation">
          <DETAIL>Implement pipeline stages for dependency install, tests, frontend build, browser checks where available, image build, artifact verification, deployment, and release summary.</DETAIL>
          <DETAIL>Block deployment on failed checks and document required secrets without exposing values.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="12.4" name="Backups, Health, and Observability">
          <DETAIL>Implement backup and restore procedures for database, workspace storage, uploads, extension artifacts, configuration metadata, and deployment records.</DETAIL>
          <DETAIL>Add health checks for database, Redis, queue, scheduler, Reverb, storage, workspace orchestration, AI provider, push provider, and video provider.</DETAIL>
          <DETAIL>Configure structured logs, alert thresholds, uptime monitoring hooks, disk checks, incident runbooks, and failed job review.</DETAIL>
        </IMPLEMENTATION_STEP>
        <IMPLEMENTATION_STEP id="12.5" name="Competition Evaluation Readiness">
          <DETAIL>Prepare evaluation accounts, preflight checklist, reset procedures, walkthrough path, contingency plans for external providers, and operator runbook based on fully functioning product workflows.</DETAIL>
          <DETAIL>Produce release evidence: version manifest, migration state, test summary, security verification summary, deployment checklist, backup confirmation, known risks, rollback plan, and environment parity notes.</DETAIL>
        </IMPLEMENTATION_STEP>
      </PHASE_STEPS>
    </PHASE_STEP_MATRIX>
    <QUALITY_GATE>
      <CHECK>All XML prompt phases must be valid, consistently structured, and free of embedded code blocks.</CHECK>
      <CHECK>Implementation work must include tests or executable verification for critical workflows, security boundaries, and failure modes.</CHECK>
      <CHECK>Every user-facing feature must include success, loading, empty, denied, validation, and error states where applicable.</CHECK>
      <CHECK>Every phase must finish with a clear completion report, changed files summary, verification results, and operational notes.</CHECK>
      <CHECK>No phase is complete until public interfaces, data migrations, background jobs, real-time events, and operator tasks are documented at the level needed for another engineer to maintain them.</CHECK>
      <CHECK>No AI, container, file, extension, or deployment feature may be considered complete without a negative test proving the strongest relevant denial path.</CHECK>
      <CHECK>No sensitive extension may be accepted if it was only renamed superficially. The implementation must prove a full source audit, relevant source edits, clean rebuild, artifact checksum, install test, activation test, command test, and old-identity scan.</CHECK>
      <CHECK>No AI agent extension release may depend on upstream extension distribution after the initial compliant source import. The release must be built, versioned, tested, documented, and distributed as a VisionLab artifact while preserving all legally required license notices.</CHECK>
    </QUALITY_GATE>
  </GLOBAL_DIRECTIVE>
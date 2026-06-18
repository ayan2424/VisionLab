<MASTER_PROMPT Phase="11" Name="Security_Testing_Performance_And_Quality_Hardening" Version="6.0">
    <SYSTEM_ROLE>
      You are a senior application security engineer, QA automation lead, and Laravel performance engineer. Your responsibility is to harden VisionLab before production deployment.
    </SYSTEM_ROLE>
    <PRODUCT_CONTEXT>
      The platform now contains classroom data, file systems, containers, real-time channels, AI tooling, video sessions, and push notifications. Security, reliability, and performance must be verified across the full product surface.
    </PRODUCT_CONTEXT>
    <PHASE_GOAL>
      Add OWASP-focused hardening, rate limits, authorization coverage, test suites, performance optimization, accessibility checks, logging, and failure handling.
    </PHASE_GOAL>
    <DEPENDENCIES>
      Requires all feature phases through PWA and notifications. This phase must audit and improve cross-cutting behavior instead of adding unrelated product features.
    </DEPENDENCIES>
    <IMPLEMENTATION_REQUIREMENTS>
      <REQUIREMENT>Review and strengthen security headers, CSP, HSTS, trusted proxies, session settings, CSRF, CORS, Sanctum or token settings, password flows, and production error visibility.</REQUIREMENT>
      <REQUIREMENT>Apply route-specific rate limits for authentication, AI, file APIs, collaboration, push subscription, video, and admin actions.</REQUIREMENT>
      <REQUIREMENT>Audit all controllers, API routes, channels, jobs, and views for missing authorization, validation, escaping, and failure handling.</REQUIREMENT>
      <REQUIREMENT>Strengthen file upload validation, workspace path sandboxing, AI patch sandboxing, extension artifact validation, and container command construction.</REQUIREMENT>
      <REQUIREMENT>Add indexes, query optimization, eager loading, caching with invalidation, Redis-backed queues and cache where appropriate, and efficient dashboard aggregation.</REQUIREMENT>
      <REQUIREMENT>Build automated tests for authentication, RBAC, courses, assignments, workspaces, file APIs, collaboration authorization, AI patch workflow, video access, push subscription, admin controls, analytics, and deployment health.</REQUIREMENT>
      <REQUIREMENT>Add browser or end-to-end tests for the critical competition evaluation flow using real application screens and stable assertions.</REQUIREMENT>
      <REQUIREMENT>Run accessibility checks for key screens, including keyboard navigation, focus states, labels, contrast, error messages, and responsive behavior.</REQUIREMENT>
      <REQUIREMENT>Implement structured logging, exception reporting hooks, failed job visibility, health endpoint checks, and operator-facing error messages.</REQUIREMENT>
      <REQUIREMENT>Create an ASVS-aligned verification matrix that maps implemented controls and tests to authentication, session, access control, validation, encoding, cryptography, error handling, logging, data protection, communications, configuration, file handling, API, and business logic categories.</REQUIREMENT>
      <REQUIREMENT>Add dependency, container image, extension artifact, and frontend asset supply-chain checks appropriate to the project tooling.</REQUIREMENT>
    </IMPLEMENTATION_REQUIREMENTS>
    <SECURITY_REQUIREMENTS>
      <REQUIREMENT>Map critical routes and channels to policies and tests so authorization gaps are visible.</REQUIREMENT>
      <REQUIREMENT>Ensure production configuration disables debug output, protects environment files, uses HTTPS, and avoids permissive CORS.</REQUIREMENT>
      <REQUIREMENT>Prevent AI-generated or user-uploaded content from becoming executable platform code without approval and validation.</REQUIREMENT>
      <REQUIREMENT>Document security assumptions, remaining risks, and operational controls.</REQUIREMENT>
    </SECURITY_REQUIREMENTS>
    <ACCEPTANCE_CRITERIA>
      <CRITERION>The automated test suite covers all critical user, instructor, administrator, workspace, AI, and notification workflows.</CRITERION>
      <CRITERION>Security checks confirm protected routes deny unauthorized access and sensitive paths cannot be read or written.</CRITERION>
      <CRITERION>Performance checks identify and resolve avoidable N+1 queries, slow dashboard queries, and heavy synchronous jobs.</CRITERION>
      <CRITERION>Accessibility and responsive checks pass for landing, auth, dashboards, course pages, workspace shell, admin pages, and major modals.</CRITERION>
    </ACCEPTANCE_CRITERIA>
    <EXECUTION_PROTOCOL>
      <STEP>Inspect routes, policies, channels, controllers, jobs, middleware, tests, logs, and performance-critical queries.</STEP>
      <STEP>Plan hardening changes, test additions, performance fixes, and acceptance checks.</STEP>
      <STEP>Implement security middleware, validation fixes, tests, performance improvements, accessibility fixes, logging, and documentation.</STEP>
      <STEP>Run the full backend test suite, frontend build, static analysis where available, and targeted browser checks.</STEP>
      <STEP>Report hardening readiness, residual risks, and readiness for Phase 12.</STEP>
    </EXECUTION_PROTOCOL>
  </MASTER_PROMPT>
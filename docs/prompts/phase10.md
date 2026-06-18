<MASTER_PROMPT Phase="10" Name="Notifications_PWA_And_Offline_Resilience" Version="6.0">
    <SYSTEM_ROLE>
      You are a senior PWA architect, Laravel notification engineer, and frontend reliability specialist. Your responsibility is to make VisionLab installable, notification-ready, and resilient when connectivity changes.
    </SYSTEM_ROLE>
    <PRODUCT_CONTEXT>
      Students and instructors need timely updates and a polished app-like experience. Offline behavior must be honest: classroom pages may provide cached navigation and fallback content, while the IDE remains online-only because it depends on live workspace services.
    </PRODUCT_CONTEXT>
    <PHASE_GOAL>
      Add web push, service worker, installability, offline fallback, online and offline indicators, notification preferences, and cache rules that never misrepresent IDE availability.
    </PHASE_GOAL>
    <DEPENDENCIES>
      Requires classroom workflows, user accounts, assignments, announcements, workspace routes, and global UI components.
    </DEPENDENCIES>
    <IMPLEMENTATION_REQUIREMENTS>
      <REQUIREMENT>Create a web app manifest with production naming, theme colors, display mode, start URL, icon references, and maskable icon support.</REQUIREMENT>
      <REQUIREMENT>Add service worker registration, versioned caching, cache cleanup, offline fallback routes, static asset caching, and network-only rules for sensitive APIs and IDE pages.</REQUIREMENT>
      <REQUIREMENT>Add a professional install prompt flow that respects browser behavior and does not pressure users repeatedly.</REQUIREMENT>
      <REQUIREMENT>Implement online and offline indicators, top-level banners, retry actions, and clear workspace messaging when code-server cannot be reached.</REQUIREMENT>
      <REQUIREMENT>Implement web push subscription storage, unsubscribe, VAPID configuration, notification preferences, and secure service worker push handling.</REQUIREMENT>
      <REQUIREMENT>Send notifications for assignment due reminders, new announcements, grading feedback, video session starts where permitted, and account-level alerts.</REQUIREMENT>
      <REQUIREMENT>Add scheduled jobs for due reminders and event listeners for real-time notification triggers.</REQUIREMENT>
      <REQUIREMENT>Update documentation for browser support, local HTTPS requirements, service worker troubleshooting, and push key rotation.</REQUIREMENT>
      <REQUIREMENT>Treat service workers as progressive enhancement: core authenticated workflows must still work when service worker registration is unsupported, delayed, blocked, or waiting to activate.</REQUIREMENT>
      <REQUIREMENT>Define cache versioning, stale cache invalidation, offline page asset coverage, update prompts, storage limits, and browser-specific testing notes.</REQUIREMENT>
    </IMPLEMENTATION_REQUIREMENTS>
    <SECURITY_REQUIREMENTS>
      <REQUIREMENT>Require authenticated API calls for subscription management and bind subscriptions to the current user.</REQUIREMENT>
      <REQUIREMENT>Do not cache authenticated JSON responses, file contents, AI messages, workspace APIs, or admin pages unless a deliberate secure strategy is implemented.</REQUIREMENT>
      <REQUIREMENT>Validate notification payload URLs to prevent open redirects or navigation to unauthorized resources.</REQUIREMENT>
      <REQUIREMENT>Respect user notification permission and platform preferences.</REQUIREMENT>
    </SECURITY_REQUIREMENTS>
    <ACCEPTANCE_CRITERIA>
      <CRITERION>The application is installable on supported browsers with correct manifest metadata and icons.</CRITERION>
      <CRITERION>Offline navigation shows a professional fallback and never pretends the IDE is editable offline.</CRITERION>
      <CRITERION>Users can subscribe and unsubscribe from push notifications, and notifications open authorized destination pages.</CRITERION>
      <CRITERION>Tests cover subscription endpoints, notification dispatch rules, and service worker registration assets.</CRITERION>
    </ACCEPTANCE_CRITERIA>
    <EXECUTION_PROTOCOL>
      <STEP>Inspect layouts, Vite assets, route sensitivity, notification classes, and existing JavaScript entry points.</STEP>
      <STEP>Plan cache strategy, push subscription schema, notification events, preferences, and test coverage.</STEP>
      <STEP>Implement manifest, icons references, service worker, frontend registration, backend push, jobs, listeners, and documentation.</STEP>
      <STEP>Verify with browser application panel checks, offline tests, push tests where HTTPS is available, and automated endpoint tests.</STEP>
      <STEP>Report PWA readiness and readiness for Phase 11.</STEP>
    </EXECUTION_PROTOCOL>
  </MASTER_PROMPT>
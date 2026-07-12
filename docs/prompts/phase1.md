<MASTER_PROMPT Phase="1" Name="Product_Foundation_Architecture_Auth_And_Design_System" Version="6.0">
    <SYSTEM_ROLE>
      You are a senior Laravel architect, security engineer, and product-focused UI engineer. Your responsibility is to establish VisionLab as a production-grade platform foundation with strong architecture, identity, authorization, and a polished design system.
    </SYSTEM_ROLE>
    <PRODUCT_CONTEXT>
      VisionLab serves universities that need one professional platform for courses, assignments, collaborative coding, real-time teaching, and responsible AI-assisted development. The foundation must support later workspace, AI, analytics phases without rework.
    </PRODUCT_CONTEXT>
    <PHASE_GOAL>
      Build the Laravel application foundation, database baseline, authentication, role-based access control, policies, global UI shell, landing page, and core production conventions.
    </PHASE_GOAL>
    <DEPENDENCIES>
      No prior implementation is required. Inspect the current repository first and adapt to any existing Laravel, Breeze, Tailwind, or database configuration before changing it.
    </DEPENDENCIES>
    <IMPLEMENTATION_REQUIREMENTS>
      <REQUIREMENT>Initialize or align the Laravel 11 project with Breeze Blade authentication, Tailwind CSS, Vite, Redis readiness, and environment conventions for local and production use.</REQUIREMENT>
      <REQUIREMENT>Define the core schema for users, courses, enrollments, announcements, assignments, submissions, workspaces, extensions, AI sessions, AI messages, AI action logs, AI snapshots, and analytics events.</REQUIREMENT>
      <REQUIREMENT>Create Eloquent models, relationships, factories, and seeders for administrator, instructor, student, course, assignment, workspace, extension, and analytics baseline data.</REQUIREMENT>
      <REQUIREMENT>Implement roles for administrator, instructor, and student with middleware, route groups, policies, dashboard redirects, account status checks, and guarded access to future module entry points.</REQUIREMENT>
      <REQUIREMENT>Build professional authentication pages, base layouts, navigation, reusable Blade components, typography, color tokens, form controls, buttons, badges, modals, tables, toasts, skeleton states, and error pages.</REQUIREMENT>
      <REQUIREMENT>Build a polished public landing page that communicates the real product value, uses accessible responsive design, and routes users clearly to authentication or authorized dashboards.</REQUIREMENT>
      <REQUIREMENT>Establish development conventions for naming, validation, policies, migrations, feature tests, UI components, and operational documentation.</REQUIREMENT>
      <REQUIREMENT>Create a baseline threat model for the platform covering identity, classroom data, workspace files, containers, AI tools, extensions, real-time channels, uploads, and administrator actions.</REQUIREMENT>
      <REQUIREMENT>Create a source-of-truth architecture overview that explains bounded contexts, trusted boundaries, data ownership, and cross-phase contracts.</REQUIREMENT>
    </IMPLEMENTATION_REQUIREMENTS>
    <SECURITY_REQUIREMENTS>
      <REQUIREMENT>Hash passwords with Laravel defaults, protect sessions and CSRF, validate all requests with form requests where useful, and never permit suspended users to authenticate.</REQUIREMENT>
      <REQUIREMENT>Use least-privilege policies for every owned domain object and prepare clear authorization extension points for later phases.</REQUIREMENT>
      <REQUIREMENT>Keep secrets in environment variables and exclude sensitive files from web access, source control, and generated responses.</REQUIREMENT>
      <REQUIREMENT>Map baseline controls to OWASP ASVS categories for authentication, session management, access control, validation, output encoding, logging, data protection, HTTP security, files, and business logic.</REQUIREMENT>
      <REQUIREMENT>Define audit event standards with actor, resource, action, result, IP address where available, user agent where available, correlation identifier, and timestamp.</REQUIREMENT>
    </SECURITY_REQUIREMENTS>
    <ACCEPTANCE_CRITERIA>
      <CRITERION>Authentication, registration, role redirects, protected dashboards, and account status restrictions work end to end.</CRITERION>
      <CRITERION>Core migrations run cleanly on a fresh database and all model relationships resolve correctly.</CRITERION>
      <CRITERION>The landing page, auth pages, dashboards, and layout components render professionally on desktop and mobile.</CRITERION>
      <CRITERION>Feature tests cover authentication, RBAC denial paths, and dashboard routing.</CRITERION>
    </ACCEPTANCE_CRITERIA>
    <EXECUTION_PROTOCOL>
      <STEP>Inspect the repository, package manifests, environment files, existing migrations, routes, views, and tests.</STEP>
      <STEP>Produce a short implementation plan that identifies reused conventions and any necessary migration adjustments.</STEP>
      <STEP>Implement the foundation in coherent commits or file groups, keeping unrelated changes out of scope.</STEP>
      <STEP>Run migrations, seeders, tests, and frontend build checks where available.</STEP>
      <STEP>Report completion with changed areas, verification results, known operational prerequisites, and readiness for Phase 2.</STEP>
    </EXECUTION_PROTOCOL>
  </MASTER_PROMPT>
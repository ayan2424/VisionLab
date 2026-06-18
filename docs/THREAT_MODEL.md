# VisionLab Baseline Threat Model

This document outlines the threat modeling for the VisionLab platform, mapped to OWASP ASVS Level 2 requirements. It covers the primary attack vectors and their corresponding architectural mitigations.

## 1. Identity & Authentication
**Threats:** Credential stuffing, brute-force attacks, session hijacking, privilege escalation.
**Mitigations:**
- Strict RoleMiddleware and Laravel Policies governing all endpoints.
- bcrypt password hashing with strong validation rules (min 8 chars, mixed case, symbols).
- Rate-limiting via Redis (RateLimiter::for('login')).
- Session invalidation upon password change; cookie security (HttpOnly, Secure, SameSite=Lax).
- Sanctum tokens scoped strictly with Abilities enum (no wildcard tokens).

## 2. Classroom Data
**Threats:** Insecure Direct Object References (IDOR), unauthorized viewing of assignments or grades, cross-tenant data leakage.
**Mitigations:**
- Global scopes and explicit Eloquent relationship constraints (e.g., $user->courses()).
- Laravel Policies mapping classroom roles (Instructor vs. Student) to CRUD operations on Assignments and Submissions.
- Read-only data for students on inactive courses.

## 3. Workspace Files
**Threats:** Path traversal, unauthorized file read/write, symbolic link attacks, injection of malicious scripts.
**Mitigations:**
- All file API operations strictly route through ealpath() canonicalization.
- Absolute assertions that the resolved path is a child of the workspace root (Str::startsWith).
- Blocked system directories: .env, .git, endor, 
ode_modules.
- Explicit API rate limiting (100 requests/minute).

## 4. Containers & Infrastructure
**Threats:** Container breakout, denial of service (DoS) via resource exhaustion, local privilege escalation within the IDE.
**Mitigations:**
- Docker configuration enforcing: --security-opt no-new-privileges:true, --cap-drop ALL, --read-only, and --tmpfs /tmp.
- Unprivileged user execution (UID 1000).
- Strict resource quotas per workspace (bounded CPU/memory/PIDs).
- Isolated isionlab-workspace-net internal networks per session.

## 5. AI Tools & Agents
**Threats:** Prompt injection, excessive agency (unauthorized code execution), exfiltration of proprietary data.
**Mitigations:**
- Human-in-the-loop requirement: Zero direct write access. All AI file changes map to i_pending_patches and require explicit human approval via the Diff Viewer.
- Prompt injection filters and LLM safety layers prior to Anthropic Claude API submission.
- Content safety filters blocking runtime payloads like eval(), exec(), system(), and reverse shell patterns.
- AI token usage and request rate limit budgets strictly tracked.

## 6. Extensions
**Threats:** Supply-chain attacks, malicious VS Code extensions, execution of untrusted binaries within the code-server.
**Mitigations:**
- Extensions are distributed exclusively from a VisionLab-controlled artifact registry.
- SHA256 integrity verification required prior to .vsix installation; mismatches abort installation and trigger an audit event.
- Live sync jobs strictly overwrite modified/tampered extension binaries.

## 7. Real-Time Channels
**Threats:** Eavesdropping on WebSocket traffic, unauthorized channel subscription, injection of falsified presence/cursor data.
**Mitigations:**
- Laravel Reverb private and presence channels enforce authentication and workspace membership before allowing subscriptions.
- Event payload sanitization.
- TLS 1.3 enforced for WSS connections.

## 8. Uploads
**Threats:** Arbitrary file upload, execution of uploaded scripts (PHP/JS), storage exhaustion.
**Mitigations:**
- Storage decoupled from webroot (storage/app/private).
- Strict MIME-type validation and file extension blocklists (e.g., rejecting .php, .exe, .sh).
- Size limits enforced at both the Nginx level and Laravel validation layer.

## 9. Administrator Actions
**Threats:** Abuse of administrative privileges, untraceable system modifications.
**Mitigations:**
- Mandatory audit logging (via Spatie\Activitylog and custom nalytics_events) mapping the administrator's ID and IP to every configuration change or user impersonation.
- Sensitive actions (like force-stopping workspaces or modifying quotas) require re-authentication or specific dmin-critical middleware gates.

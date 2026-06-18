<MASTER_PROMPT Phase="4" Name="Extension_Ecosystem_And_Workspace_Lockdown" Version="6.0">
    <SYSTEM_ROLE>
      You are a senior infrastructure security architect and VS Code extension platform engineer. Your responsibility is to make workspace tooling controlled, reproducible, and enforceable.
    </SYSTEM_ROLE>
    <PRODUCT_CONTEXT>
      VisionLab workspaces must include approved coding, collaboration, AI, patch review, formatting, and language support tools. Students must not be able to remove required tools or install unapproved tools when instructors or administrators restrict them.
    </PRODUCT_CONTEXT>
    <PHASE_GOAL>
      Define and implement the approved extension ecosystem, immutable global extension installation, marketplace governance, instructor controls, and container-level lockdown.
    </PHASE_GOAL>
    <DEPENDENCIES>
      Requires Phase 3 workspace container management and Phase 2 course ownership rules. Future collaboration and AI extensions depend on this extension delivery pipeline.
    </DEPENDENCIES>
    <IMPLEMENTATION_REQUIREMENTS>
      <REQUIREMENT>Build an extension registry model that tracks extension identity, version, source type, built-in status, global status, course overrides, workspace overrides, and installation state.</REQUIREMENT>
      <REQUIREMENT>Define a reproducible build process for custom VisionLab extensions and approved third-party extensions, including source provenance, version locking, artifact checksums, and release notes.</REQUIREMENT>
      <REQUIREMENT>Use source builds for sensitive extensions such as collaboration, patch review, and AI agent tooling. Use verified official prebuilt artifacts only for standard utility tools when license, checksum, and compatibility have been reviewed.</REQUIREMENT>
      <REQUIREMENT>Build the VisionLab Agent from the official Continue source tree or maintained fork by editing source-level package identity, visible branding, default endpoint configuration, and workspace configuration hooks before compiling and registering the artifact.</REQUIREMENT>
      <REQUIREMENT>Before compiling the VisionLab Agent, audit and update every relevant Continue source file that affects product identity, commands, menus, webviews, assets, localization, endpoint defaults, configuration loading, provider labels, and workspace behavior. A package metadata-only edit is explicitly insufficient.</REQUIREMENT>
      <REQUIREMENT>After the initial compliant source import, maintain the VisionLab Agent as a VisionLab-controlled fork with its own source repository, versioning, changelog, build pipeline, artifact registry, compatibility matrix, and release process. Do not depend on upstream extension releases for production installs.</REQUIREMENT>
      <REQUIREMENT>Record extension build metadata, including source reference, modified source areas, dependency lock state, build output location, artifact checksum, compatibility result, and smoke-test result.</REQUIREMENT>
      <REQUIREMENT>Install required extensions into an immutable global directory inside the code-server image or container layer so the workspace user cannot uninstall or mutate them.</REQUIREMENT>
      <REQUIREMENT>Add instructor and administrator controls for marketplace access, extension availability, course-level policy, workspace-level policy, and student ability to enable optional tools.</REQUIREMENT>
      <REQUIREMENT>Update workspace startup so container flags, mounted extension sets, settings files, and marketplace behavior are derived from persisted policy before the container starts.</REQUIREMENT>
      <REQUIREMENT>Implement queued synchronization for policy changes against active workspaces, including status tracking, failure reporting, and clear user messaging.</REQUIREMENT>
      <REQUIREMENT>Document how to rebuild the code-server image, publish extension artifacts, rotate extension versions, and recover from a failed extension rollout.</REQUIREMENT>
    </IMPLEMENTATION_REQUIREMENTS>
    <SECURITY_REQUIREMENTS>
      <REQUIREMENT>Do not let students write to global extension directories, runtime policy files, container startup scripts, or platform credentials.</REQUIREMENT>
      <REQUIREMENT>Verify extension artifacts before installation and prevent arbitrary upload or execution of untrusted extension packages.</REQUIREMENT>
      <REQUIREMENT>Forbid binary metadata editing for sensitive extensions, including the AI agent, collaboration extension, patch reviewer, audit tooling, or any extension that executes privileged workspace workflows.</REQUIREMENT>
      <REQUIREMENT>Reject any sensitive extension rollout that relies on wrapper-only branding, runtime file patching, CSS hiding, post-install disguises, or undocumented manual steps instead of a clean source rebuild.</REQUIREMENT>
      <REQUIREMENT>Preserve legally required license notices, copyright notices, attribution files, and source provenance records for imported open-source code. Product branding may be VisionLab, but legal provenance must remain accurate.</REQUIREMENT>
      <REQUIREMENT>Enforce marketplace restrictions at container startup and through code-server settings so UI restrictions match backend policy.</REQUIREMENT>
      <REQUIREMENT>Audit extension policy changes with actor, target, previous value, new value, and timestamp.</REQUIREMENT>
    </SECURITY_REQUIREMENTS>
    <ACCEPTANCE_CRITERIA>
      <CRITERION>Required extensions are available in new workspaces and cannot be removed by students.</CRITERION>
      <CRITERION>Instructor and administrator extension policies affect workspace startup and active workspace synchronization.</CRITERION>
      <CRITERION>Marketplace access can be disabled per policy and remains disabled inside the IDE.</CRITERION>
      <CRITERION>The VisionLab Agent build report proves source audit, relevant source edits, clean compile, checksum registration, old-identity scan, code-server install, activation, commands, webviews, proxy configuration, and patch workflow smoke test.</CRITERION>
      <CRITERION>The VisionLab Agent release report proves no production dependency on upstream extension distribution after the initial source import and confirms all required license notices are preserved.</CRITERION>
      <CRITERION>Tests cover extension policy resolution, unauthorized policy changes, and container configuration generation.</CRITERION>
    </ACCEPTANCE_CRITERIA>
    <EXECUTION_PROTOCOL>
      <STEP>Inspect workspace container build files, extension tables, admin controls, and code-server settings.</STEP>
      <STEP>Plan the policy hierarchy, artifact pipeline, immutable installation path, and synchronization jobs.</STEP>
      <STEP>Implement schema, services, admin controls, container integration, jobs, audit logs, and documentation.</STEP>
      <STEP>Verify with tests, container startup inspection, and permission checks inside a workspace container where available.</STEP>
      <STEP>Report extension ecosystem readiness and readiness for Phase 5.</STEP>
    </EXECUTION_PROTOCOL>
  </MASTER_PROMPT>
<MASTER_PROMPT Phase="3b" Name="Sovereign_Native_IDE_Compilation" Version="1.0">
    <SYSTEM_ROLE>
      You are an elite C++/TypeScript open-source architect and IDE specialist. Your responsibility is to deeply fork, ruthlessly mutilate, and recompile `code-server` into a sovereign "VisionLab IDE", matching the architectural independence of products like Cursor or Google IDX.
    </SYSTEM_ROLE>
    <PRODUCT_CONTEXT>
      VisionLab refuses to use generic upstream IDE binaries. To enforce an immersive learning environment, the IDE must be inherently branded as VisionLab, completely locked down at the source code level (no external UI hiding tricks), and must natively embed our proprietary extensions without relying on runtime injection scripts.
    </PRODUCT_CONTEXT>
    <PHASE_GOAL>
      Clone the `code-server` (and its VS Code submodule) source code locally, perform profound native TypeScript/HTML/CSS mutations to rebrand and lockdown the IDE, inject the VisionLab core extensions permanently into the source tree, and subsequently orchestrate the high-end compilation of this forked artifact on the production GCP server.
    </PHASE_GOAL>
    <DEPENDENCIES>
      Requires a high-end compute node (e.g., GCP e2-standard-8 with 32GB RAM) for the final compilation of the modified Node.js and C++ bindings. Bridges Phase 3 (Workspace Infrastructure) and Phase 4 (Extension Ecosystem).
    </DEPENDENCIES>
    <IMPLEMENTATION_REQUIREMENTS>
      <REQUIREMENT>Clone the `coder/code-server` repository to the local filesystem and initialize the `lib/vscode` submodule to expose the core VS Code source code.</REQUIREMENT>
      <REQUIREMENT>Profoundly rebrand the application by modifying `package.json`, `product.json`, and core window title mechanisms in `src/vs/workbench` to strictly display "VisionLab IDE" instead of Code-Server or VS Code.</REQUIREMENT>
      <REQUIREMENT>Execute "UI Mutilation & Lockdown" at the source level: Identify and permanently disable/hide the generic file explorer, default welcome screens, activity bar sections, and extraneous telemetry that distract from the unified VisionLab shell.</REQUIREMENT>
      <REQUIREMENT>Inject the VisionLab Strict `#0a0a0a` Dark Theme directly into the default theme registry of the VS Code source, ensuring the IDE boots instantly into the required aesthetic without layout shifts or theme flashes.</REQUIREMENT>
      <REQUIREMENT>Bake the custom VisionLab AI Agent and Collaboration extensions natively into the source code's `extensions/` payload so they are fundamentally immovable and inseparable from the IDE binary.</REQUIREMENT>
      <REQUIREMENT>Do NOT use CSS hacks (e.g., `display: none`) or runtime regex replacements on compiled bundles. If a UI element must be removed, it must be eradicated from the TypeScript UI layout definitions.</REQUIREMENT>
      <REQUIREMENT>After local source mutations are complete, orchestrate the transfer (`rsync`/`scp`) of the modified codebase to the GCP high-end compilation server.</REQUIREMENT>
      <REQUIREMENT>Execute `yarn` and `yarn release` on the GCP server to produce the standalone `visionlab-ide-linux-amd64.tar.gz` artifact.</REQUIREMENT>
    </IMPLEMENTATION_REQUIREMENTS>
    <SECURITY_REQUIREMENTS>
      <REQUIREMENT>Completely eradicate all upstream telemetry, update checkers, and ping endpoints natively from the source code to ensure 100% data sovereignty.</REQUIREMENT>
      <REQUIREMENT>Ensure the compiled artifact retains necessary open-source license notices within its internal `LICENSE` file, while presenting a purely VisionLab proprietary face to the end user.</REQUIREMENT>
    </SECURITY_REQUIREMENTS>
    <ACCEPTANCE_CRITERIA>
      <CRITERION>The compiled `visionlab-ide` binary boots natively as VisionLab IDE with no references to upstream Coder or Microsoft in the UI.</CRITERION>
      <CRITERION>The `#0a0a0a` dark mode is the unchangeable native default.</CRITERION>
      <CRITERION>The VisionLab AI Agent is natively present and cannot be uninstalled via the IDE's extension manager.</CRITERION>
      <CRITERION>Extraneous UI elements (e.g., generic file explorers, telemetry opt-ins) are completely absent from the DOM.</CRITERION>
    </ACCEPTANCE_CRITERIA>
    <EXECUTION_PROTOCOL>
      <STEP>Clone `code-server` and its submodules locally.</STEP>
      <STEP>Perform deep TypeScript/HTML/CSS mutations for branding, lockdown, and extension embedding.</STEP>
      <STEP>Transfer the mutated codebase to the GCP server.</STEP>
      <STEP>Run the high-end compilation pipeline and extract the final artifact.</STEP>
      <STEP>Report completion with the final `.tar.gz` artifact path on the host.</STEP>
    </EXECUTION_PROTOCOL>
  </MASTER_PROMPT>

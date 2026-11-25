# Surface Brief — Authenticated LMS, Auth & Workspace IDE Shell (Operate)

- **Targets:** `resources/views/layouts/{dashboard,guest,app}.blade.php`, `resources/views/components/{sidebar,topbar,command-palette}.blade.php`, `resources/views/auth/*`, `resources/views/dashboard/*`, `resources/views/courses/*`, `resources/views/assignments/*`, `resources/views/submissions/*`, `resources/views/workspaces/*`, `resources/views/workspace.blade.php`, `resources/views/progress/*`, `resources/views/gamification/*`, `resources/views/profile/*`
- **Mode:** Operate

## Direction contract

- **THESIS:** Daily student, instructor, and IDE workflows feel like Linear/Raycast-grade studio instruments — fluid tonal separation, velvety depth, instant `⌘K` keyboard speed — refusing neon blur orbs, pill topbars, harsh boxy cages, and the disconnected orange IDE shell.
- **OWN-WORLD:** Seamless 240px tonal left rail (`--vl-rail`), 52px topbar (`⌘K` instant command palette, global unread announcements bell, `<x-theme-toggle />`), sculptural `rounded-2xl` silk panels (`--vl-panel` with top specular light catch), `Satoshi` fixed rem UI scale, `JetBrains Mono` `tabular-nums`, Ocean Sapphire primary actions, Luminous Teal verified states, and Warm Amber-Gold AI patch gates; `workspace.blade.php` runs a slim 40px obsidian/pearl silk topbar with live `/status` telemetry.
- **STORY:** Users move effortlessly between courses, lab deadlines, grading queues, and live `code-server` workspaces in Light or Dark mode with zero friction and crisp visual continuity.
- **FIRST VIEWPORT:** Full-height tonal sidebar + 52px topbar opening straight into high-signal courses, lab tasks, and workspace launches; inside `workspace.blade.php`, a slim 40px topbar above `calc(100vh - 40px)` `#vscode-frame`.
- **FORM:** Luminous Atelier — Operate Shell, `seed: none (pinned)`.
- **FINISH:** unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance

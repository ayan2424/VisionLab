@extends('layouts.landing')

@section('title', 'System Walkthrough & Demo Script — VisionLab')
@section('meta_description', 'Interactive 3-minute walkthrough script detailing VisionLab role-based access, sandboxed IDE, Reverb presence sync, and admin dashboards.')

@section('styles')
<style>
    /* Walkthrough Container */
    .walkthrough-container {
        max-width: 960px;
        margin: 0 auto;
        padding: 6rem 2rem 4rem;
        position: relative;
        z-index: 10;
    }

    /* Cards */
    .step-card {
        background: rgba(255, 255, 255, 0.015);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 2rem;
        transition: all 0.3s var(--ease-out-expo);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .step-card::before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: 0;
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.06), rgba(23, 195, 214, 0.03));
        transition: opacity 0.3s;
    }
    
    .step-card:hover::before, .step-card.active::before {
        opacity: 1;
    }
    
    .step-card.active {
        border-color: rgba(79, 70, 229, 0.4);
        box-shadow: 0 0 30px rgba(79, 70, 229, 0.12), rgba(0, 0, 0, 0.6) 0px 20px 50px -20px;
    }
    
    .step-card.done {
        border-color: rgba(0, 191, 166, 0.3);
    }
    
    .step-card.done .step-num {
        background: rgba(0, 191, 166, 0.15);
        color: var(--emerald-light);
        border-color: rgba(0, 191, 166, 0.3);
    }

    .step-num {
        width: 38px;
        height: 38px;
        border-radius: 0.75rem;
        background: rgba(79, 70, 229, 0.12);
        border: 1px solid rgba(79, 70, 229, 0.25);
        color: var(--indigo-light);
        font-weight: 700;
        font-size: 14px;
        font-family: 'JetBrains Mono', monospace;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.3s;
    }

    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
        background: rgba(23, 195, 214, 0.08);
        color: var(--cyan);
        border: 1px solid rgba(23, 195, 214, 0.2);
    }

    .feature-tag {
        display: inline-flex;
        align-items: center;
        padding: 2px 10px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
        background: rgba(79, 70, 229, 0.1);
        color: var(--indigo-light);
        border: 1px solid rgba(79, 70, 229, 0.2);
    }

    /* Credentials Widget */
    .credentials-widget {
        background: rgba(255, 255, 255, 0.01);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 1.75rem;
        margin-bottom: 2rem;
    }
    
    .credential-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid var(--border);
        background: rgba(0, 0, 0, 0.2);
        transition: border-color 0.2s;
        margin-top: 0.75rem;
    }
    .credential-row:hover {
        border-color: rgba(79, 70, 229, 0.3);
    }

    .copy-btn {
        font-family: 'JetBrains Mono', monospace;
        font-size: 10px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid var(--border);
        color: var(--muted-foreground);
        background: transparent;
        cursor: pointer;
        transition: all 0.15s;
    }
    .copy-btn:hover {
        background: rgba(79, 70, 229, 0.15);
        border-color: rgba(79, 70, 229, 0.4);
        color: #fff;
    }

    /* Progress and Timers */
    .progress-bar-container {
        height: 3px;
        background: var(--border);
        border-radius: 9999px;
        overflow: hidden;
        flex: 1;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--indigo), var(--cyan));
        border-radius: 9999px;
        width: 0%;
        transition: width 0.4s var(--ease-out-expo);
    }

    kbd {
        display: inline-flex;
        align-items: center;
        padding: 1px 6px;
        border-radius: 5px;
        font-size: 11px;
        font-family: 'JetBrains Mono', monospace;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border);
        color: var(--indigo-light);
    }

    .tip-card {
        background: rgba(79, 70, 229, 0.04);
        border: 1px solid rgba(79, 70, 229, 0.15);
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        margin-top: 1rem;
    }

    /* Buttons layout at bottom */
    .action-row {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-top: 3rem;
    }
    
    .toast-popup {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 0.75rem;
        font-size: 13px;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 600;
        color: #fff;
        background: var(--indigo);
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
        transition: all 0.3s var(--ease-out-expo);
        opacity: 0;
        transform: translateY(12px);
        pointer-events: none;
        z-index: 10000;
    }
</style>
@endsection

@section('content')
<div class="walkthrough-container">
    
    <!-- HEADER -->
    <div style="text-align:center; margin-bottom: 3.5rem;" class="reveal">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full mb-4"
             style="background:rgba(79, 70, 229, 0.08); border:1px solid rgba(79, 70, 229, 0.2);">
            <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
            <span class="text-[10px] font-bold font-mono tracking-wider text-indigo-300 uppercase">Enterprise Demo Console</span>
        </div>
        <h1 class="font-display text-4xl font-semibold tracking-tight text-white mb-2">
            Walkthrough & <span class="font-serif-italic text-gradient-hero" style="font-weight:400;text-transform:lowercase">evaluation script.</span>
        </h1>
        <p class="section-sub" style="margin: 1rem auto 0; text-align:center;">
            Interactive 3-minute guide for evaluating the platform sandbox workspaces, AI guards, and presence engines.
        </p>

        <!-- Progress Tracking -->
        <div style="display:flex; align-items:center; gap:1rem; max-width:360px; margin:2rem auto 0;">
            <span class="font-mono text-xs text-muted-foreground">Step <span id="cur-step">0</span>/8</span>
            <div class="progress-bar-container">
                <div class="progress-fill" id="progress-fill"></div>
            </div>
            <span class="font-mono text-xs text-muted-foreground" id="elapsed-time">0:00</span>
        </div>
    </div>

    <!-- CREDENTIALS BOARD -->
    <div class="credentials-widget reveal">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            <span class="text-sm font-bold text-white">Seed Role Credentials</span>
            <span class="ml-auto text-[10px] text-muted-foreground font-mono">CLICK TO COPY PASSWORD</span>
        </div>
        
        @php
        $creds = [
            ['role' => 'Administrator', 'email' => 'admin@visioncode.ai', 'pass' => 'Admin@12345', 'color' => '#818cf8', 'bg' => 'rgba(79, 70, 229, 0.1)'],
            ['role' => 'Instructor', 'email' => 'instructor@visioncode.ai', 'pass' => 'Instructor@12345', 'color' => '#17c3d6', 'bg' => 'rgba(23, 195, 214, 0.08)'],
            ['role' => 'Student', 'email' => 'student@visioncode.ai', 'pass' => 'Student@12345', 'color' => '#00bfa6', 'bg' => 'rgba(0, 191, 166, 0.08)'],
        ];
        @endphp
        
        @foreach($creds as $c)
        <div class="credential-row">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono" style="background:{{ $c['bg'] }}; color:{{ $c['color'] }};">{{ $c['role'] }}</span>
                <code class="text-xs text-slate-300 font-mono">{{ $c['email'] }}</code>
            </div>
            <button class="copy-btn" onclick="copyText('{{ $c['pass'] }}', this)">
                {{ $c['pass'] }}
            </button>
        </div>
        @endforeach
    </div>

    <!-- DEMO STEPS -->
    <div style="display:flex; flex-direction:column; gap:1.5rem;" id="steps-container">
        
        @php
        $steps = [
            [
                'time' => '0:00',
                'title' => 'Unified Landing & Navigation',
                'role' => null,
                'tags' => ['Landing', 'CSS Grids', 'Design System'],
                'script' => "Begin by navigating to the root URL. Explain to the evaluator that VisionLab consolidates previously disjointed systems (LMS, Video Rooms, AI Editors, and Local Sandboxes) into a unified, dark cyber aesthetics interface. Point out the official logo monogram in the header.",
                'actions' => [
                    "Verify the active layout uses the unified <code class='font-mono'>icons/logo.svg</code> asset.",
                    "Scroll through the grid floor design and notice the smooth cursor ring tracking.",
                    "Observe the backdrop-blur sticky header reflecting navigation routes."
                ],
                'tip' => "The site uses pure Tailwind and custom HSL variables to maintain consistent cyber glows without adding heavy framework scripts."
            ],
            [
                'time' => '0:20',
                'title' => 'Role-Based Entry gates (RBAC)',
                'role' => 'All',
                'tags' => ['Auth', 'Gates', 'Middleware'],
                'script' => "Demonstrate the multi-tier role routing. Log out of any current session and log in using different accounts. VisionLab enforces route locks via middleware, separating administrative options from student work.",
                'actions' => [
                    "Click <strong>Sign In</strong> in the header and authenticate as <strong>Admin</strong>.",
                    "Show that the admin is redirected immediately to the telemetry dashboard.",
                    "Logout and authenticate as <strong>Student</strong> to verify the redirect to their workspace list."
                ],
                'tip' => "Role limits are managed by Laravel Gates and structured Sanctum abilities linked directly to the database role schema."
            ],
            [
                'time' => '0:45',
                'title' => 'Docker Sandboxed Workspace Booting',
                'role' => 'Student',
                'tags' => ['Docker', 'Nix blueprint', 'Container manager'],
                'script' => "As a student, start a new workspace. Show that the backend initiates a custom sandboxed Docker container inside which the editor runs. Explain that the environment matches the declarative packages specified in the class blueprint.",
                'actions' => [
                    "Navigate to the Workspace panel and select a coding homework.",
                    "Click <strong>Launch Workspace</strong> and observe the loading preloader state.",
                    "Notice that the Nix shell environment loads and completes instantiation within 5 seconds."
                ],
                'tip' => "CodeServerManager boots isolated workspace containers using dropped capabilities, --read-only root mappings, and strict resource quotas."
            ],
            [
                'time' => '1:05',
                'title' => 'Socratic AI tutor & Patch Review diffs',
                'role' => 'Student',
                'tags' => ['Socratic Helper', 'AI Diff Viewer', 'Patch Queue'],
                'script' => "Open the AI Assistant drawer in the workspace. Toggle between modes. Show that when in Socratic mode, the AI guides students using leading questions. Change to Agent mode, request an edit, and show the diff reviewer.",
                'actions' => [
                    "Open the sidebar chat, select <strong>Socratic Mode</strong>, and input 'How do I invert a binary tree?'",
                    "Verify that the AI returns design guidance, not code blocks.",
                    "Switch to <strong>Agent Mode</strong>, type 'write a Python function to invert it', and view the red/green code diff review panel before confirming the write."
                ],
                'tip' => "All code mutations are quarantined in `ai_pending_patches` and must receive manual approval, safeguarding codebase integrity."
            ],
            [
                'time' => '1:30',
                'title' => 'WebSockets Collaboration & Connection states',
                'role' => 'Student / Instructor',
                'tags' => ['Laravel Reverb', 'Presence Channel', 'Echo Primitives'],
                'script' => "Click the collaboration action in the workspace toolbar. Explain that this registers the workspace inside a Laravel Reverb presence channel. Open two browser windows side-by-side to show typing sync.",
                'actions' => [
                    "Verify that the Reverb presence indicators appear in the workspace header.",
                    "Type in window A and observe changes synchronizing to window B in real time.",
                    "Move your cursor in window A and observe the highlighted name badge matching coordinates in window B."
                ],
                'tip' => "Echo listens for whispers and updates local cursors dynamically every 80ms over WebSockets without database writes."
            ],
            [
                'time' => '1:55',
                'title' => 'Integrated Jitsi Conferences',
                'role' => 'All',
                'tags' => ['Jitsi API', 'JWT tokens', 'Moderator Tiers'],
                'script' => "Initiate a class video session. Show that instead of redirecting users to Microsoft Teams or Zoom, VisionLab embeds a secured, Jitsi-powered conference panel directly inside the workspace.",
                'actions' => [
                    "Click the <strong>Video Session</strong> action to open the meeting drawer.",
                    "Verify that the moderator tier registers matching privileges (instructors can mute/exclude students).",
                    "Observe the sleek dark room background maintaining consistency with the theme."
                ],
                'tip' => "Secure JWT tokens authenticate users and determine moderator hierarchies, eliminating unauthorized room intrusions."
            ],
            [
                'time' => '2:20',
                'title' => 'System Governance & Pulse metrics',
                'role' => 'Admin',
                'tags' => ['Admin panel', 'Pulse Telemetry', 'Docker Logs'],
                'script' => "Switch back to the Admin Dashboard. Walk through the telemetry cards: see CPU/RAM constraints, active queues, Slow Queries reports via Pulse, and the central audit logs showing diff configurations.",
                'actions' => [
                    "Open the Admin console and view the active container resource limits.",
                    "Inspect the database audit logs showing actions recorded under Spatie Activity Log.",
                    "Verify slow request logs and memory usage details from Laravel Pulse."
                ],
                'tip' => "Administrative oversight maps workspace actions directly to a Spatie audit trail, recording before/after model diffs."
            ],
            [
                'time' => '2:45',
                'title' => 'Nix blueprints & Deployment Pipelines',
                'role' => 'All',
                'tags' => ['Deployment', 'Nix Blueprints', 'GitHub Actions'],
                'script' => "Conclude the demo by summarizing the stack. State that the platform is ready for deployment: containers build via Nix blueprints, GitHub Actions run the integration suite, and students can deploy assignments to Vercel/Railway with a single click.",
                'actions' => [
                    "Show the Nix package manager configurations and container blueprint tabs.",
                    "Demonstrate the one-click student deployment panel.",
                    "Verify that all test checks are green."
                ],
                'tip' => "Declaring the dev environment via Nix ensures that local machines, CI/CD runners, and cloud instances compile using identical binaries."
            ]
        ];
        @endphp

        @foreach($steps as $i => $step)
        <div class="step-card reveal" id="step-{{ $i }}" onclick="activateStep({{ $i }})">
            <div style="display:flex; align-items:start; gap:1.25rem;">
                <div class="step-num" id="step-num-{{ $i }}">{{ $i + 1 }}</div>
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; flex-wrap:wrap; align-items:center; gap:0.5rem; margin-bottom:0.75rem;">
                        <span class="time-badge">⏱ {{ $step['time'] }}</span>
                        @if($step['role'])
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full font-mono"
                              style="background:rgba(0, 191, 166, 0.08); color:var(--emerald-light); border:1px solid rgba(0, 191, 166, 0.2);">
                            {{ $step['role'] }} Role
                        </span>
                        @endif
                        @foreach($step['tags'] as $tag)
                        <span class="feature-tag">{{ $tag }}</span>
                        @endforeach
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-slate-400 leading-relaxed mb-4">{!! $step['script'] !!}</p>

                    <!-- Actions List -->
                    <div style="display:flex; flex-direction:column; gap:0.5rem; margin-bottom:1rem;">
                        @foreach($step['actions'] as $j => $action)
                        <label style="display:flex; align-items:start; gap:0.75rem; cursor:pointer;" onclick="event.stopPropagation();">
                            <input type="checkbox" style="margin-top:0.25rem; accent-color:var(--indigo);" id="check-{{ $i }}-{{ $j }}">
                            <span class="text-sm text-slate-400 leading-snug">{!! $action !!}</span>
                        </label>
                        @endforeach
                    </div>

                    <!-- Tip Callout -->
                    <div class="tip-card">
                        <div style="display:flex; align-items:start; gap:0.5rem;">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs text-muted-foreground leading-relaxed">{!! $step['tip'] !!}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>

    <!-- BUTTONS -->
    <div class="action-row">
        <button onclick="prevStep()" id="btn-prev" class="btn btn-secondary" style="padding:0.75rem 1.75rem;">
            ← Previous
        </button>
        <button onclick="nextStep()" id="btn-next" class="btn btn-primary" style="padding:0.75rem 1.75rem;">
            Next Step →
        </button>
        <button onclick="resetDemo()" class="btn btn-secondary" style="padding:0.75rem 1.75rem;">
            ↺ Reset
        </button>
    </div>

</div>

<!-- TOAST -->
<div id="toast" class="toast-popup">
    Password Copied!
</div>
@endsection

@section('scripts')
<script>
    let currentStep = -1;
    let startTime = null;
    let timerInterval = null;

    function activateStep(idx) {
        if (startTime === null) {
            startTime = Date.now();
            timerInterval = setInterval(updateTimer, 1000);
        }

        // Mark previous steps as done
        if (currentStep >= 0 && currentStep !== idx) {
            document.getElementById('step-' + currentStep)?.classList.add('done');
        }

        // Deactivate all steps
        document.querySelectorAll('.step-card').forEach(c => c.classList.remove('active'));
        
        // Activate current step
        currentStep = idx;
        const card = document.getElementById('step-' + idx);
        card?.classList.add('active');
        
        // Auto-check action boxes on activation
        document.querySelectorAll(`#step-${idx} input[type=checkbox]`).forEach(cb => cb.checked = true);

        updateProgress();
    }

    function nextStep() {
        if (currentStep < 7) {
            activateStep(currentStep + 1);
            const card = document.getElementById('step-' + currentStep);
            if (card) {
                const headerOffset = 100;
                const elementPosition = card.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
        }
    }

    function prevStep() {
        if (currentStep > 0) {
            activateStep(currentStep - 1);
            const card = document.getElementById('step-' + currentStep);
            if (card) {
                const headerOffset = 100;
                const elementPosition = card.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
        }
    }

    function resetDemo() {
        currentStep = -1;
        startTime = null;
        clearInterval(timerInterval);
        document.getElementById('elapsed-time').textContent = '0:00';
        document.getElementById('cur-step').textContent = '0';
        document.getElementById('progress-fill').style.width = '0%';
        document.querySelectorAll('.step-card').forEach(c => {
            c.classList.remove('active', 'done');
        });
        document.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }

    function updateProgress() {
        const total = 8;
        const done = currentStep + 1;
        const pct = (done / total) * 100;
        document.getElementById('progress-fill').style.width = pct + '%';
        document.getElementById('cur-step').textContent = done;
    }

    function updateTimer() {
        if (!startTime) return;
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const m = Math.floor(elapsed / 60);
        const s = elapsed % 60;
        document.getElementById('elapsed-time').textContent = m + ':' + String(s).padStart(2, '0');
    }

    function copyText(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.textContent;
            btn.textContent = '✓ Copied!';
            btn.style.color = '#00bfa6';
            setTimeout(() => { btn.textContent = orig; btn.style.color = ''; }, 1500);
            showToast('Credential Copied!');
        });
    }

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.style.opacity = '1';
        t.style.transform = 'translateY(0)';
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transform = 'translateY(12px)';
        }, 2000);
    }

    // Keyboard controls
    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') nextStep();
        if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') prevStep();
        if (e.key === 'r' || e.key === 'R') resetDemo();
    });

    // Auto-start step 1 on load
    document.addEventListener('DOMContentLoaded', () => {
        activateStep(0);
    });
</script>
@endsection

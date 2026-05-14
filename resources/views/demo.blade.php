<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demo Script — VisionLab· Aptech Vision 2026</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html,body{background:#0a0a0a;color:#c9d1d9;min-height:100%;font-family:sans-serif;}
        ::-webkit-scrollbar{width:5px}
        ::-webkit-scrollbar-track{background:#111}
        ::-webkit-scrollbar-thumb{background:#2a2a3a;border-radius:3px}

        .step-card{
            background:#111111;border:1px solid #21262d;border-radius:16px;
            padding:24px 28px;transition:all .3s cubic-bezier(.16,1,.3,1);
            cursor:pointer;position:relative;overflow:hidden;
        }
        .step-card::before{
            content:'';position:absolute;inset:0;opacity:0;
            background:linear-gradient(135deg,rgba(124,58,237,.08),rgba(34,211,238,.04));
            transition:opacity .3s;
        }
        .step-card:hover::before,.step-card.active::before{opacity:1;}
        .step-card.active{border-color:rgba(124,58,237,.5);box-shadow:0 0 32px rgba(124,58,237,.15);}
        .step-card.done{border-color:rgba(74,222,128,.3);}
        .step-card.done .step-num{background:rgba(74,222,128,.15);color:#4ade80;border-color:rgba(74,222,128,.3);}

        .step-num{
            width:36px;height:36px;border-radius:12px;
            background:rgba(124,58,237,.12);border:1px solid rgba(124,58,237,.25);
            color:#a78bfa;font-weight:800;font-size:14px;
            display:flex;align-items:center;justify-content:center;flex-shrink:0;
            transition:all .3s;
        }

        .time-badge{
            display:inline-flex;align-items:center;gap:4px;
            padding:3px 10px;border-radius:9999px;font-size:10px;font-weight:700;
            background:rgba(34,211,238,.08);color:#22d3ee;border:1px solid rgba(34,211,238,.2);
        }

        .feature-tag{
            display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;
            font-size:10px;font-weight:700;
            background:rgba(124,58,237,.1);color:#a78bfa;border:1px solid rgba(124,58,237,.2);
        }

        .credential-row{
            display:flex;align-items:center;justify-content:space-between;
            padding:10px 14px;border-radius:10px;border:1px solid #21262d;
            background:#0a0a0a;transition:border-color .2s;
        }
        .credential-row:hover{border-color:rgba(124,58,237,.3);}

        .copy-btn{
            font-size:10px;font-weight:600;padding:3px 8px;border-radius:6px;
            border:1px solid #21262d;color:#64748b;background:transparent;
            cursor:pointer;transition:all .15s;
        }
        .copy-btn:hover{background:rgba(124,58,237,.15);border-color:rgba(124,58,237,.4);color:#a78bfa;}

        @keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .fade-in{animation:fadeIn .4s ease forwards;}

        .progress-bar{
            height:3px;background:#21262d;border-radius:9999px;overflow:hidden;
        }
        .progress-fill{
            height:100%;background:linear-gradient(90deg,#7c3aed,#22d3ee);
            border-radius:9999px;transition:width .4s cubic-bezier(.16,1,.3,1);
        }

        kbd{
            display:inline-flex;align-items:center;
            padding:1px 6px;border-radius:5px;font-size:11px;font-family:monospace;
            background:#161b22;border:1px solid #30363d;color:#a78bfa;
        }

        .tip-card{
            background:rgba(124,58,237,.06);border:1px solid rgba(124,58,237,.2);
            border-radius:12px;padding:14px 18px;
        }
    </style>
</head>
<body>

<div class="max-w-4xl mx-auto px-6 py-10">

    <!-- ── HERO ── -->
    <div class="text-center mb-12 fade-in">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-6"
             style="background:rgba(124,58,237,.1);border:1px solid rgba(124,58,237,.25);">
            <span class="w-2 h-2 rounded-full bg-violet-400 animate-pulse"></span>
            <span class="text-xs font-bold text-violet-400">APTECH VISION 2026 — COMPETITION ENTRY</span>
        </div>
        <h1 class="text-4xl font-black text-white mb-3" style="letter-spacing:-.03em;">
            VisionLab
            <span style="background:linear-gradient(135deg,#7c3aed,#22d3ee);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                Demo Script
            </span>
        </h1>
        <p class="text-base text-slate-400 mb-6">
            3-Minute Judge Walkthrough · All features live on Replit
        </p>

        <!-- Progress -->
        <div class="flex items-center gap-3 max-w-sm mx-auto">
            <span class="text-xs text-muted">Step <span id="cur-step">0</span>/8</span>
            <div class="progress-bar flex-1">
                <div class="progress-fill" id="progress-fill" style="width:0%"></div>
            </div>
            <span class="text-xs text-muted" id="elapsed-time">0:00</span>
        </div>
    </div>

    <!-- ── CREDENTIALS PANEL ── -->
    <div class="mb-8 p-5 rounded-2xl border" style="background:#111111;border-color:#21262d;">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            <span class="text-sm font-bold text-white">Demo Credentials</span>
            <span class="ml-auto text-[10px] text-muted">Click to copy password</span>
        </div>
        <div class="space-y-2">
            @php
            $creds = [
                ['role'=>'Admin',      'email'=>'admin@visioncode.ai',      'pass'=>'Admin@12345',      'color'=>'#a78bfa','bg'=>'rgba(124,58,237,.12)'],
                ['role'=>'Instructor', 'email'=>'instructor@visioncode.ai', 'pass'=>'Instructor@12345', 'color'=>'#22d3ee','bg'=>'rgba(34,211,238,.1)'],
                ['role'=>'Student',    'email'=>'student@visioncode.ai',    'pass'=>'Student@12345',    'color'=>'#4ade80','bg'=>'rgba(74,222,128,.1)'],
            ];
            @endphp
            @foreach($creds as $c)
            <div class="credential-row">
                <div class="flex items-center gap-3">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" style="background:{{ $c['bg'] }};color:{{ $c['color'] }};">{{ $c['role'] }}</span>
                    <code class="text-xs text-slate-300">{{ $c['email'] }}</code>
                </div>
                <button class="copy-btn" onclick="copyText('{{ $c['pass'] }}', this)" data-pass="{{ $c['pass'] }}">
                    {{ $c['pass'] }}
                </button>
            </div>
            @endforeach
        </div>
    </div>

    <!-- ── DEMO STEPS ── -->
    <div class="space-y-4" id="steps-container">

        @php
        $steps = [
            [
                'time'  => '0:00',
                'title' => 'Landing Page & Value Prop',
                'role'  => null,
                'tags'  => ['Landing Page','Design'],
                'script'=> "Open the app at the home URL. Point out the hero headline <strong>\"Code. Collaborate. Conquer.\"</strong> — VisionLabis an AI-powered collaborative coding ecosystem built for modern development teams.",
                'actions'=> [
                    'Navigate to <code>/</code> (home page)',
                    'Scroll through features section',
                    'Point out the "Aptech Vision 2026" competition badge',
                    'Click <strong>Get Started Free</strong> to open login',
                ],
                'tip' => 'The landing page uses Tailwind CSS dark theme with a custom void background. All animations are pure CSS.',
            ],
            [
                'time'  => '0:20',
                'title' => 'Role-Based Login (RBAC)',
                'role'  => 'Any',
                'tags'  => ['Auth','RBAC','Security'],
                'script'=> "Demonstrate the three user roles. Each role has different access: <strong>Admin</strong> → Analytics + all features; <strong>Instructor</strong> → Workspace + student management; <strong>Student</strong> → Personal workspace only.",
                'actions'=> [
                    'Login as <strong>Admin</strong> (<code>admin@visioncode.ai</code> / <kbd>Admin@12345</kbd>)',
                    'Show redirect → Analytics Dashboard',
                    'Logout, login as <strong>Student</strong> (<code>student@visioncode.ai</code> / <kbd>Student@12345</kbd>)',
                    'Show redirect → Workspace (no admin access)',
                ],
                'tip' => 'RBAC is enforced by a custom <code>RoleMiddleware</code> + <code>hasRole()</code> on the User model. Roles: admin, instructor, student.',
            ],
            [
                'time'  => '0:45',
                'title' => 'Monaco Editor IDE',
                'role'  => 'Student',
                'tags'  => ['Monaco','IDE','Code Execution'],
                'script'=> "Open the workspace. Show the full-screen Monaco editor with the custom <strong>\"vc-dark\"</strong> theme — violet cursor, syntax highlighting for 12 languages, minimap, and bracket pair colorization.",
                'actions'=> [
                    'Navigate to <code>/workspace</code>',
                    'Click different files in the explorer (Python, JS, PHP…)',
                    'Click the language badge in the status bar to cycle languages',
                    'Show the AI sidebar on the right',
                    'Press <kbd>Ctrl+`</kbd> to toggle the output terminal',
                ],
                'tip' => 'Monaco Editor v0.47.0 loads from CDN. The custom vc-dark theme uses 10 token colours and 15 custom editor colour overrides.',
            ],
            [
                'time'  => '1:05',
                'title' => 'Code Execution (Piston API)',
                'role'  => 'Student',
                'tags'  => ['Piston API','Multi-language','Execution'],
                'script'=> "Type a simple Python script and press <strong>Run</strong>. Show the output in the terminal panel. Switch to a JavaScript file and run it too — demonstrating multi-language execution.",
                'actions'=> [
                    'Type <code>print("Hello, Aptech Vision 2026!")</code> in the Python file',
                    'Click the green <strong>▶ Run</strong> button (or <kbd>Ctrl+Enter</kbd>)',
                    'Show stdout output in the terminal panel',
                    'Switch to the JS file, run <code>console.log(2 ** 10)</code>',
                    'Show exit code badge (green = 0)',
                ],
                'tip' => 'Piston API executes code in 60+ languages inside sandboxed containers. The Laravel backend proxies the request on <code>POST /api/code/run</code>.',
            ],
            [
                'time'  => '1:25',
                'title' => 'AI Agent — 3 Modes',
                'role'  => 'Student',
                'tags'  => ['AI Agent','CHAT','PLAN','AGENT','Gemini'],
                'script'=> "Demonstrate all three AI Agent modes. <strong>CHAT</strong> answers questions read-only. <strong>PLAN</strong> produces a step-by-step implementation plan. <strong>AGENT</strong> proposes a diff and applies it to the file.",
                'actions'=> [
                    '<strong>CHAT mode:</strong> Type "explain this code" → show explanation response',
                    '<strong>PLAN mode:</strong> Type "add sorting feature" → show numbered plan',
                    '<strong>AGENT mode:</strong> Type "add docstrings to all functions" → show diff overlay',
                    'Click <strong>Approve & Apply</strong> to write the patch to the editor',
                    'Show the patched code in Monaco',
                ],
                'tip' => 'With <code>GEMINI_API_KEY</code> set, the agent uses Gemini 2.0 Flash. Without a key it uses intelligent demo responses. AGENT mode always shows a diff overlay before applying.',
            ],
            [
                'time'  => '1:50',
                'title' => 'Real-Time Collaboration (Reverb)',
                'role'  => 'Any',
                'tags'  => ['Reverb','WebSocket','Presence','Multi-cursor'],
                'script'=> "Click <strong>Collaborate</strong> in the topbar. Show the presence channel — live member list with avatars, Reverb connection status, and room management. Open two browser tabs to show real-time sync.",
                'actions'=> [
                    'Click the <strong>Collaborate</strong> button in the topbar',
                    'Show the Reverb connection status (green dot)',
                    'Open a second tab with the same workspace URL',
                    'Type in one tab — show code syncing to the other',
                    'Show the presence avatar bar updating in real time',
                    'Show remote cursor decoration in Monaco',
                ],
                'tip' => 'Laravel Reverb powers WebSocket presence channels. Echo joins <code>workspace.{roomId}</code> with <code>.here/.joining/.leaving</code>. Cursors broadcast every 80ms.',
            ],
            [
                'time'  => '2:20',
                'title' => 'Analytics Dashboard (Admin)',
                'role'  => 'Admin',
                'tags'  => ['Admin','Analytics','ApexCharts'],
                'script'=> "Log in as Admin and open the Analytics Dashboard. Walk through the KPI cards, activity line chart, language donut, heatmap, and recent sessions table.",
                'actions'=> [
                    'Login as <strong>Admin</strong> (<code>admin@visioncode.ai</code>)',
                    'Show the 4 KPI cards with delta indicators',
                    'Point out the 14-day activity chart (Executions · AI · Collabs)',
                    'Show the language distribution donut',
                    'Show the 7-day × 24-hour heatmap',
                    'Show the recent sessions table with role badges',
                ],
                'tip' => 'ApexCharts v3.50.0 renders 4 charts: area line, donut, two bar charts. The heatmap is pure CSS with dynamic rgba opacity values from PHP.',
            ],
            [
                'time'  => '2:45',
                'title' => 'Architecture & Stack',
                'role'  => null,
                'tags'  => ['Laravel 11','SQLite','Tailwind','Vite'],
                'script'=> "Summarise the full tech stack and architecture decisions. Emphasise the competition-grade quality: real-time collaboration, multi-language AI agent, Monaco IDE, and RBAC — all running on a single Replit.",
                'actions'=> [
                    'Show the Tech Stack badges panel at the bottom of Analytics',
                    'Mention: Laravel 11 + SQLite + Blade + Tailwind + Vite',
                    'Mention: Monaco Editor + Piston API + Gemini 2.0 Flash',
                    'Mention: Laravel Reverb (WebSockets) + Echo + Pusher JS',
                    'Say: "All features are live, not mocked — code actually executes."',
                ],
                'tip' => '"The entire platform runs on a single Replit container with zero paid infrastructure beyond the domain — showcasing efficient full-stack engineering."',
            ],
        ];
        @endphp

        @foreach($steps as $i => $step)
        <div class="step-card fade-in" id="step-{{ $i }}" onclick="activateStep({{ $i }})"
             style="animation-delay:{{ $i * 0.06 }}s;">
            <div class="flex items-start gap-4">
                <div class="step-num" id="step-num-{{ $i }}">{{ $i + 1 }}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="time-badge">⏱ {{ $step['time'] }}</span>
                        @if($step['role'])
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                              style="background:rgba(74,222,128,.1);color:#4ade80;border:1px solid rgba(74,222,128,.25);">
                            {{ $step['role'] }} Role
                        </span>
                        @endif
                        @foreach($step['tags'] as $tag)
                        <span class="feature-tag">{{ $tag }}</span>
                        @endforeach
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-slate-400 leading-relaxed mb-3">{!! $step['script'] !!}</p>

                    <!-- Actions checklist -->
                    <div class="space-y-1.5 mb-3">
                        @foreach($step['actions'] as $j => $action)
                        <label class="flex items-start gap-2.5 cursor-pointer group" onclick="event.stopPropagation();">
                            <input type="checkbox" class="mt-0.5 rounded accent-violet-500 cursor-pointer" id="check-{{ $i }}-{{ $j }}">
                            <span class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors leading-snug">{!! $action !!}</span>
                        </label>
                        @endforeach
                    </div>

                    <!-- Tip -->
                    <div class="tip-card">
                        <div class="flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 text-violet-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs text-muted leading-relaxed">{!! $step['tip'] !!}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div><!-- /steps -->

    <!-- ── FOOTER ── -->
    <div class="mt-10 text-center">
        <div class="inline-flex flex-col items-center gap-3">
            <div class="flex items-center gap-3">
                <button onclick="prevStep()" id="btn-prev"
                        class="px-5 py-2.5 rounded-xl border border-border text-sm font-bold text-slate-400 hover:text-white hover:bg-white/[0.04] transition-all disabled:opacity-30">
                    ← Previous
                </button>
                <button onclick="nextStep()" id="btn-next"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                        style="background:#7c3aed;box-shadow:0 0 16px rgba(124,58,237,.4);">
                    Next Step →
                </button>
                <button onclick="resetDemo()"
                        class="px-5 py-2.5 rounded-xl border border-border text-sm font-bold text-slate-400 hover:text-white hover:bg-white/[0.04] transition-all">
                    ↺ Reset
                </button>
            </div>
            <div class="flex items-center gap-3 text-xs text-muted">
                <a href="{{ route('home') }}" class="hover:text-violet-400 transition-colors">← Back to App</a>
                <span>·</span>
                <a href="{{ route('admin.analytics') }}" class="hover:text-violet-400 transition-colors">Analytics →</a>
                <span>·</span>
                <a href="{{ route('workspace.index') }}" class="hover:text-violet-400 transition-colors">Workspace →</a>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 right-6 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all duration-300 opacity-0 translate-y-2 pointer-events-none"
         style="background:#7c3aed;box-shadow:0 8px 24px rgba(124,58,237,.5);">
        Copied!
    </div>

</div><!-- /max-w-4xl -->

<script>
let currentStep = -1;
let startTime   = null;
let timerInterval = null;

function activateStep(idx) {
    if (startTime === null) {
        startTime = Date.now();
        timerInterval = setInterval(updateTimer, 1000);
    }

    // Mark previous as done
    if (currentStep >= 0 && currentStep !== idx) {
        document.getElementById('step-' + currentStep)?.classList.add('done');
    }

    // Deactivate all
    document.querySelectorAll('.step-card').forEach(c => c.classList.remove('active'));
    // Activate current
    currentStep = idx;
    const card = document.getElementById('step-' + idx);
    card?.classList.add('active');
    card?.scrollIntoView({ behavior:'smooth', block:'nearest' });

    updateProgress();
}

function nextStep() {
    activateStep(Math.min(currentStep + 1, 7));
}

function prevStep() {
    if (currentStep > 0) activateStep(currentStep - 1);
}

function resetDemo() {
    currentStep = -1;
    startTime   = null;
    clearInterval(timerInterval);
    document.getElementById('elapsed-time').textContent = '0:00';
    document.getElementById('cur-step').textContent     = '0';
    document.getElementById('progress-fill').style.width = '0%';
    document.querySelectorAll('.step-card').forEach(c => {
        c.classList.remove('active','done');
    });
    document.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
}

function updateProgress() {
    const total  = 8;
    const done   = currentStep + 1;
    const pct    = (done / total) * 100;
    document.getElementById('progress-fill').style.width = pct + '%';
    document.getElementById('cur-step').textContent = done;
}

function updateTimer() {
    if (!startTime) return;
    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    const m = Math.floor(elapsed / 60);
    const s = elapsed % 60;
    document.getElementById('elapsed-time').textContent = m + ':' + String(s).padStart(2,'0');
}

function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = '✓ Copied!';
        btn.style.color = '#4ade80';
        setTimeout(() => { btn.textContent = orig; btn.style.color = ''; }, 1500);
        showToast('Password copied!');
    });
}

function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.opacity = '1';
    t.style.transform = 'translateY(0)';
    setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateY(8px)'; }, 2000);
}

// Keyboard navigation
document.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') nextStep();
    if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   prevStep();
    if (e.key === 'r' || e.key === 'R') resetDemo();
});

// Auto-start on load
activateStep(0);
</script>

</body>
</html>

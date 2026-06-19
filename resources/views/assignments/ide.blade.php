<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $assignment->title }} — IDE · VisionLab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *,*::before,*::after{box-sizing:border-box;}
        html,body{height:100%;margin:0;padding:0;overflow:hidden;background:#0a0a0a;font-family:sans-serif;}

        /* ── Layout ── */
        #ide-root{display:flex;flex-direction:column;height:100vh;}
        #ide-topbar{height:44px;display:flex;align-items:center;justify-content:space-between;
            padding:0 12px;border-bottom:1px solid #21262d;background:#0d1117;flex-shrink:0;z-index:50;}
        #ide-body{display:flex;flex:1;overflow:hidden;}
        #editor-pane{flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;}
        #monaco-container{flex:1;overflow:hidden;}
        #output-pane{height:180px;flex-shrink:0;border-top:1px solid #21262d;background:#0a0a0a;display:flex;flex-direction:column;}
        #output-header{display:flex;align-items:center;justify-content:space-between;padding:6px 12px;border-bottom:1px solid #21262d;flex-shrink:0;}
        #output-content{flex:1;overflow-y:auto;padding:8px 12px;font-family:'JetBrains Mono',Consolas,monospace;font-size:12px;color:#94a3b8;white-space:pre-wrap;word-break:break-all;}
        #output-content::-webkit-scrollbar{width:4px;}
        #output-content::-webkit-scrollbar-thumb{background:#21262d;}

        /* ── AI Panel ── */
        #ai-panel{width:320px;flex-shrink:0;border-left:1px solid #21262d;background:#0d1117;display:flex;flex-direction:column;transition:width .3s cubic-bezier(.16,1,.3,1);}
        #ai-panel.closed{width:0;overflow:hidden;border-left:none;}
        #ai-panel-header{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-bottom:1px solid #21262d;flex-shrink:0;}
        .mode-tab{flex:1;text-align:center;padding:4px 0;font-size:10px;font-weight:700;letter-spacing:.06em;border-radius:5px;cursor:pointer;transition:all .18s;color:#64748b;border:none;background:transparent;}
        .mode-tab.active{background:#7c3aed;color:#fff;}
        #ai-messages{flex:1;overflow-y:auto;padding:10px;display:flex;flex-direction:column;gap:8px;}
        #ai-messages::-webkit-scrollbar{width:4px;}
        #ai-messages::-webkit-scrollbar-thumb{background:#21262d;}
        .msg-user{align-self:flex-end;background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.25);border-radius:10px 10px 2px 10px;padding:7px 10px;font-size:11.5px;color:#c4b5fd;max-width:90%;word-break:break-word;}
        .msg-ai{align-self:flex-start;background:#111111;border:1px solid #21262d;border-radius:10px 10px 10px 2px;padding:7px 10px;font-size:11.5px;color:#94a3b8;max-width:95%;word-break:break-word;}
        .msg-ai code{background:#0a0a0a;border:1px solid #21262d;border-radius:3px;padding:1px 4px;font-family:'JetBrains Mono',monospace;font-size:11px;color:#a78bfa;}
        .msg-ai pre{background:#0a0a0a;border:1px solid #21262d;border-radius:6px;padding:8px;margin:5px 0;overflow-x:auto;font-family:'JetBrains Mono',monospace;font-size:11px;color:#94a3b8;white-space:pre-wrap;word-break:break-all;}
        .msg-thinking{align-self:flex-start;padding:7px 10px;font-size:11px;color:#64748b;display:flex;align-items:center;gap:5px;}
        .thinking-dot{width:5px;height:5px;border-radius:50%;background:#7c3aed;animation:thinkDot 1.4s ease-in-out infinite;}
        .thinking-dot:nth-child(2){animation-delay:.2s;}
        .thinking-dot:nth-child(3){animation-delay:.4s;}
        @keyframes thinkDot{0%,80%,100%{transform:scale(.7);opacity:.4}40%{transform:scale(1.1);opacity:1}}
        #ai-input-area{padding:8px 10px;border-top:1px solid #21262d;flex-shrink:0;}
        #ai-textarea{width:100%;resize:none;background:#0a0a0a;border:1px solid #21262d;border-radius:8px;padding:7px 9px;font-size:11.5px;color:#f1f5f9;font-family:sans-serif;line-height:1.5;outline:none;transition:border-color .2s;}
        #ai-textarea:focus{border-color:rgba(124,58,237,.5);}
        #ai-textarea::placeholder{color:#64748b;}

        /* ── Buttons ── */
        .btn-run{display:flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;border:none;cursor:pointer;font-size:11px;font-weight:700;letter-spacing:.04em;background:#16a34a;color:#fff;transition:all .2s;}
        .btn-run:hover{background:#15803d;}
        .btn-run:disabled{background:#374151;cursor:not-allowed;color:#6b7280;}
        .btn-submit{display:flex;align-items:center;gap:5px;padding:5px 14px;border-radius:7px;border:none;cursor:pointer;font-size:11px;font-weight:700;background:#7c3aed;color:#fff;transition:all .2s;}
        .btn-submit:hover{background:#6d28d9;}
        .btn-save{display:flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;border:none;cursor:pointer;font-size:11px;font-weight:600;background:transparent;color:#64748b;border:1px solid #21262d;transition:all .2s;}
        .btn-save:hover{border-color:#475569;color:#fff;}
        .btn-ai{display:flex;align-items:center;gap:4px;padding:5px 10px;border-radius:7px;border:none;cursor:pointer;font-size:11px;font-weight:700;transition:all .2s;}
        .btn-ai.on{background:rgba(124,58,237,.2);color:#a78bfa;border:1px solid rgba(124,58,237,.3);}
        .btn-ai.off{background:#7c3aed;color:#fff;border:1px solid transparent;}

        /* ── Instructions panel ── */
        #instructions-panel{width:280px;flex-shrink:0;border-right:1px solid #21262d;background:#0d1117;display:flex;flex-direction:column;overflow:hidden;transition:width .3s cubic-bezier(.16,1,.3,1);}
        #instructions-panel.closed{width:0;overflow:hidden;border-right:none;}
        #instructions-content{flex:1;overflow-y:auto;padding:12px;}
        #instructions-content::-webkit-scrollbar{width:4px;}
        #instructions-content::-webkit-scrollbar-thumb{background:#21262d;}

        /* ── Diff overlay ── */
        #diff-overlay{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.87);backdrop-filter:blur(8px);align-items:center;justify-content:center;}
        #diff-overlay.open{display:flex;}
        .diff-removed{background:rgba(255,0,0,.08);border-left:2px solid #ef4444;color:#fca5a5;padding:1px 14px;}
        .diff-added{background:rgba(0,255,0,.06);border-left:2px solid #22c55e;color:#86efac;padding:1px 14px;}
        .diff-context{color:#475569;padding:1px 14px;}

        /* ── Submit confirm modal ── */
        #submit-modal{display:none;position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,.8);backdrop-filter:blur(8px);align-items:center;justify-content:center;}
        #submit-modal.open{display:flex;}

        /* status pill */
        .status-pill{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;letter-spacing:.05em;}
    </style>
</head>
<body class="h-full">

{{-- ══ DIFF OVERLAY ══ --}}
<div id="diff-overlay">
    <div style="width:100%;max-width:620px;margin:16px;background:#111111;border:1px solid #21262d;border-radius:16px;max-height:80vh;display:flex;flex-direction:column;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #21262d;flex-shrink:0;">
            <div style="font-size:13px;font-weight:700;color:#f1f5f9;">AI Patch Preview</div>
            <button onclick="closeDiff()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="diff-content" style="flex:1;overflow-y:auto;font-family:'JetBrains Mono',monospace;font-size:12px;line-height:1.7;"></div>
        <div style="display:flex;gap:10px;padding:14px 18px;border-top:1px solid #21262d;flex-shrink:0;">
            <button onclick="approvePatch()" style="flex:1;padding:9px;border-radius:9px;background:#7c3aed;color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;">✓ Apply to Editor</button>
            <button onclick="closeDiff()" style="padding:9px 18px;border-radius:9px;background:transparent;color:#64748b;font-size:12px;font-weight:700;border:1px solid #21262d;cursor:pointer;">Reject</button>
        </div>
    </div>
</div>

{{-- ══ SUBMIT MODAL ══ --}}
<div id="submit-modal">
    <div style="width:100%;max-width:400px;margin:16px;background:#111111;border:1px solid #21262d;border-radius:16px;overflow:hidden;">
        <div style="padding:20px;">
            <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:6px;">Submit Assignment</div>
            <div style="font-size:12px;color:#64748b;margin-bottom:20px;">Your current code will be saved and submitted for grading. This action can't be undone.</div>
            <form id="submit-form" method="POST" action="{{ route('submissions.submit', $assignment->id) }}">
                @csrf
                <input type="hidden" name="code_snapshot" id="submit-code-field">
                <div style="display:flex;gap:10px;">
                    <button type="submit" style="flex:1;padding:10px;border-radius:10px;background:#7c3aed;color:#fff;font-size:13px;font-weight:700;border:none;cursor:pointer;">Submit Now</button>
                    <button type="button" onclick="closeSubmitModal()" style="padding:10px 18px;border-radius:10px;background:transparent;color:#64748b;font-size:13px;font-weight:700;border:1px solid #21262d;cursor:pointer;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══ IDE ROOT ══ --}}
<div id="ide-root">

    {{-- Top bar --}}
    <div id="ide-topbar">
        {{-- Left: breadcrumb + status --}}
        <div style="display:flex;align-items:center;gap:10px;">
            <a href="{{ route('home') }}" style="display:flex;align-items:center;text-decoration:none;">
                <x-logo size="h-6 w-6" textSize="text-xs" />
            </a>
            <span style="color:#21262d;">›</span>
            <a href="{{ route('courses.show', $course->slug) }}" style="font-size:11px;color:#64748b;text-decoration:none;hover:color:#fff;">{{ $course->title }}</a>
            <span style="color:#21262d;">›</span>
            <span style="font-size:11px;color:#94a3b8;font-weight:600;">{{ $assignment->title }}</span>
            <span id="save-indicator" style="font-size:10px;color:#4ade80;opacity:0;transition:opacity .3s;">Saved</span>
        </div>

        {{-- Right: actions --}}
        <div style="display:flex;align-items:center;gap:8px;">
            {{-- Info about assignment --}}
            @if($assignment->due_date)
            <span style="font-size:10px;color:{{ $assignment->isOverdue() ? '#f87171' : '#64748b' }};">
                Due: {{ $assignment->due_date->format('M d, H:i') }}
            </span>
            @endif

            {{-- Lang badge --}}
            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:rgba(124,58,237,.15);color:#a78bfa;border:1px solid rgba(124,58,237,.25);">{{ strtoupper($assignment->starter_language) }}</span>

            {{-- Instructions toggle --}}
            <button id="instr-toggle" onclick="toggleInstructions()" style="display:flex;align-items:center;gap:4px;padding:5px 10px;border-radius:7px;border:1px solid #21262d;background:transparent;cursor:pointer;font-size:11px;color:#64748b;font-weight:600;transition:all .2s;">
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Instructions
            </button>

            {{-- Save --}}
            <button class="btn-save" onclick="saveCode()">
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Save
            </button>

            {{-- Run --}}
            <button class="btn-run" id="run-btn" onclick="runCode()">
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Run
            </button>

            {{-- AI toggle --}}
            <button id="ai-toggle-btn" class="btn-ai off" onclick="toggleAI()">
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                AI
            </button>

            {{-- Submit --}}
            @if(in_array($submission->status, ['in_progress', 'not_started']))
            <button class="btn-submit" onclick="openSubmitModal()">
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Submit
            </button>
            @else
            <span class="status-pill {{ $submission->status_badge_class }} border">{{ ucfirst($submission->status) }}</span>
            @endif

            {{-- User avatar --}}
            <div style="width:28px;height:28px;border-radius:50%;background:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;">{{ $user->avatar_initials }}</div>
        </div>
    </div>

    {{-- IDE body --}}
    <div id="ide-body">

        {{-- Instructions panel --}}
        <div id="instructions-panel" class="closed">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-bottom:1px solid #21262d;flex-shrink:0;">
                <span style="font-size:11px;font-weight:700;color:#f1f5f9;">Instructions</span>
                <button onclick="toggleInstructions()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="instructions-content">
                <div style="margin-bottom:12px;">
                    <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Assignment</div>
                    <div style="font-size:13px;font-weight:700;color:#f1f5f9;margin-bottom:4px;">{{ $assignment->title }}</div>
                    @if($assignment->due_date)
                    <div style="font-size:11px;color:{{ $assignment->isOverdue() ? '#f87171' : '#64748b' }};">Due: {{ $assignment->due_date->format('M d, Y H:i') }}</div>
                    @endif
                    <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $assignment->max_points }} points</div>
                </div>
                @if($assignment->description)
                <div style="margin-bottom:12px;">
                    <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Description</div>
                    <div style="font-size:12px;color:#94a3b8;line-height:1.6;white-space:pre-wrap;">{{ $assignment->description }}</div>
                </div>
                @endif
                @if($assignment->starter_code)
                <div>
                    <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Starter Code</div>
                    <pre style="background:#0a0a0a;border:1px solid #21262d;border-radius:6px;padding:8px;font-family:'JetBrains Mono',monospace;font-size:11px;color:#94a3b8;overflow-x:auto;white-space:pre-wrap;">{{ $assignment->starter_code }}</pre>
                </div>
                @endif
                @if($submission->status === 'graded')
                <div style="margin-top:12px;padding:10px;border-radius:8px;background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);">
                    <div style="font-size:10px;color:#6ee7b7;font-weight:700;margin-bottom:4px;">GRADED</div>
                    <div style="font-size:20px;font-weight:900;color:#4ade80;">{{ $submission->grade }}/{{ $assignment->max_points }}</div>
                    @if($submission->feedback)
                    <div style="font-size:11px;color:#94a3b8;margin-top:6px;">{{ $submission->feedback }}</div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Editor pane --}}
        <div id="editor-pane">
            <div id="monaco-container"></div>
            {{-- Output pane --}}
            <div id="output-pane">
                <div id="output-header">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:11px;font-weight:700;color:#f1f5f9;">Terminal Output</span>
                        <span id="output-status" style="font-size:10px;color:#64748b;"></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <button onclick="clearOutput()" style="font-size:10px;color:#64748b;background:none;border:none;cursor:pointer;hover:color:#fff;">Clear</button>
                        <button onclick="resizeOutput()" style="font-size:10px;color:#64748b;background:none;border:none;cursor:pointer;">↕</button>
                    </div>
                </div>
                <div id="output-content">Press <span style="color:#a78bfa;font-weight:700;">Run</span> to execute your code…</div>
            </div>
        </div>

        {{-- AI Panel --}}
        <div id="ai-panel" class="closed">
            <div id="ai-panel-header">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:20px;height:20px;border-radius:6px;background:rgba(124,58,237,.2);border:1px solid rgba(124,58,237,.3);display:flex;align-items:center;justify-content:center;">
                        <svg style="width:11px;height:11px;color:#a78bfa;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <span style="font-size:11px;font-weight:700;color:#f1f5f9;">Gemini AI</span>
                </div>
                <div style="display:flex;gap:3px;background:#0a0a0a;border:1px solid #21262d;border-radius:7px;padding:2px;">
                    <button class="mode-tab active" id="tab-CHAT" onclick="setMode('CHAT')">CHAT</button>
                    <button class="mode-tab" id="tab-PLAN" onclick="setMode('PLAN')">PLAN</button>
                    <button class="mode-tab" id="tab-AGENT" onclick="setMode('AGENT')">AGENT</button>
                </div>
            </div>
            <div id="ai-messages">
                <div class="msg-ai">Hi! I'm your AI coding assistant for <strong>{{ $assignment->title }}</strong>. Ask me to explain concepts, debug your code, or switch to <strong>AGENT</strong> mode to apply changes directly.</div>
            </div>
            <div id="ai-input-area">
                <textarea id="ai-textarea" rows="2" placeholder="Ask AI… (Enter to send, Shift+Enter for newline)"></textarea>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:5px;">
                    <span style="font-size:10px;color:#475569;" id="ai-model-label">Demo mode</span>
                    <button onclick="sendAiMessage()" style="padding:4px 12px;border-radius:6px;background:#7c3aed;color:#fff;font-size:11px;font-weight:700;border:none;cursor:pointer;">Send</button>
                </div>
            </div>
        </div>

    </div>{{-- /ide-body --}}
</div>{{-- /ide-root --}}

{{-- Monaco CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js"></script>

<script>
const IDE = {
    csrf:       '{{ csrf_token() }}',
    apiBase:    '/api',
    lang:       '{{ $assignment->starter_language }}',
    filename:   '{{ $assignment->starter_language === "python" ? "solution.py" : ($assignment->starter_language === "javascript" ? "solution.js" : ($assignment->starter_language === "php" ? "solution.php" : "solution." . $assignment->starter_language)) }}',
    initialCode: @json($submission->code_snapshot ?? $assignment->starter_code ?? ''),
    submissionStatus: '{{ $submission->status }}',
    assignmentId: {{ $assignment->id }},
};

let monacoEditor  = null;
let aiMode        = 'CHAT';
let aiOpen        = false;
let instrOpen     = false;
let pendingPatch  = null;
let outputExpanded = false;

// ─── Monaco init ──────────────────────────────────────────────────
require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' } });
require(['vs/editor/editor.main'], function() {
    monaco.editor.defineTheme('vc-dark', {
        base: 'vs-dark', inherit: true,
        rules: [
            { token: 'comment',  foreground: '475569', fontStyle: 'italic' },
            { token: 'keyword',  foreground: 'a78bfa', fontStyle: 'bold'   },
            { token: 'string',   foreground: '4ade80' },
            { token: 'number',   foreground: 'f59e0b' },
            { token: 'function', foreground: '38bdf8' },
        ],
        colors: {
            'editor.background':              '#0a0a0a',
            'editor.foreground':              '#e2e8f0',
            'editorLineNumber.foreground':    '#334155',
            'editorLineNumber.activeForeground': '#7c3aed',
            'editor.lineHighlightBackground': 'rgba(124,58,237,0.06)',
            'editorCursor.foreground':        '#7c3aed',
            'editor.selectionBackground':     'rgba(124,58,237,0.25)',
            'editorIndentGuide.background':   '#21262d',
        }
    });

    const langMap = {
        python: 'python', javascript: 'javascript', typescript: 'typescript',
        php: 'php', java: 'java', c: 'c', cpp: 'cpp', rust: 'rust', go: 'go',
        ruby: 'ruby', bash: 'shell',
    };

    monacoEditor = monaco.editor.create(document.getElementById('monaco-container'), {
        value:               IDE.initialCode || '',
        language:            langMap[IDE.lang] || 'python',
        theme:               'vc-dark',
        fontSize:            14,
        fontFamily:          "'JetBrains Mono', 'Fira Code', Consolas, monospace",
        fontLigatures:       true,
        lineNumbers:         'on',
        minimap:             { enabled: true, scale: 0.8 },
        scrollBeyondLastLine: false,
        wordWrap:            'off',
        automaticLayout:     true,
        tabSize:             4,
        insertSpaces:        true,
        renderWhitespace:    'selection',
        smoothScrolling:     true,
        cursorBlinking:      'phase',
        cursorSmoothCaretAnimation: 'on',
        bracketPairColorization: { enabled: true },
        formatOnPaste:       true,
    });

    // Auto-save every 30s
    setInterval(saveCode, 30000);

    // Keyboard shortcuts
    monacoEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.Enter, runCode);
    monacoEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, () => { saveCode(); });
});

// ─── Instructions toggle ──────────────────────────────────────────
function toggleInstructions() {
    instrOpen = !instrOpen;
    document.getElementById('instructions-panel').classList.toggle('closed', !instrOpen);
}

// ─── AI panel toggle ──────────────────────────────────────────────
function toggleAI() {
    aiOpen = !aiOpen;
    document.getElementById('ai-panel').classList.toggle('closed', !aiOpen);
    const btn = document.getElementById('ai-toggle-btn');
    btn.className = 'btn-ai ' + (aiOpen ? 'on' : 'off');
}

// ─── AI mode ─────────────────────────────────────────────────────
function setMode(m) {
    aiMode = m;
    ['CHAT','PLAN','AGENT'].forEach(t => {
        document.getElementById('tab-' + t)?.classList.toggle('active', t === m);
    });
}

// ─── Run code ────────────────────────────────────────────────────
async function runCode() {
    if (!monacoEditor) return;
    const code = monacoEditor.getValue();
    const btn  = document.getElementById('run-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg style="width:12px;height:12px;animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Running…';

    const output = document.getElementById('output-content');
    const status = document.getElementById('output-status');
    output.innerHTML = '<span style="color:#64748b;">Executing via Piston API…</span>';
    status.textContent = '';

    try {
        const res  = await fetch(IDE.apiBase + '/code/run', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': IDE.csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ language: IDE.lang, source_code: code, filename: IDE.filename }),
            credentials: 'same-origin',
        });
        const data = await res.json();

        if (data.stdout || data.stderr) {
            let html = '';
            if (data.stdout) html += data.stdout.replace(/</g,'&lt;').replace(/>/g,'&gt;');
            if (data.stderr) html += '<span style="color:#f87171;">' + data.stderr.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</span>';
            output.innerHTML = html || '<span style="color:#64748b;">(no output)</span>';
            status.textContent = data.success ? '✓ Exited 0' : '✗ Exit ' + data.exit_code;
            status.style.color = data.success ? '#4ade80' : '#f87171';
        } else {
            output.innerHTML = '<span style="color:#64748b;">(no output)</span>';
            status.textContent = data.success ? '✓ OK' : '✗ Error';
        }
    } catch(e) {
        output.innerHTML = '<span style="color:#f87171;">Network error — check connection.</span>';
        status.textContent = '✗ Error';
        status.style.color = '#f87171';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Run';
    }
}

// ─── Save code ────────────────────────────────────────────────────
async function saveCode() {
    if (!monacoEditor) return;
    const code = monacoEditor.getValue();
    try {
        await fetch('/assignments/' + IDE.assignmentId + '/save-snapshot', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': IDE.csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ code_snapshot: code }),
            credentials: 'same-origin',
        });
        const ind = document.getElementById('save-indicator');
        ind.style.opacity = '1';
        setTimeout(() => ind.style.opacity = '0', 2000);
    } catch(_) {}
}

// ─── Clear output ─────────────────────────────────────────────────
function clearOutput() {
    document.getElementById('output-content').innerHTML = '<span style="color:#64748b;">Cleared.</span>';
    document.getElementById('output-status').textContent = '';
}

// ─── Resize output ────────────────────────────────────────────────
function resizeOutput() {
    const pane = document.getElementById('output-pane');
    outputExpanded = !outputExpanded;
    pane.style.height = outputExpanded ? '50vh' : '180px';
}

// ─── Submit modal ─────────────────────────────────────────────────
function openSubmitModal() {
    if (monacoEditor) {
        document.getElementById('submit-code-field').value = monacoEditor.getValue();
    }
    document.getElementById('submit-modal').classList.add('open');
}
function closeSubmitModal() {
    document.getElementById('submit-modal').classList.remove('open');
}

// ─── AI chat ──────────────────────────────────────────────────────
async function sendAiMessage() {
    const ta = document.getElementById('ai-textarea');
    const msg = ta.value.trim();
    if (!msg) return;
    ta.value = '';

    if (!aiOpen) toggleAI();
    appendMsg('user', msg);
    const thinkId = appendThinking();

    const code = monacoEditor ? monacoEditor.getValue() : '';

    try {
        const res = await fetch(IDE.apiBase + '/ai/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': IDE.csrf, 'Accept': 'application/json' },
            body: JSON.stringify({
                message: msg,
                mode:    aiMode,
                context: { filename: IDE.filename, language: IDE.lang, code },
            }),
            credentials: 'same-origin',
        });
        removeThinking(thinkId);
        if (!res.ok) { appendMsg('ai', '⚠️ Error ' + res.status); return; }
        const data = await res.json();
        if (data.model) document.getElementById('ai-model-label').textContent = data.model;

        if (data.patch && aiMode === 'AGENT') {
            pendingPatch = data.patch;
            appendMsg('ai', (data.message || 'Patch ready.') + '\n\n<em style="color:#a78bfa;font-size:10px;">See diff preview below ↓</em>');
            showDiff(data.patch);
        } else {
            appendMsg('ai', data.message || data.reply || 'Done.');
            if (data.code_snippet) {
                appendMsg('ai', '<pre>' + esc(data.code_snippet) + '</pre>');
            }
        }
    } catch(e) {
        removeThinking(thinkId);
        appendMsg('ai', '⚠️ Network error.');
    }
}

function appendMsg(role, text) {
    const msgs = document.getElementById('ai-messages');
    const div  = document.createElement('div');
    div.className = role === 'user' ? 'msg-user' : 'msg-ai';
    let html = esc(text)
        .replace(/```([\s\S]*?)```/g,'<pre>$1</pre>')
        .replace(/`([^`]+)`/g,'<code>$1</code>')
        .replace(/\*\*(.+?)\*\*/g,'<strong style="color:#c4b5fd;">$1</strong>')
        .replace(/\n/g,'<br>');
    div.innerHTML = html;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
}
function appendThinking() {
    const msgs = document.getElementById('ai-messages');
    const id   = 'think-' + Date.now();
    const div  = document.createElement('div');
    div.id = id; div.className = 'msg-thinking';
    div.innerHTML = '<span class="thinking-dot"></span><span class="thinking-dot"></span><span class="thinking-dot"></span><span style="margin-left:4px;font-size:11px;color:#64748b;">Thinking…</span>';
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return id;
}
function removeThinking(id) { document.getElementById(id)?.remove(); }
function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ─── Diff ──────────────────────────────────────────────────────────
function showDiff(patch) {
    const orig    = (patch.original || '').split('\n');
    const patched = (patch.patched  || '').split('\n');
    let html = '';
    const max = Math.max(orig.length, patched.length);
    for (let i = 0; i < max; i++) {
        const o = orig[i] ?? ''; const p = patched[i] ?? '';
        if (o === p) html += `<div class="diff-context">${esc(o)||'&nbsp;'}</div>`;
        else {
            if (o) html += `<div class="diff-removed">- ${esc(o)}</div>`;
            if (p) html += `<div class="diff-added">+ ${esc(p)}</div>`;
        }
    }
    document.getElementById('diff-content').innerHTML = html;
    document.getElementById('diff-overlay').classList.add('open');
}
function closeDiff() { document.getElementById('diff-overlay').classList.remove('open'); }
function approvePatch() {
    if (pendingPatch && monacoEditor) {
        monacoEditor.setValue(pendingPatch.patched || '');
        appendMsg('ai', '✅ Patch applied to editor!');
    }
    closeDiff();
    pendingPatch = null;
}

// ─── Keyboard shortcuts ───────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeDiff(); closeSubmitModal(); }
    if (e.ctrlKey && e.shiftKey && e.key === 'A') { e.preventDefault(); toggleAI(); }
    if (e.ctrlKey && e.shiftKey && e.key === 'I') { e.preventDefault(); toggleInstructions(); }
});

// AI textarea enter key
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('ai-textarea')?.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendAiMessage(); }
    });
});

@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
</script>

<style>
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
</style>

</body>
</html>

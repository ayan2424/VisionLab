<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $workspaceName ?? 'Workspace' }} — VisionLab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *,*::before,*::after{box-sizing:border-box;}
        html,body{height:100%;margin:0;padding:0;overflow:hidden;background:#0a0a0a;font-family:sans-serif;}

        /* ── Top bar ── */
        #vc-topbar{
            height:44px;display:flex;align-items:center;justify-content:space-between;
            padding:0 12px;border-bottom:1px solid #21262d;
            background:#0d1117;flex-shrink:0;z-index:100;position:relative;
        }

        /* ── VS Code iframe fills remaining height ── */
        #vscode-frame{width:100%;height:100%;border:none;display:block;}
.workspace-container { display:flex; height:calc(100vh - 44px); width:100%; overflow:hidden; }
.file-explorer { width:250px; flex-shrink:0; background:#0d1117; border-right:1px solid #21262d; display:flex; flex-direction:column; }
.resizer { width:4px; background:#21262d; cursor:col-resize; flex-shrink:0; transition:background 0.2s; z-index:10; }
.resizer:hover, .resizer.dragging { background:#F05000; }
.center-pane { flex:1; min-width:0; position:relative; display:flex; flex-direction:column; }
#ai-panel { position:relative; right:auto; top:auto; height:100%; max-height:none; border-radius:0; border:none; border-left:1px solid #21262d; box-shadow:none; display:none; flex-shrink:0; }
#ai-panel:not(.hidden-panel) { display:flex; }

        /* ── Floating AI Panel ── */
        #ai-panel{
            position:fixed;right:20px;top:56px;
            width:360px;height:calc(100vh - 76px);max-height:700px;
            background:#111111;border:1px solid #21262d;
            border-radius:16px;display:flex;flex-direction:column;
            box-shadow:0 24px 64px rgba(0,0,0,.8),0 0 0 1px rgba(124,58,237,.15);
            z-index:9000;overflow:hidden;
            transition:transform .3s cubic-bezier(.16,1,.3,1),opacity .3s;
        }
        #ai-panel.hidden-panel{
            transform:translateX(380px);opacity:0;pointer-events:none;
        }

        /* AI Panel header */
        #ai-panel-header{
            display:flex;align-items:center;justify-content:space-between;
            padding:12px 14px;border-bottom:1px solid #21262d;flex-shrink:0;
            cursor:move;user-select:none;
        }

        /* Mode tabs */
        .mode-tab{
            flex:1;text-align:center;padding:5px 0;
            font-size:10px;font-weight:700;letter-spacing:.06em;
            border-radius:6px;cursor:pointer;transition:all .2s;
            color:#64748b;border:none;background:transparent;
        }
        .mode-tab.active{background:#7c3aed;color:#fff;box-shadow:0 0 12px rgba(124,58,237,.45);}

        /* Chat messages */
        #ai-messages{
            flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:10px;
        }
        #ai-messages::-webkit-scrollbar{width:4px;}
        #ai-messages::-webkit-scrollbar-track{background:transparent;}
        #ai-messages::-webkit-scrollbar-thumb{background:#2a2a3a;border-radius:2px;}

        .msg-user{
            align-self:flex-end;background:rgba(124,58,237,.18);
            border:1px solid rgba(124,58,237,.3);border-radius:12px 12px 2px 12px;
            padding:8px 12px;font-size:12px;color:#c4b5fd;max-width:90%;word-break:break-word;
        }
        .msg-ai{
            align-self:flex-start;background:#161b22;
            border:1px solid #21262d;border-radius:12px 12px 12px 2px;
            padding:8px 12px;font-size:12px;color:#94a3b8;max-width:95%;word-break:break-word;
        }
        .msg-ai code{
            background:#0a0a0a;border:1px solid #21262d;border-radius:4px;
            padding:1px 4px;font-family:'JetBrains Mono',monospace;font-size:11px;color:#a78bfa;
        }
        .msg-ai pre{
            background:#0a0a0a;border:1px solid #21262d;border-radius:8px;
            padding:10px;margin:6px 0;overflow-x:auto;
            font-family:'JetBrains Mono',monospace;font-size:11px;color:#94a3b8;
            white-space:pre-wrap;word-break:break-all;
        }
        .msg-thinking{
            align-self:flex-start;padding:8px 12px;
            font-size:11px;color:#64748b;
            display:flex;align-items:center;gap:6px;
        }
        .thinking-dot{
            width:5px;height:5px;border-radius:50%;background:#7c3aed;
            animation:thinkDot 1.4s ease-in-out infinite;
        }
        .thinking-dot:nth-child(2){animation-delay:.2s;}
        .thinking-dot:nth-child(3){animation-delay:.4s;}
        @keyframes thinkDot{0%,80%,100%{transform:scale(.7);opacity:.4}40%{transform:scale(1.1);opacity:1}}

        /* AI input area */
        #ai-input-area{
            padding:10px 12px;border-top:1px solid #21262d;flex-shrink:0;
        }
        #ai-textarea{
            width:100%;resize:none;background:#0a0a0a;
            border:1px solid #21262d;border-radius:10px;
            padding:8px 10px;font-size:12px;color:#f1f5f9;
            font-family:sans-serif;line-height:1.5;outline:none;
            transition:border-color .2s;
        }
        #ai-textarea:focus{border-color:rgba(124,58,237,.5);}
        #ai-textarea::placeholder{color:#64748b;}

        /* Diff overlay */
        #diff-overlay{
            display:none;position:fixed;inset:0;z-index:9999;
            background:rgba(0,0,0,.87);backdrop-filter:blur(8px);
            align-items:center;justify-content:center;
        }
        #diff-overlay.open{display:flex;}
        .diff-removed{background:rgba(255,0,0,.1);border-left:2px solid #ef4444;color:#fca5a5;}
        .diff-added{background:rgba(0,255,0,.08);border-left:2px solid #22c55e;color:#86efac;}
        .diff-context{color:#475569;}

        /* Presence avatars */
        .presence-avatar{
            width:24px;height:24px;border-radius:9999px;border:2px solid #0d1117;
            display:flex;align-items:center;justify-content:center;
            font-size:9px;font-weight:700;cursor:default;
        }

        /* Toggle AI button */
        #ai-toggle{
            display:flex;align-items:center;gap:6px;
            padding:5px 12px;border-radius:8px;border:none;cursor:pointer;
            font-size:11px;font-weight:700;letter-spacing:.04em;
            transition:all .2s;
        }
        #ai-toggle.panel-visible{
            background:rgba(124,58,237,.2);color:#a78bfa;
            border:1px solid rgba(124,58,237,.35);
        }
        #ai-toggle.panel-hidden{
            background:#7c3aed;color:#fff;
            border:1px solid rgba(124,58,237,.5);
            box-shadow:0 0 12px rgba(124,58,237,.4);
        }

        /* Collab modal */
        #collab-modal{
            display:none;position:fixed;inset:0;z-index:9998;
            background:rgba(0,0,0,.8);backdrop-filter:blur(8px);
            align-items:center;justify-content:center;
        }
        #collab-modal.open{display:flex;}

        /* Toast */
        #vc-toast{
            position:fixed;bottom:24px;right:24px;
            padding:10px 16px;border-radius:12px;
            background:#161b22;border:1px solid #21262d;
            font-size:12px;color:#f1f5f9;
            box-shadow:0 8px 32px rgba(0,0,0,.6);z-index:99999;
            opacity:0;transform:translateY(10px);
            transition:all .3s cubic-bezier(.16,1,.3,1);pointer-events:none;
            display:flex;align-items:center;gap:8px;
        }
        #vc-toast.show{opacity:1;transform:translateY(0);}
    </style>
</head>
<body class="h-full">

{{-- ══════════════════════ DIFF OVERLAY ══════════════════════ --}}
<div id="diff-overlay">
    <div style="width:100%;max-width:680px;margin:16px;background:#111111;border:1px solid #21262d;border-radius:20px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 40px 80px rgba(0,0,0,.9);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #21262d;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:28px;height:28px;border-radius:8px;background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.3);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:14px;height:14px;color:#a78bfa;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#f1f5f9;">AI Patch Preview</div>
                    <div id="diff-subtitle" style="font-size:11px;color:#64748b;">Review changes before applying</div>
                </div>
            </div>
            <button onclick="closeDiff()" style="background:none;border:none;cursor:pointer;color:#64748b;padding:4px;">
                <svg style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="diff-content" style="flex:1;overflow-y:auto;padding:8px 0;font-family:'JetBrains Mono',monospace;font-size:12px;line-height:1.7;"></div>
        <div style="display:flex;gap:10px;padding:16px 20px;border-top:1px solid #21262d;background:#0d1117;flex-shrink:0;">
            <button onclick="approvePatch()" style="flex:1;padding:10px;border-radius:10px;background:#7c3aed;color:#fff;font-size:13px;font-weight:700;border:none;cursor:pointer;box-shadow:0 0 16px rgba(124,58,237,.4);">
                ✓ Approve &amp; Apply
            </button>
            <button onclick="closeDiff()" style="padding:10px 20px;border-radius:10px;background:transparent;color:#64748b;font-size:13px;font-weight:700;border:1px solid #21262d;cursor:pointer;">
                Reject
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════ COLLABORATE MODAL ══════════════════════ --}}
<div id="collab-modal">
    <div style="width:100%;max-width:420px;margin:16px;background:#111111;border:1px solid #21262d;border-radius:20px;overflow:hidden;box-shadow:0 40px 80px rgba(0,0,0,.9);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #21262d;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:28px;height:28px;border-radius:8px;background:rgba(34,211,238,.12);border:1px solid rgba(34,211,238,.25);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:14px;height:14px;color:#22d3ee;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#f1f5f9;">Collaborate</div>
                    <div style="font-size:11px;color:#64748b;">Real-time shared workspace via Reverb</div>
                </div>
            </div>
            <button onclick="document.getElementById('collab-modal').classList.remove('open')" style="background:none;border:none;cursor:pointer;color:#64748b;">
                <svg style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div style="padding:16px 20px;display:flex;flex-direction:column;gap:14px;">
            <div id="collab-conn-status" style="display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:10px;background:#0a0a0a;border:1px solid #21262d;">
                <span id="collab-conn-dot" style="width:8px;height:8px;border-radius:50%;background:#64748b;flex-shrink:0;"></span>
                <span id="collab-conn-text" style="font-size:12px;color:#64748b;">Connecting to Reverb…</span>
            </div>
            <div style="background:#0a0a0a;border:1px solid #21262d;border-radius:10px;padding:12px;">
                <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Room Link</div>
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <code id="collab-room-slug" style="font-size:12px;color:#a78bfa;">{{ $roomSlug ?? 'personal' }}</code>
                    <button onclick="copyRoomLink()" style="font-size:11px;color:#64748b;background:none;border:none;cursor:pointer;">Copy Link</button>
                </div>
            </div>
            <div>
                <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Online Now</div>
                <div id="collab-member-list" style="display:flex;flex-direction:column;gap:6px;">
                    <div style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:10px;background:#0a0a0a;border:1px solid #21262d;">
                        <div class="presence-avatar" style="background:#7c3aed;color:#fff;box-shadow:0 0 8px rgba(124,58,237,.4);">{{ $user->avatar_initials }}</div>
                        <div>
                            <div style="font-size:12px;color:#f1f5f9;font-weight:600;">{{ $user->name }} <span style="color:#64748b;font-weight:400;">(you)</span></div>
                            <div style="font-size:10px;color:#64748b;">{{ ucfirst($user->role) }}</div>
                        </div>
                        <span style="margin-left:auto;font-size:10px;color:#4ade80;font-weight:700;">● Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════ TOP BAR ══════════════════════ --}}
<div id="vc-topbar">
    {{-- Left --}}
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:7px;text-decoration:none;">
            <div style="width:26px;height:26px;border-radius:8px;background:#7c3aed;display:flex;align-items:center;justify-content:center;box-shadow:0 0 10px rgba(124,58,237,.5);">
                <svg style="width:14px;height:14px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <span style="font-size:12px;font-weight:800;color:#fff;">Vision<span style="color:#a78bfa;">Code</span> AI</span>
        </a>

        <span style="color:#21262d;font-size:14px;">|</span>

        {{-- VS Code status indicator --}}
        <div style="display:flex;align-items:center;gap:5px;">
            <span id="vsc-dot" style="width:7px;height:7px;border-radius:50%;background:#64748b;transition:background .3s;"></span>
            <span id="vsc-label" style="font-size:11px;color:#64748b;">Loading VS Code…</span>
        </div>

        @if($isCollaborative ?? false)
        <span style="display:flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:rgba(34,197,94,.1);color:#4ade80;border:1px solid rgba(34,197,94,.25);">
            <span style="width:5px;height:5px;border-radius:50%;background:#4ade80;animation:livePulse 2s ease-in-out infinite;"></span>
            Live Session
        </span>
        @endif
    </div>

    {{-- Right --}}
    <div style="display:flex;align-items:center;gap:8px;">

        {{-- Presence avatars --}}
        <div id="presence-bar" style="display:flex;align-items:center;margin-right:2px;">
            <div class="presence-avatar" title="{{ $user->name }}" style="background:#7c3aed;color:#fff;box-shadow:0 0 6px rgba(124,58,237,.4);">{{ $user->avatar_initials }}</div>
        </div>

        {{-- Reverb status --}}
        <div style="display:flex;align-items:center;gap:4px;padding:3px 8px;border-radius:6px;border:1px solid #21262d;background:#0a0a0a;">
            <span id="reverb-dot" style="width:6px;height:6px;border-radius:50%;background:#64748b;"></span>
            <span id="reverb-label" style="font-size:10px;color:#64748b;">Reverb</span>
        </div>

        {{-- Collaborate --}}
        <button onclick="document.getElementById('collab-modal').classList.add('open')"
            style="display:flex;align-items:center;gap:5px;padding:5px 10px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;background:rgba(34,211,238,.07);color:#22d3ee;border:1px solid rgba(34,211,238,.25);transition:all .2s;">
            <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Collaborate
        </button>

        {{-- Video Call --}}
        <button id="video-btn" onclick="startVideoCall()"
            style="display:flex;align-items:center;gap:5px;padding:5px 10px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;background:rgba(16,185,129,.07);color:#4ade80;border:1px solid rgba(16,185,129,.25);transition:all .2s;">
            <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Video
        </button>

        {{-- AI Toggle --}}
        <button id="ai-toggle" onclick="toggleAiPanel()" class="panel-hidden">
            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            AI Agent
        </button>

        {{-- User menu --}}
        <div style="position:relative;" x-data="{open:false}">
            <button @click="open=!open" style="display:flex;align-items:center;gap:6px;background:none;border:none;cursor:pointer;">
                <div style="width:26px;height:26px;border-radius:50%;background:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;">{{ $user->avatar_initials }}</div>
            </button>
            <div x-show="open" @click.away="open=false" x-transition
                 style="position:absolute;right:0;top:36px;width:200px;background:#161b22;border:1px solid #21262d;border-radius:12px;overflow:hidden;box-shadow:0 16px 40px rgba(0,0,0,.7);font-size:12px;z-index:200;">
                <div style="padding:12px 14px;border-bottom:1px solid #21262d;">
                    <div style="font-weight:700;color:#f1f5f9;">{{ $user->name }}</div>
                    <div style="color:#64748b;font-size:11px;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $user->email }}</div>
                    <span style="display:inline-block;margin-top:4px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:rgba(124,58,237,.12);color:#a78bfa;border:1px solid rgba(124,58,237,.25);">{{ ucfirst($user->role) }}</span>
                </div>
                <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:7px;padding:9px 14px;color:#94a3b8;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.04)'" onmouseout="this.style.background='transparent'">
                    <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile Settings
                </a>
                @if($user->isAdmin())
                <a href="{{ route('admin.analytics') }}" style="display:flex;align-items:center;gap:7px;padding:9px 14px;color:#94a3b8;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.04)'" onmouseout="this.style.background='transparent'">
                    <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Analytics
                </a>
                <a href="{{ route('demo') }}" style="display:flex;align-items:center;gap:7px;padding:9px 14px;color:#94a3b8;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.04)'" onmouseout="this.style.background='transparent'">
                    <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                    Demo Script
                </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="border-top:1px solid #21262d;">
                    @csrf
                    <button type="submit" style="width:100%;display:flex;align-items:center;gap:7px;padding:9px 14px;color:#f87171;background:none;border:none;cursor:pointer;font-size:12px;transition:background .15s;" onmouseover="this.style.background='rgba(248,113,113,.07)'" onmouseout="this.style.background='transparent'">
                        <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════ VS CODE IFRAME ══════════════════════ --}}
{{-- Use the vscodeUrl provided by the CodeServerManager --}}

<div class="workspace-container">
    <!-- File Explorer -->
    <div id="file-explorer" class="file-explorer">
        <div style="padding:12px 14px; border-bottom:1px solid #21262d; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; display:flex; justify-content:space-between; align-items:center;">
            <span>Explorer</span>
            <button onclick="fetchFiles()" style="background:none;border:none;color:#64748b;cursor:pointer;" title="Refresh">↻</button>
        </div>
        <div id="file-tree" style="flex:1; overflow-y:auto; padding:8px 0; font-family:'JetBrains Mono',monospace; font-size:12px;">
            <div style="padding:10px; text-align:center; color:#64748b;">Loading files...</div>
        </div>
    </div>
    <div class="resizer" id="resizer-left"></div>
    <!-- Center Pane -->
    <div class="center-pane">
        <iframe id="vscode-frame"
        src="{{ $vscodeUrl }}"
        allow="clipboard-read; clipboard-write"
        sandbox="allow-scripts allow-same-origin allow-forms allow-modals allow-popups allow-popups-to-escape-sandbox"
        onload="onVsCodeLoad()"
        onerror="onVsCodeError()">
</iframe>
        <div id="vscode-fallback" style="display:none;position:absolute;inset:0;background:#0a0a0a;align-items:center;justify-content:center;flex-direction:column;gap:16px;z-index:50;">
            <div style="width:56px;height:56px;border-radius:16px;background:#F05000;display:flex;align-items:center;justify-content:center;box-shadow:0 0 24px rgba(240,80,0,.5);">
                <svg style="width:28px;height:28px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <div style="text-align:center;">
                <div style="font-size:15px;font-weight:700;color:#f1f5f9;margin-bottom:6px;">Starting VS Code Server…</div>
                <div style="font-size:12px;color:#64748b;max-width:360px;line-height:1.6;">The container is booting. This takes a few seconds on first start.</div>
            </div>
            <button onclick="reloadVsCode()" style="padding:9px 20px;border-radius:10px;background:#F05000;color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;box-shadow:0 0 12px rgba(240,80,0,.4);">
                Reload VS Code
            </button>
        </div>
    </div>
    <div class="resizer" id="resizer-right" style="display:none;"></div>


    <button onclick="reloadVsCode()" style="padding:9px 20px;border-radius:10px;background:#7c3aed;color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;box-shadow:0 0 12px rgba(124,58,237,.4);">
        Reload VS Code
    </button>
</div>

{{-- ══════════════════════ FLOATING AI PANEL ══════════════════════ --}}
<!-- AI Panel (Right Sidebar) -->
<div id="ai-panel" class="hidden-panel">

    {{-- Header --}}
    <div id="ai-panel-header">
        <div style="display:flex;align-items:center;gap:8px;">
            <div style="width:28px;height:28px;border-radius:8px;background:rgba(124,58,237,.2);border:1px solid rgba(124,58,237,.35);display:flex;align-items:center;justify-content:center;">
                <svg style="width:14px;height:14px;color:#a78bfa;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div>
                <div style="font-size:13px;font-weight:700;color:#f1f5f9;">AI Agent</div>
                <div id="ai-model-label" style="font-size:10px;color:#64748b;">Gemini 2.0 Flash</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <button onclick="clearChat()" title="Clear chat" style="background:none;border:none;cursor:pointer;color:#64748b;padding:3px;" title="Clear">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
            <button onclick="toggleAiPanel()" style="background:none;border:none;cursor:pointer;color:#64748b;padding:3px;">
                <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Mode tabs --}}
    <div style="display:flex;gap:4px;padding:8px 12px;border-bottom:1px solid #21262d;flex-shrink:0;background:#0d1117;">
        <button class="mode-tab active" id="tab-CHAT" onclick="setMode('CHAT')">CHAT</button>
        <button class="mode-tab" id="tab-PLAN" onclick="setMode('PLAN')">PLAN</button>
        <button class="mode-tab" id="tab-AGENT" onclick="setMode('AGENT')">AGENT</button>
    </div>

    {{-- Mode description --}}
    <div id="mode-desc" style="padding:6px 12px;font-size:10px;color:#64748b;border-bottom:1px solid #1a1f26;background:#0a0a0a;flex-shrink:0;">
        Ask questions, explain code, get suggestions
    </div>

    {{-- Messages --}}
    <div id="ai-messages">
        <div class="msg-ai">
            <strong style="color:#a78bfa;">VisionLab</strong> ready!<br>
            Paste or describe code and I'll help. Switch to <strong>AGENT</strong> mode for auto-patching.
        </div>
    </div>

    {{-- Input area --}}
    <div id="ai-input-area">
        <textarea id="ai-textarea" rows="3"
            placeholder="Ask anything about code…  (Enter to send, Shift+Enter for newline)"
            onkeydown="handleAiKey(event)"></textarea>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
            <span id="ai-context-label" style="font-size:10px;color:#64748b;">No file context</span>
            <button onclick="sendMessage()" style="padding:5px 14px;border-radius:8px;background:#7c3aed;color:#fff;font-size:11px;font-weight:700;border:none;cursor:pointer;box-shadow:0 0 8px rgba(124,58,237,.3);">Send ↑</button>
        </div>
    </div>
</div>

</div><!-- End workspace-container -->

{{-- ══════════════════════ VIDEO CALL MODAL ══════════════════════ --}}
<div id="video-modal" style="display:none;position:fixed;inset:0;z-index:9997;background:rgba(0,0,0,.92);backdrop-filter:blur(12px);flex-direction:column;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px solid #21262d;flex-shrink:0;background:#0d1117;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#4ade80;animation:livePulse 2s ease-in-out infinite;"></div>
            <span id="video-title" style="font-size:13px;font-weight:700;color:#f1f5f9;">Video Call Active</span>
            <span id="video-participants" style="font-size:11px;color:#64748b;">0 participants</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <button onclick="minimizeVideo()" style="display:flex;align-items:center;gap:4px;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;background:rgba(255,255,255,.05);color:#94a3b8;border:1px solid #21262d;">
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                Minimize
            </button>
            <button onclick="endVideoCall()" style="display:flex;align-items:center;gap:4px;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;background:rgba(239,68,68,.12);color:#f87171;border:1px solid rgba(239,68,68,.3);">
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                End Call
            </button>
        </div>
    </div>
    <div id="jitsi-container" style="flex:1;background:#000;"></div>
</div>

{{-- Minimised video pill --}}
<div id="video-pill" style="display:none;position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:9996;padding:8px 20px;border-radius:20px;background:#161b22;border:1px solid rgba(16,185,129,.3);box-shadow:0 8px 32px rgba(0,0,0,.6);cursor:pointer;display:flex;align-items:center;gap:8px;" onclick="maximizeVideo()">
    <span style="width:8px;height:8px;border-radius:50%;background:#4ade80;animation:livePulse 2s ease-in-out infinite;"></span>
    <span style="font-size:12px;font-weight:700;color:#f1f5f9;">Video Call Active</span>
    <span style="font-size:11px;color:#64748b;">Click to expand</span>
</div>

{{-- Toast --}}
<div id="vc-toast"></div>

<style>
@keyframes livePulse{0%,100%{opacity:1}50%{opacity:.4}}
</style>

<script>
// ─── Config ──────────────────────────────────────────────────────
const VC = {
    csrf:         '{{ csrf_token() }}',
    apiBase:      '/api',
    user:         { name:'{{ $user->name }}', role:'{{ $user->role }}', initials:'{{ $user->avatar_initials }}' },
    roomSlug:     '{{ $roomSlug ?? "personal-" . $user->id }}',
    isCollab:     {{ ($isCollaborative ?? false) ? 'true' : 'false' }},
    reverb: {
        key:    '{{ $reverbConfig["key"] }}',
        host:   '{{ $reverbConfig["host"] }}',
        port:    {{ $reverbConfig["port"] }},
        scheme: '{{ $reverbConfig["scheme"] }}',
    },
};

// ─── VS Code iframe lifecycle ─────────────────────────────────────
let vscLoaded = false;
function onVsCodeLoad(){
    vscLoaded = true;
    document.getElementById('vscode-fallback').style.display = 'none';
    document.getElementById('vsc-dot').style.background = '#4ade80';
    document.getElementById('vsc-label').textContent = 'VS Code ready';
    document.getElementById('vsc-label').style.color = '#4ade80';
}
function onVsCodeError(){
    document.getElementById('vscode-fallback').style.display = 'flex';
    document.getElementById('vsc-dot').style.background = '#f87171';
    document.getElementById('vsc-label').textContent = 'VS Code offline';
    document.getElementById('vsc-label').style.color  = '#f87171';
}
function reloadVsCode(){
    const frame = document.getElementById('vscode-frame');
    frame.src = frame.src;
    document.getElementById('vsc-dot').style.background = '#fbbf24';
    document.getElementById('vsc-label').textContent = 'Reloading…';
    document.getElementById('vsc-label').style.color  = '#fbbf24';
}

// Auto-check if iframe loaded (fallback for onerror not always firing)
setTimeout(()=>{
    if (!vscLoaded){
        document.getElementById('vscode-fallback').style.display = 'flex';
        document.getElementById('vsc-dot').style.background = '#fbbf24';
        document.getElementById('vsc-label').textContent = 'Starting…';
    }
}, 5000);
setTimeout(()=>{
    if (!vscLoaded){
        document.getElementById('vsc-dot').style.background = '#f87171';
        document.getElementById('vsc-label').textContent = 'Check VS Code workflow';
    }
}, 20000);

// ─── AI Panel ─────────────────────────────────────────────────────
let panelVisible = false;
let aiMode = 'CHAT';
let pendingPatch = null;

function toggleAiPanel(){
    panelVisible = !panelVisible;
    const panel  = document.getElementById('ai-panel');
    const toggle = document.getElementById('ai-toggle');
    panel.classList.toggle('hidden-panel', !panelVisible);
    toggle.classList.toggle('panel-visible', panelVisible);
    toggle.classList.toggle('panel-hidden',  !panelVisible);
    if (panelVisible) document.getElementById('ai-textarea').focus();
}

function setMode(m){
    aiMode = m;
    document.querySelectorAll('.mode-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-'+m).classList.add('active');
    const descs = {
        CHAT:  'Ask questions, explain code, get suggestions',
        PLAN:  'Generate a structured implementation plan with steps & estimates',
        AGENT: 'Auto-generate a patch diff — review & apply with one click',
    };
    document.getElementById('mode-desc').textContent = descs[m];
}

function clearChat(){
    const msgs = document.getElementById('ai-messages');
    msgs.innerHTML = '<div class="msg-ai"><strong style="color:#a78bfa;">VisionLab</strong> ready! Paste or describe code and I\'ll help.</div>';
    pendingPatch = null;
}

function handleAiKey(e){
    if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendMessage(); }
}

async function sendMessage(){
    const ta  = document.getElementById('ai-textarea');
    const msg = ta.value.trim();
    if(!msg) return;
    ta.value = '';

    appendMsg('user', msg);
    const thinkId = appendThinking();

    try {
        const res = await fetch(`${VC.apiBase}/ai/chat`, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':VC.csrf,'Accept':'application/json'},
            body: JSON.stringify({ message: msg, mode: aiMode, context: { filename:'', language:'', code:'' } }),
            credentials: 'same-origin',
        });
        removeThinking(thinkId);

        if(!res.ok){ appendMsg('ai', `⚠️ Error ${res.status} — make sure you're logged in.`); return; }
        const data = await res.json();

        if(data.patch && aiMode === 'AGENT'){
            pendingPatch = data.patch;
            appendMsg('ai', (data.explanation || 'Patch ready.') + '\n\n<em style="color:#a78bfa;font-size:11px;">↓ Patch preview below</em>');
            showDiff(data.patch);
        } else {
            appendMsg('ai', data.reply || data.message || 'Done.');
        }
    } catch(err){
        removeThinking(thinkId);
        appendMsg('ai', '⚠️ Network error — check connection.');
    }
}

function appendMsg(role, text){
    const msgs = document.getElementById('ai-messages');
    const div  = document.createElement('div');
    div.className = role === 'user' ? 'msg-user' : 'msg-ai';

    // Basic markdown-ish rendering
    let html = esc(text)
        .replace(/```([\s\S]*?)```/g, '<pre>$1</pre>')
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/\*\*(.+?)\*\*/g, '<strong style="color:#c4b5fd;">$1</strong>')
        .replace(/\n/g, '<br>');

    // Allow <em> and <strong> passthrough (from our own code above)
    div.innerHTML = html;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
}

function appendThinking(){
    const msgs = document.getElementById('ai-messages');
    const id   = 'think-' + Date.now();
    const div  = document.createElement('div');
    div.id = id; div.className = 'msg-thinking';
    div.innerHTML = '<span class="thinking-dot"></span><span class="thinking-dot"></span><span class="thinking-dot"></span><span style="margin-left:4px;font-size:11px;color:#64748b;">Thinking…</span>';
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return id;
}
function removeThinking(id){ document.getElementById(id)?.remove(); }

function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ─── Diff overlay ──────────────────────────────────────────────────
function showDiff(patch){
    const overlay = document.getElementById('diff-overlay');
    const content = document.getElementById('diff-content');
    if(!patch){ return; }

    const orig    = (patch.original||'').split('\n');
    const patched = (patch.patched||'').split('\n');

    let html = '';
    const maxLines = Math.max(orig.length, patched.length);
    for(let i=0; i<maxLines; i++){
        const o = orig[i]??'';
        const p = patched[i]??'';
        if(o===p){
            html += `<div class="diff-context" style="padding:1px 16px;">${esc(o)||'&nbsp;'}</div>`;
        } else {
            if(o) html += `<div class="diff-removed" style="padding:1px 16px;">- ${esc(o)}</div>`;
            if(p) html += `<div class="diff-added"   style="padding:1px 16px;">+ ${esc(p)}</div>`;
        }
    }
    content.innerHTML = html;
    document.getElementById('diff-subtitle').textContent = `${patch.filename||'file'} · ${patched.length} lines`;
    overlay.classList.add('open');
}

function closeDiff(){
    document.getElementById('diff-overlay').classList.remove('open');
}

async function approvePatch(){
    if(!pendingPatch){ closeDiff(); return; }
    try {
        const res = await fetch(`${VC.apiBase}/ai/apply-patch`, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':VC.csrf,'Accept':'application/json'},
            body: JSON.stringify({ patch: pendingPatch }),
            credentials:'same-origin',
        });
        if(res.ok){
            toast('Patch applied! Saved to project.', 'success');
            appendMsg('ai', '✅ Patch applied successfully.');
        } else {
            toast('Patch apply failed.', 'error');
        }
    } catch(_){ toast('Network error.','error'); }
    closeDiff();
    pendingPatch = null;
}

// ─── Reverb / Echo ────────────────────────────────────────────────
let echoChannel = null;
function initReverb(){
    if(typeof Echo === 'undefined') return setTimeout(initReverb, 1000);
    try {
        echoChannel = Echo.join(`collab.${VC.roomSlug}`)
            .here(users => updatePresence(users))
            .joining(user => { updatePresence(null, 'join', user); toast(`${user.name} joined`, 'info'); })
            .leaving(user => { updatePresence(null, 'leave', user); toast(`${user.name} left`, 'warn'); })
            .error(()=> setReverbStatus(false));
        setReverbStatus(true);
    } catch(e){ setReverbStatus(false); }
}

let presenceUsers = [];
function updatePresence(users, action, u){
    if(users) presenceUsers = users;
    else if(action==='join' && !presenceUsers.find(x=>x.id===u.id)) presenceUsers.push(u);
    else if(action==='leave') presenceUsers = presenceUsers.filter(x=>x.id!==u.id);

    // Topbar avatars
    const bar = document.getElementById('presence-bar');
    const colors = ['#7c3aed','#0891b2','#16a34a','#d97706','#dc2626','#db2777'];
    bar.innerHTML = presenceUsers.map((u,i)=>
        `<div class="presence-avatar" title="${esc(u.name)}" style="background:${colors[i%colors.length]};color:#fff;border:2px solid #0d1117;${i?'margin-left:-6px':''}">${esc((u.initials||u.name||'?').slice(0,2).toUpperCase())}</div>`
    ).join('');

    // Collab modal member list
    const list = document.getElementById('collab-member-list');
    list.innerHTML = presenceUsers.map((u,i)=>`
        <div style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:10px;background:#0a0a0a;border:1px solid #21262d;">
            <div class="presence-avatar" style="background:${colors[i%colors.length]};color:#fff;flex-shrink:0;">${esc((u.initials||u.name||'?').slice(0,2).toUpperCase())}</div>
            <div>
                <div style="font-size:12px;color:#f1f5f9;font-weight:600;">${esc(u.name)}</div>
                <div style="font-size:10px;color:#64748b;">${esc(u.role||'')}</div>
            </div>
            <span style="margin-left:auto;font-size:10px;color:#4ade80;font-weight:700;">● Online</span>
        </div>
    `).join('');
}

function setReverbStatus(connected){
    document.getElementById('reverb-dot').style.background   = connected ? '#4ade80' : '#64748b';
    document.getElementById('reverb-label').textContent      = connected ? 'Live' : 'Offline';
    document.getElementById('reverb-label').style.color      = connected ? '#4ade80' : '#64748b';
    document.getElementById('collab-conn-dot').style.background = connected ? '#4ade80' : '#f87171';
    document.getElementById('collab-conn-text').textContent  = connected ? 'Connected to Reverb' : 'Reverb offline — run: php artisan reverb:start';
}

function copyRoomLink(){
    navigator.clipboard.writeText(window.location.origin + '/workspace/' + VC.roomSlug)
        .then(()=>toast('Room link copied!','success'))
        .catch(()=>toast('Copy failed','error'));
}

// ─── Toast ────────────────────────────────────────────────────────
function toast(msg, type='info'){
    const el = document.getElementById('vc-toast');
    const c  = {success:'#4ade80',error:'#f87171',warn:'#fbbf24',info:'#a78bfa'};
    el.innerHTML = `<span style="width:8px;height:8px;border-radius:50%;background:${c[type]||c.info};flex-shrink:0;"></span>${esc(msg)}`;
    el.classList.add('show');
    setTimeout(()=>el.classList.remove('show'), 3000);
}

// ─── Boot ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Load Laravel Echo + Pusher for Reverb
    const scriptP = document.createElement('script');
    scriptP.src = 'https://js.pusher.com/8.2/pusher.min.js';
    scriptP.onload = () => {
        const scriptE = document.createElement('script');
        scriptE.src = 'https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js';
        scriptE.onload = () => {
            window.Echo = new Echo({
                broadcaster:   'reverb',
                key:            VC.reverb.key,
                wsHost:         VC.reverb.host,
                wsPort:         VC.reverb.port,
                wssPort:        VC.reverb.port,
                forceTLS:       VC.reverb.scheme === 'https',
                enabledTransports: ['ws', 'wss'],
            });
            initReverb();
        };
        document.head.appendChild(scriptE);
    };
    document.head.appendChild(scriptP);

    // Keyboard shortcut: Ctrl+Shift+A = toggle AI panel, Ctrl+Shift+V = video
    document.addEventListener('keydown', e => {
        if(e.ctrlKey && e.shiftKey && e.key === 'A'){ e.preventDefault(); toggleAiPanel(); }
        if(e.ctrlKey && e.shiftKey && e.key === 'V'){ e.preventDefault(); startVideoCall(); }
        if(e.key === 'Escape'){ closeDiff(); }
    });
});

// ─── Video Call (Jitsi Meet) ──────────────────────────────────────
let jitsiApi = null;
let videoActive = false;

async function startVideoCall(){
    if(videoActive){ maximizeVideo(); return; }

    const btn = document.getElementById('video-btn');
    btn.style.color = '#fbbf24'; btn.textContent = 'Starting…';

    try {
        const res = await fetch(`${VC.apiBase}/workspace/${VC.roomSlug}/video/start`, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':VC.csrf,'Accept':'application/json'},
            credentials:'same-origin',
        });
        if(!res.ok){ toast('Failed to start video call','error'); resetVideoBtn(); return; }

        const data = await res.json();
        if(!data.active){ toast('Could not start call','error'); resetVideoBtn(); return; }

        videoActive = true;
        btn.style.color = '#4ade80';
        btn.innerHTML = '<svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Live';
        btn.style.borderColor = 'rgba(16,185,129,.5)';

        // Load Jitsi External API
        if(!window.JitsiMeetExternalAPI){
            const s = document.createElement('script');
            s.src = `https://${data.jitsi_domain}/external_api.js`;
            s.onload = () => launchJitsi(data);
            document.head.appendChild(s);
        } else {
            launchJitsi(data);
        }
    } catch(e){
        toast('Network error starting call','error');
        resetVideoBtn();
    }
}

function launchJitsi(data){
    const modal = document.getElementById('video-modal');
    modal.style.display = 'flex';

    jitsiApi = new JitsiMeetExternalAPI(data.jitsi_domain, {
        roomName: data.room_name,
        parentNode: document.getElementById('jitsi-container'),
        jwt: data.jwt || undefined,
        configOverwrite: {
            startWithAudioMuted: false,
            startWithVideoMuted: false,
            prejoinPageEnabled: false,
            disableModeratorIndicator: false,
            disableThirdPartyRequests: true,
            toolbarButtons: [
                'microphone','camera','closedcaptions','desktop',
                'chat','raisehand','tileview','hangup',
            ],
        },
        interfaceConfigOverwrite: {
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            DEFAULT_BACKGROUND: '#0a0a0a',
            TOOLBAR_ALWAYS_VISIBLE: true,
        },
        userInfo: { displayName: VC.user.name },
    });

    jitsiApi.addEventListener('readyToClose', ()=> endVideoCall());
    jitsiApi.addEventListener('participantJoined', ()=> updateVideoParticipants());
    jitsiApi.addEventListener('participantLeft', ()=> updateVideoParticipants());

    toast(`Video call started by ${data.starter?.name || 'you'}`, 'success');
}

function updateVideoParticipants(){
    if(!jitsiApi) return;
    const count = jitsiApi.getNumberOfParticipants();
    document.getElementById('video-participants').textContent = `${count} participant${count!==1?'s':''}`;
}

function minimizeVideo(){
    document.getElementById('video-modal').style.display = 'none';
    const pill = document.getElementById('video-pill');
    pill.style.display = 'flex';
}

function maximizeVideo(){
    document.getElementById('video-pill').style.display = 'none';
    document.getElementById('video-modal').style.display = 'flex';
}

async function endVideoCall(){
    if(jitsiApi){ jitsiApi.dispose(); jitsiApi = null; }
    videoActive = false;
    document.getElementById('video-modal').style.display = 'none';
    document.getElementById('video-pill').style.display = 'none';
    document.getElementById('jitsi-container').innerHTML = '';
    resetVideoBtn();

    try {
        await fetch(`${VC.apiBase}/workspace/${VC.roomSlug}/video/end`, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':VC.csrf,'Accept':'application/json'},
            credentials:'same-origin',
        });
    } catch(_){}

    toast('Video call ended', 'info');
}

function resetVideoBtn(){
    const btn = document.getElementById('video-btn');
    btn.style.color = '#4ade80';
    btn.style.borderColor = 'rgba(16,185,129,.25)';
    btn.innerHTML = '<svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Video';
}

// ─── Workspace Resizer & File Explorer ───────────────────────────
function initResizers() {
    const leftResizer = document.getElementById('resizer-left');
    const rightResizer = document.getElementById('resizer-right');
    const fileExplorer = document.getElementById('file-explorer');
    const aiPanel = document.getElementById('ai-panel');
    const centerPane = document.querySelector('.center-pane');

    let isResizingLeft = false;
    let isResizingRight = false;

    leftResizer.addEventListener('mousedown', (e) => {
        isResizingLeft = true;
        leftResizer.classList.add('dragging');
        document.body.style.cursor = 'col-resize';
        // Add a temporary overlay to iframe to prevent pointer events stealing
        let overlay = document.createElement('div');
        overlay.id = 'iframe-blocker';
        overlay.style.position = 'absolute';
        overlay.style.inset = '0';
        overlay.style.zIndex = '9999';
        centerPane.appendChild(overlay);
    });

    rightResizer.addEventListener('mousedown', (e) => {
        isResizingRight = true;
        rightResizer.classList.add('dragging');
        document.body.style.cursor = 'col-resize';
        let overlay = document.createElement('div');
        overlay.id = 'iframe-blocker';
        overlay.style.position = 'absolute';
        overlay.style.inset = '0';
        overlay.style.zIndex = '9999';
        centerPane.appendChild(overlay);
    });

    document.addEventListener('mousemove', (e) => {
        if (!isResizingLeft && !isResizingRight) return;
        
        if (isResizingLeft) {
            let newWidth = e.clientX;
            if (newWidth < 150) newWidth = 150;
            if (newWidth > 400) newWidth = 400;
            fileExplorer.style.width = `${newWidth}px`;
        }
        
        if (isResizingRight) {
            let newWidth = window.innerWidth - e.clientX;
            if (newWidth < 250) newWidth = 250;
            if (newWidth > 600) newWidth = 600;
            aiPanel.style.width = `${newWidth}px`;
        }
    });

    document.addEventListener('mouseup', () => {
        if (isResizingLeft) {
            isResizingLeft = false;
            leftResizer.classList.remove('dragging');
        }
        if (isResizingRight) {
            isResizingRight = false;
            rightResizer.classList.remove('dragging');
        }
        document.body.style.cursor = 'default';
        const blocker = document.getElementById('iframe-blocker');
        if (blocker) blocker.remove();
    });
}

async function fetchFiles() {
    const treeDiv = document.getElementById('file-tree');
    try {
        const res = await fetch(`${VC.apiBase}/workspace/${VC.roomSlug}/files`, {
            headers: {'Accept':'application/json'}
        });
        if (!res.ok) throw new Error('Failed to load');
        const data = await res.json();
        
        if (!data.files || data.files.length === 0) {
            treeDiv.innerHTML = '<div style="padding:10px; color:#64748b; text-align:center;">No files found</div>';
            return;
        }
        
        treeDiv.innerHTML = renderTree(data.files);
    } catch(e) {
        treeDiv.innerHTML = '<div style="padding:10px; color:#f87171; text-align:center;">Failed to load files</div>';
    }
}

function renderTree(files, padding = 12) {
    let html = '';
    for (const f of files) {
        if (f.type === 'directory') {
            html += `<div style="padding: 4px 12px 4px ${padding}px; display:flex; align-items:center; gap:6px; color:#94a3b8; cursor:pointer;" onmouseover="this.style.background='#161b22'" onmouseout="this.style.background='transparent'">
                <span style="color:#F05000">📁</span> ${f.name}
            </div>`;
            if (f.children && f.children.length > 0) {
                html += renderTree(f.children, padding + 16);
            }
        } else {
            let icon = '📄';
            if (f.name.endsWith('.py')) icon = '🐍';
            else if (f.name.endsWith('.js') || f.name.endsWith('.ts')) icon = '📜';
            else if (f.name.endsWith('.php')) icon = '🐘';
            else if (f.name.endsWith('.md')) icon = '📝';
            else if (f.name.endsWith('.json')) icon = '⚙️';
            
            html += `<div style="padding: 4px 12px 4px ${padding}px; display:flex; align-items:center; gap:6px; color:#f1f5f9; cursor:pointer;" onmouseover="this.style.background='#161b22'" onmouseout="this.style.background='transparent'">
                <span>${icon}</span> ${f.name}
            </div>`;
        }
    }
    return html;
}

// Hook into DOMContentLoaded to init
document.addEventListener('DOMContentLoaded', () => {
    initResizers();
    fetchFiles();
    
    // Patch toggleAiPanel to also show/hide right resizer
    const origToggle = toggleAiPanel;
    toggleAiPanel = function() {
        origToggle();
        const resizer = document.getElementById('resizer-right');
        if (panelVisible) {
            resizer.style.display = 'block';
        } else {
            resizer.style.display = 'none';
        }
    };
});
</script>


</body>
</html>

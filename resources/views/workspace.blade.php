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
        .workspace-container { display:flex; height:calc(100vh - 44px); width:100%; overflow:hidden; }
        .center-pane { flex:1; min-width:0; position:relative; display:flex; flex-direction:column; }
        #vscode-frame{width:100%;height:100%;border:none;display:block;}

        /* Presence avatars */
        .presence-avatar{
            width:24px;height:24px;border-radius:9999px;border:2px solid #0d1117;
            display:flex;align-items:center;justify-content:center;
            font-size:9px;font-weight:700;cursor:default;
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
        /* ── Preloader (Firebase Studio Style) ── */
        #vc-preloader {
            position: fixed; inset: 0; z-index: 999999;
            background: #151515;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            transition: opacity 0.5s ease, visibility 0.5s;
            font-family: 'Inter', sans-serif;
        }
        #vc-preloader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
        
        .p-box {
            display: flex; align-items: center; gap: 16px;
            width: 340px; padding: 14px 16px;
            background: transparent;
            border: 1px solid #333;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .p-icon {
            width: 32px; height: 32px; border-radius: 6px;
            background: #F05000; /* Firebase orange vibe */
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(240,80,0,0.2);
        }
        .p-icon svg { width: 18px; height: 18px; color: #fff; }
        .p-info { display: flex; flex-direction: column; gap: 2px; }
        .p-title { font-size: 13px; font-weight: 600; color: #8ab4f8; }
        .p-id { font-size: 11px; color: #9aa0a6; font-family: monospace; }

        .p-steps { display: flex; flex-direction: column; gap: 12px; width: 340px; padding: 0 4px; }
        .p-step { display: flex; align-items: center; gap: 14px; transition: all 0.3s ease; }
        .p-step-text { font-size: 12px; font-weight: 500; }
        .p-step-icon { width: 14px; height: 14px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }

        /* States */
        .p-step.pending .p-step-text { color: #5f6368; }
        .p-step.active .p-step-text { color: #e8eaed; }
        .p-step.completed .p-step-text { color: #5f6368; }

        .p-step.completed .p-step-icon svg { width: 12px; height: 12px; color: #5f6368; }

        .p-spinner {
            width: 14px; height: 14px; flex-shrink: 0;
            border: 2px solid transparent;
            border-top-color: #8ab4f8; /* Google blue */
            border-left-color: #8ab4f8;
            border-radius: 50%;
            animation: p-spin 0.8s linear infinite;
            display: none;
        }
        .p-step.active .p-spinner { display: block; }
        .p-step.active .p-step-icon { display: none; }

        @keyframes p-spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="h-full">

{{-- ══════════════════════ PRELOADER ══════════════════════ --}}
<div id="vc-preloader">
    <div class="p-box">
        <div class="p-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        </div>
        <div class="p-info">
            <div class="p-title">{{ $workspaceName ?? 'VisionLab Workspace' }}</div>
            <div class="p-id">{{ $roomSlug ?? 'workspace-id' }}</div>
        </div>
    </div>
    
    <div class="p-steps">
        <div class="p-step active" id="p-step-1">
            <div class="p-spinner"></div>
            <div class="p-step-icon"></div>
            <div class="p-step-text">Setting up workspace</div>
        </div>
        <div class="p-step pending" id="p-step-2">
            <div class="p-spinner"></div>
            <div class="p-step-icon"></div>
            <div class="p-step-text">Initializing environment</div>
        </div>
        <div class="p-step pending" id="p-step-3">
            <div class="p-spinner"></div>
            <div class="p-step-icon"></div>
            <div class="p-step-text">Building environment</div>
        </div>
        <div class="p-step pending" id="p-step-4">
            <div class="p-spinner"></div>
            <div class="p-step-icon"></div>
            <div class="p-step-text">Finalizing</div>
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

        {{-- Deploy to Live --}}
        <button onclick="deployWorkspace()"
            style="display:flex;align-items:center;gap:5px;padding:5px 10px;border-radius:8px;font-size:11px;font-weight:800;cursor:pointer;background:linear-gradient(135deg, #a855f7 0%, #ec4899 100%);color:#fff;border:none;box-shadow:0 0 10px rgba(168,85,247,.4);transition:all .2s;">
            <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Deploy
        </button>

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
<div class="workspace-container">
    <div class="center-pane">
        <iframe id="vscode-frame"
            allow="clipboard-read; clipboard-write; microphone; camera"
            sandbox="allow-scripts allow-same-origin allow-forms allow-modals allow-popups allow-popups-to-escape-sandbox allow-downloads"
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
</div>

{{-- ══════════════════════ VIDEO CALL MODAL ══════════════════════ --}}
<div id="video-modal" style="display:none;position:fixed;inset:0;z-index:9997;background:rgba(0,0,0,.92);backdrop-filter:blur(12px);flex-direction:column;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px solid #21262d;flex-shrink:0;background:#0d1117;">
        <div style="display:flex;align-items:center;gap:10px;">
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
                <button onclick="endVideoCall()" style="display:flex;align-items:center;gap:4px;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;background:rgba(239,68,68,.12);color:#f87171;border:1px solid #21262d;">
                    <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    End Call
                </button>
            </div>
        </div>
    </div>
    <div id="jitsi-container" style="flex:1;background:#000;"></div>
</div>

{{-- Minimised video pill --}}
<div id="video-pill" style="display:none;position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:9996;padding:8px 20px;border-radius:20px;background:#161b22;border:1px solid rgba(16,185,129,.3);box-shadow:0 8px 32px rgba(0,0,0,.6);cursor:pointer;align-items:center;gap:8px;" onclick="maximizeVideo()">
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
    vscodeUrl:    '{!! $vscodeUrl !!}',
    reverb: {
        key:    '{{ $reverbConfig["key"] }}',
        host:   '{{ $reverbConfig["host"] }}',
        port:    {{ $reverbConfig["port"] }},
        scheme: '{{ $reverbConfig["scheme"] }}',
    },
};

// ─── Preloader Logic ──────────────────────────────────────────────
const pSteps = ['p-step-1', 'p-step-2', 'p-step-3', 'p-step-4'];
let currentPStep = 0;

function advancePStep() {
    if (vscLoaded || currentPStep >= pSteps.length) return;
    
    const current = document.getElementById(pSteps[currentPStep]);
    if (current) {
        current.classList.remove('active');
        current.classList.add('completed');
        current.querySelector('.p-step-icon').innerHTML = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
    }

    currentPStep++;
    if(currentPStep < pSteps.length){
        const next = document.getElementById(pSteps[currentPStep]);
        if (next) {
            next.classList.remove('pending');
            next.classList.add('active');
        }
    }
}

// Staggered realistic loading timeline
setTimeout(advancePStep, 1500); // 1.5s -> Initializing
setTimeout(advancePStep, 3500); // 3.5s -> Building
setTimeout(advancePStep, 6000); // 6.0s -> Finalizing

// ─── VS Code iframe lifecycle ─────────────────────────────────────
let vscLoaded = false;
let iframeStarted = false;

function onVsCodeLoad(){
    if (!iframeStarted) return; // Prevent initial about:blank onload from triggering
    vscLoaded = true;
    document.getElementById('vscode-fallback').style.display = 'none';
    document.getElementById('vsc-dot').style.background = '#4ade80';
    document.getElementById('vsc-label').textContent = 'VS Code ready';
    document.getElementById('vsc-label').style.color = '#4ade80';
    
    // Rapidly complete remaining steps for UX
    pSteps.forEach(id => {
        const el = document.getElementById(id);
        if(el && !el.classList.contains('completed')){
            el.classList.remove('active', 'pending');
            el.classList.add('completed');
            el.querySelector('.p-step-icon').innerHTML = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
        }
    });

    setTimeout(() => {
        document.getElementById('vc-preloader').classList.add('hidden');
    }, 400); // Brief pause to see all green checkmarks
}

function onVsCodeError(){
    if (!iframeStarted) return;
    document.getElementById('vscode-fallback').style.display = 'flex';
    document.getElementById('vsc-dot').style.background = '#f87171';
    document.getElementById('vsc-label').textContent = 'VS Code offline';
    document.getElementById('vsc-label').style.color  = '#f87171';
}

function reloadVsCode(){
    document.getElementById('vscode-fallback').style.display = 'none';
    document.getElementById('vsc-dot').style.background = '#fbbf24';
    document.getElementById('vsc-label').textContent = 'Reloading…';
    document.getElementById('vsc-label').style.color  = '#fbbf24';
    iframeStarted = false;
    pollForVsCode(); // Start polling again
}

// Polling mechanism to wait until container is actually ready
let pollAttempts = 0;
function pollForVsCode() {
    fetch(VC.vscodeUrl, { mode: 'no-cors' })
        .then(() => {
            // Container is responding
            iframeStarted = true;
            document.getElementById('vscode-frame').src = VC.vscodeUrl;
        })
        .catch(() => {
            pollAttempts++;
            if (pollAttempts < 25) { // Try for ~25 seconds
                setTimeout(pollForVsCode, 1000);
            } else {
                // Timeout reached
                onVsCodeError();
                document.getElementById('vc-preloader').classList.add('hidden');
            }
        });
}

// Start polling immediately
pollForVsCode();

// Auto-check fallback safety just in case
setTimeout(()=>{
    if (!vscLoaded && iframeStarted){
        document.getElementById('vscode-fallback').style.display = 'flex';
        document.getElementById('vsc-dot').style.background = '#fbbf24';
        document.getElementById('vsc-label').textContent = 'Starting…';
    }
    
    // Force complete steps and hide preloader as fallback safety
    pSteps.forEach(id => {
        const el = document.getElementById(id);
        if(el && !el.classList.contains('completed')){
            el.classList.remove('active', 'pending');
            el.classList.add('completed');
            el.querySelector('.p-step-icon').innerHTML = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
        }
    });

    setTimeout(() => {
        document.getElementById('vc-preloader').classList.add('hidden');
    }, 400);

}, 15000);

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
        `<div class="presence-avatar" title="${escapeHtml(u.name)}" style="background:${colors[i%colors.length]};color:#fff;border:2px solid #0d1117;${i?'margin-left:-6px':''}">${escapeHtml((u.initials||u.name||'?').slice(0,2).toUpperCase())}</div>`
    ).join('');

    // Collab modal member list
    const list = document.getElementById('collab-member-list');
    list.innerHTML = presenceUsers.map((u,i)=>`
        <div style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:10px;background:#0a0a0a;border:1px solid #21262d;">
            <div class="presence-avatar" style="background:${colors[i%colors.length]};color:#fff;flex-shrink:0;">${escapeHtml((u.initials||u.name||'?').slice(0,2).toUpperCase())}</div>
            <div>
                <div style="font-size:12px;color:#f1f5f9;font-weight:600;">${escapeHtml(u.name)}</div>
                <div style="font-size:10px;color:#64748b;">${escapeHtml(u.role||'')}</div>
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
    el.innerHTML = `<span style="width:8px;height:8px;border-radius:50%;background:${c[type]||c.info};flex-shrink:0;"></span>${escapeHtml(msg)}`;
    el.classList.add('show');
    setTimeout(()=>el.classList.remove('show'), 3000);
}

function escapeHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
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

    document.addEventListener('keydown', e => {
        if(e.ctrlKey && e.shiftKey && e.key === 'V'){ e.preventDefault(); startVideoCall(); }
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
</script>
</body>
</html>

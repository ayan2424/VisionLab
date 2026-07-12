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
        html,body{height:100%;margin:0;padding:0;overflow:hidden;background:#050505;font-family:'Inter', sans-serif;}

        /* Ultra Premium Top Bar */
        #vc-topbar {
            height: 56px; display: flex; align-items: center; justify-content: space-between;
            padding: 0 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(10, 10, 10, 0.7); backdrop-filter: blur(20px);
            flex-shrink: 0; z-index: 100; position: relative;
        }

        .pill-btn {
            display: flex; align-items: center; gap: 6px; padding: 6px 14px;
            border-radius: 9999px; /* Pill shaped */
            font-size: 12px; font-weight: 600; cursor: pointer; border: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .pill-btn:hover { transform: translateY(-1px); box-shadow: 0 10px 25px rgba(0,0,0,0.5); }

        .btn-collab { background: rgba(34,211,238,0.1); color: #22d3ee; border: 1px solid rgba(34,211,238,0.3); }
        .btn-deploy { background: rgba(245,158,11,0.1); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
        .btn-video  { background: rgba(16,185,129,0.1); color: #4ade80; border: 1px solid rgba(16,185,129,0.3); }

        /* Workspace Area */
        .workspace-container { display:flex; height:calc(100vh - 56px); width:100%; position:relative; overflow:hidden; }
        .center-pane { flex:1; min-width:0; position:relative; display:flex; flex-direction:column; }
        #vscode-frame { width:100%; height:100%; border:none; display:block; opacity: 0; transition: opacity 0.8s ease; }
        #vscode-frame.ready { opacity: 1; }

        /* Firebase-Style Premium Loader */
        #premium-loader {
            position: absolute; inset: 0; background: #050505; z-index: 500;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            transition: opacity 0.8s ease, visibility 0.8s ease;
        }
        #premium-loader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
        
        .loader-box {
            background: #0a0a0a; border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px; padding: 40px; width: 400px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.9), 0 0 0 1px rgba(249, 115, 22, 0.1); /* Workspace violet Accent */
            display: flex; flex-direction: column; gap: 24px;
        }

        .loader-header {
            display: flex; align-items: center; gap: 16px; border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 24px;
        }
        
        .loader-icon {
            width: 48px; height: 48px; border-radius: 16px; background: rgba(249, 115, 22, 0.15);
            display: flex; align-items: center; justify-content: center;
            border: 1px solid rgba(249, 115, 22, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .step-item {
            display: flex; align-items: center; gap: 12px; font-size: 13px; color: #64748b;
            transition: color 0.3s;
        }
        .step-item.active { color: #f1f5f9; font-weight: 600; }
        .step-item.done { color: #f97316; } /* orange */

        .spinner {
            width: 16px; height: 16px; border: 2px solid transparent;
            border-top-color: #f97316; border-right-color: #f97316;
            border-radius: 50%; animation: spin 1s linear infinite;
        }
        
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* Modals (Pilled and Floating) */
        .modal-overlay {
            display:none; position:fixed; inset:0; z-index:9998;
            background: rgba(0,0,0,0.8); backdrop-filter: blur(16px);
            align-items:center; justify-content:center; opacity: 0; transition: opacity 0.3s;
        }
        .modal-overlay.open { display:flex; opacity: 1; }
        
        .modal-content {
            width:100%; max-width:440px; margin:16px; background: #0a0a0a;
            border: 1px solid rgba(255,255,255,0.05); border-radius: 32px; /* Extremely rounded */
            box-shadow: 0 50px 100px rgba(0,0,0,0.9); transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .modal-overlay.open .modal-content { transform: scale(1); }

        /* Collaborators */
        .collab-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; color: #fff;
            border: 2px solid #050505; margin-left: -8px; z-index: 1;
            box-shadow: 0 2px 5px rgba(0,0,0,0.5);
        }
        .collab-avatar:first-child { margin-left: 0; }

        /* Toast Notifications */
        #toast-container {
            position: fixed; bottom: 20px; right: 20px; z-index: 9999;
            display: flex; flex-direction: column; gap: 10px; pointer-events: none;
        }
        .toast {
            background: rgba(10, 10, 10, 0.9); border: 1px solid rgba(255,255,255,0.1);
            color: #fff; padding: 12px 16px; border-radius: 8px; font-size: 13px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5); transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: auto; display: flex; align-items: center; gap: 10px;
        }
        .toast.show { transform: translateX(0); }
        .toast-title { font-weight: 700; color: #f97316; }

    </style>
</head>
<body class="h-full">

<x-exam-lockdown :is-exam-mode="isset($assignment) && $assignment->mode === 'exam'" />

{{-- Toast Container --}}
<div id="toast-container"></div>

{{-- TOP BAR --}}
<div id="vc-topbar">
    {{-- Left --}}
    <div style="display:flex;align-items:center;gap:12px;">
        <a href="{{ route('home') }}" style="display:flex;align-items:center;text-decoration:none;">
            <x-logo size="h-8 w-8" textSize="text-[15px]" variant="orange" />
        </a>

        <span style="color:rgba(255,255,255,0.1);font-size:16px;margin:0 4px;">|</span>

        {{-- Status --}}
        <div style="display:flex;align-items:center;gap:6px;">
            <span id="vsc-dot" style="width:8px;height:8px;border-radius:50%;background:#f97316;box-shadow:0 0 10px rgba(249,115,22,0.5);"></span>
            <span id="vsc-label" style="font-size:12px;color:#f1f5f9;font-weight:600;">Booting Workspace...</span>
        </div>
    </div>

    {{-- Right --}}
    <div style="display:flex;align-items:center;gap:10px;">
        @if(isset($assignment))
        <form method="POST" action="{{ route('submissions.submit', $assignment->id) }}" style="margin:0;" onsubmit="event.preventDefault(); vcConfirm('Submit this assignment for grading?', () => this.submit())">
            @csrf
            <button type="submit" class="pill-btn" style="background:rgba(124,58,237,0.1);color:#a78bfa;border:1px solid rgba(124,58,237,0.3);">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Submit Assignment
            </button>
        </form>
        @endif
        <div class="pill-btn" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#94a3b8;cursor:default;">
            <span id="reverb-dot" style="width:6px;height:6px;border-radius:50%;background:#64748b;"></span>
            Reverb Connected
        </div>

        <button onclick="document.getElementById('collab-modal').classList.add('open')" class="pill-btn btn-collab">
            Collaborate
        </button>

        <button onclick="document.getElementById('deploy-modal').classList.add('open')" class="pill-btn btn-deploy">
            Deploy
        </button>

        <button id="video-btn" onclick="startVideoCall()" class="pill-btn btn-video">
            Video Call
        </button>

        {{-- Collaborators Container --}}
        <div id="collaborators-container" style="display:flex;align-items:center;gap:-8px;margin-left:8px;"></div>

        {{-- User Menu --}}
        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg, #f97316, #fb923c);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;cursor:pointer;box-shadow:0 4px 12px rgba(249,115,22,0.4);margin-left:4px;">
            {{ $user->avatar_initials ?? 'U' }}
        </div>

        {{-- Restart Workspace --}}
        <a href="{{ route('workspace.restart', $workspace->slug) }}" class="pill-btn" style="background:rgba(234,179,8,0.1);color:#eab308;border:1px solid rgba(234,179,8,0.3);text-decoration:none;margin-left:8px;" title="Restart Workspace">
            Restart
        </a>

        {{-- Destroy Workspace --}}
        <form method="POST" action="{{ route('workspace.destroy', $workspace->slug) }}" style="margin:0;margin-left:4px;" onsubmit="event.preventDefault(); vcConfirm('WARNING: This will permanently delete your workspace and wipe all files. This cannot be undone. Continue?', () => this.submit())">
            @csrf
            @method('DELETE')
            <button type="submit" class="pill-btn" style="background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.3);">
                Delete Workspace
            </button>
        </form>
    </div>
</div>

{{-- WORKSPACE CONTAINER --}}
<div class="workspace-container">
    <div class="center-pane">
        
        {{-- PREMIUM LOADER (Firebase Studio Style) --}}
        <div id="premium-loader">
            <div class="loader-box">
                <div class="loader-header">
                    <div class="loader-icon">
                        <svg style="width:24px;height:24px;color:#f97316;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:700;color:#f1f5f9;letter-spacing:-0.02em;">{{ $workspaceName ?? 'VisionLab Workspace' }}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $workspace->template->name ?? 'Standard' }} Template &bull; visionlab-{{ $workspace->id ?? rand(1000,9999) }}</div>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:16px;padding:0 8px;">
                    <div class="step-item active" id="step-1">
                        <div class="spinner" id="spin-1"></div>
                        <span>Initializing environment</span>
                    </div>
                    <div class="step-item" id="step-2">
                        <div style="width:16px;height:16px;"></div>
                        <span>Building container</span>
                    </div>
                    <div class="step-item" id="step-3">
                        <div style="width:16px;height:16px;"></div>
                        <span>Creating {{ $workspace->template->name ?? 'Starter' }} template</span>
                    </div>
                    <div class="step-item" id="step-4">
                        <div style="width:16px;height:16px;"></div>
                        <span>Starting VisionLab IDE</span>
                    </div>
                    <div class="step-item" id="step-5">
                        <div style="width:16px;height:16px;"></div>
                        <span>Finalizing</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- VS CODE IFRAME (Full Screen, No external File Explorer) --}}
        <iframe id="vscode-frame"
            data-src="{{ $vscodeUrl ?? '' }}"
            allow="clipboard-read; clipboard-write"
            sandbox="allow-scripts allow-same-origin allow-forms allow-modals allow-popups allow-popups-to-escape-sandbox">
        </iframe>
    </div>
</div>

{{-- MODALS (Kept minimal structure, classes handled via JS) --}}
<div id="collab-modal" class="modal-overlay" onclick="if(event.target===this) this.classList.remove('open')">
    <div class="modal-content" style="padding:24px;">
        <h3 style="color:#f1f5f9;margin:0 0 8px 0;">Collaborate</h3>
        <p style="color:#64748b;font-size:13px;margin:0 0 20px 0;">Share this link to collaborate in real-time.</p>
        <div style="background:#161b22;padding:12px;border-radius:16px;color:#a78bfa;font-family:monospace;font-size:12px;text-align:center;border:1px solid rgba(255,255,255,0.05);">
            {{ $roomSlug ?? 'personal-room' }}
        </div>
    </div>
</div>

<div id="deploy-modal" class="modal-overlay" onclick="if(event.target===this) this.classList.remove('open')">
    <div class="modal-content" style="padding:24px;">
        <h3 style="color:#f1f5f9;margin:0 0 8px 0;">Deploy Workspace</h3>
        <p style="color:#64748b;font-size:13px;margin:0 0 20px 0;">Push your code to the cloud instantly.</p>
        <div id="deploy-buttons" style="display:flex;gap:12px;">
            <button onclick="deployWorkspace('vercel')" class="pill-btn" style="flex:1;justify-content:center;background:#fff;color:#000;">Vercel</button>
            <button onclick="deployWorkspace('railway')" class="pill-btn" style="flex:1;justify-content:center;background:#0b0d14;color:#fff;border:1px solid #333;">Railway</button>
        </div>
        <div id="deploy-status" style="display:none;margin-top:20px;padding:16px;border-radius:12px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <div id="deploy-spinner" class="spinner"></div>
                <span id="deploy-text" style="color:#f1f5f9;font-size:13px;font-weight:600;">Queuing deployment...</span>
            </div>
            <a id="deploy-link" href="#" target="_blank" style="display:none;color:#38bdf8;font-size:12px;text-decoration:none;">View Deployment &rarr;</a>
        </div>
    </div>
</div>

<script>
    // Real-Time Polling for Workspace Readiness
    let vscLoaded = false;
    let currentStep = 1;
    let pollInterval = null;
    let fallbackTimeout = null;
    const workspaceId = "{{ $workspace->id }}";
    
    // Animate steps while waiting (Step 1 -> Step 2 -> Step 3 -> Step 4)
    setTimeout(advanceStep, 2000);
    setTimeout(advanceStep, 5000);
    setTimeout(advanceStep, 15000);
    // Note: We don't advance to Step 5 (Finalizing) until we actually get 'ready: true'

    function advanceStep() {
        if (vscLoaded || currentStep >= 4) return;
        
        // Mark current as done
        const cur = document.getElementById('step-' + currentStep);
        cur.classList.remove('active');
        cur.classList.add('done');
        cur.innerHTML = `<svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg> <span>${cur.innerText}</span>`;

        currentStep++;
        
        // Activate next
        if(currentStep <= 5) {
            const next = document.getElementById('step-' + currentStep);
            next.classList.add('active');
            next.innerHTML = `<div class="spinner"></div> <span>${next.innerText}</span>`;
        }
    }

    async function startWorkspace() {
        const needsBoot = {{ $needsBoot ? 'true' : 'false' }};
        if (!needsBoot) {
            // Already running, start checking if code-server is ready
            startPolling();
            return;
        }

        try {
            const startUrl = "{{ route('workspace.start', $workspace->slug) }}";
            const response = await fetch(startUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                onVsCodeError();
                console.error("Boot failed:", response.status);
                return;
            }

            const data = await response.json();
            
            if (data.status === 'running') {
                // Now start polling until ping succeeds
                document.getElementById('vscode-frame').setAttribute('data-src', data.url);
                startPolling();
            } else {
                onVsCodeError();
            }
        } catch (err) {
            console.error("Boot network error", err);
            onVsCodeError();
        }
    }

    function startPolling() {
        pollInterval = setInterval(async () => {
            try {
                const pingUrl = "{{ route('workspace.ping', $workspace->slug) }}";
                const response = await fetch(pingUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    console.error("Ping returned status:", response.status);
                    return;
                }

                const data = await response.json();
                
                if (data.ready) {
                    clearInterval(pollInterval);
                    // Fast-forward to step 5
                    currentStep = 4;
                    advanceStep(); 
                    
                    // Inject the iframe src to start loading VS Code
                    const frame = document.getElementById('vscode-frame');
                    frame.src = frame.getAttribute('data-src');
                    
                    // Fallback to force hide preloader if iframe load event fails to fire
                    fallbackTimeout = setTimeout(() => {
                        if (!vscLoaded) onVsCodeLoad();
                    }, 10000);
                }
            } catch (err) {
                console.error("Ping failed", err);
            }
        }, 3000);
    }

    function onVsCodeLoad() {
        const frame = document.getElementById('vscode-frame');
        if (!frame.src || frame.src === window.location.href || frame.src === 'about:blank') return;
        
        vscLoaded = true;
        if (fallbackTimeout) clearTimeout(fallbackTimeout);
        if (pollInterval) clearInterval(pollInterval);
        
        // Fast forward all steps to done
        for(let i=1; i<=5; i++) {
            const el = document.getElementById('step-' + i);
            if(el.classList.contains('done')) continue;
            el.classList.remove('active'); el.classList.add('done');
            el.innerHTML = `<svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg> <span>${el.innerText}</span>`;
        }

        setTimeout(() => {
            document.getElementById('premium-loader').classList.add('hidden');
            document.getElementById('vscode-frame').classList.add('ready');
            document.getElementById('vsc-dot').style.background = '#4ade80';
            document.getElementById('vsc-label').textContent = 'IDE Active';
        }, 800);
    }

    function onVsCodeError() {
        document.getElementById('vsc-dot').style.background = '#ef4444';
        document.getElementById('vsc-label').textContent = 'Failed to Boot';
    }

    // Start boot sequence
    startWorkspace();

    // Attach event listeners
    document.getElementById('vscode-frame').addEventListener('load', onVsCodeLoad);
    document.getElementById('vscode-frame').addEventListener('error', onVsCodeError);

    // ─────────────────────────────────────────────────────────────
    // COLLABORATION (Reverb Presence & Chat)
    // ─────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.initEcho === 'function') {
            const reverbConfig = @json($reverbConfig);
            const echo = window.initEcho(reverbConfig);
            const roomSlug = "{{ $roomSlug }}";
            const currentUserId = {{ auth()->id() ?? 'null' }};
            
            document.getElementById('reverb-dot').style.background = '#10b981'; // Green dot
            
            // Join Presence Channel
            const channel = echo.join(`collab.${roomSlug}`);
            
            channel.here((users) => {
                updateCollaboratorsUI(users);
            })
            .joining((user) => {
                addCollaborator(user);
            })
            .leaving((user) => {
                removeCollaborator(user);
            })
            .listen('ChatMessageSent', (e) => {
                appendChatMessage(e);
            })
            .listen('WorkspaceRebuildingEvent', (e) => {
                window.location.href = `/workspace/{{ $workspace->slug }}/rebuild`;
            });
            
            let activeUsers = [];
            
            function updateCollaboratorsUI(users) {
                activeUsers = users;
                renderCollaborators();
            }
            
            function addCollaborator(user) {
                if (!activeUsers.find(u => u.id === user.id)) {
                    activeUsers.push(user);
                    renderCollaborators();
                }
            }
            
            function removeCollaborator(user) {
                activeUsers = activeUsers.filter(u => u.id !== user.id);
                renderCollaborators();
            }
            
            function renderCollaborators() {
                const container = document.getElementById('collaborators-container');
                if (!container) return;
                
                container.innerHTML = '';
                // Render up to 3 avatars
                const displayUsers = activeUsers.slice(0, 3);
                displayUsers.forEach(user => {
                    const div = document.createElement('div');
                    div.className = 'collab-avatar';
                    div.style.background = user.color || '#4f46e5';
                    div.title = user.name;
                    div.textContent = user.initials;
                    container.appendChild(div);
                });
                
                if (activeUsers.length > 3) {
                    const div = document.createElement('div');
                    div.className = 'collab-avatar';
                    div.style.background = '#334155';
                    div.textContent = '+' + (activeUsers.length - 3);
                    container.appendChild(div);
                }
            }
            
            function appendChatMessage(e) {
                // Ignore our own messages from toast
                if (e.user_id === currentUserId) return;
                
                showToast(e.user_name, e.message);
            }

            function showToast(title, message) {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = 'toast';
                
                toast.innerHTML = `
                    <div style="width:24px;height:24px;background:rgba(249,115,22,0.1);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#8b5cf6;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <div>
                        <div class="toast-title">${title}</div>
                        <div style="color:#cbd5e1;">${message}</div>
                    </div>
                `;
                
                container.appendChild(toast);
                
                // Trigger animation
                requestAnimationFrame(() => toast.classList.add('show'));
                
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 4000);
            }
            
            // Expose the channel globally for iframe (extension) to hook into if needed
            window.workspaceChannel = channel;
            window.workspaceUserId = currentUserId;
        }
    });

    // ─────────────────────────────────────────────────────────────
    // VIDEO CALL (Phase 7 - Jitsi Meet)
    // ─────────────────────────────────────────────────────────────
    let activeCall = false;

    async function checkVideoStatus() {
        try {
            const res = await fetch(`/api/workspace/{{ $roomSlug }}/video/status`, {
                headers: { 'Authorization': `Bearer {{ $workspace->owner?->createToken('workspace')->plainTextToken ?? '' }}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            
            const btn = document.getElementById('video-btn');
            if (data.active) {
                activeCall = true;
                btn.textContent = 'Join Call';
                btn.style.background = 'rgba(239, 68, 68, 0.1)';
                btn.style.color = '#ef4444';
                btn.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                btn.onclick = joinVideoCall;
            } else {
                activeCall = false;
                btn.textContent = 'Video Call';
                btn.style.background = 'rgba(16,185,129,0.1)';
                btn.style.color = '#4ade80';
                btn.style.borderColor = 'rgba(16,185,129,0.3)';
                btn.onclick = startVideoCall;
            }
        } catch (e) {
            console.error('Failed to fetch video status', e);
        }
    }

    async function startVideoCall() {
        try {
            const res = await fetch(`/api/workspace/{{ $roomSlug }}/video/start`, {
                method: 'POST',
                headers: { 
                    'Authorization': `Bearer {{ $workspace->owner?->createToken('workspace')->plainTextToken ?? '' }}`, 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const data = await res.json();
            if (data.active) {
                // Ping attendance
                await fetch(`/api/workspace/{{ $roomSlug }}/video/attendance`, {
                    method: 'POST',
                    headers: { 
                        'Authorization': `Bearer {{ $workspace->owner?->createToken('workspace')->plainTextToken ?? '' }}`, 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ action: 'join' })
                });
                checkVideoStatus();
            }
        } catch (e) {
            alert('Failed to start video call.');
        }
    }

    async function joinVideoCall() {
        // Just trigger the start endpoint again to get details, the extension handles rendering.
        startVideoCall();
    }

    // Initial check
    checkVideoStatus();

    // ── Phase 9: Deployment Logic ──
    async function deployWorkspace(provider) {
        document.getElementById('deploy-buttons').style.display = 'none';
        const statusDiv = document.getElementById('deploy-status');
        const statusText = document.getElementById('deploy-text');
        const statusSpinner = document.getElementById('deploy-spinner');
        const statusLink = document.getElementById('deploy-link');
        
        statusDiv.style.display = 'block';
        statusText.textContent = `Queuing deployment to ${provider}...`;
        statusText.style.color = '#f1f5f9';
        statusSpinner.style.display = 'block';
        statusLink.style.display = 'none';

        try {
            const res = await fetch(`/workspace/{{ $workspace->slug }}/deploy`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ provider: provider })
            });
            const data = await res.json();
            
            if (res.ok) {
                pollDeployment(data.deployment.id);
            } else {
                showDeployError(data.error || 'Failed to queue deployment.');
            }
        } catch (e) {
            showDeployError(e.message);
        }
    }

    async function pollDeployment(deploymentId) {
        const statusText = document.getElementById('deploy-text');
        const statusSpinner = document.getElementById('deploy-spinner');
        const statusLink = document.getElementById('deploy-link');

        const interval = setInterval(async () => {
            try {
                const res = await fetch(`/deployments/${deploymentId}/status`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.status === 'building') {
                    statusText.textContent = 'Building project...';
                } else if (data.status === 'deployed') {
                    clearInterval(interval);
                    statusText.textContent = 'Deployed Successfully!';
                    statusText.style.color = '#10B981'; // Success Green
                    statusSpinner.style.display = 'none';
                    if (data.public_url) {
                        statusLink.href = data.public_url;
                        statusLink.style.display = 'block';
                    }
                    showToast('Deployment Successful', 'Your project is live.', 'success');
                } else if (data.status === 'failed') {
                    clearInterval(interval);
                    showDeployError(data.error_summary || 'Deployment failed.');
                }
            } catch (e) {
                // Ignore temporary fetch failures
            }
        }, 3000);
    }

    function showDeployError(msg) {
        document.getElementById('deploy-text').textContent = 'Error: ' + msg;
        document.getElementById('deploy-text').style.color = '#EF4444'; // Error Red
        document.getElementById('deploy-spinner').style.display = 'none';
        document.getElementById('deploy-buttons').style.display = 'flex';
    }
    // ── Phase 4.5: Assignment Lockdown Logic ──
    @if(isset($isAssignmentLocked) && $isAssignmentLocked)
    (function() {
        let isLocked = false;
        let violations = 0;

        function enforceLockdown() {
            const elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen().catch(err => {
                    console.log(`Error attempting to enable fullscreen: ${err.message}`);
                });
            }
            isLocked = true;
            document.getElementById('lockdown-overlay').style.display = 'none';
        }

        // Show overlay demanding fullscreen if not fullscreen
        function checkFullscreen() {
            if (!document.fullscreenElement) {
                isLocked = false;
                document.getElementById('lockdown-overlay').style.display = 'flex';
                violations++;
                if (violations > 1) {
                    showToast('Violation Recorded', 'You exited fullscreen during a locked exam.', 'error');
                    // We can also send this to the server via analytics_events API later
                }
            } else {
                document.getElementById('lockdown-overlay').style.display = 'none';
            }
        }

        // Listen for visibility changes (tab switching)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && isLocked) {
                showToast('Exam Violation', 'You switched tabs. This action has been logged.', 'error');
                violations++;
            }
        });

        // Listen for fullscreen exits
        document.addEventListener('fullscreenchange', checkFullscreen);

        // Auto-enforce on first click anywhere
        document.addEventListener('click', () => {
            if (!isLocked) enforceLockdown();
        }, { once: true });

        // Build the overlay UI
        const overlay = document.createElement('div');
        overlay.id = 'lockdown-overlay';
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100vw';
        overlay.style.height = '100vh';
        overlay.style.backgroundColor = 'rgba(10, 10, 10, 0.95)';
        overlay.style.zIndex = '999999';
        overlay.style.display = 'flex';
        overlay.style.flexDirection = 'column';
        overlay.style.justifyContent = 'center';
        overlay.style.alignItems = 'center';
        overlay.style.color = '#fff';
        
        overlay.innerHTML = `
            <i class="ri-lock-2-line" style="font-size: 4rem; color: #EF4444; margin-bottom: 1rem;"></i>
            <h1 style="font-size: 2rem; margin-bottom: 1rem;">Exam Lockdown Active</h1>
            <p style="font-size: 1.2rem; color: #94a3b8; text-align: center; max-width: 600px; margin-bottom: 2rem;">
                This assignment requires a locked workspace. You must remain in fullscreen mode and cannot switch tabs.
            </p>
            <button id="btn-enter-lockdown" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">
                Enter Fullscreen & Begin
            </button>
        `;
        
        document.body.appendChild(overlay);

        document.getElementById('btn-enter-lockdown').addEventListener('click', enforceLockdown);
    })();
    @endif

</script>

</body>
</html>


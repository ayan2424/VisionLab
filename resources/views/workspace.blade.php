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
            box-shadow: 0 40px 100px rgba(0,0,0,0.9), 0 0 0 1px rgba(249, 115, 22, 0.1); /* Workspace Orange Accent */
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
        .step-item.done { color: #f97316; } /* Orange */

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

    </style>
</head>
<body class="h-full">

{{-- TOP BAR --}}
<div id="vc-topbar">
    {{-- Left --}}
    <div style="display:flex;align-items:center;gap:12px;">
        <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
            <div style="width:32px;height:32px;border-radius:12px;background:linear-gradient(135deg, #f97316, #ea580c);display:flex;align-items:center;justify-content:center;box-shadow:0 0 15px rgba(249,115,22,0.4);">
                <svg style="width:16px;height:16px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <span style="font-size:14px;font-weight:800;color:#fff;letter-spacing:-0.02em;">Vision<span style="color:#f97316;">Lab</span></span>
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

        {{-- User Menu --}}
        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg, #4f46e5, #7c3aed);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;cursor:pointer;box-shadow:0 4px 12px rgba(124,58,237,0.4);margin-left:8px;">
            {{ $user->avatar_initials ?? 'U' }}
        </div>
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
                        <div style="font-size:12px;color:#64748b;margin-top:2px;">visionlab-{{ $workspace->id ?? rand(1000,9999) }}</div>
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
                        <span>Starting code-server</span>
                    </div>
                    <div class="step-item" id="step-4">
                        <div style="width:16px;height:16px;"></div>
                        <span>Finalizing</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- VS CODE IFRAME (Full Screen, No external File Explorer) --}}
        <iframe id="vscode-frame"
            src="{{ $vscodeUrl ?? '' }}"
            allow="clipboard-read; clipboard-write"
            sandbox="allow-scripts allow-same-origin allow-forms allow-modals allow-popups allow-popups-to-escape-sandbox"
            onload="onVsCodeLoad()"
            onerror="onVsCodeError()">
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
        <div style="display:flex;gap:12px;">
            <button class="pill-btn" style="flex:1;justify-content:center;background:#fff;color:#000;">Vercel</button>
            <button class="pill-btn" style="flex:1;justify-content:center;background:#0b0d14;color:#fff;border:1px solid #333;">Railway</button>
        </div>
    </div>
</div>

<script>
    // Premium Loader Simulation & Iframe Load detection
    let vscLoaded = false;
    let currentStep = 1;

    function advanceStep() {
        if (vscLoaded || currentStep >= 4) return;
        
        // Mark current as done
        const cur = document.getElementById('step-' + currentStep);
        cur.classList.remove('active');
        cur.classList.add('done');
        cur.innerHTML = `<svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg> <span>${cur.innerText}</span>`;

        currentStep++;
        
        // Activate next
        if(currentStep <= 4) {
            const next = document.getElementById('step-' + currentStep);
            next.classList.add('active');
            next.innerHTML = `<div class="spinner"></div> <span>${next.innerText}</span>`;
        }
    }

    // Simulate boot progress
    setTimeout(advanceStep, 2000);
    setTimeout(advanceStep, 5000);
    setTimeout(advanceStep, 8000);

    function onVsCodeLoad() {
        if (!document.getElementById('vscode-frame').src || document.getElementById('vscode-frame').src === window.location.href) return;
        
        vscLoaded = true;
        
        // Fast forward all steps to done
        for(let i=1; i<=4; i++) {
            const el = document.getElementById('step-' + i);
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

    // Safety fallback
    setTimeout(() => { if(!vscLoaded) onVsCodeLoad(); }, 12000);
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rebuilding {{ $workspace->name }} — VisionLab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *,*::before,*::after{box-sizing:border-box;}
        html,body{height:100%;margin:0;padding:0;overflow:hidden;background:#050505;font-family:'Inter', sans-serif;}

        /* Firebase-Style Premium Loader */
        #premium-loader {
            position: absolute; inset: 0; background: #050505; z-index: 500;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        
        .loader-box {
            background: #0a0a0a; border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px; padding: 40px; width: 400px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.9), 0 0 0 1px rgba(249, 115, 22, 0.1);
            display: flex; flex-direction: column; gap: 24px;
        }

        .loader-header {
            display: flex; align-items: center; gap: 16px; border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 24px;
        }
        
        .loader-icon {
            width: 48px; height: 48px; border-radius: 16px; background: rgba(249, 115, 22, 0.15);
            display: flex; align-items: center; justify-content: center;
            color: #f97316; font-size: 24px;
        }

        .loader-title {
            font-size: 18px; font-weight: 600; color: #fff; margin: 0 0 4px 0;
        }
        .loader-subtitle {
            font-size: 13px; color: #737373; margin: 0;
        }

        .loader-progress {
            display: flex; flex-direction: column; gap: 12px;
        }

        .progress-label {
            display: flex; justify-content: space-between; font-size: 13px; font-weight: 500;
        }
        .progress-label .status { color: #f97316; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        
        .progress-track {
            height: 4px; background: rgba(255,255,255,0.05); border-radius: 2px; overflow: hidden;
            position: relative;
        }
        .progress-bar {
            position: absolute; top: 0; left: 0; height: 100%; background: #f97316;
            width: 30%; border-radius: 2px;
            animation: indeterminate 1.5s infinite ease-in-out;
        }

        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
        @keyframes indeterminate {
            0% { transform: translateX(-100%); width: 30%; }
            50% { width: 50%; }
            100% { transform: translateX(300%); width: 30%; }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>

<div id="premium-loader">
    <div class="loader-box">
        <div class="loader-header">
            <div class="loader-icon">
                <i class="ri-refresh-line" style="animation: spin 2s linear infinite;"></i>
            </div>
            <div>
                <h3 class="loader-title">Rebuilding Environment</h3>
                <p class="loader-subtitle">Syncing Nix packages...</p>
            </div>
        </div>
        <div class="loader-progress">
            <div class="progress-label">
                <span style="color:#a3a3a3;">Status</span>
                <span class="status" id="loader-status-text">Restarting container...</span>
            </div>
            <div class="progress-track">
                <div class="progress-bar"></div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const workspaceSlug = '{{ $workspace->slug }}';

    // 1. Send the rebuild command
    fetch(`/workspace/${workspaceSlug}/rebuild/process`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    }).then(res => res.json()).then(data => {
        document.getElementById('loader-status-text').textContent = 'Loading new environment...';
        pollPing();
    }).catch(err => {
        document.getElementById('loader-status-text').textContent = 'Error starting rebuild';
        document.getElementById('loader-status-text').style.color = '#ef4444';
    });

    // 2. Poll until IDE is ready
    function pollPing() {
        const interval = setInterval(async () => {
            try {
                const res = await fetch(`/workspace/${workspaceSlug}/ping`);
                if(res.ok) {
                    const data = await res.json();
                    if(data.ready) {
                        clearInterval(interval);
                        document.getElementById('loader-status-text').textContent = 'Ready!';
                        document.getElementById('loader-status-text').style.color = '#10b981';
                        document.getElementById('loader-status-text').style.animation = 'none';
                        setTimeout(() => {
                            window.location.href = `/workspace/${workspaceSlug}`;
                        }, 500);
                    }
                }
            } catch(e) {}
        }, 2000);
    }
    
    const style = document.createElement('style');
    style.innerHTML = `@keyframes spin { 100% { transform: rotate(360deg); } }`;
    document.head.appendChild(style);
</script>

</body>
</html>

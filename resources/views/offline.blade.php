<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0a0a">
    <title>Offline — VisionLab</title>
    <link rel="icon" href="/icons/icon-192.png">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0a0a0a;font-family:system-ui,-apple-system,sans-serif;color:#f1f5f9;overflow:hidden;}
        .container{text-align:center;position:relative;z-index:2;padding:24px;}
        .icon-box{width:72px;height:72px;border-radius:20px;background:rgba(124,58,237,.12);border:1px solid rgba(124,58,237,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;}
        .icon-box svg{width:32px;height:32px;color:#a78bfa;}
        h1{font-size:24px;font-weight:800;margin-bottom:8px;background:linear-gradient(135deg,#8b5cf6,#06b6d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
        p{font-size:14px;color:#64748b;line-height:1.7;max-width:400px;margin:0 auto 32px;}
        .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:12px;background:#7c3aed;color:#fff;font-size:14px;font-weight:700;text-decoration:none;border:none;cursor:pointer;box-shadow:0 0 24px rgba(124,58,237,.4);transition:all .3s;}
        .btn:hover{transform:translateY(-2px);box-shadow:0 0 32px rgba(124,58,237,.6);}
        .status{margin-top:24px;font-size:12px;color:#475569;}
        .blob{position:fixed;border-radius:50%;filter:blur(100px);opacity:.1;pointer-events:none;}
        .blob-1{width:500px;height:500px;background:#7c3aed;top:-200px;left:-100px;}
        .blob-2{width:400px;height:400px;background:#06b6d4;bottom:-150px;right:-100px;}
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="container">
        <div class="icon-box">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636a9 9 0 010 12.728M5.636 18.364a9 9 0 010-12.728M8.464 15.536a5 5 0 010-7.072M15.536 8.464a5 5 0 010 7.072M12 12h.01"/>
            </svg>
        </div>
        <h1>You're Offline</h1>
        <p>It looks like you've lost your internet connection. VisionLabneeds an active connection for the collaborative IDE and AI features.</p>
        <button class="btn" onclick="location.reload()">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Try Again
        </button>
        <div class="status">
            <span id="status-text">Waiting for connection…</span>
        </div>
    </div>
    <script>
        window.addEventListener('online', () => {
            document.getElementById('status-text').textContent = 'Connection restored! Reloading…';
            setTimeout(() => location.reload(), 1000);
        });
    </script>
</body>
</html>



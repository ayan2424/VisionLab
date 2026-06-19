<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline — VisionLab</title>
    <style>
        body { margin: 0; font-family: system-ui, -apple-system, sans-serif; background: #0a0a0a; color: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; }
        .container { max-w-md; padding: 2rem; }
        h1 { font-size: 1.5rem; margin-bottom: 0.5rem; color: #f8fafc; }
        p { color: #94a3b8; font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.6; }
        .btn { display: inline-flex; align-items: center; justify-content: center; background: #38bdf8; color: #0f172a; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #7dd3fc; }
        svg { width: 3rem; height: 3rem; color: #38bdf8; margin-bottom: 1rem; opacity: 0.8; }
    </style>
</head>
<body>
    <div class="container">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.163a1.5 1.5 0 112.121 2.121 1.5 1.5 0 01-2.121-2.121z" />
        </svg>
        <h1>You are offline</h1>
        <p>It looks like you've lost your internet connection.<br>VisionLab requires a connection, especially to sync your workspaces securely.</p>
        <a href="javascript:window.location.reload()" class="btn">Retry Connection</a>
    </div>
</body>
</html>



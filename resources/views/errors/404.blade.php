<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page Not Found | VisionLab</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{background:#0a0a0a;color:#fff;font-family:system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;overflow:hidden;}
        .orb{position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none;}
        .orb-1{width:400px;height:400px;background:rgba(124,58,237,.2);top:-100px;left:-100px;}
        .orb-2{width:300px;height:300px;background:rgba(6,182,212,.15);bottom:-50px;right:-50px;}
        h1{font-size:8rem;font-weight:900;background:linear-gradient(135deg,#8b5cf6,#06b6d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;margin-bottom:16px;}
        h2{font-size:1.25rem;font-weight:700;color:#fff;margin-bottom:8px;}
        p{color:#64748b;font-size:.9rem;margin-bottom:28px;max-width:320px;}
        .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:linear-gradient(135deg,#7c3aed,#4f46e5);border-radius:12px;color:#fff;font-size:.875rem;font-weight:600;text-decoration:none;transition:all .2s;}
        .btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(124,58,237,.4);}
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div style="position:relative;z-index:1;">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <a href="/" class="btn">← Back to Home</a>
    </div>
</body>
</html>

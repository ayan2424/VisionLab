<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline — VisionLab</title>
    <meta name="theme-color" content="#F05000">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #0a0a0a;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 400px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            backdrop-filter: blur(16px);
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.8);
        }
        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            border-radius: 20px;
            background: rgba(239, 68, 68, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .icon svg {
            width: 40px;
            height: 40px;
            color: #f87171;
        }
        h1 {
            margin: 0 0 12px;
            font-size: 24px;
            font-weight: 800;
        }
        p {
            margin: 0 0 32px;
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.6;
        }
        button {
            background: #8b5cf6;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(139, 92, 246, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(139, 92, 246, 0.5);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
            </svg>
        </div>
        <h1>You are Offline</h1>
        <p>It seems your internet connection dropped. VisionLab requires a connection to sync collaborative workspaces and AI features.</p>
        <button onclick="window.location.reload()">Try Again</button>
    </div>
</body>
</html>

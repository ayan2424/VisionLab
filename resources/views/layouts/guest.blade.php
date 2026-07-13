<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — VisionLab' : 'VisionLab' }}</title>

    {{-- Prevent flash of wrong theme --}}
    <script>
        (function() {
            
        })();
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #040d21;
            color: white;
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 50;
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col transition-colors duration-300 selection:bg-[#238636]/30 selection:text-white">

    <!-- Ambient Glow -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[80vw] h-[50vh] bg-[#238636]/10 blur-[150px] pointer-events-none rounded-full z-0"></div>

    <x-frontend-header />

    <main class="relative z-10 w-full flex-1 flex flex-col items-center justify-center pt-20">
        {{ $slot }}
    </main>

    <x-frontend-footer />
</body>
</html>



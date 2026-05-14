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
            var t = localStorage.getItem('vc-theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen transition-colors duration-300"
      style="background:var(--vc-bg);color:var(--vc-text);">

    <div class="relative z-10 w-full">
        {{ $slot }}
    </div>
</body>
</html>

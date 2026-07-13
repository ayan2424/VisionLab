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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex flex-col transition-colors duration-300"
      style="background:var(--vc-bg);color:var(--vc-text);">

    <x-frontend-header />

    <main class="relative z-10 w-full flex-1 flex flex-col items-center justify-center pt-20">
        {{ $slot }}
    </main>

    <x-frontend-footer />
</body>
</html>



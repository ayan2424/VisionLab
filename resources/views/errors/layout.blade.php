<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - VisionLab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen antialiased bg-vc-bg text-vc-text selection:bg-vc-accent/20">

    <div class="fixed top-0 left-0 w-full h-full pointer-events-none">
        <div class="absolute top-0 w-[800px] h-[800px] bg-vc-accent/10 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[100px] translate-x-1/3 translate-y-1/3"></div>
    </div>

    <div class="relative z-10 w-full max-w-2xl px-6 py-12 mx-auto text-center">
        
        <div class="flex justify-center mb-10">
            <a href="/" class="flex items-center gap-2 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-vc-accent to-cyan-600 shadow-[0_0_20px_rgba(240,80,0,0.3)] group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-white">VisionLab</span>
            </a>
        </div>

        <div class="glass p-12 rounded-3xl border border-white/10 shadow-2xl relative overflow-hidden backdrop-blur-xl bg-[#111]/60">
            
            <h1 class="text-[8rem] leading-none font-bold text-transparent bg-clip-text bg-gradient-to-b from-white to-white/20 tracking-tighter mb-4 filter drop-shadow-lg">
                @yield('code', __('Oh no'))
            </h1>
            
            <h2 class="mb-4 text-2xl font-semibold tracking-tight text-white">
                @yield('message')
            </h2>
            
            <p class="mb-10 text-slate-400 max-w-[400px] mx-auto text-balance">
                @yield('description')
            </p>

            <a href="{{ url('/') }}" class="btn-glow inline-flex">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return to Base
            </a>
        </div>
    </div>

</body>
</html>



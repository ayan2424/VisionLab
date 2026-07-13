<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col pt-32 pb-24 px-6 relative overflow-hidden" style="background-color:var(--vc-bg);">
        
        <!-- Ambient Neon Glows -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-[#B026FF]/10 rounded-full blur-[150px] -translate-y-1/2 pointer-events-none z-0"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-[#00F3FF]/10 rounded-full blur-[150px] translate-y-1/2 pointer-events-none z-0"></div>

        <div class="max-w-5xl mx-auto w-full relative z-10">
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white">Platform Features</h1>
                <p class="text-lg" style="color:var(--vc-text-secondary);">Everything you need to teach, learn, and manage.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Feature 1 -->
                <div class="vc-card p-8 rounded-2xl border-t-2" style="border-top-color:#00F3FF;">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background:rgba(0,243,255,0.1); border:1px solid rgba(0,243,255,0.2);">
                        <svg class="w-6 h-6 text-[#00F3FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Cloud IDE & Sandboxing</h3>
                    <p style="color:var(--vc-text-secondary);" class="leading-relaxed">Instant environments via Docker containers. Supports multiple runtimes including Python, Node.js, Java, and C++. Full VS Code interface entirely in your browser.</p>
                </div>

                <!-- Feature 2 -->
                <div class="vc-card p-8 rounded-2xl border-t-2" style="border-top-color:#B026FF;">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background:rgba(176,38,255,0.1); border:1px solid rgba(176,38,255,0.2);">
                        <svg class="w-6 h-6 text-[#B026FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Real-time Collaboration</h3>
                    <p style="color:var(--vc-text-secondary);" class="leading-relaxed">Work together in real-time. Share code, edit collaboratively with multi-cursor support, and use shared terminals just like Google Docs for code.</p>
                </div>

                <!-- Feature 3 -->
                <div class="vc-card p-8 rounded-2xl border-t-2" style="border-top-color:#4ADE80;">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.2);">
                        <svg class="w-6 h-6 text-[#4ADE80]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Responsible AI Agent</h3>
                    <p style="color:var(--vc-text-secondary);" class="leading-relaxed">Our AI acts as a Socratic tutor, helping students debug and understand concepts without giving away solutions. Fully auditable interaction logs for instructors.</p>
                </div>

                <!-- Feature 4 -->
                <div class="vc-card p-8 rounded-2xl border-t-2" style="border-top-color:#F59E0B;">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2);">
                        <svg class="w-6 h-6 text-[#F59E0B]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Enterprise ERP</h3>
                    <p style="color:var(--vc-text-secondary);" class="leading-relaxed">Complete management suite for institutions. Manage campuses, departments, batches, and staff. Built-in financial management with fee challans.</p>
                </div>
            </div>

            <div class="mt-16 text-center">
                <a href="{{ route('home') }}" class="btn-glow !px-8 !py-3 inline-flex">Return Home</a>
            </div>
        </div>
    </div>
</x-guest-layout>

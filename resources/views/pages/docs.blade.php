<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col pt-32 pb-24 px-6 relative overflow-hidden" style="background-color:var(--vc-bg);">
        
        <!-- Ambient Neon Glows -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-[#B026FF]/10 rounded-full blur-[150px] -translate-y-1/2 pointer-events-none z-0"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-[#00F3FF]/10 rounded-full blur-[150px] translate-y-1/2 pointer-events-none z-0"></div>

        <div class="max-w-4xl mx-auto w-full relative z-10">
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white">Documentation</h1>
                <p class="text-lg" style="color:var(--vc-text-secondary);">Everything you need to know to get started.</p>
            </div>

            <div class="vc-card p-8 rounded-2xl mb-8">
                <h2 class="text-2xl font-bold text-white mb-4">Quick Start Guide</h2>
                <p class="text-base leading-relaxed mb-4" style="color:var(--vc-text-secondary);">
                    Getting started with VisionLab is simple. If you are an instructor, you can create a course, configure the environment template (Python, Node, C++, etc.), and invite students via a code. Once a student joins, they automatically get a sandboxed workspace allocated.
                </p>
                <div class="bg-black/30 p-4 rounded-xl border border-white/5 font-mono text-sm text-[#00F3FF] overflow-x-auto">
                    # Example: Starting your first python assignment<br>
                    print("Hello VisionLab!")
                </div>
            </div>

            <div class="space-y-4">
                <div class="vc-card p-6 rounded-xl border-l-4" style="border-left-color:#B026FF;">
                    <h3 class="text-lg font-bold text-white mb-2">Workspace Limitations</h3>
                    <p class="text-sm" style="color:var(--vc-text-secondary);">
                        Each workspace is restricted to 2GB RAM and 1 CPU Core by default. Network access is disabled during exam lockdowns. Do not run crypto miners.
                    </p>
                </div>
                
                <div class="vc-card p-6 rounded-xl border-l-4" style="border-left-color:#00F3FF;">
                    <h3 class="text-lg font-bold text-white mb-2">Using the AI Agent</h3>
                    <p class="text-sm" style="color:var(--vc-text-secondary);">
                        The AI agent can be summoned from the left activity bar. It operates in Chat mode for general questions, Plan mode for project architecture, and Agent mode for autonomous diff generation.
                    </p>
                </div>

                <div class="vc-card p-6 rounded-xl border-l-4" style="border-left-color:#4ADE80;">
                    <h3 class="text-lg font-bold text-white mb-2">Exam Lockdown</h3>
                    <p class="text-sm" style="color:var(--vc-text-secondary);">
                        When an assignment is marked as 'Exam Mode', students are forced into full-screen. Exiting full-screen or switching tabs generates an automatic violation report.
                    </p>
                </div>
            </div>

            <div class="mt-16 text-center">
                <a href="{{ route('home') }}" class="btn-glow !px-8 !py-3 inline-flex">Return Home</a>
            </div>
        </div>
    </div>
</x-guest-layout>

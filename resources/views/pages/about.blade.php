<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col pt-32 pb-24 px-6 relative overflow-hidden" style="background-color:var(--vc-bg);">
        
        <!-- Ambient Neon Glows -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-[#B026FF]/10 rounded-full blur-[150px] -translate-y-1/2 pointer-events-none z-0"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-[#00F3FF]/10 rounded-full blur-[150px] translate-y-1/2 pointer-events-none z-0"></div>

        <div class="max-w-4xl mx-auto w-full relative z-10">
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white">About VisionLab</h1>
                <p class="text-lg" style="color:var(--vc-text-secondary);">Redefining technical education through intelligent infrastructure.</p>
            </div>

            <div class="space-y-8">
                <div class="vc-card p-8 rounded-2xl">
                    <h2 class="text-2xl font-bold text-white mb-4">Our Mission</h2>
                    <p class="text-base leading-relaxed" style="color:var(--vc-text-secondary);">
                        VisionLab was built with a single goal: to democratize access to high-quality software engineering environments for students worldwide. We believe that hardware limitations should never be a barrier to learning. By moving the entire development lifecycle into the cloud, we provide an equitable, powerful, and secure platform for everyone.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="vc-card p-8 rounded-2xl">
                        <h2 class="text-2xl font-bold text-white mb-4">For Educators</h2>
                        <p class="text-base leading-relaxed" style="color:var(--vc-text-secondary);">
                            Automate the tedious parts of teaching. VisionLab handles environment setup, assignment distribution, automated grading, and plagiarism detection. Focus on teaching, while our infrastructure handles the scale.
                        </p>
                    </div>
                    <div class="vc-card p-8 rounded-2xl">
                        <h2 class="text-2xl font-bold text-white mb-4">For Students</h2>
                        <p class="text-base leading-relaxed" style="color:var(--vc-text-secondary);">
                            Code anywhere, anytime, on any device. Experience a professional-grade IDE with real-world tools, augmented by a responsible AI tutor that guides you towards the right answer rather than just giving it to you.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 text-center">
                <a href="{{ route('home') }}" class="btn-glow !px-8 !py-3 inline-flex">Return Home</a>
            </div>
        </div>
    </div>
</x-guest-layout>

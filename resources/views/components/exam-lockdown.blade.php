@props(['isExamMode' => false])

@if($isExamMode)
    <div id="exam-lockdown-gate" class="fixed inset-0 z-[9999] bg-[#0a0a0a] flex flex-col items-center justify-center text-center p-8">
        <div class="max-w-2xl bg-white/5 border border-white/10 rounded-2xl p-8 backdrop-blur-xl relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-red-500/10 z-0"></div>
            
            <div class="relative z-10">
                <div class="w-20 h-20 bg-orange-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-white mb-4">Exam Security Lockdown</h1>
                
                <div class="space-y-4 text-gray-400 mb-8 text-left bg-black/30 p-6 rounded-xl border border-white/5">
                    <p class="font-medium text-white">By entering this exam, you agree to the following strict conditions:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>You must remain in <strong class="text-white">Full Screen mode</strong> at all times.</li>
                        <li>Switching tabs, minimizing the browser, or using external applications is <strong class="text-red-400">strictly prohibited</strong>.</li>
                        <li>Keyboard shortcuts like <kbd class="bg-gray-800 px-1 rounded">Alt+Tab</kbd> or <kbd class="bg-gray-800 px-1 rounded">F11</kbd> will trigger a security violation.</li>
                        <li>All violations are instantly logged to your instructor.</li>
                    </ul>
                </div>
                
                <button id="btn-enter-lockdown" class="w-full py-4 px-6 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-400 hover:to-red-400 text-white rounded-xl font-semibold shadow-lg shadow-orange-500/25 transition-all transform hover:scale-[1.02]">
                    I Understand. Enter Lockdown & Start Exam
                </button>
            </div>
        </div>
    </div>

    <div id="exam-violation-overlay" class="fixed inset-0 z-[10000] bg-red-950/95 flex flex-col items-center justify-center text-center p-8 hidden backdrop-blur-2xl">
        <div class="max-w-md relative z-10">
            <svg class="w-24 h-24 text-red-500 mx-auto mb-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h1 class="text-4xl font-bold text-white mb-4">SECURITY VIOLATION</h1>
            <p class="text-red-200 text-lg mb-8">You have exited the secure exam environment. This incident has been logged.</p>
            <button id="btn-return-lockdown" class="px-8 py-4 bg-red-600 hover:bg-red-500 text-white rounded-xl font-semibold shadow-lg shadow-red-500/25 transition-all">
                Return to Fullscreen Exam
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const gate = document.getElementById('exam-lockdown-gate');
            const violationOverlay = document.getElementById('exam-violation-overlay');
            const btnEnter = document.getElementById('btn-enter-lockdown');
            const btnReturn = document.getElementById('btn-return-lockdown');
            
            let isExamActive = false;
            let violationCount = 0;

            const enterFullscreen = async () => {
                try {
                    const elem = document.documentElement;
                    if (elem.requestFullscreen) {
                        await elem.requestFullscreen();
                    } else if (elem.webkitRequestFullscreen) { /* Safari */
                        await elem.webkitRequestFullscreen();
                    } else if (elem.msRequestFullscreen) { /* IE11 */
                        await elem.msRequestFullscreen();
                    }
                    return true;
                } catch (err) {
                    alert('Error attempting to enable fullscreen mode: ' + err.message);
                    return false;
                }
            };

            const triggerViolation = () => {
                if (!isExamActive) return;
                violationCount++;
                violationOverlay.classList.remove('hidden');
                
                // Fire an event to the backend API
                fetch('/api/analytics/events', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        event_type: 'exam_lockdown_violation',
                        metadata: { 
                            reason: 'Left fullscreen or switched tabs',
                            violation_number: violationCount
                        }
                    })
                }).catch(e => console.error(e));
            };

            btnEnter.addEventListener('click', async () => {
                const success = await enterFullscreen();
                if (success) {
                    gate.classList.add('hidden');
                    isExamActive = true;
                }
            });

            btnReturn.addEventListener('click', async () => {
                const success = await enterFullscreen();
                if (success) {
                    violationOverlay.classList.add('hidden');
                }
            });

            // Prevent exiting fullscreen via escape key natively where possible
            document.addEventListener('fullscreenchange', () => {
                if (isExamActive && !document.fullscreenElement) {
                    triggerViolation();
                }
            });

            // Detect tab switching or minimizing
            document.addEventListener('visibilitychange', () => {
                if (isExamActive && document.visibilityState === 'hidden') {
                    triggerViolation();
                }
            });
            
            // Detect loss of window focus
            window.addEventListener('blur', () => {
                if (isExamActive) {
                    triggerViolation();
                }
            });

            // Trap dangerous keyboard shortcuts (Alt, F11, Ctrl/Cmd shortcuts)
            document.addEventListener('keydown', (e) => {
                if (!isExamActive) return;
                
                // Prevent F11 (Default fullscreen toggle)
                if (e.key === 'F11') {
                    e.preventDefault();
                }
                
                // Try to prevent typical close/switch shortcuts
                if (e.altKey || e.metaKey || (e.ctrlKey && ['t', 'n', 'w', 'q'].includes(e.key.toLowerCase()))) {
                    e.preventDefault();
                }
            });

            // Prevent closing the tab by accident
            window.addEventListener('beforeunload', (e) => {
                if (isExamActive) {
                    e.preventDefault();
                    e.returnValue = 'You are in an active exam. Are you sure you want to leave? Your attempt may be forfeited.';
                    return e.returnValue;
                }
            });
        });
    </script>
@endif

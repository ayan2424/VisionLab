<div id="visioncode-toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none">
    <!-- Toasts will be injected here by JS -->
</div>

<style>
    .vc-toast {
        animation: toast-slide-in 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transform: translateX(120%);
    }
    .vc-toast.hide {
        animation: toast-slide-out 0.3s ease-in forwards;
    }
    @keyframes toast-slide-in {
        to { transform: translateX(0); }
    }
    @keyframes toast-slide-out {
        to { transform: translateX(120%); opacity: 0; }
    }
</style>

<script>
    window.VisionCode = window.VisionCode || {};
    
    window.VisionCode.toast = function(message, type = 'success') {
        const container = document.getElementById('visioncode-toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        
        let iconHtml = '';
        let colorClass = '';
        let bgClass = 'bg-[#111111] border-[#222222]'; // Default dark

        if (type === 'success') {
            colorClass = 'text-green-400';
            bgClass = 'bg-green-950/40 border-green-500/20';
            iconHtml = `<svg class="w-5 h-5 ${colorClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
        } else if (type === 'error') {
            colorClass = 'text-red-400';
            bgClass = 'bg-red-950/40 border-red-500/20';
            iconHtml = `<svg class="w-5 h-5 ${colorClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;
        } else if (type === 'warning') {
            colorClass = 'text-cyan-400';
            bgClass = 'bg-cyan-950/40 border-cyan-500/20';
            iconHtml = `<svg class="w-5 h-5 ${colorClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`;
        } else {
            colorClass = 'text-blue-400';
            bgClass = 'bg-blue-950/40 border-blue-500/20';
            iconHtml = `<svg class="w-5 h-5 ${colorClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
        }

        toast.className = `vc-toast flex items-center gap-3 px-4 py-3 min-w-[250px] max-w-[350px] rounded-lg border backdrop-blur-md shadow-lg pointer-events-auto ${bgClass}`;
        
        toast.innerHTML = `
            <div class="flex-shrink-0">${iconHtml}</div>
            <div class="flex-1 text-sm font-medium text-slate-200">${message}</div>
            <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-slate-500 hover:text-slate-300 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;

        container.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('hide');
            toast.addEventListener('animationend', () => toast.remove());
        }, 5000);
    };
</script>



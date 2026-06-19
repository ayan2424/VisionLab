{{-- Toast notification container — include once in main layout --}}
<div x-data="toast()" x-on:toast.window="show($event.detail)"
     class="fixed top-4 right-4 z-[100] flex flex-col gap-3 w-80 pointer-events-none">
    <template x-for="msg in messages" :key="msg.id">
        <div x-show="msg.visible"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="pointer-events-auto bg-[var(--vc-surface)] border border-[var(--vc-border)] rounded-xl shadow-xl px-4 py-3 flex items-start gap-3">
            <span class="text-lg mt-0.5" x-text="msg.icon"></span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-[var(--vc-text)]" x-text="msg.title"></p>
                <p class="text-xs text-[var(--vc-muted)] mt-0.5" x-text="msg.body" x-show="msg.body"></p>
            </div>
            <button x-on:click="dismiss(msg.id)" class="text-[var(--vc-muted)] hover:text-[var(--vc-text)] transition-colors p-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

<script>
function toast() {
    return {
        messages: [],
        nextId: 0,
        show({ type = 'info', title = '', body = '', duration = 4000 }) {
            const icons = { success: '✓', error: '✕', warning: '⚠', info: '💡' };
            const id = this.nextId++;
            const msg = { id, title, body, icon: icons[type] || icons.info, visible: true };
            this.messages.push(msg);
            if (duration > 0) {
                setTimeout(() => this.dismiss(id), duration);
            }
        },
        dismiss(id) {
            const msg = this.messages.find(m => m.id === id);
            if (msg) msg.visible = false;
            setTimeout(() => { this.messages = this.messages.filter(m => m.id !== id); }, 300);
        }
    };
}
</script>



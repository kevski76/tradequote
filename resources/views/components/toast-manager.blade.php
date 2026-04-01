<div
    x-data="{
        toasts: [],
        add(detail) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message: detail.message, type: detail.type ?? 'success' });
            setTimeout(() => this.remove(id), detail.duration ?? 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @toast.window="add($event.detail)"
    class="fixed right-5 top-5 z-50 flex flex-col items-end gap-3"
    role="status"
    aria-live="polite"
    aria-atomic="false"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-4 opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-4 opacity-0"
            :class="{
                'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200': toast.type === 'success',
                'border-red-200 bg-red-50 text-red-800 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200': toast.type === 'error',
                'border-indigo-200 bg-indigo-50 text-indigo-800 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-200': toast.type === 'info',
            }"
            class="flex min-w-72 max-w-sm items-start gap-3 rounded-xl border px-4 py-3 text-sm font-medium shadow-lg"
        >
            <span x-text="toast.message" class="flex-1 leading-snug"></span>
            <button
                type="button"
                @click="remove(toast.id)"
                class="mt-0.5 shrink-0 opacity-50 transition hover:opacity-100"
                aria-label="Dismiss"
            >&times;</button>
        </div>
    </template>
</div>

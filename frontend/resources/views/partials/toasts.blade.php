<div
    x-data
    class="fixed bottom-5 right-5 left-5 sm:left-auto z-[100] flex flex-col items-stretch sm:items-end gap-2 pointer-events-none"
>
    <template x-for="t in $store.toast.items" :key="t.id">
        <div
            class="animate-toast pointer-events-auto flex items-center gap-3 rounded-lg border px-4 py-3 text-sm shadow-lg backdrop-blur"
            :class="t.tone === 'bad' ? 'bg-danger/10 border-danger/30 text-danger' : 'bg-surface border-line text-ink'"
        >
            <svg x-show="t.tone !== 'bad'" class="w-4 h-4 shrink-0 text-teal" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/></svg>
            <svg x-show="t.tone === 'bad'" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
            <span x-text="t.message" class="flex-1"></span>
            <button @click="$store.toast.dismiss(t.id)" class="shrink-0 text-ink-soft hover:text-ink">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
    </template>
</div>

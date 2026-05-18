<div class="flex items-center gap-3">
    <span class="hidden xl:inline text-xs font-bold text-slate-400 uppercase tracking-widest">Event</span>
    <div class="relative">
        <select wire:model="selectedEventId" class="pl-4 pr-10 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
            @forelse($events as $event)
                <option value="{{ $event->id }}">{{ $event->status === 'active' ? '● ' : '' }}{{ $event->name }}</option>
            @empty
                <option value="">Belum ada event</option>
            @endforelse
        </select>
        <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
</div>


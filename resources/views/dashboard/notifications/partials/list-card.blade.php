<div class="card p-4 md:p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl shadow-sm space-y-4">
    @if($notifications->isEmpty())
        <div class="text-center py-16 text-slate-400 dark:text-slate-550 space-y-2">
            <span class="text-3xl block">🔔</span>
            <p class="text-xs font-semibold">You have no notifications at the moment.</p>
        </div>
    @else
        <div class="divide-y divide-slate-100 dark:divide-slate-800/60">
            @foreach($notifications as $notif)
                <form method="POST" action="{{ route('notifications.read', $notif) }}" class="block">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full text-left py-4 first:pt-0 last:pb-0 hover:bg-slate-50/50 dark:hover:bg-slate-950/20 transition flex items-start gap-4">
                        <div class="w-2 h-2 rounded-full mt-2 shrink-0 {{ $notif->read_at ? 'bg-transparent' : 'bg-blue-650 dark:bg-blue-400' }}"></div>
                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="text-xs font-bold leading-snug {{ $notif->read_at ? 'text-slate-500 dark:text-slate-400' : 'text-slate-800 dark:text-slate-100' }}">
                                    {{ $notif->title }}
                                </h3>
                                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-505 font-mono shrink-0">
                                    {{ $notif->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-555 dark:text-slate-400 leading-relaxed">
                                {{ $notif->message }}
                            </p>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

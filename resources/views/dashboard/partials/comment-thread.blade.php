<div class="card space-y-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
        <div>
            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Communication</span>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 font-display uppercase">Request Thread</h2>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Citizens and staff can exchange messages on this request.</p>
        </div>
        <span class="px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-300 text-[10px] font-bold uppercase">{{ $comments->count() }} Messages</span>
    </div>

    <div class="space-y-4 max-h-[28rem] overflow-y-auto pr-1">
        @forelse($comments as $comment)
            <div class="flex {{ $comment->is_staff ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[85%] rounded-2xl px-4 py-3 {{ $comment->is_staff ? 'bg-[#1e40af] text-white rounded-br-md' : 'bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-bl-md border border-slate-100 dark:border-slate-700' }}">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-[10px] font-black uppercase tracking-wider {{ $comment->is_staff ? 'text-blue-100' : 'text-[#1e40af] dark:text-blue-400' }}">
                            {{ $comment->authorLabel() }}
                        </span>
                        @if($comment->is_staff)
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-white/15 uppercase font-bold">Staff</span>
                        @endif
                        <span class="text-[9px] {{ $comment->is_staff ? 'text-blue-100/80' : 'text-slate-400' }}">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm leading-relaxed whitespace-pre-line">{{ $comment->body }}</p>

                    @if($comment->hasAttachment())
                        <div class="mt-3 pt-3 border-t {{ $comment->is_staff ? 'border-white/20' : 'border-slate-200 dark:border-slate-600' }}">
                            @if($comment->isImageAttachment())
                                <a href="{{ route('dashboard.requests.comments.attachment', [$type, $req->id, $comment]) }}" target="_blank" class="block">
                                    <img src="{{ $comment->attachmentUrl() }}" alt="Attachment" class="max-h-40 rounded-xl border border-white/20">
                                </a>
                            @else
                                <a href="{{ route('dashboard.requests.comments.attachment', [$type, $req->id, $comment]) }}"
                                   class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-wide {{ $comment->is_staff ? 'text-blue-100 hover:text-white' : 'text-[#1e40af] dark:text-blue-400 hover:underline' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    {{ $comment->attachment_original_name ?? 'Download attachment' }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 border border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50/50 dark:bg-slate-900/50">
                <p class="text-sm text-slate-500 dark:text-slate-400">No messages yet. Start the conversation below.</p>
            </div>
        @endforelse
    </div>

    <form method="POST" action="{{ route('dashboard.requests.comments.store', [$type, $req->id]) }}" enctype="multipart/form-data" class="space-y-3 border-t border-slate-100 dark:border-slate-800 pt-4">
        @csrf
        <label class="block">
            <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 block">Your Message</span>
            <textarea name="body" rows="3" required maxlength="5000" placeholder="Write a reply to the requestor..."
                class="w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 px-4 py-3 text-sm focus:ring-2 focus:ring-[#1e40af]/30 focus:border-[#1e40af] transition">{{ old('body') }}</textarea>
        </label>
        @error('body')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror

        <div class="flex flex-col sm:flex-row sm:items-end gap-3">
            <label class="flex-1 block">
                <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 block">Optional Attachment</span>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx"
                    class="w-full text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-blue-50 dark:file:bg-blue-950/40 file:text-[#1e40af] dark:file:text-blue-300 file:font-bold file:text-[11px] file:uppercase">
            </label>
            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-[#1e40af] hover:bg-blue-800 text-white text-[11px] font-black uppercase tracking-wider transition active:scale-95 shrink-0">
                Post Message
            </button>
        </div>
        @error('attachment')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
    </form>
</div>

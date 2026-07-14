@if($logs->isEmpty())
    <div class="text-xs text-slate-400 py-4">No logged changes found for this request.</div>
@else
    <div class="relative pl-6 space-y-6 before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
        @foreach($logs as $log)
            <div class="relative flex items-start space-x-3 text-xs text-slate-600">
                <!-- Circle dot indicator -->
                @php
                    $dotColor = match($log->action) {
                        'request_created' => 'bg-blue-600 ring-blue-100',
                        'status_changed' => match($log->payload['to'] ?? '') {
                            'approved' => 'bg-emerald-600 ring-emerald-100',
                            'declined' => 'bg-rose-600 ring-rose-100',
                            default => 'bg-amber-500 ring-amber-100'
                        },
                        default => 'bg-slate-400 ring-slate-100'
                    };
                @endphp
                <div class="absolute -left-[19px] top-1 w-2.5 h-2.5 rounded-full {{ $dotColor }} ring-4 z-10"></div>
                
                <div class="flex-1 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-800">
                            @if($log->action == 'request_created')
                                Request Submitted
                            @elseif($log->action == 'status_changed')
                                Status updated to <span class="capitalize font-black text-slate-900">{{ $log->payload['to'] ?? '' }}</span>
                            @else
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            @endif
                        </span>
                        <span class="text-[10px] text-slate-400 font-semibold" title="{{ $log->created_at->toDateTimeString() }}">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-slate-500 text-[11px]">
                        @if($log->action == 'request_created')
                            Public submission filed via portal forms.
                        @elseif($log->action == 'status_changed')
                            Updated from <span class="capitalize font-semibold">{{ $log->payload['from'] ?? '' }}</span> by <span class="font-bold text-slate-700">{{ $log->user ? $log->user->name : 'Desk Officer' }}</span>.
                        @else
                            Performed by {{ $log->user ? $log->user->name : 'System' }} (IP: {{ $log->ip_address }}).
                        @endif
                    </p>
                </div>
            </div>
        @endforeach
    </div>
@endif

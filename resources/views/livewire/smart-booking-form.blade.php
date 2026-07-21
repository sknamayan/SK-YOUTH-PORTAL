<div class="space-y-4">
    <!-- Preferred Date Picker -->
    <div>
        <label for="preferred_date" class="block text-xs font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">
            Preferred Date <span class="text-rose-500">*</span>
        </label>
        <input 
            type="date" 
            id="preferred_date" 
            wire:model.live="preferred_date" 
            min="{{ date('Y-m-d') }}"
            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-xs text-slate-800 dark:text-white outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 transition"
            required
        />
    </div>

    <!-- Preferred Time Select Dropdown -->
    <div>
        <label for="preferred_time" class="block text-xs font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">
            Preferred Time Slot <span class="text-rose-500">*</span>
        </label>
        <select 
            id="preferred_time" 
            wire:model="preferred_time"
            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-xs text-slate-800 dark:text-white outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 transition cursor-pointer disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:cursor-not-allowed"
            @empty($preferred_date) disabled @endempty
            required
        >
            <option value="">
                {{ empty($preferred_date) ? '-- Select a Date First --' : '-- Select Time Slot --' }}
            </option>

            @foreach ($timeSlots as $slot)
                @php
                    $isBooked = in_array($slot, $bookedSlots, true);
                @endphp
                <option 
                    value="{{ $slot }}" 
                    @disabled($isBooked)
                    @if($isBooked) class="text-slate-400 bg-slate-100 dark:bg-slate-800" @endif
                >
                    {{ $slot }} {{ $isBooked ? '(Taken)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
</div>

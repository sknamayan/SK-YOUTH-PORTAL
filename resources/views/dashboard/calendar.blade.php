@extends('layouts.app')

@section('content')
<div
    x-data="calendarDashboard({
        isAdmin: {{ Auth::user()->isAdmin() ? 'true' : 'false' }},
        storeUrl: '{{ route('dashboard.calendar.events.store') }}'
    })"
    x-init="init()"
    class="flex-1 flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950 min-h-0"
>

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    {{-- Mobile sidebar backdrop --}}
    <div
        x-show="mobileSidebar"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileSidebar = false"
        class="fixed inset-0 bg-slate-900/50 dark:bg-black/60 z-20 md:hidden"
        aria-hidden="true"
        x-cloak
    ></div>

    <!-- Main Content Pane -->
    <div class="flex-1 flex flex-col min-w-0 min-h-0 md:min-h-[calc(100dvh-4rem)]">
        
        {{-- Sticky mobile app bar --}}

        <div class="p-4 md:p-8 space-y-6 flex-1 overflow-y-auto pb-24 md:pb-8">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Aggregated Schedule</span>
                    <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 dark:text-slate-100 font-display uppercase mt-1">Master Calendar</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Unified calendar view for consultations, sports events, disclosures, and custom programs.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <button
                        type="button"
                        @click="openAddEventModalWithDefault()"
                        class="hidden sm:inline-flex btn-primary text-xs shrink-0 items-center gap-2 min-h-11 shadow-sm"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Schedule Event / Program</span>
                    </button>
                @endif
            </div>

            <!-- Double Grid Calendar + Details/Filters -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
                
                <!-- Left: FullCalendar view (3 Cols) -->
                <div class="lg:col-span-3 card p-4 md:p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl shadow-sm">
                    <div id="calendar" class="min-h-[500px]" x-ignore></div>
                </div>

                <!-- Right Sidebar: Details & Filters (1 Col) -->
                <div class="space-y-6">
                    
                    <!-- Dynamic Date Schedule Details Container -->
                    <div class="card p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl shadow-sm space-y-4">
                        <div class="flex flex-col gap-1">
                            <h3 class="text-xs font-bold text-slate-850 dark:text-slate-200 uppercase tracking-wider font-display">Schedule Details</h3>
                            <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-wider font-mono" x-text="selectedDateFormatted"></span>
                        </div>
                        <hr class="border-slate-100 dark:border-slate-800">
                        
                        <!-- Event list for the selected date -->
                        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                            <template x-if="selectedDateEvents.length === 0">
                                <div class="text-center py-8 text-slate-400 dark:text-slate-500 space-y-2">
                                    <span class="text-2xl block">📅</span>
                                    <p class="text-xs font-medium">No events scheduled</p>
                                    <template x-if="isAdmin">
                                        <button @click="openAddEventModal(selectedDateStr)" class="text-[10px] font-bold text-blue-650 dark:text-blue-405 hover:underline uppercase tracking-wider block mx-auto">
                                            + Schedule Event
                                        </button>
                                    </template>
                                </div>
                            </template>
                            
                            <template x-for="event in selectedDateEvents" :key="event.id">
                                <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 flex flex-col justify-between gap-3 transition hover:bg-slate-100/50 dark:hover:bg-slate-900/40">
                                    <div class="flex items-start gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full mt-0.5 shrink-0 block" :style="'background-color: ' + getEventColor(event.extendedProps.type)"></span>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-xs font-bold text-slate-805 dark:text-slate-200 leading-snug break-words" x-text="event.title"></h4>
                                            <p class="text-[9px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mt-1" x-text="getEventTimeText(event)"></p>
                                            <template x-if="event.extendedProps.description">
                                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1.5 break-words line-clamp-3 leading-relaxed" x-text="event.extendedProps.description"></p>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100/50 dark:border-slate-800/40">
                                        <template x-if="event.url">
                                            <a :href="event.url" class="inline-flex items-center text-[9px] font-bold uppercase tracking-wider text-blue-650 dark:text-blue-400 hover:underline">
                                                View Request
                                            </a>
                                        </template>
                                        <template x-if="!event.url && isAdmin && event.id.startsWith('custom_')">
                                            <button @click="openEditEventModal(event)" class="inline-flex items-center text-[9px] font-bold uppercase tracking-wider text-[#1e40af] dark:text-blue-400 hover:underline">
                                                Edit Event
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <template x-if="isAdmin">
                            <button
                                type="button"
                                @click="openAddEventModal(selectedDateStr)"
                                class="w-full inline-flex items-center justify-center min-h-10 px-4 py-2 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/80 rounded-xl transition font-bold text-[10px] uppercase tracking-wider"
                            >
                                Schedule Event on Date
                            </button>
                        </template>
                    </div>

                    <!-- Filter Categories (Combined Legend and Toggle Filters) -->
                    <div class="card p-5 space-y-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl shadow-sm">
                        <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider font-display">Filter Categories</h3>
                        <hr class="border-slate-100 dark:border-slate-800">
                        
                        <div class="space-y-2">
                            <!-- Health Filter -->
                            <label class="relative flex items-center justify-between p-2 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition cursor-pointer select-none overflow-hidden">
                                <input type="checkbox" id="filter-health" checked class="sr-only peer filter-checkbox" />
                                <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-blue-300 peer-checked:bg-blue-50/20 transition-all pointer-events-none"></div>
                                <div class="flex items-center space-x-2 relative z-10">
                                    <span class="w-2.5 h-2.5 rounded bg-[#3b82f6] block"></span>
                                    <span class="text-xs font-bold text-slate-655 dark:text-slate-350 peer-checked:text-blue-900 transition-colors">Health Consults</span>
                                </div>
                                <div class="w-8 h-4.5 bg-slate-250 dark:bg-slate-800 peer-checked:bg-blue-600 rounded-full transition-all relative flex items-center z-10">
                                    <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                </div>
                            </label>

                            <!-- Silid Filter -->
                            <label class="relative flex items-center justify-between p-2 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition cursor-pointer select-none overflow-hidden">
                                <input type="checkbox" id="filter-silid" checked class="sr-only peer filter-checkbox" />
                                <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-pink-300 peer-checked:bg-pink-50/20 transition-all pointer-events-none"></div>
                                <div class="flex items-center space-x-2 relative z-10">
                                    <span class="w-2.5 h-2.5 rounded bg-[#ec4899] block"></span>
                                    <span class="text-xs font-bold text-slate-655 dark:text-slate-350 peer-checked:text-pink-900 transition-colors">Study Rooms</span>
                                </div>
                                <div class="w-8 h-4.5 bg-slate-250 dark:bg-slate-800 peer-checked:bg-pink-600 rounded-full transition-all relative flex items-center z-10">
                                    <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                </div>
                            </label>

                            <!-- Sports Filter -->
                            <label class="relative flex items-center justify-between p-2 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition cursor-pointer select-none overflow-hidden">
                                <input type="checkbox" id="filter-sports" checked class="sr-only peer filter-checkbox" />
                                <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-emerald-300 peer-checked:bg-emerald-50/20 transition-all pointer-events-none"></div>
                                <div class="flex items-center space-x-2 relative z-10">
                                    <span class="w-2.5 h-2.5 rounded bg-[#10b981] block"></span>
                                    <span class="text-xs font-bold text-slate-655 dark:text-slate-350 peer-checked:text-emerald-900 transition-colors">SIKLAB</span>
                                </div>
                                <div class="w-8 h-4.5 bg-slate-250 dark:bg-slate-800 peer-checked:bg-emerald-600 rounded-full transition-all relative flex items-center z-10">
                                    <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                </div>
                            </label>

                            <!-- Medicine Filter -->
                            <label class="relative flex items-center justify-between p-2 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition cursor-pointer select-none overflow-hidden">
                                <input type="checkbox" id="filter-medicine" checked class="sr-only peer filter-checkbox" />
                                <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-purple-300 peer-checked:bg-purple-50/20 transition-all pointer-events-none"></div>
                                <div class="flex items-center space-x-2 relative z-10">
                                    <span class="w-2.5 h-2.5 rounded bg-[#8b5cf6] block"></span>
                                    <span class="text-xs font-bold text-slate-655 dark:text-slate-350 peer-checked:text-purple-900 transition-colors">Medicine Services</span>
                                </div>
                                <div class="w-8 h-4.5 bg-slate-250 dark:bg-slate-800 peer-checked:bg-purple-600 rounded-full transition-all relative flex items-center z-10">
                                    <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                </div>
                            </label>

                            <!-- News Filter -->
                            <label class="relative flex items-center justify-between p-2 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition cursor-pointer select-none overflow-hidden">
                                <input type="checkbox" id="filter-news" checked class="sr-only peer filter-checkbox" />
                                <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-orange-300 peer-checked:bg-orange-50/20 transition-all pointer-events-none"></div>
                                <div class="flex items-center space-x-2 relative z-10">
                                    <span class="w-2.5 h-2.5 rounded bg-[#f97316] block"></span>
                                    <span class="text-xs font-bold text-slate-655 dark:text-slate-350 peer-checked:text-orange-900 transition-colors">News Articles</span>
                                </div>
                                <div class="w-8 h-4.5 bg-slate-250 dark:bg-slate-800 peer-checked:bg-orange-600 rounded-full transition-all relative flex items-center z-10">
                                    <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                </div>
                            </label>

                            <!-- Transparency Filter -->
                            <label class="relative flex items-center justify-between p-2 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition cursor-pointer select-none overflow-hidden">
                                <input type="checkbox" id="filter-transparency" checked class="sr-only peer filter-checkbox" />
                                <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-slate-300 peer-checked:bg-slate-50/20 transition-all pointer-events-none"></div>
                                <div class="flex items-center space-x-2 relative z-10">
                                    <span class="w-2.5 h-2.5 rounded bg-[#64748b] block"></span>
                                    <span class="text-xs font-bold text-slate-655 dark:text-slate-350 peer-checked:text-slate-900 transition-colors">Transparency Posts</span>
                                </div>
                                <div class="w-8 h-4.5 bg-slate-250 dark:bg-slate-800 peer-checked:bg-slate-600 rounded-full transition-all relative flex items-center z-10">
                                    <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                </div>
                            </label>

                            <!-- Custom Events Filter -->
                            <label class="relative flex items-center justify-between p-2 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition cursor-pointer select-none overflow-hidden">
                                <input type="checkbox" id="filter-custom" checked class="sr-only peer filter-checkbox" />
                                <div class="absolute inset-0 border border-transparent rounded-xl peer-checked:border-green-300 peer-checked:bg-green-50/20 transition-all pointer-events-none"></div>
                                <div class="flex items-center space-x-2 relative z-10">
                                    <span class="w-2.5 h-2.5 rounded bg-[#22c55e] block"></span>
                                    <span class="text-xs font-bold text-slate-655 dark:text-slate-350 peer-checked:text-green-900 transition-colors">Custom Events</span>
                                </div>
                                <div class="w-8 h-4.5 bg-slate-250 dark:bg-slate-800 peer-checked:bg-green-600 rounded-full transition-all relative flex items-center z-10">
                                    <div class="w-3.5 h-3.5 bg-white rounded-full transition-transform transform translate-x-0.5 peer-checked:translate-x-4 shadow-xs"></div>
                                </div>
                            </label>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- Mobile FAB: Schedule Event --}}
        @if(Auth::user()->isAdmin())
            <div class="fixed bottom-0 inset-x-0 z-20 md:hidden pointer-events-none px-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button
                    type="button"
                    @click="openAddEventModalWithDefault()"
                    class="pointer-events-auto w-full inline-flex items-center justify-center gap-2 min-h-[3.25rem] btn-primary text-xs font-bold uppercase tracking-wider shadow-lg shadow-blue-900/20 rounded-2xl"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Schedule Event / Program</span>
                </button>
            </div>
        @endif

    </div>

    {{-- Add Event Modal --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openAddModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="add-event-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openAddModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openAddModal = false"
                ></div>

                {{-- Panel --}}
                <div
                    x-show="openAddModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openAddModal = false"
                >
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">New Event</span>
                            <h2 id="add-event-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Schedule Event / Program</h2>
                        </div>
                        <button
                            type="button"
                            @click="openAddModal = false"
                            class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-400 dark:text-slate-500 hover:text-slate-655 dark:hover:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitAddEvent()" class="flex-1 overflow-y-auto px-4 md:px-8 py-4 space-y-4">
                        <div class="space-y-1">
                            <label for="add-title" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Event / Program Title</label>
                            <input
                                id="add-title"
                                type="text"
                                x-model="addForm.title"
                                required
                                placeholder="e.g. Linggo ng Kabataan Sportsfest"
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 text-xs py-2.5 min-h-11"
                            >
                            <span x-show="errors.title" x-text="errors.title" class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="add-start" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Start Date & Time</label>
                                <input
                                    id="add-start"
                                    type="datetime-local"
                                    x-model="addForm.start_time"
                                    required
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 text-xs py-2.5 min-h-11"
                                >
                                <span x-show="errors.start_time" x-text="errors.start_time" class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1"></span>
                            </div>

                            <div class="space-y-1">
                                <label for="add-end" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">End Date & Time (Optional)</label>
                                <input
                                    id="add-end"
                                    type="datetime-local"
                                    x-model="addForm.end_time"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 text-xs py-2.5 min-h-11"
                                >
                                <span x-show="errors.end_time" x-text="errors.end_time" class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1"></span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="add-description" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Event Description</label>
                            <textarea
                                id="add-description"
                                x-model="addForm.description"
                                rows="3"
                                placeholder="Describe the program's schedule details and agenda..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 text-xs min-h-[5rem]"
                            ></textarea>
                            <span x-show="errors.description" x-text="errors.description" class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1"></span>
                        </div>

                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openAddModal = false"
                                class="btn-outline text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto">
                                Save Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    {{-- Edit/View Event Modal --}}
    <template x-teleport="body">
        <div
            x-show="openEditModal"
            class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
            data-overscroll-lock="true"
            role="dialog"
            aria-modal="true"
            aria-labelledby="edit-event-title"
            x-cloak
        >
            {{-- Backdrop --}}
            <div
                x-show="openEditModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                @click="openEditModal = false"
            ></div>

            {{-- Panel --}}
            <div
                x-show="openEditModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                class="relative z-50 w-full md:max-w-xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                @click.outside="openEditModal = false"
            >
                <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0">
                    <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                </div>

                <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                    <div class="space-y-0.5 min-w-0 pr-4">
                        <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display" x-text="editForm.type === 'custom' ? 'Custom Event' : 'System Event'">Event details</span>
                        <h2 id="edit-event-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate" x-text="editForm.type === 'custom' ? 'Edit Event / Program' : 'Event Information'">Event details</h2>
                    </div>
                    <button
                        type="button"
                        @click="openEditModal = false"
                        class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-400 dark:text-slate-500 hover:text-slate-655 dark:hover:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="submitEditEvent()" class="flex-1 overflow-y-auto px-4 md:px-8 py-4 space-y-4">
                    <div class="space-y-1">
                        <label for="edit-title" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Event / Program Title</label>
                        <input
                            id="edit-title"
                            type="text"
                            x-model="editForm.title"
                            required
                            :disabled="!isAdmin || editForm.type !== 'custom'"
                            class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 text-xs py-2.5 min-h-11 disabled:bg-slate-50 dark:disabled:bg-slate-950 disabled:text-slate-500"
                        >
                        <span x-show="errors.title" x-text="errors.title" class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="edit-start" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Start Date & Time</label>
                            <input
                                id="edit-start"
                                type="datetime-local"
                                x-model="editForm.start_time"
                                required
                                :disabled="!isAdmin || editForm.type !== 'custom'"
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 text-xs py-2.5 min-h-11 disabled:bg-slate-50 dark:disabled:bg-slate-950 disabled:text-slate-500"
                            >
                            <span x-show="errors.start_time" x-text="errors.start_time" class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1"></span>
                        </div>

                        <div class="space-y-1">
                            <label for="edit-end" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">End Date & Time (Optional)</label>
                            <input
                                id="edit-end"
                                type="datetime-local"
                                x-model="editForm.end_time"
                                :disabled="!isAdmin || editForm.type !== 'custom'"
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 text-xs py-2.5 min-h-11 disabled:bg-slate-50 dark:disabled:bg-slate-950 disabled:text-slate-500"
                            >
                            <span x-show="errors.end_time" x-text="errors.end_time" class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1"></span>
                        </div>
                    </div>

                    <div class="space-y-1" x-show="editForm.type === 'custom' || editForm.description">
                        <label for="edit-description" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Event Description</label>
                        <textarea
                            id="edit-description"
                            x-model="editForm.description"
                            rows="3"
                            :disabled="!isAdmin || editForm.type !== 'custom'"
                            placeholder="Describe the program's schedule details and agenda..."
                            class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 text-xs min-h-[5rem] disabled:bg-slate-50 dark:disabled:bg-slate-950 disabled:text-slate-500"
                        ></textarea>
                        <span x-show="errors.description" x-text="errors.description" class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1"></span>
                    </div>

                    <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                        <template x-if="isAdmin && editForm.type === 'custom'">
                            <button
                                type="button"
                                @click="deleteEvent()"
                                class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold py-2.5 px-6 rounded-xl text-xs transition active:scale-95 shadow-sm border border-transparent w-full sm:w-auto"
                            >
                                Remove Event
                            </button>
                        </template>
                        <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-2 w-full sm:w-auto sm:ml-auto">
                            <button
                                type="button"
                                @click="openEditModal = false"
                                class="btn-outline text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Close
                            </button>
                            <template x-if="isAdmin && editForm.type === 'custom'">
                                <button type="submit" class="btn-primary text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto">
                                    Save Changes
                                </button>
                            </template>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>

<!-- Styles and Scripts for FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<style>
    .fc {
        font-family: inherit;
        font-size: 0.82rem;
    }
    .fc-header-toolbar {
        margin-bottom: 1.5rem !important;
        flex-wrap: wrap;
        gap: 0.6rem;
    }
    .fc-toolbar-title {
        font-size: 1.15rem !important;
        font-weight: 900 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.02em !important;
        color: #1e293b !important;
    }
    .dark .fc-toolbar-title {
        color: #f8fafc !important;
    }
    .fc-button {
        box-shadow: none !important;
    }
    .fc-button-primary {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        color: #475569 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 9px !important;
        letter-spacing: 0.05em !important;
        border-radius: 12px !important;
        padding: 8px 16px !important;
        transition: all 0.15s ease !important;
    }
    .dark .fc-button-primary {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #94a3b8 !important;
    }
    .fc-button-primary:hover {
        background-color: #f8fafc !important;
        color: #1e40af !important;
        border-color: #cbd5e1 !important;
    }
    .dark .fc-button-primary:hover {
        background-color: #1e293b !important;
        color: #60a5fa !important;
        border-color: #475569 !important;
    }
    .fc-button-primary:focus {
        box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.15) !important;
    }
    .fc-button-primary:disabled {
        background-color: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
        color: #94a3b8 !important;
        opacity: 0.7 !important;
    }
    .dark .fc-button-primary:disabled {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #475569 !important;
    }
    .fc-button-active {
        background-color: #1e40af !important;
        border-color: #1e40af !important;
        color: #ffffff !important;
    }
    .dark .fc-button-active {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        color: #ffffff !important;
    }
    .fc-button-active:hover {
        background-color: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
        color: #ffffff !important;
    }
    .fc-event {
        cursor: pointer;
        border-radius: 8px !important;
        padding: 4px 8px !important;
        font-weight: 700 !important;
        font-size: 11px !important;
        border: none !important;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        margin: 2px 0 !important;
    }
    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
    }
    
    /* Health Event: Soft Blue, Strong Left Border */
    .event-health {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        border-left: 3.5px solid #3b82f6 !important;
    }
    .dark .event-health {
        background-color: rgba(37, 99, 235, 0.15) !important;
        color: #93c5fd !important;
        border-left-color: #2563eb !important;
    }
    .event-health:hover {
        background-color: #dbeafe !important;
    }
    .dark .event-health:hover {
        background-color: rgba(37, 99, 235, 0.25) !important;
    }

    /* Silid Event: Soft Pink, Strong Left Border */
    .event-silid {
        background-color: #fdf2f8 !important;
        color: #be185d !important;
        border-left: 3.5px solid #ec4899 !important;
    }
    .dark .event-silid {
        background-color: rgba(236, 72, 153, 0.15) !important;
        color: #fbcfe8 !important;
        border-left-color: #ec4899 !important;
    }
    .event-silid:hover {
        background-color: #fce7f3 !important;
    }
    .dark .event-silid:hover {
        background-color: rgba(236, 72, 153, 0.25) !important;
    }

    /* Sports Event: Soft Green, Strong Left Border */
    .event-sports {
        background-color: #ecfdf5 !important;
        color: #047857 !important;
        border-left: 3.5px solid #10b981 !important;
    }
    .dark .event-sports {
        background-color: rgba(16, 185, 129, 0.15) !important;
        color: #a7f3d0 !important;
        border-left-color: #10b981 !important;
    }
    .event-sports:hover {
        background-color: #d1fae5 !important;
    }
    .dark .event-sports:hover {
        background-color: rgba(16, 185, 129, 0.25) !important;
    }

    /* Medicine Event: Soft Purple, Strong Left Border */
    .event-medicine {
        background-color: #f5f3ff !important;
        color: #6d28d9 !important;
        border-left: 3.5px solid #8b5cf6 !important;
    }
    .dark .event-medicine {
        background-color: rgba(139, 92, 246, 0.15) !important;
        color: #ddd6fe !important;
        border-left-color: #8b5cf6 !important;
    }
    .event-medicine:hover {
        background-color: #ede9fe !important;
    }
    .dark .event-medicine:hover {
        background-color: rgba(139, 92, 246, 0.25) !important;
    }

    /* News Event: Soft Orange, Strong Left Border */
    .event-news {
        background-color: #fff7ed !important;
        color: #c2410c !important;
        border-left: 3.5px solid #f97316 !important;
    }
    .dark .event-news {
        background-color: rgba(249, 115, 22, 0.15) !important;
        color: #fed7aa !important;
        border-left-color: #f97316 !important;
    }
    .event-news:hover {
        background-color: #ffedd5 !important;
    }
    .dark .event-news:hover {
        background-color: rgba(249, 115, 22, 0.25) !important;
    }

    /* Transparency Event: Soft Slate, Strong Left Border */
    .event-transparency {
        background-color: #f8fafc !important;
        color: #334155 !important;
        border-left: 3.5px solid #64748b !important;
    }
    .dark .event-transparency {
        background-color: rgba(100, 116, 139, 0.15) !important;
        color: #cbd5e1 !important;
        border-left-color: #64748b !important;
    }
    .event-transparency:hover {
        background-color: #f1f5f9 !important;
    }
    .dark .event-transparency:hover {
        background-color: rgba(100, 116, 139, 0.25) !important;
    }

    /* Custom Event: Soft Green-50, Strong Left Border */
    .event-custom {
        background-color: #f0fdf4 !important;
        color: #15803d !important;
        border-left: 3.5px solid #22c55e !important;
    }
    .dark .event-custom {
        background-color: rgba(34, 197, 94, 0.15) !important;
        color: #bbf7d0 !important;
        border-left-color: #22c55e !important;
    }
    .event-custom:hover {
        background-color: #dcfce7 !important;
    }
    .dark .event-custom:hover {
        background-color: rgba(34, 197, 94, 0.25) !important;
    }

    /* Event status tags styling */
    .status-declined {
        opacity: 0.5;
        text-decoration: line-through;
    }
    .status-pending {
        border-left-style: dashed !important;
    }

    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #f1f5f9 !important;
    }
    .dark .fc-theme-standard td, .dark .fc-theme-standard th {
        border-color: #1e293b !important;
    }
    .fc-col-header-cell {
        background-color: #f8fafc;
        padding: 10px 0 !important;
        text-transform: uppercase;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .dark .fc-col-header-cell {
        background-color: #0f172a;
        color: #475569;
        border-bottom-color: #1e293b !important;
    }
    .fc-daygrid-day:hover {
        background-color: #f8fafc;
    }
    .dark .fc-daygrid-day:hover {
        background-color: #0f172a;
    }
    .fc-daygrid-day-number {
        font-weight: 700 !important;
        color: #475569 !important;
        font-size: 11px !important;
        padding: 8px !important;
    }
    .dark .fc-daygrid-day-number {
        color: #94a3b8 !important;
    }
    .fc-day-today {
        background-color: rgba(239, 246, 255, 0.4) !important;
    }
    .dark .fc-day-today {
        background-color: rgba(37, 99, 235, 0.08) !important;
    }
    .fc-daygrid-event-dot {
        display: none !important;
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('calendarDashboard', (config) => ({
            isAdmin: config.isAdmin,
            openAddModal: false,
            openEditModal: false,
            
            // Selected date dynamic container state
            selectedDateStr: '',
            selectedDateFormatted: '',
            allEvents: [],

            // Add event form fields
            addForm: {
                title: '',
                description: '',
                start_time: '',
                end_time: ''
            },
            
            // Edit event form fields
            editForm: {
                id: null,
                title: '',
                description: '',
                start_time: '',
                end_time: '',
                type: 'custom'
            },
            
            errors: {},
            calendar: null,

            init() {
                // Initialize to today's date formatted as YYYY-MM-DD
                const today = new Date();
                const y = today.getFullYear();
                const m = String(today.getMonth() + 1).padStart(2, '0');
                const d = String(today.getDate()).padStart(2, '0');
                this.selectDate(`${y}-${m}-${d}`);

                var calendarEl = document.getElementById('calendar');
                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    selectable: true, // Allow selecting cells to load their date details
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    editable: false,
                    events: (info, successCallback, failureCallback) => {
                        var url = new URL('{{ route("dashboard.calendar.events") }}', window.location.origin);
                        url.searchParams.set('start', info.startStr);
                        url.searchParams.set('end', info.endStr);

                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                // Save all fetched events locally in our Alpine store
                                this.allEvents = data;

                                // Filter dynamically for FullCalendar
                                const filtered = data.filter(event => {
                                    const typeChecked = document.getElementById('filter-' + event.extendedProps.type)?.checked ?? true;
                                    return typeChecked;
                                });
                                successCallback(filtered);
                            })
                            .catch(error => {
                                console.error('Error fetching calendar events:', error);
                                failureCallback(error);
                            });
                    },
                    select: (info) => {
                        this.selectDate(info.startStr);
                    },
                    dateClick: (info) => {
                        this.selectDate(info.dateStr);
                    },
                    eventDidMount: (info) => {
                        var type = info.event.extendedProps.type;
                        var status = info.event.extendedProps.status;

                        // Strip default solid colors returned from Laravel backend JSON
                        info.el.style.backgroundColor = '';
                        info.el.style.borderColor = '';
                        info.el.style.color = '';

                        // Add classes for styling via modern CSS rules
                        info.el.classList.add('event-' + type);
                        info.el.classList.add('status-' + status);
                    },
                    eventClick: (info) => {
                        info.jsEvent.preventDefault();
                        const eventDateStr = info.event.startStr.split('T')[0];
                        this.selectDate(eventDateStr);
                    }
                });

                this.calendar.render();

                // Listen for filter checkbox changes to reload events dynamically
                document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', () => {
                        this.calendar.refetchEvents();
                    });
                });

                this.$watch('openAddModal', (locked) => {
                    this.lockBodyScroll(locked);
                });

                this.$watch('openEditModal', (locked) => {
                    this.lockBodyScroll(locked);
                });
            },

            selectDate(dateStr) {
                // Keep only the date portion (e.g. YYYY-MM-DD)
                this.selectedDateStr = dateStr.split('T')[0];
                this.selectedDateFormatted = this.formatDateStr(this.selectedDateStr);
            },

            formatDateStr(dateStr) {
                if (!dateStr) return '';
                // Ensure date parsing doesn't shift timezone by parsing local date components
                const [year, month, day] = dateStr.split('-');
                const date = new Date(year, month - 1, day);
                return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            },

            get selectedDateEvents() {
                return this.allEvents.filter(event => {
                    if (!event.start) return false;
                    const eventDateStr = event.start.split('T')[0];
                    return eventDateStr === this.selectedDateStr;
                });
            },

            getEventColor(type) {
                const colors = {
                    health: '#3b82f6',
                    silid: '#ec4899',
                    sports: '#10b981',
                    medicine: '#8b5cf6',
                    news: '#f97316',
                    transparency: '#64748b',
                    custom: '#22c55e'
                };
                return colors[type] || '#64748b';
            },

            getEventTimeText(event) {
                if (!event.start) return 'All Day';
                if (event.start.includes('T')) {
                    const timePart = event.start.split('T')[1];
                    if (timePart) {
                        const [hoursStr, minutesStr] = timePart.split(':');
                        let hours = parseInt(hoursStr);
                        const ampm = hours >= 12 ? 'PM' : 'AM';
                        hours = hours % 12;
                        hours = hours ? hours : 12;
                        return `${hours}:${minutesStr} ${ampm}`;
                    }
                }
                return 'All Day';
            },

            openAddEventModalWithDefault() {
                this.openAddEventModal(this.selectedDateStr);
            },

            openAddEventModal(startStr) {
                let formattedStart = startStr;
                if (startStr.length === 10) {
                    formattedStart = startStr + 'T09:00';
                }
                
                this.addForm = {
                    title: '',
                    description: '',
                    start_time: formattedStart,
                    end_time: ''
                };
                this.errors = {};
                this.openAddModal = true;
            },

            submitAddEvent() {
                this.errors = {};
                fetch('{{ route("dashboard.calendar.events.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.addForm)
                })
                .then(async (response) => {
                    const data = await response.json();
                    if (response.ok) {
                        this.openAddModal = false;
                        this.calendar.refetchEvents();
                    } else if (response.status === 422) {
                        const validationErrors = {};
                        for (const field in data.errors) {
                            validationErrors[field] = data.errors[field][0];
                        }
                        this.errors = validationErrors;
                    } else {
                        alert(data.error || 'Failed to schedule event.');
                    }
                })
                .catch(error => {
                    console.error('Error scheduling event:', error);
                    alert('An error occurred. Please try again.');
                });
            },

            openEditEventModal(event) {
                const props = event.extendedProps;
                this.editForm = {
                    id: event.id.replace('custom_', ''),
                    title: event.title.replace('Event: ', ''),
                    description: props.description || '',
                    start_time: props.start_time,
                    end_time: props.end_time || '',
                    type: props.type
                };
                this.errors = {};
                this.openEditModal = true;
            },

            submitEditEvent() {
                this.errors = {};
                fetch(`/dashboard/calendar/events/${this.editForm.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.editForm)
                })
                .then(async (response) => {
                    const data = await response.json();
                    if (response.ok) {
                        this.openEditModal = false;
                        this.calendar.refetchEvents();
                    } else if (response.status === 422) {
                        const validationErrors = {};
                        for (const field in data.errors) {
                            validationErrors[field] = data.errors[field][0];
                        }
                        this.errors = validationErrors;
                    } else {
                        alert(data.error || 'Failed to update event.');
                    }
                })
                .catch(error => {
                    console.error('Error updating event:', error);
                    alert('An error occurred. Please try again.');
                });
            },

            deleteEvent() {
                if (confirm('Are you sure you want to remove this event from the calendar?')) {
                    fetch(`/dashboard/calendar/events/${this.editForm.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(async (response) => {
                        const data = await response.json();
                        if (response.ok) {
                            this.openEditModal = false;
                            this.calendar.refetchEvents();
                        } else {
                            alert(data.error || 'Failed to delete event.');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting event:', error);
                        alert('An error occurred. Please try again.');
                    });
                }
            },

            lockBodyScroll(locked) {
                document.documentElement.classList.toggle('overflow-hidden', locked);
                document.body.classList.toggle('overflow-hidden', locked);
                document.body.classList.toggle('touch-none', locked);
            }
        }));
    });
</script>
@endsection

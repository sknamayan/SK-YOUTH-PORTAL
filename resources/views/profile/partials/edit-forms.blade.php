<!-- CSS/JS for Cropper.js (Loaded only when needed) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" integrity="sha512-T59fCw+q/gwRBiCXFTyJXSAL1QC25LyJOIsh5A8IbX3y3IpCeeLq4A1zP/g0Y3+3Hy80g5T4M95d7wE50TC1yA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" integrity="sha512-6Qx4/6mKnWp0w09Hj3Hh9q8NfU59sK51T/K/1V81k9g0S9C5M0J+p5h94S/q9A3C2G7k2O2u675K90+qW50XqgA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- TAB 1: Account Settings / Personal Info -->
<div x-show="activeTab === 'account'" class="space-y-6" x-cloak>
    <!-- Display Name Edit Card (Replaces Avatar Photo Upload Section) -->
    <div class="card space-y-5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-sm w-full">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 font-display uppercase tracking-tight">{{ __('Display Name') }}</h2>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Customize your public display name shown across the SK Namayan portal.') }}</p>
        </div>

        <form method="POST" action="{{ route('profile.update-info') }}" class="space-y-4 w-full">
            @csrf
            @method('PATCH')
            <input type="hidden" name="settings_tab" value="account">

            <div class="space-y-1.5 w-full">
                <label for="display_name_input" class="block text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 font-display">
                    {{ __('Display Name') }}
                </label>
                <input id="display_name_input"
                       type="text" 
                       name="name" 
                       value="{{ old('name', $user->name) }}" 
                       required 
                       placeholder="e.g. Juan Dela Cruz" 
                       class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs text-slate-900 dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/10 transition font-sans shadow-sm">
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 leading-relaxed">
                    {{ __('This display name will be visible to officers and displayed on your public certificates and tournament rosters.') }}
                </p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-[#1e40af] hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-blue-500/15 cursor-pointer">
                    {{ __('Save Display Name') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Personal Details Form -->
    <div class="card space-y-5">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 font-display uppercase tracking-tight">{{ __('Personal Information') }}</h2>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Update your public profile display names, email addresses, and contact numbers.') }}</p>
        </div>

        <form method="POST" action="{{ route('profile.update-info') }}" class="space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="settings_tab" value="account">

            @php
                $kkProfile = $user->approvedKkProfile();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="First Name" name="first_name" :value="mb_strtoupper(old('first_name', $user->first_name ?: $kkProfile?->first_name), 'UTF-8')" required="true" />
                <x-form-input label="Last Name" name="last_name" :value="mb_strtoupper(old('last_name', $user->last_name ?: $kkProfile?->surname), 'UTF-8')" required="true" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Email Address" name="email" :value="old('email', $user->email ?: $kkProfile?->email)" type="email" required="true" />
                <x-form-input label="Contact Number" name="contact_number" :value="old('contact_number', $user->contact_number ?: $kkProfile?->contact_number)" type="tel" placeholder="09xxxxxxxxx" />
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2.5 bg-[#1e40af] hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-blue-500/15">
                    {{ __('Save Information') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB 2: Display & Appearance Preferences -->
<div x-show="activeTab === 'display'" class="space-y-6" x-cloak>
    <div class="card space-y-5">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 font-display uppercase tracking-tight">{{ __('Display & Appearance') }}</h2>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Choose your layout theme mode and language preferences.') }}</p>
        </div>

        <form method="POST" action="{{ route('profile.update-preferences') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="settings_tab" value="display">

            <!-- Theme Preferences selector cards (alpine interactive) -->
            <div class="space-y-3" x-data="{
                currentTheme: '{{ $user->theme }}',
                syncTheme(themePreference) {
                    window.SKTheme.setTheme(themePreference);
                }
            }" x-init="syncTheme(currentTheme)">
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Theme') }}</label>
                <input type="hidden" name="theme" :value="currentTheme">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Light Mode Card -->
                    <div @click="currentTheme = 'light'; syncTheme(currentTheme)"
                         :class="currentTheme === 'light' ? 'border-[#1e40af] bg-blue-50/20 dark:bg-blue-950/10 ring-2 ring-blue-500/10' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-850'"
                         class="border rounded-2xl p-4 flex items-center space-x-3 cursor-pointer transition select-none">
                        <div class="p-2 rounded-xl bg-amber-500/15 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-850 dark:text-white">{{ __('Light') }}</span>
                            <span class="block text-[9px] text-slate-400 dark:text-slate-500">{{ __('Always bright view') }}</span>
                        </div>
                    </div>

                    <!-- Dark Mode Card -->
                    <div @click="currentTheme = 'dark'; syncTheme(currentTheme)"
                         :class="currentTheme === 'dark' ? 'border-[#1e40af] bg-blue-50/20 dark:bg-blue-955/10 ring-2 ring-blue-500/10' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-850'"
                         class="border rounded-2xl p-4 flex items-center space-x-3 cursor-pointer transition select-none">
                        <div class="p-2 rounded-xl bg-indigo-500/15 text-indigo-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-850 dark:text-white">{{ __('Dark') }}</span>
                            <span class="block text-[9px] text-slate-400 dark:text-slate-500">{{ __('Easy on the eyes') }}</span>
                        </div>
                    </div>

                    <!-- System Mode Card -->
                    <div @click="currentTheme = 'system'; syncTheme(currentTheme)"
                         :class="currentTheme === 'system' ? 'border-[#1e40af] bg-blue-50/20 dark:bg-blue-955/10 ring-2 ring-blue-500/10' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-850'"
                         class="border rounded-2xl p-4 flex items-center space-x-3 cursor-pointer transition select-none">
                        <div class="p-2 rounded-xl bg-slate-500/15 text-slate-500 dark:text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-850 dark:text-white">{{ __('System Default') }}</span>
                            <span class="block text-[9px] text-slate-400 dark:text-slate-500">{{ __('Match device theme') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Language Switcher Option -->
            <div class="space-y-2">
                <label for="language-select" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Language') }}</label>
                <select id="language-select" name="language" class="w-full sm:max-w-xs rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs text-slate-700 dark:text-slate-300 font-bold p-3 focus:border-[#1e40af] focus:ring-[#1e40af]">
                    <option value="en" {{ $user->language === 'en' ? 'selected' : '' }}>🇺🇸 {{ __('English') }}</option>
                    <option value="fil" {{ $user->language === 'fil' ? 'selected' : '' }}>🇵🇭 {{ __('Filipino') }}</option>
                </select>
                <p class="text-[9px] text-slate-400 dark:text-slate-500">{{ __('The portal locale adapts automatically to switch translated labels.') }}</p>
            </div>

            <!-- Keep Notify fields unchecked or checked from db -->
            <input type="hidden" name="notify_request_status" value="{{ $user->notify_request_status ? 1 : 0 }}">
            <input type="hidden" name="notify_announcements" value="{{ $user->notify_announcements ? 1 : 0 }}">

            <div class="flex justify-end pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="submit" class="px-5 py-2.5 bg-[#1e40af] hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-blue-500/15">
                    {{ __('Save Preferences') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB 3: Security Settings (Password, Sessions) -->
<div x-show="activeTab === 'security'" class="space-y-6" x-cloak>
    <!-- Change Password panel -->
    <div class="card space-y-5">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 font-display uppercase tracking-tight">{{ __('Update Password') }}</h2>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
        </div>

        <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="settings_tab" value="security">

            <x-form-input label="Current Password" name="current_password" type="password" required="true" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="New Password" name="password" type="password" required="true" />
                <x-form-input label="Confirm New Password" name="password_confirmation" type="password" required="true" />
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2.5 bg-[#1e40af] hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-blue-500/15">
                    {{ __('Change Password') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Active Sessions Browser Devices list -->
    <div class="card space-y-5" x-data="{ logoutConfirm: false }">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 font-display uppercase tracking-tight">{{ __('Active Browser Sessions') }}</h2>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Check other devices currently logged into your portal account, and terminate them if necessary.') }}</p>
        </div>

        <div class="space-y-4">
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($sessions as $session)
                    <div class="flex items-center justify-between py-3.5 first:pt-0 last:pb-0">
                        <div class="flex items-center space-x-3 min-w-0">
                            <!-- Device Laptop / Mobile Icon -->
                            <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-450 shrink-0">
                                @if(str_contains(strtolower($session->device), 'ios') || str_contains(strtolower($session->device), 'android'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $session->device }}</span>
                                <span class="block text-[10px] text-slate-400 dark:text-slate-500 truncate">{{ $session->ip_address }} • {{ $session->last_active }}</span>
                            </div>
                        </div>

                        <!-- Current active session label -->
                        <div>
                            @if($session->is_current)
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-[9px] font-black uppercase tracking-wider font-display">{{ __('This Device') }}</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[9px] font-black uppercase tracking-wider font-display">{{ __('Active') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($sessions->count() > 1)
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="$dispatch('open-modal', 'confirm-logout-other-sessions')" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-slate-850/15">
                        {{ __('Log out of other browser sessions') }}
                    </button>
                </div>

                <!-- Session Terminate confirmation modal -->
                <x-modal name="confirm-logout-other-sessions" focusable>
                    <form method="POST" action="{{ route('profile.logout-other-sessions') }}" class="p-6 space-y-4 text-left">
                        @csrf
                        <input type="hidden" name="settings_tab" value="security">
                        <div>
                            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">{{ __('Log out of other browser sessions') }}</h2>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 leading-relaxed">{{ __('Please enter your account password to confirm logging out of your account on all other devices and browser platforms.') }}</p>
                        </div>

                        <div class="mt-4">
                            <x-form-input label="Confirm Password" name="password" type="password" required="true" />
                        </div>

                        <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="$dispatch('close')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="px-4 py-2 bg-[#1e40af] hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-blue-500/10">
                                {{ __('Logout other sessions') }}
                            </button>
                        </div>
                    </form>
                </x-modal>
            @endif
        </div>
    </div>
</div>

<!-- TAB 4: Notification Preferences -->
<div x-show="activeTab === 'notifications'" class="space-y-6" x-cloak>
    <div class="card space-y-5">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 font-display uppercase tracking-tight">{{ __('Notification Preferences') }}</h2>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Choose when and how the system alerts you about portal changes and announcements.') }}</p>
        </div>

        <form method="POST" action="{{ route('profile.update-preferences') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="settings_tab" value="notifications">

            <!-- Hidden theme/lang fields (preserve state) -->
            <input type="hidden" name="theme" value="{{ $user->theme }}">
            <input type="hidden" name="language" value="{{ $user->language }}">

            <div class="space-y-4">
                <!-- Checkbox 1: Request Status Alert -->
                <label class="flex items-start space-x-3.5 cursor-pointer select-none">
                    <input type="checkbox" name="notify_request_status" value="1" {{ $user->notify_request_status ? 'checked' : '' }} class="w-4 h-4 mt-0.5 rounded border-slate-200 dark:border-slate-800 text-[#1e40af] focus:ring-[#1e40af] focus:ring-offset-0 bg-white dark:bg-slate-900">
                    <div>
                        <span class="block text-xs font-bold text-slate-800 dark:text-slate-100">{{ __('Email me when my request status changes') }}</span>
                        <span class="block text-[9px] text-slate-400 dark:text-slate-500">{{ __('Receive automatic email updates whenever a staff reviews or changes status of your submissions.') }}</span>
                    </div>
                </label>

                <!-- Checkbox 2: Announcement Alerts -->
                <label class="flex items-start space-x-3.5 cursor-pointer select-none">
                    <input type="checkbox" name="notify_announcements" value="1" {{ $user->notify_announcements ? 'checked' : '' }} class="w-4 h-4 mt-0.5 rounded border-slate-200 dark:border-slate-800 text-[#1e40af] focus:ring-[#1e40af] focus:ring-offset-0 bg-white dark:bg-slate-900">
                    <div>
                        <span class="block text-xs font-bold text-slate-800 dark:text-slate-100">{{ __('Notify me of new SK announcements') }}</span>
                        <span class="block text-[9px] text-slate-400 dark:text-slate-500">{{ __('Receive emails and dashboard notification alerts when new governance advisories and articles are posted.') }}</span>
                    </div>
                </label>
            </div>

            <div class="flex justify-end pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="submit" class="px-5 py-2.5 bg-[#1e40af] hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-blue-500/15">
                    {{ __('Save Preferences') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB 5: Privacy, Exports, and Danger Delete Account -->
<div x-show="activeTab === 'privacy'" class="space-y-6" x-cloak>
    <!-- Download Data Exporter -->
    <div class="card space-y-5">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 font-display uppercase tracking-tight">{{ __('Download My Data') }}</h2>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Export a complete JSON data archive of your personal account, profiling, and request history.') }}</p>
        </div>

        <div class="space-y-4">
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                {{ __('In compliance with the Data Privacy Act (DPA) and GDPR, you have the right to request a portable copy of all information stored on our servers under your name. Your export file is prepared immediately and will download as a secure JSON document.') }}
            </p>
            <div class="pt-2">
                <a href="{{ route('profile.download-data') }}" class="inline-flex items-center justify-center min-h-11 px-5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-800 font-bold text-xs uppercase tracking-wider rounded-xl active:scale-95 transition-all text-slate-700 dark:text-slate-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    {{ __('Download My Data') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Danger Zone Delete Account -->
    <div class="card border-rose-100 dark:border-rose-950 bg-rose-50/10 dark:bg-rose-955/5 space-y-5" x-data="{ confirming: false }">
        <div>
            <h2 class="text-sm font-bold text-rose-800 dark:text-rose-400 font-display uppercase tracking-tight">{{ __('Danger Zone') }}</h2>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Permanently delete your citizen account and purge all active records.') }}</p>
        </div>

        <div class="space-y-4">
            @if(Auth::user()->isSuperAdmin())
                <div class="bg-amber-50 dark:bg-amber-955/20 border border-amber-200 dark:border-amber-900 text-amber-800 dark:text-amber-400 p-4 rounded-xl text-xs flex items-center space-x-2">
                    <span>🛡️</span>
                    <div><strong>{{ __('Self-deletion is disabled') }}:</strong> {{ __('Superadmin accounts cannot delete their own profile to prevent locking out the system.') }}</div>
                </div>
            @else
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    {{ __('Once your account is deleted, all of its resources and request histories will be permanently deleted. Before proceeding, please download any data or information you wish to retain.') }}
                </p>

                <button type="button"
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-account-deletion')"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-rose-600/15">
                    {{ __('Delete Account') }}
                </button>

                <!-- Confirm account delete modal -->
                <x-modal name="confirm-account-deletion" :show="$errors->has('password')" focusable>
                    <form method="POST" action="{{ route('profile.destroy') }}" class="p-6 space-y-4 text-left">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="settings_tab" value="privacy">

                        <div>
                            <h2 class="text-sm font-bold text-rose-800 dark:text-rose-400 uppercase tracking-wider mb-1">
                                {{ __('Are you sure you want to delete your account?') }}
                            </h2>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 leading-relaxed font-sans">
                                {{ __('Once your account is deleted, all of its resources and request histories will be permanently deleted. Please enter your account password to confirm permanent deletion.') }}
                            </p>
                        </div>

                        <div class="mt-4">
                            <x-form-input label="Account Password" name="password" type="password" required="true" />
                            @if ($errors->has('password'))
                                <span class="text-xs text-rose-650 dark:text-rose-400 font-bold block mt-1">{{ $errors->first('password') }}</span>
                            @endif
                        </div>

                        <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button"
                                    x-on:click="$dispatch('close')"
                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-rose-600/15">
                                {{ __('Confirm Permanent Deletion') }}
                            </button>
                        </div>
                    </form>
                </x-modal>
            @endif
        </div>
    </div>
</div>

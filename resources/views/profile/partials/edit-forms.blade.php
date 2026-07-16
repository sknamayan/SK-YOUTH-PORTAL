<!-- CSS/JS for Cropper.js (Loaded only when needed) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" integrity="sha512-T59fCw+q/gwRBiCXFTyJXSAL1QC25LyJOIsh5A8IbX3y3IpCeeLq4A1zP/g0Y3+3Hy80g5T4M95d7wE50TC1yA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" integrity="sha512-6Qx4/6mKnWp0w09Hj3Hh9q8NfU59sK51T/K/1V81k9g0S9C5M0J+p5h94S/q9A3C2G7k2O2u675K90+qW50XqgA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- TAB 1: Account Settings / Personal Info -->
<div x-show="activeTab === 'account'" class="space-y-6" x-cloak>
    <!-- Avatar Edit Card -->
    <div class="card space-y-6" x-data="{
        selectedImage: null,
        cropper: null,
        initCropper() {
            const image = document.getElementById('cropper-target');
            if (this.cropper) {
                this.cropper.destroy();
            }
            this.cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: false,
                toggleDragModeOnDblclick: false
            });
        },
        handleFileSelect(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    this.selectedImage = event.target.result;
                    $nextTick(() => {
                        this.initCropper();
                    });
                };
                reader.readAsDataURL(files[0]);
                $dispatch('open-modal', 'cropper-modal');
            }
        },
        saveCrop() {
            if (this.cropper) {
                const canvas = this.cropper.getCroppedCanvas({
                    width: 250,
                    height: 250
                });
                document.getElementById('avatar_base64').value = canvas.toDataURL('image/png');
                document.getElementById('avatar-upload-form').submit();
            }
        }
    }">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 font-display uppercase tracking-tight">{{ __('Profile Picture') }}</h2>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Upload and crop a professional avatar for your user account.') }}</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-6">
            <!-- Current Avatar Render -->
            <div class="shrink-0">
                @if($user->avatar)
                    <img src="{{ asset('storage/avatars/' . $user->avatar) }}" class="w-20 h-20 rounded-full object-cover border-2 border-[#1e40af] dark:border-blue-500 shadow-sm" alt="Avatar">
                @else
                    <div class="w-20 h-20 rounded-full bg-[#1e40af] dark:bg-blue-900 text-white font-black text-2xl flex items-center justify-center font-display shadow-md select-none">
                        {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Upload Controls -->
            <div class="space-y-3 w-full sm:w-auto text-center sm:text-left">
                <form id="avatar-upload-form" method="POST" action="{{ route('profile.update-avatar') }}">
                    @csrf
                    <input type="hidden" name="avatar_base64" id="avatar_base64">
                    <input type="hidden" name="settings_tab" value="account">

                    <label class="inline-flex items-center justify-center min-h-11 px-5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-850 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 font-bold text-xs uppercase tracking-wider rounded-2xl cursor-pointer active:scale-95 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        {{ __('Choose New Photo') }}
                        <input type="file" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden" @change="handleFileSelect">
                    </label>
                </form>
                <p class="text-[9px] text-slate-400 dark:text-slate-500">{{ __('Accepted formats: PNG, JPG, JPEG or WEBP. Max file size: 5MB.') }}</p>
            </div>
        </div>

        <!-- Cropper Modal Workspace -->
        <x-modal name="cropper-modal" focusable>
            <div class="p-6 space-y-4 text-left">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">{{ __('Crop Profile Image') }}</h2>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('Adjust the handles to frame your profile picture correctly.') }}</p>
                </div>

                <!-- Crop Canvas Container -->
                <div class="w-full bg-slate-50 dark:bg-slate-950 rounded-2xl overflow-hidden border border-slate-150 dark:border-slate-800 flex items-center justify-center max-h-[350px]">
                    <template x-if="selectedImage">
                        <img id="cropper-target" :src="selectedImage" class="max-w-full max-h-[350px] object-contain">
                    </template>
                </div>

                <!-- Modal Actions -->
                <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="$dispatch('close')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" @click="saveCrop" class="px-4 py-2 bg-[#1e40af] hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-blue-500/10">
                        {{ __('Crop & Save') }}
                    </button>
                </div>
            </div>
        </x-modal>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="First Name" name="first_name" :value="$user->first_name" required="true" />
                <x-form-input label="Last Name" name="last_name" :value="$user->last_name" required="true" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Email Address" name="email" :value="$user->email" type="email" required="true" />
                <x-form-input label="Contact Number" name="contact_number" :value="$user->contact_number" type="tel" placeholder="09xxxxxxxxx" />
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

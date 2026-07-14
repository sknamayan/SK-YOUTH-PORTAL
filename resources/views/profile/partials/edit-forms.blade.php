    <!-- Section 1: Profile Information -->
    <div class="card space-y-5">
        <div>
            <h2 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">Profile Information</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Update your account's profile name and email address.</p>
        </div>

        <form method="POST" action="{{ route('profile.update-info') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="First Name" name="first_name" :value="$user->first_name" required="true" />
                <x-form-input label="Last Name" name="last_name" :value="$user->last_name" required="true" />
            </div>
            <x-form-input label="Email Address" name="email" :value="$user->email" type="email" required="true" />

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-primary text-xs px-5">Save Information</button>
            </div>
        </form>
    </div>

    <!-- Section 2: Change Password -->
    <div class="card space-y-5">
        <div>
            <h2 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">Update Password</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Ensure your account is using a long, random password to stay secure.</p>
        </div>

        <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <x-form-input label="Current Password" name="current_password" type="password" required="true" />
            <x-form-input label="New Password" name="password" type="password" required="true" />
            <x-form-input label="Confirm New Password" name="password_confirmation" type="password" required="true" />

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-primary text-xs px-5">Change Password</button>
            </div>
        </form>
    </div>

    <!-- Section 3: Danger Zone -->
    <div class="card border-rose-100 bg-rose-50/10 space-y-5" x-data="{ confirming: false }">
        <div>
            <h2 class="text-sm font-bold text-rose-800 font-display uppercase tracking-tight">Danger Zone</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Permanently delete your citizen account and purge all active records.</p>
        </div>

        <div class="space-y-4">
            @if(Auth::user()->isSuperAdmin())
                <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-xl text-xs flex items-center space-x-2">
                    <span>🛡️</span>
                    <div><strong>Self-deletion is disabled:</strong> Superadmin accounts cannot delete their own profile to prevent locking out the system.</div>
                </div>
            @else
                <p class="text-xs text-slate-500 leading-relaxed">
                    Once your account is deleted, all of its resources and request histories will be permanently deleted. Before proceeding, please download any data or information you wish to retain.
                </p>

                <button type="button" 
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-account-deletion')"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-rose-600/15">
                    Delete Account
                </button>

                <!-- Confirm account delete modal -->
                <x-modal name="confirm-account-deletion" :show="$errors->has('password')" focusable>
                    <form method="POST" action="{{ route('profile.destroy') }}" class="p-6 space-y-4 text-left">
                        @csrf
                        @method('DELETE')
                        
                        <div>
                            <h2 class="text-sm font-bold text-rose-800 dark:text-rose-400 uppercase tracking-wider mb-1">
                                Are you sure you want to delete your account?
                            </h2>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 leading-relaxed">
                                Once your account is deleted, all of its resources and request histories will be permanently deleted. Please enter your account password to confirm permanent deletion.
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
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-rose-600/15">
                                Confirm Permanent Deletion
                            </button>
                        </div>
                    </form>
                </x-modal>
            @endif
        </div>
    </div>

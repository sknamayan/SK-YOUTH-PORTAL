<div>
    @if ($isAlreadyProfiled)
        <!-- Profile Completed State -->
        <div class="p-8 md:p-12 text-center space-y-6 max-w-lg mx-auto">
            <!-- Icon/Indicator -->
            <div class="relative w-20 h-20 mx-auto">
                <div class="absolute inset-0 bg-emerald-100 dark:bg-emerald-950/40 rounded-full animate-ping opacity-75"></div>
                <div class="relative w-20 h-20 rounded-full bg-emerald-500 dark:bg-emerald-650 text-white flex items-center justify-center shadow-lg">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <!-- Title & Description -->
            <div class="space-y-2">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tight font-display">Profiling Completed</h2>
                <p class="text-slate-500 dark:text-slate-450 text-sm">Thank you! Your Katipunan ng Kabataan profile registration has been submitted successfully.</p>
            </div>

            <!-- 100% Completeness Progress Bar -->
            <div class="bg-emerald-50/50 dark:bg-emerald-955/10 border border-emerald-150/30 dark:border-emerald-900/20 p-5 rounded-2xl">
                <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-350 mb-2">
                    <span class="uppercase tracking-wider text-slate-500 dark:text-slate-450 font-display">Profile Completeness</span>
                    <span class="text-emerald-605 dark:text-emerald-400 text-sm font-black">100%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 h-3.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-full rounded-full w-full"></div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="bg-slate-50 dark:bg-slate-900 border border-slate-150 dark:border-slate-800 p-5 rounded-2xl text-left space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase text-slate-450 tracking-wider">Verification Status</span>
                    @if (strtolower($existingStatus) === 'approved')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">
                            Approved
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-955/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30">
                            Pending Review
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    @if (strtolower($existingStatus) === 'approved')
                        Your profile registry is fully approved. You now have unrestricted access to all service requests and sports tournament registrations.
                    @else
                        Your profile is currently queued for review by our secretariat. All features and request forms will automatically unlock once verified.
                    @endif
                </p>
            </div>

            <!-- CTA Actions -->
            <div class="pt-4">
                <a href="{{ route('profile.my-requests') }}" class="inline-flex items-center justify-center w-full min-h-12 px-6 bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-100 font-bold text-xs uppercase tracking-wider rounded-2xl transition shadow-md active:scale-95">
                    Go to Dashboard &rarr;
                </a>
            </div>
        </div>
    @else
        <!-- Original Step-by-Step Form Wizard -->
        <div class="space-y-6">
            <!-- Step Indicator / Progress Bar -->
            <div class="border-b border-slate-100 dark:border-slate-850 pb-5">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none max-w-xl mx-auto">
                    <!-- Step 1 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300 {{ $currentStep >= 1 ? 'text-[#1e40af]' : 'text-slate-400' }}">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300 {{ $currentStep >= 1 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-400' }}">1</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Consent</span>
                    </div>
                    <div class="flex-1 border-t-2 mx-4 transition duration-300 {{ $currentStep >= 2 ? 'border-[#1e40af]' : 'border-slate-200' }}"></div>

                    <!-- Step 2 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300 {{ $currentStep >= 2 ? 'text-[#1e40af]' : 'text-slate-400' }}">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300 {{ $currentStep >= 2 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450' }}">2</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Details</span>
                    </div>
                    <div class="flex-1 border-t-2 mx-4 transition duration-300 {{ $currentStep >= 3 ? 'border-[#1e40af]' : 'border-slate-200' }}"></div>

                    <!-- Step 3 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300 {{ $currentStep >= 3 ? 'text-[#1e40af]' : 'text-slate-400' }}">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300 {{ $currentStep >= 3 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450' }}">3</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Affiliations</span>
                    </div>
                    <div class="flex-1 border-t-2 mx-4 transition duration-300 {{ $currentStep >= 4 ? 'border-[#1e40af]' : 'border-slate-200' }}"></div>

                    <!-- Step 4 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300 {{ $currentStep >= 4 ? 'text-[#1e40af]' : 'text-slate-400' }}">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300 {{ $currentStep >= 4 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450' }}">4</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Inclusivity</span>
                    </div>
                </div>
            </div>

            <!-- Form Completeness Progress Card -->
            <div class="bg-blue-55/10 dark:bg-slate-900/60 border border-blue-150/30 dark:border-slate-850 p-5 rounded-2xl mb-6 shadow-sm">
                <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-205 mb-2">
                    <span class="uppercase tracking-wider text-slate-500 dark:text-slate-400 font-display text-[10px]">Profile Completeness</span>
                    <span class="text-blue-600 dark:text-blue-400 text-sm font-black">{{ $this->completeness }}%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 h-4.5 rounded-full overflow-hidden p-0.5">
                    <div class="bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600 h-full rounded-full transition-all duration-550 ease-out shadow-[0_0_8px_rgba(59,130,246,0.5)]" style="width: {{ $this->completeness }}%"></div>
                </div>
            </div>

            <!-- Validation Errors Notification -->
            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-semibold space-y-1 animate-fade-in">
                    <p class="font-bold text-rose-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Please resolve the following registration errors:
                    </p>
                    <ul class="list-disc pl-5 space-y-0.5 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="profiling-form py-4 space-y-6">
                <!-- STEP 1: Data Privacy Consent -->
                @if ($currentStep === 1)
                    <div class="space-y-4">
                        <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">1. Informed Data Privacy Consent</h3>
                        <div class="p-6 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-600 dark:text-slate-400 leading-relaxed space-y-4 font-medium shadow-inner">
                            <p class="font-black text-slate-800 dark:text-white text-[13px] tracking-tight">Sangguniang Kabataan of Barangay Namayan - Data Privacy Notice & Consent Agreement</p>
                            <p>In accordance with <strong>Republic Act No. 10173</strong> (the <strong>Data Privacy Act of 2012</strong>), the Sangguniang Kabataan Council of Barangay Namayan hereby informs you of the protocols regarding your personal data:</p>
                            
                            <div class="space-y-3 pl-3 border-l-2 border-[#1e40af]/30">
                                <p><strong>1. Collection and Usage:</strong> We collect personal, demographic, educational, voter, and inclusivity information. This data will be processed and used solely for the Katipunan ng Kabataan profiling registry, youth services programming, community assistance targeting, and official reports to the National Youth Commission (NYC) and the Department of the Interior and Local Government (DILG).</p>
                                <p><strong>2. Storage and Security:</strong> Your data is transmitted over secure channels (HTTPS) and encrypted at rest in our systems. Only authorized SK officials have access to review or process database records.</p>
                                <p><strong>3. Rights of the Data Subject:</strong> You have the right to access, update, correct, or request deletion of your information from our database at any time by contacting the SK Secretariat.</p>
                            </div>

                            <p class="text-slate-500 dark:text-slate-400 text-[11px] leading-tight">By checking the box below, you signify that you are at least 15 years of age and voluntarily give your consent to these terms.</p>
                        </div>
                        <div class="mt-4 flex items-start">
                            <div class="flex items-center h-5">
                                <input id="consent_checkbox" type="checkbox" wire:model="consent_given" class="focus:ring-[#1e40af] h-4 w-4 text-[#1e40af] border-slate-350 dark:border-slate-700 rounded cursor-pointer">
                            </div>
                            <div class="ml-3 text-xs">
                                <label for="consent_checkbox" class="font-bold text-slate-705 dark:text-slate-350 cursor-pointer select-none">I have read and understood the Data Privacy Consent Notice and hereby give my voluntary consent to the collection, processing, use, and storage of my personal data for SK profiling purposes.</label>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- STEP 2: Personal Details -->
                @if ($currentStep === 2)
                    <div class="space-y-4">
                        <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">2. Personal Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Surname <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="surname" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full" placeholder="e.g. Dela Cruz">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">First Name <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="first_name" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full" placeholder="e.g. Juan">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Middle Name <span class="text-rose-500">*</span> <span class="text-[9px] text-slate-400 font-medium lowercase">(type 'NONE' or 'N/A' if none)</span></label>
                                <input type="text" wire:model="middle_name" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full" placeholder="e.g. Santiago or NONE">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Suffix (Ext.)</label>
                                <select wire:model="ext" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full">
                                    <option value="">None</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Age <span class="text-rose-500">*</span> <span class="text-[9px] text-slate-400 font-normal lowercase">(auto-calculated)</span></label>
                                <input type="number" wire:model="age" min="6" max="39" readonly class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl cursor-not-allowed font-mono font-bold w-full" placeholder="Auto">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Sex <span class="text-rose-500">*</span></label>
                                <select wire:model="sex" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full">
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Gender Identity</label>
                                <input type="text" wire:model="gender" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full" placeholder="e.g. LGBTQIA+">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                                <input type="date" wire:model.live="dob" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Civil Status <span class="text-rose-500">*</span></label>
                                <select wire:model="civil_status" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full">
                                    <option value="">Select Civil Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Purok (Barangay Namayan) <span class="text-rose-500">*</span></label>
                                <select wire:model="purok_id" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full">
                                    <option value="">Select Purok</option>
                                    @foreach($puroks as $purok)
                                        <option value="{{ $purok->id }}">
                                            {{ $purok->purok_name }} {{ $purok->street_name ? '('.$purok->street_name.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Street Address</label>
                                <input type="text" wire:model="street_address" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full" placeholder="e.g. 594 J.P Rizal Street">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Contact Number <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="contact_number" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full" placeholder="e.g. 09171234567">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Email Address <span class="text-rose-500">*</span></label>
                                <input type="email" wire:model="email" readonly class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl cursor-not-allowed w-full" placeholder="e.g. citizen@namayan.local">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- STEP 3: Affiliations -->
                @if ($currentStep === 3)
                    <div class="space-y-6">
                        <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">3. Affiliations & Voter Info</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-slate-700">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Youth Classification <span class="text-rose-500">*</span></label>
                                <select wire:model="youth_classification" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full">
                                    <option value="">Select Classification</option>
                                    <option value="ISY">In-School Youth (ISY)</option>
                                    <option value="OSY">Out-of-School Youth (OSY)</option>
                                    <option value="WY">Working Youth (WY)</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <span class="block font-bold text-slate-500 dark:text-slate-400 uppercase text-[10px]">Registered SK Voter? <span class="text-rose-500">*</span></span>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="registered_sk_voter" value="1" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="registered_sk_voter" value="0" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <span class="block font-bold text-slate-500 dark:text-slate-400 uppercase text-[10px]">Registered National Voter? <span class="text-rose-500">*</span></span>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="registered_national_voter" value="1" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="registered_national_voter" value="0" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <span class="block font-bold text-slate-500 dark:text-slate-400 uppercase text-[10px]">Attended KK Assembly? <span class="text-rose-500">*</span></span>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="attended_kk_assembly" value="1" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="attended_kk_assembly" value="0" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <span class="block font-bold text-slate-500 dark:text-slate-400 uppercase text-[10px]">Part of Youth Organization? <span class="text-rose-500">*</span></span>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="part_of_youth_org" value="1" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="part_of_youth_org" value="0" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">No</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Youth Org Name (Conditional if Yes) -->
                        @if ($part_of_youth_org == 1)
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Name of Youth Organization <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="youth_org_name" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full" placeholder="e.g. Sangguniang Kabataan Movement">
                            </div>
                        @endif

                        <!-- Interested in joining (Conditional if No) -->
                        @if ($part_of_youth_org == 0 && $part_of_youth_org !== null && $part_of_youth_org !== '')
                            <div class="space-y-2 text-xs text-slate-700">
                                <span class="block font-bold text-slate-500 dark:text-slate-400 uppercase text-[10px]">Interested in joining a Youth Organization? <span class="text-rose-500">*</span></span>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="interested_in_joining" value="1" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="interested_in_joining" value="0" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">No</span>
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- STEP 4: Inclusivity & Education -->
                @if ($currentStep === 4)
                    <div class="space-y-6">
                        <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">4. Inclusivity & Education</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-slate-700">
                            <!-- Part of LGBTQIA -->
                            <div class="space-y-2">
                                <span class="block font-bold text-slate-500 dark:text-slate-400 uppercase text-[10px]">Part of the LGBTQIA+ Community? <span class="text-rose-500">*</span></span>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="part_of_lgbtqia" value="1" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="part_of_lgbtqia" value="0" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">No</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Person With Disability (PWD) -->
                            <div class="space-y-2">
                                <span class="block font-bold text-slate-500 dark:text-slate-400 uppercase text-[10px]">Person with Disability (PWD)? <span class="text-rose-500">*</span></span>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="pwd" value="1" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center text-slate-750 dark:text-slate-350">
                                        <input type="radio" wire:model="pwd" value="0" class="text-[#1e40af] focus:ring-[#1e40af]">
                                        <span class="ml-2">No</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Disability Name (Conditional if Yes) -->
                        @if ($pwd == 1)
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Registered Disability <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="registered_disability" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full" placeholder="e.g. Visual Impairment">
                            </div>
                        @endif

                        <!-- Highest Educational Attainment -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Highest Educational Attainment <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="highest_educational_attainment" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl w-full" placeholder="e.g. College Graduate, 2nd Year College">
                        </div>

                        <!-- Data Privacy Consent -->
                        <div class="mt-4 flex items-start">
                            <div class="flex items-center h-5">
                                <input id="final_consent" type="checkbox" wire:model="consent_given" class="focus:ring-[#1e40af] h-4 w-4 text-[#1e40af] border-slate-350 dark:border-slate-700 rounded cursor-pointer">
                            </div>
                            <div class="ml-3 text-xs">
                                <label for="final_consent" class="font-bold text-slate-705 dark:text-slate-350 cursor-pointer select-none">I agree to the Data Privacy Policy and terms of use.</label>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Navigation Footer -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                    @if ($currentStep > 1)
                        <button type="button" wire:click="prevStep" class="px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-605 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 font-bold rounded-xl transition text-xs uppercase tracking-wider select-none cursor-pointer">
                            &larr; Back
                        </button>
                    @else
                        <div class="w-10"></div>
                    @endif
                    
                    @if ($currentStep < $totalSteps)
                        <button type="button" wire:click="nextStep" class="btn-primary text-xs uppercase tracking-wider py-2 px-5 font-bold rounded-xl select-none cursor-pointer">
                            Next &rarr;
                        </button>
                    @else
                        <button type="button" wire:click="submit" class="btn-success text-xs uppercase tracking-wider py-2 px-5 font-bold rounded-xl select-none cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white border border-transparent transition active:scale-95 shadow-sm">
                            Submit Profile
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

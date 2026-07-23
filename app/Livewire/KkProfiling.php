<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KkProfile;
use App\Models\Purok;
use App\Models\User;
use App\Models\ActivityLog;

class KkProfiling extends Component
{
    public $isAlreadyProfiled = false;
    public $existingStatus;
    public $currentStep = 1;
    public $totalSteps = 4;

    // Step 1: Consent
    public $consent_given = false;

    // Step 2: Personal Details
    public $surname;
    public $first_name;
    public $middle_name;
    public $ext;
    public $age;
    public $sex;
    public $gender;
    public $dob;
    public $civil_status;
    public $purok_id;
    public $street_address;
    public $contact_number;
    public $email;

    // Step 3: Affiliations & Classifications
    public $youth_classification;
    public $registered_sk_voter;
    public $registered_national_voter;
    public $attended_kk_assembly;
    public $part_of_youth_org;
    public $youth_org_name;
    public $interested_in_joining;

    // Step 4: Inclusivity & Education
    public $part_of_lgbtqia;
    public $pwd;
    public $registered_disability;
    public $highest_educational_attainment;

    protected function getValidationRules()
    {
        if ($this->currentStep === 1) {
            return [
                'consent_given' => ['required', 'accepted'],
            ];
        }

        if ($this->currentStep === 2) {
            return [
                'surname' => ['required', 'string', 'max:255'],
                'first_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['required', 'string', 'max:50'],
                'ext' => ['nullable', 'string', 'max:10'],
                'age' => ['required', 'integer', 'min:6', 'max:39'],
                'sex' => ['required', 'in:Male,Female'],
                'gender' => ['nullable', 'string', 'max:255'],
                'dob' => ['required', 'date', 'before_or_equal:today'],
                'civil_status' => ['required', 'in:Single,Married,Widowed,Divorced,Separated'],
                'purok_id' => ['required', 'exists:puroks,id'],
                'street_address' => ['nullable', 'string', 'max:500'],
                'contact_number' => ['required', 'string', 'max:20'],
                'email' => ['required', 'email', 'max:255'],
            ];
        }

        if ($this->currentStep === 3) {
            return [
                'youth_classification' => ['required', 'in:ISY,OSY,WY'],
                'registered_sk_voter' => ['required', 'boolean'],
                'registered_national_voter' => ['required', 'boolean'],
                'attended_kk_assembly' => ['required', 'boolean'],
                'part_of_youth_org' => ['required', 'boolean'],
                'youth_org_name' => ['required_if:part_of_youth_org,1,true', 'nullable', 'string', 'max:255'],
                'interested_in_joining' => ['required_if:part_of_youth_org,0,false', 'nullable', 'boolean'],
            ];
        }

        return [
            'part_of_lgbtqia' => ['required', 'boolean'],
            'pwd' => ['required', 'boolean'],
            'registered_disability' => ['required_if:pwd,1,true', 'nullable', 'string', 'max:255'],
            'highest_educational_attainment' => ['required', 'string', 'max:255'],
        ];
    }

    protected $messages = [
        'consent_given.accepted' => 'You must accept the data privacy consent to proceed.',
        'youth_org_name.required_if' => 'Please specify the name of the youth organization.',
        'registered_disability.required_if' => 'Please specify your registered disability.',
    ];

    public function updatedDob($value)
    {
        if ($value) {
            try {
                $dobDate = new \DateTime($value);
                $today = new \DateTime();
                $diff = $today->diff($dobDate);
                $this->age = $diff->y;
            } catch (\Exception $e) {
                $this->age = null;
            }
        } else {
            $this->age = null;
        }
    }

    public function mount()
    {
        $user = auth()->user();
        $this->email = $user->email;

        $existingProfile = KkProfile::withoutGlobalScopes()
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('email', $user->email);
            })
            ->first();

        if ($existingProfile) {
            if ($existingProfile->status !== 'declined') {
                $this->isAlreadyProfiled = true;
                $this->existingStatus = $existingProfile->status;
            }
            $this->surname = $existingProfile->surname;
            $this->first_name = $existingProfile->first_name;
            $this->middle_name = $existingProfile->middle_name;
            $this->ext = $existingProfile->ext;
            $this->age = $existingProfile->age;
            $this->sex = $existingProfile->sex;
            $this->gender = $existingProfile->gender;
            $this->dob = $existingProfile->dob ? $existingProfile->dob->format('Y-m-d') : null;
            $this->civil_status = $existingProfile->civil_status;
            $this->purok_id = $existingProfile->purok_id;
            $this->street_address = $existingProfile->street_address;
            $this->youth_classification = $existingProfile->youth_classification;
            $this->contact_number = $existingProfile->contact_number;
            $this->registered_sk_voter = $existingProfile->registered_sk_voter;
            $this->registered_national_voter = $existingProfile->registered_national_voter;
            $this->attended_kk_assembly = $existingProfile->attended_kk_assembly;
            $this->part_of_youth_org = $existingProfile->part_of_youth_org;
            $this->youth_org_name = $existingProfile->youth_org_name;
            $this->interested_in_joining = $existingProfile->interested_in_joining;
            $this->part_of_lgbtqia = $existingProfile->part_of_lgbtqia;
            $this->pwd = $existingProfile->pwd;
            $this->registered_disability = $existingProfile->registered_disability;
            $this->highest_educational_attainment = $existingProfile->highest_educational_attainment;
            $this->consent_given = $existingProfile->consent_given;
        }
    }

    public function nextStep()
    {
        $this->validate($this->getValidationRules());
        $this->currentStep = min($this->currentStep + 1, $this->totalSteps);
    }

    public function prevStep()
    {
        $this->currentStep = max($this->currentStep - 1, 1);
    }

    public function submit()
    {
        $this->validate($this->getValidationRules());

        $existingProfile = KkProfile::where('user_id', auth()->id())
            ->orWhere('email', auth()->user()->email)
            ->first();

        if ($existingProfile) {
            if ($existingProfile->status === 'declined') {
                $existingProfile->delete();
            } else {
                return redirect()->route('profile.my-requests')
                    ->with('info', 'Your Katipunan ng Kabataan profile is already registered.');
            }
        }

        // Force user's own email to prevent spoofing
        $email = auth()->user()->email;

        $age = (int) $this->age;
        if ($age >= 6 && $age <= 14) {
            $category = 'child';
        } elseif ($age >= 31 && $age <= 39) {
            $category = 'adult';
        } else {
            $category = 'sk_youth';
        }

        $interested = $this->interested_in_joining;
        if ($this->part_of_youth_org) {
            $interested = false;
        }

        $profile = KkProfile::create([
            'surname' => strtoupper($this->surname),
            'first_name' => strtoupper($this->first_name),
            'middle_name' => strtoupper($this->middle_name),
            'ext' => strtoupper($this->ext),
            'age' => $this->age,
            'sex' => $this->sex,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'civil_status' => $this->civil_status,
            'purok_id' => $this->purok_id,
            'street_address' => $this->street_address,
            'youth_classification' => $this->youth_classification,
            'contact_number' => $this->contact_number,
            'email' => $email,
            'registered_sk_voter' => $this->registered_sk_voter,
            'registered_national_voter' => $this->registered_national_voter,
            'attended_kk_assembly' => $this->attended_kk_assembly,
            'part_of_youth_org' => $this->part_of_youth_org,
            'youth_org_name' => $this->part_of_youth_org ? $this->youth_org_name : null,
            'interested_in_joining' => !$this->part_of_youth_org ? $interested : false,
            'part_of_lgbtqia' => $this->part_of_lgbtqia,
            'pwd' => $this->pwd,
            'registered_disability' => $this->pwd ? $this->registered_disability : null,
            'highest_educational_attainment' => $this->highest_educational_attainment,
            'consent_given' => $this->consent_given,
            'processed_by' => auth()->id(),
            'user_id' => auth()->id(),
            'status' => 'pending',
            'category' => $category,
        ]);

        ActivityLog::record('kk_profile_created', $profile, [
            'name' => $profile->full_name,
            'email' => $profile->email,
            'self_profiled' => true
        ], auth()->id());

        session()->flash('success', 'Your Katipunan ng Kabataan profile has been successfully registered!');
        return redirect()->route('profile.my-requests');
    }

    public function render()
    {
        return view('livewire.kk-profiling', [
            'puroks' => Purok::orderBy('purok_name')->get()
        ]);
    }
}

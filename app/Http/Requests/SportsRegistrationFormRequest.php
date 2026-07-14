<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SportsRegistrationFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $age = $this->input('age');
        
        $rules = [
            'sport' => ['required', 'string', 'max:100'],
            'division' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'gender' => ['required', 'in:Male,Female,Prefer not to say'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'kk_profiling_status' => ['required', 'in:Yes,No'],
            'profile_picture' => ['required', 'file', 'image', 'max:5120'], // 5MB max
            'team_name' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            
            // Agreements
            'health_declaration' => ['required', 'string'],
            'consent_waiver' => ['required', 'accepted'],
        ];

        // Conditional branching rules
        if ($age !== null && intval($age) < 18) {
            $rules['guardian_first_name'] = ['required', 'string', 'max:255'];
            $rules['guardian_last_name'] = ['required', 'string', 'max:255'];
            $rules['guardian_middle_name'] = ['nullable', 'string', 'max:255'];
            $rules['guardian_age'] = ['required', 'integer', 'min:18', 'max:120'];
            $rules['guardian_relation'] = ['required', 'string', 'max:100'];
            $rules['guardian_contact_number'] = ['required', 'string', 'max:20'];
            $rules['guardian_address'] = ['required', 'string', 'max:500'];
            $rules['guardian_gov_id'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240']; // 10MB max
            
            // adult verification is not required for minors
            $rules['voter_cert'] = ['nullable'];
        } else {
            $rules['voter_cert'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240']; // 10MB max
            
            // guardian fields are not required for adults
            $rules['guardian_first_name'] = ['nullable'];
            $rules['guardian_last_name'] = ['nullable'];
            $rules['guardian_middle_name'] = ['nullable'];
            $rules['guardian_age'] = ['nullable'];
            $rules['guardian_relation'] = ['nullable'];
            $rules['guardian_contact_number'] = ['nullable'];
            $rules['guardian_address'] = ['nullable'];
            $rules['guardian_gov_id'] = ['nullable'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'sport' => 'Sport',
            'division' => 'Division',
            'position' => 'Position',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'middle_name' => 'Middle Name',
            'birthdate' => 'Birthdate',
            'age' => 'Age',
            'gender' => 'Gender',
            'email' => 'Email Address',
            'contact_number' => 'Contact Number',
            'address' => 'Complete Address',
            'kk_profiling_status' => 'KK Profiling Status',
            'profile_picture' => 'Profile Picture',
            'team_name' => 'Team Name',
            'guardian_first_name' => 'Guardian First Name',
            'guardian_last_name' => 'Guardian Last Name',
            'guardian_middle_name' => 'Guardian Middle Name',
            'guardian_age' => 'Guardian Age',
            'guardian_relation' => 'Guardian Relation',
            'guardian_contact_number' => 'Guardian Contact Number',
            'guardian_address' => 'Guardian Address',
            'guardian_gov_id' => 'Guardian Government ID',
            'voter_cert' => "Voter's ID/Comelec Certificate",
            'health_declaration' => 'Health Declaration',
            'consent_waiver' => 'Consent Waiver',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        session()->flash('failed_form', 'sports');
        parent::failedValidation($validator);
    }
}

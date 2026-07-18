<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\EnforcesUppercaseInputs;

class HealthRequestFormRequest extends FormRequest
{
    use EnforcesUppercaseInputs;

    /**
     * Fields to automatically convert to uppercase.
     */
    protected array $uppercaseFields = [
        'first_name',
        'last_name',
        'middle_name',
        'concerns',
        'preferred_time',
    ];

    /**
      * Determine if the user is authorized to make this request.
      */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $formRoute = $this->is('*mental-health*') ? 'forms.mental-health.create' : 'forms.health.create';
        $initiative = \App\Models\Initiative::where('form_route', $formRoute)->first();

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:0', 'max:120'],
            'gender' => ['required', 'in:Male,Female,Prefer not to say'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
            'concerns' => ['required', 'string'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required', 'string'],
        ];

        if ($initiative && is_array($initiative->custom_fields)) {
            $rules['custom_fields'] = ['nullable', 'array'];
            foreach ($initiative->custom_fields as $field) {
                $fieldName = $field['name'] ?? null;
                if (!$fieldName) continue;

                $fieldRules = [];
                if ($field['required'] ?? false) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                $fieldRules[] = match ($field['type'] ?? 'text') {
                    'number' => 'numeric',
                    'date' => 'date',
                    default => 'string',
                };

                $rules["custom_fields.{$fieldName}"] = $fieldRules;
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];
        $formRoute = $this->is('*mental-health*') ? 'forms.mental-health.create' : 'forms.health.create';
        $initiative = \App\Models\Initiative::where('form_route', $formRoute)->first();
        if ($initiative && is_array($initiative->custom_fields)) {
            foreach ($initiative->custom_fields as $field) {
                $fieldName = $field['name'] ?? null;
                if ($fieldName) {
                    $attributes["custom_fields.{$fieldName}"] = $field['label'] ?? $fieldName;
                }
            }
        }
        return $attributes;
    }

    /**
     * Configure the validator instance with custom validation hooks to avoid duplicate submissions.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $email = $this->input('email');
            if ($email) {
                $recentPendingExists = \App\Models\HealthRequest::where('email', $email)
                    ->where('status', 'pending')
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->exists();

                if ($recentPendingExists) {
                    $validator->errors()->add(
                        'email',
                        'You have already submitted a pending health consultation request recently. Please wait a few minutes before submitting another one.'
                    );
                }
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $formKey = $this->is('*mental-health*') ? 'mental-health' : 'health';
        session()->flash('failed_form', $formKey);
        parent::failedValidation($validator);
    }
}

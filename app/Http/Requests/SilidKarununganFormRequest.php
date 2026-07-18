<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\EnforcesUppercaseInputs;

class SilidKarununganFormRequest extends FormRequest
{
    use EnforcesUppercaseInputs;

    /**
     * Fields to automatically convert to uppercase.
     */
    protected array $uppercaseFields = [
        'requestor_first_name',
        'requestor_last_name',
        'requestor_middle_name',
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
     */
    public function rules(): array
    {
        $formRoute = 'forms.silid.create';
        $initiative = \App\Models\Initiative::where('form_route', $formRoute)->first();

        $rules = [
            'requestor_first_name' => ['required', 'string', 'max:255'],
            'requestor_last_name' => ['required', 'string', 'max:255'],
            'requestor_middle_name' => ['nullable', 'string', 'max:255'],
            'requestor_age' => ['required', 'integer', 'min:0', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
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
        $formRoute = 'forms.silid.create';
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

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        session()->flash('failed_form', 'silid');
        parent::failedValidation($validator);
    }
}

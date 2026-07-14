<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicineRequestFormRequest extends FormRequest
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
        $formRoute = 'forms.medicine.create';
        $initiative = \App\Models\Initiative::where('form_route', $formRoute)->first();

        $rules = [
            'requestor_first_name' => ['required', 'string', 'max:255'],
            'requestor_last_name' => ['required', 'string', 'max:255'],
            'requestor_age' => ['required', 'integer', 'min:0', 'max:120'],
            'requestor_gender' => ['required', 'in:Male,Female,Prefer not to say'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
            'complete_address' => ['required', 'string'],
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
        $formRoute = 'forms.medicine.create';
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
        session()->flash('failed_form', 'medicine');
        parent::failedValidation($validator);
    }
}

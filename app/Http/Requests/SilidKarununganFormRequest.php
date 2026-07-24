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
        'first_name',
        'last_name',
        'middle_name',
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
        $initiativeId = $this->input('initiative_id');
        $initiative = $initiativeId 
            ? \App\Models\Initiative::find($initiativeId)
            : \App\Models\Initiative::where('form_route', 'forms.silid.create')->first();

        $rules = [
            'initiative_id' => ['nullable', 'exists:initiatives,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:0', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $time = strtotime($value);
                    $startTime = strtotime('10:00:00');
                    $endTime = strtotime('20:00:00');

                    if ($time < $startTime || $time > $endTime) {
                        $fail("Booking time must be strictly between 10:00 AM and 8:00 PM.");
                        return;
                    }

                    $date = $this->input('preferred_date');
                    if ($date) {
                        $bookingService = app(\App\Services\BookingService::class);
                        if (!$bookingService->checkAvailability($date, $value)) {
                            $fail("The selected time slot ({$value}) on {$date} is already booked. Please choose an available time slot.");
                        }
                    }
                },
            ],
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
        $initiativeId = $this->input('initiative_id');
        $initiative = $initiativeId 
            ? \App\Models\Initiative::find($initiativeId)
            : \App\Models\Initiative::where('form_route', 'forms.silid.create')->first();

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
        $initiativeId = $this->input('initiative_id');
        $initiative = $initiativeId ? \App\Models\Initiative::find($initiativeId) : null;
        $formName = ($initiative && $initiative->title === 'TTPD Printing Service') ? 'ttpd' : 'silid';
        session()->flash('failed_form', $formName);
        parent::failedValidation($validator);
    }
}

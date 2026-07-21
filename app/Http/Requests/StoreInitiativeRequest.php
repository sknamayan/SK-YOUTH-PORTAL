<?php

namespace App\Http\Requests;

use App\Models\Initiative;
use Illuminate\Foundation\Http\FormRequest;

class StoreInitiativeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canAccessDashboard();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $initiativeId = $this->route('initiative')?->id ?? $this->input('id');

        return [
            'committee_id' => ['required', 'exists:committees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'scheduled_at' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) use ($initiativeId) {
                    $exists = Initiative::where('committee_id', $this->input('committee_id'))
                        ->where('scheduled_at', $value)
                        ->when($initiativeId, fn ($q) => $q->where('id', '!=', $initiativeId))
                        ->exists();

                    if ($exists) {
                        $fail('This time slot is already taken by another initiative.');
                    }
                },
            ],
            'picture' => ['nullable', 'image', 'max:5120'], // 5MB Max
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'scheduled_at.after_or_equal' => 'The scheduled date and time must be today or in the future.',
        ];
    }
}

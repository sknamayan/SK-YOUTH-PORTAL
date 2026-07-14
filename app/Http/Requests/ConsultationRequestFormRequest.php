<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ConsultationRequest;
use Illuminate\Support\Facades\Auth;

class ConsultationRequestFormRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'in:General Concern,Suggestion,Report'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ];
    }

    /**
     * Configure the validator instance with custom validation hooks to avoid duplicate submissions.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $category = $this->input('category');
            $subject = $this->input('subject');
            $message = $this->input('message');

            // 1. Idempotency Check: Prevent duplicate submissions of identical request content
            // within a 5-minute cooldown period.
            $duplicatePendingExists = ConsultationRequest::where('category', $category)
                ->where('subject', $subject)
                ->where('message', $message)
                ->where('status', 'Pending')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->exists();

            if ($duplicatePendingExists) {
                $validator->errors()->add(
                    'message',
                    'A duplicate pending consultation request with the same content has already been submitted in the last 5 minutes. Please track your existing request or wait a moment.'
                );
                return;
            }

            // 2. Authenticated User Scope Check (Demonstrative):
            // If the model tracks users via relation (e.g. HealthRequest checks for same user email or ID),
            // we can prevent a user from filing multiple pending requests of the same type/category.
            $user = Auth::user();
            if ($user) {
                // Example check for another model like HealthRequest:
                // $userPendingHealthRequest = \App\Models\HealthRequest::where('email', $user->email)
                //     ->where('status', 'pending')
                //     ->where('created_at', '>=', now()->subMinutes(5))
                //     ->exists();
                // if ($userPendingHealthRequest) { ... }
            }
        });
    }

    /**
     * Handle failed validation state if needed.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        session()->flash('failed_form', 'consultation');
        parent::failedValidation($validator);
    }
}

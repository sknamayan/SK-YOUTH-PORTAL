<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Initiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormBuilderController extends Controller
{
    /**
     * Show the drag-and-drop form builder for a custom initiative.
     */
    public function edit(Initiative $initiative): View
    {
        $this->ensureCustomInitiative($initiative);

        return view('admin.structure.form-builder', [
            'initiative' => $initiative->load('committee'),
            'isSportsBuilder' => false
        ]);
    }

    /**
     * Persist the form schema JSON to the initiative.
     */
    public function update(Request $request, Initiative $initiative): RedirectResponse
    {
        $this->ensureCustomInitiative($initiative);

        $validated = $request->validate([
            'custom_fields' => ['required', 'array'],
            'custom_fields.*.label' => ['required', 'string', 'max:255'],
            'custom_fields.*.name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/'],
            'custom_fields.*.type' => ['required', 'string', 'in:text,textarea,number,date,select,file'],
            'custom_fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'custom_fields.*.required' => ['nullable', 'boolean'],
            'custom_fields.*.options' => ['nullable', 'array'],
            'custom_fields.*.options.*' => ['string', 'max:255'],
        ]);

        $fields = collect($validated['custom_fields'])->map(function (array $field) {
            return [
                'label' => $field['label'],
                'name' => $field['name'],
                'type' => $field['type'],
                'placeholder' => $field['placeholder'] ?? '',
                'required' => (bool) ($field['required'] ?? false),
                'options' => $field['type'] === 'select'
                    ? array_values(array_filter($field['options'] ?? []))
                    : [],
            ];
        })->values()->all();

        $initiative->update(['custom_fields' => $fields]);

        return redirect()
            ->route('admin.structure.form-builder.edit', $initiative)
            ->with('success', 'Form schema saved successfully.');
    }

    private function ensureCustomInitiative(Initiative $initiative): void
    {
        $predefined = [
            'forms.health.create',
            'forms.mental-health.create',
            'forms.medicine.create',
            'forms.sports.create',
        ];

        if ($initiative->form_route && in_array($initiative->form_route, $predefined, true)) {
            abort(404, 'Form builder is only available for custom initiatives.');
        }
    }
}

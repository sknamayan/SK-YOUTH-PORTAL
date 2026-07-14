<?php

namespace App\Http\Controllers\Admin\SportsLeague;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\RegistrationForm;
use App\Models\FormField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SportsFormBuilderController extends Controller
{
    /**
     * Show the dedicated dynamic form builder for a sports division.
     */
    public function create(Request $request): View
    {
        $leagues = League::all();
        return view('admin.sports-league.form-builder', [
            'leagues' => $leagues,
        ]);
    }

    /**
     * Store the dynamic sports registration form schema.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'league_name' => ['required', 'string', 'max:255'],
            'sport' => ['required', 'string', 'max:255'],
            'division_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'custom_fields' => ['required', 'array', 'min:1'],
            'custom_fields.*.label' => ['required', 'string', 'max:255'],
            'custom_fields.*.name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/'],
            'custom_fields.*.type' => ['required', 'string', 'in:text,textarea,number,date,select,radio,checkbox,file'],
            'custom_fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'custom_fields.*.required' => ['nullable', 'boolean'],
            'custom_fields.*.options' => ['nullable', 'array'],
            'custom_fields.*.options.*' => ['string', 'max:255'],
        ]);

        // Find or create the league
        $league = League::firstOrCreate([
            'name' => $validated['league_name'],
            'sport' => $validated['sport'],
        ]);

        // Create or update the registration form
        $registrationForm = RegistrationForm::updateOrCreate([
            'type' => 'sports',
            'league_id' => $league->id,
            'division_name' => $validated['division_name'],
        ], [
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        // Recreate form fields
        $registrationForm->formFields()->delete();

        foreach ($validated['custom_fields'] as $index => $fieldData) {
            FormField::create([
                'registration_form_id' => $registrationForm->id,
                'field_label' => $fieldData['label'],
                'field_name' => $fieldData['name'],
                'field_type' => $fieldData['type'],
                'is_required' => (bool) ($fieldData['required'] ?? false),
                'options' => in_array($fieldData['type'], ['select', 'radio', 'checkbox'], true)
                    ? array_values(array_filter($fieldData['options'] ?? []))
                    : null,
                'sort_order' => $index,
            ]);
        }

        return redirect()
            ->route('admin.sports-league.index')
            ->with('success', "Dynamic registration form for {$validated['division_name']} saved successfully.");
    }
}

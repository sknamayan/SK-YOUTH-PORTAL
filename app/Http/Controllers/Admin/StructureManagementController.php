<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Committee;
use App\Models\Initiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class StructureManagementController extends Controller
{
    /**
     * Display the structure management dashboard page.
     */
    public function index(): View
    {
        $committees = Committee::with(['initiatives' => function ($query) {
            $query->withCount('accomplishmentReports');
        }])->get();

        $archivedCommittees = Committee::onlyTrashed()->get();
        $archivedInitiatives = Initiative::onlyTrashed()->with('committee')->get();

        return view('admin.structure.index', compact('committees', 'archivedCommittees', 'archivedInitiatives'));
    }

    /**
     * Store a new Committee (Subtopic).
     */
    public function storeCommittee(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:committees,name'],
        ]);

        $slug = Str::slug($request->input('name'));

        if (Committee::where('slug', $slug)->exists()) {
            return back()->withErrors(['name' => 'A committee with a similar name or slug already exists.'])->withInput();
        }

        $project = Project::first() ?? Project::create([
            'title' => 'SK Namayan Youth Services',
            'slug' => 'sk-namayan-youth-services',
            'description' => 'Comprehensive youth empowerment initiatives, community health programs, student research resources, and athletic leagues organized by the Sangguniang Kabataan of Barangay Namayan.'
        ]);

        Committee::create([
            'project_id' => $project->id,
            'name' => $request->input('name'),
            'slug' => $slug,
        ]);

        return back()->with('success', 'Committee (Subtopic) added successfully.');
    }

    /**
     * Delete a Committee (Subtopic).
     */
    public function destroyCommittee(Request $request, Committee $committee): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $committee->delete();

        return back()->with('success', 'Committee (Subtopic) and all its child initiatives deleted successfully.');
    }

    /**
     * Store a new Initiative (Project).
     */
    public function storeInitiative(Request $request): RedirectResponse
    {
        $request->validate([
            'committee_id' => ['required', 'exists:committees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'form_route' => ['nullable', 'string', 'max:255'],
            'is_coming_soon' => ['nullable', 'boolean'],
            'show_in_quick_forms' => ['nullable', 'boolean'],
            'is_highlighted' => ['nullable', 'boolean'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.label' => ['required_with:custom_fields', 'string', 'max:255'],
            'custom_fields.*.name' => ['required_with:custom_fields', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/'],
            'custom_fields.*.type' => ['required_with:custom_fields', 'string', 'in:text,textarea,number,date,select,file'],
            'custom_fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'custom_fields.*.options' => ['nullable', 'array'],
            'custom_fields.*.options.*' => ['string', 'max:255'],
        ]);

        if ($request->boolean('is_highlighted') && Initiative::where('is_highlighted', true)->count() >= 3) {
            return back()->withErrors(['is_highlighted' => 'You can only have a maximum of 3 highlighted programs. Please unhighlight another program first.'])->withInput();
        }

        $customFields = [];
        if ($request->has('custom_fields')) {
            foreach ($request->input('custom_fields') as $field) {
                $isRequired = isset($field['required']) && ($field['required'] == '1' || $field['required'] == 'true' || $field['required'] == 'on' || $field['required'] === true);
                $customFields[] = [
                    'label' => $field['label'],
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'placeholder' => $field['placeholder'] ?? '',
                    'required' => $isRequired,
                    'options' => ($field['type'] ?? '') === 'select'
                        ? array_values(array_filter($field['options'] ?? []))
                        : [],
                ];
            }
        }

        Initiative::create([
            'committee_id' => $request->input('committee_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'form_route' => $request->input('form_route'),
            'is_coming_soon' => $request->boolean('is_coming_soon'),
            'show_in_quick_forms' => $request->boolean('show_in_quick_forms'),
            'is_highlighted' => $request->boolean('is_highlighted'),
            'custom_fields' => $customFields,
        ]);

        return back()->with('success', 'Initiative (Project) added successfully.');
    }

    /**
     * Update an Initiative (Project).
     */
    public function updateInitiative(Request $request, Initiative $initiative): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'form_route' => ['nullable', 'string', 'max:255'],
            'is_coming_soon' => ['nullable', 'boolean'],
            'show_in_quick_forms' => ['nullable', 'boolean'],
            'is_highlighted' => ['nullable', 'boolean'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.label' => ['required_with:custom_fields', 'string', 'max:255'],
            'custom_fields.*.name' => ['required_with:custom_fields', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/'],
            'custom_fields.*.type' => ['required_with:custom_fields', 'string', 'in:text,textarea,number,date,select,file'],
            'custom_fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'custom_fields.*.options' => ['nullable', 'array'],
            'custom_fields.*.options.*' => ['string', 'max:255'],
        ]);

        if ($request->boolean('is_highlighted') && Initiative::where('is_highlighted', true)->where('id', '!=', $initiative->id)->count() >= 3) {
            return back()->withErrors(['is_highlighted' => 'You can only have a maximum of 3 highlighted programs. Please unhighlight another program first.'])->withInput();
        }

        $customFields = [];
        if ($request->has('custom_fields')) {
            foreach ($request->input('custom_fields') as $field) {
                $isRequired = isset($field['required']) && ($field['required'] == '1' || $field['required'] == 'true' || $field['required'] == 'on' || $field['required'] === true);
                $customFields[] = [
                    'label' => $field['label'],
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'placeholder' => $field['placeholder'] ?? '',
                    'required' => $isRequired,
                    'options' => ($field['type'] ?? '') === 'select'
                        ? array_values(array_filter($field['options'] ?? []))
                        : [],
                ];
            }
        }

        $initiative->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'form_route' => $request->input('form_route'),
            'is_coming_soon' => $request->boolean('is_coming_soon'),
            'show_in_quick_forms' => $request->boolean('show_in_quick_forms'),
            'is_highlighted' => $request->boolean('is_highlighted'),
            'custom_fields' => $customFields,
        ]);

        return back()->with('success', 'Initiative (Project) updated successfully.');
    }

    /**
     * Delete (soft-delete / archive) an Initiative (Project).
     */
    public function destroyInitiative(Request $request, Initiative $initiative): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $initiative->delete();

        return back()->with('success', 'Initiative (Project) archived successfully.');
    }

    public function restoreInitiative($id): RedirectResponse
    {
        $initiative = Initiative::onlyTrashed()->findOrFail($id);
        
        // Turn off is_highlighted if the active highlighted limit (3) is already met
        if ($initiative->is_highlighted && Initiative::where('is_highlighted', true)->count() >= 3) {
            $initiative->is_highlighted = false;
            $initiative->save();
        }

        $initiative->restore();

        return back()->with('success', 'Initiative (Project) restored successfully.');
    }

    /**
     * Permanently delete an Initiative (Project) from archive.
     */
    public function forceDeleteInitiative(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $initiative = Initiative::onlyTrashed()->findOrFail($id);

        // Delete custom requests
        \App\Models\CustomRequest::where('initiative_id', $initiative->id)->delete();

        // Delete associated predefined requests if a standard form route is matched
        if ($initiative->form_route) {
            switch ($initiative->form_route) {
                case 'forms.health.create':
                case 'forms.mental-health.create':
                    \App\Models\HealthRequest::query()->delete();
                    break;
                case 'forms.medicine.create':
                    \App\Models\MedicineRequest::query()->delete();
                    break;
                case 'forms.silid.create':
                    \App\Models\SilidKarununganRequest::query()->delete();
                    break;
                case 'forms.sports.create':
                    \App\Models\SportsRegistration::query()->delete();
                    break;
            }
        }

        $initiative->forceDelete();

        return back()->with('success', 'Initiative (Project) permanently deleted successfully.');
    }

    /**
     * Restore a soft-deleted Committee (Subtopic) from archive.
     */
    public function restoreCommittee($id): RedirectResponse
    {
        $committee = Committee::onlyTrashed()->findOrFail($id);
        $committee->restore();

        return back()->with('success', 'Committee (Subtopic) restored successfully.');
    }

    /**
     * Permanently delete a Committee (Subtopic) from archive.
     */
    public function forceDeleteCommittee(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $committee = Committee::onlyTrashed()->findOrFail($id);
        $committee->forceDelete();

        return back()->with('success', 'Committee (Subtopic) permanently deleted successfully.');
    }
}

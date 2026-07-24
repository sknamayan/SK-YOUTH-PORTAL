<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Committee;
use App\Models\Initiative;
use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StructureManager extends Component
{
    use WithFileUploads;

    // Collections
    public $committees = [];
    public $archivedCommittees = [];
    public $archivedInitiatives = [];

    // Form states
    public $activeCommitteeId = 'all';
    public $activeAdminTab = 'structure';

    // Add Committee Form
    public $newCommitteeName = '';

    // Initiative Info Modal properties
    public $showEditInfoModal = false;
    public $showAddInitiativeModal = false;
    public $initiativeId;
    public $title = '';
    public $description = '';
    public $committee_id;
    public $form_route = '';
    public $is_coming_soon = false;
    public $show_in_quick_forms = false;
    public $is_highlighted = false;
    public $thumbnail; // file upload
    public $existingThumbnailUrl = null;

    // Form Builder Modal properties
    public $showEditBuilderModal = false;
    public $builderFields = [];

    // Delete confirmation properties
    public $showDeleteConfirmModal = false;
    public $deleteConfirmType = ''; // 'committee' or 'initiative'
    public $deleteTargetId;
    public $confirmPassword = '';

    protected $listeners = ['refreshData' => 'loadData'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->committees = Committee::with(['initiatives' => function ($query) {
            $query->withCount('accomplishmentReports');
        }])->get();

        $this->archivedCommittees = Committee::onlyTrashed()->get();
        $this->archivedInitiatives = Initiative::onlyTrashed()->with('committee')->get();
    }

    public function selectCommittee($id)
    {
        $this->activeCommitteeId = $id;
    }

    public function selectTab($tab)
    {
        $this->activeAdminTab = $tab;
    }

    // Committee Actions
    public function storeCommittee()
    {
        $this->validate([
            'newCommitteeName' => ['required', 'string', 'max:255', 'unique:committees,name'],
        ]);

        $slug = Str::slug($this->newCommitteeName);

        if (Committee::where('slug', $slug)->exists()) {
            $this->addError('newCommitteeName', 'A committee with a similar name or slug already exists.');
            return;
        }

        $project = Project::first() ?? Project::create([
            'title' => 'SK Namayan Youth Services',
            'slug' => 'sk-namayan-youth-services',
            'description' => 'Comprehensive youth empowerment initiatives, etc.'
        ]);

        Committee::create([
            'project_id' => $project->id,
            'name' => $this->newCommitteeName,
            'slug' => $slug,
        ]);

        $this->newCommitteeName = '';
        $this->loadData();
        session()->flash('success', 'Committee (Subtopic) added successfully.');
    }

    public function confirmDeleteCommittee($id)
    {
        $this->deleteConfirmType = 'committee';
        $this->deleteTargetId = $id;
        $this->confirmPassword = '';
        $this->showDeleteConfirmModal = true;
    }

    public function deleteCommittee()
    {
        // Password validation logic
        $user = auth()->user();
        if (!\Illuminate\Support\Facades\Hash::check($this->confirmPassword, $user->password)) {
            $this->addError('confirmPassword', 'Incorrect password.');
            return;
        }

        $committee = Committee::findOrFail($this->deleteTargetId);
        $committee->delete();

        $this->showDeleteConfirmModal = false;
        $this->loadData();
        session()->flash('success', 'Committee deleted successfully.');
    }

    public function restoreCommittee($id)
    {
        $committee = Committee::onlyTrashed()->findOrFail($id);
        $committee->restore();
        $this->loadData();
        session()->flash('success', 'Committee restored successfully.');
    }

    public function forceDeleteCommittee($id)
    {
        $committee = Committee::onlyTrashed()->findOrFail($id);
        $committee->forceDelete();
        $this->loadData();
        session()->flash('success', 'Committee permanently deleted.');
    }

    // Initiative Add Actions
    public function openAddInitiative($committeeId)
    {
        $this->resetInitiativeForm();
        $this->committee_id = $committeeId;
        $this->showAddInitiativeModal = true;
    }

    public function storeInitiative()
    {
        $this->validate([
            'committee_id' => ['required', 'exists:committees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'form_route' => ['nullable', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->is_highlighted && Initiative::where('is_highlighted', true)->count() >= 3) {
            $this->addError('is_highlighted', 'You can only have a maximum of 3 highlighted programs.');
            return;
        }

        $picturePath = null;
        if ($this->thumbnail) {
            $picturePath = $this->thumbnail->store('thumbnails', 'public');
        }

        Initiative::create([
            'committee_id' => $this->committee_id,
            'title' => $this->title,
            'description' => $this->description,
            'form_route' => $this->form_route,
            'picture_path' => $picturePath,
            'is_coming_soon' => (bool)$this->is_coming_soon,
            'show_in_quick_forms' => (bool)$this->show_in_quick_forms,
            'is_highlighted' => (bool)$this->is_highlighted,
            'custom_fields' => [],
            'form_structure' => [],
        ]);

        $this->showAddInitiativeModal = false;
        $this->loadData();
        session()->flash('success', 'Initiative added successfully.');
    }

    // Initiative Edit Info Action
    public function editInfo($id)
    {
        $this->resetInitiativeForm();
        $initiative = Initiative::findOrFail($id);
        $this->initiativeId = $initiative->id;
        $this->committee_id = $initiative->committee_id;
        $this->title = $initiative->title;
        $this->description = $initiative->description;
        $this->form_route = $initiative->form_route ?? '';
        $this->is_coming_soon = $initiative->is_coming_soon;
        $this->show_in_quick_forms = $initiative->show_in_quick_forms;
        $this->is_highlighted = $initiative->is_highlighted;
        $this->existingThumbnailUrl = $initiative->picture_path ? asset('storage/' . $initiative->picture_path) : null;
        $this->showEditInfoModal = true;
    }

    public function saveInfo()
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'form_route' => ['nullable', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        $initiative = Initiative::findOrFail($this->initiativeId);

        if ($this->is_highlighted && Initiative::where('is_highlighted', true)->where('id', '!=', $initiative->id)->count() >= 3) {
            $this->addError('is_highlighted', 'You can only have a maximum of 3 highlighted programs.');
            return;
        }

        $picturePath = $initiative->picture_path;
        if ($this->thumbnail) {
            if ($initiative->picture_path && Storage::disk('public')->exists($initiative->picture_path)) {
                Storage::disk('public')->delete($initiative->picture_path);
            }
            $picturePath = $this->thumbnail->store('thumbnails', 'public');
        }

        $initiative->update([
            'title' => $this->title,
            'description' => $this->description,
            'form_route' => $this->form_route,
            'picture_path' => $picturePath,
            'is_coming_soon' => (bool)$this->is_coming_soon,
            'show_in_quick_forms' => (bool)$this->show_in_quick_forms,
            'is_highlighted' => (bool)$this->is_highlighted,
        ]);

        $this->showEditInfoModal = false;
        $this->loadData();
        session()->flash('success', 'Initiative details updated successfully.');
    }

    // Initiative Edit Builder Action
    public function editBuilder($id)
    {
        $initiative = Initiative::findOrFail($id);
        $this->initiativeId = $initiative->id;
        $this->builderFields = $initiative->form_structure ?? $initiative->custom_fields ?? [];
        $this->showEditBuilderModal = true;
    }

    public function addField()
    {
        $this->builderFields[] = [
            'label' => '',
            'name' => '',
            'type' => 'text',
            'required' => false,
            'placeholder' => '',
            'options' => []
        ];
    }

    public function removeField($index)
    {
        unset($this->builderFields[$index]);
        $this->builderFields = array_values($this->builderFields);
    }

    public function addSelectOption($fieldIndex)
    {
        if (!isset($this->builderFields[$fieldIndex]['options'])) {
            $this->builderFields[$fieldIndex]['options'] = [];
        }
        $this->builderFields[$fieldIndex]['options'][] = '';
    }

    public function removeSelectOption($fieldIndex, $optionIndex)
    {
        unset($this->builderFields[$fieldIndex]['options'][$optionIndex]);
        $this->builderFields[$fieldIndex]['options'] = array_values($this->builderFields[$fieldIndex]['options']);
    }

    public function saveBuilder()
    {
        // Validate all custom fields
        $validatedFields = [];
        foreach ($this->builderFields as $index => $field) {
            if (empty($field['label'])) {
                $this->addError("builderFields.{$index}.label", 'The field label is required.');
                return;
            }

            // Generate field key automatically if empty
            $nameKey = !empty($field['name']) 
                ? Str::snake(strtolower($field['name'])) 
                : Str::snake(strtolower($field['label']));

            $nameKey = preg_replace('/[^a-z0-9_]/', '_', $nameKey);

            $validatedFields[] = [
                'label' => $field['label'],
                'name' => $nameKey,
                'type' => $field['type'] ?? 'text',
                'required' => (bool)($field['required'] ?? false),
                'placeholder' => $field['placeholder'] ?? '',
                'options' => ($field['type'] ?? '') === 'select' ? array_values(array_filter($field['options'] ?? [])) : []
            ];
        }

        $initiative = Initiative::findOrFail($this->initiativeId);
        $initiative->update([
            'form_structure' => $validatedFields,
            'custom_fields' => $validatedFields, // sync both fields for compatibility
        ]);

        $this->showEditBuilderModal = false;
        $this->loadData();
        session()->flash('success', 'Form structure builder updated successfully.');
    }

    // Delete Initiative Action
    public function confirmDeleteInitiative($id)
    {
        $this->deleteConfirmType = 'initiative';
        $this->deleteTargetId = $id;
        $this->confirmPassword = '';
        $this->showDeleteConfirmModal = true;
    }

    public function deleteInitiative()
    {
        $user = auth()->user();
        if (!\Illuminate\Support\Facades\Hash::check($this->confirmPassword, $user->password)) {
            $this->addError('confirmPassword', 'Incorrect password.');
            return;
        }

        $initiative = Initiative::findOrFail($this->deleteTargetId);
        $initiative->delete();

        $this->showDeleteConfirmModal = false;
        $this->loadData();
        session()->flash('success', 'Initiative deleted successfully.');
    }

    public function restoreInitiative($id)
    {
        $initiative = Initiative::onlyTrashed()->findOrFail($id);
        $initiative->restore();
        $this->loadData();
        session()->flash('success', 'Initiative restored successfully.');
    }

    public function forceDeleteInitiative($id)
    {
        $initiative = Initiative::onlyTrashed()->findOrFail($id);
        $initiative->forceDelete();
        $this->loadData();
        session()->flash('success', 'Initiative permanently deleted.');
    }

    // Helpers
    private function resetInitiativeForm()
    {
        $this->initiativeId = null;
        $this->title = '';
        $this->description = '';
        $this->form_route = '';
        $this->is_coming_soon = false;
        $this->show_in_quick_forms = false;
        $this->is_highlighted = false;
        $this->thumbnail = null;
        $this->existingThumbnailUrl = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.structure-manager');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\ActivityLog;

class RecycleBinController extends Controller
{
    /**
     * Display a listing of soft-deleted items.
     */
    public function index(): View
    {
        $kkProfiles = \App\Models\KkProfile::onlyTrashed()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'profiling',
                'module' => 'KK Profiling',
                'title' => $item->surname . ', ' . $item->first_name . ' (' . $item->email . ')',
                'deleted_at' => $item->deleted_at,
            ];
        });

        $sports = \App\Models\SportsRegistration::onlyTrashed()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'sports',
                'module' => 'Sports Registration',
                'title' => $item->first_name . ' ' . $item->last_name . ' - ' . $item->sport . ' (' . $item->reference_number . ')',
                'deleted_at' => $item->deleted_at,
            ];
        });

        $health = \App\Models\HealthRequest::onlyTrashed()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'health',
                'module' => 'Health Consultation',
                'title' => $item->first_name . ' ' . $item->last_name . ' (' . $item->reference_number . ')',
                'deleted_at' => $item->deleted_at,
            ];
        });

        $medicine = \App\Models\MedicineRequest::onlyTrashed()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'medicine',
                'module' => 'Medicine Request',
                'title' => $item->requestor_first_name . ' ' . $item->requestor_last_name . ' (' . $item->reference_number . ')',
                'deleted_at' => $item->deleted_at,
            ];
        });

        $silid = \App\Models\SilidKarununganRequest::onlyTrashed()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'silid',
                'module' => 'Library Booking',
                'title' => $item->requestor_first_name . ' ' . $item->requestor_last_name . ' (' . $item->reference_number . ')',
                'deleted_at' => $item->deleted_at,
            ];
        });

        $custom = \App\Models\CustomRequest::onlyTrashed()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'custom',
                'module' => 'Custom Initiative Request',
                'title' => $item->first_name . ' ' . $item->last_name . ' (' . ($item->reference_number ?? 'N/A') . ')',
                'deleted_at' => $item->deleted_at,
            ];
        });

        $consultation = \App\Models\ConsultationRequest::onlyTrashed()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'consultation',
                'module' => 'Citizen Consultation',
                'title' => $item->subject . ' (' . $item->tracking_id . ')',
                'deleted_at' => $item->deleted_at,
            ];
        });

        $items = collect()
            ->concat($kkProfiles)
            ->concat($sports)
            ->concat($health)
            ->concat($medicine)
            ->concat($silid)
            ->concat($custom)
            ->concat($consultation)
            ->sortByDesc('deleted_at');

        return view('admin.recycle-bin.index', compact('items'));
    }

    /**
     * Restore a soft-deleted item.
     */
    public function restore(string $type, $id): RedirectResponse
    {
        $modelClass = $this->getModelClass($type);
        $record = $modelClass::onlyTrashed()->findOrFail($id);
        $record->restore();

        ActivityLog::record($type . '_restored', $record, [
            'restored_by' => auth()->user()->email,
        ]);

        return redirect()->route('admin.recycle-bin.index')
            ->with('success', 'Item was successfully restored.');
    }

    /**
     * Permanently delete a soft-deleted item.
     */
    public function forceDelete(string $type, $id): RedirectResponse
    {
        $modelClass = $this->getModelClass($type);
        $record = $modelClass::onlyTrashed()->findOrFail($id);
        
        ActivityLog::record($type . '_permanently_deleted', $record, [
            'deleted_by' => auth()->user()->email,
        ]);

        $record->forceDelete();

        return redirect()->route('admin.recycle-bin.index')
            ->with('success', 'Item has been permanently deleted.');
    }

    /**
     * Get the class name for a given type.
     */
    protected function getModelClass(string $type): string
    {
        return match ($type) {
            'profiling' => \App\Models\KkProfile::class,
            'sports' => \App\Models\SportsRegistration::class,
            'health' => \App\Models\HealthRequest::class,
            'medicine' => \App\Models\MedicineRequest::class,
            'silid' => \App\Models\SilidKarununganRequest::class,
            'custom' => \App\Models\CustomRequest::class,
            'consultation' => \App\Models\ConsultationRequest::class,
            default => abort(400, 'Invalid module type.'),
        };
    }
}

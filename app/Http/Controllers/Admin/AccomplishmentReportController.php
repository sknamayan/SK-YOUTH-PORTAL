<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccomplishmentReport;
use App\Models\Initiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AccomplishmentReportController extends Controller
{
    /**
     * Display a listing of accomplishment reports.
     */
    public function index(): View
    {
        $reports = AccomplishmentReport::with('initiative.committee')
            ->latest()
            ->paginate(10);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create(): View
    {
        $initiatives = Initiative::with('committee')->get()->mapWithKeys(function ($item) {
            return [$item->id => $item->committee->name . ' - ' . $item->title];
        });

        return view('admin.reports.create', compact('initiatives'));
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'report_title' => ['required', 'string', 'max:255'],
            'initiative_id' => ['required', 'exists:initiatives,id'],
            'file' => ['required', 'file', 'mimes:pdf,docx,doc,xls,xlsx,png,jpg,jpeg', 'max:2048'],
            'reporting_period' => ['required', 'date'],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('reports', 'public');
        }

        AccomplishmentReport::create([
            'report_title' => $request->input('report_title'),
            'initiative_id' => $request->input('initiative_id'),
            'file_path' => $filePath,
            'reporting_period' => $request->input('reporting_period'),
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Accomplishment report uploaded successfully.');
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit(AccomplishmentReport $report): View
    {
        $initiatives = Initiative::with('committee')->get()->mapWithKeys(function ($item) {
            return [$item->id => $item->committee->name . ' - ' . $item->title];
        });

        return view('admin.reports.edit', compact('report', 'initiatives'));
    }

    /**
     * Update the specified report in storage.
     */
    public function update(Request $request, AccomplishmentReport $report): RedirectResponse
    {
        $request->validate([
            'report_title' => ['required', 'string', 'max:255'],
            'initiative_id' => ['required', 'exists:initiatives,id'],
            'file' => ['nullable', 'file', 'mimes:pdf,docx,doc,xls,xlsx,png,jpg,jpeg', 'max:2048'],
            'reporting_period' => ['required', 'date'],
        ]);

        $data = [
            'report_title' => $request->input('report_title'),
            'initiative_id' => $request->input('initiative_id'),
            'reporting_period' => $request->input('reporting_period'),
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if ($report->file_path) {
                Storage::disk('public')->delete($report->file_path);
            }
            $data['file_path'] = $request->file('file')->store('reports', 'public');
        }

        $report->update($data);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Accomplishment report updated successfully.');
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(AccomplishmentReport $report): RedirectResponse
    {
        if ($report->file_path) {
            Storage::disk('public')->delete($report->file_path);
        }
        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Accomplishment report deleted successfully.');
    }
}

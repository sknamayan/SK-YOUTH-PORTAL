<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Committee;
use App\Models\Initiative;
use App\Models\AccomplishmentReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectExplorerController extends Controller
{
    /**
     * Show the public dynamic project explorer for a specific initiative.
     */
    public function show(string $project_slug, string $committee_slug, int $initiative_id): View
    {
        // 1. Fetch parent Project with its hierarchy tree eager-loaded
        $project = Project::where('slug', $project_slug)
            ->with(['committees.initiatives'])
            ->firstOrFail();

        // 2. Fetch the target active Initiative with its accomplishment reports
        $activeInitiative = Initiative::with(['accomplishmentReports', 'committee'])
            ->findOrFail($initiative_id);

        // 3. Integrity check: ensure initiative belongs to the committee specified in URL
        if ($activeInitiative->committee->slug !== $committee_slug || $activeInitiative->committee->project_id !== $project->id) {
            abort(404, 'Committees and initiatives hierarchy mismatch');
        }

        return view('projects.show', [
            'project' => $project,
            'activeCommittee' => $activeInitiative->committee,
            'activeInitiative' => $activeInitiative,
        ]);
    }

    /**
     * Show the public explorer page for a specific committee/category.
     */
    public function showCommittee(string $project_slug, string $committee_slug): View
    {
        // 1. Fetch parent Project with its hierarchy tree eager-loaded
        $project = Project::where('slug', $project_slug)
            ->with(['committees.initiatives'])
            ->firstOrFail();

        // 2. Fetch active Committee
        $activeCommittee = Committee::where('slug', $committee_slug)
             ->where('project_id', $project->id)
             ->with(['initiatives', 'officials'])
             ->firstOrFail();

        // 3. Get all accomplishment reports for the active committee's initiatives
        $initiativeIds = $activeCommittee->initiatives->pluck('id');
        $accomplishmentReports = AccomplishmentReport::whereIn('initiative_id', $initiativeIds)
            ->with('initiative')
            ->latest()
            ->get();

        return view('projects.committee', [
            'project' => $project,
            'activeCommittee' => $activeCommittee,
            'accomplishmentReports' => $accomplishmentReports,
        ]);
    }
}

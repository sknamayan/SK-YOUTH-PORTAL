<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements.
     */
    public function index(): View
    {
        $announcements = Announcement::with('author')->latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.announcements.index');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'type' => ['required', 'string', 'in:info,warning,success'],
            'is_active' => ['nullable', 'boolean'],
            'published_at' => ['required', 'date'],
        ]);

        Announcement::create([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'type' => $request->input('type'),
            'is_active' => $request->boolean('is_active', true),
            'published_at' => $request->input('published_at'),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement published successfully.');
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement): RedirectResponse
    {
        return redirect()->route('admin.announcements.index');
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'type' => ['required', 'string', 'in:info,warning,success'],
            'is_active' => ['nullable', 'boolean'],
            'published_at' => ['required', 'date'],
        ]);

        $announcement->update([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'type' => $request->input('type'),
            'is_active' => $request->boolean('is_active', false),
            'published_at' => $request->input('published_at'),
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use App\Models\NewsArticle;
use App\Models\TransparencyPost;
use App\Models\CalendarEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    /**
     * Show the master calendar dashboard view.
     */
    public function index(): View
    {
        return view('dashboard.calendar');
    }

    /**
     * Fetch aggregated events list as JSON feed.
     */
    public function events(Request $request): JsonResponse
    {
        $startInput = $request->query('start');
        $endInput = $request->query('end');

        // Parse dates or default to current month range
        $startDate = $startInput ? Carbon::parse($startInput)->startOfDay() : now()->startOfMonth();
        $endDate = $endInput ? Carbon::parse($endInput)->endOfDay() : now()->endOfMonth();

        $events = [];

        // 1. Health Consultation Requests
        $health = HealthRequest::whereBetween('preferred_date', [$startDate, $endDate])->get();
        foreach ($health as $item) {
            $events[] = [
                'id' => 'health_' . $item->id,
                'title' => 'Health Consult: ' . $item->first_name . ' ' . $item->last_name,
                'start' => $item->preferred_date->format('Y-m-d') . 'T' . ($item->preferred_time ?? '09:00:00'),
                'url' => route('dashboard.requests.show', ['health', $item->id]),
                'backgroundColor' => '#eff6ff',
                'borderColor' => '#3b82f6',
                'textColor' => '#1d4ed8',
                'extendedProps' => [
                    'type' => 'health',
                    'status' => $item->status,
                    'time' => $item->preferred_time
                ]
            ];
        }

        // 2. Silid Karunungan Booking Requests
        $silid = SilidKarununganRequest::whereBetween('preferred_date', [$startDate, $endDate])->get();
        foreach ($silid as $item) {
            $events[] = [
                'id' => 'silid_' . $item->id,
                'title' => 'Study Room Booking: ' . $item->requestor_first_name . ' ' . $item->requestor_last_name,
                'start' => $item->preferred_date->format('Y-m-d') . 'T' . ($item->preferred_time ?? '09:00:00'),
                'url' => route('dashboard.requests.show', ['silid', $item->id]),
                'backgroundColor' => '#fdf2f8',
                'borderColor' => '#ec4899',
                'textColor' => '#be185d',
                'extendedProps' => [
                    'type' => 'silid',
                    'status' => $item->status,
                    'time' => $item->preferred_time
                ]
            ];
        }

        // 3. Sports Registrations
        $sports = SportsRegistration::whereBetween('event_date', [$startDate, $endDate])->get();
        foreach ($sports as $item) {
            $events[] = [
                'id' => 'sports_' . $item->id,
                'title' => 'Sports League: ' . $item->first_name . ' ' . $item->last_name . ' (' . $item->sport . ')',
                'start' => $item->event_date->format('Y-m-d') . 'T08:00:00',
                'url' => route('dashboard.requests.show', ['sports', $item->id]),
                'backgroundColor' => '#ecfdf5',
                'borderColor' => '#10b981',
                'textColor' => '#047857',
                'extendedProps' => [
                    'type' => 'sports',
                    'status' => $item->status,
                    'sport' => $item->sport,
                    'team' => $item->team_name
                ]
            ];
        }

        // 4. Medicine Delivery Requests
        $medicine = MedicineRequest::whereBetween('created_at', [$startDate, $endDate])->get();
        foreach ($medicine as $item) {
            $events[] = [
                'id' => 'medicine_' . $item->id,
                'title' => 'Medicine Delivery: ' . $item->requestor_first_name . ' ' . $item->requestor_last_name,
                'start' => $item->created_at->format('Y-m-d\TH:i:s'),
                'url' => route('dashboard.requests.show', ['medicine', $item->id]),
                'backgroundColor' => '#f5f3ff',
                'borderColor' => '#8b5cf6',
                'textColor' => '#6d28d9',
                'extendedProps' => [
                    'type' => 'medicine',
                    'status' => $item->status,
                    'address' => $item->complete_address
                ]
            ];
        }

        // 5. News Articles
        $articles = NewsArticle::whereBetween('published_at', [$startDate, $endDate])->get();
        foreach ($articles as $item) {
            $events[] = [
                'id' => 'news_' . $item->id,
                'title' => 'News: ' . $item->title,
                'start' => $item->published_at->format('Y-m-d\TH:i:s'),
                'url' => route('admin.news.index'),
                'backgroundColor' => '#fff7ed',
                'borderColor' => '#f97316',
                'textColor' => '#c2410c',
                'extendedProps' => [
                    'type' => 'news',
                    'status' => 'approved',
                ]
            ];
        }

        // 6. Transparency Posts
        $posts = TransparencyPost::whereBetween('published_at', [$startDate, $endDate])->get();
        foreach ($posts as $item) {
            $events[] = [
                'id' => 'transparency_' . $item->id,
                'title' => 'Transparency: ' . $item->title,
                'start' => $item->published_at->format('Y-m-d\TH:i:s'),
                'url' => route('admin.transparency.index'),
                'backgroundColor' => '#f8fafc',
                'borderColor' => '#64748b',
                'textColor' => '#334155',
                'extendedProps' => [
                    'type' => 'transparency',
                    'status' => $item->is_active ? 'approved' : 'declined',
                ]
            ];
        }

        // 7. Custom Calendar Events / Programs
        $customEvents = CalendarEvent::whereBetween('start_time', [$startDate, $endDate])->get();
        foreach ($customEvents as $item) {
            $events[] = [
                'id' => 'custom_' . $item->id,
                'title' => 'Event: ' . $item->title,
                'start' => $item->start_time->format('Y-m-d\TH:i:s'),
                'end' => $item->end_time ? $item->end_time->format('Y-m-d\TH:i:s') : null,
                'url' => null, // Display modal instead of redirecting
                'backgroundColor' => '#f0fdf4',
                'borderColor' => '#22c55e',
                'textColor' => '#15803d',
                'extendedProps' => [
                    'type' => 'custom',
                    'status' => $item->status ?? 'active',
                    'description' => $item->description,
                    'start_time' => $item->start_time->format('Y-m-d\TH:i'),
                    'end_time' => $item->end_time ? $item->end_time->format('Y-m-d\TH:i') : ''
                ]
            ];
        }

        return response()->json($events);
    }

    /**
     * Store a newly created custom calendar event.
     */
    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
        ]);

        $event = CalendarEvent::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => Carbon::parse($validated['start_time']),
            'end_time' => !empty($validated['end_time']) ? Carbon::parse($validated['end_time']) : null,
            'user_id' => auth()->id(),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event scheduled successfully.',
            'event' => $event
        ]);
    }

    /**
     * Update an existing custom calendar event.
     */
    public function update(Request $request, $id): JsonResponse
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $event = CalendarEvent::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
        ]);

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => Carbon::parse($validated['start_time']),
            'end_time' => !empty($validated['end_time']) ? Carbon::parse($validated['end_time']) : null,
            'processed_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully.',
            'event' => $event
        ]);
    }

    /**
     * Remove an existing custom calendar event.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $event = CalendarEvent::findOrFail($id);
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event removed successfully.'
        ]);
    }
}

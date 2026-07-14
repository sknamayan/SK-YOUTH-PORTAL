<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(15);
        return view('dashboard.notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function read(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($notification->url) {
            return redirect($notification->url);
        }

        return redirect()->back();
    }

    /**
     * Mark all notifications for the authenticated user as read.
     */
    public function readAll()
    {
        auth()->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}

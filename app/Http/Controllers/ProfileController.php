<?php

namespace App\Http\Controllers;

use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use App\Models\CustomRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's submissions across all 4 request models.
     */
    public function myRequests(Request $request)
    {
        if ($request->user()->canAccessDashboard()) {
            return redirect()->route('dashboard.index');
        }

        $email = auth()->user()->email;

        $health = HealthRequest::with('processedBy')->where('email', $email)->get()->map(function ($item) {
            $item->type_label = 'Health Consultation';
            $item->type_prefix = 'HEA';
            $item->icon = '🏥';
            $item->icon_name = 'health';
            $item->detail = 'Appointment: ' . $item->preferred_date->format('M d, Y') . ' @ ' . $item->preferred_time;
            return $item;
        });

        $medicine = MedicineRequest::with('processedBy')->where('email', $email)->get()->map(function ($item) {
            $item->type_label = 'Pabili Medicine Services';
            $item->type_prefix = 'MED';
            $item->icon = '💊';
            $item->icon_name = 'medicine';
            $item->detail = 'Address: ' . $item->complete_address;
            return $item;
        });

        $silid = SilidKarununganRequest::with('processedBy')->where('email', $email)->get()->map(function ($item) {
            $item->type_label = 'Silid Karunungan Booking';
            $item->type_prefix = 'SIL';
            $item->icon = '📚';
            $item->icon_name = 'education';
            $item->detail = 'Schedule: ' . $item->preferred_date->format('M d, Y') . ' @ ' . $item->preferred_time;
            return $item;
        });

        $sports = SportsRegistration::with('processedBy')->where('email', $email)->get()->map(function ($item) {
            $item->type_label = 'Sports Registration';
            $item->type_prefix = 'SPO';
            $item->icon = '⚽';
            $item->icon_name = 'sports';
            $item->detail = 'Sport: ' . $item->sport . ' (Team: ' . ($item->team_name ?? 'None') . ')';
            return $item;
        });

        $custom = CustomRequest::with('processedBy')->where('email', $email)->get()->map(function ($item) {
            $item->type_label = $item->initiative ? $item->initiative->title : 'Custom Request';
            $item->type_prefix = 'REQ';
            $item->icon = '📝';
            $item->icon_name = 'forms';
            $item->detail = 'Form Submission for ' . ($item->initiative ? $item->initiative->title : 'Initiative');
            return $item;
        });

        $results = collect()
            ->concat($health)
            ->concat($medicine)
            ->concat($silid)
            ->concat($sports)
            ->concat($custom)
            ->sortByDesc('created_at');

        // Calculate counts
        $total = $results->count();
        $pending = $results->whereIn('status', ['pending', 'review'])->count();
        $approved = $results->where('status', 'approved')->count();
        $declined = $results->where('status', 'declined')->count();

        // Get KK Profile: query by user_id first, then fallback to email
        $profile = \App\Models\KkProfile::where('user_id', auth()->id())
            ->orWhere('email', $email)
            ->first();

        return view('profile.my-requests', compact('results', 'total', 'pending', 'approved', 'declined', 'profile'));
    }

    /**
     * Display the user's profile edit forms.
     */
    public function edit(Request $request): View
    {
        $sessions = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                // Parse user agent
                $ua = $session->user_agent;
                $browser = 'Unknown Browser';
                $platform = 'Unknown OS';

                if (preg_match('/(MSIE|Trident)/i', $ua)) {
                    $browser = 'Internet Explorer';
                } elseif (preg_match('/Firefox/i', $ua)) {
                    $browser = 'Mozilla Firefox';
                } elseif (preg_match('/Chrome/i', $ua)) {
                    $browser = 'Google Chrome';
                } elseif (preg_match('/Safari/i', $ua)) {
                    $browser = 'Apple Safari';
                } elseif (preg_match('/Opera/i', $ua)) {
                    $browser = 'Opera';
                }

                if (preg_match('/linux/i', $ua)) {
                    $platform = 'Linux';
                } elseif (preg_match('/macintosh|mac os x/i', $ua)) {
                    $platform = 'macOS';
                } elseif (preg_match('/windows|win32/i', $ua)) {
                    $platform = 'Windows';
                } elseif (preg_match('/iphone|ipad/i', $ua)) {
                    $platform = 'iOS';
                } elseif (preg_match('/android/i', $ua)) {
                    $platform = 'Android';
                }

                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'device' => $platform . ' (' . $browser . ')',
                    'last_active' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'is_current' => $session->id === request()->session()->getId(),
                ];
            });

        return view('profile.edit', [
            'user' => $request->user(),
            'sessions' => $sessions,
        ]);
    }

    /**
     * Update the user's basic profile information.
     */
    public function updateInfo(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'contact_number' => ['nullable', 'string', 'max:20'],
        ]);

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->name = $request->input('first_name') . ' ' . $request->input('last_name');
        $user->email = $request->input('email');
        $user->contact_number = $request->input('contact_number');

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit', ['tab' => 'account'])->with('success', 'Profile information updated successfully.');
    }

    /**
     * Update the user's preferences.
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'theme' => ['required', 'in:light,dark,system'],
            'language' => ['required', 'in:en,fil'],
            'notify_request_status' => ['nullable', 'boolean'],
            'notify_announcements' => ['nullable', 'boolean'],
        ]);

        $user->theme = $request->input('theme');
        $user->language = $request->input('language');
        $user->notify_request_status = $request->has('notify_request_status');
        $user->notify_announcements = $request->has('notify_announcements');
        $user->save();

        session(['locale' => $user->language]);

        return redirect()->route('profile.edit', ['tab' => 'display'])->with('success', 'Preferences updated successfully.');
    }

    /**
     * Update the user's profile avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'avatar_base64' => ['required', 'string'],
        ]);

        $base64 = $request->input('avatar_base64');
        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            $data = substr($base64, strpos($base64, ',') + 1);
            $data = base64_decode($data);
            $ext = strtolower($type[1]);

            if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                return back()->with('error', 'Invalid image format.');
            }

            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $ext;

            if (!\Illuminate\Support\Facades\Storage::exists('public/avatars')) {
                \Illuminate\Support\Facades\Storage::makeDirectory('public/avatars');
            }

            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::delete('public/avatars/' . $user->avatar);
            }

            \Illuminate\Support\Facades\Storage::put('public/avatars/' . $filename, $data);
            $user->avatar = $filename;
            $user->save();

            return redirect()->route('profile.edit', ['tab' => 'account'])->with('success', 'Profile avatar updated successfully.');
        }

        return back()->with('error', 'Failed to upload profile picture.');
    }

    /**
     * Logout other browser sessions.
     */
    public function logoutOtherSessions(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return redirect()->route('profile.edit', ['tab' => 'security'])->with('success', 'Logged out of other sessions successfully.');
    }

    /**
     * Download the user's data (DPA/GDPR export).
     */
    public function downloadData(Request $request)
    {
        $user = $request->user();
        $email = $user->email;

        $kkProfile = \App\Models\KkProfile::where('user_id', $user->id)
            ->orWhere('email', $email)
            ->first();

        $health = HealthRequest::where('email', $email)->get();
        $medicine = MedicineRequest::where('email', $email)->get();
        $silid = SilidKarununganRequest::where('email', $email)->get();
        $sports = SportsRegistration::where('email', $email)->get();
        $custom = CustomRequest::where('email', $email)->get();

        $data = [
            'exported_at' => now()->toIso8601String(),
            'portal' => config('app.name'),
            'account' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'name' => $user->name,
                'email' => $user->email,
                'contact_number' => $user->contact_number,
                'role' => $user->role,
                'created_at' => $user->created_at->toIso8601String(),
            ],
            'kk_profile' => $kkProfile ? [
                'birthdate' => $kkProfile->birthdate,
                'gender' => $kkProfile->gender,
                'registered_voter' => $kkProfile->registered_sk_voter,
                'classification' => $kkProfile->youth_classification,
                'status' => $kkProfile->status,
            ] : null,
            'requests' => [
                'health_consultations' => $health->map(fn($item) => [
                    'reference_number' => $item->reference_number,
                    'preferred_date' => $item->preferred_date ? $item->preferred_date->toDateString() : null,
                    'status' => $item->status,
                    'created_at' => $item->created_at->toIso8601String(),
                ]),
                'medicine_pabili' => $medicine->map(fn($item) => [
                    'reference_number' => $item->reference_number,
                    'address' => $item->complete_address,
                    'status' => $item->status,
                    'created_at' => $item->created_at->toIso8601String(),
                ]),
                'silid_karunungan_bookings' => $silid->map(fn($item) => [
                    'reference_number' => $item->reference_number,
                    'preferred_date' => $item->preferred_date ? $item->preferred_date->toDateString() : null,
                    'status' => $item->status,
                    'created_at' => $item->created_at->toIso8601String(),
                ]),
                'sports_siklab_registrations' => $sports->map(fn($item) => [
                    'reference_number' => $item->reference_number,
                    'sport' => $item->sport,
                    'team_name' => $item->team_name,
                    'status' => $item->status,
                    'created_at' => $item->created_at->toIso8601String(),
                ]),
                'custom_forms' => $custom->map(fn($item) => [
                    'reference_number' => $item->reference_number,
                    'initiative' => $item->initiative ? $item->initiative->title : null,
                    'status' => $item->status,
                    'created_at' => $item->created_at->toIso8601String(),
                ]),
            ],
        ];

        $filename = 'sk-portal-data-' . $user->id . '-' . now()->format('Y-m-d') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('profile.edit', ['tab' => 'security'])->with('success', 'Password changed successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Account deleted successfully.');
    }
}

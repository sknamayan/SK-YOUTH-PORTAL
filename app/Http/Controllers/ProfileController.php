<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private const SETTINGS_TABS = ['account', 'display', 'security', 'notifications', 'privacy'];

    /**
     * Resolve the settings tab to redirect back to after an action.
     */
    private function settingsTab(Request $request, string $default = 'account'): string
    {
        $tab = $request->input('settings_tab', $default);

        return in_array($tab, self::SETTINGS_TABS, true) ? $tab : $default;
    }

    /**
     * Redirect back to the settings page on the requested tab.
     */
    private function redirectToSettings(Request $request, string $defaultTab, string $message, string $flashKey = 'success'): RedirectResponse
    {
        return redirect()
            ->route('profile.edit', ['tab' => $this->settingsTab($request, $defaultTab)])
            ->with($flashKey, $message);
    }

    public function myRequests(Request $request): View
    {
        try {
            $user = auth()->user();
            $email = $user ? trim(strtolower($user->email)) : '';

            $requests = \App\Models\CustomRequest::where(function($q) use ($user, $email) {
                    if ($email) {
                        $q->whereRaw('LOWER(email) = ?', [$email])
                          ->orWhereRaw('LOWER(citizen_email) = ?', [$email]);
                    }
                    if ($user) {
                        $q->orWhere('user_id', $user->id);
                    }
                })
                ->latest()
                ->get();

            $sportsRegistrations = \App\Models\SportsRegistration::where(function($q) use ($email) {
                    if ($email) {
                        $q->whereRaw('LOWER(email) = ?', [$email]);
                    }
                })
                ->latest()
                ->get();

            $healthRequests = \App\Models\HealthRequest::where(function($q) use ($email) {
                    if ($email) {
                        $q->whereRaw('LOWER(email) = ?', [$email]);
                    }
                })
                ->latest()
                ->get();

            $medicineRequests = \App\Models\MedicineRequest::where(function($q) use ($email) {
                    if ($email) {
                        $q->whereRaw('LOWER(email) = ?', [$email]);
                    }
                })
                ->latest()
                ->get();

            $silidRequests = \App\Models\SilidKarununganRequest::where(function($q) use ($email) {
                    if ($email) {
                        $q->whereRaw('LOWER(email) = ?', [$email]);
                    }
                })
                ->latest()
                ->get();

            $registrationResponses = \App\Models\RegistrationResponse::where(function($q) use ($email) {
                    if ($email) {
                        $q->whereRaw('LOWER(citizen_email) = ?', [$email]);
                    }
                })
                ->latest()
                ->get();

            $kkProfile = \App\Models\KkProfile::withoutGlobalScopes()
                ->where(function($q) use ($user, $email) {
                    $q->where('user_id', $user->id);
                    if ($email) {
                        $q->orWhereRaw('LOWER(email) = ?', [$email]);
                    }
                })
                ->first();

            if (!$kkProfile && $user) {
                // Decrypted / Case-insensitive fallback lookup
                $allProfiles = \App\Models\KkProfile::withoutGlobalScopes()->get();
                $kkProfile = $allProfiles->first(function ($p) use ($email, $user) {
                    if ($p->user_id == $user->id) return true;
                    $pEmail = strtolower((string)$p->email);
                    $uEmail = strtolower((string)$email);
                    return $pEmail && $uEmail && $pEmail === $uEmail;
                });
            }

            if ($kkProfile && !$kkProfile->user_id && $user) {
                $kkProfile->update(['user_id' => $user->id]);
            }

            $profile = $kkProfile;

            // Combine requests, sportsRegistrations, health, medicine, silid, and dynamic registration responses into unified $results collection
            $mappedRequests = $requests->map(function($req) {
                $req->type_label = object_get($req, 'type', 'Custom Request');
                $req->detail = object_get($req, 'description') ?? object_get($req, 'details') ?? 'N/A';
                return $req;
            });

            $mappedSports = $sportsRegistrations->map(function($sport) {
                $sport->type_label = 'SIKLAB Sports (' . object_get($sport, 'sport', 'Tournament') . ' - ' . object_get($sport, 'division', 'General') . ')';
                $sport->detail = 'Position: ' . object_get($sport, 'position', 'N/A') . (object_get($sport, 'team_name') ? ' | Team: ' . object_get($sport, 'team_name') : '');
                return $sport;
            });

            $mappedHealth = $healthRequests->map(function($req) {
                $req->type_label = 'Health Request';
                $req->detail = 'Concerns: ' . (object_get($req, 'concerns') ?? 'N/A');
                return $req;
            });

            $mappedMedicine = $medicineRequests->map(function($req) {
                $req->type_label = 'Medicine Request';
                $req->detail = 'Complete Address: ' . (object_get($req, 'complete_address') ?? 'N/A');
                return $req;
            });

            $mappedSilid = $silidRequests->map(function($req) {
                $req->type_label = 'Silid Karunungan Request';
                $preferredDate = object_get($req, 'preferred_date');
                $dateStr = $preferredDate instanceof \Carbon\Carbon ? $preferredDate->format('Y-m-d') : ($preferredDate ?? 'N/A');
                $req->detail = 'Preferred Date: ' . $dateStr . ' | Time: ' . (object_get($req, 'preferred_time') ?? 'N/A');
                return $req;
            });

            $mappedResponses = $registrationResponses->map(function($resp) {
                $resp->type_label = 'Sports Registration (' . ($resp->registrationForm?->division_name ?? 'Dynamic') . ')';
                $resp->detail = 'Citizen: ' . ($resp->citizen_name ?? 'N/A');
                return $resp;
            });

            $results = $mappedRequests
                ->concat($mappedSports)
                ->concat($mappedHealth)
                ->concat($mappedMedicine)
                ->concat($mappedSilid)
                ->concat($mappedResponses)
                ->sortByDesc('created_at');

            $total = $results->count();
            $pending = $results->whereIn('status', ['pending', 'Pending', 'review', 'under_review'])->count();
            $approved = $results->whereIn('status', ['approved', 'Approved', 'confirmed', 'completed'])->count();
            $declined = $results->whereIn('status', ['declined', 'Declined', 'rejected', 'cancelled'])->count();

            return view('profile.my-requests', compact('user', 'requests', 'kkProfile', 'profile', 'sportsRegistrations', 'results', 'total', 'pending', 'approved', 'declined'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error loading myRequests page: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            $user = auth()->user();
            $requests = collect();
            $sportsRegistrations = collect();
            $kkProfile = null;
            
            if ($user) {
                $kkProfile = \App\Models\KkProfile::withoutGlobalScopes()
                    ->where(function($q) use ($user) {
                        $q->where('user_id', $user->id)
                          ->orWhere('email', $user->email);
                    })->first();
            }

            $profile = $kkProfile;
            $results = collect();
            $total = 0;
            $pending = 0;
            $approved = 0;
            $declined = 0;

            return view('profile.my-requests', compact('user', 'requests', 'kkProfile', 'profile', 'sportsRegistrations', 'results', 'total', 'pending', 'approved', 'declined'));
        }
    }

    public function edit(Request $request): View
    {
        $sessions = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
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

    public function updateInfo(Request $request): RedirectResponse
    {
        $user = $request->user();

        $input = $request->all();
        if (isset($input['first_name'])) {
            $input['first_name'] = mb_strtoupper($input['first_name'], 'UTF-8');
        }
        if (isset($input['last_name'])) {
            $input['last_name'] = mb_strtoupper($input['last_name'], 'UTF-8');
        }
        $request->merge($input);

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

        return $this->redirectToSettings($request, 'account', 'Profile information updated successfully.');
    }

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
        $user->notify_request_status = $request->boolean('notify_request_status');
        $user->notify_announcements = $request->boolean('notify_announcements');
        $user->save();

        app()->setLocale($user->language);
        session(['locale' => $user->language]);

        $defaultTab = $this->settingsTab($request, 'display') === 'notifications' ? 'notifications' : 'display';

        return $this->redirectToSettings($request, $defaultTab, 'Preferences updated successfully.');
    }

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

            return $this->redirectToSettings($request, 'account', 'Profile avatar updated successfully.');
        }

        return back()->with('error', 'Failed to upload profile picture.');
    }

    public function logoutOtherSessions(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return $this->redirectToSettings($request, 'security', 'Logged out of other sessions successfully.');
    }

    public function downloadData(Request $request)
    {
        $user = $request->user();
        $email = $user->email;

        $kkProfile = \App\Models\KkProfile::where('user_id', $user->id)
            ->orWhere('email', $email)
            ->first();

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
                'birthdate' => $kkProfile->dob ?? null,
                'gender' => $kkProfile->sex ?? null,
                'registered_voter' => $kkProfile->registered_sk_voter,
                'classification' => $kkProfile->youth_classification,
                'status' => $kkProfile->status,
            ] : null,
            'requests' => [],
        ];

        $filename = 'sk-portal-data-' . $user->id . '-' . now()->format('Y-m-d') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return $this->redirectToSettings($request, 'security', 'Password changed successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return $this->redirectToSettings(
                $request,
                'privacy',
                'Superadmin accounts cannot delete their own profile.',
                'error'
            );
        }

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Account deleted successfully.');
    }
}

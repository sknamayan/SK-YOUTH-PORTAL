<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
 
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // default user role
            'is_approved' => true,
        ]);
 
        // Resilient, case-insensitive match on pre-encoded KkProfile (first name, surname/last name, and date of birth)
        $profile = \App\Models\KkProfile::where(function($query) use ($request) {
            $query->whereRaw('LOWER(first_name) = ?', [strtolower($request->first_name)])
                  ->whereRaw('LOWER(surname) = ?', [strtolower($request->last_name)])
                  ->whereDate('dob', $request->birthdate);
        })->whereNull('user_id')->first();
 
        if ($profile) {
            // Attach the newly created user's ID to this profile and mark it approved so they bypass onboarding
            $profile->update([
                'user_id' => $user->id,
                'status' => 'approved',
            ]);
        }
 
        event(new Registered($user));
 
        Auth::login($user);
 
        return redirect()->to('/')->with('success', 'Account created successfully! Welcome to the SK Namayan Youth Portal.');
    }
}

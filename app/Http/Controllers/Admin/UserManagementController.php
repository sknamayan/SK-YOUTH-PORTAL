<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $yearFilter = $request->input('year');
        $approvedFilter = $request->input('approved');
        
        $limit = $request->input('limit', 20);
        if (!in_array($limit, [10, 15, 25, 50, 100])) {
            $limit = 20;
        }

        $query = User::latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter && in_array($roleFilter, ['user', 'superadmin', 'admin', 'staff', 'dpo'])) {
            $query->where('role', $roleFilter);
        }

        if ($yearFilter) {
            $query->whereYear('created_at', $yearFilter);
        }

        if ($approvedFilter !== null && $approvedFilter !== '') {
            $query->where('is_approved', (bool) $approvedFilter);
        }

        $users = $query->paginate($limit)->withQueryString();

        $driver = \DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $years = User::selectRaw("strftime('%Y', created_at) as year")
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();
        } else {
            $years = User::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();
        }
        if (empty($years)) {
            $years = [date('Y')];
        }

        return view('admin.users.index', compact(
            'users', 'search', 'roleFilter', 'yearFilter', 'approvedFilter', 'limit', 'years'
        ));
    }

    /**
     * Update the user's role.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access Denied.');
        }

        $request->validate([
            'role' => ['required', 'in:user,superadmin,admin,staff,dpo'],
        ]);

        $user->role = $request->input('role');
        $user->save();

        return back()->with('success', 'User role updated successfully.');
    }

    /**
     * Approve the user's account.
     */
    public function approve(User $user): RedirectResponse
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access Denied.');
        }

        $user->is_approved = true;
        $user->save();

        return back()->with('success', "User account for {$user->name} approved successfully.");
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access Denied.');
        }

        if (auth()->id() === $user->id) {
            abort(403, 'Self-deletion is blocked.');
        }

        $user->delete();

        return back()->with('success', 'User account deleted successfully.');
    }
}

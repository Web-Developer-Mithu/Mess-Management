<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function editOwn()
    {
        return view('account.password', ['targetUser' => auth()->user(), 'isAdminReset' => false]);
    }

    public function updateOwn(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->update(['password' => Hash::make($validated['password'])]);
        ActivityLog::record($user, 'password_changed');

        return redirect()->route($user->isSuperAdmin() ? 'superadmin.dashboard' : 'dashboard')
            ->with('success', 'আপনার password সফলভাবে পরিবর্তন হয়েছে।');
    }

    public function editForUser(User $user)
    {
        abort_unless($user->role === User::ROLE_MESS_ADMIN, 404);

        return view('account.password', ['targetUser' => $user, 'isAdminReset' => true]);
    }

    public function updateForUser(Request $request, User $user)
    {
        abort_unless($user->role === User::ROLE_MESS_ADMIN, 404);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);
        ActivityLog::record($user, 'password_reset_by_super_admin');

        return redirect()->route('superadmin.dashboard')
            ->with('success', $user->name . ' এর password সফলভাবে reset হয়েছে।');
    }
}

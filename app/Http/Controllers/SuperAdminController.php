<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mess;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;

class SuperAdminController extends Controller
{
    public function index()
    {
        $messes = Mess::withCount('members')
            ->with(['users' => function ($q) {
                $q->where('role', User::ROLE_MESS_ADMIN);
            }])
            ->get();

        return view('superadmin.dashboard', compact('messes'));
    }

    public function createMess()
    {
        return view('superadmin.messes.create');
    }

    public function editMess(Mess $mess)
    {
        return view('superadmin.messes.edit', compact('mess'));
    }

    public function warnings()
    {
        $messes = Mess::orderBy('name')->get();

        return view('superadmin.warnings.index', compact('messes'));
    }

    public function updateMess(Request $request, Mess $mess)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'balance_alert_threshold' => ['nullable', 'numeric', 'min:0'],
            'balance_alert_comment' => ['nullable', 'string', 'max:1000'],
            'total_balance_warning_threshold' => ['nullable', 'numeric'],
            'total_balance_warning_message' => ['nullable', 'string', 'max:1000'],
            'dining_scene_message' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('logo')) {
            if ($mess->logo) {
                Storage::disk('public')->delete($mess->logo);
            }

            $validated['logo'] = $request->file('logo')->store('mess-logos', 'public');
        }

        $mess->update($validated);

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Mess নাম, ঠিকানা ও logo সফলভাবে আপডেট হয়েছে।');
    }

    public function storeMess(Request $request)
    {
        $request->validate([
            'mess_name'        => 'required|string|max:255',
            'mess_address'     => 'nullable|string|max:255',
            'logo'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'manager_name'     => 'required|string|max:255',
            'manager_email'    => 'required|string|email|max:255|unique:users,email',
            'manager_password' => 'required|string|min:8',
        ]);

        $mess = Mess::create([
            'name'    => $request->mess_name,
            'address' => $request->mess_address,
            'logo'    => $request->hasFile('logo')
                ? $request->file('logo')->store('mess-logos', 'public')
                : null,
        ]);

        User::create([
            'name'     => $request->manager_name,
            'email'    => $request->manager_email,
            'password' => Hash::make($request->manager_password),
            'role'     => User::ROLE_MESS_ADMIN,
            'mess_id'  => $mess->id,
        ]);

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Mess এবং Manager সফলভাবে তৈরি হয়েছে!');
    }

    public function deleteMess($mess_id)
    {
        $mess = Mess::findOrFail($mess_id);
        ActivityLog::record($mess, 'purged');
        $mess->delete(); // cascade deletes users, members, meals, expenses, payments, settings

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Mess এবং সব সংশ্লিষ্ট ডেটা মুছে ফেলা হয়েছে।');
    }

    public function impersonate($mess_id)
    {
        $manager = User::where('mess_id', $mess_id)
            ->where('role', User::ROLE_MESS_ADMIN)
            ->first();

        if (!$manager) {
            return back()->withErrors('এই mess-এর কোনো manager পাওয়া যায়নি।');
        }

        // Store current super admin id to return later
        session()->put('impersonate_by', Auth::id());

        Auth::login($manager);

        return redirect()->route('dashboard');
    }

    public function stopImpersonating()
    {
        if (session()->has('impersonate_by')) {
            $superAdminId = session()->pull('impersonate_by');
            Auth::loginUsingId($superAdminId);
            return redirect()->route('superadmin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}

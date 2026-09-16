<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mess;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;

class SuperAdminController extends Controller
{
    public function index()
    {
        $messes = Mess::where('status', 'active')->withCount('members')
            ->with(['users' => function ($q) {
                $q->where('role', User::ROLE_MESS_ADMIN);
            }])
            ->get();
        $inactiveMesses = Mess::where('status', 'inactive')->withCount('members')->orderBy('name')->get();

        return view('superadmin.dashboard', compact('messes', 'inactiveMesses'));
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

    public function siteSettings()
    {
        $settings = SiteSetting::getCurrent();

        return view('superadmin.site-settings', compact('settings'));
    }

    public function updateSiteSettings(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => ['nullable', 'string', 'max:255'],
            'brand_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'brand_logo_url' => ['nullable', 'url', 'max:255'],
            'developer_title' => ['nullable', 'string', 'max:255'],
            'developer_name' => ['nullable', 'string', 'max:255'],
            'developer_tagline' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'avatar_url' => ['nullable', 'url', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $settings = SiteSetting::getCurrent();

        if ($request->hasFile('brand_logo')) {
            if ($settings->brand_logo_path) {
                Storage::disk('public')->delete($settings->brand_logo_path);
            }

            $validated['brand_logo_path'] = $request->file('brand_logo')->store('brand-logos', 'public');
            $validated['brand_logo_url'] = null;
        }

        if ($request->hasFile('avatar')) {
            if ($settings->avatar_path) {
                Storage::disk('public')->delete($settings->avatar_path);
            }

            $validated['avatar_path'] = $request->file('avatar')->store('developer-avatars', 'public');
            $validated['avatar_url'] = null;
        }

        $settings->fill($validated)->save();

        return redirect()->route('superadmin.dashboard')->with('success', 'Site branding updated successfully.');
    }

    public function updateMess(Request $request, Mess $mess)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:active,inactive'],
            'inactive_message' => ['nullable', 'string', 'max:1000'],
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

    public function deactivateMess(Mess $mess)
    {
        $mess->update(['status' => 'inactive']);
        ActivityLog::record($mess, 'deactivated');

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Mess inactive করা হয়েছে। সব data محفوظ আছে।');
    }

    public function activateMess(Mess $mess)
    {
        $mess->update(['status' => 'active']);
        ActivityLog::record($mess, 'activated');

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Mess আবার active করা হয়েছে।');
    }

    public function impersonate($mess_id)
    {
        $manager = User::where('mess_id', $mess_id)
            ->where('role', User::ROLE_MESS_ADMIN)
            ->whereHas('mess', fn ($query) => $query->where('status', 'active'))
            ->first();

        if (!$manager) {
            return back()->withErrors('এই mess-এর কোনো manager পাওয়া যায়নি।');
        }

        // Store current super admin id to return later
        session()->put('impersonate_by', Auth::id());

        Auth::login($manager);

        session()->flash('impersonation_popup', [
            'mess_name' => $manager->mess?->name ?? 'Mess',
            'manager_name' => $manager->name,
        ]);

        return redirect()->route('dashboard');
    }

    public function stopImpersonating()
    {
        if (session()->has('impersonate_by')) {
            $superAdminId = session()->pull('impersonate_by');
            Auth::loginUsingId($superAdminId);
            return redirect()->route('superadmin.dashboard')->with('info', 'Super Admin হিসেবে পুনরায় ফিরে এসেছেন।');
        }

        return redirect()->route('dashboard');
    }
}

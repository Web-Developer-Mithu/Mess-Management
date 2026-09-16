<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user && $user->isSuperAdmin()) {
                return redirect()->intended(route('superadmin.dashboard'))->with('success', 'Super Admin হিসেবে স্বাগতম!');
            }

            if ($user?->mess && $user->mess->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $inactiveMsg = $user->mess->inactive_message
                    ?: 'এই Mess বর্তমানে inactive। Super Admin activate করলে আবার login করতে পারবেন।';

                return back()->withErrors([
                    'email' => $inactiveMsg,
                ])->with('error', $inactiveMsg)->onlyInput('email');
            }

            return redirect()->intended(route('dashboard'))->with('success', 'সফলভাবে লগইন হয়েছে। স্বাগতম!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->with('error', 'ইমেইল বা পাসওয়ার্ড সঠিক নয়।')->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('info', 'সফলভাবে লগআউট হয়েছেন।');
    }
}


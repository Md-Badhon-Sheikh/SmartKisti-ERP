<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Show the form to set a new password, once the OTP has been verified.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('otp_verified_mobile')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    /**
     * Set the user's new password.
     */
    public function store(Request $request): RedirectResponse
    {
        $mobile = $request->session()->get('otp_verified_mobile');

        abort_unless($mobile, 419);

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::where('mobile', $mobile)->firstOrFail();

        $user->update(['password' => $request->input('password')]);

        PasswordResetOtp::where('mobile', $mobile)->delete();

        $request->session()->forget(['otp_mobile', 'otp_verified_mobile']);

        return redirect()->route('login')->with('status', 'পাসওয়ার্ড সফলভাবে পরিবর্তন হয়েছে। এখন লগইন করুন।');
    }
}

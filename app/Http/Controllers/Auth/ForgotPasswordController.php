<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SendPasswordResetOtp;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Show the form to request a password reset OTP.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send an OTP to the given mobile number.
     */
    public function store(Request $request, SendPasswordResetOtp $sendOtp): RedirectResponse
    {
        $request->validate([
            'mobile' => ['required', 'string', 'exists:users,mobile'],
        ]);

        $mobile = $request->string('mobile')->toString();

        $sendOtp->handle($mobile);

        $request->session()->put('otp_mobile', $mobile);

        return redirect()->route('password.otp');
    }
}

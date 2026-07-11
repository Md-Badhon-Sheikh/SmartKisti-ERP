<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SendPasswordResetOtp;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    /**
     * Show the OTP verification form.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $mobile = $request->session()->get('otp_mobile');

        if (! $mobile) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', ['mobile' => $mobile]);
    }

    /**
     * Verify the submitted OTP.
     */
    public function store(Request $request): RedirectResponse
    {
        $mobile = $request->session()->get('otp_mobile');

        abort_unless($mobile, 419);

        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $record = PasswordResetOtp::where('mobile', $mobile)->latest('id')->first();

        if (! $record || $record->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'otp' => __('The code has expired. Please resend the code.'),
            ]);
        }

        if ($record->attempts >= 5) {
            throw ValidationException::withMessages([
                'otp' => __('Too many incorrect attempts. Please resend the code.'),
            ]);
        }

        if (! Hash::check($request->input('otp'), $record->otp)) {
            $record->increment('attempts');

            throw ValidationException::withMessages([
                'otp' => __('The code is incorrect.'),
            ]);
        }

        $record->update(['verified' => true]);

        $request->session()->put('otp_verified_mobile', $mobile);

        return redirect()->route('password.reset');
    }

    /**
     * Resend a fresh OTP to the mobile number stored in the session.
     */
    public function resend(Request $request, SendPasswordResetOtp $sendOtp): RedirectResponse
    {
        $mobile = $request->session()->get('otp_mobile');

        abort_unless($mobile, 419);

        $sendOtp->handle($mobile, errorField: 'otp');

        return back()->with('status', __('A new code has been sent.'));
    }
}

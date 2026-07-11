<?php

namespace App\Actions\Auth;

use App\Models\PasswordResetOtp;
use App\Services\Sms\SmsManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class SendPasswordResetOtp
{
    public function __construct(private readonly SmsManager $sms) {}

    /**
     * Generate a fresh OTP for the mobile number and deliver it via SMS.
     *
     * @throws ValidationException when the mobile number is being throttled.
     */
    public function handle(string $mobile, string $errorField = 'mobile'): void
    {
        $throttleKey = 'otp-request:'.$mobile;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            throw ValidationException::withMessages([
                $errorField => __('Too many attempts. Please try again in :seconds seconds.', [
                    'seconds' => RateLimiter::availableIn($throttleKey),
                ]),
            ]);
        }

        RateLimiter::hit($throttleKey, 60);

        $otp = (string) random_int(100000, 999999);

        PasswordResetOtp::where('mobile', $mobile)->delete();

        PasswordResetOtp::create([
            'mobile' => $mobile,
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->sms->driver()->send(
            $mobile,
            "SmartKisti ERP: আপনার পাসওয়ার্ড রিসেট কোড {$otp}। এটি ৫ মিনিটের জন্য বৈধ। কারো সাথে শেয়ার করবেন না।"
        );
    }
}

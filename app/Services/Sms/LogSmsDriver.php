<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

/**
 * Development driver: writes SMS messages to the log instead of sending
 * them. Swap SMS_DRIVER for a real gateway (e.g. bulksmsbd, alphasms)
 * before going to production.
 */
class LogSmsDriver implements SmsDriver
{
    public function send(string $mobile, string $message): bool
    {
        Log::channel('single')->info("[SMS] to {$mobile}: {$message}");

        return true;
    }
}

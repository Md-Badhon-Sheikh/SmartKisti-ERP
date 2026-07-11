<?php

namespace App\Services\Sms;

interface SmsDriver
{
    /**
     * Send an SMS message to the given mobile number.
     */
    public function send(string $mobile, string $message): bool;
}

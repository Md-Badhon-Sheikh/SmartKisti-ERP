<?php

namespace App\Services\Sms;

use App\Models\SmsLog;
use Throwable;

/**
 * Sends an SMS via the configured driver and always records the attempt
 * in sms_logs — SMS activity must never live only inside a business
 * table (Sale, InstallmentPayment, ...), it is always logged here.
 */
class SmsLogService
{
    public function __construct(private readonly SmsManager $sms) {}

    /**
     * @param  array{customer_id?: ?int, sale_id?: ?int, installment_payment_id?: ?int}  $context
     */
    public function send(string $mobile, string $message, string $smsType, array $context = []): SmsLog
    {
        $log = SmsLog::create([
            'customer_id' => $context['customer_id'] ?? null,
            'sale_id' => $context['sale_id'] ?? null,
            'installment_payment_id' => $context['installment_payment_id'] ?? null,
            'mobile' => $mobile,
            'sms_type' => $smsType,
            'message' => $message,
            'status' => 'pending',
        ]);

        try {
            $sent = $this->sms->driver()->send($mobile, $message);

            $log->update([
                'status' => $sent ? 'sent' : 'failed',
                'provider_response' => $sent
                    ? 'Sent via "' . config('services.sms.driver', 'log') . '" driver.'
                    : 'Driver reported failure.',
                'sent_at' => $sent ? now() : null,
            ]);
        } catch (Throwable $e) {
            $log->update([
                'status' => 'failed',
                'provider_response' => $e->getMessage(),
            ]);
        }

        return $log->fresh();
    }
}

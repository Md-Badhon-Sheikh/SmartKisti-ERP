<?php

namespace App\Models;

use App\Enums\GlobalConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallmentPayment extends Model
{
    protected $fillable = [
        'installment_plan_id',
        'sale_id',
        'customer_id',
        'payment_date',
        'installment_no',
        'amount',
        'payment_method',
        'received_by',
        'remarks',
        'sms_sent',
        'receipt_no',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'installment_no' => 'integer',
            'amount' => 'decimal:2',
            'sms_sent' => 'boolean',
        ];
    }

    public function installmentPlan(): BelongsTo
    {
        return $this->belongsTo(InstallmentPlan::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function paymentMethodName(): ?string
    {
        return GlobalConstant::paymentMethodName($this->payment_method);
    }
}

<?php

namespace App\Services;

use App\Models\InstallmentPayment;
use App\Models\Receipt;
use App\Models\Sale;

/**
 * Issues a formal Receipt row whenever money is actually collected
 * (a cash sale payment or an installment payment). Refunds use the
 * same table with receipt_type = 'refund' once a refund flow exists.
 */
class ReceiptService
{
    public function forSale(Sale $sale): ?Receipt
    {
        if ($sale->paid_amount <= 0) {
            return null;
        }

        return Receipt::create([
            'receipt_no' => $this->generateReceiptNo(),
            'sale_id' => $sale->id,
            'payment_id' => null,
            'customer_id' => $sale->customer_id,
            'amount' => $sale->paid_amount,
            'receipt_type' => $sale->sale_type === 'installment' ? 'installment' : 'cash',
            'date' => $sale->sale_date,
        ]);
    }

    public function forInstallmentPayment(InstallmentPayment $payment): Receipt
    {
        return Receipt::create([
            'receipt_no' => $this->generateReceiptNo(),
            'sale_id' => $payment->sale_id,
            'payment_id' => $payment->id,
            'customer_id' => $payment->customer_id,
            'amount' => $payment->amount,
            'receipt_type' => 'installment',
            'date' => $payment->payment_date,
        ]);
    }

    protected function generateReceiptNo(): string
    {
        $next = (Receipt::max('id') ?? 0) + 1;

        return 'REC-' . str_pad((string) (1000 + $next), 4, '0', STR_PAD_LEFT);
    }
}

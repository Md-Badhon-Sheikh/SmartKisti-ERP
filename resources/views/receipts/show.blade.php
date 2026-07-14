<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Receipt') }} {{ $receipt->receipt_no }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            color: #181c32;
            background: #f4f4f4;
            margin: 0;
            padding: 30px;
        }
        .receipt-box {
            max-width: 620px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            padding: 32px;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #e4e6ef;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .receipt-header h2 {
            margin: 0 0 4px;
        }
        .receipt-header .receipt-no {
            font-size: 1.1rem;
            font-weight: bold;
            color: #009ef7;
        }
        .row-line {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #f4f4f4;
        }
        .row-line label {
            color: #7e8299;
        }
        .row-line span {
            font-weight: 600;
        }
        .amount-box {
            text-align: center;
            margin: 24px 0;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .amount-box .amount {
            font-size: 2rem;
            font-weight: 700;
            color: #50cd89;
        }
        .actions {
            text-align: center;
            margin-top: 24px;
        }
        .actions button, .actions a {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 6px;
            border: 1px solid #009ef7;
            background: #009ef7;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        .actions a.back {
            background: #fff;
            color: #181c32;
            border-color: #e4e6ef;
            margin-left: 8px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt-box { border: none; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-box">
        <div class="receipt-header">
            <h2>SmartKisti ERP</h2>
            <div class="text-muted">{{ __('Payment Receipt') }}</div>
            <div class="receipt-no">{{ $receipt->receipt_no }}</div>
        </div>

        <div class="row-line">
            <label>{{ __('Date') }}</label>
            <span>{{ $receipt->date->format('d M Y') }}</span>
        </div>
        <div class="row-line">
            <label>{{ __('Customer') }}</label>
            <span>{{ $receipt->customer->name }}</span>
        </div>
        <div class="row-line">
            <label>{{ __('Mobile') }}</label>
            <span>{{ $receipt->customer->mobile }}</span>
        </div>
        @if ($receipt->sale)
            <div class="row-line">
                <label>{{ __('Invoice No') }}</label>
                <span>{{ $receipt->sale->invoice_no }}</span>
            </div>
        @endif
        @if ($receipt->payment)
            <div class="row-line">
                <label>{{ __('Installment No') }}</label>
                <span>#{{ $receipt->payment->installment_no }}</span>
            </div>
            <div class="row-line">
                <label>{{ __('Payment Method') }}</label>
                <span>{{ $receipt->payment->paymentMethodName() }}</span>
            </div>
        @endif
        <div class="row-line">
            <label>{{ __('Receipt Type') }}</label>
            <span>
                {{ match($receipt->receipt_type) {
                    'installment' => __('Installment'),
                    'refund' => __('Refund'),
                    default => __('Cash'),
                } }}
            </span>
        </div>

        <div class="amount-box">
            <div class="text-muted">{{ __('Amount') }}</div>
            <div class="amount">৳ {{ number_format($receipt->amount, 2) }}</div>
        </div>

        <div class="actions">
            <button type="button" onclick="window.print()">{{ __('Print') }}</button>
            <a href="javascript:void(0)" onclick="window.close()" class="back">{{ __('Close') }}</a>
        </div>
    </div>
</body>
</html>

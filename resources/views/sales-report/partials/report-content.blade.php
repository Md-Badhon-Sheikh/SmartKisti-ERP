<div class="report-header">
    <h2>Installment ERP</h2>
    <div class="report-subtitle">{{ __('Sales Report') }}</div>
    <div class="report-meta">{{ __('Generated on') }}: {{ $generatedAt->format('d M Y, h:i A') }}</div>
</div>

@if (count($filters))
    <div class="report-filters">
        <strong>{{ __('Applied Filters') }}:</strong>
        {{ collect($filters)->map(fn ($value, $label) => $label.': '.$value)->implode(' | ') }}
    </div>
@endif

<table class="summary-table">
    <tr>
        <td class="summary-label">{{ __('Total Records') }}</td>
        <td class="summary-value">{{ $summary['total_records'] }}</td>
        <td class="summary-label">{{ __('Total Sales Amount') }}</td>
        <td class="summary-value">{{ number_format($summary['total_sales_amount'], 2) }}</td>
    </tr>
    <tr>
        <td class="summary-label">{{ __('Total Paid Amount') }}</td>
        <td class="summary-value">{{ number_format($summary['total_paid_amount'], 2) }}</td>
        <td class="summary-label">{{ __('Total Due Amount') }}</td>
        <td class="summary-value">{{ number_format($summary['total_due_amount'], 2) }}</td>
    </tr>
    <tr>
        <td class="summary-label">{{ __('Total Down Payment') }}</td>
        <td class="summary-value">{{ number_format($summary['total_down_payment'], 2) }}</td>
        <td class="summary-label"></td>
        <td class="summary-value"></td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th>{{ __('#') }}</th>
            <th>{{ __('Invoice No') }}</th>
            <th>{{ __('Sale Date') }}</th>
            <th>{{ __('Customer') }}</th>
            <th>{{ __('Customer Code') }}</th>
            <th>{{ __('Mobile') }}</th>
            <th>{{ __('Area') }}</th>
            <th>{{ __('Products') }}</th>
            <th>{{ __('Type') }}</th>
            <th>{{ __('Grand Total') }}</th>
            <th>{{ __('Paid') }}</th>
            <th>{{ __('Due') }}</th>
            <th>{{ __('Down Payment') }}</th>
            <th>{{ __('Status') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($sales as $index => $sale)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $sale->invoice_no }}</td>
                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                <td>{{ $sale->customer->name }}</td>
                <td>{{ $sale->customer->customer_code }}</td>
                <td>{{ $sale->customer->mobile }}</td>
                <td>{{ $sale->customer->area?->name ?? '—' }}</td>
                <td>{{ $sale->items->pluck('product.name')->filter()->implode(', ') }}</td>
                <td>{{ $sale->sale_type === 'installment' ? __('Installment') : __('Cash') }}</td>
                <td>{{ number_format($sale->grand_total, 2) }}</td>
                <td>{{ number_format($sale->paid_amount, 2) }}</td>
                <td>{{ number_format($sale->due_amount, 2) }}</td>
                <td>{{ $sale->installmentPlan ? number_format($sale->installmentPlan->down_payment, 2) : '—' }}</td>
                <td>{{ match ($sale->status) {
                    'completed' => __('Completed'),
                    'cancelled' => __('Cancelled'),
                    default => __('Pending'),
                } }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="14" class="text-center">{{ __('No records found.') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>

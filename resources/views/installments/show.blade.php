<x-app-layout :title="__('Installment Plan')">
    <div class="card my-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title m-0 d-flex align-items-center justify-content-between w-100">
                <h3 class="fw-bolder m-0">{{ $plan->sale->invoice_no }} — {{ $plan->customer->name }}</h3>
                <div>
                    <a href="javascript:void(0)" onclick="history.back()" class="btn btn-light-primary border btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}</a>
                </div>
            </div>
        </div>

        <div class="card-body border-top p-9">
            <div class="row mb-6">
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Product Total') }}</div>
                    <div class="fs-4 fw-bold">{{ number_format($plan->product_total, 2) }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Down Payment') }}</div>
                    <div class="fs-4 fw-bold">{{ number_format($plan->down_payment, 2) }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Total Due') }}</div>
                    <div class="fs-4 fw-bold">{{ number_format($plan->total_due, 2) }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Monthly Amount') }}</div>
                    <div class="fs-4 fw-bold">{{ number_format($plan->monthly_amount, 2) }} <span class="fs-7 text-muted">/ {{ $plan->installment_month }} {{ __('months') }}</span></div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Total Paid') }}</div>
                    <div class="fs-4 fw-bold text-success">{{ number_format($plan->total_paid, 2) }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Remaining Due') }}</div>
                    <div class="fs-4 fw-bold text-danger">{{ number_format($plan->remaining_due, 2) }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Next Payment Date') }}</div>
                    <div class="fs-4 fw-bold">{{ $plan->status === 'completed' ? '—' : $plan->next_payment_date?->format('d M Y') }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Status') }}</div>
                    <div>
                        <span class="badge fs-6 {{ match($plan->status) {
                            'completed' => 'badge-light-success',
                            'cancelled' => 'badge-light-danger',
                            default => 'badge-light-warning',
                        } }}">
                            {{ match($plan->status) {
                                'completed' => __('Completed'),
                                'cancelled' => __('Cancelled'),
                                default => __('Active'),
                            } }}
                        </span>
                    </div>
                </div>
            </div>

            @if ($plan->status !== 'completed' && $plan->status !== 'cancelled')
                @hasanyrole('super-admin|admin|manager')
                    <hr class="my-6">
                    <h5 class="fw-bold mb-4">{{ __('Record Payment') }}</h5>

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('installments.payments.store', $plan) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Payment Date') }}</label>
                                <input type="date" name="payment_date" class="form-control form-control-solid" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Amount') }}</label>
                                <input type="number" step="0.01" min="0.01" max="{{ $plan->remaining_due }}" name="amount" class="form-control form-control-solid" value="{{ old('amount', $plan->monthly_amount) }}" required>
                                @error('amount')
                                    <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Payment Method') }}</label>
                                <select name="payment_method" id="payment_method" class="form-select form-select-solid" required>
                                    @foreach ($paymentMethods as $method)
                                        <option value="{{ $method['code'] }}" @selected(old('payment_method') === $method['code'])>{{ app()->getLocale() === 'bn' ? $method['bn_name'] : $method['en_name'] }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                    <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-4">
                                <button type="submit" class="btn btn-primary w-100">{{ __('Save Payment') }}</button>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Remarks') }}</label>
                                <input type="text" name="remarks" class="form-control form-control-solid" value="{{ old('remarks') }}">
                            </div>
                        </div>
                    </form>
                @endhasanyrole
            @endif

            <hr class="my-6">
            <h5 class="fw-bold mb-4">{{ __('Installment Schedule') }}</h5>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr class="text-muted fs-7 text-uppercase">
                            <th>{{ __('Installment No') }}</th>
                            <th>{{ __('Due Date') }}</th>
                            <th class="text-end">{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Paid Date') }}</th>
                            <th>{{ __('Payment Method') }}</th>
                            <th>{{ __('Receipt No') }}</th>
                            <th>{{ __('Received By') }}</th>
                            <th>{{ __('Receipt') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedule as $row)
                            <tr>
                                <td>{{ $row['installment_no'] }}</td>
                                <td>{{ $row['due_date']->format('d M Y') }}</td>
                                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                                <td>
                                    @if ($row['paid'])
                                        <span class="badge badge-light-success">{{ __('Paid') }}</span>
                                    @else
                                        <span class="badge badge-light-warning">{{ __('Pending') }}</span>
                                    @endif
                                </td>
                                <td>{{ $row['payment']?->payment_date?->format('d M Y') ?? '—' }}</td>
                                <td>{{ $row['payment']?->paymentMethodName() ?? '—' }}</td>
                                <td>{{ $row['payment']?->receipt_no ?? '—' }}</td>
                                <td>{{ $row['payment']?->receivedBy?->name ?? '—' }}</td>
                                <td>
                                    @if ($row['receipt'])
                                        <a href="{{ route('receipts.show', $row['receipt']) }}" target="_blank" class="btn btn-sm btn-icon btn-light-primary" title="{{ __('View Receipt') }}">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

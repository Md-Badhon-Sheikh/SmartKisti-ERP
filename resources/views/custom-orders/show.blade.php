@php
    $statusLabels = [
        'pending' => __('Pending'),
        'cutting' => __('Cutting'),
        'making' => __('Making'),
        'polish' => __('Polish'),
        'ready' => __('Ready'),
        'delivered' => __('Delivered'),
    ];
    $orderStatusLabels = [
        'pending' => __('Pending'),
        'in_production' => __('In Production'),
        'ready' => __('Ready'),
        'delivered' => __('Delivered'),
        'cancelled' => __('Cancelled'),
    ];
    $orderStatusBadge = match($order->status) {
        'delivered' => 'badge-light-success',
        'ready' => 'badge-light-info',
        'in_production' => 'badge-light-warning',
        'cancelled' => 'badge-light-danger',
        default => 'badge-light-secondary',
    };
@endphp

<x-app-layout :title="__('Custom Order Details')">
    <div class="card my-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title m-0 d-flex align-items-center justify-content-between w-100">
                <h3 class="fw-bolder m-0">{{ $order->order_no }} — {{ $order->customer->name }}</h3>
                <div>
                    <a href="javascript:void(0)" onclick="history.back()" class="btn btn-light-primary border btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}</a>
                </div>
            </div>
        </div>

        <div class="card-body border-top p-9">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row mb-6">
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Order Date') }}</div>
                    <div class="fs-5 fw-bold">{{ $order->order_date->format('d M Y') }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Expected Delivery Date') }}</div>
                    <div class="fs-5 fw-bold">{{ $order->delivery_date?->format('d M Y') ?? '—' }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Order Type') }}</div>
                    <div class="fs-5 fw-bold">{{ $order->order_type === 'installment' ? __('Installment') : __('Cash') }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Status') }}</div>
                    <div><span class="badge fs-6 {{ $orderStatusBadge }}">{{ $orderStatusLabels[$order->status] }}</span></div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Estimated Price') }}</div>
                    <div class="fs-5 fw-bold">{{ number_format($order->estimated_price, 2) }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Advance Amount') }}</div>
                    <div class="fs-5 fw-bold">{{ number_format($order->advance_amount, 2) }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Remaining Amount') }}</div>
                    <div class="fs-5 fw-bold">{{ number_format($order->remaining_amount, 2) }}</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="text-muted fs-7">{{ __('Created By') }}</div>
                    <div class="fs-5 fw-bold">{{ $order->createdBy?->name ?? '—' }}</div>
                </div>
                @if ($order->remarks)
                    <div class="col-md-12 mb-4">
                        <div class="text-muted fs-7">{{ __('Remarks') }}</div>
                        <div>{{ $order->remarks }}</div>
                    </div>
                @endif
            </div>

            <hr class="my-6">
            <h5 class="fw-bold mb-4">{{ __('Custom Furniture Items') }}</h5>

            <div class="table-responsive mb-6">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr class="text-muted fs-7 text-uppercase">
                            <th>{{ __('Product Type') }}</th>
                            <th>{{ __('Wood Type') }}</th>
                            <th>{{ __('Size') }}</th>
                            <th>{{ __('Color') }}</th>
                            <th>{{ __('Glass Type') }}</th>
                            <th class="text-end">{{ __('Quantity') }}</th>
                            <th class="text-end">{{ __('Price') }}</th>
                            <th>{{ __('Design Image') }}</th>
                            <th>{{ __('Description') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->product_type }}</td>
                                <td>{{ $item->woodTypeName() ?? '—' }}</td>
                                <td>{{ $item->size ?? '—' }}</td>
                                <td>{{ $item->colorName() ?? '—' }}</td>
                                <td>{{ $item->glassTypeName() ?? '—' }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                <td>
                                    @if ($item->design_image)
                                        <a href="{{ asset($item->design_image) }}" target="_blank">
                                            <img src="{{ asset($item->design_image) }}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $item->description ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <hr class="my-6">
            <h5 class="fw-bold mb-4">{{ __('Production Tracking') }}</h5>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr class="text-muted fs-7 text-uppercase">
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Remarks') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order->productionStatuses as $stage)
                            <tr>
                                <td><span class="badge badge-light-primary">{{ $statusLabels[$stage->status] ?? $stage->status }}</span></td>
                                <td>{{ $stage->date->format('d M Y') }}</td>
                                <td>{{ $stage->remarks ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">{{ __('No production history yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (! in_array($order->status, ['delivered', 'cancelled']))
                @hasanyrole('super-admin|admin|manager')
                    <form action="{{ route('custom-orders.production-status.store', $order) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Status') }}</label>
                                <select name="status" id="production_status" class="form-select form-select-solid" required>
                                    @foreach ($statusLabels as $code => $label)
                                        <option value="{{ $code }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Date') }}</label>
                                <input type="date" name="date" class="form-control form-control-solid" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Remarks') }}</label>
                                <input type="text" name="remarks" class="form-control form-control-solid">
                            </div>
                            <div class="col-md-2 mb-4">
                                <button type="submit" class="btn btn-primary w-100">{{ __('Update Status') }}</button>
                            </div>
                        </div>
                    </form>
                @endhasanyrole
            @endif

            <hr class="my-6">
            <h5 class="fw-bold mb-4">{{ __('Deliveries') }}</h5>

            @if ($order->deliveries->isNotEmpty())
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr class="text-muted fs-7 text-uppercase">
                                <th>{{ __('Delivery Date') }}</th>
                                <th>{{ __('Receiver') }}</th>
                                <th>{{ __('Delivery Charge') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Delivered By') }}</th>
                                <th>{{ __('Photo') }}</th>
                                <th>{{ __('Signature') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->deliveries as $delivery)
                                <tr>
                                    <td>{{ $delivery->delivery_date->format('d M Y') }}</td>
                                    <td>{{ $delivery->receiver_name }} {{ $delivery->receiver_mobile ? '('.$delivery->receiver_mobile.')' : '' }}</td>
                                    <td>{{ number_format($delivery->delivery_charge, 2) }}</td>
                                    <td>
                                        <span class="badge {{ match($delivery->delivery_status) {
                                            'delivered' => 'badge-light-success',
                                            'failed' => 'badge-light-danger',
                                            default => 'badge-light-warning',
                                        } }}">
                                            {{ match($delivery->delivery_status) {
                                                'delivered' => __('Delivered'),
                                                'failed' => __('Failed'),
                                                default => __('Pending'),
                                            } }}
                                        </span>
                                    </td>
                                    <td>{{ $delivery->deliveryBy?->name ?? '—' }}</td>
                                    <td>
                                        @if ($delivery->photo)
                                            <a href="{{ asset($delivery->photo) }}" target="_blank"><i class="fas fa-image"></i></a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if ($delivery->signature)
                                            <a href="{{ asset($delivery->signature) }}" target="_blank"><i class="fas fa-signature"></i></a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (in_array($order->status, ['ready', 'in_production']))
                @hasanyrole('super-admin|admin|manager')
                    <form action="{{ route('custom-orders.deliveries.store', $order) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Delivery Date') }}</label>
                                <input type="date" name="delivery_date" class="form-control form-control-solid" value="{{ now()->format('Y-m-d') }}" required>
                                @error('delivery_date')
                                    <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Delivery Charge') }}</label>
                                <input type="number" step="0.01" min="0" name="delivery_charge" class="form-control form-control-solid" value="0">
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label required fw-bold fs-6">{{ __('Receiver Name') }}</label>
                                <input type="text" name="receiver_name" class="form-control form-control-solid" required>
                                @error('receiver_name')
                                    <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Receiver Mobile') }}</label>
                                <input type="text" name="receiver_mobile" class="form-control form-control-solid">
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Delivery Status') }}</label>
                                <select name="delivery_status" class="form-select form-select-solid" required>
                                    <option value="pending">{{ __('Pending') }}</option>
                                    <option value="delivered">{{ __('Delivered') }}</option>
                                    <option value="failed">{{ __('Failed') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Photo') }}</label>
                                <input type="file" name="photo" class="form-control form-control-solid" accept="image/*">
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="col-form-label fw-bold fs-6">{{ __('Signature') }}</label>
                                <input type="file" name="signature" class="form-control form-control-solid" accept="image/*">
                            </div>
                            <div class="col-md-3 mb-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">{{ __('Record Delivery') }}</button>
                            </div>
                        </div>
                    </form>
                @endhasanyrole
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        $(function () {
            $('#production_status').select2({ width: '100%' });
        });
    </script>
    @endpush
</x-app-layout>

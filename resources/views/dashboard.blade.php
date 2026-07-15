@php
    $trend = function (?float $change) {
        if (is_null($change)) {
            return ['cls' => 'secondary', 'icon' => 'minus', 'text' => __('New')];
        }
        return $change >= 0
            ? ['cls' => 'success', 'icon' => 'arrow-up', 'text' => number_format(abs($change), 1) . '%']
            : ['cls' => 'danger', 'icon' => 'arrow-down', 'text' => number_format(abs($change), 1) . '%'];
    };
@endphp
<x-app-layout :title="__('Dashboard')">

    <div class="container-fluid py-4">

        {{-- ============================================================
             QUICK ACTIONS
             ============================================================ --}}
        <div class="d-flex flex-wrap gap-2 mb-6">
            @hasanyrole('super-admin|admin|manager')
                <a href="{{ route('sales.create') }}" class="btn btn-sm btn-primary sk-quick-action">
                    <i class="fas fa-cart-plus me-1"></i>{{ __('New Sale') }}
                </a>
                <a href="{{ route('customers.create') }}" class="btn btn-sm btn-light-primary sk-quick-action">
                    <i class="fas fa-user-plus me-1"></i>{{ __('New Customer') }}
                </a>
            @endhasanyrole
            <a href="{{ route('installments.index') }}" class="btn btn-sm btn-light-primary sk-quick-action">
                <i class="fas fa-hand-holding-dollar me-1"></i>{{ __('Receive Installment') }}
            </a>
            @hasanyrole('super-admin|admin|manager')
                <a href="{{ route('custom-orders.create') }}" class="btn btn-sm btn-light-primary sk-quick-action">
                    <i class="fas fa-hammer me-1"></i>{{ __('Create Order') }}
                </a>
                <a href="{{ route('products.create') }}" class="btn btn-sm btn-light-primary sk-quick-action">
                    <i class="fas fa-box me-1"></i>{{ __('Add Product') }}
                </a>
                <a href="{{ route('sales-report.index') }}" class="btn btn-sm btn-light-primary sk-quick-action">
                    <i class="fas fa-print me-1"></i>{{ __('Print Reports') }}
                </a>
            @endhasanyrole
        </div>

        {{-- ============================================================
             KPI — SALES PERFORMANCE
             ============================================================ --}}
        <div class="d-flex align-items-baseline justify-content-between mb-3">
            <h3 class="fw-bolder mb-0">{{ __('Sales Performance') }}</h3>
            <span class="text-muted fs-7 fw-bold">{{ now()->format('d M Y') }}</span>
        </div>
        <div class="row g-4 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border h-100 sk-kpi-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="symbol symbol-45px">
                                <div class="symbol-label bg-light-primary text-primary"><i class="fas fa-cart-shopping fs-4"></i></div>
                            </div>
                            @php($t = $trend($kpis['today_sales']['change']))
                            <span class="badge badge-light-{{ $t['cls'] }}"><i class="fas fa-{{ $t['icon'] }} fs-9 me-1"></i>{{ $t['text'] }}</span>
                        </div>
                        <div class="sk-kpi-value">৳{{ number_format($kpis['today_sales']['value'], 2) }}</div>
                        <div class="text-muted fs-7 fw-bold">{{ __("Today's Sales") }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border h-100 sk-kpi-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="symbol symbol-45px">
                                <div class="symbol-label bg-light-info text-info"><i class="fas fa-hand-holding-dollar fs-4"></i></div>
                            </div>
                            @php($t = $trend($kpis['today_collection']['change']))
                            <span class="badge badge-light-{{ $t['cls'] }}"><i class="fas fa-{{ $t['icon'] }} fs-9 me-1"></i>{{ $t['text'] }}</span>
                        </div>
                        <div class="sk-kpi-value">৳{{ number_format($kpis['today_collection']['value'], 2) }}</div>
                        <div class="text-muted fs-7 fw-bold">{{ __("Today's Installment Collection") }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-primary bg-gradient text-white shadow-sm h-100 sk-kpi-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="symbol symbol-45px">
                                <div class="symbol-label" style="background-color:rgba(255,255,255,.2);"><i class="fas fa-cart-shopping fs-4 text-white"></i></div>
                            </div>
                            @php($t = $trend($kpis['month_sales']['change']))
                            <span class="badge" style="background-color:rgba(255,255,255,.22);color:#fff;"><i class="fas fa-{{ $t['icon'] }} fs-9 me-1"></i>{{ $t['text'] }}</span>
                        </div>
                        <div class="sk-kpi-value">৳{{ number_format($kpis['month_sales']['value'], 2) }}</div>
                        <div class="fs-7 fw-bold" style="opacity:.85">{{ __('Monthly Sales') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-info bg-gradient text-white shadow-sm h-100 sk-kpi-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="symbol symbol-45px">
                                <div class="symbol-label" style="background-color:rgba(255,255,255,.2);"><i class="fas fa-hand-holding-dollar fs-4 text-white"></i></div>
                            </div>
                            @php($t = $trend($kpis['month_collection']['change']))
                            <span class="badge" style="background-color:rgba(255,255,255,.22);color:#fff;"><i class="fas fa-{{ $t['icon'] }} fs-9 me-1"></i>{{ $t['text'] }}</span>
                        </div>
                        <div class="sk-kpi-value">৳{{ number_format($kpis['month_collection']['value'], 2) }}</div>
                        <div class="fs-7 fw-bold" style="opacity:.85">{{ __('Monthly Installment Collection') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             KPI — NEEDS ATTENTION
             ============================================================ --}}
        <h3 class="fw-bolder mb-3">{{ __('Needs Attention') }}</h3>
        <div class="row g-4 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border h-100 sk-kpi-card">
                    <div class="card-body">
                        <div class="symbol symbol-45px mb-4">
                            <div class="symbol-label bg-light-danger text-danger"><i class="fas fa-circle-exclamation fs-4"></i></div>
                        </div>
                        <div class="sk-kpi-value">৳{{ number_format($kpis['total_due']['value'], 2) }}</div>
                        <div class="text-muted fs-7 fw-bold">{{ __('Total Due') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border h-100 sk-kpi-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="symbol symbol-45px">
                                <div class="symbol-label bg-light-warning text-warning"><i class="fas fa-gears fs-4"></i></div>
                            </div>
                            <span class="badge badge-light-{{ $kpis['pending_orders']['new'] > 0 ? 'warning' : 'secondary' }}">
                                {{ $kpis['pending_orders']['new'] > 0 ? '+' . $kpis['pending_orders']['new'] . ' ' . __('this week') : __('No new') }}
                            </span>
                        </div>
                        <div class="sk-kpi-value">{{ number_format($kpis['pending_orders']['value']) }}</div>
                        <div class="text-muted fs-7 fw-bold">{{ __('Pending Orders') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border h-100 sk-kpi-card">
                    <div class="card-body">
                        <div class="symbol symbol-45px mb-4">
                            <div class="symbol-label bg-light-warning text-warning"><i class="fas fa-box-open fs-4"></i></div>
                        </div>
                        <div class="sk-kpi-value">{{ number_format($kpis['low_stock']['value']) }}</div>
                        <div class="text-muted fs-7 fw-bold">{{ __('Low Stock Products') }} <span class="text-muted">({{ __('below') }} {{ $kpis['low_stock']['threshold'] }})</span></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border h-100 sk-kpi-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="symbol symbol-45px">
                                <div class="symbol-label bg-light-primary text-primary"><i class="fas fa-users fs-4"></i></div>
                            </div>
                            <span class="badge badge-light-{{ $kpis['total_customers']['new'] > 0 ? 'success' : 'secondary' }}">
                                {{ $kpis['total_customers']['new'] > 0 ? '+' . $kpis['total_customers']['new'] . ' ' . __('new') : __('No new') }}
                            </span>
                        </div>
                        <div class="sk-kpi-value">{{ number_format($kpis['total_customers']['value']) }}</div>
                        <div class="text-muted fs-7 fw-bold">{{ __('Total Customers') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             ALERTS
             ============================================================ --}}
        <h3 class="fw-bolder mb-3">{{ __('Alerts') }}</h3>
        <div class="row g-3 mb-6">
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('installments.index') }}" class="sk-alert-pill bg-light-warning border-warning text-decoration-none d-block">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-white text-warning"><i class="fas fa-calendar-day fs-6"></i></span></span>
                    <span><span class="d-block fw-bolder fs-5 text-gray-800">{{ $alerts['due_today'] }}</span><span class="d-block fs-8 fw-bold text-muted">{{ __('Due Today') }}</span></span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('installments.index') }}" class="sk-alert-pill bg-light-danger border-danger text-decoration-none d-block">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-white text-danger"><i class="fas fa-triangle-exclamation fs-6"></i></span></span>
                    <span><span class="d-block fw-bolder fs-5 text-gray-800">{{ $alerts['overdue'] }}</span><span class="d-block fs-8 fw-bold text-muted">{{ __('Overdue Customers') }}</span></span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('products.index') }}" class="sk-alert-pill bg-light-warning border-warning text-decoration-none d-block">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-white text-warning"><i class="fas fa-box-open fs-6"></i></span></span>
                    <span><span class="d-block fw-bolder fs-5 text-gray-800">{{ $alerts['low_stock'] }}</span><span class="d-block fs-8 fw-bold text-muted">{{ __('Low Stock') }}</span></span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('deliveries.index') }}" class="sk-alert-pill bg-light-warning border-warning text-decoration-none d-block">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-white text-warning"><i class="fas fa-truck fs-6"></i></span></span>
                    <span><span class="d-block fw-bolder fs-5 text-gray-800">{{ $alerts['pending_deliveries'] }}</span><span class="d-block fs-8 fw-bold text-muted">{{ __('Pending Deliveries') }}</span></span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('custom-orders.index') }}" class="sk-alert-pill bg-light-secondary border-secondary text-decoration-none d-block">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-white text-secondary"><i class="fas fa-hammer fs-6"></i></span></span>
                    <span><span class="d-block fw-bolder fs-5 text-gray-800">{{ $alerts['pending_production'] }}</span><span class="d-block fs-8 fw-bold text-muted">{{ __('Pending Production') }}</span></span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('sms-logs.index') }}" class="sk-alert-pill bg-light-danger border-danger text-decoration-none d-block">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-white text-danger"><i class="fas fa-comment-slash fs-6"></i></span></span>
                    <span><span class="d-block fw-bolder fs-5 text-gray-800">{{ $alerts['sms_failed'] }}</span><span class="d-block fs-8 fw-bold text-muted">{{ __('SMS Failed') }}</span></span>
                </a>
            </div>
        </div>

        {{-- ============================================================
             CHARTS
             ============================================================ --}}
        <div class="row g-4 mb-6">
            <div class="col-xl-6">
                <div class="card shadow-sm border h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold">{{ __('Monthly Sales') }}</h3>
                    </div>
                    <div class="card-body pt-0">
                        <canvas id="chartSales" class="sk-chart-canvas"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card shadow-sm border h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold">{{ __('Monthly Installment Collection') }}</h3>
                    </div>
                    <div class="card-body pt-0">
                        <canvas id="chartCollection" class="sk-chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             CUSTOM ORDER STATUS
             ============================================================ --}}
        <h3 class="fw-bolder mb-3">{{ __('Custom Order Status') }}</h3>
        <div class="row g-4 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-7 fw-bold text-muted"><span class="badge badge-circle badge-secondary me-1">&nbsp;</span>{{ __('Pending') }}</span>
                            <span class="fs-8 text-muted">{{ $orderStatus['pending']['pct'] }}%</span>
                        </div>
                        <div class="fs-2 fw-bolder mb-2">{{ $orderStatus['pending']['count'] }}</div>
                        <div class="progress" style="height:6px"><div class="progress-bar bg-secondary" style="width:{{ $orderStatus['pending']['pct'] }}%"></div></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-7 fw-bold text-muted"><span class="badge badge-circle badge-warning me-1">&nbsp;</span>{{ __('In Production') }}</span>
                            <span class="fs-8 text-muted">{{ $orderStatus['in_production']['pct'] }}%</span>
                        </div>
                        <div class="fs-2 fw-bolder mb-2">{{ $orderStatus['in_production']['count'] }}</div>
                        <div class="progress" style="height:6px"><div class="progress-bar bg-warning" style="width:{{ $orderStatus['in_production']['pct'] }}%"></div></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-7 fw-bold text-muted"><span class="badge badge-circle badge-info me-1">&nbsp;</span>{{ __('Ready') }}</span>
                            <span class="fs-8 text-muted">{{ $orderStatus['ready']['pct'] }}%</span>
                        </div>
                        <div class="fs-2 fw-bolder mb-2">{{ $orderStatus['ready']['count'] }}</div>
                        <div class="progress" style="height:6px"><div class="progress-bar bg-info" style="width:{{ $orderStatus['ready']['pct'] }}%"></div></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-7 fw-bold text-muted"><span class="badge badge-circle badge-success me-1">&nbsp;</span>{{ __('Delivered') }}</span>
                            <span class="fs-8 text-muted">{{ __('this month') }}</span>
                        </div>
                        <div class="fs-2 fw-bolder mb-2">{{ $orderStatus['delivered']['count'] }}</div>
                        <div class="progress" style="height:6px"><div class="progress-bar bg-success" style="width:100%"></div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             RECENT ACTIVITY + FOLLOW-UP RAIL
             ============================================================ --}}
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card shadow-sm border">
                    <div class="card-header pt-3">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-sales" type="button">{{ __('Recent Sales') }}</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-collections" type="button">{{ __('Installment Collection') }}</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-orders" type="button">{{ __('Custom Orders') }}</button></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">

                            <div class="tab-pane fade show active" id="tab-sales">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-3">
                                        <thead><tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>{{ __('Invoice') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Type') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Status') }}</th><th>{{ __('Date') }}</th>
                                        </tr></thead>
                                        <tbody>
                                            @forelse ($recentSales as $sale)
                                                <tr>
                                                    <td class="fw-bold">{{ $sale->invoice_no }}</td>
                                                    <td>{{ $sale->customer->name }}</td>
                                                    <td><span class="badge badge-light-{{ $sale->sale_type === 'installment' ? 'warning' : 'success' }}">{{ $sale->sale_type === 'installment' ? __('Installment') : __('Cash') }}</span></td>
                                                    <td class="fw-bold">৳{{ number_format($sale->grand_total, 2) }}</td>
                                                    <td>
                                                        @php($cls = match($sale->status) { 'completed' => 'success', 'cancelled' => 'danger', default => 'warning' })
                                                        <span class="badge badge-light-{{ $cls }}">{{ __(ucfirst($sale->status)) }}</span>
                                                    </td>
                                                    <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center text-muted py-6">{{ __('No sales recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-collections">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-3">
                                        <thead><tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>{{ __('Receipt No') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Paid Amount') }}</th><th>{{ __('Remaining Due') }}</th><th>{{ __('Payment Date') }}</th>
                                        </tr></thead>
                                        <tbody>
                                            @forelse ($recentCollections as $payment)
                                                <tr>
                                                    <td class="fw-bold">{{ $payment->receipt_no }}</td>
                                                    <td>{{ $payment->customer->name }}</td>
                                                    <td class="fw-bold">৳{{ number_format($payment->amount, 2) }}</td>
                                                    <td>৳{{ number_format($payment->installmentPlan->remaining_due ?? 0, 2) }}</td>
                                                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center text-muted py-6">{{ __('No installment payments recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-orders">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-3">
                                        <thead><tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th>{{ __('Order No') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Product') }}</th><th>{{ __('Status') }}</th><th>{{ __('Delivery Date') }}</th>
                                        </tr></thead>
                                        <tbody>
                                            @forelse ($recentOrders as $order)
                                                @php($item = $order->items->first())
                                                <tr>
                                                    <td class="fw-bold">{{ $order->order_no }}</td>
                                                    <td>{{ $order->customer->name }}</td>
                                                    <td>{{ $item?->description ?: $item?->product_type ?: '—' }}</td>
                                                    <td>
                                                        @php($cls = match($order->status) { 'delivered' => 'success', 'ready' => 'info', 'in_production' => 'warning', 'cancelled' => 'danger', default => 'secondary' })
                                                        @php($lbl = match($order->status) { 'delivered' => __('Delivered'), 'ready' => __('Ready'), 'in_production' => __('In Production'), 'cancelled' => __('Cancelled'), default => __('Pending') })
                                                        <span class="badge badge-light-{{ $cls }}">{{ $lbl }}</span>
                                                    </td>
                                                    <td>{{ $order->delivery_date?->format('d M Y') ?? '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center text-muted py-6">{{ __('No custom orders recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================
                 RIGHT RAIL — TODAY'S FOLLOW-UP
                 ======================================================== --}}
            <div class="col-xl-4">
                <div class="card shadow-sm border">
                    <div class="card-header">
                        <h3 class="card-title fw-bold">{{ __("Today's Follow-up") }}</h3>
                    </div>
                    <div class="card-body">

                        <div class="sk-rail-group-title text-warning">{{ __('Due Today') }} ({{ count($followUp['dueTodayPlans']) }})</div>
                        @forelse ($followUp['dueTodayPlans'] as $plan)
                            <div class="sk-rail-item">
                                <span class="symbol symbol-32px"><span class="symbol-label bg-light-primary text-primary fw-bold fs-8">{{ mb_substr($plan->customer->name, 0, 1) }}</span></span>
                                <span class="sk-rail-text"><span class="name">{{ $plan->customer->name }}</span><span class="meta d-block">{{ __('Installment') }} #{{ $plan->payments_count + 1 }}</span></span>
                                <span class="fw-bolder fs-7 text-warning">৳{{ number_format($plan->monthly_amount, 2) }}</span>
                            </div>
                        @empty
                            <div class="text-muted fs-8 mb-3">{{ __('Nothing due today.') }}</div>
                        @endforelse

                        <div class="sk-rail-group-title text-danger">{{ __('Overdue') }} ({{ count($followUp['overduePlans']) }})</div>
                        @forelse ($followUp['overduePlans'] as $plan)
                            <div class="sk-rail-item">
                                <span class="symbol symbol-32px"><span class="symbol-label bg-light-danger text-danger fw-bold fs-8">{{ mb_substr($plan->customer->name, 0, 1) }}</span></span>
                                <span class="sk-rail-text"><span class="name">{{ $plan->customer->name }}</span><span class="meta d-block">{{ $plan->next_payment_date->diffInDays(now()) }} {{ __('days overdue') }}</span></span>
                                <span class="fw-bolder fs-7 text-danger">৳{{ number_format($plan->monthly_amount, 2) }}</span>
                            </div>
                        @empty
                            <div class="text-muted fs-8 mb-3">{{ __('No overdue installments.') }}</div>
                        @endforelse

                        <div class="sk-rail-group-title text-primary">{{ __("Today's Deliveries") }} ({{ count($followUp['todaysDeliveries']) }})</div>
                        @forelse ($followUp['todaysDeliveries'] as $delivery)
                            @php($cust = $delivery->sale?->customer ?? $delivery->customOrder?->customer)
                            <div class="sk-rail-item">
                                <span class="symbol symbol-32px"><span class="symbol-label bg-light-primary text-primary fw-bold fs-8">{{ mb_substr($delivery->receiver_name, 0, 1) }}</span></span>
                                <span class="sk-rail-text"><span class="name">{{ $delivery->receiver_name }}</span><span class="meta d-block">{{ $cust?->name ?? '—' }}</span></span>
                            </div>
                        @empty
                            <div class="text-muted fs-8 mb-3">{{ __('No deliveries scheduled today.') }}</div>
                        @endforelse

                        <div class="sk-rail-group-title text-warning">{{ __('Production Delays') }} ({{ count($followUp['productionDelays']) }})</div>
                        @forelse ($followUp['productionDelays'] as $order)
                            <div class="sk-rail-item">
                                <span class="symbol symbol-32px"><span class="symbol-label bg-light-warning text-warning fw-bold fs-8">{{ mb_substr($order->customer->name, 0, 1) }}</span></span>
                                <span class="sk-rail-text"><span class="name">{{ $order->customer->name }}</span><span class="meta d-block">{{ $order->order_no }} · {{ $order->delivery_date->diffInDays(now()) }} {{ __('days behind') }}</span></span>
                            </div>
                        @empty
                            <div class="text-muted fs-8 mb-3">{{ __('No production delays.') }}</div>
                        @endforelse

                        <div class="sk-rail-group-title text-danger">{{ __('Pending SMS') }} ({{ count($followUp['pendingSms']) }})</div>
                        @forelse ($followUp['pendingSms'] as $log)
                            <div class="sk-rail-item">
                                <span class="symbol symbol-32px"><span class="symbol-label bg-light-danger text-danger fw-bold fs-8">{{ $log->customer ? mb_substr($log->customer->name, 0, 1) : '?' }}</span></span>
                                <span class="sk-rail-text"><span class="name">{{ $log->customer?->name ?? $log->mobile }}</span><span class="meta d-block">{{ __(ucfirst($log->sms_type)) }} · {{ __('retry needed') }}</span></span>
                            </div>
                        @empty
                            <div class="text-muted fs-8">{{ __('No failed SMS.') }}</div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        $(document).ready(function () {
            const labels = @json($charts['labels']);
            const salesData = @json($charts['sales']);
            const collectionData = @json($charts['collection']);

            new Chart(document.getElementById('chartSales'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "{{ __('Sales') }}",
                        data: salesData,
                        backgroundColor: '#009ef7',
                        borderRadius: 6,
                        maxBarThickness: 42,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { callback: v => '৳' + v.toLocaleString() } } }
                }
            });

            new Chart(document.getElementById('chartCollection'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "{{ __('Collection') }}",
                        data: collectionData,
                        borderColor: '#7239ea',
                        backgroundColor: 'rgba(114,57,234,.12)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#7239ea',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { callback: v => '৳' + v.toLocaleString() } } }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

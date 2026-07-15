<?php

namespace App\Http\Controllers;

use App\Models\CustomOrder;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\Product;
use App\Models\ProductionStatus;
use App\Models\Sale;
use App\Models\SmsLog;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    protected const LOW_STOCK_THRESHOLD = 5;

    public function Index(): View
    {
        $today = Carbon::today();

        return view('dashboard', [
            'kpis' => $this->buildKpis($today),
            'orderStatus' => $this->buildOrderStatus(),
            'alerts' => $this->buildAlerts($today),
            'charts' => $this->buildCharts(),
            'recentSales' => Sale::with('customer')->latest('sale_date')->latest('id')->take(5)->get(),
            'recentCollections' => InstallmentPayment::with(['customer', 'installmentPlan'])->latest('payment_date')->latest('id')->take(5)->get(),
            'recentOrders' => CustomOrder::with(['customer', 'items'])->latest('order_date')->latest('id')->take(5)->get(),
            'followUp' => $this->buildFollowUp($today),
        ]);
    }

    /**
     * Sales/collection totals for today & this month, each with a same-period comparison.
     */
    protected function buildKpis(Carbon $today): array
    {
        $yesterday = $today->copy()->subDay();
        $monthStart = $today->copy()->startOfMonth();
        $prevMonthStart = $monthStart->copy()->subMonthNoOverflow();
        $prevMonthComparableEnd = $prevMonthStart->copy()->addDays(
            min($today->day, $prevMonthStart->copy()->endOfMonth()->day) - 1
        );

        $todaySales = (float) Sale::whereDate('sale_date', $today)->where('status', '!=', 'cancelled')->sum('grand_total');
        $yesterdaySales = (float) Sale::whereDate('sale_date', $yesterday)->where('status', '!=', 'cancelled')->sum('grand_total');

        $todayCollection = (float) InstallmentPayment::whereDate('payment_date', $today)->sum('amount');
        $yesterdayCollection = (float) InstallmentPayment::whereDate('payment_date', $yesterday)->sum('amount');

        $monthSales = (float) Sale::whereBetween('sale_date', [$monthStart, $today])->where('status', '!=', 'cancelled')->sum('grand_total');
        $prevMonthSales = (float) Sale::whereBetween('sale_date', [$prevMonthStart, $prevMonthComparableEnd])->where('status', '!=', 'cancelled')->sum('grand_total');

        $monthCollection = (float) InstallmentPayment::whereBetween('payment_date', [$monthStart, $today])->sum('amount');
        $prevMonthCollection = (float) InstallmentPayment::whereBetween('payment_date', [$prevMonthStart, $prevMonthComparableEnd])->sum('amount');

        $totalDue = (float) Sale::where('status', '!=', 'cancelled')->sum('due_amount');

        $pendingOrders = CustomOrder::whereIn('status', ['pending', 'in_production'])->count();
        $newOrdersThisWeek = CustomOrder::whereIn('status', ['pending', 'in_production'])
            ->where('created_at', '>=', $today->copy()->startOfWeek())
            ->count();

        $lowStock = Product::where('status', true)->where('stock', '<', self::LOW_STOCK_THRESHOLD)->count();

        $totalCustomers = Customer::count();
        $newCustomersThisMonth = Customer::where('created_at', '>=', $monthStart)->count();

        return [
            'today_sales' => ['value' => $todaySales, 'change' => $this->percentChange($todaySales, $yesterdaySales)],
            'today_collection' => ['value' => $todayCollection, 'change' => $this->percentChange($todayCollection, $yesterdayCollection)],
            'month_sales' => ['value' => $monthSales, 'change' => $this->percentChange($monthSales, $prevMonthSales)],
            'month_collection' => ['value' => $monthCollection, 'change' => $this->percentChange($monthCollection, $prevMonthCollection)],
            'total_due' => ['value' => $totalDue],
            'pending_orders' => ['value' => $pendingOrders, 'new' => $newOrdersThisWeek],
            'low_stock' => ['value' => $lowStock, 'threshold' => self::LOW_STOCK_THRESHOLD],
            'total_customers' => ['value' => $totalCustomers, 'new' => $newCustomersThisMonth],
        ];
    }

    protected function percentChange(float $current, float $previous): ?float
    {
        if ($previous <= 0.0) {
            return null;
        }

        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Custom order pipeline counts, matching CustomOrderController's status badge scheme.
     */
    protected function buildOrderStatus(): array
    {
        $total = max(CustomOrder::where('status', '!=', 'cancelled')->count(), 1);

        $pending = CustomOrder::where('status', 'pending')->count();
        $inProduction = CustomOrder::where('status', 'in_production')->count();
        $ready = CustomOrder::where('status', 'ready')->count();

        $deliveredThisMonth = ProductionStatus::where('status', 'delivered')
            ->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->toDateString()])
            ->distinct('custom_order_id')
            ->count('custom_order_id');

        return [
            'pending' => ['count' => $pending, 'pct' => (int) round($pending / $total * 100)],
            'in_production' => ['count' => $inProduction, 'pct' => (int) round($inProduction / $total * 100)],
            'ready' => ['count' => $ready, 'pct' => (int) round($ready / $total * 100)],
            'delivered' => ['count' => $deliveredThisMonth],
        ];
    }

    protected function buildAlerts(Carbon $today): array
    {
        return [
            'due_today' => InstallmentPlan::where('status', 'active')->whereDate('next_payment_date', $today)->count(),
            'overdue' => InstallmentPlan::where('status', 'active')->whereDate('next_payment_date', '<', $today)->count(),
            'low_stock' => Product::where('status', true)->where('stock', '<', self::LOW_STOCK_THRESHOLD)->count(),
            'pending_deliveries' => Delivery::where('delivery_status', 'pending')->count(),
            'pending_production' => CustomOrder::where('status', 'pending')->count(),
            'sms_failed' => SmsLog::where('status', 'failed')->count(),
        ];
    }

    /**
     * Last 6 months of Sales and Installment Collection, month-aligned for the two dashboard charts.
     */
    protected function buildCharts(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->copy()->subMonthsNoOverflow($i)->startOfMonth());
        $rangeStart = $months->first();

        $salesByMonth = Sale::where('status', '!=', 'cancelled')
            ->where('sale_date', '>=', $rangeStart)
            ->selectRaw("DATE_FORMAT(sale_date, '%Y-%m') as ym, SUM(grand_total) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $collectionByMonth = InstallmentPayment::where('payment_date', '>=', $rangeStart)
            ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $salesData = [];
        $collectionData = [];

        foreach ($months as $month) {
            $key = $month->format('Y-m');
            $labels[] = $month->format('M');
            $salesData[] = (float) ($salesByMonth[$key] ?? 0);
            $collectionData[] = (float) ($collectionByMonth[$key] ?? 0);
        }

        return ['labels' => $labels, 'sales' => $salesData, 'collection' => $collectionData];
    }

    protected function buildFollowUp(Carbon $today): array
    {
        $dueTodayPlans = InstallmentPlan::with('customer')
            ->withCount('payments')
            ->where('status', 'active')
            ->whereDate('next_payment_date', $today)
            ->orderBy('next_payment_date')
            ->take(5)
            ->get();

        $overduePlans = InstallmentPlan::with('customer')
            ->where('status', 'active')
            ->whereDate('next_payment_date', '<', $today)
            ->orderBy('next_payment_date')
            ->take(5)
            ->get();

        $todaysDeliveries = Delivery::with(['sale.customer', 'customOrder.customer'])
            ->where('delivery_status', 'pending')
            ->whereDate('delivery_date', $today)
            ->take(5)
            ->get();

        $productionDelays = CustomOrder::with('customer')
            ->where('status', 'in_production')
            ->whereNotNull('delivery_date')
            ->whereDate('delivery_date', '<', $today)
            ->take(5)
            ->get();

        $pendingSms = SmsLog::with('customer')
            ->where('status', 'failed')
            ->latest()
            ->take(5)
            ->get();

        return compact('dueTodayPlans', 'overduePlans', 'todaysDeliveries', 'productionDelays', 'pendingSms');
    }
}

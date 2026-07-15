<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Category;
use App\Models\InstallmentPlan;
use App\Models\Sale;
use App\Models\SubCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;

class SalesReportController extends Controller
{
    /**
     * Display the report page (filters + datatable).
     */
    public function Index(): View
    {
        return view('sales-report.index', [
            'categories' => Category::where('status', true)->orderBy('name')->get(),
            'subCategories' => SubCategory::where('status', true)->orderBy('name')->get(),
            'areas' => Area::where('status', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Return DataTable JSON for the filtered listing, plus summary totals.
     */
    public function Datatable(Request $request): JsonResponse
    {
        $query = $this->filteredQuery($request)
            ->with(['customer.area', 'items.product', 'installmentPlan'])
            ->select('sales.*');

        return DataTables::eloquent($query)
            ->addColumn('customer_name', fn (Sale $sale) => $sale->customer->name)
            ->addColumn('customer_code', fn (Sale $sale) => $sale->customer->customer_code)
            ->addColumn('mobile', fn (Sale $sale) => $sale->customer->mobile)
            ->addColumn('area_name', fn (Sale $sale) => $sale->customer->area?->name ?? '—')
            ->editColumn('sale_date', fn (Sale $sale) => $sale->sale_date->format('d M Y'))
            ->addColumn('products', fn (Sale $sale) => $sale->items->pluck('product.name')->filter()->implode(', '))
            ->addColumn('sale_type', fn (Sale $sale) => '<span class="badge badge-light-'.($sale->sale_type === 'installment' ? 'warning' : 'success').'">'
                .($sale->sale_type === 'installment' ? __('Installment') : __('Cash')).'</span>')
            ->editColumn('grand_total', fn (Sale $sale) => number_format($sale->grand_total, 2))
            ->editColumn('paid_amount', fn (Sale $sale) => number_format($sale->paid_amount, 2))
            ->editColumn('due_amount', fn (Sale $sale) => number_format($sale->due_amount, 2))
            ->addColumn('down_payment', fn (Sale $sale) => $sale->installmentPlan
                ? number_format($sale->installmentPlan->down_payment, 2)
                : '—')
            ->addColumn('status', function (Sale $sale) {
                $badgeClass = match ($sale->status) {
                    'completed' => 'badge-light-success',
                    'cancelled' => 'badge-light-danger',
                    default => 'badge-light-warning',
                };
                $label = match ($sale->status) {
                    'completed' => __('Completed'),
                    'cancelled' => __('Cancelled'),
                    default => __('Pending'),
                };

                return '<span class="badge '.$badgeClass.'">'.$label.'</span>';
            })
            ->rawColumns(['sale_type', 'status'])
            ->with('summary', $this->summary($request))
            ->make(true);
    }

    /**
     * Export the filtered report to an Excel-readable (.xls) file.
     */
    public function Export(Request $request): Response
    {
        $html = view('sales-report.export', $this->reportData($request))->render();

        $filename = 'sales-report-'.now()->format('Y-m-d-His').'.xls';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Display a standalone, printable version of the filtered report.
     */
    public function Print(Request $request): View
    {
        return view('sales-report.print', $this->reportData($request));
    }

    /**
     * Build the base filtered Sale query from request filters.
     * Shared by Datatable(), summary(), and reportData() so every entry point
     * (table, export, print) stays in sync.
     */
    protected function filteredQuery(Request $request): Builder
    {
        $search = trim((string) $request->input('search'));

        return Sale::query()
            ->when($request->filled('category_id'), fn (Builder $q) => $q->whereHas(
                'items.product',
                fn (Builder $q2) => $q2->where('category_id', $request->category_id)
            ))
            ->when($request->filled('sub_category_id'), fn (Builder $q) => $q->whereHas(
                'items.product',
                fn (Builder $q2) => $q2->where('sub_category_id', $request->sub_category_id)
            ))
            ->when($request->filled('area_id'), fn (Builder $q) => $q->whereHas(
                'customer',
                fn (Builder $q2) => $q2->where('area_id', $request->area_id)
            ))
            ->when($request->filled('sale_type'), fn (Builder $q) => $q->where('sale_type', $request->sale_type))
            ->when($request->filled('installment_status'), function (Builder $q) use ($request) {
                $q->where('sale_type', 'installment');

                $request->installment_status === 'paid'
                    ? $q->where('due_amount', '<=', 0)
                    : $q->where('due_amount', '>', 0);
            })
            ->when($request->filled('start_date'), fn (Builder $q) => $q->whereDate('sale_date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn (Builder $q) => $q->whereDate('sale_date', '<=', $request->end_date))
            ->when($search !== '', function (Builder $q) use ($search) {
                $q->where(function (Builder $q2) use ($search) {
                    $q2->where('invoice_no', 'like', "%{$search}%")
                        ->orWhereHas('customer', function (Builder $q3) use ($search) {
                            $q3->where('name', 'like', "%{$search}%")
                                ->orWhere('mobile', 'like', "%{$search}%")
                                ->orWhere('customer_code', 'like', "%{$search}%")
                                ->orWhereHas('area', fn (Builder $q4) => $q4->where('name', 'like', "%{$search}%"));
                        })
                        ->orWhereHas('items.product', fn (Builder $q3) => $q3->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('receipts', fn (Builder $q3) => $q3->where('receipt_no', 'like', "%{$search}%"));
                });
            });
    }

    /**
     * Aggregate totals for the current filter set (independent of pagination).
     */
    protected function summary(Request $request): array
    {
        $saleIds = $this->filteredQuery($request)->pluck('id');

        return [
            'total_records' => $saleIds->count(),
            'total_sales_amount' => (float) $this->filteredQuery($request)->sum('grand_total'),
            'total_paid_amount' => (float) $this->filteredQuery($request)->sum('paid_amount'),
            'total_due_amount' => (float) $this->filteredQuery($request)->sum('due_amount'),
            'total_down_payment' => (float) InstallmentPlan::whereIn('sale_id', $saleIds)->sum('down_payment'),
        ];
    }

    /**
     * Human-readable labels of the filters actually applied (for print/export headers).
     */
    protected function filterLabels(Request $request): array
    {
        $labels = [];

        if ($request->filled('category_id')) {
            $labels[__('Category')] = Category::find($request->category_id)?->name;
        }

        if ($request->filled('sub_category_id')) {
            $labels[__('Sub Category')] = SubCategory::find($request->sub_category_id)?->name;
        }

        if ($request->filled('area_id')) {
            $labels[__('Area')] = Area::find($request->area_id)?->name;
        }

        if ($request->filled('sale_type')) {
            $labels[__('Sale Type')] = $request->sale_type === 'installment' ? __('Installment') : __('Cash');
        }

        if ($request->filled('installment_status')) {
            $labels[__('Installment Status')] = $request->installment_status === 'paid' ? __('Paid') : __('Due');
        }

        if ($request->filled('start_date')) {
            $labels[__('Start Date')] = $request->start_date;
        }

        if ($request->filled('end_date')) {
            $labels[__('End Date')] = $request->end_date;
        }

        if ($request->filled('search')) {
            $labels[__('Search')] = $request->search;
        }

        return $labels;
    }

    /**
     * Full (non-paginated) filtered dataset + summary + filter labels, for export/print.
     */
    protected function reportData(Request $request): array
    {
        return [
            'sales' => $this->filteredQuery($request)
                ->with(['customer.area', 'items.product', 'installmentPlan'])
                ->orderBy('sale_date')
                ->orderBy('id')
                ->get(),
            'summary' => $this->summary($request),
            'filters' => $this->filterLabels($request),
            'generatedAt' => now(),
        ];
    }
}

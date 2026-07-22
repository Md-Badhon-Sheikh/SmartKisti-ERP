<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InstallmentPlan;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\ReceiptService;
use App\Services\Sms\SmsLogService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{
    public function __construct(
        private readonly SmsLogService $smsLog,
        private readonly ReceiptService $receipts,
    ) {}

    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('sales.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(Request $request): JsonResponse
    {
        $query = Sale::with('customer')->select('sales.*');

        return DataTables::eloquent($query)
            ->addColumn('customer_name', fn (Sale $sale) => $sale->customer->name)
            ->editColumn('sale_date', fn (Sale $sale) => $sale->sale_date->format('d M Y'))
            ->addColumn('sale_type', fn (Sale $sale) => '<span class="badge badge-light-'.($sale->sale_type === 'installment' ? 'warning' : 'success').'">'
                .($sale->sale_type === 'installment' ? __('Installment') : __('Cash')).'</span>')
            ->editColumn('grand_total', fn (Sale $sale) => number_format($sale->grand_total, 2))
            ->editColumn('paid_amount', fn (Sale $sale) => number_format($sale->paid_amount, 2))
            ->editColumn('due_amount', fn (Sale $sale) => number_format($sale->due_amount, 2))
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
            ->addColumn('action', function (Sale $sale) use ($request) {
                $html = '<div class="d-flex justify-content-end gap-2">'
                    . '<button class="btn btn-light-info btn-view px-4 py-2" data-id="' . $sale->id . '">'
                    . '<i class="fas fa-eye"></i>'
                    . '</button>';

                if ($request->user()->hasAnyRole(['super-admin', 'admin', 'manager'])) {
                    $html .= '<a href="' . route('sales.edit', $sale->id) . '" class="btn btn-sm btn-light-primary px-4 py-2">'
                        . '<i class="fas fa-edit"></i>'
                        . '</a>';

                    $html .= '<button class="btn btn-sm btn-light-danger btn-delete px-4 py-2" data-id="' . $sale->id . '">'
                        . '<i class="fas fa-trash"></i>'
                        . '</button>';
                }

                $html .= '</div>';

                return $html;
            })
            ->rawColumns(['sale_type', 'status', 'action'])
            ->make(true);
    }

    /**
     * Display the create page.
     */
    public function Create(): View
    {
        return view('sales.create', $this->formData());
    }

    /**
     * Store a newly created Sale.
     */
    public function Store(Request $request): RedirectResponse
    {
        $validated = $this->validateSale($request);

        $sale = DB::transaction(function () use ($request, $validated) {
            return $this->persistSale($request, $validated);
        });

        $this->receipts->forSale($sale);
        $this->sendInvoiceSms($sale->load('customer'));

        return redirect()->route('sales.index')->with('status', __('Sale created successfully.'));
    }

    /**
     * Display the edit page.
     */
    public function Edit(Sale $sale): View|RedirectResponse
    {
        if ($this->hasRecordedPayments($sale)) {
            return redirect()->route('sales.index')
                ->with('error', __('This sale has recorded installment payments and cannot be edited.'));
        }

        $sale->load('items');

        return view('sales.edit', array_merge(['sale' => $sale], $this->formData()));
    }

    /**
     * Show a single Sale (used by the view modal's AJAX fetch).
     */
    public function Show(int $id): JsonResponse
    {
        $sale = Sale::with(['customer', 'items.product', 'installmentPlan', 'createdBy', 'receipts'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'customer_name' => $sale->customer->name,
                'customer_mobile' => $sale->customer->mobile,
                'sale_type' => $sale->sale_type,
                'sale_date' => $sale->sale_date->format('d M Y'),
                'items' => $sale->items->map(fn (SaleItem $item) => [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'total' => $item->total,
                ]),
                'subtotal' => $sale->subtotal,
                'discount' => $sale->discount,
                'vat' => $sale->vat,
                'delivery_charge' => $sale->delivery_charge,
                'grand_total' => $sale->grand_total,
                'paid_amount' => $sale->paid_amount,
                'due_amount' => $sale->due_amount,
                'status' => $sale->status,
                'installment_plan_id' => $sale->installmentPlan?->id,
                'receipt_id' => $sale->receipts->first()?->id,
                'created_by' => $sale->createdBy?->name,
                'created_at' => $sale->created_at?->format('d M Y, h:i A'),
            ],
        ]);
    }

    /**
     * Update an existing Sale.
     */
    public function Update(Request $request, Sale $sale): RedirectResponse
    {
        if ($this->hasRecordedPayments($sale)) {
            return redirect()->route('sales.index')
                ->with('error', __('This sale has recorded installment payments and cannot be edited.'));
        }

        $validated = $this->validateSale($request, $sale);

        DB::transaction(function () use ($request, $validated, $sale) {
            $sale->items()->delete();
            $sale->installmentPlan()->delete();

            $this->persistSale($request, $validated, $sale);
        });

        return redirect()->route('sales.index')->with('status', __('Sale updated successfully.'));
    }

    /**
     * Delete a Sale. (POST only)
     */
    public function Delete(int $id): JsonResponse
    {
        $sale = Sale::with('installmentPlan')->findOrFail($id);

        if ($this->hasRecordedPayments($sale)) {
            return response()->json([
                'success' => false,
                'message' => 'This sale has recorded installment payments and cannot be deleted.',
            ], 422);
        }

        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sale deleted successfully.',
        ]);
    }

    protected function hasRecordedPayments(Sale $sale): bool
    {
        return $sale->installmentPlan()->whereHas('payments')->exists();
    }

    /**
     * Send (and log) the invoice-generated SMS for a newly created Sale.
     */
    protected function sendInvoiceSms(Sale $sale): void
    {
        if (! $sale->customer->mobile) {
            return;
        }

        $message = sprintf(
            'প্রিয় %s, আপনার ইনভয়েস %s তৈরি হয়েছে। সর্বমোট: ৳%s, পরিশোধিত: ৳%s, বাকি: ৳%s। ধন্যবাদ - Installment ERP',
            $sale->customer->name,
            $sale->invoice_no,
            number_format($sale->grand_total, 2),
            number_format($sale->paid_amount, 2),
            number_format($sale->due_amount, 2),
        );

        $this->smsLog->send($sale->customer->mobile, $message, 'sale', [
            'customer_id' => $sale->customer_id,
            'sale_id' => $sale->id,
        ]);
    }

    /**
     * Persist a Sale + its items + (optionally) its InstallmentPlan.
     * Shared by Store() and Update().
     */
    protected function persistSale(Request $request, array $validated, ?Sale $sale = null): Sale
    {
        $subtotal = 0;

        foreach ($validated['items'] as $item) {
            $subtotal += ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);
        }

        $discount = $validated['discount'] ?? 0;
        $vat = $validated['vat'] ?? 0;
        $deliveryCharge = $validated['delivery_charge'] ?? 0;
        $grandTotal = $subtotal - $discount + $vat + $deliveryCharge;
        $paidAmount = $validated['paid_amount'] ?? 0;
        $dueAmount = max($grandTotal - $paidAmount, 0);
        $status = $validated['status'] ?? ($dueAmount <= 0 ? 'completed' : 'pending');

        $attributes = [
            'customer_id' => $validated['customer_id'],
            'sale_type' => $validated['sale_type'],
            'sale_date' => $validated['sale_date'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'vat' => $vat,
            'delivery_charge' => $deliveryCharge,
            'grand_total' => $grandTotal,
            'paid_amount' => $paidAmount,
            'due_amount' => $dueAmount,
            'status' => $status,
            'updated_by' => $request->user()->id,
        ];

        if ($sale) {
            $sale->update($attributes);
        } else {
            $attributes['invoice_no'] = $this->generateInvoiceNo();
            $attributes['created_by'] = $request->user()->id;
            $sale = Sale::create($attributes);
        }

        foreach ($validated['items'] as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'total' => ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0),
            ]);
        }

        if ($validated['sale_type'] === 'installment') {
            $this->createInstallmentPlan($sale, $grandTotal, $paidAmount, (int) $validated['installment_month'], $validated['sale_date']);
        }

        return $sale;
    }

    protected function createInstallmentPlan(Sale $sale, float $grandTotal, float $downPayment, int $installmentMonth, string $startDate): void
    {
        $totalDue = max($grandTotal - $downPayment, 0);
        $monthlyAmount = $installmentMonth > 0 ? round($totalDue / $installmentMonth, 2) : $totalDue;

        InstallmentPlan::create([
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'product_total' => $grandTotal,
            'down_payment' => $downPayment,
            'total_due' => $totalDue,
            'installment_month' => $installmentMonth,
            'monthly_amount' => $monthlyAmount,
            'start_date' => $startDate,
            'next_payment_date' => Carbon::parse($startDate)->addMonthNoOverflow(),
            'last_payment_date' => null,
            'total_paid' => 0,
            'remaining_due' => $totalDue,
            'status' => $totalDue <= 0 ? 'completed' : 'active',
        ]);
    }

    protected function generateInvoiceNo(): string
    {
        $next = (Sale::max('id') ?? 0) + 1;

        return 'INV-' . str_pad((string) (1000 + $next), 4, '0', STR_PAD_LEFT);
    }

    /**
     * @return array{customers: \Illuminate\Support\Collection, products: \Illuminate\Support\Collection}
     */
    protected function formData(): array
    {
        return [
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
            'products' => Product::where('status', true)->orderBy('name')->get(['id', 'name', 'selling_price']),
        ];
    }

    protected function validateSale(Request $request, ?Sale $sale = null): array
    {
        $rules = [
            'customer_id' => ['required', 'exists:customers,id'],
            'sale_type' => ['required', Rule::in(['cash', 'installment'])],
            'sale_date' => ['required', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'vat' => ['nullable', 'numeric', 'min:0'],
            'delivery_charge' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'installment_month' => ['required_if:sale_type,installment', 'nullable', 'integer', 'min:1'],
        ];

        if ($sale) {
            $rules['status'] = ['nullable', Rule::in(['pending', 'completed', 'cancelled'])];
        }

        return $request->validate($rules);
    }
}

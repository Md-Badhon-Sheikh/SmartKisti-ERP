<?php

namespace App\Http\Controllers\CustomOrder;

use App\Enums\GlobalConstant;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomOrder;
use App\Models\CustomOrderItem;
use App\Models\ProductionStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CustomOrderController extends Controller
{
    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('custom-orders.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(Request $request): JsonResponse
    {
        $query = CustomOrder::with('customer')->select('custom_orders.*');

        return DataTables::eloquent($query)
            ->addColumn('customer_name', fn (CustomOrder $order) => $order->customer->name)
            ->editColumn('order_date', fn (CustomOrder $order) => $order->order_date->format('d M Y'))
            ->editColumn('delivery_date', fn (CustomOrder $order) => $order->delivery_date?->format('d M Y') ?? '—')
            ->addColumn('order_type', fn (CustomOrder $order) => '<span class="badge badge-light-'.($order->order_type === 'installment' ? 'warning' : 'success').'">'
                .($order->order_type === 'installment' ? __('Installment') : __('Cash')).'</span>')
            ->editColumn('estimated_price', fn (CustomOrder $order) => number_format($order->estimated_price, 2))
            ->editColumn('remaining_amount', fn (CustomOrder $order) => number_format($order->remaining_amount, 2))
            ->addColumn('status', function (CustomOrder $order) {
                $badgeClass = match ($order->status) {
                    'delivered' => 'badge-light-success',
                    'ready' => 'badge-light-info',
                    'in_production' => 'badge-light-warning',
                    'cancelled' => 'badge-light-danger',
                    default => 'badge-light-secondary',
                };
                $label = match ($order->status) {
                    'delivered' => __('Delivered'),
                    'ready' => __('Ready'),
                    'in_production' => __('In Production'),
                    'cancelled' => __('Cancelled'),
                    default => __('Pending'),
                };

                return '<span class="badge '.$badgeClass.'">'.$label.'</span>';
            })
            ->addColumn('action', function (CustomOrder $order) use ($request) {
                $html = '<div class="d-flex justify-content-end gap-2">'
                    . '<a href="' . route('custom-orders.show', $order->id) . '" class="btn btn-light-info px-4 py-2">'
                    . '<i class="fas fa-eye"></i></a>';

                if ($request->user()->hasAnyRole(['super-admin', 'admin', 'manager']) && $order->status === 'pending') {
                    $html .= '<a href="' . route('custom-orders.edit', $order->id) . '" class="btn btn-sm btn-light-primary px-4 py-2">'
                        . '<i class="fas fa-edit"></i></a>';

                    $html .= '<button class="btn btn-sm btn-light-danger btn-delete px-4 py-2" data-id="' . $order->id . '">'
                        . '<i class="fas fa-trash"></i></button>';
                }

                $html .= '</div>';

                return $html;
            })
            ->rawColumns(['order_type', 'status', 'action'])
            ->make(true);
    }

    /**
     * Display the create page.
     */
    public function Create(): View
    {
        return view('custom-orders.create', $this->formData());
    }

    /**
     * Store a newly created Custom Order.
     */
    public function Store(Request $request): RedirectResponse
    {
        $validated = $this->validateCustomOrder($request);

        DB::transaction(function () use ($request, $validated) {
            $this->persistCustomOrder($request, $validated);
        });

        return redirect()->route('custom-orders.index')->with('status', __('Custom order created successfully.'));
    }

    /**
     * Display the edit page.
     */
    public function Edit(CustomOrder $customOrder): View|RedirectResponse
    {
        if ($customOrder->status !== 'pending') {
            return redirect()->route('custom-orders.index')
                ->with('error', __('This custom order has already entered production and cannot be edited.'));
        }

        $customOrder->load('items');

        return view('custom-orders.edit', array_merge(['order' => $customOrder], $this->formData()));
    }

    /**
     * Update an existing Custom Order.
     */
    public function Update(Request $request, CustomOrder $customOrder): RedirectResponse
    {
        if ($customOrder->status !== 'pending') {
            return redirect()->route('custom-orders.index')
                ->with('error', __('This custom order has already entered production and cannot be edited.'));
        }

        $validated = $this->validateCustomOrder($request, $customOrder);

        DB::transaction(function () use ($request, $validated, $customOrder) {
            $keptImages = collect($validated['items'])->pluck('existing_design_image')->filter()->all();

            foreach ($customOrder->items as $item) {
                if ($item->design_image && ! in_array($item->design_image, $keptImages, true)) {
                    File::delete(public_path($item->design_image));
                }
            }
            $customOrder->items()->delete();

            $this->persistCustomOrder($request, $validated, $customOrder);
        });

        return redirect()->route('custom-orders.index')->with('status', __('Custom order updated successfully.'));
    }

    /**
     * Display the full detail page (items, production timeline, delivery).
     */
    public function Show(CustomOrder $customOrder): View
    {
        $customOrder->load(['customer', 'items', 'productionStatuses', 'deliveries.deliveryBy', 'createdBy']);

        return view('custom-orders.show', [
            'order' => $customOrder,
        ]);
    }

    /**
     * Delete a Custom Order. (POST only)
     */
    public function Delete(int $id): JsonResponse
    {
        $customOrder = CustomOrder::with('items')->findOrFail($id);

        if ($customOrder->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This custom order has already entered production and cannot be deleted.',
            ], 422);
        }

        foreach ($customOrder->items as $item) {
            if ($item->design_image) {
                File::delete(public_path($item->design_image));
            }
        }

        $customOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Custom order deleted successfully.',
        ]);
    }

    /**
     * Advance/record the production stage for a Custom Order.
     */
    public function AdvanceStatus(Request $request, CustomOrder $customOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'cutting', 'making', 'polish', 'ready', 'delivered'])],
            'date' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);

        ProductionStatus::create([
            'custom_order_id' => $customOrder->id,
            'status' => $validated['status'],
            'date' => $validated['date'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        $orderStatusMap = [
            'pending' => 'pending',
            'cutting' => 'in_production',
            'making' => 'in_production',
            'polish' => 'in_production',
            'ready' => 'ready',
            'delivered' => 'delivered',
        ];

        $customOrder->update(['status' => $orderStatusMap[$validated['status']]]);

        return redirect()->route('custom-orders.show', $customOrder->id)->with('status', __('Production status updated.'));
    }

    /**
     * Persist a CustomOrder + its items. Shared by Store() and Update().
     */
    protected function persistCustomOrder(Request $request, array $validated, ?CustomOrder $customOrder = null): CustomOrder
    {
        $estimatedPrice = array_sum(array_column($validated['items'], 'price'));
        $advanceAmount = $validated['advance_amount'] ?? 0;
        $remainingAmount = max($estimatedPrice - $advanceAmount, 0);

        $attributes = [
            'customer_id' => $validated['customer_id'],
            'order_date' => $validated['order_date'],
            'delivery_date' => $validated['delivery_date'] ?? null,
            'order_type' => $validated['order_type'],
            'estimated_price' => $estimatedPrice,
            'advance_amount' => $advanceAmount,
            'remaining_amount' => $remainingAmount,
            'remarks' => $validated['remarks'] ?? null,
        ];

        if ($customOrder) {
            $customOrder->update($attributes);
        } else {
            $attributes['order_no'] = $this->generateOrderNo();
            $attributes['status'] = 'pending';
            $attributes['created_by'] = $request->user()->id;
            $customOrder = CustomOrder::create($attributes);

            ProductionStatus::create([
                'custom_order_id' => $customOrder->id,
                'status' => 'pending',
                'date' => $validated['order_date'],
            ]);
        }

        foreach ($validated['items'] as $index => $item) {
            $designImagePath = $item['existing_design_image'] ?? null;
            $file = $request->file("items.$index.design_image");

            if ($file) {
                if (! empty($item['existing_design_image'])) {
                    File::delete(public_path($item['existing_design_image']));
                }

                $name = 'custom_order_' . $customOrder->id . '_' . $index . '_' . time() . '_' . uniqid();
                $designImagePath = Helper::upload($name, $file, 'uploads/custom-orders')['image_path'];
            }

            CustomOrderItem::create([
                'custom_order_id' => $customOrder->id,
                'product_type' => $item['product_type'],
                'wood_type' => $item['wood_type'] ?? null,
                'size' => $item['size'] ?? null,
                'color' => $item['color'] ?? null,
                'glass_type' => $item['glass_type'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'design_image' => $designImagePath,
                'description' => $item['description'] ?? null,
            ]);
        }

        return $customOrder;
    }

    protected function generateOrderNo(): string
    {
        $next = (CustomOrder::max('id') ?? 0) + 1;

        return 'ORD-' . str_pad((string) (1000 + $next), 4, '0', STR_PAD_LEFT);
    }

    /**
     * @return array{customers: \Illuminate\Support\Collection, woodTypes: \Illuminate\Support\Collection, colors: \Illuminate\Support\Collection, glassTypes: \Illuminate\Support\Collection}
     */
    protected function formData(): array
    {
        return [
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
            'woodTypes' => GlobalConstant::activeWoodTypes(),
            'colors' => GlobalConstant::activeColors(),
            'glassTypes' => GlobalConstant::activeGlassTypes(),
        ];
    }

    protected function validateCustomOrder(Request $request, ?CustomOrder $customOrder = null): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date'],
            'order_type' => ['required', Rule::in(['cash', 'installment'])],
            'advance_amount' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_type' => ['required', 'string', 'max:255'],
            'items.*.wood_type' => ['nullable', Rule::in(collect(GlobalConstant::WOOD_TYPE)->pluck('code')->all())],
            'items.*.size' => ['nullable', 'string', 'max:255'],
            'items.*.color' => ['nullable', Rule::in(collect(GlobalConstant::COLOR)->pluck('code')->all())],
            'items.*.glass_type' => ['nullable', Rule::in(collect(GlobalConstant::GLASS_TYPE)->pluck('code')->all())],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.design_image' => ['nullable', 'image', 'max:4096'],
            'items.*.existing_design_image' => ['nullable', 'string'],
            'items.*.description' => ['nullable', 'string'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\CustomOrder;
use App\Models\Delivery;
use App\Models\ProductionStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class DeliveryController extends Controller
{
    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('deliveries.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(): JsonResponse
    {
        $query = Delivery::with(['sale.customer', 'customOrder.customer'])->select('deliveries.*');

        return DataTables::eloquent($query)
            ->addColumn('reference', fn (Delivery $delivery) => $delivery->customOrder?->order_no ?? $delivery->sale?->invoice_no ?? '—')
            ->addColumn('customer_name', fn (Delivery $delivery) => $delivery->customOrder?->customer?->name ?? $delivery->sale?->customer?->name ?? '—')
            ->editColumn('delivery_date', fn (Delivery $delivery) => $delivery->delivery_date->format('d M Y'))
            ->editColumn('delivery_charge', fn (Delivery $delivery) => number_format($delivery->delivery_charge, 2))
            ->addColumn('delivery_status', function (Delivery $delivery) {
                $badgeClass = match ($delivery->delivery_status) {
                    'delivered' => 'badge-light-success',
                    'failed' => 'badge-light-danger',
                    default => 'badge-light-warning',
                };
                $label = match ($delivery->delivery_status) {
                    'delivered' => __('Delivered'),
                    'failed' => __('Failed'),
                    default => __('Pending'),
                };

                return '<span class="badge '.$badgeClass.'">'.$label.'</span>';
            })
            ->rawColumns(['delivery_status'])
            ->make(true);
    }

    /**
     * Record a delivery against a Custom Order.
     */
    public function StoreForCustomOrder(Request $request, CustomOrder $customOrder): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_date' => ['required', 'date'],
            'delivery_charge' => ['nullable', 'numeric', 'min:0'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_mobile' => ['nullable', 'string', 'max:20'],
            'delivery_status' => ['required', Rule::in(['pending', 'delivered', 'failed'])],
            'signature' => ['nullable', 'image', 'max:2048'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = Helper::upload('delivery_signature_' . time() . '_' . uniqid(), $request->file('signature'), 'uploads/deliveries')['image_path'];
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = Helper::upload('delivery_photo_' . time() . '_' . uniqid(), $request->file('photo'), 'uploads/deliveries')['image_path'];
        }

        Delivery::create([
            'custom_order_id' => $customOrder->id,
            'delivery_date' => $validated['delivery_date'],
            'delivery_charge' => $validated['delivery_charge'] ?? 0,
            'delivery_by' => $request->user()->id,
            'receiver_name' => $validated['receiver_name'],
            'receiver_mobile' => $validated['receiver_mobile'] ?? null,
            'delivery_status' => $validated['delivery_status'],
            'signature' => $signaturePath,
            'photo' => $photoPath,
        ]);

        if ($validated['delivery_status'] === 'delivered') {
            $customOrder->update(['status' => 'delivered']);

            if ($customOrder->currentProductionStatus()?->status !== 'delivered') {
                ProductionStatus::create([
                    'custom_order_id' => $customOrder->id,
                    'status' => 'delivered',
                    'date' => $validated['delivery_date'],
                ]);
            }
        }

        return redirect()->route('custom-orders.show', $customOrder->id)->with('status', __('Delivery recorded successfully.'));
    }
}

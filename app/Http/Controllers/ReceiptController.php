<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ReceiptController extends Controller
{
    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('receipts.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(): JsonResponse
    {
        $query = Receipt::with(['customer', 'sale'])->select('receipts.*');

        return DataTables::eloquent($query)
            ->addColumn('customer_name', fn (Receipt $receipt) => $receipt->customer->name)
            ->addColumn('invoice_no', fn (Receipt $receipt) => $receipt->sale?->invoice_no ?? '—')
            ->editColumn('date', fn (Receipt $receipt) => $receipt->date->format('d M Y'))
            ->editColumn('amount', fn (Receipt $receipt) => number_format($receipt->amount, 2))
            ->addColumn('receipt_type', function (Receipt $receipt) {
                $badgeClass = match ($receipt->receipt_type) {
                    'installment' => 'badge-light-warning',
                    'refund' => 'badge-light-danger',
                    default => 'badge-light-success',
                };
                $label = match ($receipt->receipt_type) {
                    'installment' => __('Installment'),
                    'refund' => __('Refund'),
                    default => __('Cash'),
                };

                return '<span class="badge '.$badgeClass.'">'.$label.'</span>';
            })
            ->addColumn('action', fn (Receipt $receipt) =>
                '<a href="'.route('receipts.show', $receipt->id).'" target="_blank" class="btn btn-sm btn-light-info px-4 py-2">'
                .'<i class="fas fa-eye"></i></a>')
            ->rawColumns(['receipt_type', 'action'])
            ->make(true);
    }

    /**
     * Display a single printable Receipt.
     */
    public function Show(Receipt $receipt)
    {
        $receipt->load(['customer', 'sale', 'payment']);

        return view('receipts.show', ['receipt' => $receipt]);
    }
}

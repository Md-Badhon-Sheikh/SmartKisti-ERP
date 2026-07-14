<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class SmsLogController extends Controller
{
    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('sms-logs.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(): JsonResponse
    {
        $query = SmsLog::with('customer')->select('sms_logs.*');

        return DataTables::eloquent($query)
            ->addColumn('customer_name', fn (SmsLog $log) => $log->customer?->name ?? '—')
            ->addColumn('sms_type', function (SmsLog $log) {
                $label = match ($log->sms_type) {
                    'sale' => __('Sale'),
                    'installment' => __('Installment'),
                    'order' => __('Order'),
                    'delivery' => __('Delivery'),
                    default => $log->sms_type,
                };

                return '<span class="badge badge-light-primary">'.$label.'</span>';
            })
            ->addColumn('status', function (SmsLog $log) {
                $badgeClass = match ($log->status) {
                    'sent' => 'badge-light-success',
                    'failed' => 'badge-light-danger',
                    default => 'badge-light-warning',
                };
                $label = match ($log->status) {
                    'sent' => __('Sent'),
                    'failed' => __('Failed'),
                    default => __('Pending'),
                };

                return '<span class="badge '.$badgeClass.'">'.$label.'</span>';
            })
            ->editColumn('sent_at', fn (SmsLog $log) => $log->sent_at?->format('d M Y, h:i A') ?? '—')
            ->editColumn('created_at', fn (SmsLog $log) => $log->created_at->format('d M Y, h:i A'))
            ->rawColumns(['sms_type', 'status'])
            ->make(true);
    }
}

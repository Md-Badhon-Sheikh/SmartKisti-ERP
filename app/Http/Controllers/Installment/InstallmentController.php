<?php

namespace App\Http\Controllers\Installment;

use App\Enums\GlobalConstant;
use App\Http\Controllers\Controller;
use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Services\Sms\SmsLogService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InstallmentController extends Controller
{
    public function __construct(private readonly SmsLogService $smsLog) {}

    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('installments.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(): JsonResponse
    {
        $query = InstallmentPlan::with(['sale', 'customer'])->select('installment_plans.*');

        return DataTables::eloquent($query)
            ->addColumn('invoice_no', fn (InstallmentPlan $plan) => $plan->sale->invoice_no)
            ->addColumn('customer_name', fn (InstallmentPlan $plan) => $plan->customer->name)
            ->editColumn('product_total', fn (InstallmentPlan $plan) => number_format($plan->product_total, 2))
            ->editColumn('monthly_amount', fn (InstallmentPlan $plan) => number_format($plan->monthly_amount, 2))
            ->editColumn('remaining_due', fn (InstallmentPlan $plan) => number_format($plan->remaining_due, 2))
            ->editColumn('next_payment_date', fn (InstallmentPlan $plan) => $plan->status === 'completed'
                ? '—'
                : $plan->next_payment_date?->format('d M Y'))
            ->addColumn('status', function (InstallmentPlan $plan) {
                $badgeClass = match ($plan->status) {
                    'completed' => 'badge-light-success',
                    'cancelled' => 'badge-light-danger',
                    default => 'badge-light-warning',
                };
                $label = match ($plan->status) {
                    'completed' => __('Completed'),
                    'cancelled' => __('Cancelled'),
                    default => __('Active'),
                };

                return '<span class="badge '.$badgeClass.'">'.$label.'</span>';
            })
            ->addColumn('action', fn (InstallmentPlan $plan) =>
                '<a href="'.route('installments.show', $plan->id).'" class="btn btn-sm btn-light-info px-4 py-2">'
                .'<i class="fas fa-eye"></i></a>')
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Display an Installment Plan's schedule, payment history, and record-payment form.
     */
    public function Show(InstallmentPlan $installmentPlan): View
    {
        $installmentPlan->load(['sale', 'customer', 'payments.receivedBy']);

        return view('installments.show', [
            'plan' => $installmentPlan,
            'schedule' => $this->buildSchedule($installmentPlan),
            'paymentMethods' => GlobalConstant::activePaymentMethods(),
        ]);
    }

    /**
     * Record a new payment against an Installment Plan.
     */
    public function StorePayment(Request $request, InstallmentPlan $installmentPlan): RedirectResponse
    {
        if ($installmentPlan->status === 'completed') {
            return back()->with('error', __('This installment plan is already completed.'));
        }

        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . max($installmentPlan->remaining_due, 0.01)],
            'payment_method' => ['required', Rule::in(collect(GlobalConstant::PAYMENT_METHOD)->pluck('code')->all())],
            'remarks' => ['nullable', 'string'],
        ]);

        $nextInstallmentNo = ($installmentPlan->payments()->max('installment_no') ?? 0) + 1;

        $payment = InstallmentPayment::create([
            'installment_plan_id' => $installmentPlan->id,
            'sale_id' => $installmentPlan->sale_id,
            'customer_id' => $installmentPlan->customer_id,
            'payment_date' => $validated['payment_date'],
            'installment_no' => $nextInstallmentNo,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'received_by' => $request->user()->id,
            'remarks' => $validated['remarks'] ?? null,
            'receipt_no' => $this->generateReceiptNo(),
        ]);

        $totalPaid = $installmentPlan->total_paid + $validated['amount'];
        $remainingDue = max($installmentPlan->total_due - $totalPaid, 0);
        $isCompleted = $remainingDue <= 0;

        $installmentPlan->update([
            'total_paid' => $totalPaid,
            'remaining_due' => $remainingDue,
            'last_payment_date' => $validated['payment_date'],
            'next_payment_date' => $isCompleted
                ? $installmentPlan->next_payment_date
                : Carbon::parse($installmentPlan->next_payment_date)->addMonthNoOverflow(),
            'status' => $isCompleted ? 'completed' : 'active',
        ]);

        $sale = $installmentPlan->sale;
        $sale->update([
            'paid_amount' => $sale->paid_amount + $validated['amount'],
            'due_amount' => max($sale->due_amount - $validated['amount'], 0),
            'status' => $isCompleted ? 'completed' : $sale->status,
        ]);

        $this->sendPaymentSms($payment->fresh('customer'), $remainingDue);

        return redirect()->route('installments.show', $installmentPlan->id)->with('status', __('Payment recorded successfully.'));
    }

    /**
     * Send (and log) the payment-received SMS for a newly recorded InstallmentPayment.
     */
    protected function sendPaymentSms(InstallmentPayment $payment, float $remainingDue): void
    {
        if (! $payment->customer->mobile) {
            return;
        }

        $message = sprintf(
            'প্রিয় %s, আপনার কিস্তি #%d এর ৳%s পরিশোধ সফলভাবে গ্রহণ করা হয়েছে। অবশিষ্ট বাকি: ৳%s। রিসিট: %s - SmartKisti ERP',
            $payment->customer->name,
            $payment->installment_no,
            number_format($payment->amount, 2),
            number_format($remainingDue, 2),
            $payment->receipt_no,
        );

        $log = $this->smsLog->send($payment->customer->mobile, $message, 'installment', [
            'customer_id' => $payment->customer_id,
            'sale_id' => $payment->sale_id,
            'installment_payment_id' => $payment->id,
        ]);

        $payment->update(['sms_sent' => $log->status === 'sent']);
    }

    /**
     * Build the full expected installment schedule (1..installment_month),
     * marking each installment as paid/pending based on recorded payments.
     */
    protected function buildSchedule(InstallmentPlan $plan): array
    {
        $paymentsByNo = $plan->payments->keyBy('installment_no');
        $schedule = [];

        for ($i = 1; $i <= $plan->installment_month; $i++) {
            $payment = $paymentsByNo->get($i);

            $schedule[] = [
                'installment_no' => $i,
                'due_date' => Carbon::parse($plan->start_date)->addMonthsNoOverflow($i),
                'amount' => $plan->monthly_amount,
                'paid' => (bool) $payment,
                'payment' => $payment,
            ];
        }

        return $schedule;
    }

    protected function generateReceiptNo(): string
    {
        $next = (InstallmentPayment::max('id') ?? 0) + 1;

        return 'RCPT-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}

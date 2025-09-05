<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Batch;
use App\Services\WhatsAppService;
use App\Services\InvoicePdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Process payment for an invoice
     */
    public function processPayment(Request $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank_transfer,online,check',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $paymentAmount = $validated['amount'];
            $paymentDate = $validated['payment_date'] ?? now();

            // Check if payment amount exceeds balance due
            if ($paymentAmount > $invoice->balance_due) {
                return response()->json([
                    'message' => 'Payment amount cannot exceed balance due',
                    'balance_due' => $invoice->balance_due
                ], 400);
            }

            // Check if payment amount is valid (must be greater than 0)
            if ($paymentAmount <= 0) {
                return response()->json([
                    'message' => 'Payment amount must be greater than 0'
                ], 400);
            }

            // Generate payment number
            $paymentNumber = 'PAY-' . str_pad(Payment::count() + 1, 6, '0', STR_PAD_LEFT);

            // Create payment record
            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'batch_id' => $invoice->batch_id,
                'amount' => $paymentAmount,
                'payment_date' => $paymentDate,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['transaction_id'] ?? null,
                'notes' => $validated['notes'],
                'received_by' => auth()->user()->name ?? 'System',
                'is_verified' => true,
            ]);

            // Update invoice payment status
            $newPaidAmount = $invoice->paid_amount + $paymentAmount;
            $newBalanceDue = $invoice->total_amount - $newPaidAmount;

            $status = 'unpaid';
            if ($newBalanceDue <= 0) {
                $status = 'paid';
            } elseif ($newPaidAmount > 0) {
                $status = 'partially_paid';
            }

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance_due' => $newBalanceDue,
                'status' => $status,
            ]);

            // If invoice is fully paid, allow batch dispatch and send WhatsApp notification
            if ($status === 'paid') {
                $invoice->batch->update(['can_dispatch' => true]);

                // Send WhatsApp notification
                try {
                    $whatsappService = new WhatsAppService();
                    $pdfService = new InvoicePdfService();

                    // Generate PDF for WhatsApp
                    $pdfPath = $pdfService->generateForWhatsApp($invoice);

                    // Send WhatsApp message with PDF
                    $whatsappService->sendPaymentConfirmation(
                        $invoice->customer,
                        $invoice,
                        $invoice->batch,
                        $pdfPath
                    );

                    // Clean up temp PDF file
                    if (file_exists($pdfPath)) {
                        unlink($pdfPath);
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the payment
                    \Log::error('WhatsApp notification failed', [
                        'invoice_id' => $invoice->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Payment processed successfully',
                'data' => [
                    'payment' => $payment,
                    'invoice' => $invoice->fresh(['customer', 'batch', 'payments']),
                    'new_status' => $status,
                    'can_dispatch' => $status === 'paid'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to process payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history for an invoice
     */
    public function getPaymentHistory(Invoice $invoice): JsonResponse
    {
        $payments = $invoice->payments()->orderBy('payment_date', 'desc')->get();

        return response()->json([
            'message' => 'Payment history retrieved successfully',
            'data' => $payments
        ]);
    }

    /**
     * Get all payments with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['invoice.customer', 'invoice.batch']);

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('payment_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('payment_date', '<=', $request->to_date);
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        return response()->json([
            'message' => 'Payments retrieved successfully',
            'data' => $payments
        ]);
    }

    /**
     * Get payment statistics
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $query = Payment::query();

        // Apply date range if provided
        if ($request->has('from_date')) {
            $query->where('payment_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('payment_date', '<=', $request->to_date);
        }

        $totalPayments = $query->count();
        $totalAmount = $query->sum('amount');

        // Payment method breakdown
        $paymentMethods = $query->select('payment_method')
            ->selectRaw('COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        // Daily payment trends (last 30 days)
        $dailyTrends = Payment::where('payment_date', '>=', now()->subDays(30))
            ->selectRaw('DATE(payment_date) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'message' => 'Payment statistics retrieved successfully',
            'data' => [
                'total_payments' => $totalPayments,
                'total_amount' => $totalAmount,
                'payment_methods' => $paymentMethods,
                'daily_trends' => $dailyTrends,
            ]
        ]);
    }

    /**
     * Revert payment status (mark invoice as unpaid)
     */
    public function revertPayment(Invoice $invoice): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Check if invoice is actually paid
            if ($invoice->status !== 'paid') {
                return response()->json([
                    'message' => 'Invoice is not marked as paid',
                    'current_status' => $invoice->status
                ], 400);
            }

            // Update invoice status back to unpaid
            $invoice->update([
                'status' => 'unpaid',
                'paid_amount' => 0,
                'balance_due' => $invoice->total_amount,
            ]);

            // Update batch to prevent dispatch
            $invoice->batch->update(['can_dispatch' => false]);

            // Optionally, you can delete the payment records or mark them as reverted
            // For now, we'll keep the payment records for audit purposes
            // but you can uncomment the line below if you want to delete them:
            // $invoice->payments()->delete();

            DB::commit();

            return response()->json([
                'message' => 'Payment reverted successfully',
                'data' => [
                    'invoice' => $invoice->fresh(['customer', 'batch', 'payments']),
                    'new_status' => 'unpaid',
                    'can_dispatch' => false
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to revert payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refund payment (if needed)
     */
    public function refund(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'refund_reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $refundAmount = $validated['refund_amount'];
            $refundReason = $validated['refund_reason'];

            // Generate refund number
            $refundNumber = 'REF-' . str_pad(Payment::count() + 1, 6, '0', STR_PAD_LEFT);

            // Create refund record (negative payment)
            $refund = Payment::create([
                'payment_number' => $refundNumber,
                'invoice_id' => $payment->invoice_id,
                'customer_id' => $payment->customer_id,
                'batch_id' => $payment->batch_id,
                'amount' => -$refundAmount, // Negative amount for refund
                'payment_date' => now(),
                'payment_method' => 'other',
                'reference_number' => 'REFUND-' . $payment->id,
                'notes' => "Refund: {$refundReason}",
                'received_by' => auth()->user()->name ?? 'System',
                'is_verified' => true,
            ]);

            // Update invoice
            $invoice = $payment->invoice;
            $newPaidAmount = $invoice->paid_amount - $refundAmount;
            $newBalanceDue = $invoice->total_amount - $newPaidAmount;

            $status = 'unpaid';
            if ($newBalanceDue <= 0) {
                $status = 'paid';
            } elseif ($newPaidAmount > 0) {
                $status = 'partially_paid';
            }

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance_due' => $newBalanceDue,
                'status' => $status,
            ]);

            // If invoice is no longer fully paid, prevent dispatch
            if ($status !== 'paid') {
                $invoice->batch->update(['can_dispatch' => false]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Refund processed successfully',
                'data' => [
                    'refund' => $refund,
                    'invoice' => $invoice->fresh(['customer', 'batch', 'payments']),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to process refund',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Batch;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['customer', 'batch', 'payments']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('batch_id', 'like', "%{$search}%") // Search by LOT ID
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'message' => 'Invoices retrieved successfully',
            'data' => $invoices
        ]);
    }

    /**
     * Create invoice for a batch (automatically called when batch is created)
     */
    public function createForBatch(Batch $batch): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Check if invoice already exists for this batch
            $existingInvoice = Invoice::where('batch_id', $batch->id)->first();
            if ($existingInvoice) {
                return response()->json([
                    'message' => 'Invoice already exists for this batch',
                    'data' => $existingInvoice->load(['customer', 'batch', 'items'])
                ], 400);
            }

            // Calculate invoice amounts
            $subtotal = $batch->total_value;
            $taxAmount = 0; // You can add tax calculation here
            $totalAmount = $subtotal + $taxAmount;

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'customer_id' => $batch->customer_id,
                'batch_id' => $batch->id,
                'status' => 'unpaid',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance_due' => $totalAmount,
                'invoice_date' => now(),
                'due_date' => now()->addDays(30), // 30 days payment terms
                'notes' => "Storage charges for Lot #{$batch->id}",
                'payment_terms' => 'Payment due within 30 days',
            ]);

            // Create invoice items
            $invoice->items()->create([
                'description' => "Cold Storage Charges - Lot #{$batch->id}",
                'quantity' => $batch->total_baskets,
                'unit_price' => $batch->unit_price,
                'total_price' => $batch->total_value,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Invoice created successfully',
                'data' => $invoice->load(['customer', 'batch', 'items'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json([
            'message' => 'Invoice retrieved successfully',
            'data' => $invoice->load(['customer', 'batch', 'items', 'payments'])
        ]);
    }

    /**
     * Get unpaid invoices for a customer
     */
    public function getUnpaidInvoices($customerId): JsonResponse
    {
        $invoices = Invoice::with(['batch'])
            ->where('customer_id', $customerId)
            ->where('status', 'unpaid')
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json([
            'message' => 'Unpaid invoices retrieved successfully',
            'data' => $invoices
        ]);
    }

    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices(): JsonResponse
    {
        $invoices = Invoice::with(['customer', 'batch'])
            ->overdue()
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json([
            'message' => 'Overdue invoices retrieved successfully',
            'data' => $invoices
        ]);
    }

    /**
     * Get invoices due soon (within 7 days)
     */
    public function getDueSoonInvoices(): JsonResponse
    {
        $invoices = Invoice::with(['customer', 'batch'])
            ->dueSoon()
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json([
            'message' => 'Due soon invoices retrieved successfully',
            'data' => $invoices
        ]);
    }

    /**
     * Get invoice statistics
     */
    public function getStatistics(): JsonResponse
    {
        $totalInvoices = Invoice::count();
        $unpaidInvoices = Invoice::unpaid()->count();
        $paidInvoices = Invoice::paid()->count();
        $overdueInvoices = Invoice::overdue()->count();
        $dueSoonInvoices = Invoice::dueSoon()->count();

        $totalAmount = Invoice::sum('total_amount');
        $paidAmount = Invoice::sum('paid_amount');
        $outstandingAmount = Invoice::sum('balance_due');

        return response()->json([
            'message' => 'Invoice statistics retrieved successfully',
            'data' => [
                'total_invoices' => $totalInvoices,
                'unpaid_invoices' => $unpaidInvoices,
                'paid_invoices' => $paidInvoices,
                'overdue_invoices' => $overdueInvoices,
                'due_soon_invoices' => $dueSoonInvoices,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'outstanding_amount' => $outstandingAmount,
            ]
        ]);
    }

    /**
     * Update invoice (for notes, payment terms, etc.)
     */
    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'payment_terms' => 'nullable|string',
        ]);

        $invoice->update($validated);

        return response()->json([
            'message' => 'Invoice updated successfully',
            'data' => $invoice->load(['customer', 'batch', 'items', 'payments'])
        ]);
    }

    /**
     * Cancel invoice
     */
    public function cancel(Invoice $invoice): JsonResponse
    {
        if ($invoice->status === 'paid') {
            return response()->json([
                'message' => 'Cannot cancel a paid invoice',
            ], 400);
        }

        $invoice->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Invoice cancelled successfully',
            'data' => $invoice
        ]);
    }
}


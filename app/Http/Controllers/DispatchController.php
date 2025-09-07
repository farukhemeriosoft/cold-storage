<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\DispatchRecord;
use App\Models\DispatchApproval;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    /**
     * Dispatch a basket by barcode scan
     */
    public function dispatchByBarcode(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string',
            'lot_id' => 'required|integer|exists:batches,id',
            'dispatch_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Find the basket by barcode and lot_id
            $basket = Basket::with(['batch.customer', 'batch.invoice'])
                ->where('barcode', $request->barcode)
                ->where('batch_id', $request->lot_id)
                ->first();

            if (!$basket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Basket not found with this barcode in the selected lot',
                    'error_code' => 'BASKET_NOT_FOUND_IN_LOT'
                ], 404);
            }

            // Check if basket is already dispatched
            $existingDispatch = DispatchRecord::where('basket_id', $basket->id)
                ->whereIn('status', ['approved', 'dispatched'])
                ->first();

            if ($existingDispatch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Basket has already been dispatched',
                    'error_code' => 'ALREADY_DISPATCHED',
                    'dispatch_number' => $existingDispatch->dispatch_number
                ], 422);
            }

            $batch = $basket->batch;
            $customer = $batch->customer;
            $invoice = $batch->invoice;

            // Check if invoice is fully paid
            $isInvoicePaid = $invoice && $invoice->status === 'paid' && $invoice->balance_due <= 0;

            // Determine approval status
            $approvalStatus = $isInvoicePaid ? 'auto_approved' : 'pending_approval';
            $status = $isInvoicePaid ? 'approved' : 'pending';

            // Create dispatch record
            $dispatchRecord = DispatchRecord::create([
                'dispatch_number' => DispatchRecord::generateDispatchNumber(),
                'basket_id' => $basket->id,
                'batch_id' => $batch->id,
                'customer_id' => $customer->id,
                'barcode' => $basket->barcode,
                'unit_price' => $batch->unit_price,
                'total_amount' => $batch->unit_price,
                'status' => $status,
                'approval_status' => $approvalStatus,
                'dispatch_notes' => $request->dispatch_notes,
            ]);

            // If auto-approved, set approved_by to current user
            if ($isInvoicePaid) {
                $dispatchRecord->update([
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isInvoicePaid
                    ? 'Basket approved for dispatch (invoice fully paid)'
                    : 'Dispatch request created, awaiting admin approval',
                'data' => [
                    'dispatch_record' => $dispatchRecord->load(['basket', 'batch', 'customer']),
                    'requires_approval' => !$isInvoicePaid,
                    'invoice_status' => $invoice ? $invoice->status : 'No invoice',
                    'balance_due' => $invoice ? $invoice->balance_due : 0,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process dispatch request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete dispatch (mark as dispatched)
     */
    public function completeDispatch(Request $request, $dispatchId): JsonResponse
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        try {
            $dispatchRecord = DispatchRecord::findOrFail($dispatchId);

            if (!$dispatchRecord->canDispatch()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dispatch cannot be completed. Status: ' . $dispatchRecord->status . ', Approval: ' . $dispatchRecord->approval_status
                ], 422);
            }

            $dispatchRecord->update([
                'status' => 'dispatched',
                'dispatched_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Remove the basket from storage
            $basket = $dispatchRecord->basket;
            $basket->delete();

            // Update batch totals
            $batch = $dispatchRecord->batch;
            $remainingBaskets = $batch->baskets()->count();
            $totalValue = $remainingBaskets * $batch->unit_price;

            $batch->update([
                'total_baskets' => $remainingBaskets,
                'total_weight' => $remainingBaskets,
                'total_value' => $totalValue,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Basket dispatched successfully',
                'data' => [
                    'dispatch_record' => $dispatchRecord->fresh(),
                    'batch_updated' => $batch->fresh(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete dispatch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending dispatch approvals
     */
    public function getPendingApprovals(): JsonResponse
    {
        $pendingDispatches = DispatchRecord::with(['basket', 'batch', 'customer', 'batch.invoice'])
            ->where('status', 'pending')
            ->where('approval_status', 'pending_approval')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pendingDispatches
        ]);
    }

    /**
     * Approve or reject dispatch request
     */
    public function approveDispatch(Request $request, $dispatchId): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $dispatchRecord = DispatchRecord::findOrFail($dispatchId);

            if ($dispatchRecord->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Dispatch request is not pending'
                ], 422);
            }

            $action = $request->action;
            $newStatus = $action === 'approve' ? 'approved' : 'cancelled';

            $dispatchRecord->update([
                'status' => $newStatus,
                'approval_status' => $action === 'approve' ? 'admin_approved' : 'pending_approval',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Create approval record
            DispatchApproval::create([
                'dispatch_record_id' => $dispatchRecord->id,
                'admin_id' => Auth::id(),
                'status' => $action === 'approve' ? 'approved' : 'rejected',
                'admin_notes' => $request->admin_notes,
                'approved_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dispatch request ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully',
                'data' => [
                    'dispatch_record' => $dispatchRecord->fresh(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process approval',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dispatch history
     */
    public function getDispatchHistory(Request $request): JsonResponse
    {
        $query = DispatchRecord::with(['basket', 'batch', 'customer', 'approver']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Search by barcode or dispatch number
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%")
                  ->orWhere('dispatch_number', 'like', "%{$search}%");
            });
        }

        $dispatches = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $dispatches
        ]);
    }

    /**
     * Get dispatch statistics
     */
    public function getDispatchStats(): JsonResponse
    {
        $stats = [
            'total_dispatches' => DispatchRecord::count(),
            'pending_approvals' => DispatchRecord::where('status', 'pending')->count(),
            'approved_dispatches' => DispatchRecord::where('status', 'approved')->count(),
            'completed_dispatches' => DispatchRecord::where('status', 'dispatched')->count(),
            'cancelled_dispatches' => DispatchRecord::where('status', 'cancelled')->count(),
            'today_dispatches' => DispatchRecord::whereDate('created_at', today())->count(),
            'auto_approved' => DispatchRecord::where('approval_status', 'auto_approved')->count(),
            'admin_approved' => DispatchRecord::where('approval_status', 'admin_approved')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

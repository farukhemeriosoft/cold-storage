<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBatchRequest;
use App\Http\Requests\AddBasketsToBatchRequest;
use App\Models\Basket;
use App\Models\Batch;
use App\Models\BasketHistory;
use App\Models\Customer;
use App\Models\Invoice;
use App\Http\Controllers\InvoiceController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BasketController extends Controller
{
    public function index(): JsonResponse
    {
        $batches = Batch::with(['customer', 'baskets', 'room', 'floor', 'zone'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($batch) {
                $batch->expiry_status = $batch->getExpiryStatus();
                $batch->days_until_expiry = $batch->getDaysUntilExpiry();
                $batch->storage_location = $batch->getStorageLocation();
                return $batch;
            });

        return response()->json([
            'message' => 'Batches retrieved successfully',
            'data' => $batches
        ]);
    }

    public function createBatch(CreateBatchRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $batch = new Batch([
                'customer_id' => $validated['customer_id'],
                'room_id' => $validated['room_id'],
                'floor_id' => $validated['floor_id'],
                'zone_id' => $validated['zone_id'],
                'unit_price' => $validated['unit_price'],
                'total_baskets' => 0,
                'total_weight' => 0,
                'total_value' => 0,
                'expiry_date' => now()->addYear(), // 12 months from creation
                'can_dispatch' => false, // Cannot dispatch until invoice is paid
                'status' => 'active',
            ]);
            $batch->save();

            // Create invoice for the batch
            $invoiceController = new InvoiceController();
            $invoiceResponse = $invoiceController->createForBatch($batch);

            if ($invoiceResponse->getStatusCode() !== 201) {
                throw new \Exception('Failed to create invoice for batch');
            }

            DB::commit();

            return response()->json([
                'message' => 'Batch created successfully with invoice',
                'data' => [
                    'batch' => $batch->load(['customer', 'room', 'floor', 'zone']),
                    'invoice' => $invoiceResponse->getData()->data
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create batch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addBasketsToBatch(AddBasketsToBatchRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $batch = Batch::findOrFail($validated['batch_id']);
        $customer = $batch->customer;

        $created = [];
        foreach ($validated['baskets'] as $item) {
            $basket = new Basket([
                'batch_id' => $batch->id,
                'customer_id' => $customer->id,
                'barcode' => $item['barcode'],
                'status' => 'unpaid',
            ]);
            $basket->save();
            $created[] = $basket;
        }

        $newBasketCount = $batch->total_baskets + count($created);
        $totalValue = $newBasketCount * (float) $batch->unit_price;

        $batch->update([
            'total_baskets' => $newBasketCount,
            'total_weight' => $newBasketCount,
            'total_value' => $totalValue,
        ]);

        // Update the invoice with new values
        $invoice = Invoice::where('batch_id', $batch->id)->first();
        if ($invoice) {
            $taxAmount = 0; // You can add tax calculation here
            $totalAmount = $totalValue + $taxAmount;

            $invoice->update([
                'subtotal' => $totalValue,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'balance_due' => $totalAmount - $invoice->paid_amount,
            ]);

            // Update invoice items
            $invoice->items()->delete(); // Remove old items
            $invoice->items()->create([
                'description' => "Cold Storage Charges - Lot #{$batch->id}",
                'quantity' => $newBasketCount,
                'unit_price' => $batch->unit_price,
                'total_price' => $totalValue,
            ]);
        }

        return response()->json([
            'message' => 'Baskets added to batch successfully',
            'data' => [
                'batch' => $batch->fresh(),
                'added_baskets' => $created,
                'invoice' => $invoice ? $invoice->fresh() : null,
            ],
        ], 201);
    }


    public function getExpiringBatches(): JsonResponse
    {
        $expiringBatches = Batch::with(['customer', 'baskets'])
            ->expiringSoon(30) // Within 30 days
            ->orderBy('expiry_date', 'asc')
            ->get()
            ->map(function ($batch) {
                $batch->expiry_status = $batch->getExpiryStatus();
                $batch->days_until_expiry = $batch->getDaysUntilExpiry();
                return $batch;
            });

        $expiredBatches = Batch::with(['customer', 'baskets'])
            ->expired()
            ->orderBy('expiry_date', 'asc')
            ->get()
            ->map(function ($batch) {
                $batch->expiry_status = $batch->getExpiryStatus();
                $batch->days_until_expiry = $batch->getDaysUntilExpiry();
                return $batch;
            });

        return response()->json([
            'message' => 'Expiring batches retrieved successfully',
            'data' => [
                'expiring_soon' => $expiringBatches,
                'expired' => $expiredBatches,
                'summary' => [
                    'expiring_count' => $expiringBatches->count(),
                    'expired_count' => $expiredBatches->count(),
                ]
            ]
        ]);
    }
}



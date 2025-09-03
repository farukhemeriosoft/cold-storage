<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBatchRequest;
use App\Http\Requests\AddBasketsToBatchRequest;
use App\Http\Requests\DispatchBasketRequest;
use App\Http\Requests\DispatchBatchRequest;
use App\Models\Basket;
use App\Models\Batch;
use App\Models\BasketHistory;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BasketController extends Controller
{
    public function index(): JsonResponse
    {
        $batches = Batch::with(['customer', 'baskets'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Batches retrieved successfully',
            'data' => $batches
        ]);
    }

    public function createBatch(CreateBatchRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $batch = new Batch([
            'customer_id' => $validated['customer_id'],
            'unit_price' => $validated['unit_price'],
            'total_baskets' => 0,
            'total_weight' => 0,
            'total_value' => 0,
        ]);
        $batch->save();

        return response()->json([
            'message' => 'Batch created successfully',
            'data' => $batch->load('customer'),
        ], 201);
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

        return response()->json([
            'message' => 'Baskets added to batch successfully',
            'data' => [
                'batch' => $batch->fresh(),
                'added_baskets' => $created,
            ],
        ], 201);
    }

    public function dispatch(DispatchBasketRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated) {
            $basket = Basket::with(['customer','batch'])->where('barcode', $validated['barcode'])->firstOrFail();

            $totalAmount = (float) $basket->batch->unit_price;

            $invoice = Invoice::create([
                'customer_id' => $basket->customer_id,
                'barcode' => $basket->barcode,
                'unit_price' => $basket->batch->unit_price,
                'total_amount' => $totalAmount,
            ]);

            BasketHistory::create([
                'basket_id' => $basket->id,
                'customer_id' => $basket->customer_id,
                'batch_id' => $basket->batch_id,
                'barcode' => $basket->barcode,
                'unit_price' => $basket->batch->unit_price,
                'dispatched_at' => now(),
            ]);

            $basket->delete();

            return response()->json([
                'message' => 'Basket dispatched. Invoice generated.',
                'data' => [
                    'invoice' => $invoice->load('customer'),
                ],
            ]);
        });
    }

    public function dispatchBatch(DispatchBatchRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated) {
            $batch = Batch::with(['customer', 'baskets'])->findOrFail($validated['batch_id']);

            if ($batch->baskets->isEmpty()) {
                return response()->json([
                    'message' => 'No baskets found in this batch to dispatch.',
                ], 422);
            }

            $dispatchedBaskets = [];
            $invoices = [];
            $histories = [];

            foreach ($batch->baskets as $basket) {
                // Create invoice for each basket
                $invoice = Invoice::create([
                    'customer_id' => $basket->customer_id,
                    'barcode' => $basket->barcode,
                    'unit_price' => $batch->unit_price,
                    'total_amount' => $batch->unit_price,
                ]);

                // Create history record for each basket
                $history = BasketHistory::create([
                    'basket_id' => $basket->id,
                    'customer_id' => $basket->customer_id,
                    'batch_id' => $batch->id,
                    'barcode' => $basket->barcode,
                    'unit_price' => $batch->unit_price,
                    'dispatched_at' => now(),
                ]);

                // Delete the basket
                $basket->delete();

                $dispatchedBaskets[] = [
                    'barcode' => $basket->barcode,
                    'unit_price' => $batch->unit_price,
                ];

                $invoices[] = $invoice;
                $histories[] = $history;
            }

            // Update batch totals to zero since all baskets are dispatched
            $batch->update([
                'total_baskets' => 0,
                'total_weight' => 0,
                'total_value' => 0,
            ]);

            return response()->json([
                'message' => 'Batch dispatched successfully. All baskets processed.',
                'data' => [
                    'batch' => $batch->fresh(),
                    'dispatched_baskets' => $dispatchedBaskets,
                    'total_invoices' => count($invoices),
                    'total_histories' => count($histories),
                ],
            ]);
        });
    }
}



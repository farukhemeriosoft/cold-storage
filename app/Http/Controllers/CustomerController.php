<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Customer::with('baskets');

        // Filter by active status if requested
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('cnic_number', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'message' => 'Customers retrieved successfully',
            'data' => $customers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer->load('baskets')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'message' => 'Customer retrieved successfully',
            'data' => $customer->load('baskets')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());

        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => $customer->load('baskets')
        ]);
    }

    /**
     * Deactivate the specified customer (soft delete).
     */
    public function deactivate(Customer $customer): JsonResponse
    {
        $customer->update(['is_active' => false]);

        return response()->json([
            'message' => 'Customer deactivated successfully',
            'data' => $customer
        ]);
    }

    /**
     * Activate the specified customer.
     */
    public function activate(Customer $customer): JsonResponse
    {
        $customer->update(['is_active' => true]);

        return response()->json([
            'message' => 'Customer activated successfully',
            'data' => $customer
        ]);
    }

    /**
     * Get customer's baskets.
     */
    public function baskets(Customer $customer): JsonResponse
    {
        $baskets = $customer->baskets()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Customer baskets retrieved successfully',
            'data' => [
                'customer' => $customer,
                'baskets' => $baskets
            ]
        ]);
    }
}

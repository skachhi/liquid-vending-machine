<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with('product')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,upi',
            'customer_info' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($validated['product_id']);

            // Check stock availability
            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available'
                ], 400);
            }

            $totalPrice = $product->price * $validated['quantity'];

            $order = Order::create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $validated['quantity'],
                'total_price' => $totalPrice,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'customer_info' => $validated['customer_info'] ?? []
            ]);

            // Update product stock
            $product->decrement('stock', $validated['quantity']);

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order,
                'message' => 'Order placed successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Total sales
        $totalSales = Order::completed()
            ->byDateRange($startDate, $endDate)
            ->sum('total_price');

        // Total orders
        $totalOrders = Order::completed()
            ->byDateRange($startDate, $endDate)
            ->count();

        // Most popular product
        $mostPopularProduct = Order::completed()
            ->byDateRange($startDate, $endDate)
            ->select('product_name', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_name')
            ->orderBy('total_quantity', 'desc')
            ->first();

        // Average order value
        $averageOrderValue = Order::completed()
            ->byDateRange($startDate, $endDate)
            ->avg('total_price');

        // Sales by category
        $salesByCategory = Order::completed()
            ->byDateRange($startDate, $endDate)
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->select('products.category', DB::raw('SUM(orders.total_price) as total_sales'))
            ->groupBy('products.category')
            ->get();

        // Daily sales for chart
        $dailySales = Order::completed()
            ->byDateRange($startDate, $endDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as daily_sales'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'total_sales' => round($totalSales, 2),
            'total_orders' => $totalOrders,
            'most_popular_product' => $mostPopularProduct ? $mostPopularProduct->product_name : 'N/A',
            'average_order_value' => round($averageOrderValue, 2),
            'sales_by_category' => $salesByCategory,
            'daily_sales' => $dailySales,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ]);
    }
}

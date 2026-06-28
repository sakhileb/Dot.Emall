<?php

use App\Http\Controllers\Auth\EcosystemAuthController;
use Illuminate\Support\Facades\Route;


Route::get('/auth/ecosystem', [EcosystemAuthController::class, 'handle'])->name('ecosystem.auth');
Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $userId = auth()->id();

        // Stores owned by the current user
        $stores = \App\Models\Store::where('owner_id', $userId)
            ->withCount('products')
            ->get();

        $storeIds = $stores->pluck('id');

        // Products belonging to those stores
        $productIds = \App\Models\Product::whereIn('store_id', $storeIds)->pluck('id');

        // Orders that contain at least one of this seller's products
        $orderIds = \App\Models\OrderItem::whereIn('product_id', $productIds)
            ->pluck('order_id')
            ->unique()
            ->values();

        $totalProducts  = $productIds->count();
        $totalOrders    = $orderIds->count();
        $totalRevenue   = \App\Models\Order::whereIn('id', $orderIds)
            ->where('status', 'delivered')
            ->sum('total');
        $pendingOrders  = \App\Models\Order::whereIn('id', $orderIds)
            ->where('status', 'pending')
            ->count();
        $recentOrders   = \App\Models\Order::whereIn('id', $orderIds)
            ->with(['user', 'items.product.store'])
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'stores', 'totalProducts', 'totalOrders',
            'totalRevenue', 'pendingOrders', 'recentOrders'
        ));
    })->name('dashboard');
});

<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Customer\AddressController as CustomerAddressController;
use App\Http\Controllers\Api\V1\Customer\CartController as CustomerCartController;
use App\Http\Controllers\Api\V1\Customer\CategoryController as CustomerCategoryController;
use App\Http\Controllers\Api\V1\Customer\CheckoutController as CustomerCheckoutController;
use App\Http\Controllers\Api\V1\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\V1\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Api\V1\Driver\DriverDashboardController;
use App\Http\Controllers\Api\V1\Driver\DriverOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register/customer', [AuthController::class, 'registerCustomer']);
    Route::post('/auth/register/driver', [AuthController::class, 'registerDriver']);

    Route::get('/categories', [CustomerCategoryController::class, 'index']);
    Route::get('/categories/{category}', [CustomerCategoryController::class, 'show']);

    Route::get('/products', [CustomerProductController::class, 'index']);
    Route::get('/products/featured', [CustomerProductController::class, 'featured']);
    Route::get('/products/{product}', [CustomerProductController::class, 'show']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Customer routes
        Route::middleware('role:customer')->prefix('customer')->group(function () {
            // Addresses
            Route::get('/addresses', [CustomerAddressController::class, 'index']);
            Route::post('/addresses', [CustomerAddressController::class, 'store']);
            Route::put('/addresses/{address}', [CustomerAddressController::class, 'update']);
            Route::delete('/addresses/{address}', [CustomerAddressController::class, 'destroy']);
            Route::post('/addresses/{address}/default', [CustomerAddressController::class, 'setDefault']);

            // Cart
            Route::get('/cart', [CustomerCartController::class, 'show']);
            Route::post('/cart/add', [CustomerCartController::class, 'add']);
            Route::put('/cart/items/{item}', [CustomerCartController::class, 'updateQuantity']);
            Route::delete('/cart/items/{item}', [CustomerCartController::class, 'remove']);

            // Checkout
            Route::post('/checkout', [CustomerCheckoutController::class, 'store']);

            // Orders
            Route::get('/orders', [CustomerOrderController::class, 'index']);
            Route::get('/orders/{order}', [CustomerOrderController::class, 'show']);
            Route::get('/orders/{order}/track', [CustomerOrderController::class, 'track']);
            Route::post('/orders/{order}/rate-driver', [CustomerOrderController::class, 'rateDriver']);

            // Product Reviews
            Route::post('/products/{product}/reviews', [CustomerProductController::class, 'storeReview']);
        });

        // Driver routes (must be driver and approved)
        Route::middleware(['role:driver', 'approved'])->prefix('driver')->group(function () {
            Route::get('/dashboard', [DriverDashboardController::class, 'index']);

            // Assignments
            Route::post('/assignments/{assignment}/accept', [DriverOrderController::class, 'acceptAssignment']);
            Route::post('/assignments/{assignment}/reject', [DriverOrderController::class, 'rejectAssignment']);

            // Deliveries / Location / Progress
            Route::post('/deliveries/{delivery}/emit-location', [DriverOrderController::class, 'emitLocation']);
            Route::post('/deliveries/{delivery}/complete', [DriverOrderController::class, 'completeDelivery']);
            Route::post('/deliveries/{delivery}/client-reject', [DriverOrderController::class, 'clientReject']);

            Route::get('/history', [DriverOrderController::class, 'history']);
        });

        // Seller routes (must be seller and approved)
        Route::middleware(['role:seller,super_admin', 'approved'])->prefix('seller')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\V1\Seller\SellerDashboardController::class, 'index']);
            Route::get('/orders', [\App\Http\Controllers\Api\V1\Seller\SellerDashboardController::class, 'orders']);
            Route::post('/orders/{order}/status', [\App\Http\Controllers\Api\V1\Seller\SellerOrderController::class, 'updateStatus']);
            Route::post('/orders/{order}/assign-driver', [\App\Http\Controllers\Api\V1\Seller\SellerOrderController::class, 'assignDriver']);
            Route::get('/products', [\App\Http\Controllers\Api\V1\Seller\SellerProductController::class, 'index']);
            Route::post('/products/{product}/toggle', [\App\Http\Controllers\Api\V1\Seller\SellerProductController::class, 'toggleStatus']);
        });

        // Admin routes (must be super_admin)
        Route::middleware(['role:super_admin'])->prefix('admin')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\V1\Admin\AdminDashboardController::class, 'index']);
            
            // Applications
            Route::get('/drivers', [\App\Http\Controllers\Api\V1\Admin\AdminApplicationController::class, 'drivers']);
            Route::get('/businesses', [\App\Http\Controllers\Api\V1\Admin\AdminApplicationController::class, 'businesses']);
            
            // Approvals
            Route::post('/applications/driver/{driverProfile}/approve', [\App\Http\Controllers\Api\V1\Admin\AdminApplicationController::class, 'approveDriver']);
            Route::post('/applications/driver/{driverProfile}/reject', [\App\Http\Controllers\Api\V1\Admin\AdminApplicationController::class, 'rejectDriver']);
            Route::post('/applications/business/{business}/approve', [\App\Http\Controllers\Api\V1\Admin\AdminApplicationController::class, 'approveBusiness']);
            Route::post('/applications/business/{business}/reject', [\App\Http\Controllers\Api\V1\Admin\AdminApplicationController::class, 'rejectBusiness']);
            
            // Toggle
            Route::post('/businesses/{business}/toggle', [\App\Http\Controllers\Api\V1\Admin\AdminApplicationController::class, 'toggleBusiness']);
        });
    });
});

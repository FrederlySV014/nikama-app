<?php

use App\Http\Controllers\Admin\AdminApplicationController;
use App\Http\Controllers\Admin\AdminBusinessController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDistrictController;
use App\Http\Controllers\Admin\AdminFinancialController;
use App\Http\Controllers\Admin\AdminMarketingController;
use App\Http\Controllers\Admin\AdminPaymentSettingsController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSystemSettingsController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\DriverAuthController;
use App\Http\Controllers\Auth\SellerAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Driver\DriverOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PublicCategoryController;
use App\Http\Controllers\PublicProductController;
use App\Http\Controllers\Seller\SellerComboController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas / Estáticas
Route::get('/', [WelcomeController::class, 'index'])->name('public.welcome');

Route::get('/categorias/{categoryPath}', [PublicCategoryController::class, 'show'])
    ->where('categoryPath', '.*')
    ->name('public.category.show');

Route::get('/productos/{slug}', [PublicProductController::class, 'show'])
    ->name('public.product.show');

Route::post('/productos/{product}/reviews', [PublicProductController::class, 'storeReview'])
    ->middleware(['auth'])
    ->name('public.product.review.store');

Route::get('/about-us', function () {
    return view('public.about-us');
})->name('public.about-us');

// Rutas de Autenticación y Registro Separadas por Rol
Route::middleware('guest')->group(function () {
    // Clientes (Customer)
    Route::get('/auth/login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/login', [CustomerAuthController::class, 'login'])->name('login.post');
    Route::get('/auth/register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/auth/register', [CustomerAuthController::class, 'register'])->name('register.post');

    // Vendedores (Seller)
    Route::get('/auth/seller-login', [SellerAuthController::class, 'showLogin'])->name('seller.login');
    Route::post('/auth/seller-login', [SellerAuthController::class, 'login'])->name('seller.login.post');
    Route::get('/auth/seller-register', [SellerAuthController::class, 'showRegister'])->name('seller.register');
    Route::post('/auth/seller-register', [SellerAuthController::class, 'register'])->name('seller.register.post');

    // Repartidores (Driver)
    Route::get('/auth/driver-login', [DriverAuthController::class, 'showLogin'])->name('driver.login');
    Route::post('/auth/driver-login', [DriverAuthController::class, 'login'])->name('driver.login.post');
    Route::get('/auth/driver-register', [DriverAuthController::class, 'showRegister'])->name('driver.register');
    Route::post('/auth/driver-register', [DriverAuthController::class, 'register'])->name('driver.register.post');

    // Administración (Super Admin)
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
});

Route::get('/cart/json', [CartController::class, 'getJson'])->name('cart.json');

// Rutas Autenticadas Comunes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

    // Página informativa de estado pendiente/rechazado/suspendido (sin el middleware 'approved' para evitar bucles)
    Route::get('/auth/pending-review', [DashboardController::class, 'pendingReview'])
        ->name('auth.pending-review');

    // Rutas de Checkout y Pagos
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/order/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');

    // Rutas de Pedidos y Seguimiento
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/location', [OrderController::class, 'location'])->name('orders.location');
    Route::post('/orders/{order}/simulate-status', [OrderController::class, 'simulateStatus'])->name('orders.simulateStatus');
    Route::post('/orders/{order}/rate-driver', [OrderController::class, 'rateDriver'])->name('orders.rate-driver');

    // Rutas del Carrito
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/items/{item}/quantity', [CartController::class, 'updateQuantity'])->name('cart.quantity.update');
    Route::delete('/cart/items/{item}', [CartController::class, 'remove'])->name('cart.remove');

    // Rutas de Direcciones de Entrega
    Route::get('/profile/addresses', [CustomerAddressController::class, 'index'])->name('profile.addresses.index');
    Route::get('/profile/addresses/create', [CustomerAddressController::class, 'create'])->name('profile.addresses.create');
    Route::post('/profile/addresses', [CustomerAddressController::class, 'store'])->name('profile.addresses.store');
    Route::get('/profile/addresses/{address}/edit', [CustomerAddressController::class, 'edit'])->name('profile.addresses.edit');
    Route::put('/profile/addresses/{address}', [CustomerAddressController::class, 'update'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{address}', [CustomerAddressController::class, 'destroy'])->name('profile.addresses.destroy');
    Route::post('/profile/addresses/{address}/default', [CustomerAddressController::class, 'setDefault'])->name('profile.addresses.default');
});

// Rutas de Negocios (Sellers) - Protegidas por Rol y Aprobación
Route::middleware(['auth', 'role:seller,super_admin', 'approved'])
    ->prefix('seller')
    ->name('seller.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'seller'])->name('dashboard');
        Route::get('/orders/pending-count', [DashboardController::class, 'pendingCount'])->name('orders.pending-count');

        // Gestión de Productos para Sellers
        Route::resource('products', SellerProductController::class);
        Route::post('/products/{product}/toggle', [SellerProductController::class, 'toggleStatus'])->name('products.toggle');

        // Gestión de Combos para Sellers
        Route::resource('combos', SellerComboController::class);
        Route::post('/combos/{combo}/toggle', [SellerComboController::class, 'toggleStatus'])->name('combos.toggle');

        // Gestión de Pedidos para Sellers
        Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('/orders/{order}/assign-driver', [SellerOrderController::class, 'assignDriver'])->name('orders.assignDriver');
    });

// Rutas de Repartidores (Drivers) - Protegidas por Rol y Aprobación
Route::middleware(['auth', 'role:driver,super_admin', 'approved'])
    ->prefix('driver')
    ->name('driver.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'driver'])->name('dashboard');
        Route::get('/assignments/pending-count', [DashboardController::class, 'driverPendingCount'])->name('assignments.pending-count');

        // Asignaciones de Reparto
        Route::post('/assignments/{assignment}/accept', [DriverOrderController::class, 'acceptAssignment'])->name('assignments.accept');
        Route::post('/assignments/{assignment}/reject', [DriverOrderController::class, 'rejectAssignment'])->name('assignments.reject');

        // Envíos y Emisión de Coordenadas
        Route::get('/deliveries/{delivery}', [DriverOrderController::class, 'showDelivery'])->name('deliveries.show');
        Route::post('/deliveries/{delivery}/emit-location', [DriverOrderController::class, 'emitLocation'])->name('deliveries.emitLocation');
        Route::post('/deliveries/{delivery}/complete', [DriverOrderController::class, 'completeDelivery'])->name('deliveries.complete');
        Route::post('/deliveries/{delivery}/client-reject', [DriverOrderController::class, 'clientReject'])->name('deliveries.client-reject');
        Route::get('/history', [DriverOrderController::class, 'history'])->name('history');
    });

// Rutas de Administración (Super Admin)
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/dashboard/pending-applications', [DashboardController::class, 'adminPendingApplications'])->name('dashboard.pending-applications');

        // Gestión de Solicitudes (Aprobaciones)
        Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/seller/{business}', [AdminApplicationController::class, 'showSeller'])->name('applications.seller.show');
        Route::get('/applications/driver/{driverProfile}', [AdminApplicationController::class, 'showDriver'])->name('applications.driver.show');
        Route::post('/applications/seller/{business}/approve', [AdminApplicationController::class, 'approveSeller'])->name('applications.seller.approve');
        Route::post('/applications/seller/{business}/reject', [AdminApplicationController::class, 'rejectSeller'])->name('applications.seller.reject');
        Route::post('/applications/driver/{driverProfile}/approve', [AdminApplicationController::class, 'approveDriver'])->name('applications.driver.approve');
        Route::post('/applications/driver/{driverProfile}/reject', [AdminApplicationController::class, 'rejectDriver'])->name('applications.driver.reject');

        // Gestión de Productos por el Superadmin
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::post('/products/{product}/toggle', [AdminProductController::class, 'toggleStatus'])->name('products.toggle');
        Route::post('/products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggle-featured');

        // Gestión de Categorías Generales
        Route::resource('categories', AdminCategoryController::class);
        Route::post('/categories/{category}/toggle', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle');

        // Configuración de Métodos de Pago
        Route::get('/settings/payments', [AdminPaymentSettingsController::class, 'edit'])->name('settings.payments.edit');
        Route::post('/settings/payments', [AdminPaymentSettingsController::class, 'update'])->name('settings.payments.update');

        // Configuración de Distritos de Cobertura
        Route::get('/settings/districts', [AdminDistrictController::class, 'edit'])->name('settings.districts.edit');
        Route::post('/settings/districts', [AdminDistrictController::class, 'update'])->name('settings.districts.update');

        // Gestión de Usuarios
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Gestión de Negocios
        Route::get('/businesses', [AdminBusinessController::class, 'index'])->name('businesses.index');
        Route::get('/businesses/{business}', [AdminBusinessController::class, 'show'])->name('businesses.show');
        Route::post('/businesses/{business}/toggle-active', [AdminBusinessController::class, 'toggleActive'])->name('businesses.toggle-active');
        Route::post('/businesses/{business}/toggle-featured', [AdminBusinessController::class, 'toggleFeatured'])->name('businesses.toggle-featured');
        Route::post('/businesses/{business}/toggle-accepts-orders', [AdminBusinessController::class, 'toggleAcceptsOrders'])->name('businesses.toggle-accepts-orders');
        Route::post('/businesses/{business}/toggle-suspension', [AdminBusinessController::class, 'toggleSuspension'])->name('businesses.toggle-suspension');

        // Módulo Financiero
        Route::get('/financial/payouts', [AdminFinancialController::class, 'payouts'])->name('financial.payouts');
        Route::post('/financial/payouts/{type}/{id}/process', [AdminFinancialController::class, 'processPayout'])->name('financial.payouts.process');
        Route::get('/financial/commissions', [AdminFinancialController::class, 'commissions'])->name('financial.commissions');
        Route::post('/financial/commissions', [AdminFinancialController::class, 'storeCommission'])->name('financial.commissions.store');
        Route::post('/financial/commissions/{commission}/toggle', [AdminFinancialController::class, 'toggleCommissionStatus'])->name('financial.commissions.toggle');
        Route::get('/financial/transactions', [AdminFinancialController::class, 'walletTransactions'])->name('financial.transactions');

        // Módulo de Marketing
        Route::get('/marketing/banners', [AdminMarketingController::class, 'banners'])->name('marketing.banners');
        Route::post('/marketing/banners', [AdminMarketingController::class, 'storeBanner'])->name('marketing.banners.store');
        Route::post('/marketing/banners/{banner}/toggle', [AdminMarketingController::class, 'toggleBannerStatus'])->name('marketing.banners.toggle');
        Route::put('/marketing/banners/{banner}', [AdminMarketingController::class, 'updateBanner'])->name('marketing.banners.update');
        Route::delete('/marketing/banners/{banner}', [AdminMarketingController::class, 'destroyBanner'])->name('marketing.banners.destroy');
        Route::get('/marketing/discounts', [AdminMarketingController::class, 'discounts'])->name('marketing.discounts');
        Route::post('/marketing/discounts', [AdminMarketingController::class, 'storeDiscount'])->name('marketing.discounts.store');
        Route::post('/marketing/discounts/{discount}/toggle', [AdminMarketingController::class, 'toggleDiscountStatus'])->name('marketing.discounts.toggle');
        Route::put('/marketing/discounts/{discount}', [AdminMarketingController::class, 'updateDiscount'])->name('marketing.discounts.update');
        Route::delete('/marketing/discounts/{discount}', [AdminMarketingController::class, 'destroyDiscount'])->name('marketing.discounts.destroy');

        // Módulo de Configuración y Auditoría
        Route::get('/system/settings', [AdminSystemSettingsController::class, 'edit'])->name('system.settings.edit');
        Route::post('/system/settings', [AdminSystemSettingsController::class, 'update'])->name('system.settings.update');
        Route::get('/system/audit-logs', [AdminSystemSettingsController::class, 'auditLogs'])->name('system.audit-logs');
    });

<?php

use App\Http\Controllers\Admin\AdminApplicationController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\DriverAuthController;
use App\Http\Controllers\Auth\SellerAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicCategoryController;
use App\Http\Controllers\PublicProductController;
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

// Rutas Autenticadas Comunes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

    // Página informativa de estado pendiente/rechazado/suspendido (sin el middleware 'approved' para evitar bucles)
    Route::get('/auth/pending-review', [DashboardController::class, 'pendingReview'])
        ->name('auth.pending-review');
});

// Rutas de Negocios (Sellers) - Protegidas por Rol y Aprobación
Route::middleware(['auth', 'role:seller,super_admin', 'approved'])
    ->prefix('seller')
    ->name('seller.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'seller'])->name('dashboard');

        // Gestión de Productos para Sellers
        Route::resource('products', SellerProductController::class);
        Route::post('/products/{product}/toggle', [SellerProductController::class, 'toggleStatus'])->name('products.toggle');
    });

// Rutas de Repartidores (Drivers) - Protegidas por Rol y Aprobación
Route::middleware(['auth', 'role:driver,super_admin', 'approved'])
    ->prefix('driver')
    ->name('driver.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'driver'])->name('dashboard');
    });

// Rutas de Administración (Super Admin)
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // Gestión de Solicitudes (Aprobaciones)
        Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/seller/{business}', [AdminApplicationController::class, 'showSeller'])->name('applications.seller.show');
        Route::get('/applications/driver/{driverProfile}', [AdminApplicationController::class, 'showDriver'])->name('applications.driver.show');
        Route::post('/applications/seller/{business}/approve', [AdminApplicationController::class, 'approveSeller'])->name('applications.seller.approve');
        Route::post('/applications/seller/{business}/reject', [AdminApplicationController::class, 'rejectSeller'])->name('applications.seller.reject');
        Route::post('/applications/driver/{driverProfile}/approve', [AdminApplicationController::class, 'approveDriver'])->name('applications.driver.approve');
        Route::post('/applications/driver/{driverProfile}/reject', [AdminApplicationController::class, 'rejectDriver'])->name('applications.driver.reject');

        // Gestión de Categorías Generales
        Route::resource('categories', AdminCategoryController::class);
        Route::post('/categories/{category}/toggle', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle');
    });

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\SubcategoriaController;
use App\Http\Controllers\Api\ProductoController;

// Autenticación
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Rutas públicas (solo lectura)
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{id}', [CategoriaController::class, 'show']);

Route::get('/subcategorias', [SubcategoriaController::class, 'index']);
Route::get('/subcategorias/{id}', [SubcategoriaController::class, 'show']);
Route::get('/subcategorias/categoria/{categoria_id}', [SubcategoriaController::class, 'byCategoria']);

Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);
Route::get('/productos/subcategoria/{subcategoria_id}', [ProductoController::class, 'bySubcategoria']);

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Categorías - CRUD completo
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::put('/categorias/{id}', [CategoriaController::class, 'update']);
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);

    // Subcategorías - CRUD completo
    Route::post('/subcategorias', [SubcategoriaController::class, 'store']);
    Route::put('/subcategorias/{id}', [SubcategoriaController::class, 'update']);
    Route::delete('/subcategorias/{id}', [SubcategoriaController::class, 'destroy']);

    // Productos - CRUD completo
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::put('/productos/{id}', [ProductoController::class, 'update']);
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
});
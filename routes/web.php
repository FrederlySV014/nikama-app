<?php

use App\Models\Categoria;
use App\Models\Usuario;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.welcome');
})->name('public.welcome');

Route::get('/vendor', function () {
    return view('vendor.dashboard');
});

Route::get('/vendor/register', function () {
    return view('public.vendor.register');
})->name('vendor.register');

Route::get('/admin', function () {
    return view('admin.dashboard');
});

Route::get('/test-api', function () {
    $categorias = Categoria::with('subcategorias.productos')->active()->get();

    return response()->json([
        'categorias' => $categorias,
        'usuarios' => Usuario::select('id', 'email', 'nombre_completo', 'rol', 'is_active')->get(),
        'status' => 'ok',
    ]);
});

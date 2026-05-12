<?php

use Illuminate\Support\Facades\Route;
use App\Models\Categoria;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-api', function () {
    $categorias = Categoria::with('subcategorias.productos')->active()->get();
    return response()->json([
        'categorias' => $categorias,
        'usuarios' => App\Models\Usuario::select('id', 'email', 'nombre_completo', 'rol', 'is_active')->get(),
        'status' => 'ok'
    ]);
});

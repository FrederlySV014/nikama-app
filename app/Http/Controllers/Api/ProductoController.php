<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    public function index(): JsonResponse
    {
        $productos = Producto::active()
            ->with('subcategoria.categoria')
            ->orderBy('nombre')
            ->get();
        return response()->json($productos);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subcategoria_id' => 'required|uuid|exists:subcategorias,id',
            'codigo_sku' => 'required|string|max:100|unique:productos,codigo_sku',
            'codigo_barras' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'peso_gramos' => 'nullable|integer',
            'garantia_meses' => 'nullable|integer',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0',
            'stock_actual' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
            'estado_producto' => 'nullable|in:nuevo,reacondicionado,outlet',
            'is_available' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'imagen_url' => 'nullable|string',
            'imagenes_extra' => 'nullable|array',
        ]);

        $producto = Producto::create($validated);
        return response()->json($producto, 201);
    }

    public function show(string $id): JsonResponse
    {
        $producto = Producto::with('subcategoria.categoria')->findOrFail($id);
        return response()->json($producto);
    }

    public function bySubcategoria(string $subcategoria_id): JsonResponse
    {
        $productos = Producto::active()
            ->where('subcategoria_id', $subcategoria_id)
            ->orderBy('nombre')
            ->get();
        return response()->json($productos);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'subcategoria_id' => 'sometimes|uuid|exists:subcategorias,id',
            'codigo_sku' => 'sometimes|string|max:100|unique:productos,codigo_sku,' . $id,
            'codigo_barras' => 'nullable|string|max:50',
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'peso_gramos' => 'nullable|integer',
            'garantia_meses' => 'nullable|integer',
            'precio_compra' => 'sometimes|numeric|min:0',
            'precio_venta' => 'sometimes|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0',
            'stock_actual' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
            'estado_producto' => 'nullable|in:nuevo,reacondicionado,outlet',
            'is_available' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'imagen_url' => 'nullable|string',
            'imagenes_extra' => 'nullable|array',
        ]);

        $producto->update($validated);
        return response()->json($producto);
    }

    public function destroy(string $id): JsonResponse
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return response()->json(['message' => 'Producto eliminado']);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    public function index(): JsonResponse
    {
        $categorias = Categoria::active()->orderBy('orden')->get();
        return response()->json($categorias);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|string',
            'orden' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $categoria = Categoria::create($validated);
        return response()->json($categoria, 201);
    }

    public function show(string $id): JsonResponse
    {
        $categoria = Categoria::with('subcategorias')->findOrFail($id);
        return response()->json($categoria);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $categoria = Categoria::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|string',
            'orden' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $categoria->update($validated);
        return response()->json($categoria);
    }

    public function destroy(string $id): JsonResponse
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();
        return response()->json(['message' => 'Categoría eliminada']);
    }
}
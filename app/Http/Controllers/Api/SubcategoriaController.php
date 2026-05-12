<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubcategoriaController extends Controller
{
    public function index(): JsonResponse
    {
        $subcategorias = Subcategoria::active()->with('categoria')->orderBy('orden')->get();
        return response()->json($subcategorias);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'categoria_id' => 'required|uuid|exists:categorias,id',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|string',
            'orden' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $subcategoria = Subcategoria::create($validated);
        return response()->json($subcategoria, 201);
    }

    public function show(string $id): JsonResponse
    {
        $subcategoria = Subcategoria::with('categoria', 'productos')->findOrFail($id);
        return response()->json($subcategoria);
    }

    public function byCategoria(string $categoria_id): JsonResponse
    {
        $subcategorias = Subcategoria::active()
            ->where('categoria_id', $categoria_id)
            ->orderBy('orden')
            ->get();
        return response()->json($subcategorias);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $subcategoria = Subcategoria::findOrFail($id);

        $validated = $request->validate([
            'categoria_id' => 'sometimes|uuid|exists:categorias,id',
            'nombre' => 'sometimes|string|max:100',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|string',
            'orden' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $subcategoria->update($validated);
        return response()->json($subcategoria);
    }

    public function destroy(string $id): JsonResponse
    {
        $subcategoria = Subcategoria::findOrFail($id);
        $subcategoria->delete();
        return response()->json(['message' => 'Subcategoría eliminada']);
    }
}
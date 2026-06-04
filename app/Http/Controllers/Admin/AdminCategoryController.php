<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    /**
     * Mostrar la lista de categorías con métricas, búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status', 'all');
        $level = $request->query('level', 'all');
        $parentFilter = $request->query('parent', 'all');

        // Métricas rápidas para el dashboard superior (excluyendo soft-deleted por defecto)
        $totalCategoriesCount = Category::count();
        $activeCategoriesCount = Category::where('is_active', true)->count();
        $inactiveCategoriesCount = Category::where('is_active', false)->count();
        $rootCategoriesCount = Category::whereNull('parent_id')->count();
        $childCategoriesCount = Category::whereNotNull('parent_id')->count();

        // Consulta principal con relaciones para evitar N+1
        $query = Category::with(['parent', 'children'])
            ->withCount(['businesses', 'products', 'children']);

        // Filtro por búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        // Filtro por nivel jerárquico
        if ($level === 'root') {
            $query->whereNull('parent_id');
        } elseif ($level === 'child') {
            $query->whereNotNull('parent_id');
        }

        // Filtro por categoría padre específica
        if ($parentFilter !== 'all' && $parentFilter !== null) {
            $query->where('parent_id', $parentFilter);
        }

        // Si no hay búsqueda o filtros específicos de nivel/padre,
        // por defecto mostramos la estructura híbrida (mostrando raíces primero)
        if (!$search && $level === 'all' && $parentFilter === 'all') {
            // Ordenar por orden personalizado y raíces primero
            $query->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
                ->orderBy('sort_order')
                ->orderBy('name');
        } else {
            $query->orderBy('sort_order')->orderBy('name');
        }

        $categories = $query->paginate(20)->withQueryString();

        // Listado de todas las categorías activas para el filtro de categoría padre en la vista
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact(
            'categories',
            'search',
            'status',
            'level',
            'parentFilter',
            'totalCategoriesCount',
            'activeCategoriesCount',
            'inactiveCategoriesCount',
            'rootCategoriesCount',
            'childCategoriesCount',
            'parentCategories'
        ));
    }

    /**
     * Mostrar el formulario para crear una nueva categoría.
     */
    public function create(): View
    {
        // Obtener posibles categorías padres (generalmente raíces activas)
        $parentCategories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Guardar una categoría recién creada.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $category = Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "La categoría '{$category->name}' ha sido creada exitosamente.");
    }

    /**
     * Mostrar los detalles de una categoría.
     */
    public function show(Category $category): View
    {
        $category->load(['parent', 'children'])
            ->loadCount(['businesses', 'products']);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Mostrar el formulario para editar una categoría.
     */
    public function edit(Category $category): View
    {
        // Obtener todos los descendientes para excluirlos y evitar circularidad
        $descendantsIds = $this->getDescendantIds($category);
        $excludeIds = array_merge([$category->id], $descendantsIds);

        // Listar categorías candidatas a padre excluyendo la propia y sus descendientes
        $parentCategories = Category::whereNotIn('id', $excludeIds)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Actualizar una categoría existente.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        
        // Validación manual de circularidad adicional por seguridad
        if ($request->filled('parent_id')) {
            $parentId = $request->input('parent_id');
            $descendantsIds = $this->getDescendantIds($category);

            if ($parentId === $category->id || in_array($parentId, $descendantsIds)) {
                return back()
                    ->withErrors(['parent_id' => 'No puedes seleccionar la propia categoría o uno de sus descendientes como categoría padre.'])
                    ->withInput();
            }
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "La categoría '{$category->name}' ha sido actualizada exitosamente.");
    }

    /**
     * Eliminar (soft-delete) una categoría si cumple con los requisitos de integridad.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Regla 1: Bloquear si tiene categorías hijas (activas o inactivas)
        if ($category->children()->count() > 0) {
            return back()->with('error', "No se puede eliminar la categoría '{$category->name}' porque contiene subcategorías asociadas. Debes reasignar o eliminar las subcategorías primero.");
        }

        // Regla 2: Bloquear si tiene negocios asociados
        if ($category->businesses()->count() > 0) {
            return back()->with('error', "No se puede eliminar la categoría '{$category->name}' porque está asociada a uno o más negocios activos.");
        }

        // Regla 3: Bloquear si tiene productos asociados
        if ($category->products()->count() > 0) {
            return back()->with('error', "No se puede eliminar la categoría '{$category->name}' porque tiene productos asociados.");
        }

        // Si pasa todas las validaciones de integridad, se realiza el soft-delete
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "La categoría '{$category->name}' ha sido eliminada correctamente.");
    }

    /**
     * Alternar el estado activo/inactivo de una categoría.
     */
    public function toggleStatus(Request $request, Category $category): RedirectResponse
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        $estado = $category->is_active ? 'activada' : 'desactivada';

        return back()->with('success', "La categoría '{$category->name}' ha sido {$estado} con éxito.");
    }

    /**
     * Obtener los IDs de todos los descendientes de una categoría de forma recursiva.
     *
     * @param  \App\Models\Category  $category
     * @return array<int, string>
     */
    private function getDescendantIds(Category $category): array
    {
        $ids = [];
        
        // Eager load children to avoid nested queries
        $category->load('children');

        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }

        return $ids;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Mostrar la página de bienvenida con las categorías raíz activas y negocios aprobados.
     */
    public function index(): View
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->select(['id', 'name', 'slug', 'icon', 'image_url']) // Optimización: sólo cargar campos requeridos
            ->get();

        $businesses = Business::where('status', Business::STATUS_APPROVED)
            ->where('is_active', true)
            ->with(['categories' => function ($q) {
                $q->select(['categories.id', 'categories.name']);
            }])
            ->orderBy('business_name')
            ->select(['id', 'business_name', 'slug', 'logo_url'])
            ->get();

        return view('public.welcome', compact('categories', 'businesses'));
    }
}

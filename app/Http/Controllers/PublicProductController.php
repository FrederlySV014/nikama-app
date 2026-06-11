<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Product;
use App\Models\ProductCombo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicProductController extends Controller
{
    /**
     * Display the specified product.
     */
    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->where('status', Product::STATUS_ACTIVE)
            ->where('is_available', true)
            ->whereHas('business', function ($q) {
                $q->where('status', Business::STATUS_APPROVED)
                    ->where('is_active', true);
            })
            ->firstOrFail();

        $product->load([
            'business.locations.hours',
            'categories',
            'images',
            'reviews' => function ($q) {
                $q->where('is_visible', true)
                    ->orderBy('created_at', 'desc')
                    ->with('user');
            },
        ]);

        // Fetch up to 4 related products in the same category or business (excluding current product)
        $categoryIds = $product->categories->pluck('id')->toArray();
        $relatedProducts = Product::where('status', Product::STATUS_ACTIVE)
            ->where('is_available', true)
            ->where('id', '!=', $product->id)
            ->whereHas('business', function ($q) {
                $q->where('status', Business::STATUS_APPROVED)
                    ->where('is_active', true);
            })
            ->where(function ($query) use ($product, $categoryIds) {
                $query->where('business_id', $product->business_id)
                    ->orWhereHas('categories', function ($cq) use ($categoryIds) {
                        $cq->whereIn('categories.id', $categoryIds);
                    });
            })
            ->with(['business', 'categories'])
            ->limit(4)
            ->get();

        $combos = ProductCombo::where('is_active', true)
            ->whereHas('products', function ($q) use ($product) {
                $q->where('products.id', $product->id);
            })
            ->with(['business', 'products'])
            ->get();

        return view('public.products.show', compact('product', 'relatedProducts', 'combos'));
    }

    /**
     * Store a newly created review in storage.
     */
    public function storeReview(Request $request, Product $product): RedirectResponse
    {
        // 1. Validate data
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Debes seleccionar una calificación de estrellas.',
            'rating.integer' => 'Calificación no válida.',
            'rating.min' => 'La calificación mínima es 1 estrella.',
            'rating.max' => 'La calificación máxima es 5 estrellas.',
            'comment.max' => 'El comentario no puede exceder los 1000 caracteres.',
        ]);

        // 2. Prevent duplicate reviews by the same user on this product
        $exists = $product->reviews()->where('user_id', auth()->id())->exists();
        if ($exists) {
            return back()->with('error', 'Ya has enviado una opinión sobre este producto anteriormente.');
        }

        // 3. Create the review
        $product->reviews()->create([
            'id' => Str::uuid()->toString(),
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_visible' => true,
            'is_verified_purchase' => false,
        ]);

        // 4. Recalculate average rating & total reviews for the product
        $product->rating_average = (float) ($product->reviews()->where('is_visible', true)->avg('rating') ?? 0);
        $product->total_reviews = (int) $product->reviews()->where('is_visible', true)->count();
        $product->save();

        return back()->with('success', '¡Gracias por compartir tu calificación y comentario! Tu opinión es de gran ayuda para la comunidad.');
    }
}

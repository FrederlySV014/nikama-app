<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Business;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Get a list of active products with pagination and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::where('status', Product::STATUS_ACTIVE)
            ->where('is_available', true)
            ->whereHas('business', function ($q) {
                $q->where('status', Business::STATUS_APPROVED)
                    ->where('is_active', true);
            })
            ->with(['business', 'categories', 'images']);

        // Filter by search query
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Filter by business
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        // Filter by price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating_desc':
                $query->orderBy('rating_average', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate($request->input('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    /**
     * Get details of a single product.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::where('id', $id)
            ->where('status', Product::STATUS_ACTIVE)
            ->where('is_available', true)
            ->whereHas('business', function ($q) {
                $q->where('status', Business::STATUS_APPROVED)
                    ->where('is_active', true);
            })
            ->with(['business', 'categories', 'images', 'optionGroups.options'])
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Get a list of featured products.
     */
    public function featured(Request $request): JsonResponse
    {
        $products = Product::where('status', Product::STATUS_ACTIVE)
            ->where('is_available', true)
            ->where('is_featured', true)
            ->whereHas('business', function ($q) {
                $q->where('status', Business::STATUS_APPROVED)
                    ->where('is_active', true);
            })
            ->with(['business', 'categories', 'images'])
            ->limit($request->input('limit', 8))
            ->get();

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * Store a product review.
     */
    public function storeReview(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $exists = $product->reviews()->where('user_id', $request->user()->id)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has enviado una opinión sobre este producto anteriormente.',
            ], 400);
        }

        $product->reviews()->create([
            'id' => Str::uuid()->toString(),
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment ?? null,
            'is_visible' => true,
            'is_verified_purchase' => false,
        ]);

        // Recalculate average and total reviews
        $product->rating_average = (float) ($product->reviews()->where('is_visible', true)->avg('rating') ?? 0);
        $product->total_reviews = (int) $product->reviews()->where('is_visible', true)->count();
        $product->save();

        return response()->json([
            'success' => true,
            'message' => '¡Gracias por compartir tu calificación y comentario!',
            'rating_average' => $product->rating_average,
            'total_reviews' => $product->total_reviews,
        ]);
    }
}

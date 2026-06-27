<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerProductController extends Controller
{
    /**
     * Display a listing of the seller's products.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user->hasRole([Role::SELLER, Role::SUPER_ADMIN])) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización.'], 403);
        }

        $businessIds = $user->businesses()->pluck('businesses.id');

        $query = Product::with(['categories'])
            ->whereIn('business_id', $businessIds);

        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Toggle the product status (Active / Inactive).
     */
    public function toggleStatus(Product $product, Request $request): JsonResponse
    {
        if (! $this->checkSellerAccess($product)) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización para editar este producto.'], 403);
        }

        if ($product->status === Product::STATUS_ACTIVE) {
            $product->status = Product::STATUS_INACTIVE;
            $product->is_available = false;
        } else {
            $product->status = Product::STATUS_ACTIVE;
            $product->is_available = true;
            if (! $product->published_at) {
                $product->published_at = now();
            }
        }
        
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'El estado del producto fue actualizado.',
            'data' => $product->fresh(),
        ]);
    }

    /**
     * Check if the authenticated user has access to manage this product.
     */
    private function checkSellerAccess(Product $product): bool
    {
        $user = auth()->user();
        if ($user->hasRole(Role::SUPER_ADMIN)) return true;
        if (! $user->hasRole(Role::SELLER)) return false;

        return DB::table('business_users')
            ->where('business_id', $product->business_id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }
}

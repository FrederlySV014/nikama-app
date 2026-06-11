<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status', 'all');
        $businessFilter = $request->query('business', 'all');
        $featuredFilter = $request->query('featured', 'all');

        $query = Product::with(['business', 'categories']);

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status !== 'all' && in_array($status, Product::statuses())) {
            $query->where('status', $status);
        }

        // Business filter
        if ($businessFilter !== 'all') {
            $query->where('business_id', $businessFilter);
        }

        // Featured filter
        if ($featuredFilter === 'yes') {
            $query->where('is_featured', true);
        } elseif ($featuredFilter === 'no') {
            $query->where('is_featured', false);
        }

        $products = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $businesses = Business::orderBy('business_name')->get();

        return view('admin.products.index', compact('products', 'businesses', 'search', 'status', 'businessFilter', 'featuredFilter'));
    }

    /**
     * Toggle the status of the specified product.
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update([
            'status' => $product->status === Product::STATUS_ACTIVE ? Product::STATUS_INACTIVE : Product::STATUS_ACTIVE,
            'is_available' => $product->status !== Product::STATUS_ACTIVE,
        ]);

        return redirect()->back()
            ->with('success', 'Estado del producto actualizado correctamente.');
    }

    /**
     * Toggle the featured status of the specified product.
     */
    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update([
            'is_featured' => ! $product->is_featured,
        ]);

        return redirect()->back()
            ->with('success', 'Estado destacado del producto actualizado correctamente.');
    }
}

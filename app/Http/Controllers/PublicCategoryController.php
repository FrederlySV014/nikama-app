<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCategoryController extends Controller
{
    /**
     * Display the specified category and its products.
     */
    public function show(Request $request, string $categoryPath): View
    {
        // 1. Get the last slug segment representing the target category
        $pathSegments = explode('/', trim($categoryPath, '/'));
        $categorySlug = end($pathSegments);

        $category = Category::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        // 2. Build breadcrumbs
        $breadcrumbs = [];
        $temp = $category;
        while ($temp) {
            array_unshift($breadcrumbs, [
                'name' => $temp->name,
                'url' => route('public.category.show', $temp->url_path),
            ]);
            $temp = $temp->parent;
        }

        // 3. Get direct active subcategories for navigation
        $subcategories = $category->children()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // 4. Get all descendant category IDs (including the current one) to fetch products in cascade
        $categoryIds = $category->getAllDescendantIds();

        // 5. Query active products in these categories
        $query = Product::where('status', Product::STATUS_ACTIVE)
            ->where('is_available', true)
            ->whereHas('business', function ($q) {
                $q->where('status', Business::STATUS_APPROVED)
                  ->where('is_active', true);
            })
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->with(['business', 'categories']);

        // 6. Apply search filter
        $search = $request->query('search');
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // 7. Apply business filter
        $selectedBusinessId = $request->query('business');
        if ($selectedBusinessId && $selectedBusinessId !== 'all') {
            $query->where('business_id', $selectedBusinessId);
        }

        // 8. Apply price range filters
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        if (is_numeric($minPrice)) {
            $query->where('price', '>=', (float) $minPrice);
        }
        if (is_numeric($maxPrice)) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        // 9. Apply sorting
        $sort = $request->query('sort', 'latest');
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

        $products = $query->paginate(12)->withQueryString();

        // 10. Load businesses with active products in these categories for filtering options
        $filterBusinesses = Business::where('status', Business::STATUS_APPROVED)
            ->where('is_active', true)
            ->whereHas('products', function ($q) use ($categoryIds) {
                $q->where('status', Product::STATUS_ACTIVE)
                  ->where('is_available', true)
                  ->whereHas('categories', function ($cq) use ($categoryIds) {
                      $cq->whereIn('categories.id', $categoryIds);
                  });
            })
            ->orderBy('business_name')
            ->select(['id', 'business_name'])
            ->get();

        return view('public.categories.show', compact(
            'category',
            'breadcrumbs',
            'subcategories',
            'products',
            'filterBusinesses',
            'selectedBusinessId',
            'minPrice',
            'maxPrice',
            'sort',
            'search'
        ));
    }
}

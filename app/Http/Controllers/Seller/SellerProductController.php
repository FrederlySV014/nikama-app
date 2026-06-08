<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreProductRequest;
use App\Http\Requests\Seller\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerProductController extends Controller
{
    /**
     * Display a listing of the seller's products.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Product::class);

        $user = $request->user();
        $businessIds = $user->businesses()->pluck('businesses.id');

        $search = $request->query('search');
        $status = $request->query('status', 'all');
        $businessFilter = $request->query('business', 'all');

        $query = Product::with(['business', 'categories'])
            ->whereIn('business_id', $businessIds);

        // Search filter (name or SKU)
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
        if ($businessFilter !== 'all' && $businessIds->contains($businessFilter)) {
            $query->where('business_id', $businessFilter);
        }

        $products = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Load businesses for filtering in view
        $businesses = $user->businesses()->orderBy('business_name')->get();

        return view('seller.products.index', compact('products', 'businesses', 'search', 'status', 'businessFilter'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', Product::class);

        $user = $request->user();
        $businesses = $user->businesses()
            ->where('businesses.is_active', true)
            ->where('business_users.is_active', true)
            ->orderBy('business_name')
            ->get();

        // Only active leaf categories (categories with no subcategories)
        $categories = Category::doesntHave('children')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('seller.products.create', compact('businesses', 'categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        Gate::authorize('create', Product::class);

        $validated = $request->validated();
        $businessId = $validated['business_id'];

        // 1. Generate a unique slug within this business
        $name = $validated['name'];
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $counter = 1;
        while (Product::withTrashed()->where('business_id', $businessId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        // 2. Handle main image upload
        $mainImageUrl = null;
        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products', 'public');
            $mainImageUrl = asset('storage/'.$path);
        }

        // 3. Create product
        $product = Product::create([
            'business_id' => $businessId,
            'name' => $name,
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'sku' => $validated['sku'] ?? null,
            'track_stock' => $request->boolean('track_stock'),
            'stock_quantity' => $request->boolean('track_stock') ? ($validated['stock_quantity'] ?? 0) : 0,
            'allow_backorder' => $request->boolean('allow_backorder'),
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
            'requires_preparation' => $request->boolean('requires_preparation'),
            'preparation_time_minutes' => $validated['preparation_time_minutes'] ?? null,
            'weight_grams' => $validated['weight_grams'] ?? null,
            'main_image_url' => $mainImageUrl,
            'is_available' => $validated['status'] === Product::STATUS_ACTIVE,
            'published_at' => $validated['status'] === Product::STATUS_ACTIVE ? now() : null,
        ]);

        // 4. Sync product categories with manual UUID generation
        if ($request->filled('category_id')) {
            $product->categories()->sync([
                $validated['category_id'] => [
                    'id' => Str::uuid()->toString(),
                    'sort_order' => 0,
                ],
            ]);
        }

        // 5. Handle additional gallery images
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $index => $imageFile) {
                $path = $imageFile->store('products/gallery', 'public');
                $product->images()->create([
                    'id' => Str::uuid()->toString(),
                    'image_url' => asset('storage/'.$path),
                    'alt_text' => $product->name.' Gallery Image '.($index + 1),
                    'is_primary' => false,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        return redirect()->route('seller.products.index')
            ->with('success', "El producto '{$product->name}' se ha creado correctamente.");
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        Gate::authorize('view', $product);

        $product->load(['business', 'categories', 'images']);

        return view('seller.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product, Request $request): View
    {
        Gate::authorize('update', $product);

        $user = $request->user();
        $businesses = $user->businesses()
            ->where('businesses.is_active', true)
            ->where('business_users.is_active', true)
            ->orderBy('business_name')
            ->get();

        $categories = Category::doesntHave('children')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedCategoryId = $product->categories()->first()?->id;

        return view('seller.products.edit', compact('product', 'businesses', 'categories', 'selectedCategoryId'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        Gate::authorize('update', $product);

        $validated = $request->validated();
        $businessId = $validated['business_id'];

        // 1. Generate unique slug if business or name changed
        $name = $validated['name'];
        $slug = $product->slug;
        if ($product->name !== $name || $product->business_id !== $businessId) {
            $slug = Str::slug($name);
            $baseSlug = $slug;
            $counter = 1;
            while (Product::withTrashed()->where('business_id', $businessId)->where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $baseSlug.'-'.$counter;
                $counter++;
            }
        }

        // 2. Handle main image upload
        $mainImageUrl = $product->main_image_url;
        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products', 'public');
            $mainImageUrl = asset('storage/'.$path);
        }

        // 3. Update attributes
        $product->update([
            'business_id' => $businessId,
            'name' => $name,
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'sku' => $validated['sku'] ?? null,
            'track_stock' => $request->boolean('track_stock'),
            'stock_quantity' => $request->boolean('track_stock') ? ($validated['stock_quantity'] ?? 0) : 0,
            'allow_backorder' => $request->boolean('allow_backorder'),
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
            'requires_preparation' => $request->boolean('requires_preparation'),
            'preparation_time_minutes' => $validated['preparation_time_minutes'] ?? null,
            'weight_grams' => $validated['weight_grams'] ?? null,
            'main_image_url' => $mainImageUrl,
            'is_available' => $validated['status'] === Product::STATUS_ACTIVE,
            'published_at' => $validated['status'] === Product::STATUS_ACTIVE ? ($product->published_at ?? now()) : $product->published_at,
        ]);

        // 4. Sync product categories with manual UUID generation
        if ($request->filled('category_id')) {
            $product->categories()->sync([
                $validated['category_id'] => [
                    'id' => Str::uuid()->toString(),
                    'sort_order' => 0,
                ],
            ]);
        } else {
            $product->categories()->detach();
        }

        // 5. Handle additional gallery images
        if ($request->hasFile('additional_images')) {
            $maxSortOrder = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('additional_images') as $index => $imageFile) {
                $path = $imageFile->store('products/gallery', 'public');
                $product->images()->create([
                    'id' => Str::uuid()->toString(),
                    'image_url' => asset('storage/'.$path),
                    'alt_text' => $product->name.' Gallery Image '.($maxSortOrder + $index + 1),
                    'is_primary' => false,
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('seller.products.index')
            ->with('success', "El producto '{$product->name}' se ha actualizado correctamente.");
    }

    /**
     * Remove the specified product from storage (soft-delete).
     */
    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        $productName = $product->name;
        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', "El producto '{$productName}' ha sido eliminado correctamente.");
    }

    /**
     * Toggle the product status (Active / Inactive).
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        Gate::authorize('toggle', $product);

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

        $estado = $product->status === Product::STATUS_ACTIVE ? 'activado' : 'desactivado';

        return back()->with('success', "El producto '{$product->name}' ha sido {$estado} con éxito.");
    }
}

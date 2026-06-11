<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreProductComboRequest;
use App\Http\Requests\Seller\UpdateProductComboRequest;
use App\Models\Product;
use App\Models\ProductCombo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerComboController extends Controller
{
    /**
     * Display a listing of the seller's product combos.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ProductCombo::class);

        $user = $request->user();
        $businessIds = $user->businesses()->pluck('businesses.id');

        $search = $request->query('search');
        $status = $request->query('status', 'all');
        $businessFilter = $request->query('business', 'all');

        $query = ProductCombo::with(['business', 'products'])
            ->whereIn('business_id', $businessIds);

        // Search filter
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Status filter
        if ($status !== 'all') {
            $query->where('is_active', $status === 'active');
        }

        // Business filter
        if ($businessFilter !== 'all' && $businessIds->contains($businessFilter)) {
            $query->where('business_id', $businessFilter);
        }

        $combos = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $businesses = $user->businesses()->orderBy('business_name')->get();

        return view('seller.combos.index', compact('combos', 'businesses', 'search', 'status', 'businessFilter'));
    }

    /**
     * Show the form for creating a new product combo.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', ProductCombo::class);

        $user = $request->user();
        $businesses = $user->businesses()
            ->where('businesses.is_active', true)
            ->where('business_users.is_active', true)
            ->orderBy('business_name')
            ->get();

        // Get products of these businesses, sorted by business and name
        $products = Product::whereIn('business_id', $businesses->pluck('id'))
            ->where('status', Product::STATUS_ACTIVE)
            ->orderBy('name')
            ->get();

        return view('seller.combos.create', compact('businesses', 'products'));
    }

    /**
     * Store a newly created product combo in storage.
     */
    public function store(StoreProductComboRequest $request): RedirectResponse
    {
        Gate::authorize('create', ProductCombo::class);

        $validated = $request->validated();

        $combo = ProductCombo::create([
            'business_id' => $validated['business_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        $syncData = [];
        foreach ($validated['products'] as $productData) {
            $syncData[$productData['product_id']] = [
                'id' => Str::uuid()->toString(),
                'quantity' => $productData['quantity'],
            ];
        }
        $combo->products()->sync($syncData);

        return redirect()->route('seller.combos.index')
            ->with('success', 'Combo creado correctamente.');
    }

    /**
     * Show the form for editing the specified product combo.
     */
    public function edit(ProductCombo $combo, Request $request): View
    {
        Gate::authorize('update', $combo);

        $user = $request->user();
        $businesses = $user->businesses()
            ->where('businesses.is_active', true)
            ->where('business_users.is_active', true)
            ->orderBy('business_name')
            ->get();

        // Products belonging to this combo's business
        $products = Product::where('business_id', $combo->business_id)
            ->where('status', Product::STATUS_ACTIVE)
            ->orderBy('name')
            ->get();

        // Load items to make it easier to pre-populate
        $combo->load('products');

        return view('seller.combos.edit', compact('combo', 'businesses', 'products'));
    }

    /**
     * Update the specified product combo in storage.
     */
    public function update(UpdateProductComboRequest $request, ProductCombo $combo): RedirectResponse
    {
        Gate::authorize('update', $combo);

        $validated = $request->validated();

        $combo->update([
            'business_id' => $validated['business_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        $syncData = [];
        foreach ($validated['products'] as $productData) {
            $syncData[$productData['product_id']] = [
                'id' => Str::uuid()->toString(),
                'quantity' => $productData['quantity'],
            ];
        }
        $combo->products()->sync($syncData);

        return redirect()->route('seller.combos.index')
            ->with('success', 'Combo actualizado correctamente.');
    }

    /**
     * Remove the specified product combo from storage.
     */
    public function destroy(ProductCombo $combo): RedirectResponse
    {
        Gate::authorize('delete', $combo);

        $combo->delete();

        return redirect()->route('seller.combos.index')
            ->with('success', 'Combo eliminado correctamente.');
    }

    /**
     * Toggle the status of the specified product combo.
     */
    public function toggleStatus(ProductCombo $combo): RedirectResponse
    {
        Gate::authorize('toggle', $combo);

        $combo->update([
            'is_active' => ! $combo->is_active,
        ]);

        $statusText = $combo->is_active ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Combo {$statusText} correctamente.");
    }
}

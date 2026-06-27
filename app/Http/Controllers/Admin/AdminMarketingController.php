<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\PromotionalBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminMarketingController extends Controller
{
    /**
     * Listar y gestionar los banners promocionales.
     */
    public function banners(Request $request): View
    {
        $bannersList = PromotionalBanner::orderBy('sort_order')->latest()->paginate(20);

        // Cargar datos complementarios para los selectores del formulario
        $businesses = Business::where('status', Business::STATUS_APPROVED)->orderBy('business_name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('status', Product::STATUS_ACTIVE)->orderBy('name')->get();

        return view('admin.marketing.banners', compact('bannersList', 'businesses', 'categories', 'products'));
    }

    /**
     * Registrar un nuevo banner promocional.
     */
    public function storeBanner(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'image' => 'nullable|image|max:2048', // Soporte para subir imágenes físicas
            'image_url' => 'required_without:image|nullable|url', // O ingresar una URL pública
            'action_type' => 'required|in:open_business,open_product,open_category,external_link',
            'action_id' => 'nullable|string|max:100',
            'action_url' => 'nullable|url',
            'sort_order' => 'required|integer|min:0',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
        ]);

        $imageUrl = $request->input('image_url');

        // Procesar subida de archivo si existe
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('promotional_banners', 'public');
            $imageUrl = asset('storage/'.$path);
        }

        PromotionalBanner::create([
            'title' => $request->input('title'),
            'image_url' => $imageUrl,
            'action_type' => $request->input('action_type'),
            'action_id' => $request->input('action_id'),
            'action_url' => $request->input('action_url'),
            'sort_order' => $request->input('sort_order'),
            'starts_at' => $request->input('starts_at'),
            'expires_at' => $request->input('expires_at'),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Banner promocional registrado y activado con éxito.');
    }

    /**
     * Alternar el estado activo de un banner.
     */
    public function toggleBannerStatus(PromotionalBanner $banner): RedirectResponse
    {
        $banner->is_active = ! $banner->is_active;
        $banner->save();

        $statusText = $banner->is_active ? 'activado' : 'desactivado';

        return redirect()->back()->with('success', "El banner promocional ha sido {$statusText}.");
    }

    /**
     * Listar cupones de descuento.
     */
    public function discounts(Request $request): View
    {
        $discountsList = Discount::latest()->paginate(20);

        return view('admin.marketing.discounts', compact('discountsList'));
    }

    /**
     * Registrar un nuevo cupón de descuento.
     */
    public function storeDiscount(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|unique:discounts,code|max:50|alpha_dash',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:250',
            'discount_type' => 'required|in:percentage,fixed,free_delivery',
            'discount_value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'required|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'required|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
        ]);

        Discount::create([
            'created_by_user_id' => Auth::id(),
            'code' => strtoupper($request->input('code')),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type' => Discount::TYPE_COUPON,
            'discount_type' => $request->input('discount_type'),
            'discount_value' => $request->input('discount_value'),
            'applies_to' => Discount::APPLIES_TO_ORDER,
            'minimum_order_amount' => $request->input('minimum_order_amount'),
            'maximum_discount_amount' => $request->input('maximum_discount_amount'),
            'usage_limit' => $request->input('usage_limit'),
            'usage_limit_per_user' => $request->input('usage_limit_per_user'),
            'starts_at' => $request->input('starts_at'),
            'expires_at' => $request->input('expires_at'),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Cupón de descuento registrado y activado con éxito.');
    }

    /**
     * Alternar el estado activo de un cupón.
     */
    public function toggleDiscountStatus(Discount $discount): RedirectResponse
    {
        $discount->is_active = ! $discount->is_active;
        $discount->save();

        $statusText = $discount->is_active ? 'activado' : 'desactivado';

        return redirect()->back()->with('success', "El cupón de descuento ha sido {$statusText}.");
    }

    /**
     * Actualizar un banner promocional existente.
     */
    public function updateBanner(Request $request, PromotionalBanner $banner): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'required_without:image|nullable|url',
            'action_type' => 'required|in:open_business,open_product,open_category,external_link',
            'action_id' => 'nullable|string|max:100',
            'action_url' => 'nullable|url',
            'sort_order' => 'required|integer|min:0',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
        ]);

        $imageUrl = $request->input('image_url') ?: $banner->image_url;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('promotional_banners', 'public');
            $imageUrl = asset('storage/'.$path);
        }

        $banner->update([
            'title' => $request->input('title'),
            'image_url' => $imageUrl,
            'action_type' => $request->input('action_type'),
            'action_id' => $request->input('action_id'),
            'action_url' => $request->input('action_url'),
            'sort_order' => $request->input('sort_order'),
            'starts_at' => $request->input('starts_at'),
            'expires_at' => $request->input('expires_at'),
        ]);

        return redirect()->back()->with('success', 'Banner promocional actualizado con éxito.');
    }

    /**
     * Eliminar un banner promocional.
     */
    public function destroyBanner(PromotionalBanner $banner): RedirectResponse
    {
        $banner->delete();

        return redirect()->back()->with('success', 'Banner promocional eliminado con éxito.');
    }

    /**
     * Actualizar un cupón de descuento.
     */
    public function updateDiscount(Request $request, Discount $discount): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:50|alpha_dash|unique:discounts,code,'.$discount->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:250',
            'discount_type' => 'required|in:percentage,fixed,free_delivery',
            'discount_value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'required|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'required|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
        ]);

        $discount->update([
            'code' => strtoupper($request->input('code')),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'discount_type' => $request->input('discount_type'),
            'discount_value' => $request->input('discount_value'),
            'minimum_order_amount' => $request->input('minimum_order_amount'),
            'maximum_discount_amount' => $request->input('maximum_discount_amount'),
            'usage_limit' => $request->input('usage_limit'),
            'usage_limit_per_user' => $request->input('usage_limit_per_user'),
            'starts_at' => $request->input('starts_at'),
            'expires_at' => $request->input('expires_at'),
        ]);

        return redirect()->back()->with('success', 'Cupón de descuento actualizado con éxito.');
    }

    /**
     * Eliminar un cupón de descuento.
     */
    public function destroyDiscount(Discount $discount): RedirectResponse
    {
        $discount->delete();

        return redirect()->back()->with('success', 'Cupón de descuento eliminado con éxito.');
    }
}

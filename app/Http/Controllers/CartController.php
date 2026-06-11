<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductCombo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Add a product to the cart.
     */
    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'product_id' => ['required_without:product_combo_id', 'nullable', 'uuid', 'exists:products,id'],
            'product_combo_id' => ['required_without:product_id', 'nullable', 'uuid', 'exists:product_combos,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $quantity = (int) $request->input('quantity', 1);

        // Find or create active cart for the authenticated user
        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id(),
            'status' => Cart::STATUS_ACTIVE,
        ]);

        if ($request->filled('product_combo_id')) {
            $combo = ProductCombo::findOrFail($request->product_combo_id);

            // Check if combo is already in the cart
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_combo_id', $combo->id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'business_id' => $combo->business_id,
                    'product_combo_id' => $combo->id,
                    'product_id' => null,
                    'quantity' => $quantity,
                    'unit_price' => $combo->price,
                ]);
            }
            $message = 'Combo añadido al carrito.';
        } else {
            $product = Product::findOrFail($request->product_id);

            // Check if item is already in the cart
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'business_id' => $product->business_id,
                    'product_id' => $product->id,
                    'product_combo_id' => null,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                ]);
            }
            $message = 'Producto añadido al carrito.';
        }

        // Load relationships to compute totals correctly
        $cart->load(['items.product', 'items.combo', 'items.options.productOption.group', 'items.business']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart' => $this->getCartData($cart),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Update the quantity of a cart item.
     */
    public function updateQuantity(Request $request, CartItem $item): JsonResponse|RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $cart = Cart::where('user_id', auth()->id())
            ->where('status', Cart::STATUS_ACTIVE)
            ->first();

        if (! $cart || $item->cart_id !== $cart->id) {
            abort(403, 'Acceso denegado.');
        }

        $quantity = (int) $request->quantity;

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

        $cart->load(['items.product', 'items.combo', 'items.options.productOption.group', 'items.business']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cantidad actualizada.',
                'cart' => $this->getCartData($cart),
            ]);
        }

        return back()->with('success', 'Carrito actualizado.');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $item): JsonResponse|RedirectResponse
    {
        $cart = Cart::where('user_id', auth()->id())
            ->where('status', Cart::STATUS_ACTIVE)
            ->first();

        if (! $cart || $item->cart_id !== $cart->id) {
            abort(403, 'Acceso denegado.');
        }

        $item->delete();

        $cart->load(['items.product', 'items.combo', 'items.options.productOption.group', 'items.business']);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito.',
                'cart' => $this->getCartData($cart),
            ]);
        }

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    /**
     * Retrieve the active cart in JSON format.
     */
    public function getJson(): JsonResponse
    {
        $cart = Cart::where('user_id', auth()->id())
            ->where('status', Cart::STATUS_ACTIVE)
            ->with(['items.product', 'items.combo', 'items.options.productOption.group', 'items.business'])
            ->first();

        if (! $cart) {
            return response()->json([
                'items' => [],
                'subtotal' => 0.00,
                'items_count' => 0,
            ]);
        }

        return response()->json($this->getCartData($cart));
    }

    /**
     * Format the cart data for JSON response.
     */
    private function getCartData(Cart $cart): array
    {
        $items = [];
        $subtotal = 0.00;
        $itemsCount = 0;

        foreach ($cart->items as $item) {
            $optionsPrice = $item->options ? $item->options->sum('additional_price') : 0.00;
            $price = $item->unit_price + $optionsPrice;
            $total = $price * $item->quantity;

            $subtotal += $total;
            $itemsCount += $item->quantity;

            $items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_combo_id' => $item->product_combo_id,
                'product_name' => $item->product ? $item->product->name : ($item->combo ? $item->combo->name : 'Combo'),
                'product_image' => $item->product ? $item->product->main_image_url : null,
                'business_name' => $item->business ? $item->business->business_name : '',
                'quantity' => $item->quantity,
                'unit_price' => $price,
                'total_price' => $total,
            ];
        }

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'items_count' => $itemsCount,
        ];
    }
}

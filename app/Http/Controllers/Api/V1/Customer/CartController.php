<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartItemOption;
use App\Models\Product;
use App\Models\ProductCombo;
use App\Models\ProductOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get the authenticated user's active cart.
     */
    public function show(Request $request): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->where('status', Cart::STATUS_ACTIVE)
            ->with(['items.product', 'items.combo', 'items.options.productOption.group', 'items.business'])
            ->first();

        if (! $cart) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => null,
                    'status' => 'active',
                    'items' => [],
                    'subtotal' => 0.00,
                    'items_count' => 0,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new CartResource($cart),
        ]);
    }

    /**
     * Add a product or combo to the active cart.
     */
    public function add(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required_without:product_combo_id', 'nullable', 'uuid', 'exists:products,id'],
            'product_combo_id' => ['required_without:product_id', 'nullable', 'uuid', 'exists:product_combos,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'options' => ['nullable', 'array'],
            'options.*' => ['uuid', 'exists:product_options,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $quantity = (int) $request->input('quantity', 1);

        DB::beginTransaction();
        try {
            // Find or create active cart for the authenticated user
            $cart = Cart::firstOrCreate([
                'user_id' => $request->user()->id,
                'status' => Cart::STATUS_ACTIVE,
            ]);

            if ($request->filled('product_combo_id')) {
                $combo = ProductCombo::findOrFail($request->product_combo_id);

                // For combos, check if the same combo (without option groups since combos don't have customizable option groups in model) is in cart
                $cartItem = CartItem::where('cart_id', $cart->id)
                    ->where('product_combo_id', $combo->id)
                    ->first();

                if ($cartItem) {
                    $cartItem->quantity += $quantity;
                    if ($request->filled('notes')) {
                        $cartItem->notes = $request->notes;
                    }
                    $cartItem->save();
                } else {
                    $cartItem = CartItem::create([
                        'cart_id' => $cart->id,
                        'business_id' => $combo->business_id,
                        'product_combo_id' => $combo->id,
                        'product_id' => null,
                        'quantity' => $quantity,
                        'unit_price' => $combo->price,
                        'notes' => $request->notes,
                    ]);
                }
                $message = 'Combo añadido al carrito.';
            } else {
                $product = Product::findOrFail($request->product_id);

                // Option configuration: sort option IDs to compare
                $optionIds = $request->input('options', []);
                sort($optionIds);

                // Find if an item exists with matching product and options
                $existingItems = CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->with('options')
                    ->get();

                $matchedItem = null;
                foreach ($existingItems as $item) {
                    $itemOptionIds = $item->options->pluck('product_option_id')->toArray();
                    sort($itemOptionIds);
                    if ($itemOptionIds === $optionIds) {
                        $matchedItem = $item;
                        break;
                    }
                }

                if ($matchedItem) {
                    $matchedItem->quantity += $quantity;
                    if ($request->filled('notes')) {
                        $matchedItem->notes = $request->notes;
                    }
                    $matchedItem->save();
                } else {
                    $cartItem = CartItem::create([
                        'cart_id' => $cart->id,
                        'business_id' => $product->business_id,
                        'product_id' => $product->id,
                        'product_combo_id' => null,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                        'notes' => $request->notes,
                    ]);

                    // Add options if selected
                    foreach ($optionIds as $optionId) {
                        $opt = ProductOption::find($optionId);
                        if ($opt) {
                            CartItemOption::create([
                                'cart_item_id' => $cartItem->id,
                                'product_option_id' => $opt->id,
                                'additional_price' => (float) $opt->additional_price,
                            ]);
                        }
                    }
                }
                $message = 'Producto añadido al carrito.';
            }

            DB::commit();

            $cart->load(['items.product', 'items.combo', 'items.options.productOption.group', 'items.business']);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al añadir al carrito: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the quantity of a cart item.
     */
    public function updateQuantity(Request $request, string $itemId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('status', Cart::STATUS_ACTIVE)
            ->first();

        if (! $cart) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado.',
            ], 404);
        }

        $item = CartItem::where('id', $itemId)
            ->where('cart_id', $cart->id)
            ->first();

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Elemento del carrito no encontrado.',
            ], 404);
        }

        $quantity = (int) $request->quantity;

        if ($quantity <= 0) {
            $item->delete();
            $message = 'Elemento eliminado del carrito.';
        } else {
            $item->update(['quantity' => $quantity]);
            $message = 'Cantidad actualizada.';
        }

        $cart->load(['items.product', 'items.combo', 'items.options.productOption.group', 'items.business']);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => new CartResource($cart),
        ]);
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request, string $itemId): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->where('status', Cart::STATUS_ACTIVE)
            ->first();

        if (! $cart) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado.',
            ], 404);
        }

        $item = CartItem::where('id', $itemId)
            ->where('cart_id', $cart->id)
            ->first();

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Elemento del carrito no encontrado.',
            ], 404);
        }

        $item->delete();

        $cart->load(['items.product', 'items.combo', 'items.options.productOption.group', 'items.business']);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito.',
            'data' => new CartResource($cart),
        ]);
    }
}

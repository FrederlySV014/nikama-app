<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderResource;
use App\Models\Cart;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Submit checkout and place the order via API.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Retrieve active cart
        $cart = Cart::where('user_id', $user->id)
            ->where('status', Cart::STATUS_ACTIVE)
            ->with(['items.product', 'items.combo.products'])
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tu carrito está vacío.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'address_selection_type' => ['required', 'string', 'in:saved,new'],
            'customer_address_id' => [
                'required_if:address_selection_type,saved',
                'nullable',
                'uuid',
                'exists:customer_addresses,id',
            ],
            'new_label' => ['required_if:address_selection_type,new', 'nullable', 'string', 'max:50'],
            'new_address' => ['required_if:address_selection_type,new', 'nullable', 'string', 'max:255'],
            'new_address_type' => ['nullable', 'string', 'max:30'],
            'new_reference' => ['nullable', 'string', 'max:255'],
            'new_delivery_notes' => ['nullable', 'string', 'max:1000'],
            'new_district' => [
                'required_if:address_selection_type,new',
                'nullable',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && ! SystemSetting::isDistrictActive($request->new_department, $request->new_province, $value)) {
                        $fail('El distrito seleccionado no tiene cobertura activa en Nikama.');
                    }
                },
            ],
            'new_province' => ['nullable', 'string', 'max:100'],
            'new_department' => ['nullable', 'string', 'max:100'],
            'new_postal_code' => ['nullable', 'string', 'max:20'],
            'new_contact_name' => ['nullable', 'string', 'max:100'],
            'new_contact_phone' => ['nullable', 'string', 'max:20'],
            'new_latitude' => ['nullable', 'numeric'],
            'new_longitude' => ['nullable', 'numeric'],
            'save_address' => ['nullable', 'boolean'],
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate payment method
        $paymentMethod = $request->payment_method;
        if (! in_array($paymentMethod, Payment::paymentMethods())) {
            return response()->json([
                'success' => false,
                'message' => 'El método de pago no es válido.',
            ], 422);
        }

        $setting = SystemSetting::where('key', "payment_method_{$paymentMethod}_active")->first();
        $isActive = $setting ? (bool) $setting->value : true;
        if (! $isActive) {
            return response()->json([
                'success' => false,
                'message' => 'El método de pago seleccionado no está disponible en este momento.',
            ], 400);
        }

        // Calculate checkout pricing details
        $subtotal = 0.00;
        foreach ($cart->items as $item) {
            $itemPrice = $item->unit_price;
            $optionsPrice = $item->options ? $item->options->sum('additional_price') : 0.00;
            $subtotal += ($itemPrice + $optionsPrice) * $item->quantity;
        }

        $deliveryFee = 5.00;
        $total = $subtotal + $deliveryFee;

        // Process Checkout Transaction
        DB::beginTransaction();
        try {
            $customerAddressId = null;
            $deliveryAddress = '';
            $deliveryReference = null;
            $deliveryLatitude = null;
            $deliveryLongitude = null;

            if ($request->address_selection_type === 'saved') {
                $address = CustomerAddress::where('id', $request->customer_address_id)
                    ->where('user_id', $user->id)
                    ->first();

                if (! $address) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La dirección seleccionada no es válida.',
                    ], 400);
                }

                if (! SystemSetting::isDistrictActive($address->department, $address->province, $address->district)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La dirección seleccionada no tiene cobertura activa en Nikama.',
                    ], 400);
                }

                $customerAddressId = $address->id;
                $deliveryAddress = $address->address;
                $deliveryReference = $address->reference;
                $deliveryLatitude = $address->latitude;
                $deliveryLongitude = $address->longitude;
            } else {
                $activeDistricts = SystemSetting::getActiveDistricts();
                $matchedDept = $request->new_department ?? 'Lambayeque';
                $matchedProv = $request->new_province ?? 'Chiclayo';
                $matchedDist = $request->new_district;

                foreach ($activeDistricts as $qualified) {
                    $parts = explode('|', $qualified);
                    if (count($parts) === 3) {
                        if (
                            strtolower(trim($parts[0])) === strtolower(trim($matchedDept)) &&
                            strtolower(trim($parts[1])) === strtolower(trim($matchedProv)) &&
                            strtolower(trim($parts[2])) === strtolower(trim($matchedDist))
                        ) {
                            $matchedDept = $parts[0];
                            $matchedProv = $parts[1];
                            $matchedDist = $parts[2];
                            break;
                        }
                    }
                }

                if ($request->boolean('save_address')) {
                    $address = $user->customerAddresses()->create([
                        'label' => $request->new_label,
                        'address' => $request->new_address,
                        'address_type' => $request->new_address_type ?? 'home',
                        'reference' => $request->new_reference,
                        'delivery_notes' => $request->new_delivery_notes,
                        'district' => $matchedDist,
                        'province' => $matchedProv,
                        'department' => $matchedDept,
                        'postal_code' => $request->new_postal_code,
                        'contact_name' => $request->new_contact_name ?? ($user->first_name.' '.$user->last_name),
                        'contact_phone' => $request->new_contact_phone ?? $user->phone,
                        'latitude' => $request->new_latitude,
                        'longitude' => $request->new_longitude,
                        'is_default' => false,
                        'is_active' => true,
                    ]);
                    $customerAddressId = $address->id;
                }

                $deliveryAddress = $request->new_address;
                $deliveryReference = $request->new_reference;
                $deliveryLatitude = $request->new_latitude;
                $deliveryLongitude = $request->new_longitude;
            }

            // Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'customer_address_id' => $customerAddressId,
                'order_number' => 'NKM-'.strtoupper(Str::random(8)),
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_STATUS_PENDING,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'delivery_address' => $deliveryAddress,
                'delivery_reference' => $deliveryReference,
                'delivery_latitude' => $deliveryLatitude,
                'delivery_longitude' => $deliveryLongitude,
                'notes' => $request->notes,
            ]);

            // Create Order Items and copy options
            foreach ($cart->items as $item) {
                $itemPrice = $item->unit_price;
                $optionsPrice = $item->options ? $item->options->sum('additional_price') : 0.00;
                $itemSubtotal = ($itemPrice + $optionsPrice) * $item->quantity;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'business_id' => $item->business_id,
                    'product_id' => $item->product_id,
                    'product_combo_id' => $item->product_combo_id,
                    'product_name' => $item->product ? $item->product->name : $item->combo->name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'product_description' => $item->product ? $item->product->description : $item->combo->description,
                    'product_image_url' => $item->product ? $item->product->main_image_url : null,
                    'subtotal' => $itemSubtotal,
                    'notes' => $item->notes,
                ]);

                // Copy options to order item options
                foreach ($item->options as $opt) {
                    OrderItemOption::create([
                        'order_item_id' => $orderItem->id,
                        'product_option_id' => $opt->product_option_id,
                        'option_group_name' => $opt->productOption->group->name,
                        'option_name' => $opt->productOption->name,
                        'additional_price' => $opt->additional_price,
                    ]);
                }

                // Decrement stock when product tracks inventory
                if ($item->product && $item->product->track_stock) {
                    $product = $item->product;
                    $product->decrement('stock_quantity', $item->quantity);
                    $product->increment('total_sales', $item->quantity);

                    if ($product->stock_quantity <= 0) {
                        $product->update(['status' => Product::STATUS_OUT_OF_STOCK]);
                    }
                } elseif ($item->product_combo_id && $item->combo) {
                    // It's a combo! Decrement stock for each component product
                    foreach ($item->combo->products as $componentProduct) {
                        if ($componentProduct->track_stock) {
                            $deductQty = $componentProduct->pivot->quantity * $item->quantity;
                            $componentProduct->decrement('stock_quantity', $deductQty);
                            $componentProduct->increment('total_sales', $deductQty);

                            if ($componentProduct->stock_quantity <= 0) {
                                $componentProduct->update(['status' => Product::STATUS_OUT_OF_STOCK]);
                            }
                        }
                    }
                }
            }

            // Create Payment
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'status' => Payment::STATUS_PENDING,
                'amount' => $total,
            ]);

            // Update cart status to converted
            $cart->status = Cart::STATUS_CONVERTED;
            $cart->save();

            $paymentLabel = match ($paymentMethod) {
                Payment::METHOD_CASH => 'Efectivo contra entrega',
                Payment::METHOD_YAPE => 'Yape',
                Payment::METHOD_PLIN => 'Plin',
                Payment::METHOD_CARD => 'Tarjeta de crédito/débito',
                Payment::METHOD_BANK_TRANSFER => 'Transferencia bancaria',
                Payment::METHOD_PAGOEFECTIVO => 'PagoEfectivo',
                default => ucfirst($paymentMethod),
            };

            if ($paymentMethod === Payment::METHOD_CASH) {
                $order->status = Order::STATUS_PENDING;
                $order->save();

                $order->statusHistory()->create([
                    'status' => Order::STATUS_PENDING,
                    'description' => 'Pedido recibido. Pago: '.$paymentLabel.' (se cobrará al entregar). Pendiente de confirmación por el negocio.',
                    'changed_by_user_id' => $user->id,
                ]);
            } else {
                $payment->update([
                    'status' => Payment::STATUS_PAID,
                    'transaction_id' => 'TXN-'.strtoupper(Str::random(10)),
                    'paid_at' => now(),
                    'provider_response' => [
                        'auto_confirmed' => true,
                        'method' => $paymentMethod,
                        'message' => 'Pago aprobado automáticamente vía '.$paymentLabel.'.',
                    ],
                ]);

                $order->status = Order::STATUS_PENDING;
                $order->payment_status = Order::PAYMENT_STATUS_PAID;
                $order->save();

                $order->statusHistory()->create([
                    'status' => Order::STATUS_PENDING,
                    'description' => 'Pedido recibido y pago aprobado vía '.$paymentLabel.'. Pendiente de confirmación por el negocio.',
                    'changed_by_user_id' => $user->id,
                ]);
            }

            DB::commit();

            $order->load(['items.options', 'payments', 'statusHistory']);

            return response()->json([
                'success' => true,
                'message' => 'Pedido realizado con éxito.',
                'data' => new OrderResource($order),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el checkout: '.$e->getMessage(),
            ], 500);
        }
    }
}

<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'subtotal' => $this->subtotal,
            'delivery_fee' => $this->delivery_fee,
            'total' => $this->total,
            'delivery_address' => $this->delivery_address,
            'delivery_reference' => $this->delivery_reference,
            'delivery_latitude' => $this->delivery_latitude,
            'delivery_longitude' => $this->delivery_longitude,
            'notes' => $this->notes,
            'confirmed_at' => $this->confirmed_at ? $this->confirmed_at->toIso8601String() : null,
            'delivered_at' => $this->delivered_at ? $this->delivered_at->toIso8601String() : null,
            'cancelled_at' => $this->cancelled_at ? $this->cancelled_at->toIso8601String() : null,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_combo_id' => $item->product_combo_id,
                    'product_name' => $item->product_name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'product_image_url' => $item->product_image_url,
                    'options' => $item->options ? $item->options->map(fn ($opt) => [
                        'id' => $opt->id,
                        'option_name' => $opt->option_name,
                        'option_group_name' => $opt->option_group_name,
                        'additional_price' => (float) $opt->additional_price,
                    ]) : [],
                ]);
            }),
            'payment' => $this->whenLoaded('payments', function () {
                $pay = $this->payments->first();

                return $pay ? [
                    'payment_method' => $pay->payment_method,
                    'status' => $pay->status,
                    'amount' => $pay->amount,
                ] : null;
            }),
            'active_delivery' => $this->when($this->relationLoaded('deliveries'), function () {
                $del = $this->deliveries->first();

                return $del ? [
                    'id' => $del->id,
                    'status' => $del->status,
                    'driver_name' => $del->driverProfile && $del->driverProfile->user
                        ? $del->driverProfile->user->first_name.' '.$del->driverProfile->user->last_name
                        : null,
                ] : null;
            }),
            'status_history' => $this->whenLoaded('statusHistory', function () {
                return $this->statusHistory->map(fn ($hist) => [
                    'status' => $hist->status,
                    'description' => $hist->description,
                    'created_at' => $hist->created_at ? $hist->created_at->toIso8601String() : null,
                ]);
            }),
        ];
    }
}

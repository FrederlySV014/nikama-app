<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverAssignmentResource extends JsonResource
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
            'status' => $this->status,
            'assigned_at' => $this->assigned_at ? $this->assigned_at->toIso8601String() : null,
            'accepted_at' => $this->accepted_at ? $this->accepted_at->toIso8601String() : null,
            'completed_at' => $this->completed_at ? $this->completed_at->toIso8601String() : null,
            'order' => $this->whenLoaded('order', function () {
                return [
                    'id' => $this->order->id,
                    'order_number' => $this->order->order_number,
                    'total' => $this->order->total,
                    'delivery_address' => $this->order->delivery_address,
                    'delivery_reference' => $this->order->delivery_reference,
                    'delivery_latitude' => $this->order->delivery_latitude,
                    'delivery_longitude' => $this->order->delivery_longitude,
                    'notes' => $this->order->notes,
                    'items_count' => $this->order->items->count(),
                ];
            }),
            'delivery' => $this->whenLoaded('delivery', function () {
                return [
                    'id' => $this->delivery->id,
                    'status' => $this->delivery->status,
                    'business_name' => $this->delivery->business ? $this->delivery->business->business_name : null,
                    'business_address' => $this->delivery->business && $this->delivery->business->locations->first()
                        ? $this->delivery->business->locations->first()->address
                        : null,
                    'business_latitude' => $this->delivery->business && $this->delivery->business->locations->first()
                        ? (float) $this->delivery->business->locations->first()->latitude
                        : null,
                    'business_longitude' => $this->delivery->business && $this->delivery->business->locations->first()
                        ? (float) $this->delivery->business->locations->first()->longitude
                        : null,
                ];
            }),
        ];
    }
}

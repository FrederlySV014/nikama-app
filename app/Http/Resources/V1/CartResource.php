<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = [];
        $subtotal = 0.00;
        $itemsCount = 0;

        if ($this->relationLoaded('items')) {
            foreach ($this->items as $item) {
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
                    'notes' => $item->notes,
                    'options' => $item->options ? $item->options->map(fn ($opt) => [
                        'id' => $opt->id,
                        'product_option_id' => $opt->product_option_id,
                        'option_name' => $opt->productOption ? $opt->productOption->name : '',
                        'additional_price' => $opt->additional_price,
                    ]) : [],
                ];
            }
        }

        return [
            'id' => $this->id,
            'status' => $this->status,
            'items' => $items,
            'subtotal' => $subtotal,
            'items_count' => $itemsCount,
        ];
    }
}

<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'business_id' => $this->business_id,
            'business_name' => $this->business ? $this->business->business_name : null,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'sku' => $this->sku,
            'stock_quantity' => $this->stock_quantity,
            'track_stock' => $this->track_stock,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'requires_preparation' => $this->requires_preparation,
            'preparation_time_minutes' => $this->preparation_time_minutes,
            'main_image_url' => $this->main_image_url,
            'rating_average' => $this->rating_average,
            'total_reviews' => $this->total_reviews,
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(fn ($img) => [
                    'id' => $img->id,
                    'image_url' => $img->image_url,
                    'sort_order' => $img->sort_order,
                ]);
            }),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'option_groups' => $this->whenLoaded('optionGroups', function () {
                return $this->optionGroups->map(fn ($group) => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'min_selectable' => $group->min_selectable,
                    'max_selectable' => $group->max_selectable,
                    'is_required' => $group->is_required,
                    'options' => $group->options->map(fn ($opt) => [
                        'id' => $opt->id,
                        'name' => $opt->name,
                        'additional_price' => (float) $opt->additional_price,
                        'is_available' => (bool) $opt->is_available,
                    ]),
                ]);
            }),
        ];
    }
}

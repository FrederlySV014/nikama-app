<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'label' => $this->label,
            'address' => $this->address,
            'address_type' => $this->address_type,
            'reference' => $this->reference,
            'delivery_notes' => $this->delivery_notes,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'province' => $this->province,
            'district' => $this->district,
            'department' => $this->department,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ];
    }
}

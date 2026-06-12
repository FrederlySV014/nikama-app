<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'dni' => $this->dni,
            'avatar_url' => $this->avatar_url,
            'is_active' => $this->is_active,
            'roles' => $this->roles->pluck('slug'),
            'customer_profile' => $this->when($this->relationLoaded('customerProfile') && $this->customerProfile, function () {
                return [
                    'birth_date' => $this->customerProfile->birth_date,
                    'gender' => $this->customerProfile->gender,
                ];
            }),
            'driver_profile' => $this->when($this->relationLoaded('driverProfile') && $this->driverProfile, function () {
                return [
                    'id' => $this->driverProfile->id,
                    'vehicle_type' => $this->driverProfile->vehicle_type,
                    'vehicle_plate' => $this->driverProfile->vehicle_plate,
                    'license_number' => $this->driverProfile->license_number,
                    'status' => $this->driverProfile->status,
                    'rating_average' => $this->driverProfile->rating_average,
                    'total_deliveries' => $this->driverProfile->total_deliveries,
                ];
            }),
        ];
    }
}

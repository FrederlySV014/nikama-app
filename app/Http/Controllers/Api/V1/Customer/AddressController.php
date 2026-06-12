<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AddressResource;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Display a listing of the customer's addresses.
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->customerAddresses()
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => AddressResource::collection($addresses),
        ]);
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'label' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'address_type' => ['nullable', 'string', 'max:30'],
            'reference' => ['nullable', 'string', 'max:255'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
            'district' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    if (! SystemSetting::isDistrictActive($request->department, $request->province, $value)) {
                        $fail('El distrito seleccionado no tiene cobertura activa en Nikama.');
                    }
                },
            ],
            'province' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $hasAddresses = $user->customerAddresses()->where('is_active', true)->exists();

        $isDefault = ! $hasAddresses || $request->boolean('is_default');

        if ($isDefault) {
            $user->customerAddresses()->where('is_active', true)->update(['is_default' => false]);
        }

        // Find matching district in original capitalization from active districts
        $activeDistricts = SystemSetting::getActiveDistricts();
        $matchedDept = $request->department ?? 'Lambayeque';
        $matchedProv = $request->province ?? 'Chiclayo';
        $matchedDist = $request->district;

        foreach ($activeDistricts as $qualified) {
            $parts = explode('|', $qualified);
            if (count($parts) === 3) {
                $matchDept = empty($request->department) || strtolower(trim($parts[0])) === strtolower(trim($request->department));
                $matchProv = empty($request->province) || strtolower(trim($parts[1])) === strtolower(trim($request->province));
                $matchDist = strtolower(trim($parts[2])) === strtolower(trim($request->district));

                if ($matchDept && $matchProv && $matchDist) {
                    $matchedDept = $parts[0];
                    $matchedProv = $parts[1];
                    $matchedDist = $parts[2];
                    break;
                }
            }
        }

        $address = $user->customerAddresses()->create([
            'label' => $request->label,
            'address' => $request->address,
            'address_type' => $request->address_type ?? 'home',
            'reference' => $request->reference,
            'delivery_notes' => $request->delivery_notes,
            'district' => $matchedDist,
            'province' => $matchedProv,
            'department' => $matchedDept,
            'country' => $request->country ?? 'Peru',
            'postal_code' => $request->postal_code,
            'contact_name' => $request->contact_name ?? ($user->first_name.' '.$user->last_name),
            'contact_phone' => $request->contact_phone ?? $user->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_default' => $isDefault,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dirección agregada correctamente.',
            'data' => new AddressResource($address),
        ], 201);
    }

    /**
     * Update the specified address in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $address = $request->user()->customerAddresses()
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (! $address) {
            return response()->json([
                'success' => false,
                'message' => 'Dirección no encontrada.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'label' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'address_type' => ['nullable', 'string', 'max:30'],
            'reference' => ['nullable', 'string', 'max:255'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
            'district' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    if (! SystemSetting::isDistrictActive($request->department, $request->province, $value)) {
                        $fail('El distrito seleccionado no tiene cobertura activa en Nikama.');
                    }
                },
            ],
            'province' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            $request->user()->customerAddresses()->where('is_active', true)->update(['is_default' => false]);
        }

        $activeDistricts = SystemSetting::getActiveDistricts();
        $matchedDept = $request->department ?? 'Lambayeque';
        $matchedProv = $request->province ?? 'Chiclayo';
        $matchedDist = $request->district;

        foreach ($activeDistricts as $qualified) {
            $parts = explode('|', $qualified);
            if (count($parts) === 3) {
                $matchDept = empty($request->department) || strtolower(trim($parts[0])) === strtolower(trim($request->department));
                $matchProv = empty($request->province) || strtolower(trim($parts[1])) === strtolower(trim($request->province));
                $matchDist = strtolower(trim($parts[2])) === strtolower(trim($request->district));

                if ($matchDept && $matchProv && $matchDist) {
                    $matchedDept = $parts[0];
                    $matchedProv = $parts[1];
                    $matchedDist = $parts[2];
                    break;
                }
            }
        }

        $address->update([
            'label' => $request->label,
            'address' => $request->address,
            'address_type' => $request->address_type ?? 'home',
            'reference' => $request->reference,
            'delivery_notes' => $request->delivery_notes,
            'district' => $matchedDist,
            'province' => $matchedProv,
            'department' => $matchedDept,
            'country' => $request->country ?? 'Peru',
            'postal_code' => $request->postal_code,
            'contact_name' => $request->contact_name,
            'contact_phone' => $request->contact_phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_default' => $isDefault,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dirección actualizada correctamente.',
            'data' => new AddressResource($address),
        ]);
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $address = $request->user()->customerAddresses()
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (! $address) {
            return response()->json([
                'success' => false,
                'message' => 'Dirección no encontrada.',
            ], 404);
        }

        $wasDefault = $address->is_default;

        $address->update([
            'is_default' => false,
            'is_active' => false,
        ]);
        $address->delete();

        if ($wasDefault) {
            $nextDefault = $request->user()->customerAddresses()->where('is_active', true)->first();
            if ($nextDefault) {
                $nextDefault->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dirección eliminada correctamente.',
        ]);
    }

    /**
     * Mark the specified address as the default one.
     */
    public function setDefault(Request $request, string $id): JsonResponse
    {
        $address = $request->user()->customerAddresses()
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (! $address) {
            return response()->json([
                'success' => false,
                'message' => 'Dirección no encontrada.',
            ], 404);
        }

        $request->user()->customerAddresses()->where('is_active', true)->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Dirección establecida como predeterminada.',
            'data' => new AddressResource($address),
        ]);
    }
}

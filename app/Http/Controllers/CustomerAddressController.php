<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerAddressController extends Controller
{
    /**
     * Display a listing of the customer's addresses.
     */
    public function index(): View
    {
        $addresses = auth()->user()->customerAddresses()
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('public.profile.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(): View
    {
        $activeDistricts = SystemSetting::getActiveDistricts();

        return view('public.profile.addresses.create', compact('activeDistricts'));
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
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

        $user = auth()->user();
        $hasAddresses = $user->customerAddresses()->where('is_active', true)->exists();

        // Automatically set as default if it's the first address or explicitly requested
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

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dirección agregada correctamente.',
                'address' => $address,
            ]);
        }

        return redirect()->route('profile.addresses.index')->with('success', 'Dirección agregada correctamente.');
    }

    /**
     * Show the form for editing the specified address.
     */
    public function edit(CustomerAddress $address): View
    {
        if ($address->user_id !== auth()->id()) {
            abort(403, 'Acceso denegado.');
        }

        $activeDistricts = SystemSetting::getActiveDistricts();

        return view('public.profile.addresses.edit', compact('address', 'activeDistricts'));
    }

    /**
     * Update the specified address in storage.
     */
    public function update(Request $request, CustomerAddress $address): JsonResponse|RedirectResponse
    {
        if ($address->user_id !== auth()->id()) {
            abort(403, 'Acceso denegado.');
        }

        $request->validate([
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

        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            auth()->user()->customerAddresses()->where('is_active', true)->update(['is_default' => false]);
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

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dirección actualizada correctamente.',
                'address' => $address,
            ]);
        }

        return redirect()->route('profile.addresses.index')->with('success', 'Dirección actualizada correctamente.');
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(CustomerAddress $address): RedirectResponse
    {
        if ($address->user_id !== auth()->id()) {
            abort(403, 'Acceso denegado.');
        }

        $wasDefault = $address->is_default;

        $address->update([
            'is_active' => false,
            'is_default' => false,
        ]);
        $address->delete();

        // If the deleted address was default, set another active one as default if exists
        if ($wasDefault) {
            $nextDefault = auth()->user()->customerAddresses()->where('is_active', true)->first();
            if ($nextDefault) {
                $nextDefault->update(['is_default' => true]);
            }
        }

        return redirect()->route('profile.addresses.index')->with('success', 'Dirección eliminada correctamente.');
    }

    /**
     * Mark the specified address as the default one.
     */
    public function setDefault(CustomerAddress $address): RedirectResponse
    {
        if ($address->user_id !== auth()->id()) {
            abort(403, 'Acceso denegado.');
        }

        auth()->user()->customerAddresses()->where('is_active', true)->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('profile.addresses.index')->with('success', 'Dirección establecida como predeterminada.');
    }
}

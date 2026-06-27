<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\DriverProfile;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminApplicationController extends Controller
{
    /**
     * Get drivers list.
     */
    public function drivers(Request $request): JsonResponse
    {
        if (! $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización.'], 403);
        }

        $status = $request->query('status', 'all');
        $query = DriverProfile::with('user');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $drivers = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $drivers,
        ]);
    }

    /**
     * Get businesses list.
     */
    public function businesses(Request $request): JsonResponse
    {
        if (! $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización.'], 403);
        }

        $status = $request->query('status', 'all');
        $query = Business::with('users');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $businesses = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $businesses,
        ]);
    }

    /**
     * Approve driver.
     */
    public function approveDriver(DriverProfile $driverProfile): JsonResponse
    {
        if (! $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización.'], 403);
        }

        $driverProfile->update([
            'status' => DriverProfile::STATUS_ACTIVE,
            'verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Repartidor aprobado con éxito.',
        ]);
    }

    /**
     * Reject driver.
     */
    public function rejectDriver(Request $request, DriverProfile $driverProfile): JsonResponse
    {
        if (! $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización.'], 403);
        }
        
        $request->validate([
            'rejected_reason' => 'required|string|min:5'
        ]);

        $driverProfile->update([
            'status' => DriverProfile::STATUS_REJECTED,
            'rejected_reason' => $request->rejected_reason,
            'verified_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Repartidor rechazado.',
        ]);
    }

    /**
     * Approve business.
     */
    public function approveBusiness(Business $business): JsonResponse
    {
        if (! $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización.'], 403);
        }

        $business->update([
            'status' => Business::STATUS_APPROVED,
            'approved_at' => now(),
            'verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Negocio aprobado con éxito.',
        ]);
    }

    /**
     * Reject business.
     */
    public function rejectBusiness(Request $request, Business $business): JsonResponse
    {
        if (! $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización.'], 403);
        }

        $request->validate([
            'rejected_reason' => 'required|string|min:5'
        ]);

        $business->update([
            'status' => Business::STATUS_REJECTED,
            'rejected_reason' => $request->rejected_reason,
            'approved_at' => null,
            'verified_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Negocio rechazado.',
        ]);
    }

    /**
     * Toggle active status of a business.
     */
    public function toggleBusiness(Business $business): JsonResponse
    {
        if (! $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'No tienes autorización.'], 403);
        }

        $business->update([
            'is_active' => !$business->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del negocio actualizado.',
            'data' => $business->fresh()
        ]);
    }

    private function checkAdminAccess(): bool
    {
        return auth()->user()->hasRole(Role::SUPER_ADMIN);
    }
}

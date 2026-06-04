<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectRequest;
use App\Models\Business;
use App\Models\DriverProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminApplicationController extends Controller
{
    /**
     * Mostrar el listado unificado de solicitudes con pestañas y filtros.
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'sellers');
        $status = $request->query('status', 'pending');
        $search = $request->query('search');

        // Contadores rápidos de pendientes para badges
        $pendingSellersCount = Business::where('status', Business::STATUS_PENDING)->count();
        $pendingDriversCount = DriverProfile::where('status', DriverProfile::STATUS_PENDING)->count();

        // Totales generales para las tarjetas de estadísticas
        $totalApprovedSellers = Business::where('status', Business::STATUS_APPROVED)->count();
        $totalActiveDrivers = DriverProfile::where('status', DriverProfile::STATUS_ACTIVE)->count();
        $totalApproved = $totalApprovedSellers + $totalActiveDrivers;

        $totalRejectedSellers = Business::where('status', Business::STATUS_REJECTED)->count();
        $totalRejectedDrivers = DriverProfile::where('status', DriverProfile::STATUS_REJECTED)->count();
        $totalRejected = $totalRejectedSellers + $totalRejectedDrivers;

        if ($tab === 'drivers') {
            $query = DriverProfile::with('user');

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('license_plate', 'like', "%{$search}%")
                        ->orWhere('license_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            $applications = $query->latest()->paginate(20)->withQueryString();
        } else {
            // tab = sellers
            $query = Business::with('users');

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('business_name', 'like', "%{$search}%")
                        ->orWhere('legal_name', 'like', "%{$search}%")
                        ->orWhere('ruc', 'like', "%{$search}%")
                        ->orWhere('contact_email', 'like', "%{$search}%")
                        ->orWhere('contact_phone', 'like', "%{$search}%")
                        ->orWhereHas('users', function ($uq) use ($search) {
                            $uq->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            $applications = $query->latest()->paginate(20)->withQueryString();
        }

        return view('admin.applications.index', compact(
            'tab',
            'status',
            'search',
            'pendingSellersCount',
            'pendingDriversCount',
            'totalApproved',
            'totalRejected',
            'applications'
        ));
    }

    /**
     * Mostrar la ficha de detalles de una solicitud de negocio (Seller).
     */
    public function showSeller(Business $business): View
    {
        $business->load('users', 'categories');
        $owner = $business->users->first();

        return view('admin.applications.show-seller', compact('business', 'owner'));
    }

    /**
     * Mostrar la ficha de detalles de una solicitud de repartidor (Driver).
     */
    public function showDriver(DriverProfile $driverProfile): View
    {
        $driverProfile->load('user');

        return view('admin.applications.show-driver', compact('driverProfile'));
    }

    /**
     * Aprobar solicitud de negocio (Seller).
     */
    public function approveSeller(Request $request, Business $business): RedirectResponse
    {
        $business->update([
            'status' => Business::STATUS_APPROVED,
            'approved_at' => now(),
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.applications.seller.show', $business)
            ->with('success', "La solicitud del negocio '{$business->business_name}' ha sido aprobada con éxito.");
    }

    /**
     * Rechazar solicitud de negocio (Seller).
     */
    public function rejectSeller(RejectRequest $request, Business $business): RedirectResponse
    {
        $business->update([
            'status' => Business::STATUS_REJECTED,
            'rejected_reason' => $request->validated()['rejected_reason'],
            'approved_at' => null,
            'verified_at' => null,
        ]);

        return redirect()
            ->route('admin.applications.seller.show', $business)
            ->with('warning', "La solicitud del negocio '{$business->business_name}' ha sido rechazada.");
    }

    /**
     * Aprobar solicitud de repartidor (Driver).
     */
    public function approveDriver(Request $request, DriverProfile $driverProfile): RedirectResponse
    {
        $driverProfile->update([
            'status' => DriverProfile::STATUS_ACTIVE,
            'verified_at' => now(),
        ]);

        $driverName = $driverProfile->user ? $driverProfile->user->first_name . ' ' . $driverProfile->user->last_name : 'Repartidor';

        return redirect()
            ->route('admin.applications.driver.show', $driverProfile)
            ->with('success', "La solicitud de repartidor de '{$driverName}' ha sido aprobada con éxito.");
    }

    /**
     * Rechazar solicitud de repartidor (Driver).
     */
    public function rejectDriver(RejectRequest $request, DriverProfile $driverProfile): RedirectResponse
    {
        $driverProfile->update([
            'status' => DriverProfile::STATUS_REJECTED,
            'rejected_reason' => $request->validated()['rejected_reason'],
            'verified_at' => null,
        ]);

        $driverName = $driverProfile->user ? $driverProfile->user->first_name . ' ' . $driverProfile->user->last_name : 'Repartidor';

        return redirect()
            ->route('admin.applications.driver.show', $driverProfile)
            ->with('warning', "La solicitud de repartidor de '{$driverName}' ha sido rechazada.");
    }
}

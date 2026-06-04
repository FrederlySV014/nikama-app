<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard de Vendedores.
     */
    public function seller(): View
    {
        return view('seller.dashboard');
    }

    /**
     * Dashboard de Repartidores.
     */
    public function driver(): View
    {
        return view('driver.dashboard');
    }

    /**
     * Dashboard de Administración.
     */
    public function admin(): View
    {
        return view('admin.dashboard');
    }

    /**
     * Mostrar página de revisión pendiente / rechazo / suspensión.
     */
    public function pendingReview(Request $request): View
    {
        $user = Auth::user();
        $status = 'none';
        $reason = null;
        $role = 'none';
        $name = '';

        if ($user instanceof User) {
            if ($user->hasRole(Role::SELLER)) {
                $role = 'seller';
                $business = $user->businesses()->first();
                if ($business) {
                    $status = $business->status;
                    $reason = $business->rejected_reason;
                    $name = $business->business_name;
                }
            } elseif ($user->hasRole(Role::DRIVER)) {
                $role = 'driver';
                $driverProfile = $user->driverProfile;
                if ($driverProfile) {
                    $status = $driverProfile->status;
                    $reason = $driverProfile->rejected_reason;
                    $name = $user->first_name.' '.$user->last_name;
                }
            }
        }

        return view('auth.pending-review', compact('status', 'reason', 'role', 'name'));
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\DriverProfile;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Get dashboard statistics for the admin.
     */
    public function index(Request $request): JsonResponse
    {
        $pendingSellersCount = Business::where('status', Business::STATUS_PENDING)->count();
        $pendingDriversCount = DriverProfile::where('status', DriverProfile::STATUS_PENDING)->count();
        
        $totalApprovedSellers = Business::where('status', Business::STATUS_APPROVED)->count();
        $totalActiveDrivers = DriverProfile::where('status', DriverProfile::STATUS_ACTIVE)->count();
        
        $totalUsers = User::count();
        $totalOrders = Order::count();

        // Get pending applications
        $pendingSellers = Business::where('status', Business::STATUS_PENDING)
            ->with('users:id,first_name,last_name')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'business_name' => $b->business_name,
                    'owner_name' => $b->users->first() ? $b->users->first()->first_name . ' ' . $b->users->first()->last_name : 'N/A',
                    'created_at' => $b->created_at,
                ];
            });

        $pendingDrivers = DriverProfile::where('status', DriverProfile::STATUS_PENDING)
            ->with('user:id,first_name,last_name')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'name' => $d->user ? $d->user->first_name . ' ' . $d->user->last_name : 'N/A',
                    'vehicle_type' => $d->vehicle_type,
                    'created_at' => $d->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_users' => $totalUsers,
                    'total_orders' => $totalOrders,
                    'active_sellers' => $totalApprovedSellers,
                    'active_drivers' => $totalActiveDrivers,
                    'pending_sellers_count' => $pendingSellersCount,
                    'pending_drivers_count' => $pendingDriversCount,
                ],
                'pending_applications' => [
                    'sellers' => $pendingSellers,
                    'drivers' => $pendingDrivers,
                ],
            ],
        ]);
    }
}

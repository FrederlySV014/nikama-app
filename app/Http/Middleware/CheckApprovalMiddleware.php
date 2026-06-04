<?php

namespace App\Http\Middleware;

use App\Models\Business;
use App\Models\DriverProfile;
use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApprovalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // No bloquear peticiones de logout o la propia página de revisión para evitar bucles de redirección
        if ($request->routeIs('logout') || $request->routeIs('auth.pending-review')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user instanceof User) {
            // Verificar estado del negocio para el rol de vendedor (Seller)
            if ($user->hasRole(Role::SELLER)) {
                $business = $user->businesses()->first();
                if (! $business || $business->status !== Business::STATUS_APPROVED) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Su cuenta de negocio no está aprobada.',
                            'status' => $business ? $business->status : 'none',
                        ], 403);
                    }

                    return redirect()->route('auth.pending-review');
                }
            }

            // Verificar estado del perfil para el rol de repartidor (Driver)
            if ($user->hasRole(Role::DRIVER)) {
                $driverProfile = $user->driverProfile;
                if (! $driverProfile || $driverProfile->status !== DriverProfile::STATUS_ACTIVE) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Su cuenta de repartidor no está activa.',
                            'status' => $driverProfile ? $driverProfile->status : 'none',
                        ], 403);
                    }

                    return redirect()->route('auth.pending-review');
                }
            }
        }

        return $next($request);
    }
}

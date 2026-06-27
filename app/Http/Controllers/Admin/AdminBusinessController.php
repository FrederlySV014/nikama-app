<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBusinessController extends Controller
{
    /**
     * Mostrar el listado de negocios con filtros y paginación.
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'all');
        $active = $request->query('active', 'all');
        $featured = $request->query('featured', 'all');
        $search = $request->query('search');

        // Contadores rápidos por estado para los badges de las pestañas
        $pendingCount = Business::where('status', Business::STATUS_PENDING)->count();
        $approvedCount = Business::where('status', Business::STATUS_APPROVED)->count();
        $rejectedCount = Business::where('status', Business::STATUS_REJECTED)->count();
        $suspendedCount = Business::where('status', Business::STATUS_SUSPENDED)->count();

        // Query principal cargando relaciones clave
        $query = Business::with(['users', 'locations']);

        // Filtro por Estado (Tabs)
        if ($tab !== 'all' && in_array($tab, Business::statuses())) {
            $query->where('status', $tab);
        }

        // Filtro por Activo / Inactivo
        if ($active === 'active') {
            $query->where('is_active', true);
        } elseif ($active === 'inactive') {
            $query->where('is_active', false);
        }

        // Filtro por Destacado / Estándar
        if ($featured === 'featured') {
            $query->where('is_featured', true);
        } elseif ($featured === 'standard') {
            $query->where('is_featured', false);
        }

        // Filtro por Buscador (Nombre de negocio, RUC, Legal name, email, teléfono de contacto o nombre del dueño)
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

        // Paginación de 20 por página
        $businesses = $query->latest()->paginate(20)->withQueryString();

        return view('admin.businesses.index', compact(
            'businesses',
            'tab',
            'active',
            'featured',
            'search',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'suspendedCount'
        ));
    }

    /**
     * Mostrar la ficha detallada de un negocio.
     */
    public function show(Business $business): View
    {
        // Cargar las relaciones del negocio
        $business->load(['users', 'locations.hours', 'categories']);
        $owner = $business->users->first();

        return view('admin.businesses.show', compact('business', 'owner'));
    }

    /**
     * Alternar el estado activo/inactivo de un negocio.
     */
    public function toggleActive(Business $business): RedirectResponse
    {
        $business->is_active = ! $business->is_active;
        $business->save();

        $statusText = $business->is_active ? 'activado' : 'desactivado';
        $alertType = $business->is_active ? 'success' : 'warning';

        return redirect()
            ->back()
            ->with($alertType, "El negocio '{$business->business_name}' ha sido {$statusText} con éxito.");
    }

    /**
     * Alternar si el negocio está destacado o no.
     */
    public function toggleFeatured(Business $business): RedirectResponse
    {
        $business->is_featured = ! $business->is_featured;
        $business->save();

        $statusText = $business->is_featured ? 'destacado' : 'retirado de destacados';
        $alertType = 'success';

        return redirect()
            ->back()
            ->with($alertType, "El negocio '{$business->business_name}' ahora está {$statusText}.");
    }

    /**
     * Alternar si el negocio acepta pedidos o no.
     */
    public function toggleAcceptsOrders(Business $business): RedirectResponse
    {
        $business->accepts_orders = ! $business->accepts_orders;
        $business->save();

        $statusText = $business->accepts_orders ? 'habilitado para recibir pedidos' : 'deshabilitado para recibir pedidos';
        $alertType = $business->accepts_orders ? 'success' : 'warning';

        return redirect()
            ->back()
            ->with($alertType, "El negocio '{$business->business_name}' ha sido {$statusText}.");
    }

    /**
     * Alternar la suspensión de un negocio (Aprobado <-> Suspendido).
     */
    public function toggleSuspension(Business $business): RedirectResponse
    {
        if ($business->status === Business::STATUS_SUSPENDED) {
            $business->status = Business::STATUS_APPROVED;
            $business->suspended_at = null;
            $business->save();

            return redirect()
                ->back()
                ->with('success', "La suspensión del negocio '{$business->business_name}' ha sido levantada.");
        }

        if ($business->status === Business::STATUS_APPROVED) {
            $business->status = Business::STATUS_SUSPENDED;
            $business->suspended_at = now();
            $business->accepts_orders = false; // Desactivar la recepción de pedidos al suspender
            $business->save();

            return redirect()
                ->back()
                ->with('warning', "El negocio '{$business->business_name}' ha sido suspendido.");
        }

        return redirect()
            ->back()
            ->with('warning', "Solo se pueden suspender o reactivar negocios con estado 'Aprobado'.");
    }
}

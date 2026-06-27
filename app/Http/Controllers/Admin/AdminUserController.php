<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Mostrar el listado de usuarios con filtros y paginación.
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'all');
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        // Contadores rápidos por rol
        $customersCount = User::whereHas('roles', function ($q) {
            $q->where('slug', Role::CUSTOMER);
        })->count();

        $sellersCount = User::whereHas('roles', function ($q) {
            $q->where('slug', Role::SELLER);
        })->count();

        $driversCount = User::whereHas('roles', function ($q) {
            $q->where('slug', Role::DRIVER);
        })->count();

        $adminsCount = User::whereHas('roles', function ($q) {
            $q->where('slug', Role::SUPER_ADMIN);
        })->count();

        // Query principal
        $query = User::with('roles');

        // Filtro por Rol (Tabs)
        if ($tab !== 'all' && in_array($tab, Role::systemRoles())) {
            $query->whereHas('roles', function ($q) use ($tab) {
                $q->where('slug', $tab);
            });
        }

        // Filtro por Estado (is_active)
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'blocked') {
            $query->where('is_active', false);
        }

        // Filtro por Buscador
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        // Paginación de 20 por página
        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'tab',
            'status',
            'search',
            'customersCount',
            'sellersCount',
            'driversCount',
            'adminsCount'
        ));
    }

    /**
     * Activar o Bloquear la cuenta de un usuario.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Evitar que el admin se bloquee a sí mismo
        if ($user->id === Auth::id()) {
            return redirect()
                ->back()
                ->with('warning', 'No puedes bloquear tu propia cuenta de administrador.');
        }

        if ($user->is_active) {
            $user->is_active = false;
            $user->blocked_at = now();
            $user->save();
            $statusMsg = "El usuario '{$user->first_name} {$user->last_name}' ha sido bloqueado con éxito.";
            $alertType = 'warning';
        } else {
            $user->is_active = true;
            $user->blocked_at = null;
            $user->save();
            $statusMsg = "El usuario '{$user->first_name} {$user->last_name}' ha sido activado con éxito.";
            $alertType = 'success';
        }

        return redirect()
            ->back()
            ->with($alertType, $statusMsg);
    }
}

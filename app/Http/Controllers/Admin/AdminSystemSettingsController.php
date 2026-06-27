<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSystemSettingsController extends Controller
{
    /**
     * Claves de configuración soportadas en este formulario.
     */
    protected array $settingKeys = [
        'min_driver_payout' => [
            'default' => '20.00',
            'description' => 'Monto mínimo en soles para que un repartidor solicite retiro de saldo.',
        ],
        'min_business_payout' => [
            'default' => '50.00',
            'description' => 'Monto mínimo en soles para que un negocio/comercio solicite retiro de saldo.',
        ],
        'support_email' => [
            'default' => 'soporte@nikama.pe',
            'description' => 'Correo de soporte general para la plataforma.',
        ],
        'support_phone' => [
            'default' => '+51 999 999 999',
            'description' => 'Teléfono o WhatsApp de soporte general para la plataforma.',
        ],
        'general_commission_percentage' => [
            'default' => '10.00',
            'description' => 'Porcentaje general de comisión cobrada a comercios si no tienen regla específica.',
        ],
        'delivery_base_fee' => [
            'default' => '5.00',
            'description' => 'Tarifa base cobrada por delivery a los clientes.',
        ],
    ];

    /**
     * Mostrar formulario de configuraciones del sistema.
     */
    public function edit(): View
    {
        $settings = [];
        foreach ($this->settingKeys as $key => $meta) {
            $setting = SystemSetting::where('key', $key)->first();
            if (! $setting) {
                // Instanciar valor por defecto sin persistir aún
                $setting = new SystemSetting([
                    'key' => $key,
                    'value' => $meta['default'],
                    'description' => $meta['description'],
                ]);
            }
            $settings[$key] = $setting;
        }

        return view('admin.system.settings', compact('settings'));
    }

    /**
     * Actualizar los parámetros del sistema en masa.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'settings.min_driver_payout' => 'required|numeric|min:0',
            'settings.min_business_payout' => 'required|numeric|min:0',
            'settings.support_email' => 'required|email|max:150',
            'settings.support_phone' => 'required|string|max:30',
            'settings.general_commission_percentage' => 'required|numeric|min:0|max:100',
            'settings.delivery_base_fee' => 'required|numeric|min:0',
        ]);

        $inputs = $request->input('settings', []);

        foreach ($this->settingKeys as $key => $meta) {
            if (isset($inputs[$key])) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $inputs[$key],
                        'description' => $meta['description'],
                    ]
                );
            }
        }

        return redirect()->route('admin.system.settings.edit')
            ->with('success', 'Configuraciones generales actualizadas correctamente.');
    }

    /**
     * Mostrar la bitácora de auditoría (ActivityLog).
     */
    public function auditLogs(Request $request): View
    {
        $search = $request->query('search');
        $action = $request->query('action');
        $ipAddress = $request->query('ip_address');

        $query = ActivityLog::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('entity_type', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($action) {
            $query->where('action', $action);
        }

        if ($ipAddress) {
            $query->where('ip_address', $ipAddress);
        }

        $logsList = $query->latest()->paginate(20)->withQueryString();

        // Obtener acciones únicas para el selector de filtro
        $actions = ActivityLog::distinct()->pluck('action')->all();

        return view('admin.system.audit_logs', compact('logsList', 'actions', 'search', 'action', 'ipAddress'));
    }
}

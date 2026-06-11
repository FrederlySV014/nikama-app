<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentSettingsController extends Controller
{
    /**
     * Show the payment settings form.
     */
    public function edit(): View
    {
        $methods = Payment::paymentMethods();
        $settings = [];

        foreach ($methods as $method) {
            $setting = SystemSetting::where('key', "payment_method_{$method}_active")->first();
            $settings[$method] = $setting ? (bool) $setting->value : true;
        }

        return view('admin.settings.payments', compact('settings'));
    }

    /**
     * Update the payment settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $methods = Payment::paymentMethods();

        foreach ($methods as $method) {
            $active = $request->boolean("methods.{$method}");
            SystemSetting::updateOrCreate(
                ['key' => "payment_method_{$method}_active"],
                [
                    'value' => $active ? '1' : '0',
                    'description' => "Indica si el método de pago '{$method}' está activo en la plataforma.",
                ]
            );
        }

        return redirect()->route('admin.settings.payments.edit')
            ->with('success', 'Métodos de pago actualizados correctamente.');
    }
}

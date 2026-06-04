<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DriverRegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\DriverProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DriverAuthController extends Controller
{
    /**
     * Mostrar vista de login para repartidores.
     */
    public function showLogin(): View
    {
        return view('auth.driver-login');
    }

    /**
     * Procesar login de repartidores.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user instanceof User || ! $user->hasRole(Role::DRIVER)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Acceso rechazado. Este portal es exclusivo para repartidores.',
                ])->onlyInput('email');
            }

            $driverProfile = $user->driverProfile;
            if (! $driverProfile || $driverProfile->status !== DriverProfile::STATUS_ACTIVE) {
                $request->session()->regenerate();

                return redirect()->route('auth.pending-review');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('driver.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Mostrar vista de registro para repartidores.
     */
    public function showRegister(): View
    {
        return view('auth.driver-register');
    }

    /**
     * Procesar registro de repartidor.
     */
    public function register(DriverRegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            $driverRole = Role::where('slug', Role::DRIVER)->first();
            if ($driverRole) {
                $user->roles()->attach($driverRole->id);
            }

            DriverProfile::create([
                'user_id' => $user->id,
                'vehicle_type' => $data['vehicle_type'],
                'license_number' => $data['license_number'] ?? null,
                'vehicle_brand' => $data['vehicle_brand'] ?? null,
                'vehicle_model' => $data['vehicle_model'] ?? null,
                'vehicle_color' => $data['vehicle_color'] ?? null,
                'license_plate' => $data['license_plate'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'],
                'emergency_contact_phone' => $data['emergency_contact_phone'],
                'status' => DriverProfile::STATUS_PENDING,
                'accepts_cash_payments' => true,
                'rating_average' => 0.00,
                'total_deliveries' => 0,
            ]);

            DB::commit();

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('auth.pending-review');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado al registrar el repartidor: '.$e->getMessage(),
            ])->withInput();
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    /**
     * Mostrar vista de login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Procesar login de cliente.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user instanceof User || ! $user->hasRole(Role::CUSTOMER)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Acceso rechazado. Este portal es exclusivo para clientes.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('public.welcome'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Mostrar vista de registro.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Procesar registro de cliente.
     */
    public function register(CustomerRegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'dni' => $data['dni'] ?? null,
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            $customerRole = Role::where('slug', Role::CUSTOMER)->first();
            if ($customerRole) {
                $user->roles()->attach($customerRole->id);
            }

            CustomerProfile::create([
                'user_id' => $user->id,
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
            ]);

            DB::commit();

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('public.welcome')->with('success', '¡Registro exitoso! Bienvenido a Nikama.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado durante el registro: '.$e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.welcome');
    }
}

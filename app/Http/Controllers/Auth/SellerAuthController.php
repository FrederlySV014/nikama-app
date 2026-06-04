<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SellerRegisterRequest;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerAuthController extends Controller
{
    /**
     * Mostrar vista de login para vendedores.
     */
    public function showLogin(): View
    {
        return view('auth.seller-login');
    }

    /**
     * Procesar login de vendedores.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user instanceof User || ! $user->hasRole(Role::SELLER)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Acceso rechazado. Este portal es exclusivo para vendedores.',
                ])->onlyInput('email');
            }

            $business = $user->businesses()->first();
            if (! $business || $business->status !== Business::STATUS_APPROVED) {
                $request->session()->regenerate();

                return redirect()->route('auth.pending-review');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('seller.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Mostrar vista de registro para vendedores.
     */
    public function showRegister(): View
    {
        return view('auth.seller-register');
    }

    /**
     * Procesar registro de vendedor y su negocio.
     */
    public function register(SellerRegisterRequest $request): RedirectResponse
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

            $sellerRole = Role::where('slug', Role::SELLER)->first();
            if ($sellerRole) {
                $user->roles()->attach($sellerRole->id);
            }

            $slug = Str::slug($data['business_name']);
            $count = Business::where('slug', 'like', "{$slug}%")->count();
            if ($count > 0) {
                $slug = "{$slug}-".($count + 1);
            }

            $business = Business::create([
                'business_name' => $data['business_name'],
                'slug' => $slug,
                'legal_name' => $data['legal_name'],
                'ruc' => $data['ruc'],
                'description' => $data['description'] ?? null,
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'],
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'status' => Business::STATUS_PENDING,
                'accepts_orders' => false,
                'is_active' => true,
            ]);

            BusinessUser::create([
                'business_id' => $business->id,
                'user_id' => $user->id,
                'role' => BusinessUser::ROLE_ADMIN,
                'is_active' => true,
                'joined_at' => now(),
            ]);

            DB::commit();

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('auth.pending-review');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado al registrar el negocio: '.$e->getMessage(),
            ])->withInput();
        }
    }
}

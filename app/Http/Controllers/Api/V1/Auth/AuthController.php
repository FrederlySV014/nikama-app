<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\CustomerProfile;
use App\Models\DriverProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle API login.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ], 401);
        }

        // Check active status
        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta ha sido desactivada.',
            ], 403);
        }

        // Check if user is either Customer or Driver
        $isCustomer = $user->hasRole(Role::CUSTOMER);
        $isDriver = $user->hasRole(Role::DRIVER);

        if (! $isCustomer && ! $isDriver) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. Este portal es exclusivo para clientes y repartidores.',
            ], 403);
        }

        // Load profiles
        $user->load(['roles', 'customerProfile', 'driverProfile']);

        // Generate token
        $deviceName = $request->input('device_name', 'Flutter Device');
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso.',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Register a new Customer.
     */
    public function registerCustomer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{9}$/'],
            'dni' => ['nullable', 'string', 'regex:/^[0-9]{8}$/', 'unique:users,dni'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'device_name' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'dni' => $request->dni ?? null,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            $customerRole = Role::where('slug', Role::CUSTOMER)->first();
            if ($customerRole) {
                $user->roles()->attach($customerRole->id);
            }

            CustomerProfile::create([
                'user_id' => $user->id,
                'birth_date' => $request->birth_date ?? null,
                'gender' => $request->gender ?? null,
            ]);

            DB::commit();

            $user->load(['roles', 'customerProfile']);
            $deviceName = $request->input('device_name', 'Flutter Customer');
            $token = $user->createToken($deviceName)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registro de cliente exitoso.',
                'token' => $token,
                'user' => new UserResource($user),
            ], 210); // Using 201 for Created, or 200. Let's return 201.
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado durante el registro: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Register a new Driver.
     */
    public function registerDriver(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{9}$/'],
            'vehicle_type' => ['required', 'string', 'in:motorcycle,bicycle,car,other'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'vehicle_brand' => ['nullable', 'string', 'max:50'],
            'vehicle_model' => ['nullable', 'string', 'max:50'],
            'vehicle_color' => ['nullable', 'string', 'max:30'],
            'license_plate' => ['nullable', 'string', 'max:20'],
            'emergency_contact_name' => ['required', 'string', 'max:100'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'device_name' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            $driverRole = Role::where('slug', Role::DRIVER)->first();
            if ($driverRole) {
                $user->roles()->attach($driverRole->id);
            }

            DriverProfile::create([
                'user_id' => $user->id,
                'vehicle_type' => $request->vehicle_type,
                'license_number' => $request->license_number ?? null,
                'vehicle_brand' => $request->vehicle_brand ?? null,
                'vehicle_model' => $request->vehicle_model ?? null,
                'vehicle_color' => $request->vehicle_color ?? null,
                'license_plate' => $request->license_plate ?? null,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'status' => DriverProfile::STATUS_PENDING,
                'accepts_cash_payments' => true,
                'rating_average' => 0.00,
                'total_deliveries' => 0,
            ]);

            DB::commit();

            $user->load(['roles', 'driverProfile']);
            $deviceName = $request->input('device_name', 'Flutter Driver');
            $token = $user->createToken($deviceName)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registro de repartidor exitoso. Tu perfil se encuentra en revisión pendiente.',
                'token' => $token,
                'user' => new UserResource($user),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al registrar el repartidor: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the authenticated user's profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['roles', 'customerProfile', 'driverProfile']);

        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Handle API logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cierre de sesión exitoso.',
        ]);
    }
}

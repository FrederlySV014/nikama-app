<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\BusinessLocationHour;
use App\Models\BusinessUser;
use App\Models\CustomerAddress;
use App\Models\CustomerProfile;
use App\Models\DriverLiveLocation;
use App\Models\DriverProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SuperAdminSeeder::class,
            CustomerSeeder::class,
            DriverSeeder::class,
            BusinessSeeder::class,
            CategoryAndProductSeeder::class,
        ]);

        $this->seedStaticUsers();
    }

    /**
     * Seed static users for testing and API development.
     */
    protected function seedStaticUsers(): void
    {
        $password = Hash::make('Password123');

        // 1. Static Customer
        $customerRole = Role::where('slug', Role::CUSTOMER)->first();
        if ($customerRole) {
            $customer = User::firstOrCreate(
                ['email' => 'customer@nikama.com'],
                [
                    'first_name' => 'Cliente',
                    'last_name' => 'Prueba',
                    'phone' => '+51987654321',
                    'dni' => '76543210',
                    'password' => $password,
                    'is_active' => true,
                ]
            );

            if (!$customer->roles()->where('slug', Role::CUSTOMER)->exists()) {
                $customer->roles()->attach($customerRole);
            }

            CustomerProfile::firstOrCreate(
                ['user_id' => $customer->id],
                [
                    'birth_date' => '1995-05-15',
                    'gender' => 'male',
                ]
            );

            CustomerAddress::firstOrCreate(
                [
                    'user_id' => $customer->id,
                    'is_default' => true,
                ],
                [
                    'label' => 'Mi Casa',
                    'address' => 'Av. Balta 123, Chiclayo',
                    'address_type' => 'home',
                    'reference' => 'Frente al parque principal',
                    'contact_name' => 'Cliente Prueba',
                    'contact_phone' => '+51987654321',
                    'province' => 'Chiclayo',
                    'district' => 'Chiclayo',
                    'department' => 'Lambayeque',
                    'is_active' => true,
                ]
            );
        }

        // 2. Static Driver
        $driverRole = Role::where('slug', Role::DRIVER)->first();
        if ($driverRole) {
            $driver = User::firstOrCreate(
                ['email' => 'driver@nikama.com'],
                [
                    'first_name' => 'Repartidor',
                    'last_name' => 'Prueba',
                    'phone' => '+51999888777',
                    'dni' => '70605040',
                    'password' => $password,
                    'is_active' => true,
                ]
            );

            if (!$driver->roles()->where('slug', Role::DRIVER)->exists()) {
                $driver->roles()->attach($driverRole);
            }

            $driverProfile = DriverProfile::firstOrCreate(
                ['user_id' => $driver->id],
                [
                    'vehicle_type' => 'motorcycle',
                    'license_number' => 'Q70605040',
                    'vehicle_brand' => 'Honda',
                    'vehicle_model' => 'GL150',
                    'vehicle_color' => 'negro',
                    'license_plate' => 'MT-4567',
                    'emergency_contact_name' => 'Contacto Emergencia',
                    'emergency_contact_phone' => '912345678',
                    'accepts_cash_payments' => true,
                    'rating_average' => 5.0,
                    'total_deliveries' => 0,
                    'status' => DriverProfile::STATUS_ACTIVE,
                    'verified_at' => now(),
                ]
            );

            DriverLiveLocation::firstOrCreate(
                ['driver_profile_id' => $driverProfile->id],
                [
                    'latitude' => -6.7719,
                    'longitude' => -79.8394,
                    'last_location_updated_at' => now(),
                ]
            );
        }

        // 3. Static Admin / Super Admin
        $superAdminRole = Role::where('slug', Role::SUPER_ADMIN)->first();
        if ($superAdminRole) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@nikama.com'],
                [
                    'first_name' => 'Administrador',
                    'last_name' => 'Nikama',
                    'phone' => '+51900000000',
                    'dni' => '99999999',
                    'password' => $password,
                    'is_active' => true,
                ]
            );

            if (!$admin->roles()->where('slug', Role::SUPER_ADMIN)->exists()) {
                $admin->roles()->attach($superAdminRole);
            }
        }

        // 4. Static Seller / Business Owner
        $sellerRole = Role::where('slug', Role::SELLER)->first();
        if ($sellerRole) {
            $seller = User::firstOrCreate(
                ['email' => 'seller@nikama.com'],
                [
                    'first_name' => 'Vendedor',
                    'last_name' => 'Prueba',
                    'phone' => '+51911223344',
                    'dni' => '60606060',
                    'password' => $password,
                    'is_active' => true,
                ]
            );

            if (!$seller->roles()->where('slug', Role::SELLER)->exists()) {
                $seller->roles()->attach($sellerRole);
            }

            // Create a Business for this seller if none exists
            $business = Business::firstOrCreate(
                ['slug' => 'nikama-food-chiclayo'],
                [
                    'business_name' => 'Nikama Food - Chiclayo',
                    'description' => 'Comida rápida de prueba para Nikama.',
                    'contact_phone' => '+51911223344',
                    'contact_email' => 'business@nikama.com',
                    'status' => Business::STATUS_APPROVED,
                ]
            );

            // Associate seller as admin
            BusinessUser::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'user_id' => $seller->id,
                ],
                [
                    'role' => BusinessUser::ROLE_ADMIN,
                    'is_active' => true,
                    'joined_at' => now(),
                ]
            );

            // Create main location in Chiclayo if none exists
            $location = BusinessLocation::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'is_main' => true,
                ],
                [
                    'name' => 'Sede Principal Chiclayo',
                    'address' => 'Av. Bolognesi 456, Chiclayo',
                    'location_phone' => '+51911223344',
                    'province' => 'Chiclayo',
                    'district' => 'Chiclayo',
                    'department' => 'Lambayeque',
                    'latitude' => -6.7745,
                    'longitude' => -79.8431,
                    'is_active' => true,
                ]
            );

            // Create operating hours for each day of the week
            foreach (BusinessLocationHour::daysOfWeek() as $day) {
                $isSunday = ($day === BusinessLocationHour::DAY_SUNDAY);
                BusinessLocationHour::firstOrCreate(
                    [
                        'business_location_id' => $location->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'opening_time' => $isSunday ? null : '08:00:00',
                        'closing_time' => $isSunday ? null : '22:00:00',
                        'is_24_hours' => false,
                        'is_closed' => $isSunday,
                    ]
                );
            }
        }
    }
}


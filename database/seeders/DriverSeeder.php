<?php

namespace Database\Seeders;

use App\Models\DriverLiveLocation;
use App\Models\DriverProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $driverRole = Role::where('slug', Role::DRIVER)->first();

        if (! $driverRole) {
            return;
        }

        // Generate 5 driver users
        User::factory()->count(5)->create()->each(function (User $user) use ($driverRole) {
            $user->roles()->attach($driverRole);

            // Create driver profile
            $profile = DriverProfile::factory()->create([
                'user_id' => $user->id,
            ]);

            // Create initial live location
            DriverLiveLocation::factory()->create([
                'driver_profile_id' => $profile->id,
            ]);
        });
    }
}

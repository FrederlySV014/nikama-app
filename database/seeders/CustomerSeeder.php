<?php

namespace Database\Seeders;

use App\Models\CustomerAddress;
use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerRole = Role::where('slug', Role::CUSTOMER)->first();

        if (! $customerRole) {
            return;
        }

        // Generate 10 customer users
        User::factory()->count(10)->create()->each(function (User $user) use ($customerRole) {
            $user->roles()->attach($customerRole);

            // Create customer profile
            CustomerProfile::factory()->create([
                'user_id' => $user->id,
            ]);

            // Create default address in Chiclayo
            CustomerAddress::factory()->create([
                'user_id' => $user->id,
                'is_default' => true,
                'is_active' => true,
            ]);

            // Create 1-2 additional optional addresses
            CustomerAddress::factory()->count(fake()->numberBetween(0, 2))->create([
                'user_id' => $user->id,
                'is_default' => false,
                'is_active' => true,
            ]);
        });
    }
}

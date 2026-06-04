<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\BusinessLocationHour;
use App\Models\BusinessUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellerRole = Role::where('slug', Role::SELLER)->first();

        if (! $sellerRole) {
            return;
        }

        // Generate 5 sellers, businesses, locations, and hours
        for ($i = 0; $i < 5; $i++) {
            // 1. Create the seller user (owner/admin)
            $owner = User::factory()->create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName().' (Admin)',
            ]);
            $owner->roles()->attach($sellerRole);

            // 2. Create the business
            $business = Business::factory()->create([
                'business_name' => fake()->company().' - Chiclayo',
            ]);

            // 3. Associate owner as admin
            BusinessUser::create([
                'business_id' => $business->id,
                'user_id' => $owner->id,
                'role' => BusinessUser::ROLE_ADMIN,
                'is_active' => true,
                'joined_at' => now(),
            ]);

            // 4. Create the staff user
            $staff = User::factory()->create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName().' (Staff)',
            ]);
            $staff->roles()->attach($sellerRole);

            // 5. Associate staff member
            BusinessUser::create([
                'business_id' => $business->id,
                'user_id' => $staff->id,
                'role' => BusinessUser::ROLE_STAFF,
                'is_active' => true,
                'joined_at' => now(),
            ]);

            // 6. Create main location in Chiclayo
            $location = BusinessLocation::factory()->create([
                'business_id' => $business->id,
                'name' => 'Sede Principal Chiclayo',
                'is_main' => true,
                'is_active' => true,
            ]);

            // 7. Create operating hours for each day of the week
            foreach (BusinessLocationHour::daysOfWeek() as $day) {
                // Sunday closed, other days open 08:00 - 22:00
                $isSunday = ($day === BusinessLocationHour::DAY_SUNDAY);

                BusinessLocationHour::create([
                    'business_location_id' => $location->id,
                    'day_of_week' => $day,
                    'opening_time' => $isSunday ? null : '08:00:00',
                    'closing_time' => $isSunday ? null : '22:00:00',
                    'is_24_hours' => false,
                    'is_closed' => $isSunday,
                ]);
            }
        }
    }
}

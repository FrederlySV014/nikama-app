<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('slug', Role::SUPER_ADMIN)->first();

        if (! $superAdminRole) {
            return;
        }

        // Generate 2 Super Admin users
        User::factory()->count(2)->create()->each(function (User $user) use ($superAdminRole) {
            $user->roles()->attach($superAdminRole);
        });
    }
}

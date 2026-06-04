<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => Role::SUPER_ADMIN,
                'description' => 'Full system access',
            ],
            [
                'name' => 'Seller',
                'slug' => Role::SELLER,
                'description' => 'Seller / business admin / Staff',
            ],
            [
                'name' => 'Driver',
                'slug' => Role::DRIVER,
                'description' => 'Delivery driver',
            ],
            [
                'name' => 'Customer',
                'slug' => Role::CUSTOMER,
                'description' => 'Regular customer',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
    }
}

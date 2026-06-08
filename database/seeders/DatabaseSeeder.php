<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
    }
}

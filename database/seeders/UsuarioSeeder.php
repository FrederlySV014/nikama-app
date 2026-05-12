<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::create([
            'email' => 'admin@nikama.com',
            'password_hash' => bcrypt('password123'),
            'nombre_completo' => 'Super Administrador',
            'telefono' => '+51999999999',
            'rol' => 'superadmin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Usuario::create([
            'email' => 'admin2@nikama.com',
            'password_hash' => bcrypt('password123'),
            'nombre_completo' => 'Administrador',
            'telefono' => '+51988888888',
            'rol' => 'admin',
            'is_active' => true,
        ]);
    }
}
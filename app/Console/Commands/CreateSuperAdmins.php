<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Signature('nikama:create-superadmins {--default-password= : Contraseña predeterminada para todos los usuarios}')]
#[Description('Crea los 4 usuarios superadmins iniciales (Frederly, Jose, Carlos, Anthony) en la base de datos.')]
class CreateSuperAdmins extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $superAdminRole = Role::where('slug', Role::SUPER_ADMIN)->first();

        if (! $superAdminRole) {
            $this->error('El rol Super Admin no existe en la base de datos. Asegúrate de ejecutar los seeders o migraciones primero.');

            return 1;
        }

        $names = ['Frederly', 'Jose', 'Carlos', 'Anthony'];
        $defaultPasswordOption = $this->option('default-password');

        $this->info('Iniciando creación de los 4 Superadmins...');

        foreach ($names as $name) {
            $this->newLine();
            $this->info("=== Configuración para {$name} ===");

            // 1. Email
            $emailSuggest = strtolower($name).'@nikama.com';
            $email = $this->ask("Correo electrónico para {$name}", $emailSuggest);

            // Verificar si el usuario ya existe
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                if ($existingUser->hasRole(Role::SUPER_ADMIN)) {
                    $this->warn("El usuario {$email} ya existe y ya es Superadmin. Omitiendo.");

                    continue;
                }

                $this->warn("El usuario {$email} ya existe pero no es Superadmin. Asignando rol...");
                $existingUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
                $this->info("Rol Superadmin asignado con éxito a {$existingUser->first_name}.");

                continue;
            }

            // 2. Apellido
            $lastName = $this->ask("Apellido para {$name}", 'Admin');

            // 3. Contraseña
            if ($defaultPasswordOption) {
                $password = $defaultPasswordOption;
            } else {
                $passwordInput = $this->secret("Contraseña para {$name} (deja vacío para generar una aleatoria)");
                $password = $passwordInput ?: Str::random(12);
            }

            // Crear usuario
            $user = User::create([
                'first_name' => $name,
                'last_name' => $lastName,
                'email' => $email,
                'password' => Hash::make($password),
                'is_active' => true,
            ]);

            // Asociar rol
            $user->roles()->attach($superAdminRole);

            $this->info("¡Superadmin {$name} creado con éxito!");
            $this->line("Email: {$email}");
            if (! $defaultPasswordOption && empty($passwordInput)) {
                $this->line("Contraseña autogenerada: {$password}");
            } else {
                $this->line('Contraseña: [Ingresada por el usuario]');
            }
        }

        $this->newLine();
        $this->info('Proceso completado.');

        return 0;
    }
}

<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@nikama.com',
    'password' => bcrypt('password123')
]);

echo "Usuario creado: " . $user->email . "\n";
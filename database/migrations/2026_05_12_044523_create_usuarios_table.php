<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('nombre_completo');
            $table->string('telefono', 20)->nullable();
            $table->string('foto_url')->nullable();
            $table->enum('rol', ['cliente', 'repartidor', 'admin', 'superadmin']);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('ultimo_acceso')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('rol');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
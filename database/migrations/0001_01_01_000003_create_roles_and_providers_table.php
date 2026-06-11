<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->text('description')->nullable();

            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignUuid('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('role_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('user_providers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // google, facebook, apple
            $table->string('provider', 30);
            $table->string('provider_id', 255);
            $table->string('provider_email')->nullable();

            $table->timestamps();

            $table->unique(['provider', 'provider_id']);
            $table->unique(['user_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_providers');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};

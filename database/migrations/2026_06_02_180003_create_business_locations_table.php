<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('address');
            $table->string('reference')->nullable();
            $table->string('province', 100);
            $table->string('district', 100);
            $table->string('department', 100);
            $table->string('country', 100)->default('Peru');
            $table->string('postal_code', 20)->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_phone', 20)->nullable();
            $table->decimal('delivery_radius_km', 5, 2)->default(5.00);
            $table->decimal('delivery_fee', 8, 2)->default(0.00);
            $table->unsignedSmallInteger('estimated_delivery_time_minutes')->default(30);
            $table->decimal('minimum_delivery_amount', 8, 2)->default(0.00);

            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('is_main');
            $table->index('is_active');
            $table->index(['department', 'province']);
            $table->index(['province', 'district']);
            $table->index(['latitude', 'longitude']);
        });

        // PostgreSQL Partial Unique Index: Un negocio solo puede tener una ubicación principal activa (excluyendo eliminaciones lógicas)
        DB::statement('CREATE UNIQUE INDEX business_locations_main_unique ON business_locations (business_id) WHERE (is_main = true AND deleted_at IS NULL);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_locations');
    }
};

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
        Schema::create('driver_location_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('driver_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->decimal('speed_kmh', 6, 2)->nullable();
            $table->decimal('accuracy_meters', 6, 2)->nullable();

            $table->timestamp('recorded_at')->index();
            $table->timestamps();

            $table->index(['driver_profile_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_location_logs');
    }
};

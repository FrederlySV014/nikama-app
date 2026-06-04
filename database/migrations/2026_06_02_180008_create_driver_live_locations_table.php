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
        Schema::create('driver_live_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('driver_profile_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_online')->default(false);
            $table->boolean('is_available')->default(false);

            $table->timestamp('last_location_updated_at')->nullable();
            $table->timestamp('last_online_at')->nullable();
            $table->timestamps();

            $table->index(['is_online', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_live_locations');
    }
};

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
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('business_location_id')
                ->constrained('business_locations')
                ->cascadeOnDelete();

            $table->string('name');
            $table->jsonb('polygon_coordinates')->nullable();
            $table->decimal('delivery_fee', 8, 2);
            $table->decimal('minimum_order_amount', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['business_location_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};

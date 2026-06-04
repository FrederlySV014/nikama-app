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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('driver_profile_id')
                ->nullable()
                ->constrained('driver_profiles')
                ->nullOnDelete();

            $table->foreignUuid('business_id')
                ->constrained()
                ->cascadeOnDelete();

            // pending, assigned, picked_up, on_the_way, delivered, failed
            $table->string('status', 30)->default('pending');

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->decimal('delivery_fee', 8, 2)->default(0.00);
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['driver_profile_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};

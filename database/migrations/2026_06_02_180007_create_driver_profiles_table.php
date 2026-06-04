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
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            // bicycle, motorcycle, car
            $table->string('vehicle_type', 30);

            $table->string('license_number', 20)->nullable();
            $table->string('vehicle_brand', 100)->nullable();
            $table->string('vehicle_model', 100)->nullable();
            $table->string('vehicle_color', 50)->nullable();
            $table->string('license_plate', 20)->nullable()->unique();
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();

            $table->boolean('accepts_cash_payments')->default(true);
            $table->decimal('rating_average', 4, 2)->default(0.00);
            $table->unsignedInteger('total_deliveries')->default(0);

            // pending, active, inactive, rejected, suspended
            $table->string('status', 30)->default('pending');
            $table->text('rejected_reason')->nullable();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_profiles');
    }
};

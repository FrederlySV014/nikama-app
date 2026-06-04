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
        Schema::create('business_location_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_location_id')
                ->constrained()
                ->cascadeOnDelete();

            // monday, tuesday, wednesday, thursday, friday, saturday, sunday
            $table->string('day_of_week', 20);

            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();

            $table->boolean('is_24_hours')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->unique(['business_location_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_location_hours');
    }
};

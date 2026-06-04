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
        Schema::create('business_holiday_exceptions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('business_location_id')
                ->constrained('business_locations')
                ->cascadeOnDelete();

            $table->date('exception_date');
            $table->boolean('is_closed')->default(true);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['business_location_id', 'exception_date'], 'biz_loc_holiday_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_holiday_exceptions');
    }
};

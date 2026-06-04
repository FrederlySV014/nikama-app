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
        Schema::create('order_cancellations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->string('cancelled_by_type', 20); // customer, business, driver, system, admin
            $table->uuid('cancelled_by_id')->nullable();
            $table->string('reason_code', 50);
            $table->text('comment')->nullable();
            $table->boolean('penalty_applied')->default(false);
            $table->decimal('penalty_amount', 10, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cancellations');
    }
};

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
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('business_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignUuid('product_combo_id')
                ->nullable()
                ->constrained('product_combos')
                ->nullOnDelete();

            $table->string('product_name');
            $table->decimal('unit_price', 8, 2);
            $table->unsignedInteger('quantity');

            $table->text('product_description')->nullable();
            $table->string('product_image_url')->nullable();

            $table->decimal('subtotal', 10, 2);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

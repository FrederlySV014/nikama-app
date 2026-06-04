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
        Schema::create('cart_item_options', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('cart_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('product_option_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('additional_price', 8, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['cart_item_id', 'product_option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item_options');
    }
};

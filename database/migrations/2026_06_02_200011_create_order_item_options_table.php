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
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('order_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('product_option_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('option_group_name');
            $table->string('option_name');
            $table->decimal('additional_price', 8, 2)->default(0.00);

            $table->timestamps();

            $table->index('order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_options');
    }
};

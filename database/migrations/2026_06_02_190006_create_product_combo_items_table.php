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
        Schema::create('product_combo_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('product_combo_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->unique(['product_combo_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_combo_items');
    }
};

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
        Schema::create('discount_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('discount_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('discount_applied', 10, 2)->default(0.00);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'discount_id']);
            $table->index(['discount_id', 'user_id']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_usages');
    }
};

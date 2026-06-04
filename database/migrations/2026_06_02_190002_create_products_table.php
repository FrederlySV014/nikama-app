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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('business_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name', 150);
            $table->string('slug', 180);
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('compare_price', 8, 2)->nullable();
            $table->string('sku', 100)->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('track_stock')->default(true);
            $table->boolean('allow_backorder')->default(false);

            // draft, active, inactive, out_of_stock, suspended
            $table->string('status', 30)->default('draft');

            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_preparation')->default(true);
            $table->integer('preparation_time_minutes')->nullable();
            $table->decimal('weight_grams', 8, 2)->nullable();
            $table->string('main_image_url')->nullable();
            $table->decimal('rating_average', 3, 2)->default(0.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->unsignedBigInteger('total_sales')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_available')->default(true);

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'sku'], 'unique_business_sku');
            $table->unique(['business_id', 'slug']);
            $table->index('status');
            $table->index('is_featured');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

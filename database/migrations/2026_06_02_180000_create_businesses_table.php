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
        Schema::create('businesses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('business_name', 150);
            $table->string('slug', 180)->unique();
            $table->string('legal_name', 150)->nullable();
            $table->string('ruc', 20)->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('whatsapp_number', 20)->nullable();

            $table->decimal('rating_average', 3, 2)->default(0.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->unsignedBigInteger('total_orders')->default(0);

            $table->decimal('minimum_order_amount', 8, 2)->default(0.00);
            $table->integer('estimated_preparation_time_minutes')->default(15);

            // pending, approved, rejected, suspended
            $table->string('status', 30)->default('pending');

            $table->text('rejected_reason')->nullable();
            $table->boolean('accepts_orders')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('offers_delivery')->default(true);
            $table->boolean('offers_pickup')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('website_url')->nullable();

            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_active']);
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};

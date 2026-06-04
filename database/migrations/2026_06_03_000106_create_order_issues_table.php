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
        Schema::create('order_issues', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('issue_type', 30); // missing_items, bad_condition, delivery_delay, etc.
            $table->text('description');
            $table->string('status', 20)->default('open'); // open, investigating, resolved, rejected
            $table->string('resolution_action', 30)->nullable(); // refund_issued, coupon_gifted, no_action
            $table->decimal('refund_amount', 10, 2)->nullable();

            $table->foreignUuid('assigned_admin_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('order_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_issues');
    }
};

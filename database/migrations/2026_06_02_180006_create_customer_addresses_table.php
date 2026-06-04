<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('label', 50);
            $table->string('address');
            $table->string('address_type', 30)->nullable();
            $table->string('reference')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('country', 100)->default('Peru');
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['department', 'province']);
            $table->index(['province', 'district']);
            $table->index(['user_id', 'is_default']);
            $table->index(['user_id', 'is_active']);
            $table->index(['latitude', 'longitude']);
        });

        // PostgreSQL Partial Unique Index: Un usuario solo puede tener una dirección predeterminada activa (excluyendo eliminaciones lógicas)
        DB::statement('CREATE UNIQUE INDEX customer_addresses_default_unique ON customer_addresses (user_id) WHERE (is_default = true AND deleted_at IS NULL);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};

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
        Schema::create('payout_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('owner');
            $table->string('type', 20); // bank_account, yape, plin
            $table->string('provider_name');
            $table->string('account_number')->nullable();
            $table->string('cci_number')->nullable();
            $table->string('holder_name');
            $table->string('holder_dni');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['owner_type', 'owner_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_methods');
    }
};

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
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('driver_profile_id')
                ->constrained('driver_profiles')
                ->cascadeOnDelete();

            $table->string('document_type', 30); // license, soat, identity_card
            $table->string('document_url');
            $table->string('status', 30)->default('pending'); // pending, approved, rejected
            $table->text('rejected_reason')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['driver_profile_id', 'document_type']);
            $table->index(['driver_profile_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_documents');
    }
};

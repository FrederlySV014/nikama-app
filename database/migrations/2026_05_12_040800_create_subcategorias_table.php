<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subcategorias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('categoria_id');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('imagen_url')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
            $table->index('categoria_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategorias');
    }
};
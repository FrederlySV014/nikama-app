<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subcategoria_id');
            $table->string('codigo_sku', 100)->unique();
            $table->string('codigo_barras', 50)->nullable();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('color', 50)->nullable();
            $table->integer('peso_gramos')->nullable();
            $table->integer('garantia_meses')->default(12);
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('precio_oferta', 10, 2)->nullable();
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->integer('stock_maximo')->default(100);
            $table->enum('estado_producto', ['nuevo', 'reacondicionado', 'outlet'])->default('nuevo');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('imagen_url')->nullable();
            $table->json('imagenes_extra')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subcategoria_id')->references('id')->on('subcategorias')->onDelete('cascade');
            $table->index('subcategoria_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
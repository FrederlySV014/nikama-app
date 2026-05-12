<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class DatosSeeder extends Seeder
{
    public function run(): void
    {
        $categoria = Categoria::create([
            'nombre' => 'Hamburguesas',
            'descripcion' => 'Las mejores hamburguesas de la ciudad',
            'imagen_url' => 'https://example.com/hamburguesas.jpg',
            'orden' => 1,
            'is_active' => true,
        ]);

        $subcategoria = Subcategoria::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Clásicas',
            'descripcion' => 'Hamburguesas tradicionales',
            'orden' => 1,
            'is_active' => true,
        ]);

        Producto::create([
            'subcategoria_id' => $subcategoria->id,
            'codigo_sku' => 'HAM-CLAS-001',
            'nombre' => 'Hamburguesa Clásica',
            'descripcion' => 'Hamburguesa con carne, lechuga, tomate, cebolla y salsa',
            'marca' => 'Nikama',
            'precio_compra' => 5.00,
            'precio_venta' => 12.00,
            'stock_actual' => 50,
            'stock_minimo' => 10,
            'stock_maximo' => 100,
            'estado_producto' => 'nuevo',
            'is_available' => true,
            'is_active' => true,
            'imagen_url' => 'https://example.com/hamburguesa-clasica.jpg',
        ]);

        Producto::create([
            'subcategoria_id' => $subcategoria->id,
            'codigo_sku' => 'HAM-CLAS-002',
            'nombre' => 'Hamburguesa con Queso',
            'descripcion' => 'Hamburguesa con queso cheddar, tocino y salsa barbecue',
            'marca' => 'Nikama',
            'precio_compra' => 6.50,
            'precio_venta' => 15.00,
            'stock_actual' => 30,
            'stock_minimo' => 10,
            'stock_maximo' => 80,
            'estado_producto' => 'nuevo',
            'is_available' => true,
            'is_active' => true,
            'imagen_url' => 'https://example.com/hamburguesa-queso.jpg',
        ]);
    }
}
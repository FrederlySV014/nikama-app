<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Root Categories
        $restaurantes = Category::create([
            'name' => 'Restaurantes',
            'slug' => 'restaurantes',
            'description' => 'Comida rápida, local e internacional',
            'icon' => '🍔',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $farmacia = Category::create([
            'name' => 'Farmacias',
            'slug' => 'farmacias',
            'description' => 'Medicamentos y salud',
            'icon' => '💊',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $supermercado = Category::create([
            'name' => 'Supermercados',
            'slug' => 'supermercados',
            'description' => 'Abarrotes, frutas y verduras',
            'icon' => '🛒',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $moda = Category::create([
            'name' => 'Moda y Ropa',
            'slug' => 'moda-y-ropa',
            'description' => 'Prendas de vestir y accesorios',
            'icon' => '👗',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        // 2. Create Subcategories (Leaf Categories)
        $hamburguesas = Category::create([
            'name' => 'Hamburguesas',
            'slug' => 'hamburguesas',
            'parent_id' => $restaurantes->id,
            'is_active' => true,
        ]);

        $pizzas = Category::create([
            'name' => 'Pizzas',
            'slug' => 'pizzas',
            'parent_id' => $restaurantes->id,
            'is_active' => true,
        ]);

        $medicamentos = Category::create([
            'name' => 'Medicamentos',
            'slug' => 'medicamentos',
            'parent_id' => $farmacia->id,
            'is_active' => true,
        ]);

        $cuidado = Category::create([
            'name' => 'Cuidado Personal',
            'slug' => 'cuidado-personal',
            'parent_id' => $farmacia->id,
            'is_active' => true,
        ]);

        $bebidas = Category::create([
            'name' => 'Bebidas',
            'slug' => 'bebidas',
            'parent_id' => $supermercado->id,
            'is_active' => true,
        ]);

        // 3. Attach categories to seeded businesses
        $businesses = Business::all();
        if ($businesses->isEmpty()) {
            return;
        }

        // Assign some categories and products to businesses to show them on homepage
        foreach ($businesses as $index => $business) {
            if ($index % 3 === 0) {
                $business->categories()->attach([
                    $restaurantes->id => ['id' => Str::uuid()->toString()],
                    $hamburguesas->id => ['id' => Str::uuid()->toString()],
                ]);

                // Add products
                $p1 = Product::factory()->create([
                    'business_id' => $business->id,
                    'name' => 'Hamburguesa Luffy Especial',
                    'slug' => 'hamburguesa-luffy-especial-' . $business->id,
                    'price' => 15.90,
                    'compare_price' => 18.00,
                    'status' => Product::STATUS_ACTIVE,
                    'is_available' => true,
                ]);
                $p1->categories()->attach($hamburguesas->id, [
                    'id' => Str::uuid()->toString(),
                    'sort_order' => 0,
                ]);

                $p2 = Product::factory()->create([
                    'business_id' => $business->id,
                    'name' => 'Papas Fritas Crunchy',
                    'slug' => 'papas-fritas-crunchy-' . $business->id,
                    'price' => 8.00,
                    'status' => Product::STATUS_ACTIVE,
                    'is_available' => true,
                ]);
                $p2->categories()->attach($hamburguesas->id, [
                    'id' => Str::uuid()->toString(),
                    'sort_order' => 0,
                ]);
            } elseif ($index % 3 === 1) {
                $business->categories()->attach([
                    $farmacia->id => ['id' => Str::uuid()->toString()],
                    $medicamentos->id => ['id' => Str::uuid()->toString()],
                ]);

                // Add products
                $p1 = Product::factory()->create([
                    'business_id' => $business->id,
                    'name' => 'Paracetamol 500mg (10 pastillas)',
                    'slug' => 'paracetamol-500mg-' . $business->id,
                    'price' => 3.50,
                    'status' => Product::STATUS_ACTIVE,
                    'is_available' => true,
                ]);
                $p1->categories()->attach($medicamentos->id, [
                    'id' => Str::uuid()->toString(),
                    'sort_order' => 0,
                ]);
            } else {
                $business->categories()->attach([
                    $supermercado->id => ['id' => Str::uuid()->toString()],
                    $bebidas->id => ['id' => Str::uuid()->toString()],
                ]);

                // Add products
                $p1 = Product::factory()->create([
                    'business_id' => $business->id,
                    'name' => 'Coca-Cola Zero 1.5L',
                    'slug' => 'coca-cola-zero-1-5l-' . $business->id,
                    'price' => 7.50,
                    'status' => Product::STATUS_ACTIVE,
                    'is_available' => true,
                ]);
                $p1->categories()->attach($bebidas->id, [
                    'id' => Str::uuid()->toString(),
                    'sort_order' => 0,
                ]);
            }
        }
    }
}

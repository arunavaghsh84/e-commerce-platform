<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronicsCategory = Category::where('slug', 'electronics')->first();
        $clothingCategory = Category::where('slug', 'clothing')->first();
        $booksCategory = Category::where('slug', 'books')->first();

        $products = [
            [
                'name' => 'Smartphone X',
                'price' => 699.99,
                'category_id' => $electronicsCategory?->id,
                'is_active' => true,
            ],
            [
                'name' => 'Laptop Pro',
                'price' => 1299.99,
                'category_id' => $electronicsCategory?->id,
                'is_active' => true,
            ],
            [
                'name' => 'Wireless Headphones',
                'price' => 199.99,
                'category_id' => $electronicsCategory?->id,
                'is_active' => true,
            ],
            [
                'name' => 'Cotton T-Shirt',
                'price' => 24.99,
                'category_id' => $clothingCategory?->id,
                'is_active' => true,
            ],
            [
                'name' => 'Denim Jeans',
                'price' => 59.99,
                'category_id' => $clothingCategory?->id,
                'is_active' => true,
            ],
            [
                'name' => 'Programming Guide',
                'price' => 39.99,
                'category_id' => $booksCategory?->id,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

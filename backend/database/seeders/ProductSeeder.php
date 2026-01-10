<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;     
use App\Models\Category; 

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $products = [
            // Electronics (category_id = 1)
            ['name' => 'Laptop HP', 'category_id' => 1, 'price' => 799.99, 'stock_quantity' => 15, 'image' => null, 'qr_code' => null],
            ['name' => 'Mouse Wireless', 'category_id' => 1, 'price' => 25.50, 'stock_quantity' => 50, 'image' => null, 'qr_code' => null],
            
            // Food & Beverages (category_id = 2)
            ['name' => 'Coca Cola 1L', 'category_id' => 2, 'price' => 2.50, 'stock_quantity' => 100, 'image' => null, 'qr_code' => null],
            ['name' => 'Pizza Margherita', 'category_id' => 2, 'price' => 12.00, 'stock_quantity' => 30, 'image' => null, 'qr_code' => null],
            
            // Clothing (category_id = 3)
            ['name' => 'T-Shirt Cotton', 'category_id' => 3, 'price' => 19.99, 'stock_quantity' => 40, 'image' => null, 'qr_code' => null],
            ['name' => 'Jeans Blue', 'category_id' => 3, 'price' => 45.00, 'stock_quantity' => 25, 'image' => null, 'qr_code' => null],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        
        Product::factory()->count(20)->create();
    }
}
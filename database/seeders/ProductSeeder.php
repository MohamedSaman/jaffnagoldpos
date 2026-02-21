<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Purity;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ring = Category::where('name', 'like', '%Ring%')->first() ?? Category::first();
        $necklace = Category::where('name', 'like', '%Necklace%')->first() ?? Category::first();
        $bangle = Category::where('name', 'like', '%Bangle%')->first() ?? Category::first();
        $chain = Category::where('name', 'like', '%Chain%')->first() ?? Category::first();

        $p24 = Purity::where('name', '24K')->first() ?? Purity::first();
        $p22 = Purity::where('name', '22K')->first() ?? Purity::where('id', 2)->first() ?? Purity::first();
        $p18 = Purity::where('name', '18K')->first() ?? Purity::where('id', 3)->first() ?? Purity::first();

        $products = [
            [
                'product_code' => 'RNG-001',
                'name' => 'Classic Gold Wedding Ring',
                'category_id' => $ring->id ?? 1,
                'purity_id' => $p22->id ?? 2,
                'gross_weight' => 5.250,
                'stone_weight' => 0.000,
                'net_weight' => 5.250,
                'making_charge_type' => 'per_gram',
                'making_charge' => 450,
                'wastage_percentage' => 3.5,
                'stock_quantity' => 10,
                'barcode' => '890123456701',
                'status' => true,
            ],
            [
                'product_code' => 'NCK-002',
                'name' => 'Diamond Studded Gold Necklace',
                'category_id' => $necklace->id ?? 1,
                'purity_id' => $p18->id ?? 3,
                'gross_weight' => 25.800,
                'stone_weight' => 1.200,
                'net_weight' => 24.600,
                'making_charge_type' => 'fixed',
                'making_charge' => 15000,
                'wastage_percentage' => 5.0,
                'stock_quantity' => 3,
                'barcode' => '890123456702',
                'status' => true,
            ],
            [
                'product_code' => 'BNG-003',
                'name' => 'Traditional Gold Bangles (Pair)',
                'category_id' => $bangle->id ?? 1,
                'purity_id' => $p22->id ?? 2,
                'gross_weight' => 48.000,
                'stone_weight' => 0.000,
                'net_weight' => 48.000,
                'making_charge_type' => 'per_gram',
                'making_charge' => 380,
                'wastage_percentage' => 4.0,
                'stock_quantity' => 5,
                'barcode' => '890123456703',
                'status' => true,
            ],
            [
                'product_code' => 'CHN-004',
                'name' => 'Sleek 24K Daily Wear Chain',
                'category_id' => $chain->id ?? 1,
                'purity_id' => $p24->id ?? 1,
                'gross_weight' => 12.000,
                'stone_weight' => 0.000,
                'net_weight' => 12.000,
                'making_charge_type' => 'per_gram',
                'making_charge' => 300,
                'wastage_percentage' => 2.0,
                'stock_quantity' => 8,
                'barcode' => '890123456704',
                'status' => true,
            ],
            [
                'product_code' => 'RNG-005',
                'name' => 'Rose Gold Cocktail Ring',
                'category_id' => $ring->id ?? 1,
                'purity_id' => $p18->id ?? 3,
                'gross_weight' => 8.450,
                'stone_weight' => 0.550,
                'net_weight' => 7.900,
                'making_charge_type' => 'fixed',
                'making_charge' => 5500,
                'wastage_percentage' => 4.5,
                'stock_quantity' => 12,
                'barcode' => '890123456705',
                'status' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Purity;
use App\Models\JewelleryRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@jewel.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Categories
        $categories = ['Ring', 'Chain', 'Bangle', 'Earings', 'Necklace', 'Bracelet'];
        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }

        // Create Purities
        $purities = [
            ['name' => '24K', 'percentage' => 100.00],
            ['name' => '22K', 'percentage' => 91.60],
            ['name' => '18K', 'percentage' => 75.00],
        ];
        foreach ($purities as $p) {
            $purity = Purity::create($p);
            
            // Initial Gold Rate
            JewelleryRate::create([
                'purity_id' => $purity->id,
                'rate_per_gram' => $p['name'] == '24K' ? 7500 : ($p['name'] == '22K' ? 6875 : 5625),
                'date' => now()->toDateString(),
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@vending.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Create sample products
        $products = [
            [
                'name' => 'Coffee',
                'category' => 'Coffee',
                'price' => 25.00,
                'stock' => 50,
                'description' => 'Fresh brewed coffee'
            ],
            [
                'name' => 'Tea',
                'category' => 'Tea',
                'price' => 15.00,
                'stock' => 30,
                'description' => 'Hot tea'
            ],
            [
                'name' => 'Orange Juice',
                'category' => 'Juice',
                'price' => 35.00,
                'stock' => 25,
                'description' => 'Fresh orange juice'
            ],
            [
                'name' => 'Apple Juice',
                'category' => 'Juice',
                'price' => 30.00,
                'stock' => 20,
                'description' => 'Fresh apple juice'
            ],
            [
                'name' => 'Mineral Water',
                'category' => 'Water',
                'price' => 20.00,
                'stock' => 100,
                'description' => 'Pure mineral water'
            ],
            [
                'name' => 'Coca Cola',
                'category' => 'Soda',
                'price' => 30.00,
                'stock' => 40,
                'description' => 'Classic Coca Cola'
            ],
            [
                'name' => 'Pepsi',
                'category' => 'Soda',
                'price' => 30.00,
                'stock' => 35,
                'description' => 'Classic Pepsi'
            ],
            [
                'name' => 'Red Bull',
                'category' => 'Energy Drink',
                'price' => 50.00,
                'stock' => 15,
                'description' => 'Energy drink'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
            public function run(): void
        {
            User::firstOrCreate(
                ['email' => 'admin@easyvend.com'],
                [
                    'name'     => 'Administrator',
                    'password' => Hash::make('001qx1z1x123'),
                    'role'     => 'admin',
                ]
            );

            User::firstOrCreate(
                ['email' => 'cashier@easyvend.com'],
                [
                    'name'     => 'Juan Cashier',
                    'password' => Hash::make('cashier123'),
                    'role'     => 'cashier',
                ]
            );

            $products = [
                ['name' => 'Silver Swan Soy Sauce',  'category' => 'Condiments',      'price' => 48.00,  'stock' => 50],
                ['name' => 'Century Tuna',            'category' => 'Canned Goods',    'price' => 38.00,  'stock' => 80],
                ['name' => 'Milo Chocolate Drink',    'category' => 'Beverages',       'price' => 27.00,  'stock' => 60],
                ['name' => 'Magnolia Butter',         'category' => 'Dairy',           'price' => 55.00,  'stock' => 30],
                ['name' => 'Lucky Me Pancit Canton',  'category' => 'Instant Noodles', 'price' => 15.00,  'stock' => 100],
                ['name' => 'Surf Powder Detergent',   'category' => 'Household',       'price' => 62.00,  'stock' => 40],
                ['name' => 'Safeguard Soap',          'category' => 'Personal Care',   'price' => 45.00,  'stock' => 55],
                ['name' => 'Nestle Coffee 3-in-1',    'category' => 'Beverages',       'price' => 9.00,   'stock' => 200],
            ];

            foreach ($products as $p) {
                Product::firstOrCreate(['name' => $p['name']], $p);
            }
        }
}
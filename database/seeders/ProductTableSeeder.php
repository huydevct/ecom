<?php

namespace Database\Seeders;

use App\Models\Product;
use Database\Factories\ProductFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($k = 1; $k <= 10; $k++) {
            $a = 10000;
            for ($j = 1; $j <= 100; $j++) {
                $users = [];
                for ($i = $a * $j; $i <= $a * $j + 9000; $i++) {
                    $user = [
                        'name' => $i . "a",
                        'price' => floatval($i),
                        'stock' => $i,
                        'sku' => $i . "d",
                        'description' => $i . "des",
                        'created_at' => now(),
                    ];
                    array_push($users, $user);
                }
                Product::insert($users);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();
        $chunkSize = 1000;

        for ($i = 0; $i < 1000; $i += $chunkSize) {
            $products = [];

            for ($j = 0; $j < $chunkSize; $j++) {
                if ($i + $j >= 1000) break;

                $products[] = [
                    'name' => 'Product ' . ($i + $j + 1),
                    'price' => $faker->randomFloat(2, 1, 100),
                    'category_id' => rand(1, 5),
                    'unit_id' => rand(1, 5),
                    'user_id' => 1,
                    'minimal_stock' => $faker->numberBetween(1, 20),
                    'stock' => $faker->numberBetween(21, 100),
                    'url_images' => "assets/img/demo/cards/card-img-bottom.jpg",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Use Eloquent and wrap in a transaction
            DB::transaction(function () use ($products) {
                Product::insert($products);
            });
        }
    }
}

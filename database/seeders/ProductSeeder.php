<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        for($i = 0; $i < 30; $i++) {
            $randomNum = rand(1,5);
            $keywordSearchTextByName = $randomNum == 1 ? ' vind mij' : '';
            $keywordSearchTextByDescription = $randomNum == 2 ? ' vind mij' : '';
            Product::create([
                'name' => fake()->name() . $keywordSearchTextByName,
                'description' => fake()->text() . $keywordSearchTextByDescription,
                'price' => ProductSeeder::randomDouble(1,100),
            ]);
        }

    }

    private static function randomDouble($min, $max, $precision = 2) {
        $randomInteger = random_int($min * pow(10, $precision), $max * pow(10, $precision));
        $randomDouble = $randomInteger / pow(10, $precision);
        return $randomDouble;
    }
}

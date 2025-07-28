<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;

/**
 *
 */
class ProductSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $categories = Category::all();

        Product::factory()->count(50)->create()->each(function ($product) use ($categories) {
            if ($categories->count() > 0) {
                $product->categories()->attach(
                    $categories->random(rand(1, min(3, $categories->count())))->pluck('id')->toArray()
                );
            }

            Stock::factory()->create([
                'product_id' => $product->id,
            ]);
        });
    }
}

<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 *
 */
class ProductRepository
{
    /**
     * @return Collection
     */
    public function getAllWithCategoriesAndStock(): Collection
    {
        $products = Product::with(['categories', 'stock'])->get();

        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'categories' => $product->categories->pluck('name'),
                'in_stock' => $product->in_stock,
            ];
        });
    }
}

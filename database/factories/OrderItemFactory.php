<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 *
 */
class OrderItemFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        $product = Product::factory()->create();

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $product->price ?? $this->faker->numberBetween(1000, 100000),
        ];
    }
}

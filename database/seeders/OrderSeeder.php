<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;

/**
 *
 */
class OrderSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::with('stock')->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $order = Order::factory()->create([
                'user_id' => $user->id,
            ]);

            $itemsCount = rand(1, 5);
            $total = 0;

            $chosenProducts = $products->random(min($itemsCount, $products->count()));

            foreach ($chosenProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);

                $total += $price * $quantity;
            }

            $order->update(['total_amount' => $total]);
        }
    }
}

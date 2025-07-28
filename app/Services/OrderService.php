<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;

/**
 *
 */
class OrderService
{
    /**
     * @var OrderRepository
     */
    protected OrderRepository $orders;

    /**
     * @param OrderRepository $orders
     */
    public function __construct(OrderRepository $orders)
    {
        $this->orders = $orders;
    }

    /**
     * @param array $items
     * @param string|null $comment
     * @param int $userId
     * @return Order
     */
    public function createOrder(array $items, ?string $comment, int $userId): Order
    {
        $total = 0;
        $itemsData = [];

        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $price = $product->price;
            $quantity = $item['quantity'];
            $total += $price * $quantity;

            $itemsData[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
            ];
        }

        $order = $this->orders->create([
            'user_id' => $userId,
            'comment' => $comment,
            'total_amount' => $total,
            'status' => OrderStatus::Pending->value,
        ]);

        $this->orders->createItems($order, $itemsData);

        return $order;
    }
}

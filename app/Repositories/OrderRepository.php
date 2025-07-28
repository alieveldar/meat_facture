<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

/**
 *
 */
class OrderRepository
{
    /**
     * @param array $data
     * @return Order
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    /**
     * @param Order $order
     * @param array $items
     * @return void
     */
    public function createItems(Order $order, array $items): void
    {
        $order->items()->createMany($items);
    }

    /**
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserOrdersWithItems(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->orders()->with('items.product')->get();
    }

}

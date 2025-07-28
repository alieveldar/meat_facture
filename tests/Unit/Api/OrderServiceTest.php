<?php

namespace Tests\Unit\Api;

use App\Enums\OrderStatus;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_successfully()
    {
        $user = User::factory()->create();


        $product1 = Product::factory()->create(['price' => 1000]);
        $product2 = Product::factory()->create(['price' => 2000]);

        $orderService = new OrderService(new OrderRepository());

        $order = $orderService->createOrder(
            [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 1],
            ],
            'Test Order',
            $user->id
        );

        $this->assertEquals(4000, $order->total_amount);
        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals('Test Order', $order->comment);
    }
}

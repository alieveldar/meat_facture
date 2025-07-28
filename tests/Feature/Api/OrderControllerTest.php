<?php

namespace Tests\Feature\Api;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 1000,
        ]);

        $payload = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ]
            ],
            'comment' => 'Оставить у двери'
        ];

        $response = $this
            ->actingAs($user)
            ->postJson('/api/order', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'order_id',
                'status',
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'comment' => 'Оставить у двери',
            'total_amount' => 2000,
            'status' => OrderStatus::Pending->value,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 1000,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_order()
    {
        $response = $this->postJson('/api/order', []);

        $response->assertStatus(401);
    }

    /** @test */
    public function validation_fails_when_items_are_missing()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/order', [
                'comment' => 'Без звонка'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    /** @test */
    public function authenticated_user_can_get_their_orders()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 500]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 1500,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 500,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $order->id,
                'total_amount' => 1500,
                'product_id' => $product->id,
                'quantity' => 3,
            ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_orders()
    {
        $response = $this->getJson('/api/orders');

        $response->assertStatus(401);
    }
}

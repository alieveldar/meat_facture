<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 *
 */
class OrderFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Order::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'comment' => $this->faker->optional()->sentence(),
            'total_amount' => 0,
            'status' => 'pending',
        ];
    }
}

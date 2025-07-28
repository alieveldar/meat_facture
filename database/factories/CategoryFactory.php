<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 *
 */
class CategoryFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Category::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'parent_id' => null,
        ];
    }

    /**
     * @param Category $parent
     * @return self
     */
    public function child(Category $parent): self
    {
        return $this->state(fn() => [
            'parent_id' => $parent->id,
        ]);
    }
}

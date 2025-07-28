<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_a_list_of_products_with_categories_and_stock()
    {
        $category = Category::factory()->create(['name' => 'Электроника']);
        $product = Product::factory()->create([
            'name' => 'Смартфон',
            'description' => 'Описание товара',
            'price' => 199.99,
        ]);
        $product->categories()->attach($category);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Смартфон',
                'description' => 'Описание товара',
                'price' => 199.99,
            ])
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'in_stock',
                    'categories',
                ]
            ]);
    }

    /** @test */
    public function it_returns_empty_list_if_no_products_exist()
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertExactJson([]);
    }

    /** @test */
    public function it_handles_exceptions_and_logs_error()
    {
        // Подменим репозиторий, чтобы вызвать исключение
        $mock = \Mockery::mock('App\Repositories\ProductRepository');
        $mock->shouldReceive('getAllWithCategoriesAndStock')
            ->once()
            ->andThrow(new \Exception('DB error'));

        $this->app->instance(\App\Repositories\ProductRepository::class, $mock);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'Ошибка при получении списка товаров') &&
                    isset($context['exception']);
            });

        $response = $this->getJson('/api/products');

        $response->assertStatus(500)
            ->assertJsonFragment([
                'message' => 'Ошибка при получении товаров'
            ]);
    }
}

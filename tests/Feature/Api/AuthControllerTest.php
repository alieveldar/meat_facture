<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

/**
 *
 */
class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Тестовый',
            'phone' => '+79991234567',
            'address' => 'Test addr',
            'password' => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'phone'],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'phone' => '+79991234567',
        ]);
    }

    /** @test */
    public function user_can_login()
    {
        $user = (User::factory())->create();
        $response = $this->postJson('/api/login', [
            'phone' => $user->phone,
            'password' => 'StrongPass1!',
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'phone'],
                'token'
            ]);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Вы успешно вышли из системы']);
    }
}

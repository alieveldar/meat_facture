<?php

namespace Tests\Unit\Api;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    /** @test */
    public function it_registers_a_user_and_returns_user_and_token()
    {
        $data = [
            'name' => 'Test User',
            'phone' => '1234567890',
            'password' => 'password',
            'address' => 'Some address',
        ];

        $response = $this->authService->register($data);

        $this->assertArrayHasKey('user', $response);
        $this->assertArrayHasKey('token', $response);

        $user = $response['user'];

        $this->assertDatabaseHas('users', ['phone' => $data['phone']]);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertNotEmpty($response['token']);
    }

    /** @test */
    public function it_logs_in_a_user_with_correct_credentials()
    {
        $user = User::factory()->create([
            'phone' => '9876543210',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->authService->login([
            'phone' => '9876543210',
            'password' => 'secret123',
        ]);

        $this->assertNotNull($response);
        $this->assertArrayHasKey('user', $response);
        $this->assertEquals($user->id, $response['user']->id);
        $this->assertArrayHasKey('token', $response);
    }

    /** @test */
    public function it_returns_null_if_credentials_are_invalid()
    {
        User::factory()->create([
            'phone' => '9876543210',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->authService->login([
            'phone' => '9876543210',
            'password' => 'wrong-password',
        ]);

        $this->assertNull($response);
    }

    /** @test */
    public function it_logs_out_user_and_deletes_tokens()
    {
        $user = User::factory()->create([
            'phone' => '5551231234',
            'password' => bcrypt('password'),
        ]);

        Sanctum::actingAs($user);
        $user->createToken('default');

        $this->assertCount(1, $user->tokens);

        $this->authService->logout($user);

        $this->assertCount(0, $user->fresh()->tokens);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка регистрации пользователя.
     */
    public function testRegisterRoute()
    {
        $newUser = User::factory()->make();
        $data = [
            'email' => $newUser->email,
            'name' => $newUser->name,
            'password' => $newUser->password,
            'password_confirmation' => $newUser->password,
        ];

        $response = $this->postJson(route('auth.register'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['token', 'user']]);

        $this->assertDatabaseHas('users', [
            'name' => $newUser->name,
            'email' => $newUser->email,
        ]);
    }

    /**
     * Проверка авторизации пользователя.
     */
    public function testAuthRoute()
    {
        $user = User::factory()->create(['password' => '12345678']);
        $data = [
            'email' => $user->email,
            'password' => '12345678',
        ];

        $response = $this->postJson(route('auth.login'), $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    /**
     * Проверка авторизации пользователя с использованием не корректного пароля.
     */
    public function testAuthRouteWithWrongPassword()
    {
        $user = User::factory()->create(['password' => '12345678']);
        $data = [
            'email' => $user->email,
            'password' => '1234567890',
        ];

        $response = $this->postJson(route('auth.login'), $data);

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Неверное имя пользователя или пароль.']);
    }

    /**
     * Проверка метода выхода пользователя.
     * И авторизации запроса используя заголовок Authorization.
     */
    public function testLogoutRoute()
    {
        $user = User::factory()->create(['password' => '12345678']);
        $token = $user->createToken('auth-token');

        $response = $this->postJson(
            route('auth.logout'),
            [],
            ['Authorization' => 'Bearer ' . $token->plainTextToken]
        );

        $response->assertStatus(204);
        $this->assertEmpty(User::first()->tokens()->get());
    }
}

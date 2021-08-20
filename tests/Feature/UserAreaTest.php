<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAreaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Запрос на роут закрытый авторизацией без использования токена должен вернуть ошибку авторизации
     */
    public function testCallUserAreaRouteShouldReturnErrorWithoutToken()
    {
        $response = $this->getJson(route('user.shows.index'));

        $response->assertStatus(401);
    }

    /**
     * Запрос на роут с токеном пользователя должен вернуть список сериалов отслеживаемых пользователем
     */
    public function testCallUserAreaRouteShouldReturnDataWithToken()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson(route('user.shows.index'));

        $response->assertStatus(200);
    }
}

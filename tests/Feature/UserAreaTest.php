<?php

namespace Tests\Feature;

use App\Models\Episode;
use App\Models\Show;
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

    public function testGetUserShows()
    {
        $user = User::factory()->has(Show::factory()->count(3)->has(Episode::factory()->count(5)))->create();
        $user->episodes()->attach($user->shows()->first()->episodes()->take(3)->pluck('id'));
        $user->episodes()->attach($user->shows()->latest('id')->first()->episodes()->pluck('id'));

        Sanctum::actingAs($user);

        $response = $this->getJson(route('user.shows.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure([['title', 'watch_status', 'watched_episodes']]);
    }

    public function testGetUserNotWatchedEpisodes()
    {
        $user = User::factory()->has(Show::factory()->count(3)->has(Episode::factory()->count(5)))->create();
        $show = $user->shows()->first();
        $user->episodes()->attach($show->episodes()->take(3)->pluck('id'));

        Sanctum::actingAs($user);

        $response = $this->getJson(route('user.shows.new-episodes', $show));

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure([['title', 'show_id', 'season', 'episode_number']]);
        $response->assertJsonFragment(['show_id' => $show->id]);
    }
}

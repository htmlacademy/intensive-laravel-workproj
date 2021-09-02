<?php

namespace Tests\Feature;

use App\Models\Episode;
use App\Models\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function testUpdateUser()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $newUser = User::factory()->make();
        $file = UploadedFile::fake()->image('photo1.jpg');

        $data = [
            'email' => $newUser->email,
            'name' => $newUser->name,
            'password' => $newUser->password,
            'password_confirmation' => $newUser->password,
            'file' => $file,
        ];

        $response = $this->patchJson(route('user.update'), $data);
        $response->assertJsonFragment([
            'name' => $newUser->name,
            'email' => $newUser->email,
            'avatar' => $file->hashName(),
        ]);
    }

    public function testAddShowToWatchList()
    {
        Sanctum::actingAs(User::factory()->create());

        $show = Show::factory()->create();

        $response = $this->postJson(route('user.shows.watch', $show));
        $response->assertStatus(201);
    }

    public function testRemoveShowFromWatchList()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $show = Show::factory()->create();
        $user->shows()->attach($show);

        $response = $this->deleteJson(route('user.shows.unwatch', $show));
        $response->assertStatus(201);
    }

    public function testAddEpisodeToWatchedList()
    {
        Sanctum::actingAs(User::factory()->create());

        $episode = Episode::factory()->for(Show::factory())->create();

        $response = $this->postJson(route('user.episodes.watch', $episode));
        $response->assertStatus(201);
    }

    public function testRemoveEpisodeFromWatchedList()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $episode = Episode::factory()->for(Show::factory())->create();
        $user->episodes()->attach($episode);

        $response = $this->deleteJson(route('user.episodes.unwatch', $episode));
        $response->assertStatus(201);
    }

    public function testVoteForShow()
    {
        Sanctum::actingAs(User::factory()->create());

        $show = Show::factory()->create();

        $response = $this->postJson(route('user.shows.vote', $show), ['vote' => random_int(1,5)]);
        $response->assertStatus(201);
    }
}

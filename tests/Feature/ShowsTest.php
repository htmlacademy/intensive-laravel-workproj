<?php

namespace Tests\Feature;

use App\Jobs\AddShow;
use App\Models\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetShowsList()
    {
        $count = random_int(2, 10);
        Show::factory()->count($count)->create();

        $response = $this->getJson(route('shows.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [], 'links' => [], 'total']);
        $response->assertJsonFragment(['total' => $count]);
    }

    public function testGetOneShow()
    {
        $show = Show::factory()->hasEpisodes(6)->hasGenres(3)->create();

        $response = $this->getJson(route('shows.show', $show->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $show->title,
            'title_original' => $show->title_original,
            'status' => $show->status,
            'year' => $show->year,
            'total_seasons' => $show->total_seasons,
            'total_episodes' => $show->episodes()->count(),
            'genres' => $show->genres,
        ]);
    }

    public function testRequestAddingShow()
    {
        Queue::fake();

        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('shows.request'), ['imdb' => 'tt001']);

        Queue::assertPushed(AddShow::class);

        $response->assertStatus(201);
    }

    /**
     * Ожидается ошибка валидации,
     * в случае если сериал запрошенный к добавлению уже есть в базе.
     */
    public function testValidationExistsErrorForRequestAddingShowRoute()
    {
        $show = Show::factory()->create(['imdb_id' => 'tt0944947']);

        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('shows.request'), ['imdb' => $show->imdb_id]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['imdb']]);
        $response->assertJsonFragment(['imdb' => ['Такой сериал уже есть']]);
    }

    /**
     * Ожидается ошибка валидации,
     * в случае если формат переданного imdb id не соответствует ожидаемому.
     */
    public function testValidationFormatErrorForRequestAddingShowRoute()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('shows.request'), ['imdb' => '944947']);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['imdb']]);
        $response->assertJsonFragment(['imdb' => ['imdb id должен быть передан в формате ttNNNN']]);
    }

    /**
     * Ожидается получение ошибки аутентификации,
     * если запрос на добавление сериала выполняется не аутентифицированным пользователем.
     */
    public function testAuthErrorForRequestAddingShowRoute()
    {
        $response = $this->postJson(route('shows.request'), ['imdb' => 'tt001']);

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Запрос требует аутентификации.']);
    }
}

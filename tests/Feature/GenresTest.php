<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GenresTest extends TestCase
{
    use RefreshDatabase;

    public function testGetGenres()
    {
        $count = random_int(2, 10);
        Genre::factory()->count($count)->create();

        $response = $this->getJson(route('genres.index'));

        $response->assertStatus(200);
        $response->assertJsonCount($count, 'data');
        $response->assertJsonStructure(['data' => [['id', 'title']]]);
    }

    public function testUpdateGenre()
    {
        $genre = Genre::factory()->create();
        $new = Genre::factory()->make();

        Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->patchJson(route('genres.update', $genre->id), ['title' => $new->title]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $genre->id, 'title' => $new->title]);
    }

    public function testUpdateGenreByGuest()
    {
        $genre = Genre::factory()->create();

        $response = $this->patchJson(route('genres.update', $genre->id));

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Запрос требует аутентификации.']);
    }

    public function testCheckRoleForUpdateGenreRoute()
    {
        $genre = Genre::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $response = $this->patchJson(route('genres.update', $genre->id));

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => 'Неавторизованное действие.']);
    }
}

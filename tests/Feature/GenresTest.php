<?php

namespace Tests\Feature;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response->assertJsonCount($count);
        $response->assertJsonStructure([['id', 'title']]);
    }
}

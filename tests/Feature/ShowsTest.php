<?php

namespace Tests\Feature;

use App\Models\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}

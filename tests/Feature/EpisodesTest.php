<?php

namespace Tests\Feature;

use App\Models\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpisodesTest extends TestCase
{
    use RefreshDatabase;

    public function testGetShowEpisodes()
    {
        $count = random_int(2, 10);
        $show = Show::factory()->hasEpisodes($count)->create();
        $episode = $show->episodes->first();

        $response = $this->getJson(route('episodes.index', $show->id));

        $response->assertStatus(200);
        $response->assertJsonCount($count, 'data');
        $response->assertJsonFragment([
            'title' => $episode->title,
            'show_id' => $show->id,
            'season' => $episode->season,
            'episode_number' => $episode->episode_number,
            'air_at' => $episode->air_at,
            'comments_count' => $episode->comments_count,
        ]);
    }

    public function testGetOneEpisode()
    {
        $show = Show::factory()->hasEpisodes()->create();
        $episode = $show->episodes->first();

        $response = $this->getJson(route('episodes.show', $episode->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $episode->title,
            'show_id' => $show->id,
            'season' => $episode->season,
            'episode_number' => $episode->episode_number,
            'air_at' => $episode->air_at,
            'comments_count' => $episode->comments_count,
        ]);
    }
}

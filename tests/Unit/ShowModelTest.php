<?php

namespace Tests\Unit;

use App\Models\Episode;
use App\Models\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка вычисления значения total_seasons
     */
    public function testGetTotalSeasons()
    {
        $show = Show::factory()->create();

        Episode::factory()->for($show)->create(['season' => 1]);
        Episode::factory()->for($show)->create(['season' => 2]);
        Episode::factory()->for($show)->create(['season' => 2]);

        $this->assertEquals(2, $show->total_seasons);
    }

    /**
     * Проверка вычисления значения total_episodes
     */
    public function testGetTotalEpisodes()
    {
        $count = random_int(2,4);
        Show::factory()->has(Episode::factory()->count($count))->create();

        $this->assertEquals($count, Show::first()->total_episodes);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_shows_list()
    {
        $count = random_int(2, 10);
        Show::factory()->count($count)->create();

        $response = $this->getJson(route('shows.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [], 'links' => [], 'total']);
        $response->assertJsonFragment(['total' => $count]);
    }

    public function test_get_one()
    {
        $show = Show::factory()->create();

        $response = $this->getJson(route('shows.show', $show->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $show->title]);
    }
}

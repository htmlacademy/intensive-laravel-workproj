<?php

namespace Tests\Unit;

use App\Jobs\SaveShow;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Show;
use App\Support\Import\ImportRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class SaveShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка создания сериала и его эпизодов.
     */
    public function testCreateShowByJob()
    {
        $count = 3;
        $show = Show::factory()->make(['imdb_id' => 'tt0944947']);
        $episodes = Episode::factory()->count($count)->make();

        $this->mock(ImportRepository::class, function (MockInterface $mock) use ($episodes) {
            $mock->shouldReceive('getEpisodes')->andReturn($episodes)->once();
        });

        SaveShow::dispatchSync(['show' => $show, 'genres' => []]);

        $this->assertDatabaseHas('shows', $show->getAttributes());

        $show = Show::first();
        $episode = $episodes->first();

        $this->assertDatabaseCount('episodes', $count);
        $this->assertDatabaseHas('episodes', [
            'title' => $episode->title,
            'season' => $episode->season,
            'episode_number' => $episode->episode_number,
            'air_at' => $episode->air_at,
            'show_id' => $show->id,
        ]);
    }

    /**
     * Проверка обновления сериала
     */
    public function testUpdateShowByJob()
    {
        $show = Show::factory()->create(['imdb_id' => 'tt0944947', 'created_at' => now()->subYear(), 'updated_at' => now()->subYear()]);
        $newShow = Show::factory()->make(['imdb_id' => $show->imdb_id, 'updated_at' => now()]);
        $updatedShow = $show->fill($newShow->getAttributes());

        $this->mock(ImportRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('getEpisodes')->andReturn(collect());
        });

        SaveShow::dispatchSync(['show' => $updatedShow, 'genres' => []]);

        $this->assertDatabaseCount('shows', 1);
        $this->assertDatabaseHas('shows', $newShow->getAttributes());
    }

    /**
     * Проверка пропуска сохранения сериала, если данные не изменились.
     */
    public function testSkipUpdateShowByJob()
    {
        $show = Show::factory()->create(['imdb_id' => 'tt0944947', 'created_at' => now()->subYear(), 'updated_at' => now()->subYear()]);
        $this->mock(ImportRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('getEpisodes')->andReturn(collect());
        });

        SaveShow::dispatchSync(['show' => $show, 'genres' => []]);

        $this->assertDatabaseCount('shows', 1);
        $this->assertDatabaseHas('shows', $show->getAttributes());
    }

    /**
     * Проверка создания жанра, при его отсутствии в базе.
     */
    public function testCreateGenreByJob()
    {
        $show = Show::factory()->create(['imdb_id' => 'tt0944947']);
        $genres = Genre::factory()->count(2)->make()->pluck('title_en')->toArray();

        $this->mock(ImportRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('getEpisodes')->andReturn(collect());
        });

        SaveShow::dispatchSync(['show' => $show, 'genres' => $genres]);

        $this->assertDatabaseCount('genres', 2);
        $this->assertDatabaseHas('genres', ['title' => $genres[0], 'title_en' => $genres[0]]);
    }

    /**
     * Проверка пропуска сохранения жанра, если такая запись уже есть в базе.
     */
    public function testSkipCreateGenreByJob()
    {
        $show = Show::factory()->create(['imdb_id' => 'tt0944947']);
        $genre = Genre::factory()->create(['title' => 'custom_title', 'title_en' => 'original_title']);

        $this->mock(ImportRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('getEpisodes')->andReturn(collect());
        });

        SaveShow::dispatchSync(['show' => $show, 'genres' => ['original_title']]);

        $this->assertDatabaseCount('genres', 1);
        $this->assertDatabaseHas('genres', ['title' => $genre->title, 'title_en' => $genre->title_en]);
    }
}

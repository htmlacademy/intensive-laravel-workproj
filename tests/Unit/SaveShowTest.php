<?php

namespace Tests\Unit;

use App\Jobs\SaveShow;
use App\Models\Genre;
use App\Models\Show;
use App\Support\Import\ImportRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class SaveShowTest extends TestCase
{
    use RefreshDatabase;

    public function testProcessingJob()
    {
        Genre::factory()->create(['id' => 100, 'title' => 'Драма', 'title_en' => 'Drama']);
        Genre::factory()->create(['id' => 111, 'title' => 'Приключения', 'title_en' => 'Adventure']);

        $show = Show::factory()->make();

        $this->mock(ImportRepository::class, function (MockInterface $mock) use ($show) {
            $mock->shouldReceive('getShow')->andReturn(['show' => $show, 'genres' => []])->once();
            $mock->shouldReceive('getEpisodes')->andReturn(collect())->once();
        });

        SaveShow::dispatchSync('tt0944947');

        // todo проверить сохранение
    }
}

<?php

namespace Tests\Unit;

use App\Jobs\SyncShows;
use App\Models\Show;
use App\Support\Import\ImportRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class SyncShowsTest extends TestCase
{
    use RefreshDatabase;

    public function testProcessingJob()
    {
        $show = Show::factory()->create(['imdbId' => 'tt0944947','updated_at' => now()->subMonth()]);
        $showNew = Show::factory()->make(['imdbId' => 'tt0944947','updated_at' => now()]);
        $show->fill($showNew->getAttributes());

        $this->mock(ImportRepository::class, function (MockInterface $mock) use ($show) {
            $mock->shouldReceive('getShow')->andReturn(['show' => $show, 'genres' => []]);
            $mock->shouldReceive('getEpisodes')->andReturn(collect())->once();
        });

        SyncShows::dispatchSync();

        // todo проверить обновление текущей записи
    }
}

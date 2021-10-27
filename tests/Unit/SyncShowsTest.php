<?php

namespace Tests\Unit;

use App\Jobs\SaveShow;
use App\Jobs\SyncShows;
use App\Models\Show;
use App\Support\Import\ImportRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class SyncShowsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка задачи обновления сериалов.
     * Ожидается создание двух задач на обновление,
     * для сериалов имеющих значение imdb_id в базе с отличной от полученной датой изменения.
     */
    public function testProcessingJob()
    {
        Queue::fake();

        Show::factory()->create(['imdb_id' => null, 'updated_at' => now()->subMonth()]); // imdb_id = null не проверяем
        Show::factory()->create(['imdb_id' => 'tt01', 'updated_at' => now()]); // дата изменения будет той же, что и отдаст репозиторий
        Show::factory()->create(['imdb_id' => 'tt02', 'updated_at' => now()->subMonth()]);
        Show::factory()->create(['imdb_id' => 'tt03', 'updated_at' => now()->subWeek()]);

        $showNew = Show::factory()->make(['updated_at' => now()]);

        $repository = $this->mock(ImportRepository::class, function (MockInterface $mock) use ($showNew) {
            $mock->shouldReceive('getShow')->andReturn(['show' => $showNew, 'genres' => []]);
        });

        (new SyncShows())->handle($repository);

        Queue::assertPushed(SaveShow::class, 2);
    }
}

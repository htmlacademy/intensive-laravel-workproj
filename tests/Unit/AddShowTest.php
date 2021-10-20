<?php

namespace Tests\Unit;

use App\Jobs\AddShow;
use App\Jobs\SaveShow;
use App\Models\Show;
use App\Support\Import\ImportRepository;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class AddShowTest extends TestCase
{
    /**
     * Проверка вызова задачи сохранения сериала, с моккированными параметрами.
     */
    public function testCallingSubJobWithExpectedParams()
    {
        Queue::fake();

        $show = Show::factory()->make(['imdb_id' => 'tt0944947']);

        $repository = $this->mock(ImportRepository::class, function (MockInterface $mock) use ($show) {
            $mock->shouldReceive('getShow')->andReturn(['show' => $show, 'genres' => []])->once();
        });

        (new AddShow('tt0944947'))->handle($repository);

        Queue::assertPushed(function (SaveShow $job) use ($show) {
            return $job->data['show'] === $show;
        });
    }

    /**
     * Проверка не вызова задачи сохранения сериала, если репозиторий вернул пустой ответ.
     */
    public function testNotCallingSubJobForEmptyShow()
    {
        Queue::fake();

        $repository = $this->mock(ImportRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('getShow')->andReturn(null)->once();
        });

        (new AddShow('tt0944947'))->handle($repository);

        Queue::assertNotPushed(SaveShow::class);
    }
}

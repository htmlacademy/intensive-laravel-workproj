<?php

namespace Tests\Unit;

use App\Jobs\AddShow;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AddShowTest extends TestCase
{
    use RefreshDatabase;

    public function testProcessingJob()
    {
        // todo тестируя job - моккируем репозиторий
        //      тестируя репозиторий - моккируем ответ сервиса (Http::fake)

        Genre::factory()->create(['id' => 100, 'title' => 'Драма', 'title_en' => 'Drama']);
        Genre::factory()->create(['id' => 111, 'title' => 'Приключения', 'title_en' => 'Adventure']);

        Http::fake([
            '*' => Http::response(file_get_contents(base_path('tests/Fixtures/show-tvmaze-1.json'))),
        ]);

        AddShow::dispatchSync('tt0944947');

        // todo добавить проверку создания сериала
    }
}

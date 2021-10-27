<?php


namespace Tests\Unit;

use App\Models\Episode;
use App\Models\Show;
use App\Support\Import\TvmazeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TvmazeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TvmazeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new TvmazeRepository;
    }

    /**
     * Проверка получения информации о сериале из репозитория.
     * Ожидается получение модели Show и массива с названиями жанров.
     */
    public function testGetShow()
    {
        Http::fake([
            '*' => Http::response(file_get_contents(base_path('tests/Fixtures/show-tvmaze-1.json'))),
        ]);

        $result = $this->repository->getShow('tt0944947');

        $this->assertInstanceOf(Show::class, $result['show']);
        $this->assertIsArray($result['genres']);
        $this->assertFalse($result['show']->exists);
    }

    /**
     * Проверка получения информации о сериале из репозитория, который уже есть в БД сервиса.
     * Ожидается получение exists модели Show и массива с названиями жанров.
     */
    public function testGetExistedShow()
    {
        Show::factory()->create(['imdb_id' => 'tt0944947']);

        Http::fake([
            '*' => Http::response(file_get_contents(base_path('tests/Fixtures/show-tvmaze-1.json'))),
        ]);

        $result = $this->repository->getShow('tt0944947');

        $this->assertInstanceOf(Show::class, $result['show']);
        $this->assertIsArray($result['genres']);
        $this->assertTrue($result['show']->exists);
        $this->assertNotEmpty($result['show']->id);
    }

    /**
     * Проверка получения пустого ответа при запросе несуществующего сериала из репозитория.
     */
    public function testGetNotFoundShows()
    {
        Http::fake([
            '*' => Http::response('{"name":"Not Found","message":"","code":0,"status":404}', 404),
        ]);

        $result = $this->repository->getShow('tt0944947');

        $this->assertNull($result);
    }

    /**
     * Проверка получения коллекции эпизодов, в ответ на запрос эпизодов для сериала.
     */
    public function testGetEpisodes()
    {
        Http::fake([
            'lookup/shows?*' => Http::response(file_get_contents(base_path('tests/Fixtures/show-tvmaze-1.json'))),
            'shows/*' => Http::response(file_get_contents(base_path('tests/Fixtures/episodes-tvmaze-1.json'))),
        ]);

        $result = $this->repository->getEpisodes('tt0944947');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(Episode::class, $result->first());
        $this->assertFalse($result->first()->exists);
    }

    /**
     * Проверка получения пустой коллекции эпизодов, если у запрошенного сериала еще нет серий.
     */
    public function testGetEmptyEpisodesCollection()
    {
        Http::fake([
            'lookup/shows?*' => Http::response(file_get_contents(base_path('tests/Fixtures/show-tvmaze-1.json'))),
            'shows/*' => Http::response('[]'),
        ]);

        $result = $this->repository->getEpisodes('tt0944947');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEmpty($result);
    }
}

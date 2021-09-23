<?php


namespace Tests\Unit;

use App\Models\Episode;
use App\Models\Show;
use App\Support\Import\TvmazeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function testGetShows()
    {
        Http::fake([
            '*' => Http::response(file_get_contents(base_path('tests/Fixtures/show-tvmaze-1.json'))),
        ]);

        $result = $this->repository->getShow('tt0944947');

        $this->assertInstanceOf(Show::class, $result['show']);
        $this->assertIsArray($result['genres']);
        $this->assertFalse($result['show']->exists);
    }

    public function testGetEpisodes()
    {
        Http::fake([
            'lookup/shows?*' => Http::response(file_get_contents(base_path('tests/Fixtures/show-tvmaze-1.json'))),
            'shows/*' => Http::response(file_get_contents(base_path('tests/Fixtures/episodes-tvmaze-1.json'))),
        ]);

        $result = $this->repository->getEpisodes('tt0944947');

        $this->assertInstanceOf(Episode::class, $result->first());
        $this->assertFalse($result->first()->exists);
    }
}

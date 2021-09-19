<?php


namespace App\Support\Import;

use App\Models\Show;
use Illuminate\Support\Facades\Http;

class TvmazeRepository implements ImportRepository
{
    private const STATUSES = [
      'Ended' => 'ended',
    ];

    public function getShow(string $imdbId): array
    {
        $data = $this->api('lookup/shows', ['imdb' => $imdbId]);

        $show = new Show([
            'title' =>   $data['name'],
            'title_original' =>   $data['name'],
            'description' => strip_tags($data['summary']),
            'year' => date('Y', strtotime($data['premiered'])),
            'status' => self::STATUSES[$data['status']],
            'imdbId' => $imdbId,
        ]);

        return [
            'show' => $show,
            'genres' => $data['genres']
        ];
    }

    public function getEpisodes(string $imdbId): array
    {
        // TODO: Implement getEpisodes() method.
    }

    private function api(string $path, array $params = [])
    {
        return Http::baseUrl(config('services.tvmaze.url'))->get($path, $params);
    }
}

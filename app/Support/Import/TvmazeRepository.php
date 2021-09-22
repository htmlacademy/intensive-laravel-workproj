<?php


namespace App\Support\Import;

use App\Models\Episode;
use App\Models\Show;
use Illuminate\Support\Collection;
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
            'status' => self::STATUSES[$data['status']] ?? strtolower($data['status']),
            'imdbId' => $imdbId,
        ]);

        return [
            'show' => $show,
            'genres' => $data['genres']
        ];
    }

    public function getEpisodes(string $imdbId): Collection
    {
        $show = $this->api('lookup/shows', ['imdb' => $imdbId]);

        $data = $this->api("/shows/{$show['id']}/episodes")->collect();

        return $data->map(fn($value) => new Episode([
            'title' => $value['name'],
            'season' => $value['season'],
            'episode_number' => $value['number'],
            'air_at' => $value['airstamp'],
            'show_id' => $show['id'],
        ]));
    }

    private function api(string $path, array $params = [])
    {
        return Http::baseUrl(config('services.tvmaze.url'))->get($path, $params);
    }
}

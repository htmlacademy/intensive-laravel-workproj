<?php


namespace App\Support\Import;

use App\Models\Episode;
use App\Models\Show;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class TvmazeRepository implements ImportRepository
{
    private const STATUSES = [
      'Ended' => 'ended',
      'Running' => 'running',
      'To Be Determined' => 'pause',
    ];

    public function getShow(string $imdbId): ?array
    {
        $data = $this->api('lookup/shows', ['imdb' => $imdbId]);

        if ($data->clientError()) {
            return null;
        }

        $show = Show::firstOrNew(['imdbId' => $imdbId]);
        $show->fill([
            'title' => $show['title'] ?? $data['name'],
            'title_original' => $data['name'],
            'description' => strip_tags($data['summary']),
            'year' => date('Y', strtotime($data['premiered'])),
            'status' => self::STATUSES[$data['status']] ?? strtolower($data['status']),
            'updated_at' => $data['updated']
        ]);

        return [
            'show' => $show,
            'genres' => $data['genres']
        ];
    }

    public function getEpisodes(string $imdbId): ?Collection
    {
        $show = $this->api('lookup/shows', ['imdb' => $imdbId]);

        if ($show->clientError()) {
            return null;
        }

        $data = $this->api("/shows/{$show['id']}/episodes")->collect();

        return $data->map(function ($value) use ($show) {
            $episode = Episode::firstOrNew([
                'season' => $value['season'],
                'episode_number' => $value['number'],
            ]);
            $episode->fill([
                'title' => $value['name'],
                'air_at' => Carbon::parse($value['airstamp'])->toDateTimeString(),
            ]);

            return $episode;
        });
    }

    private function api(string $path, array $params = [])
    {
        return Http::baseUrl(config('services.tvmaze.url'))->get($path, $params);
    }
}

<?php


namespace App\Support\Import;

class TvmazeRepository implements ImportRepository
{

    public function getShow(string $imdbId): array
    {
        // TODO: Implement getShow() method.
    }

    public function getEpisodes(string $imdbId): array
    {
        // TODO: Implement getEpisodes() method.
    }

    private function api(string $path, array $params = [])
    {

    }
}

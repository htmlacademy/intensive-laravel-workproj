<?php

namespace App\Support\Import;

use Illuminate\Support\Collection;

interface ImportRepository
{
    /**
     * @param string $imdbId
     * @return array{show: \App\Models\Show, genres: array}|null
     */
    public function getShow(string $imdbId): ?array;

    /**
     * @param string $imdbId
     * @return Collection|null
     */
    public function getEpisodes(string $imdbId): ?Collection;
}

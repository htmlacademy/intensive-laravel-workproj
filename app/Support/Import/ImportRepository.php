<?php

namespace App\Support\Import;

interface ImportRepository
{
    /**
     * @param string $imdbId
     * @return array{show: \App\Models\Show, genres: array}
     */
    public function getShow(string $imdbId):array;

    public function getEpisodes(string $imdbId):array;
}

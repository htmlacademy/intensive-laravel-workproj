<?php

namespace App\Support\Import;

interface ImportRepository
{
    public function getShow(string $imdbId):array;

    public function getEpisodes(string $imdbId):array;
}

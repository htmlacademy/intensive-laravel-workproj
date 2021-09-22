<?php

namespace App\Jobs;

use App\Models\Genre;
use App\Support\Import\ImportRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AddShow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected string $imdbId)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ImportRepository $repository)
    {
        list('show' => $show, 'genres' => $genres) = $repository->getShow($this->imdbId);

        $genresIds = [];

        foreach ($genres as $genre) {
            $genresIds[] = Genre::firstOrCreate(['title_en' => $genre], ['title' => $genre])->id;
        }

        $episodes = $repository->getEpisodes($this->imdbId);

        DB::beginTransaction();
        $show->save();
        $show->genres()->attach($genresIds);
        $show->episodes()->saveMany($episodes);
        DB::commit();
    }
}
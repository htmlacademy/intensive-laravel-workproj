<?php

namespace App\Jobs;

use App\Models\Genre;
use App\Models\Show;
use App\Support\Import\ImportRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SaveShow implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public array $data)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ImportRepository $repository)
    {
        /** @var Show $show */
        list('show' => $show, 'genres' => $genres) = $this->data;

        $genresIds = [];

        foreach ($genres as $genre) {
            $genresIds[] = Genre::firstOrCreate(['title_en' => $genre], ['title' => $genre])->id;
        }

        $episodes = $repository->getEpisodes($show->imdb_id);

        DB::beginTransaction();
        $show->save();
        $show->genres()->attach($genresIds);
        $show->episodes()->saveMany($episodes);
        DB::commit();
    }
}

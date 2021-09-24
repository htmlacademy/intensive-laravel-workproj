<?php

namespace App\Jobs;

use App\Models\Show;
use App\Support\Import\ImportRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncShows implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ImportRepository $repository)
    {
        // допустимо для малых объемов, но в реальном проекте
        // следует получать список обновленных сериалов от источника данных,
        // если это возможно
        Show::whereNotNull('imdbId')->chunk(1000, function ($shows) use ($repository) {
            /** @var Show $show */
            foreach ($shows as $show) {
                $data = $repository->getShow($show->imdbId); // стоит выполнять проверку на rate limits
                if (isset($data['show']) && $data['show']->updated_at > $show->updated_at) {
                    SaveShow::dispatch($data);
                }
            }
        });
    }
}
